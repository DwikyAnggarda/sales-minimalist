<?php

namespace App\Livewire;

use App\Models\Branch;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Sale;
use App\Models\Store;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class SalesDashboard extends Component
{
    use WithPagination;

    // Filter properties
    public $startDate;
    public $endDate;
    public $branchId;
    public $productCategoryId = '';

    // Modal state & Form properties
    public $showModal = false;
    public $isEditMode = false;
    public $saleId;
    public $storeId;
    public $productId = '';
    public $amount;
    public $transactionDate;

    protected $queryString = [
        'startDate' => ['except' => ''],
        'endDate'   => ['except' => ''],
        'branchId'  => ['except' => ''],
        'productCategoryId' => ['except' => ''],
    ];

    public function mount()
    {
        $this->startDate = now()->startOfMonth()->toDateString();
        $this->endDate   = now()->toDateString();
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['startDate', 'endDate', 'branchId', 'productCategoryId'])) {
            $this->resetPage();
        }
    }

    public function resetFilters()
    {
        $this->reset(['startDate', 'endDate', 'branchId', 'productCategoryId']);
        $this->startDate = now()->startOfMonth()->toDateString();
        $this->endDate   = now()->toDateString();
        $this->resetPage();
    }

    public function render()
    {
        $start = $this->startDate . ' 00:00:00';
        $end   = $this->endDate   . ' 23:59:59';

        // ── Paginated Sales List ─────────────────────────────────────────────
        $salesQuery = Sale::with('store.branch', 'product.category')
            ->whereBetween('transaction_date', [$start, $end]);

        if ($this->branchId) {
            $salesQuery->whereHas('store', fn ($q) => $q->where('branch_id', $this->branchId));
        }

        if ($this->productCategoryId) {
            $salesQuery->whereHas('product', fn ($q) => $q->where('product_category_id', $this->productCategoryId));
        }

        $sales = $salesQuery->latest('transaction_date')->paginate(10);

        // ── KPI Stats (computed fresh — no cache to eliminate staleness) ────
        //
        // We run each aggregate as its own query so there is zero ambiguity:
        // calling sum() and count() on the same builder instance can produce
        // incorrect results with some drivers when a JOIN is involved.

        $totalSales = (float) DB::table('sales')
            ->when($this->branchId, fn ($q) =>
                $q->join('stores as s_rev', 'sales.store_id', '=', 's_rev.id')
                  ->where('s_rev.branch_id', $this->branchId)
            )
            ->when($this->productCategoryId, fn ($q) =>
                $q->join('products as p_rev', 'sales.product_id', '=', 'p_rev.id')
                  ->where('p_rev.product_category_id', $this->productCategoryId)
            )
            ->whereBetween('sales.transaction_date', [$start, $end])
            ->sum('sales.amount');

        $totalTransactions = (int) DB::table('sales')
            ->when($this->branchId, fn ($q) =>
                $q->join('stores as s_cnt', 'sales.store_id', '=', 's_cnt.id')
                  ->where('s_cnt.branch_id', $this->branchId)
            )
            ->when($this->productCategoryId, fn ($q) =>
                $q->join('products as p_cnt', 'sales.product_id', '=', 'p_cnt.id')
                  ->where('p_cnt.product_category_id', $this->productCategoryId)
            )
            ->whereBetween('sales.transaction_date', [$start, $end])
            ->count('sales.id');

        $averageOrderValue = $totalTransactions > 0
            ? $totalSales / $totalTransactions
            : 0;

        // Top Performing Store
        $topStoreRow = DB::table('sales')
            ->join('stores', 'sales.store_id', '=', 'stores.id')
            ->whereBetween('sales.transaction_date', [$start, $end])
            ->when($this->branchId, fn ($q) => $q->where('stores.branch_id', $this->branchId))
            ->when($this->productCategoryId, fn ($q) =>
                $q->join('products as p_top', 'sales.product_id', '=', 'p_top.id')
                  ->where('p_top.product_category_id', $this->productCategoryId)
            )
            ->select('stores.name', DB::raw('SUM(sales.amount) as total'))
            ->groupBy('stores.id', 'stores.name')
            ->orderByDesc('total')
            ->first();

        // ── Revenue by Branch ────────────────────────────────────────────────
        //
        // When a branch filter is active, only show that branch so that
        // percentages never exceed 100% (which previously caused layout overflow).
        // Percentages are always relative to the SUM of displayed branches.

        $revenueByBranch = DB::table('branches')
            ->when($this->branchId, fn ($q) => $q->where('branches.id', $this->branchId))
            ->leftJoin('stores', 'branches.id', '=', 'stores.branch_id')
            ->leftJoin('sales', function ($join) use ($start, $end) {
                $join->on('stores.id', '=', 'sales.store_id')
                     ->whereBetween('sales.transaction_date', [$start, $end]);
            })
            ->select('branches.name', DB::raw('COALESCE(SUM(sales.amount), 0) as total'))
            ->groupBy('branches.id', 'branches.name')
            ->orderByDesc('total')
            ->get();

        $displayedTotal = (float) $revenueByBranch->sum('total');

        $revenueByBranch = $revenueByBranch->map(function ($item) use ($displayedTotal) {
            $item->percentage = $displayedTotal > 0
                ? min(round(($item->total / $displayedTotal) * 100, 1), 100)
                : 0;
            return $item;
        });

        $stats = [
            'totalSales'        => $totalSales,
            'totalTransactions' => $totalTransactions,
            'averageOrderValue' => $averageOrderValue,
            'topStore'          => $topStoreRow ? $topStoreRow->name : '-',
            'revenueByBranch'   => $revenueByBranch,
        ];

        // ── Chart Data ───────────────────────────────────────────────────────

        // 1. Daily Revenue Trend
        $trendRows = DB::table('sales')
            ->when($this->branchId, fn ($q) =>
                $q->join('stores as s_trend', 'sales.store_id', '=', 's_trend.id')
                  ->where('s_trend.branch_id', $this->branchId)
            )
            ->whereBetween('sales.transaction_date', [$start, $end])
            ->select(
                DB::raw('DATE(sales.transaction_date) as day'),
                DB::raw('SUM(sales.amount) as total')
            )
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        $trendLabels = $trendRows->map(fn ($r) => \Carbon\Carbon::parse($r->day)->format('d M'))->toArray();
        $trendValues = $trendRows->pluck('total')->map(fn ($v) => (float) $v)->toArray();

        // 2. Top 5 Stores by Revenue
        $topStoreRows = DB::table('sales')
            ->join('stores', 'sales.store_id', '=', 'stores.id')
            ->whereBetween('sales.transaction_date', [$start, $end])
            ->when($this->branchId, fn ($q) => $q->where('stores.branch_id', $this->branchId))
            ->select('stores.name', DB::raw('SUM(sales.amount) as total'))
            ->groupBy('stores.id', 'stores.name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $storeLabels = $topStoreRows->pluck('name')->toArray();
        $storeValues = $topStoreRows->pluck('total')->map(fn ($v) => (float) $v)->toArray();

        // Dispatch chart data as a browser event so JavaScript can update
        // charts that are protected by wire:ignore without a full page reload.
        $this->dispatch('charts-updated',
            trendLabels: $trendLabels,
            trendValues: $trendValues,
            storeLabels: $storeLabels,
            storeValues: $storeValues,
        );

        return view('livewire.sales-dashboard', [
            'sales'             => $sales,
            'stats'             => $stats,
            'branches'          => Branch::all(),
            'allStores'         => Store::with('branch')->orderBy('name')->get(),
            'allProducts'       => Product::with('category')->orderBy('name')->get(),
            'productCategories' => ProductCategory::orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        $this->resetValidation();
        $this->reset(['saleId', 'storeId', 'productId', 'amount', 'transactionDate']);
        $this->transactionDate = now()->format('Y-m-d\TH:i');
        $this->isEditMode = false;
        $this->showModal  = true;
    }

    public function edit(Sale $sale)
    {
        $this->resetValidation();
        $this->saleId          = $sale->id;
        $this->storeId         = $sale->store_id;
        $this->productId       = $sale->product_id ?? '';
        $this->amount          = $sale->amount;
        $this->transactionDate = $sale->transaction_date->format('Y-m-d\TH:i');
        $this->isEditMode      = true;
        $this->showModal       = true;
    }

    public function save()
    {
        $this->validate([
            'storeId'         => 'required|exists:stores,id',
            'productId'       => 'nullable|exists:products,id',
            'amount'          => 'required|numeric|min:0',
            'transactionDate' => 'required|date',
        ]);

        $data = [
            'store_id'         => $this->storeId,
            'product_id'       => $this->productId ?: null,
            'amount'           => $this->amount,
            'transaction_date' => $this->transactionDate,
        ];

        if ($this->isEditMode) {
            Sale::findOrFail($this->saleId)->update($data);
            $this->dispatch('toast', message: 'Sale updated successfully.', type: 'success');
        } else {
            Sale::create($data);
            $this->dispatch('toast', message: 'Sale created successfully.', type: 'success');
        }

        $this->showModal = false;
    }

    public function delete(Sale $sale)
    {
        $sale->delete();
        $this->dispatch('toast', message: 'Sale deleted successfully.', type: 'success');
    }
}
