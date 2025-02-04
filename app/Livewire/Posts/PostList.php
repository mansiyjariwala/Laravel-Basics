<?php

namespace App\Livewire\Posts;

use App\Models\Post;
use App\Models\Category;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class PostList extends Component
{
    use WithFileUploads;

    public $title;
    public $content;
    public $category_id;
    public $featured_image;
    public $post_id;
    public $isEditing = false;
    public $temp_image;

    protected $rules = [
        'title' => 'required|min:3',
        'content' => 'required',
        'category_id' => 'required|exists:categories,id',
        'featured_image' => 'image|max:1024' // 1MB Max
    ];

    public function render()
    {
        return view('livewire.posts.post-list', [
            'posts' => Post::with('category')->latest()->get(),
            'categories' => Category::all()
        ]);
    }

    public function store()
    {
        $this->validate();

        $image_path = null;
        if ($this->featured_image) {
            $image_path = $this->featured_image->store('posts', 'public');
        }

        Post::create([
            'title' => $this->title,
            'slug' => Str::slug($this->title),
            'content' => $this->content,
            'category_id' => $this->category_id,
            'featured_image' => $image_path
        ]);

        $this->reset(['title', 'content', 'category_id', 'featured_image']);
        session()->flash('message', 'Post created successfully!');
    }

    public function edit($id)
    {
        $this->isEditing = true;
        $this->post_id = $id;
        $post = Post::find($id);
        $this->title = $post->title;
        $this->content = $post->content;
        $this->category_id = $post->category_id;
        $this->temp_image = $post->featured_image;
    }

    public function update()
    {
        $this->validate([
            'title' => 'required|min:3',
            'content' => 'required',
            'category_id' => 'required|exists:categories,id',
            'featured_image' => 'nullable|image|max:1024'
        ]);

        $post = Post::find($this->post_id);
        $image_path = $post->featured_image;

        if ($this->featured_image) {
            if ($post->featured_image) {
                Storage::disk('public')->delete($post->featured_image);
            }
            $image_path = $this->featured_image->store('posts', 'public');
        }

        $post->update([
            'title' => $this->title,
            'slug' => Str::slug($this->title),
            'content' => $this->content,
            'category_id' => $this->category_id,
            'featured_image' => $image_path
        ]);

        $this->reset(['title', 'content', 'category_id', 'featured_image', 'isEditing', 'post_id', 'temp_image']);
        session()->flash('message', 'Post updated successfully!');
    }

    public function delete($id)
    {
        $post = Post::find($id);
        if ($post->featured_image) {
            Storage::disk('public')->delete($post->featured_image);
        }
        $post->delete();
        session()->flash('message', 'Post deleted successfully!');
    }

    public function cancel()
    {
        $this->reset(['title', 'content', 'category_id', 'featured_image', 'isEditing', 'post_id', 'temp_image']);
    }
}
