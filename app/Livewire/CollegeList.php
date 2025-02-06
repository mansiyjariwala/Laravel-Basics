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
    public $currentImage;
    use WithFileUploads;

    public function render()
    {
        return view('livewire.college-list')->layout('components.layouts.app');
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

            $this->reset(['name', 'location', 'image', 'currentImage']);
            session()->flash('success', 'College created successfully!');
            $this->dispatch('pg:eventRefresh-default');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to create college. Please try again.');
        }
    }

    #[\Livewire\Attributes\On('edit-college-form')]
    public function editCollege($data)
    {
        $this->id = $data['id'];
        $this->name = $data['name'];
        $this->location = $data['location'];
        $this->currentImage = $data['image'] && !str_contains($data['image'], 'Temp') ? $data['image'] : null;
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
                if ($college->image && !str_contains($college->image, 'Temp')) {
                    Storage::disk('public')->delete($college->image);
                }
                $image_path = $this->image->store('colleges', 'public');
            }

            $college->update([
                'name' => $this->name,
                'location' => $this->location,
                'image' => $image_path
            ]);

            $this->reset(['id', 'name', 'location', 'image', 'currentImage']);
            session()->flash('success', 'College updated successfully!');
            $this->dispatch('pg:eventRefresh-default');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update college. Please try again.');
        }
    }

    #[\Livewire\Attributes\On('delete-college-form')]
    public function delete($data)
    {
        try {
            $college = College::findOrFail($data['rowId']);

            // Delete the image file if it exists
            if ($college->image && !str_contains($college->image, 'Temp')) {
                Storage::disk('public')->delete($college->image);
            }
            $college->delete();

            session()->flash('success', 'College deleted successfully!');
            $this->dispatch('pg:eventRefresh-default');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete college. Please try again.');
        }
    }
}
