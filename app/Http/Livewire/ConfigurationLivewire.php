<?php

namespace App\Http\Livewire;

use App\Models\AnneScolaire;
use App\Models\Trimestre;
use Livewire\Component;

class ConfigurationLivewire extends Component
{
	public $showInputYear = false;
	public $annee;
    public function render()
    {
        return view('livewire.configuration-livewire',
        	[
        		'currentAnneScolaire' =>  AnneScolaire::latest()->first(),
                'trimestres' => Trimestre::all();
        	]
    	);
    }

    protected $rules = [
    	'annee' => 'required'
    ];

    public function saveYear()
    {
    	$this->validate();

    	AnneScolaire::create([
    		'name' => $this->annee
    	]);

    	$this->reset();
    }
}
