<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\ProductCategory;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class ProductMasterData extends Component
{
    use WithPagination;

    // Search
    public $search = '';

    // Modal state
    public $showModal = false;
    public $isEditMode = false;
    public $productId;

    // Category modal
    public $showCategoryModal = false;
    public $categoryName = '';

    // Form properties
    #[Validate('required|exists:product_categories,id')]
    public $product_category_id = '';

    #[Validate('required|string|max:255')]
    public $name = '';

    #[Validate('required|numeric|min:0')]
    public $price = '';

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function updated($propertyName)
    {
        if ($propertyName === 'search') {
            $this->resetPage();
        }
    }

    // ── Category CRUD ───────────────────────────────────────────────────────

    public function openCategoryModal()
    {
        $this->resetValidation();
        $this->categoryName = '';
        $this->showCategoryModal = true;
    }

    public function saveCategory()
    {
        $this->validate([
            'categoryName' => 'required|string|max:255|unique:product_categories,name',
        ]);

        ProductCategory::create(['name' => $this->categoryName]);

        $this->showCategoryModal = false;
        $this->categoryName = '';
        $this->dispatch('toast', message: 'Category created successfully.', type: 'success');
    }

    public function deleteCategory($id)
    {
        ProductCategory::findOrFail($id)->delete();
        $this->dispatch('toast', message: 'Category deleted successfully.', type: 'success');
    }

    // ── Product CRUD ────────────────────────────────────────────────────────

    public function create()
    {
        $this->resetValidation();
        $this->reset(['productId', 'product_category_id', 'name', 'price']);
        $this->isEditMode = false;
        $this->showModal = true;
    }

    public function edit(Product $product)
    {
        $this->resetValidation();
        $this->productId = $product->id;
        $this->product_category_id = $product->product_category_id;
        $this->name = $product->name;
        $this->price = $product->price;
        $this->isEditMode = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'product_category_id' => $this->product_category_id,
            'name' => $this->name,
            'price' => $this->price,
        ];

        if ($this->isEditMode) {
            Product::findOrFail($this->productId)->update($data);
            $this->dispatch('toast', message: 'Product updated successfully.', type: 'success');
        } else {
            Product::create($data);
            $this->dispatch('toast', message: 'Product created successfully.', type: 'success');
        }

        $this->showModal = false;
    }

    public function delete(Product $product)
    {
        $product->delete();
        $this->dispatch('toast', message: 'Product deleted successfully.', type: 'success');
    }

    public function render()
    {
        $products = Product::with('category')
            ->when($this->search, function ($query) {
                $query->where('name', 'ilike', '%' . $this->search . '%')
                      ->orWhereHas('category', fn ($q) => $q->where('name', 'ilike', '%' . $this->search . '%'));
            })
            ->latest()
            ->paginate(10);

        return view('livewire.product-master-data', [
            'products' => $products,
            'categories' => ProductCategory::orderBy('name')->get(),
        ]);
    }
}
