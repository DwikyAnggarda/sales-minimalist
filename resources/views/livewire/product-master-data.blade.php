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
                <h2 class="text-2xl font-extrabold font-manrope text-base-content tracking-tight">Product Master Data</h2>
                <p class="text-base-content/70 mt-1 text-xs leading-relaxed">Manage product categories and product inventory.</p>
            </div>
        </div>

        {{-- Filter & Actions Bar --}}
        <div class="flex flex-wrap items-center gap-3 bg-base-200/50 rounded-xl border border-base-content/10 p-4">
            {{-- Search --}}
            <div class="flex flex-col flex-1 min-w-[200px]">
                <label class="text-[10px] font-bold uppercase tracking-wider text-base-content/50 mb-1">Search</label>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search products or categories..."
                       class="bg-transparent border-none p-0 text-sm font-semibold focus:ring-0 text-base-content outline-none w-full" />
            </div>

            <div class="w-px h-8 bg-base-content/10 shrink-0 hidden md:block"></div>

            {{-- Buttons --}}
            <div class="flex gap-2 w-full md:w-auto">
                <button wire:click="openCategoryModal"
                        class="btn btn-sm rounded-xl font-bold text-xs text-white shadow-sm border-none flex-1 md:flex-none"
                        style="background: linear-gradient(135deg, #3525cd 0%, #4f46e5 100%);">
                    <span class="material-symbols-outlined text-base">category</span>
                    Add Category
                </button>
                <button wire:click="create"
                        class="btn btn-primary text-white btn-sm rounded-xl font-bold text-xs shadow-sm border-none flex-1 md:flex-none"
                        style="background: linear-gradient(135deg, #3525cd 0%, #4f46e5 100%);">
                    <span class="material-symbols-outlined text-base">add</span>
                    Add Product
                </button>
            </div>
        </div>

        {{-- Categories Overview --}}
        @if($categories->count() > 0)
        <div class="flex flex-wrap gap-2">
            @foreach($categories as $cat)
                <div class="badge badge-lg gap-2 bg-base-100 border border-base-content/10 text-base-content font-bold text-xs px-4 py-3 rounded-xl shadow-sm">
                    <span class="material-symbols-outlined text-sm text-primary">label</span>
                    {{ $cat->name }}
                    <span class="text-base-content/40">({{ $cat->products_count ?? $cat->products()->count() }})</span>
                    <button wire:click="deleteCategory({{ $cat->id }})" wire:confirm="Delete category '{{ $cat->name }}'? All its products will also be deleted."
                            class="ml-1 hover:text-error transition-colors">
                        <span class="material-symbols-outlined text-sm">close</span>
                    </button>
                </div>
            @endforeach
        </div>
        @endif

        {{-- Products Table --}}
        <div class="bg-base-100 rounded-[1.5rem] shadow-sm overflow-hidden border border-base-content/5 pb-2">
            <div class="p-6 sm:p-8 border-b border-base-content/10">
                <h4 class="text-xl font-black font-manrope text-base-content">Product List</h4>
                <p class="text-sm text-base-content/60 mt-1">All registered products with categories and pricing.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left whitespace-nowrap">
                    <thead>
                        <tr class="bg-base-200/30 border-b border-base-content/5">
                            <th class="px-8 py-5 text-[11px] font-bold uppercase tracking-widest text-base-content/50">#</th>
                            <th class="px-8 py-5 text-[11px] font-bold uppercase tracking-widest text-base-content/50">Product Name</th>
                            <th class="px-8 py-5 text-[11px] font-bold uppercase tracking-widest text-base-content/50">Category</th>
                            <th class="px-8 py-5 text-[11px] font-bold uppercase tracking-widest text-base-content/50">Price</th>
                            <th class="px-8 py-5 text-[11px] font-bold uppercase tracking-widest text-base-content/50">Created</th>
                            <th class="px-8 py-5 text-[11px] font-bold uppercase tracking-widest text-right text-base-content/50">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-base-content/5">
                        @forelse ($products as $index => $product)
                        <tr class="hover:bg-base-200/30 transition-colors group">
                            <td class="px-8 py-5 text-sm text-base-content/60 font-semibold">
                                {{ $products->firstItem() + $index }}
                            </td>
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-3">
                                    <div class="h-9 w-9 shrink-0 rounded-lg bg-base-200/80 flex items-center justify-center text-indigo-500">
                                        <span class="material-symbols-outlined text-[18px]">inventory_2</span>
                                    </div>
                                    <p class="text-sm font-bold text-base-content tracking-tight">{{ $product->name }}</p>
                                </div>
                            </td>
                            <td class="px-8 py-5">
                                <span class="badge badge-ghost text-xs font-bold px-3 py-2 rounded-lg bg-primary/10 text-primary border-none">
                                    {{ $product->category->name }}
                                </span>
                            </td>
                            <td class="px-8 py-5">
                                <span class="text-[15px] font-black text-base-content">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                            </td>
                            <td class="px-8 py-5">
                                <p class="text-sm text-base-content/60 font-medium">{{ $product->created_at->format('d M Y') }}</p>
                            </td>
                            <td class="px-8 py-5 text-right">
                                <div class="flex justify-end items-center gap-2">
                                    <button wire:click="edit({{ $product->id }})"
                                            class="p-2 hover:bg-base-200 rounded-lg text-base-content/50 hover:text-blue-500 transition-all" title="Edit">
                                        <span class="material-symbols-outlined text-[20px]">edit</span>
                                    </button>
                                    <button wire:click="delete({{ $product->id }})" wire:confirm="Delete product '{{ $product->name }}'?"
                                            class="p-2 hover:bg-base-200 rounded-lg text-base-content/50 hover:text-error transition-all" title="Delete">
                                        <span class="material-symbols-outlined text-[20px]">delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6">
                                <div class="flex flex-col items-center justify-center py-16 text-center">
                                    <span class="material-symbols-outlined text-5xl text-base-content/20 mb-3">inventory_2</span>
                                    <p class="text-base font-bold text-base-content">No products found.</p>
                                    <p class="text-xs font-medium text-base-content/50 mt-1">Create a category first, then add your products.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{-- Pagination --}}
            <div class="p-6 sm:p-8 bg-base-100 border-t border-base-content/5">
                {{ $products->links() }}
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════════════════
             Product Create/Edit Modal
        ══════════════════════════════════════════════════════════════════════ --}}
        <input type="checkbox" id="product-modal" class="modal-toggle" wire:model.live="showModal" />
        <div class="modal modal-bottom sm:modal-middle" role="dialog">
            <div class="modal-box bg-base-100 rounded-t-[1.5rem] sm:rounded-[1.5rem] border border-base-content/10 sm:max-w-md p-0 shadow-xl font-[Inter]">
                <div class="bg-base-200/50 px-6 py-5 border-b border-base-content/10 flex items-center">
                    <div class="w-10 h-10 rounded-lg bg-indigo-500/10 text-indigo-500 flex items-center justify-center mr-3 shrink-0">
                        <span class="material-symbols-outlined text-[20px]">inventory_2</span>
                    </div>
                    <h3 class="font-black font-manrope text-lg text-base-content">
                        {{ $isEditMode ? 'Edit Product' : 'Add New Product' }}
                    </h3>
                </div>

                <form wire:submit="save" class="p-6 space-y-5">
                    {{-- Category --}}
                    <div class="flex flex-col">
                        <label class="text-[11px] font-bold uppercase tracking-widest text-base-content/60 mb-2">Category</label>
                        <select wire:model="product_category_id" class="select select-bordered select-md w-full bg-base-50 focus:border-primary text-base-content">
                            <option value="">Select a category...</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('product_category_id') <span class="text-error text-xs font-bold mt-1.5">{{ $message }}</span> @enderror
                    </div>

                    {{-- Product Name --}}
                    <div class="flex flex-col">
                        <label class="text-[11px] font-bold uppercase tracking-widest text-base-content/60 mb-2">Product Name</label>
                        <input type="text" wire:model="name" placeholder="Enter product name..."
                               class="input input-bordered w-full bg-base-50 focus:border-primary text-base-content h-12" />
                        @error('name') <span class="text-error text-xs font-bold mt-1.5">{{ $message }}</span> @enderror
                    </div>

                    {{-- Price --}}
                    <div class="flex flex-col">
                        <label class="text-[11px] font-bold uppercase tracking-widest text-base-content/60 mb-2">Unit Price (IDR)</label>
                        <div class="relative flex border border-base-content/20 rounded-lg focus-within:border-primary overflow-hidden items-stretch">
                            <span class="flex items-center justify-center px-4 bg-base-200/50 text-base-content/70 font-bold text-sm border-r border-base-content/10">Rp</span>
                            <input type="number" wire:model="price" placeholder="0"
                                   class="input input-ghost flex-1 w-full rounded-none focus:bg-transparent focus:outline-none border-none text-base-content font-bold h-12" />
                        </div>
                        @error('price') <span class="text-error text-xs font-bold mt-1.5">{{ $message }}</span> @enderror
                    </div>

                    <div class="mt-8 pt-5 border-t border-base-content/10 flex justify-end gap-3">
                        <button type="button" class="btn btn-ghost text-base-content/70 px-5 rounded-xl font-bold" wire:click="$set('showModal', false)">Cancel</button>
                        <button type="submit" class="btn btn-primary text-white px-6 rounded-xl font-bold shadow-sm border-none"
                                style="background: linear-gradient(135deg, #3525cd 0%, #4f46e5 100%);">
                            {{ $isEditMode ? 'Save Changes' : 'Add Product' }}
                        </button>
                    </div>
                </form>
            </div>
            <button type="button" class="modal-backdrop bg-neutral/80" wire:click="$set('showModal', false)">
                <span class="sr-only">Close</span>
            </button>
        </div>

        {{-- ══════════════════════════════════════════════════════════════════════
             Category Create Modal
        ══════════════════════════════════════════════════════════════════════ --}}
        <input type="checkbox" id="category-modal" class="modal-toggle" wire:model.live="showCategoryModal" />
        <div class="modal modal-bottom sm:modal-middle" role="dialog">
            <div class="modal-box bg-base-100 rounded-t-[1.5rem] sm:rounded-[1.5rem] border border-base-content/10 sm:max-w-sm p-0 shadow-xl font-[Inter]">
                <div class="bg-base-200/50 px-6 py-5 border-b border-base-content/10 flex items-center">
                    <div class="w-10 h-10 rounded-lg bg-emerald-500/10 text-emerald-500 flex items-center justify-center mr-3 shrink-0">
                        <span class="material-symbols-outlined text-[20px]">category</span>
                    </div>
                    <h3 class="font-black font-manrope text-lg text-base-content">Add Category</h3>
                </div>

                <form wire:submit="saveCategory" class="p-6 space-y-5">
                    <div class="flex flex-col">
                        <label class="text-[11px] font-bold uppercase tracking-widest text-base-content/60 mb-2">Category Name</label>
                        <input type="text" wire:model="categoryName" placeholder="e.g. Beverages, Snacks..."
                               class="input input-bordered w-full bg-base-50 focus:border-primary text-base-content h-12" />
                        @error('categoryName') <span class="text-error text-xs font-bold mt-1.5">{{ $message }}</span> @enderror
                    </div>

                    <div class="mt-8 pt-5 border-t border-base-content/10 flex justify-end gap-3">
                        <button type="button" class="btn btn-ghost text-base-content/70 px-5 rounded-xl font-bold" wire:click="$set('showCategoryModal', false)">Cancel</button>
                        <button type="submit" class="btn btn-success text-white px-6 rounded-xl font-bold shadow-sm border-none">
                            Create Category
                        </button>
                    </div>
                </form>
            </div>
            <button type="button" class="modal-backdrop bg-neutral/80" wire:click="$set('showCategoryModal', false)">
                <span class="sr-only">Close</span>
            </button>
        </div>
    </div>
</div>
