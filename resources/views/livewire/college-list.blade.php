{{-- Move the contents from livewire.college.blade.php to this file --}}

<div>
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        @if (session()->has('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 alert alert-success" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 alert alert-danger" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <!-- Form Section -->
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg px-4 py-4">
            <form wire:submit.prevent="{{ $id ? 'update' : 'store' }}">
                <div class="mb-4">
                    <label for="name" class="block text-gray-700 text-sm font-bold mb-2">College Name:</label>
                    <input type="text"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                           id="name"
                           wire:model="name"
                           placeholder="Enter college name">
                    @error('name') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                    <label for="location" class="block text-gray-700 text-sm font-bold mb-2">Location:</label>
                    <input type="text"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                           id="location"
                           wire:model="location"
                           placeholder="Enter college location">
                    @error('location') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                    <label for="image" class="block text-gray-700 text-sm font-bold mb-2">Image:</label>
                    <input type="file"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                           id="image"
                           wire:model="image"
                           accept="image/*">
                    @error('image') <span class="text-red-500">{{ $message }}</span> @enderror

                    {{-- Image Preview --}}
                    <div class="mt-2">
                        @if ($image && is_object($image))
                            {{-- New image preview --}}
                            <img src="{{ $image->temporaryUrl() }}"
                                 alt="Preview"
                                 class="max-w-xs h-auto rounded shadow-lg">
                        @elseif ($currentImage)
                            {{-- Existing image preview --}}
                            <img src="{{ asset('storage/' . $currentImage) }}"
                                 alt="Current Image"
                                 class="max-w-xs h-auto rounded shadow-lg">
                        @endif
                    </div>
                </div>

                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit"
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-black bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        {{ $id ? 'Update' : 'Save' }} College
                    </button>
                </div>
            </form>
        </div>

        <livewire:college-table />
    </div>
</div>
