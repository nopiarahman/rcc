<div class="max-w-5xl mx-auto p-6">
    <h2 class="text-2xl font-semibold mb-4 mt-5 dark:text-white">
        {{ $editing ? 'Edit Banner' : 'Add New Banner' }}
    </h2>

    <div class="max-w-sm">
        <form wire:submit.prevent="saveBanner">
    @if (session()->has('success'))
        <div class="p-4 mb-4 text-green-700 bg-green-100 rounded">
            {{ session('success') }}
        </div>
    @endif
    
    @if($errors->any())
        <div class="p-4 mb-4 text-red-700 bg-red-100 rounded">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif
        <form wire:submit.prevent="saveBanner">
            <flux:field class="mb-2">
                <flux:label>Title</flux:label>
                <flux:input type="text" wire:model.defer="title" />
                <flux:error name="title" />
            </flux:field>

            <flux:field class="mb-2">
                <flux:label>Description</flux:label>
                <flux:textarea wire:model.defer="description" />
                <flux:error name="description" />
            </flux:field>

            <flux:field class="mb-2">
                <flux:label>Image</flux:label>
                <flux:input type="file" wire:model="image" />
                <flux:error name="image" />
            </flux:field>

            <flux:field class="mb-2">
                <flux:label>Status</flux:label>
                <flux:select wire:model.defer="status">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </flux:select>
                <flux:error name="status" />
            </flux:field>

            <flux:field class="mb-2">
                <flux:label>Order</flux:label>
                <flux:input type="number" wire:model.defer="order" />
                <flux:error name="order" />
            </flux:field>
            
            <div class="flex items-center space-x-2">
                <flux:button type="submit" variant="primary">
                    {{ $editing ? 'Update' : 'Save' }}
                </flux:button>
                @if ($editing)
                    <flux:button wire:click="resetFields">
                        Cancel
                    </flux:button>
                @endif
            </div>
        </form>
    </div>
    
    <h2 class="text-xl font-semibold mt-5 mb-4 dark:text-white">
        Banner List
    </h2>
    
    <div class="overflow-x-auto shadow-lg rounded-lg">
        <table class="table-auto w-full border-collapse text-base text-gray-700 dark:bg-stone-50" style="text-align: left">
            <thead class="bg-gray-50 text-gray-800 uppercase tracking-wide">
                <tr>
                    <th class="px-6 py-4 border-b border-gray-300">#</th>
                    <th class="px-6 py-4 border-b border-gray-300">Image</th>
                    <th class="px-6 py-4 border-b border-gray-300">Title</th>
                    <th class="px-6 py-4 border-b border-gray-300">Description</th>
                    <th class="px-6 py-4 border-b border-gray-300">Status</th>
                    <th class="px-6 py-4 border-b border-gray-300">Order</th>
                    <th class="px-6 py-4 border-b border-gray-300">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($banners as $key => $banner)
                    <tr class="hover:bg-gray-50 transition duration-200 bg-white" style="line-height: 1">
                        <td class="px-6 py-2 border-b border-gray-200">{{ $key + 1 }}</td>
                        <td class="px-6 py-2 border-b border-gray-200">
                            @if($banner->hasMedia('banners'))
                                <img src="{{ $banner->getFirstMediaUrl('banners', 'thumb') }}" alt="Banner Image" class="h-16 w-auto object-cover rounded">
                            @else
                                <div class="h-16 w-24 bg-gray-100 flex items-center justify-center text-gray-400 rounded">
                                    No Image
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-2 border-b border-gray-200">{{ $banner->title }}</td>
                        <td class="px-6 py-2 border-b border-gray-200">{{ $banner->description }}</td>
                        <td class="px-6 py-2 border-b border-gray-200">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $banner->status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $banner->status ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-2 border-b border-gray-200">{{ $banner->order }}</td>
                        <td class="px-6 py-2 border-b border-gray-200 space-x-2">
                            <flux:button size="sm" wire:click="editBanner({{ $banner->id }})">Edit</flux:button>
                            <flux:button 
                                size="sm" 
                                variant="danger" 
                                x-data 
                                x-on:click="if (confirm('Are you sure?')) { $wire.deleteBanner({{ $banner->id }}) }"
                            >
                                Delete
                            </flux:button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
