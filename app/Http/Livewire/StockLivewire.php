<?php

namespace App\Http\Livewire;

use App\Models\Stock;
use Livewire\Component;

class StockLivewire extends Component
{
	public $identifiant;
	public $name;
	private $stocks;
	

	public function render()
	{
		$this->stocks = Stock::paginate();
		return view('livewire.stock-livewire', [

			'stocks' => $this->stocks


		]);
	}

	protected $rules =[
		'name' => 'required'

	];

	public function updated($propertyName){
		$this->validateOnly($propertyName);
	}

	public function saveStock(){
		$this->validate();

		if($this->identifiant == null){
			Stock::create([
				'name' => $this->name
			]);

		}else{

			$stock = Stock::find($this->identifiant);
			$stock->update([
				'name' => $this->name

			]);

		}

		$this->name = "";

	}

	public function destroy($id){

		Stock::find($id)->delete();
	}


	public function edit($id){
		$stock = Stock::find($id);

		$this->identifiant = $stock->id;
		$this->name = $stock->name;

	}
}
