<?php

namespace App\Livewire\Makanan;

use Livewire\Component;
use App\Models\Topping;

class ToppingCrud extends Component
{
    public $nama = '';
    public $default_price;
    public $topping_id = null;
    public $toppings = [];
    public $kategori = 'makanan';

    public function mount()
    {
        $this->ambilData();
    }

    public function rules()
    {
        return [
            'nama' => 'required|string|min:2',
            'default_price' => 'required|numeric|min:0',
            'kategori' => 'required|string',
        ];
    }

    public function simpan()
    {
        $this->validate();
        Topping::updateOrCreate(
            ['id' => $this->topping_id],
            [
                'nama' => $this->nama,
                'default_price' => $this->default_price,
                'kategori' => $this->kategori,
            ]
        );
        $this->resetForm();
        $this->ambilData();
        session()->flash('success', 'Topping makanan berhasil disimpan!');
    }

    public function edit($id)
    {
        $topping = Topping::findOrFail($id);
        $this->topping_id = $topping->id;
        $this->nama = $topping->nama;
        $this->default_price = $topping->default_price;
        $this->kategori = $topping->kategori;
    }

    public function hapus($id)
    {
        Topping::destroy($id);
        $this->ambilData();
        session()->flash('success', 'Topping makanan berhasil dihapus!');
    }

    public function resetForm()
    {
        $this->topping_id = null;
        $this->nama = '';
        $this->default_price = '';
        $this->kategori = 'makanan';
    }

    public function ambilData()
    {
        $this->toppings = Topping::where('kategori', 'makanan')->orderBy('nama')->get();
    }

    public function render()
    {
        return view('livewire.makanan.topping-crud');
    }
}
