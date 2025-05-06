<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Minuman;

class DetailHppModal extends Component
{
    public $minuman;
    public $bahanList = [];
    public $show = false;

    protected $listeners = ['open-modal-hpp' => 'loadData'];

    public function loadData($id)
    {
        $this->minuman = Minuman::with('bahans')->find($id);
        $this->bahanList = $this->minuman->bahans->map(function($bahan) {
            return [
                'nama' => $bahan->nama,
                'harga_satuan' => $bahan->harga_satuan,
                'jumlah' => $bahan->pivot->jumlah,
                'satuan' => $bahan->satuan,
                'total' => $bahan->harga_satuan * $bahan->pivot->jumlah,
            ];
        })->toArray();

        $this->show = true;
    }

    public function closeModal()
    {
        $this->show = false;
    }

    public function render()
    {
        return view('livewire.detail-hpp-modal');
    }
}
