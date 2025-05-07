<?php

namespace App\Livewire;

use Livewire\Component;

class HistoryPage extends Component
{
    public function render()
    {
        return view('livewire.history-page')->layout('layouts.public');
    }
}
