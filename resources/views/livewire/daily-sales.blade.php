<div>
    <div class="space-y-6 lg:space-y-10 font-[Inter] min-h-screen">
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap');
            @import url('https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap');
            .font-manrope { font-family: 'Manrope', sans-serif; }
            .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        </style>

        {{-- Page Header --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div class="shrink-0">
                <h2 class="text-2xl font-extrabold font-manrope text-base-content tracking-tight">Daily Sales</h2>
                <p class="text-base-content/70 mt-1 text-xs leading-relaxed">Detailed sales report with advanced filtering and export capabilities.</p>
            </div>
        </div>

        {{-- Filter Bar --}}
        <div class="flex flex-wrap items-center gap-3 bg-base-200/50 rounded-xl border border-base-content/10 p-4">
            {{-- FROM --}}
            <div class="flex flex-col flex-1 md:flex-none">
                <label class="text-[10px] font-bold uppercase tracking-wider text-base-content/50 mb-1">From</label>
                <input wire:model.live="startDate" type="date"
                       class="bg-transparent border-none p-0 text-sm font-semibold focus:ring-0 cursor-pointer text-base-content min-w-[120px] outline-none w-full" />
            </div>
            <div class="w-px h-8 bg-base-content/10 shrink-0"></div>
            {{-- TO --}}
            <div class="flex flex-col px-3 flex-1 md:flex-none">
                <label class="text-[10px] font-bold uppercase tracking-wider text-base-content/50 mb-1">To</label>
                <input wire:model.live="endDate" type="date"
                       class="bg-transparent border-none p-0 text-sm font-semibold focus:ring-0 cursor-pointer text-base-content min-w-[120px] outline-none w-full" />
            </div>
            <div class="w-px h-8 bg-base-content/10 shrink-0 hidden md:block"></div>
            {{-- BRANCH --}}
            <div class="flex flex-col px-3 flex-1 md:flex-none md:w-auto">
                <label class="text-[10px] font-bold uppercase tracking-wider text-base-content/50 mb-1">Branch</label>
                <select wire:model.live="branchId"
                        class="bg-transparent border-none p-0 text-sm font-semibold focus:ring-0 cursor-pointer text-base-content outline-none w-full md:w-auto">
                    <option value="">All Branches</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-px h-8 bg-base-content/10 shrink-0 hidden md:block"></div>
            {{-- PRODUCT CATEGORY --}}
            <div class="flex flex-col px-3 flex-1 md:flex-none md:w-auto">
                <label class="text-[10px] font-bold uppercase tracking-wider text-base-content/50 mb-1">Category</label>
                <select wire:model.live="productCategoryId"
                        class="bg-transparent border-none p-0 text-sm font-semibold focus:ring-0 cursor-pointer text-base-content outline-none w-full md:w-auto">
                    <option value="">All Categories</option>
                    @foreach($productCategories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-px h-8 bg-base-content/10 shrink-0 hidden md:block"></div>
            {{-- Reset --}}
            <button wire:click="resetFilters"
                    class="bg-base-100 hover:bg-base-300 p-2.5 rounded-lg shadow-sm transition-shadow text-primary flex items-center justify-center w-full md:w-auto"
                    title="Reset Filters">
                <span class="material-symbols-outlined text-xl">refresh</span>
            </button>
        </div>

        {{-- KPI Summary Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Total Revenue --}}
            <div class="bg-base-100 p-8 rounded-[1.5rem] shadow-sm border border-base-content/5 hover:shadow-md transition-all group relative overflow-hidden">
                <div class="flex justify-between items-start mb-4 relative z-10">
                    <div class="p-2.5 bg-blue-500/10 dark:bg-blue-500/20 rounded-lg text-blue-600 dark:text-blue-400 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                        <span class="material-symbols-outlined">payments</span>
                    </div>
                </div>
                <p class="text-[11px] font-bold uppercase tracking-widest text-base-content/60 mb-1 relative z-10">Total Revenue</p>
                <h3 class="text-xl font-black font-manrope text-base-content tracking-tight relative z-10">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
            </div>
            {{-- Total Transactions --}}
            <div class="bg-base-100 p-8 rounded-[1.5rem] shadow-sm border border-base-content/5 hover:shadow-md transition-all group relative overflow-hidden">
                <div class="flex justify-between items-start mb-4 relative z-10">
                    <div class="p-2.5 bg-emerald-500/10 dark:bg-emerald-500/20 rounded-lg text-emerald-600 dark:text-emerald-400 group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                        <span class="material-symbols-outlined">shopping_bag</span>
                    </div>
                </div>
                <p class="text-[11px] font-bold uppercase tracking-widest text-base-content/60 mb-1 relative z-10">Total Transactions</p>
                <h3 class="text-xl font-black font-manrope text-base-content tracking-tight relative z-10">{{ number_format($totalTransactions, 0, ',', '.') }}</h3>
            </div>
            {{-- Avg Order Value --}}
            <div class="bg-base-100 p-8 rounded-[1.5rem] shadow-sm border border-base-content/5 hover:shadow-md transition-all group relative overflow-hidden">
                <div class="flex justify-between items-start mb-4 relative z-10">
                    <div class="p-2.5 bg-amber-500/10 dark:bg-amber-500/20 rounded-lg text-amber-600 dark:text-amber-400 group-hover:bg-amber-600 group-hover:text-white transition-colors">
                        <span class="material-symbols-outlined">calculate</span>
                    </div>
                </div>
                <p class="text-[11px] font-bold uppercase tracking-widest text-base-content/60 mb-1 relative z-10">Avg. Order Value</p>
                <h3 class="text-xl font-black font-manrope text-base-content tracking-tight relative z-10">Rp {{ number_format($averageOrderValue, 0, ',', '.') }}</h3>
            </div>
        </div>

        {{-- Sales Table --}}
        <div class="bg-base-100 rounded-[1.5rem] shadow-sm overflow-hidden border border-base-content/5 pb-2">
            <div class="p-6 sm:p-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 border-b border-base-content/10">
                <div>
                    <h4 class="text-xl sm:text-2xl font-black font-manrope text-base-content">Sales Records</h4>
                    <p class="text-sm text-base-content/60 mt-1">Detailed transaction log for the selected period.</p>
                </div>
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <button wire:click="create"
                            class="btn btn-sm rounded-xl font-bold text-xs text-white shadow-sm border-none flex-1 sm:flex-none"
                            style="background: linear-gradient(135deg, #3525cd 0%, #4f46e5 100%);">
                        <span class="material-symbols-outlined text-base">add</span>
                        Record Sale
                    </button>
                    <button wire:click="exportXlsx"
                            class="btn btn-sm rounded-xl font-bold text-xs border border-base-content/10 bg-base-100 hover:bg-emerald-50 hover:border-emerald-300 hover:text-emerald-600 transition-all flex-1 sm:flex-none">
                        <span class="material-symbols-outlined text-base">table_view</span>
                        XLSX
                    </button>
                    <button wire:click="exportPdf"
                            class="btn btn-sm rounded-xl font-bold text-xs border border-base-content/10 bg-base-100 hover:bg-red-50 hover:border-red-300 hover:text-red-600 transition-all flex-1 sm:flex-none">
                        <span class="material-symbols-outlined text-base">picture_as_pdf</span>
                        PDF
                    </button>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table id="sales-table-daily" class="w-full text-left whitespace-nowrap">
                    <thead>
                        <tr class="bg-base-200/30 border-b border-base-content/5">
                            <th class="px-8 py-5 text-[11px] font-bold uppercase tracking-widest text-base-content/50">#</th>
                            <th class="px-8 py-5 text-[11px] font-bold uppercase tracking-widest text-base-content/50">Date</th>
                            <th class="px-8 py-5 text-[11px] font-bold uppercase tracking-widest text-base-content/50">Store / Branch</th>
                            <th class="px-8 py-5 text-[11px] font-bold uppercase tracking-widest text-base-content/50">Product</th>
                            <th class="px-8 py-5 text-[11px] font-bold uppercase tracking-widest text-base-content/50">Category</th>
                            <th class="px-8 py-5 text-[11px] font-bold uppercase tracking-widest text-base-content/50">Amount</th>
                            <th class="px-8 py-5 text-[11px] font-bold uppercase tracking-widest text-right text-base-content/50">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-base-content/5">
                        @forelse ($sales as $index => $sale)
                        <tr class="hover:bg-base-200/30 transition-colors group">
                            <td class="px-8 py-5 text-sm text-base-content/60 font-semibold">
                                {{ $sales->firstItem() + $index }}
                            </td>
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
                                        <p class="text-sm font-bold text-base-content tracking-tight">{{ $sale->store->name ?? '-' }}</p>
                                        <p class="text-[11px] font-semibold text-base-content/50 mt-0.5">{{ $sale->store->branch->name ?? '-' }}</p>
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
                            <td colspan="7">
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
            {{-- Pagination --}}
            <div id="pagination-area-daily" class="p-6 sm:p-8 bg-base-100 border-t border-base-content/5">
                {{ $sales->links() }}
            </div>
        </div>

        <script>
            (function () {
                var _paginationClickedDaily = false;

                // Event delegation on document — survives Livewire morphing
                document.addEventListener('click', function (e) {
                    var paginationArea = document.getElementById('pagination-area-daily');
                    if (paginationArea && paginationArea.contains(e.target)) {
                        if (e.target.closest('a, button, span[wire\\:click], nav')) {
                            _paginationClickedDaily = true;
                        }
                    }
                }, true);

                // Livewire v4 commit hook — fires after every server roundtrip
                document.addEventListener('livewire:initialized', function () {
                    Livewire.hook('commit', function (ref) {
                        ref.succeed(function () {
                            if (_paginationClickedDaily) {
                                _paginationClickedDaily = false;
                                setTimeout(function () {
                                    var table = document.getElementById('sales-table-daily');
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

        {{-- ══════════════════════════════════════════════════════════════════════
             Sale Create/Edit Modal
        ══════════════════════════════════════════════════════════════════════ --}}
        <input type="checkbox" id="daily-sale-modal" class="modal-toggle" wire:model.live="showModal" />
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
                    {{-- Store --}}
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

                    {{-- Product --}}
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

                    {{-- Amount --}}
                    <div class="flex flex-col">
                        <label class="text-[11px] font-bold uppercase tracking-widest text-base-content/60 mb-2">Sale Amount (IDR)</label>
                        <div class="relative flex border border-base-content/20 rounded-lg focus-within:border-primary overflow-hidden items-stretch">
                            <span class="flex items-center justify-center px-4 bg-base-200/50 text-base-content/70 font-bold text-sm border-r border-base-content/10">Rp</span>
                            <input type="number" wire:model="amount" placeholder="0"
                                   class="input input-ghost flex-1 w-full rounded-none focus:bg-transparent focus:outline-none border-none text-base-content font-bold h-12" />
                        </div>
                        @error('amount') <span class="text-error text-xs font-bold mt-1.5">{{ $message }}</span> @enderror
                    </div>

                    {{-- Date & Time --}}
                    <div class="flex flex-col">
                        <label class="text-[11px] font-bold uppercase tracking-widest text-base-content/60 mb-2">Date & Time</label>
                        <input type="datetime-local" wire:model="transactionDate"
                               class="input input-bordered w-full bg-base-50 focus:border-primary text-base-content h-12" />
                        @error('transactionDate') <span class="text-error text-xs font-bold mt-1.5">{{ $message }}</span> @enderror
                    </div>

                    <div class="mt-8 pt-5 border-t border-base-content/10 flex justify-end gap-3">
                        <button type="button" class="btn btn-ghost text-base-content/70 px-5 rounded-xl font-bold" wire:click="$set('showModal', false)">Cancel</button>
                        <button type="submit" class="btn btn-primary text-white px-6 rounded-xl font-bold shadow-sm border-none"
                                style="background: linear-gradient(135deg, #3525cd 0%, #4f46e5 100%);">
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
</div>
