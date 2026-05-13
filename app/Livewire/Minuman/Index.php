<?php
namespace App\Livewire\Minuman;

use Livewire\Component;
use App\Models\Minuman;

class Index extends Component
{
    public $filterKategori = '';
    public $allKategoris = [];

    public $minumans = [];

    public function onKategoriChanged($value)
    {
        $this->filterKategori = $value;
        $this->muatData();
    }
    
    public function mount()
    {
        $this->muatData();
        $this->allKategoris = Minuman::distinct()->pluck('kategori')->filter()->values()->toArray();
    }
    
    public function muatData()
    {
        $this->minumans = Minuman::when($this->filterKategori, function ($query) {
            $query->where('kategori', $this->filterKategori);
        })->latest()->get();
    }
    
    public function setStatus($id, $isHabis)
    {
        $minuman = Minuman::findOrFail($id);
        $minuman->update([
            'is_habis' => $isHabis
        ]);
        
        $status = $isHabis ? 'habis' : 'tersedia';
        session()->flash('success', "Status minuman {$minuman->nama} berhasil diubah menjadi {$status}.");
        
        // Reload data to reflect changes
        $this->muatData();
    }

    public function hapus($id)
    {
        $minuman = Minuman::findOrFail($id);
        $nama = $minuman->nama;
        $minuman->delete();
        
        $this->muatData();
        session()->flash('success', "Minuman {$nama} berhasil dihapus.");
    }
    
    public function render()
    {
        return view('livewire.minuman.index');
    }
};