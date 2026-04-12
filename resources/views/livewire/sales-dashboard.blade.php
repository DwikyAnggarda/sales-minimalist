<div>
    <div class="space-y-6 lg:space-y-10 font-[Inter] min-h-screen">
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap');
            @import url('https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap');
            
            .font-manrope { font-family: 'Manrope', sans-serif; }
            .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        </style>

        <!-- Page Header & Filters -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div class="shrink-0 md:max-w-[500px]">
                <h2 class="text-2xl sm:text-2xl font-extrabold font-manrope text-base-content tracking-tight">Sales Analytics</h2>
                <p class="text-base-content/70 mt-1 text-xs leading-relaxed">Comprehensive performance overview across regional branches and order volume metrics.</p>
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-3 bg-base-200/50 rounded-xl border border-base-content/10 p-4">
            {{-- FROM --}}
            <div class="flex flex-col flex-1 md:flex-none">
                <label class="text-[10px] font-bold uppercase tracking-wider text-base-content/50 mb-1">From</label>
                <input wire:model.live="startDate" type="date" class="bg-transparent border-none p-0 text-sm font-semibold focus:ring-0 cursor-pointer text-base-content min-w-[120px] outline-none w-full">
            </div>
            <div class="w-px h-8 bg-base-content/10 shrink-0"></div>
            {{-- TO --}}
            <div class="flex flex-col px-3 flex-1 md:flex-none">
                <label class="text-[10px] font-bold uppercase tracking-wider text-base-content/50 mb-1">To</label>
                <input wire:model.live="endDate" type="date" class="bg-transparent border-none p-0 text-sm font-semibold focus:ring-0 cursor-pointer text-base-content min-w-[120px] outline-none w-full">
            </div>
            <div class="w-px h-8 bg-base-content/10 shrink-0 hidden md:block"></div>
            {{-- BRANCH --}}
            <div class="flex flex-col px-3 flex-1 md:flex-none md:w-auto">
                <label class="text-[10px] font-bold uppercase tracking-wider text-base-content/50 mb-1">Branch</label>
                <select wire:model.live="branchId" class="bg-transparent border-none p-0 text-sm font-semibold focus:ring-0 cursor-pointer text-base-content outline-none w-full md:w-auto">
                    <option value="">All Branches</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-px h-8 bg-base-content/10 shrink-0 hidden md:block"></div>
            {{-- CATEGORY --}}
            <div class="flex flex-col px-3 flex-1 md:flex-none md:w-auto">
                <label class="text-[10px] font-bold uppercase tracking-wider text-base-content/50 mb-1">Category</label>
                <select wire:model.live="productCategoryId" class="bg-transparent border-none p-0 text-sm font-semibold focus:ring-0 cursor-pointer text-base-content outline-none w-full md:w-auto">
                    <option value="">All Categories</option>
                    @foreach($productCategories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-px h-8 bg-base-content/10 shrink-0 md:block hidden"></div>
            {{-- Reset --}}
            <button wire:click="resetFilters" class="bg-base-100 hover:bg-base-300 p-2.5 rounded-lg shadow-sm transition-shadow text-primary flex items-center justify-center w-full md:w-auto" title="Reset Filters">
                <span class="material-symbols-outlined text-xl">refresh</span>
            </button>
        </div>

        <!-- KPI Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Card 1 -->
            <div class="bg-base-100 p-8 rounded-[1.5rem] shadow-sm border border-base-content/5 hover:shadow-md transition-all group relative overflow-hidden">
                <div class="flex justify-between items-start mb-4 relative z-10">
                    <div class="p-2.5 bg-blue-500/10 dark:bg-blue-500/20 rounded-lg text-blue-600 dark:text-blue-400 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                        <span class="material-symbols-outlined">payments</span>
                    </div>
                </div>
                <p class="text-[11px] font-bold uppercase tracking-widest text-base-content/60 mb-1 relative z-10">Total Revenue</p>
                <h3 class="text-xl font-black font-manrope text-base-content tracking-tight relative z-10">Rp {{ number_format($stats['totalSales'], 0, ',', '.') }}</h3>
            </div>

            <!-- Card 2 -->
            <div class="bg-base-100 p-8 rounded-[1.5rem] shadow-sm border border-base-content/5 hover:shadow-md transition-all group relative overflow-hidden">
                <div class="flex justify-between items-start mb-4 relative z-10">
                    <div class="p-2.5 bg-emerald-500/10 dark:bg-emerald-500/20 rounded-lg text-emerald-600 dark:text-emerald-400 group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                        <span class="material-symbols-outlined">shopping_bag</span>
                    </div>
                </div>
                <p class="text-[11px] font-bold uppercase tracking-widest text-base-content/60 mb-1 relative z-10">Total Transactions</p>
                <h3 class="text-xl font-black font-manrope text-base-content tracking-tight relative z-10">{{ number_format($stats['totalTransactions'], 0, ',', '.') }}</h3>
            </div>

            <!-- Card 3 -->
            <div class="bg-base-100 p-8 rounded-[1.5rem] shadow-sm border border-base-content/5 hover:shadow-md transition-all group relative overflow-hidden">
                <div class="flex justify-between items-start mb-4 relative z-10">
                    <div class="p-2.5 bg-amber-500/10 dark:bg-amber-500/20 rounded-lg text-amber-600 dark:text-amber-400 group-hover:bg-amber-600 group-hover:text-white transition-colors">
                        <span class="material-symbols-outlined">calculate</span>
                    </div>
                </div>
                <p class="text-[11px] font-bold uppercase tracking-widest text-base-content/60 mb-1 relative z-10">Avg. Order Value</p>
                <h3 class="text-xl font-black font-manrope text-base-content tracking-tight relative z-10">Rp {{ number_format($stats['averageOrderValue'], 0, ',', '.') }}</h3>
            </div>

            <!-- Card 4 -->
            <div class="bg-base-100 p-8 rounded-[1.5rem] shadow-sm border border-base-content/5 hover:shadow-md transition-all group relative overflow-hidden">
                <div class="flex justify-between items-start mb-4 relative z-10">
                    <div class="p-2.5 bg-indigo-500/10 dark:bg-indigo-500/20 rounded-lg text-indigo-600 dark:text-indigo-400 group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">stars</span>
                    </div>
                </div>
                <p class="text-[11px] font-bold uppercase tracking-widest text-base-content/60 mb-1 relative z-10">Top Performer</p>
                <h3 class="text-xl font-black font-manrope text-base-content tracking-tight leading-tight relative z-10">{{ $stats['topStore'] ?: 'N/A' }}</h3>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Revenue Trend (full grid 12) -->
            <div class="col-span-12">
                <div class="bg-base-100 p-8 rounded-[1.5rem] shadow-sm border border-base-content/5 w-full overflow-hidden">
                    <div class="mb-6">
                        <h4 class="text-xl font-bold font-manrope text-base-content flex items-center">
                            <span class="material-symbols-outlined mr-2 text-blue-500">insights</span>
                            Revenue Trend
                        </h4>
                        <p class="text-sm text-base-content/60 mt-1">Daily growth visualization over selected period.</p>
                    </div>
                    <div wire:ignore class="relative w-full h-[300px]">
                        <canvas id="revenueTrendChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Top Stores Performance (grid 6) -->
            <div class="col-span-12 lg:col-span-6 flex flex-col">
                <div class="bg-base-100 p-8 rounded-[1.5rem] shadow-sm border border-base-content/5 w-full overflow-hidden flex-1">
                    <div class="mb-6">
                        <h4 class="text-xl font-bold font-manrope text-base-content flex items-center">
                            <span class="material-symbols-outlined mr-2 text-indigo-500">storefront</span>
                            Top Stores Performance
                        </h4>
                        <p class="text-sm text-base-content/60 mt-1">Revenue breakdown by store locations.</p>
                    </div>
                    <div wire:ignore class="relative w-full h-[300px]">
                        <canvas id="topStoresChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Branch Activity List (grid 6) -->
            <div class="col-span-12 lg:col-span-6 bg-base-100 p-8 rounded-[1.5rem] shadow-sm border border-base-content/5 flex flex-col overflow-hidden">
                <h4 class="text-xl font-bold font-manrope text-base-content mb-8 flex items-center">
                    <span class="material-symbols-outlined mr-2 text-emerald-500">domain</span>
                    Branch Activity
                </h4>
                
                <div class="space-y-8 flex-1">
                    @forelse($stats['revenueByBranch'] as $index => $branchData)
                        @php 
                            $bgColors = ['bg-blue-500', 'bg-emerald-500', 'bg-amber-500', 'bg-rose-500', 'bg-indigo-500', 'bg-teal-500'];
                            $textColors = ['text-blue-600 dark:text-blue-400', 'text-emerald-600 dark:text-emerald-400', 'text-amber-600 dark:text-amber-400', 'text-rose-600 dark:text-rose-400', 'text-indigo-600 dark:text-indigo-400', 'text-teal-600 dark:text-teal-400'];
                            $bgClass = $bgColors[$index % count($bgColors)];
                            $txtClass = $textColors[$index % count($textColors)];
                        @endphp
                        <div>
                            <div class="flex justify-between items-center mb-2.5">
                                <span class="text-sm font-bold text-base-content">{{ $branchData->name }}</span>
                                <span class="text-sm font-black {{ $txtClass }}">Rp {{ number_format($branchData->total, 0, ',', '.') }}</span>
                            </div>
                            <div class="w-full h-2 bg-base-200 rounded-full overflow-hidden">
                                <div class="h-full {{ $bgClass }} transition-all duration-1000 ease-out" style="width: {{ min($branchData->percentage, 100) }}%"></div>
                            </div>
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center p-8 bg-base-200/50 rounded-xl border border-dashed border-base-content/20">
                            <span class="material-symbols-outlined text-4xl text-base-content/30 mb-2">sentiment_dissatisfied</span>
                            <p class="text-sm font-medium text-base-content/50">No branch data available.</p>
                        </div>
                    @endforelse
                </div>

                <!-- Market Insight Block -->
                @if(count($stats['revenueByBranch']) > 0)
                <div class="mt-10 p-6 bg-base-200/50 rounded-xl border border-base-content/5">
                    <div class="flex items-start gap-4">
                        <div class="h-10 w-10 shrink-0 rounded-lg bg-base-100 flex items-center justify-center text-blue-500 shadow-sm border border-base-content/5">
                            <span class="material-symbols-outlined">insights</span>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-widest text-base-content/50">Market Insight</p>
                            <p class="text-xs font-semibold text-base-content mt-1.5 leading-relaxed">
                                <strong class="text-blue-500">{{ $stats['revenueByBranch'][0]->name }}</strong> is leading with {{ number_format($stats['revenueByBranch'][0]->percentage, 1) }}% of total regional sales.
                            </p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Recent Transactions Table Section -->
        <div class="bg-base-100 rounded-[1.5rem] shadow-sm overflow-hidden border border-base-content/5 pb-2">
            <div class="p-6 sm:p-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 border-b border-base-content/10">
                <div>
                    <h4 class="text-xl sm:text-2xl font-black font-manrope text-base-content">Recent Transactions</h4>
                    <p class="text-sm text-base-content/60 mt-1">Detailed log of the latest sales entries.</p>
                </div>
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <button wire:click="create" class="btn btn-primary text-white font-bold text-sm flex items-center justify-center gap-2 rounded-xl shadow-sm border-none w-full sm:w-auto relative" style="background: linear-gradient(135deg, #3525cd 0%, #4f46e5 100%);">
                        <span class="material-symbols-outlined text-lg">add</span>
                        Record New Sale
                    </button>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table id="sales-table-dashboard" class="w-full text-left whitespace-nowrap">
                    <thead>
                        <tr class="bg-base-200/30 border-b border-base-content/5">
                            <th class="px-8 py-5 text-[11px] font-bold uppercase tracking-widest text-base-content/50">Date</th>
                            <th class="px-8 py-5 text-[11px] font-bold uppercase tracking-widest text-base-content/50">Store/Branch</th>
                            <th class="px-8 py-5 text-[11px] font-bold uppercase tracking-widest text-base-content/50">Product</th>
                            <th class="px-8 py-5 text-[11px] font-bold uppercase tracking-widest text-base-content/50">Category</th>
                            <th class="px-8 py-5 text-[11px] font-bold uppercase tracking-widest text-base-content/50">Amount</th>
                            <th class="px-8 py-5 text-[11px] font-bold uppercase tracking-widest text-right text-base-content/50">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-base-content/5">
                        @forelse ($sales as $sale)
                        <tr class="hover:bg-base-200/30 transition-colors group">
                            <td class="px-8 py-5">
                                <p class="text-sm font-bold text-base-content">{{ $sale->transaction_date->format('M d, Y') }}</p>
                                <p class="text-[11px] font-semibold text-base-content/50 mt-0.5">{{ $sale->transaction_date->format('H:i') }}</p>
                            </td>
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-3">
                                    <div class="h-9 w-9 shrink-0 rounded-lg bg-base-200/80 flex items-center justify-center text-blue-500">
                                        <span class="material-symbols-outlined text-[18px]">store</span>
                                    </div>
                                    <div class="flex flex-col">
                                        <p class="text-sm font-bold text-base-content tracking-tight">{{ $sale->store->name }}</p>
                                        <p class="text-[11px] font-semibold text-base-content/50 mt-0.5">{{ $sale->store->branch->name }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-5">
                                <p class="text-sm font-bold text-base-content">{{ $sale->product->name ?? '—' }}</p>
                            </td>
                            <td class="px-8 py-5">
                                @if($sale->product && $sale->product->category)
                                    <span class="badge badge-ghost text-xs font-bold px-3 py-2 rounded-lg bg-primary/10 text-primary border-none">
                                        {{ $sale->product->category->name }}
                                    </span>
                                @else
                                    <span class="text-base-content/40 text-xs font-medium">—</span>
                                @endif
                            </td>
                            <td class="px-8 py-5">
                                <span class="text-[15px] font-black text-base-content">Rp {{ number_format($sale->amount, 0, ',', '.') }}</span>
                            </td>
                            <td class="px-8 py-5 text-right">
                                <div class="flex justify-end items-center gap-2">
                                    <button wire:click="edit({{ $sale->id }})" class="p-2 hover:bg-base-200 rounded-lg text-base-content/50 hover:text-blue-500 transition-all" title="Edit">
                                        <span class="material-symbols-outlined text-[20px]">edit</span>
                                    </button>
                                    <button wire:click="delete({{ $sale->id }})" wire:confirm="Delete this transaction?" class="p-2 hover:bg-base-200 rounded-lg text-base-content/50 hover:text-error transition-all" title="Delete">
                                        <span class="material-symbols-outlined text-[20px]">delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6">
                                <div class="flex flex-col items-center justify-center py-16 text-center">
                                    <span class="material-symbols-outlined text-5xl text-base-content/20 mb-3">receipt_long</span>
                                    <p class="text-base font-bold text-base-content">No transactions found.</p>
                                    <p class="text-xs font-medium text-base-content/50 mt-1">Try adjusting your filters to see more results.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- Pagination -->
            <div id="pagination-area-dashboard" class="p-6 sm:p-8 bg-base-100 border-t border-base-content/5">
                {{ $sales->links() }}
            </div>
        </div>

        <script>
            (function () {
                var _paginationClickedDashboard = false;

                // Event delegation on document — survives Livewire morphing
                document.addEventListener('click', function (e) {
                    var paginationArea = document.getElementById('pagination-area-dashboard');
                    if (paginationArea && paginationArea.contains(e.target)) {
                        if (e.target.closest('a, button, span[wire\\:click], nav')) {
                            _paginationClickedDashboard = true;
                        }
                    }
                }, true);

                // Livewire v4 commit hook — fires after every server roundtrip
                document.addEventListener('livewire:initialized', function () {
                    Livewire.hook('commit', function (ref) {
                        ref.succeed(function () {
                            if (_paginationClickedDashboard) {
                                _paginationClickedDashboard = false;
                                setTimeout(function () {
                                    var table = document.getElementById('sales-table-dashboard');
                                    if (table) {
                                        table.scrollIntoView({ behavior: 'smooth', block: 'start' });
                                    }
                                }, 80);
                            }
                        });
                    });
                });
            })();
        </script>

        <!-- Modal Form (Create/Edit) -->
        <input type="checkbox" id="transaction-modal" class="modal-toggle" wire:model.live="showModal" />
        <div class="modal modal-bottom sm:modal-middle" role="dialog">
            <div class="modal-box bg-base-100 rounded-t-[1.5rem] sm:rounded-[1.5rem] border border-base-content/10 sm:max-w-md p-0 shadow-xl font-[Inter]">
                <div class="bg-base-200/50 px-6 py-5 border-b border-base-content/10 flex items-center">
                    <div class="w-10 h-10 rounded-lg bg-blue-500/10 text-blue-500 flex items-center justify-center mr-3 shrink-0">
                        <span class="material-symbols-outlined text-[20px]">payments</span>
                    </div>
                    <h3 class="font-black font-manrope text-lg text-base-content">
                        {{ $isEditMode ? 'Edit Transaction' : 'Record New Sale' }}
                    </h3>
                </div>
                
                <form wire:submit="save" class="p-6 space-y-5">
                    <div class="flex flex-col">
                        <label class="text-[11px] font-bold uppercase tracking-widest text-base-content/60 mb-2">Target Store</label>
                        <select wire:model="storeId" class="select select-bordered select-md w-full bg-base-50 focus:border-primary text-base-content">
                            <option value="">Select a store...</option>
                            @foreach($allStores as $store)
                                <option value="{{ $store->id }}">{{ $store->name }} ({{ $store->branch->name }})</option>
                            @endforeach
                        </select>
                        @error('storeId') <span class="text-error text-xs font-bold mt-1.5">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex flex-col">
                        <label class="text-[11px] font-bold uppercase tracking-widest text-base-content/60 mb-2">Product</label>
                        <select wire:model="productId" class="select select-bordered select-md w-full bg-base-50 focus:border-primary text-base-content">
                            <option value="">Select a product (optional)...</option>
                            @foreach($allProducts as $product)
                                <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->category->name ?? '-' }})</option>
                            @endforeach
                        </select>
                        @error('productId') <span class="text-error text-xs font-bold mt-1.5">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex flex-col">
                        <label class="text-[11px] font-bold uppercase tracking-widest text-base-content/60 mb-2">Sale Amount (IDR)</label>
                        <div class="relative flex border border-base-content/20 rounded-lg focus-within:border-primary overflow-hidden items-stretch">
                            <span class="flex items-center justify-center px-4 bg-base-200/50 text-base-content/70 font-bold text-sm border-r border-base-content/10">Rp</span>
                            <input type="number" wire:model="amount" placeholder="0" class="input input-ghost flex-1 w-full rounded-none focus:bg-transparent focus:outline-none border-none text-base-content font-bold h-12">
                        </div>
                        @error('amount') <span class="text-error text-xs font-bold mt-1.5">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex flex-col">
                        <label class="text-[11px] font-bold uppercase tracking-widest text-base-content/60 mb-2">Date & Time</label>
                        <input type="datetime-local" wire:model="transactionDate" class="input input-bordered w-full bg-base-50 focus:border-primary text-base-content h-12">
                        @error('transactionDate') <span class="text-error text-xs font-bold mt-1.5">{{ $message }}</span> @enderror
                    </div>

                    <div class="mt-8 pt-5 border-t border-base-content/10 flex justify-end gap-3">
                        <button type="button" class="btn btn-ghost text-base-content/70 px-5 rounded-xl font-bold" wire:click="$set('showModal', false)">Cancel</button>
                        <button type="submit" class="btn btn-primary text-white px-6 rounded-xl font-bold shadow-sm border-none" style="background: linear-gradient(135deg, #3525cd 0%, #4f46e5 100%);">
                            {{ $isEditMode ? 'Save Changes' : 'Record Sale' }}
                        </button>
                    </div>
                </form>
            </div>
            <button type="button" class="modal-backdrop bg-neutral/80" wire:click="$set('showModal', false)">
                <span class="sr-only">Close</span>
            </button>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════════════
    Chart.js Setup – loaded ONCE, survives Livewire re-renders
    ═══════════════════════════════════════════════════════════════════════════ --}}
    @once
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>

        <script>
            (function () {
                var COLORS = ['#3b82f6', '#10b981', '#f59e0b', '#f43f5e', '#6366f1', '#14b8a6']; 

                function formatRp(val) {
                    return 'Rp ' + (val >= 1000000
                        ? (val / 1000000).toFixed(1) + 'M'
                        : val >= 1000 ? (val / 1000).toFixed(0) + 'K' : val);
                }

                Chart.defaults.font.family = "'Inter', system-ui, -apple-system, sans-serif";
                Chart.defaults.color = "#94a3b8"; 

                function buildTrendChart(labels, values) {
                    var el = document.getElementById('revenueTrendChart');
                    if (!el) return;
                    if (window._trendChart) { window._trendChart.destroy(); window._trendChart = null; }

                    var ctx = el.getContext('2d');
                    var gradient = ctx.createLinearGradient(0, 0, 0, 300);
                    gradient.addColorStop(0, 'rgba(59, 130, 246, 0.3)'); 
                    gradient.addColorStop(1, 'rgba(59, 130, 246, 0.0)');

                    window._trendChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Revenue',
                                data: values,
                                borderColor: '#3b82f6',
                                backgroundColor: gradient,
                                borderWidth: 3,
                                pointRadius: 0, 
                                pointHoverRadius: 6,
                                pointBackgroundColor: '#ffffff',
                                pointHoverBackgroundColor: '#3b82f6',
                                pointBorderColor: '#3b82f6',
                                pointBorderWidth: 2,
                                tension: 0.4,
                                fill: true
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            interaction: { mode: 'index', intersect: false },
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: 'rgba(15, 23, 42, 0.95)', 
                                    titleFont: { size: 13, weight: 'bold' },
                                    bodyFont: { size: 14, weight: 'bold' },
                                    padding: 12,
                                    cornerRadius: 8,
                                    displayColors: false,
                                    callbacks: { label: function (ctx) { return 'Rp ' + Number(ctx.raw).toLocaleString('id-ID'); } }
                                }
                            },
                            scales: {
                                x: {
                                    grid: { display: false, drawBorder: false },
                                    ticks: { maxTicksLimit: 7, maxRotation: 0, font: { weight: '600' } }
                                },
                                y: {
                                    beginAtZero: true,
                                    grid: { color: 'rgba(148, 163, 184, 0.2)', drawBorder: false, tickLength: 0 },
                                    ticks: { maxTicksLimit: 5, callback: formatRp, padding: 12, font: { weight: '600' } },
                                    border: { display: false }
                                }
                            }
                        }
                    });
                }

                function buildStoreChart(labels, values) {
                    var el = document.getElementById('topStoresChart');
                    if (!el) return;
                    if (window._storeChart) { window._storeChart.destroy(); window._storeChart = null; }

                    var ctx = el.getContext('2d');
                    window._storeChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Revenue',
                                data: values,
                                backgroundColor: labels.map(function (_, i) { return COLORS[i % COLORS.length]; }),
                                borderRadius: 6,
                                borderSkipped: false,
                                barPercentage: 0.6,
                                categoryPercentage: 0.8
                            }]
                        },
                        options: {
                            indexAxis: 'y',
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: 'rgba(15, 23, 42, 0.95)',
                                    titleFont: { size: 13, weight: 'bold' },
                                    bodyFont: { size: 14, weight: 'bold' },
                                    padding: 12,
                                    cornerRadius: 8,
                                    displayColors: false,
                                    callbacks: { label: function (ctx) { return 'Rp ' + Number(ctx.raw).toLocaleString('id-ID'); } }
                                }
                            },
                            scales: {
                                x: {
                                    beginAtZero: true,
                                    grid: { color: 'rgba(148, 163, 184, 0.2)', drawBorder: false, tickLength: 0 },
                                    ticks: { maxTicksLimit: 5, callback: formatRp, font: { weight: '600' }, padding: 8 },
                                    border: { display: false }
                                },
                                y: {
                                    grid: { display: false, drawBorder: false },
                                    ticks: { font: { weight: '600' }, padding: 8 },
                                    border: { display: false }
                                }
                            }
                        }
                    });
                }

                window.addEventListener('charts-updated', function (e) {
                    var d = e.detail;
                    var data = Array.isArray(d) ? d[0] : d;

                    function tryRender() {
                        if (typeof Chart === 'undefined') { setTimeout(tryRender, 100); return; }
                        buildTrendChart(data.trendLabels || [], data.trendValues || []);
                        buildStoreChart(data.storeLabels || [], data.storeValues || []);
                    }
                    tryRender();
                });
            })();
        </script>
    @endonce
</div>