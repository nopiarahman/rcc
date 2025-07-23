<?php

namespace App\Livewire\Makanan;

use Livewire\Component;
use App\Models\Makanan;

class DetailHppModal extends Component
{
    public $makanan;
    public $bahanList = [];
    public $show = false;

    protected $listeners = ['open-modal-hpp-makanan' => 'loadData'];

    public function loadData($id)
    {
        $this->makanan = Makanan::with('bahans')->find($id);
        $this->bahanList = $this->makanan && $this->makanan->bahans ? $this->makanan->bahans->map(function($bahan) {
            return [
                'nama' => $bahan->nama,
                'harga_satuan' => $bahan->harga_satuan,
                'jumlah' => $bahan->pivot->jumlah,
                'satuan' => $bahan->satuan,
                'total' => $bahan->harga_satuan * $bahan->pivot->jumlah,
            ];
        })->toArray() : [];
        $this->show = true;
    }

    public function closeModal()
    {
        $this->show = false;
    }

    public function render()
    {
        return view('livewire.makanan.detail-hpp-modal', [
            'makanan' => $this->makanan,
            'bahanList' => $this->bahanList,
            'show' => $this->show,
        ]);
    }
}
