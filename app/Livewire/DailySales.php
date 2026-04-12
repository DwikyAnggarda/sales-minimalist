<?php

namespace App\Livewire;

use App\Exports\DailySalesExport;
use App\Models\Branch;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Sale;
use App\Models\Store;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

#[Layout('layouts.app')]
class DailySales extends Component
{
    use WithPagination;

    // Filter properties
    public $startDate;
    public $endDate;
    public $branchId = '';
    public $productCategoryId = '';

    // Modal state & Form properties
    public $showModal = false;
    public $isEditMode = false;
    public $saleId;
    public $storeId = '';
    public $productId = '';
    public $amount = '';
    public $transactionDate = '';

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

    /**
     * Build the base filtered query (shared between table + export).
     */
    private function buildQuery()
    {
        $start = $this->startDate . ' 00:00:00';
        $end   = $this->endDate   . ' 23:59:59';

        return Sale::with('store.branch', 'product.category')
            ->whereBetween('transaction_date', [$start, $end])
            ->when($this->branchId, function ($q) {
                $q->whereHas('store', fn ($sq) => $sq->where('branch_id', $this->branchId));
            })
            ->when($this->productCategoryId, function ($q) {
                $q->whereHas('product', fn ($sq) => $sq->where('product_category_id', $this->productCategoryId));
            });
    }

    // ── Sale CRUD ───────────────────────────────────────────────────────────

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

    // ── Export Methods ──────────────────────────────────────────────────────

    public function exportXlsx()
    {
        $export = new DailySalesExport(
            $this->startDate,
            $this->endDate,
            $this->branchId,
            $this->productCategoryId,
        );

        return Excel::download($export, 'daily-sales.xlsx');
    }

    public function exportPdf()
    {
        $export = new DailySalesExport(
            $this->startDate,
            $this->endDate,
            $this->branchId,
            $this->productCategoryId,
        );

        return Excel::download($export, 'daily-sales.pdf', \Maatwebsite\Excel\Excel::DOMPDF);
    }

    public function render()
    {
        $query = $this->buildQuery();

        $start = $this->startDate . ' 00:00:00';
        $end   = $this->endDate   . ' 23:59:59';

        // ── KPI Summary ─────────────────────────────────────────────────────
        $totalRevenue = (float) DB::table('sales')
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
            ? $totalRevenue / $totalTransactions
            : 0;

        $sales = $query->latest('transaction_date')->paginate(15);

        return view('livewire.daily-sales', [
            'sales'             => $sales,
            'totalRevenue'      => $totalRevenue,
            'totalTransactions' => $totalTransactions,
            'averageOrderValue' => $averageOrderValue,
            'branches'          => Branch::orderBy('name')->get(),
            'allStores'         => Store::with('branch')->orderBy('name')->get(),
            'allProducts'       => Product::with('category')->orderBy('name')->get(),
            'productCategories' => ProductCategory::orderBy('name')->get(),
        ]);
    }
}
