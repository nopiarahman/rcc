<?php

namespace App\Livewire\Makanan;

use Livewire\Component;
use App\Models\Bahan;

class BahanCrud extends Component
{
    public $nama = '';
    public $satuan = '';
    public $kategori = 'makanan';
    public $harga_satuan;
    public $bahan_id = null;
    public $bahans = [];

    public function mount()
    {
        $this->ambilData();
    }

    public function rules()
    {
        return [
            'nama' => 'required|string|min:2',
            'kategori' => 'required|string',
            'satuan' => 'required|string',
            'harga_satuan' => 'required|numeric|min:0',
        ];
    }

    public function simpan()
    {
        $this->validate();
        Bahan::updateOrCreate(
            ['id' => $this->bahan_id],
            [
                'nama' => $this->nama,
                'satuan' => $this->satuan,
                'kategori' => $this->kategori,
                'harga_satuan' => $this->harga_satuan,
            ]
        );
        $this->resetForm();
        $this->ambilData();
        session()->flash('success', 'Bahan makanan berhasil disimpan!');
    }

    public function edit($id)
    {
        $bahan = Bahan::findOrFail($id);
        $this->bahan_id = $bahan->id;
        $this->nama = $bahan->nama;
        $this->kategori = $bahan->kategori;
        $this->satuan = $bahan->satuan;
        $this->harga_satuan = $bahan->harga_satuan;
    }

    public function hapus($id)
    {
        Bahan::destroy($id);
        $this->ambilData();
        session()->flash('success', 'Bahan makanan berhasil dihapus!');
    }

    public function resetForm()
    {
        $this->bahan_id = null;
        $this->nama = '';
        $this->kategori = 'makanan';
        $this->satuan = '';
        $this->harga_satuan = '';
    }

    public function ambilData()
    {
        $this->bahans = Bahan::where('kategori', 'makanan')->orderBy('nama')->get();
    }

    public function render()
    {
        return view('livewire.makanan.bahan-crud');
    }
}
