@extends('layouts.base')
@section('content')
<div class="container">
	<h5 class="text-center">Ajouter un élève en <b> {{ $classe->name ?? "" }} </b></h5>
	<form action="{{ route('eleves.store') }}" method="POST">
		@csrf
		@method('POST')
		@include('eleves._form',['btn_name' => 'Enregistrer'])
	</form>

	<div class="col-md-8 offset-md-2">
		<div class="">
			
			<h5 class="text-center">Ajouter la liste des élèves   <b> {{ $classe->name ?? "" }} </b> à partir du fichier excel </h5>

			<p>
			1. Télécharger le Modèle <button onclick="downloadModel({{$classe->id}},'{{$classe->name  }}')" class="ml-4"><i class="fa fa-arrow-down" style="font-size:24px"></i></button>
			</p>

			<p class="d-flex">
				2. Charger le Modèle 
				<form action="">
					<input type="file" id="input_file" accept=".csv">
					<button id="save"> <i class="fa fa-upload" style="font-size:24px"></i></button>
					<img src="{{ asset('images/loader.gif') }}" width="200" alt="" id="loader" style="display: none;" >
				</form>
				
			</p>
			<p id="responseGet">
				
			</p>

	
		</div>
	</div>
</div>
@stop
@push('scripts')
<script>
let liste_eleve = []

function downloadModel(id, classe_name){
	const rows = [
		['CLASSE','NOM','PRENOM','DATE DE NAISSANCE','SEXE','ADRESSE'],
		[classe_name,'NINININAHAZWE','JEAN LIONEL','1996-02-18','H','KIGOBE-NTAHARWA-BUJUMBURA'],
	]
	let csvContent = "data:text/csv;charset=utf-8,";
	rows.forEach((rowArray) =>{
	    let row = rowArray.join(",");
	    csvContent += row + "\r\n";
	});
	var encodedUri = encodeURI(csvContent);
	var link = document.createElement("a");
	link.setAttribute("href", encodedUri);
	link.setAttribute("download", "fiche_"+classe_name+".csv");
	document.body.appendChild(link); // Required for FF

	link.click(); // This will download the data file named "my_data.csv".
}
const input_file = document.getElementById("input_file");
input_file.addEventListener('change', function(event) {
	event.preventDefault();
	/* Act on the event */
	const fileList = event.target.files[0];
    //console.log(fileList);
    readFile(fileList);
});

function readFile(file) {
  const reader = new FileReader();
  reader.addEventListener('load', (event) => {
    const result = event.target.result;
    // Do something with result
    const data = result.split('\r\n')
   
    // const headers = data[0].split(',')
    const classe_id = "{{ $_GET['id'] }}"

    const sep = getDefaultSeperator(data[0]);

    for(let i =1; i<data.length-1; i++){
    	let line = data[i].split(sep)
    	let eleve = {
    		classe_id : classe_id,
    		first_name : line[1],
    		last_name: line[2],
    		date_naissance: formateDate(line[3]),
    		sexe: line[4],
    		address: line[5],
    	}
    	liste_eleve.push(eleve)
    }

    console.log(liste_eleve)

  });

  reader.addEventListener('progress', (event) => {
    if (event.loaded && event.total) {
      const percent = (event.loaded / event.total) * 100;
      console.log(`Progress: ${Math.round(percent)}`);
    }
  });
  reader.readAsText(file,'ISO-8859-1');
}

//SAVING INFORMATION

$("#save").on('click', function(event) {
	event.preventDefault();
	/* Act on the event */

	$("#loader").show()
	$.ajax({
	  url: '{{ route('save_student') }}',
	  type: 'POST',
	  data: {data: liste_eleve, _token: "{{ csrf_token() }}"},
	  complete: function(xhr, textStatus) {
	    //called when complete
	  },
	  success: function(data, textStatus, xhr) {
	  	$("#input_file").val("")
	    //called when successful
	     $("#loader").hide();
	    $("#responseGet").html(`<span class="text-primary display-3"> Importation réussi de ${liste_eleve.length}  élèves  </span>`)
	  },
	  error: function(xhr, textStatus, errorThrown) {
	    //called when there is an error
	    $("#responseGet").html(`<span class="text-danger"> Erreur ${textStatus}  élèves</span>`)
	  }
	});
	
});

function formateDate(date){
	const givenDate = new Date(date);
	return givenDate.getFullYear() +"-"+(givenDate.getMonth() + 1)+"-"+givenDate.getDate();
}

function getDefaultSeperator(string){
    const pointVirgule = (string.match(/;/g) || []).length
    const virgule = (string.match(/,/g) || []).length

    return pointVirgule > virgule ? ';' : ','
}

</script>

@endpush