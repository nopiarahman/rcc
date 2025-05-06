<div>
    <flux:navbar scrollable>
        <flux:navbar.item :current="request()->routeIs('minuman.index')" :href="route('minuman.index')" wire:navigate>Minuman</flux:navbar.item>
        <flux:navbar.item :current="request()->routeIs('minuman.bahan.create')" :href="route('minuman.bahan.create')" wire:navigate >Bahan</flux:navbar.item>
        <flux:navbar.item :current="request()->routeIs('minuman.sizes.create')" :href="route('minuman.sizes.create')" wire:navigate>Size</flux:navbar.item>
        <flux:navbar.item :current="request()->routeIs('minuman.sugar.create')" :href="route('minuman.sugar.create')" wire:navigate>Sugar</flux:navbar.item>
        <flux:navbar.item :current="request()->routeIs('minuman.topping.create')" :href="route('minuman.topping.create')" wire:navigate>Topping</flux:navbar.item>
    </flux:navbar>
</div>