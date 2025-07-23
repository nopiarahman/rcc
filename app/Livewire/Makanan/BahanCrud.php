<?php

namespace App\Livewire\Makanan;

use Livewire\Component;
use App\Models\Bahan;

class BahanCrud extends Component
{
    public $nama = '';
    public $satuan = '';
    public $kategori = Bahan::KATEGORI_DISPLAY;
    public $harga_satuan;
    
    public $kategoriOptions = [
        Bahan::KATEGORI_DISPLAY => 'Display',
        Bahan::KATEGORI_NON_DISPLAY => 'Non-Display'
    ];
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
        
        try {
            $data = [
                'nama' => $this->nama,
                'satuan' => $this->satuan,
                'kategori' => $this->kategori,
                'harga_satuan' => $this->harga_satuan,
                'jenis' => Bahan::JENIS_MAKANAN, // Set jenis to 'makanan' for this component
            ];
            
            if ($this->bahan_id) {
                // Update existing record
                $bahan = Bahan::findOrFail($this->bahan_id);
                $bahan->update($data);
            } else {
                // Create new record
                $bahan = Bahan::create($data);
            }
            
            $this->resetForm();
            $this->ambilData();
            session()->flash('success', 'Bahan makanan berhasil disimpan!');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menyimpan bahan: ' . $e->getMessage());
        }
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
        try {
            Bahan::destroy($id);
            $this->ambilData();
            session()->flash('success', 'Bahan makanan berhasil dihapus!');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus bahan: ' . $e->getMessage());
        }
    }

    public function resetForm()
    {
        $this->bahan_id = null;
        $this->nama = '';
        $this->kategori = Bahan::KATEGORI_DISPLAY;
        $this->satuan = '';
        $this->harga_satuan = null;
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function ambilData()
    {
        // Only get bahan with jenis 'makanan'
        $this->bahans = Bahan::where('jenis', Bahan::JENIS_MAKANAN)
                           ->orderBy('nama')
                           ->get();
    }

    public function render()
    {
        return view('livewire.makanan.bahan-crud');
    }
}
