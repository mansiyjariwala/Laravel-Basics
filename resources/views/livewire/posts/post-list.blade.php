<div>
    @if (session()->has('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif

    <form wire:submit.prevent="{{ $isEditing ? 'update' : 'store' }}" enctype="multipart/form-data">
        <div class="form-group">
            <input type="text" class="form-control" wire:model="title" placeholder="Post Title">
            @error('title') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <textarea class="form-control" wire:model="content" placeholder="Post Content"></textarea>
            @error('content') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <select class="form-control" wire:model="category_id">
                <option value="">Select Category</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
            @error('category_id') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <input type="file" class="form-control" wire:model="featured_image">
            @error('featured_image') <span class="text-danger">{{ $message }}</span> @enderror

            <div wire:loading wire:target="featured_image">Uploading...</div>

            @if ($featured_image)
                <img src="{{ $featured_image->temporaryUrl() }}" width="200">
            @elseif($temp_image)
                <img src="{{ Storage::url($temp_image) }}" width="200">
            @endif
        </div>

        <button type="submit" class="btn btn-primary">
            {{ $isEditing ? 'Update' : 'Create' }} Post
        </button>

        @if($isEditing)
            <button type="button" class="btn btn-secondary" wire:click="cancel">Cancel</button>
        @endif
    </form>

    <table class="table mt-4">
        <thead>
            <tr>
                <th>Title</th>
                <th>Category</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($posts as $post)
                <tr>
                    <td>{{ $post->title }}</td>
                    <td>{{ $post->category->name }}</td>
                    <td>
                        @if($post->featured_image)
                            <img src="{{ Storage::url($post->featured_image) }}" width="100">
                        @endif
                    </td>
                    <td>
                        <button wire:click="edit({{ $post->id }})" class="btn btn-sm btn-primary">Edit</button>
                        <button wire:click="delete({{ $post->id }})" class="btn btn-sm btn-danger">Delete</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
