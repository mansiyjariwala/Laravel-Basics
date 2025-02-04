<?php

namespace App\Livewire;

use App\Models\College;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class CollegeList extends Component
{
    public $name;
    public $location;
    public $image;
    public $id;
    use WithFileUploads;

    public function render()
    {
        return view('livewire.college-list', [
            'colleges' => College::all()
        ])->layout('components.layouts.app');
    }

    protected function rules()
    {
        return [
            'name' => 'required|min:5',
            'location' => 'required|min:5',
            'image' => 'required|image|max:1024',
        ];
    }

    public function store()
    {
        try {
            $validated = $this->validate([
                'name' => 'required|min:3',
                'location' => 'required',
                'image' => 'nullable|image|max:1024'
            ]);

            $image_path = null;
            if ($this->image) {
                $image_path = $this->image->store('colleges', 'public');
            }

            College::create([
                'name' => $this->name,
                'location' => $this->location,
                'image' => $image_path
            ]);

            $this->reset(['name', 'location', 'image']);
            session()->flash('success', 'College created successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to create college. Please try again.');
        }
    }

    public function edit($id)
    {
        $college = College::find($id);
        $this->id = $id;
        $this->name = $college->name;
        $this->location = $college->location;
        $this->image = $college->image;
    }

    public function update()
    {
        try {
            $validated = $this->validate([
                'name' => 'required|min:3',
                'location' => 'required',
                'image' => 'nullable|image|max:1024'
            ]);

            $college = College::find($this->id);
            $image_path = $college->image;

            if ($this->image) {
                if ($college->image) {
                    Storage::disk('public')->delete($college->image);
                }
                $image_path = $this->image->store('colleges', 'public');
            }

            $college->update([
                'name' => $this->name,
                'location' => $this->location,
                'image' => $image_path
            ]);

            $this->reset(['id', 'name', 'location', 'image']);
            session()->flash('success', 'College updated successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update college. Please try again.');
        }
    }

    public function delete($id)
    {
        try {
            $college = College::findOrFail($id);
            if ($college->image) {
                Storage::disk('public')->delete($college->image);
            }
            $college->delete();
            session()->flash('success', 'College deleted successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete college. Please try again.');
        }
    }
}
