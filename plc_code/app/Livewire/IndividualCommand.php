<?php

namespace App\Livewire;

use App\Models\Luminary;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class IndividualCommand extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $luminaryNo;
    public $concentratorNo;
    public $selectedLuminaryId;
    public $openModal = false;

    public function render()
    {
        return view('livewire.individual-command', [
            'luminaries' => Luminary::when($this->luminaryNo, fn($q) => $q->where('node_id', 'like', "%{$this->luminaryNo}%"))
                ->when($this->concentratorNo, function ($q) {
                    $q->whereHas('concentrator', fn($q) => $q->where('concentrator_no', 'like', "%{$this->concentratorNo}%"));
                })
                ->paginate(5, ['*'], 'individual_commands')
        ]);
    }

    #[On('open-dim-modal')]
    public function openDimModal($id)
    {
        $this->selectedLuminaryId = $id;
        $this->openModal = true;
    }
}
