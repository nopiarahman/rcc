<?php

namespace App\Livewire\Makanan;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Makanan;
use App\Models\Bahan;
use App\Models\Topping;

class Edit extends Component
{
    use WithFileUploads;

    public $makanan;
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

    public function mount($makanan)
    {
        $this->makanan = Makanan::with(['toppings', 'bahans'])->findOrFail($makanan);
        $this->nama = $this->makanan->nama;
        $this->deskripsi = $this->makanan->deskripsi;
        $this->short_description = $this->makanan->short_description;
        $this->tag = $this->makanan->tag;
        $this->kategori = $this->makanan->kategori;
        $this->base_price = $this->makanan->base_price;
        $this->is_habis = $this->makanan->is_habis;
        $this->defaultTopping = $this->makanan->default_topping;
        $this->toppings = Topping::where('kategori', 'makanan')->get();
        $this->bahans = Bahan::where('kategori', 'makanan')->get();
        $this->selectedToppings = $this->makanan->toppings->mapWithKeys(function($topping) {
            return [
                $topping->id => [
                    'aktif' => true,
                    'harga' => $topping->pivot->extra_price ?? 0
                ]
            ];
        })->toArray();
        $this->selectedBahans = $this->makanan->bahans->mapWithKeys(function($bahan) {
            return [
                $bahan->id => [
                    'aktif' => true,
                    'qty' => $bahan->pivot->jumlah ?? 0,
                    'harga' => $bahan->pivot->harga_satuan ?? 0
                ]
            ];
        })->toArray();
    }

    public function update()
    {
        $this->validate([
            'nama' => 'required|string',
            'deskripsi' => 'nullable|string',
            'base_price' => 'required|integer|min:0',
            'defaultTopping' => 'nullable',
            'tag' => 'nullable|string',
            'short_description' => 'nullable|string',
        ]);

        $this->makanan->update([
            'nama' => $this->nama,
            'kategori' => $this->kategori,
            'deskripsi' => $this->deskripsi,
            'short_description' => $this->short_description,
            'tag' => $this->tag,
            'base_price' => $this->base_price,
            'is_habis' => $this->is_habis,
            'default_topping' => $this->defaultTopping,
        ]);

        $this->makanan->toppings()->sync(
            collect($this->selectedToppings)
                ->filter(fn($aktif) => $aktif)
                ->keys()
        );
        $this->makanan->bahans()->sync(
            collect($this->selectedBahans)
                ->filter(fn($aktif) => $aktif)
                ->keys()
        );

        session()->flash('success', 'Makanan berhasil diperbarui!');
        return redirect()->route('makanan.index');
    }

    public function render()
    {
        return view('livewire.makanan.edit');
    }
}
