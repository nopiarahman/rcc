<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold dark:text-white">Manage Discount Codes</h2>
        <flux:button color="primary" wire:click="openForm">
            <flux:icon name="plus" class="mr-1" /> Add Discount Code
        </flux:button>
    </div>

    @if (session()->has('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <!-- Filters -->
    <div class="flex flex-col md:flex-row gap-4 mb-6">
        <div class="flex-1">
            <flux:input type="text" wire:model.live.debounce.300ms="search" placeholder="Search discount codes..." />
        </div>
        <div class="w-full md:w-64">
            <flux:select wire:model.live="filterActive">
                <flux:select.option value="all">All Codes</flux:select.option>
                <flux:select.option value="active">Active Now</flux:select.option>
                <flux:select.option value="inactive">Inactive</flux:select.option>
            </flux:select>
        </div>
    </div>

    <!-- Discount Codes Table -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-zinc-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Code</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Discount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Min. Purchase</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Usage</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Period</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-zinc-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($discountCodes as $discountCode)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-mono font-bold text-indigo-600 dark:text-indigo-400">{{ $discountCode->code }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            <div class="font-medium">{{ $discountCode->name }}</div>
                            @if ($discountCode->description)
                                <div class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ Str::limit($discountCode->description, 50) }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                            <div class="font-medium">{{ $discountCode->formatted_discount }}</div>
                            <div class="text-xs text-gray-400 dark:text-gray-500">{{ $discountCode->discount_type }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                            Rp {{ number_format($discountCode->minimum_purchase, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                            <div>{{ $discountCode->used_count }} used</div>
                            @if ($discountCode->max_redeem)
                                <div class="text-xs text-gray-400 dark:text-gray-500">{{ $discountCode->remainingUses }} left</div>
                            @else
                                <div class="text-xs text-gray-400 dark:text-gray-500">Unlimited</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                            <div>{{ $discountCode->start_date->format('d M Y H:i') }}</div>
                            <div class="text-xs text-gray-400 dark:text-gray-500">to {{ $discountCode->end_date->format('d M Y H:i') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if ($discountCode->isActive())
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                                    Active
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                    Inactive
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end space-x-2">
                                <button wire:click="toggleActive({{ $discountCode->id }})" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300" title="Toggle Active">
                                    {{ $discountCode->is_active ? 'Disable' : 'Enable' }}
                                </button>
                                <button wire:click="duplicate({{ $discountCode->id }})" class="text-purple-600 dark:text-purple-400 hover:text-purple-900 dark:hover:text-purple-300" title="Duplicate">
                                    Copy
                                </button>
                                <button wire:click="edit({{ $discountCode->id }})" class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300" title="Edit">
                                    Edit
                                </button>
                                <button wire:click="confirmDelete({{ $discountCode->id }})" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300" title="Delete">
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">
                            No discount codes found. Create your first discount code by clicking the "Add Discount Code" button.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $discountCodes->links() }}
    </div>

    <!-- Add/Edit Discount Code Modal -->
    <flux:modal title="{{ $editMode ? 'Edit Discount Code' : 'Add New Discount Code' }}" wire:model="showForm">
        <form wire:submit.prevent="save" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <flux:label>Discount Code</flux:label>
                    <div class="flex space-x-2">
                        <flux:input type="text" wire:model="code" placeholder="e.g. SUMMER2025" class="flex-1" />
                        <flux:button type="button" wire:click="generateCode" variant="outline">Generate</flux:button>
                    </div>
                    @error('code') <flux:error>{{ $message }}</flux:error> @enderror
                </div>

                <div>
                    <flux:label>Discount Name</flux:label>
                    <flux:input type="text" wire:model="name" placeholder="e.g. Summer Sale 2025" />
                    @error('name') <flux:error>{{ $message }}</flux:error> @enderror
                </div>
            </div>

            <div>
                <flux:label>Description (Optional)</flux:label>
                <flux:textarea wire:model="description" rows="2" placeholder="Brief description of this discount code" />
                @error('description') <flux:error>{{ $message }}</flux:error> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <flux:label>Discount Amount</flux:label>
                    <flux:input type="number" wire:model="discount_amount" min="0" step="0.01" />
                    @error('discount_amount') <flux:error>{{ $message }}</flux:error> @enderror
                </div>

                <div>
                    <flux:label>Discount Type</flux:label>
                    <flux:select wire:model="discount_type">
                        <flux:select.option value="percentage">Percentage (%)</flux:select.option>
                        <flux:select.option value="fixed">Fixed Amount (Rp)</flux:select.option>
                    </flux:select>
                    @error('discount_type') <flux:error>{{ $message }}</flux:error> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <flux:label>Minimum Purchase (Rp)</flux:label>
                    <flux:input type="number" wire:model="minimum_purchase" min="0" step="0.01" placeholder="0 = No minimum" />
                    @error('minimum_purchase') <flux:error>{{ $message }}</flux:error> @enderror
                </div>

                <div>
                    <flux:label>Max Usage (Optional)</flux:label>
                    <flux:input type="number" wire:model="max_redeem" min="1" placeholder="Leave empty for unlimited" />
                    @error('max_redeem') <flux:error>{{ $message }}</flux:error> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <flux:label>Start Date</flux:label>
                    <flux:input type="datetime-local" wire:model="start_date" />
                    @error('start_date') <flux:error>{{ $message }}</flux:error> @enderror
                </div>

                <div>
                    <flux:label>End Date</flux:label>
                    <flux:input type="datetime-local" wire:model="end_date" />
                    @error('end_date') <flux:error>{{ $message }}</flux:error> @enderror
                </div>
            </div>

            <div>
                <label class="flex items-center">
                    <input type="checkbox" wire:model="is_active" class="form-checkbox h-5 w-5 text-indigo-600">
                    <span class="ml-2 text-gray-700 dark:text-gray-300">Active</span>
                </label>
                @error('is_active') <flux:error>{{ $message }}</flux:error> @enderror
            </div>

            <div class="flex justify-end space-x-2 pt-4">
                <flux:button variant="outline" wire:click="closeForm">Cancel</flux:button>
                <flux:button type="submit" color="primary">{{ $editMode ? 'Update' : 'Save' }}</flux:button>
            </div>
        </form>
    </flux:modal>

    <!-- Delete Confirmation Modal -->
    <flux:modal title="Confirm Delete" wire:model="showDeleteConfirmation">
        <p class="text-gray-600 dark:text-gray-300 mb-4">Are you sure you want to delete this discount code? This action cannot be undone.</p>
        
        <div class="flex justify-end space-x-2">
            <flux:button variant="outline" wire:click="cancelDelete">Cancel</flux:button>
            <flux:button color="danger" wire:click="delete">Delete</flux:button>
        </div>
    </flux:modal>
</div>