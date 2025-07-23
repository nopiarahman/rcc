<?php

namespace App\Livewire\Makanan;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Makanan;
use App\Models\Bahan;
use App\Models\Topping;

class Create extends Component
{
    use WithFileUploads;

    public $foto;
    public $fotoPreview;
    public string $nama = '';
    public string $deskripsi = '';
    public string $short_description = '';
    public string $tag = '';
    public string $kategori = '';
    public int $base_price;
    public bool $is_habis = false;
    public $defaultTopping;

    public $toppings;
    public $bahans;
    public array $selectedToppings = [];
    public array $selectedBahans = [];

    public function mount()
    {
        $this->toppings = Topping::where('kategori', 'makanan')->get();
        $this->bahans = Bahan::where('kategori', 'makanan')->get();
    }

    public function simpan()
    {
        $this->validate([
            'nama' => 'required|string',
            'deskripsi' => 'nullable|string',
            'base_price' => 'required|integer|min:0',
            'defaultTopping' => 'nullable',
            'tag' => 'nullable|string',
            'short_description' => 'nullable|string',
            'foto' => 'nullable|image|max:2048',
        ]);

        $makanan = Makanan::create([
            'nama' => $this->nama,
            'kategori' => $this->kategori,
            'deskripsi' => $this->deskripsi,
            'short_description' => $this->short_description,
            'tag' => $this->tag,
            'base_price' => $this->base_price,
            'is_habis' => $this->is_habis,
            'default_topping' => $this->defaultTopping,
        ]);

        // Handle foto upload with Spatie Media Library
        if ($this->foto) {
            $makanan->addMedia($this->foto->getRealPath())
                ->usingFileName($this->foto->getClientOriginalName())
                ->toMediaCollection('foto');
        }

        // Sync Toppings with extra_price
        $syncToppings = collect($this->selectedToppings)
            ->filter(fn($data) => isset($data['aktif']) && $data['aktif'])
            ->mapWithKeys(function($data, $id) {
                return [$id => [
                    'extra_price' => isset($data['harga']) ? $data['harga'] : 0
                ]];
            })->toArray();
        $makanan->toppings()->sync($syncToppings);

        // Sync Bahans with qty and harga
        $syncBahans = collect($this->selectedBahans)
            ->filter(fn($data) => isset($data['aktif']) && $data['aktif'])
            ->mapWithKeys(function($data, $id) {
                return [$id => [
                    'jumlah' => isset($data['qty']) ? $data['qty'] : 0,
                    'harga_satuan' => isset($data['harga']) ? $data['harga'] : 0
                ]];
            })->toArray();
        $makanan->bahans()->sync($syncBahans);

        session()->flash('success', 'Makanan berhasil disimpan!');
        return redirect()->route('makanan.index');
    }

    public function render()
    {
        return view('livewire.makanan.create');
    }
}
