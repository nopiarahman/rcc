<div class="mb-6">
    <nav class="flex gap-4 border-b pb-2">
        <a href="{{ route('makanan.index') }}" class="{{ request()->routeIs('makanan.index') ? 'font-bold border-b-2 border-primary' : '' }}">Makanan</a>
        <a href="{{ route('makanan.bahan-crud') }}" class="{{ request()->routeIs('makanan.bahan-crud') ? 'font-bold border-b-2 border-primary' : '' }}">Bahan</a>
        <a href="{{ route('makanan.topping-crud') }}" class="{{ request()->routeIs('makanan.topping-crud') ? 'font-bold border-b-2 border-primary' : '' }}">Topping</a>
    </nav>
</div>
