<?php

namespace App\Livewire\Botolan;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\BotolanProduk;
use App\Models\Minuman;

class Edit extends Component
{
    use WithFileUploads;

    public BotolanProduk $produk;
    public $foto;
    public int|string $minumanId = '';
    public bool $is_active = true;
    public array $ukurans = [];
    public array $ukuranFotos = [];

    public function mount(int $botolan): void
    {
        $this->produk = BotolanProduk::with(['minuman', 'allUkurans'])->findOrFail($botolan);
        $this->minumanId = $this->produk->minuman_id;
        $this->is_active = $this->produk->is_active;
        $this->ukurans = $this->produk->allUkurans()
            ->with('media')
            ->get()
            ->map(fn($u) => [
                'id'           => $u->id,
                'label'        => $u->label,
                'harga'        => $u->harga,
                'existing_foto'=> $u->getFirstMediaUrl('foto') ?: null,
            ])
            ->toArray();
        if (empty($this->ukurans)) {
            $this->ukurans = [['id' => null, 'label' => '', 'harga' => '', 'existing_foto' => null]];
        }
    }

    public function addUkuran(): void
    {
        $this->ukurans[] = ['id' => null, 'label' => '', 'harga' => '', 'existing_foto' => null];
    }

    public function removeUkuran(int $index): void
    {
        array_splice($this->ukurans, $index, 1);
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
            'minumanId'       => 'required|exists:minumans,id',
            'foto'            => 'nullable|image|max:2048',
            'ukurans'         => 'required|array|min:1',
            'ukurans.*.label' => 'required|string|max:50',
            'ukurans.*.harga' => 'required|integer|min:0',
            'ukuranFotos.*'   => 'nullable|image|max:2048',
        ]);

        $this->produk->update([
            'minuman_id' => $this->minumanId,
            'is_active'  => $this->is_active,
        ]);

        // Clear media on all old ukurans before deleting
        $this->produk->allUkurans()->with('media')->get()
            ->each(fn($u) => $u->clearMediaCollection('foto'));
        $this->produk->allUkurans()->delete();

        foreach ($this->ukurans as $i => $u) {
            $ukuran = $this->produk->allUkurans()->create([
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
            $this->produk->clearMediaCollection('foto');
            $this->produk->addMedia($this->foto->getRealPath())
                ->usingFileName($this->foto->getClientOriginalName())
                ->toMediaCollection('foto');
        }

        session()->flash('success', 'Botolan berhasil diupdate!');
        return redirect()->route('botolan.index');
    }

    public function render()
    {
        return view('livewire.botolan.edit', [
            'minumans' => Minuman::orderBy('nama')->get(),
        ]);
    }
}
