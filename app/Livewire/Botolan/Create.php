<?php

namespace App\Livewire\Botolan;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\BotolanProduk;
use App\Models\Minuman;

class Create extends Component
{
    use WithFileUploads;

    public $foto;
    public int|string $minumanId = '';
    public bool $is_active = true;
    public array $ukurans = [
        ['label' => '250 ml', 'harga' => ''],
    ];
    public array $ukuranFotos = [];

    public function addUkuran(): void
    {
        $this->ukurans[] = ['label' => '', 'harga' => ''];
    }

    public function removeUkuran(int $index): void
    {
        array_splice($this->ukurans, $index, 1);
        // Re-index fotos to keep alignment
        $fotos = [];
        foreach ($this->ukuranFotos as $i => $f) {
            if ($i !== $index) {
                $fotos[] = $f;
            }
        }
        $this->ukuranFotos = $fotos;
    }

    public function simpan()
    {
        $this->validate([
            'minumanId'          => 'required|exists:minumans,id',
            'foto'               => 'nullable|image|max:2048',
            'ukurans'            => 'required|array|min:1',
            'ukurans.*.label'    => 'required|string|max:50',
            'ukurans.*.harga'    => 'required|integer|min:0',
            'ukuranFotos.*'      => 'nullable|image|max:2048',
        ]);

        $produk = BotolanProduk::create([
            'minuman_id' => $this->minumanId,
            'is_active'  => $this->is_active,
        ]);

        foreach ($this->ukurans as $i => $u) {
            $ukuran = $produk->allUkurans()->create([
                'label'     => $u['label'],
                'harga'     => (int) $u['harga'],
                'is_active' => true,
            ]);
            if (!empty($this->ukuranFotos[$i])) {
                $ukuran->addMedia($this->ukuranFotos[$i]->getRealPath())
                    ->usingFileName($this->ukuranFotos[$i]->getClientOriginalName())
                    ->toMediaCollection('foto');
            }
        }

        if ($this->foto) {
            $produk->addMedia($this->foto->getRealPath())
                ->usingFileName($this->foto->getClientOriginalName())
                ->toMediaCollection('foto');
        }

        session()->flash('success', 'Botolan berhasil ditambahkan!');
        return redirect()->route('botolan.index');
    }

    public function render()
    {
        $configured = BotolanProduk::pluck('minuman_id')->toArray();
        return view('livewire.botolan.create', [
            'minumans'   => Minuman::orderBy('nama')->get(),
            'configured' => $configured,
        ]);
    }
}
