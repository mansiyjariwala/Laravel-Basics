<div class="p-4">
    @if (session()->has('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif

    <form wire:submit.prevent="{{ $isEditing ? 'update' : 'store' }}" class="mb-4">
        <div class="form-group">
            <input type="text" class="form-control" wire:model="name" placeholder="Category Name">
            @error('name') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <button type="submit" class="btn btn-primary">
            {{ $isEditing ? 'Update' : 'Create' }} Category
        </button>

        @if($isEditing)
            <button type="button" class="btn btn-secondary" wire:click="cancel">Cancel</button>
        @endif
    </form>

    <table class="table mt-4">
        <thead>
            <tr>
                <th>Name</th>
                <th>Slug</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($categories as $category)
                <tr>
                    <td>{{ $category->name }}</td>
                    <td>{{ $category->slug }}</td>
                    <td>
                        <button wire:click="edit({{ $category->id }})" class="btn btn-sm btn-primary">Edit</button>
                        <button wire:click="delete({{ $category->id }})" class="btn btn-sm btn-danger">Delete</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
