<?php

namespace App\Livewire\Categories;

use App\Models\Category;
use Livewire\Component;
use Illuminate\Support\Str;

class CategoryList extends Component
{
    public $name;
    public $category_id;
    public $isEditing = false;

    protected $rules = [
        'name' => 'required|min:3'
    ];

    public function render()
    {
        return view('livewire.categories.category-list', [
            'categories' => Category::latest()->get()
        ])->layout('components.layouts.app');
    }

    public function store()
    {
        $this->validate();

        Category::create([
            'name' => $this->name,
            'slug' => Str::slug($this->name)
        ]);

        $this->reset('name');
        session()->flash('message', 'Category created successfully!');
    }

    public function edit($id)
    {
        $this->isEditing = true;
        $this->category_id = $id;
        $this->name = Category::find($id)->name;
    }

    public function update()
    {
        $this->validate();

        Category::find($this->category_id)->update([
            'name' => $this->name,
            'slug' => Str::slug($this->name)
        ]);

        $this->reset(['name', 'isEditing', 'category_id']);
        session()->flash('message', 'Category updated successfully!');
    }

    public function delete($id)
    {
        Category::find($id)->delete();
        session()->flash('message', 'Category deleted successfully!');
    }

    public function cancel()
    {
        $this->reset(['name', 'isEditing', 'category_id']);
    }
}
