<style type="text/css">


    .test-element {
     /* color: #9ca1b2;*/ /*  2b2d3c */
     background: #2a2c3b; /* 2f3242  */
     /* overflow-x: hidden; */

     color: #FFF;
 }


 body,
 html {
  height: 100%;
}
/* workaround modal-open padding issue */

body.modal-open {
  padding-right: 0 !important;
}

#sidebar {
  padding-left: 0;
}
    /*
 * Off Canvas at medium breakpoint
 * --------------------------------------------------
 */
 
 @media screen and (max-width: 48em) {
  .row-offcanvas {
    position: relative;
    -webkit-transition: all 0.25s ease-out;
    -moz-transition: all 0.25s ease-out;
    transition: all 0.25s ease-out;
}
.row-offcanvas-left .sidebar-offcanvas {
    left: -33%;
}
.row-offcanvas-left.active {
    left: 33%;
    margin-left: -6px;
}
.sidebar-offcanvas {
    position: absolute;
    top: 0;
    width: 33%;
    height: 100%;
}
}
    /*
 * Off Canvas wider at sm breakpoint
 * --------------------------------------------------
 */
 
 @media screen and (max-width: 34em) {
  .row-offcanvas-left .sidebar-offcanvas {
    left: -45%;
}
.row-offcanvas-left.active {
    left: 45%;
    margin-left: -6px;
}
.sidebar-offcanvas {
    width: 45%;
}
}

.card {
  overflow: hidden;
}

.card-block .rotate {
  z-index: 8;
  float: right;
  height: 100%;
}

.card-block .rotate i {
  color: rgba(20, 20, 20, 0.15);
  position: absolute;
  left: 0;
  left: auto;
  right: -10px;
  bottom: 0;
  display: block;
  -webkit-transform: rotate(-44deg);
  -moz-transform: rotate(-44deg);
  -o-transform: rotate(-44deg);
  -ms-transform: rotate(-44deg);
  transform: rotate(-44deg);
}
</style>

<div >
    {{-- Care about people's approval and you will be their prisoner.

    

    --}}

    {{-- <canvas id="myChart" width="400" height="400"></canvas> --}}


    <div class="alert alert-warning fade collapse" role="alert" id="myAlert">
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">×</span>
        <span class="sr-only">Close</span>
    </button>
</div>

<div class="row mb-3">
  <div class="col-xl-3 col-lg-6">
    <div class="card card-inverse card-success">
      <div class="card-block bg-success">
        <div class="rotate">
          <i class="fa fa-dollar fa-5x"></i>
      </div>
      <h6 class="text-uppercase">MINERVAL</h6>
      
      <h5>{{ $minerval }}</h5>
  </div>
</div>
</div>
<div class="col-xl-3 col-lg-6">
    <div class="card card-inverse card-danger">
      <div class="card-block bg-danger">
        <div class="rotate">
          <i class="fa fa-list fa-4x"></i>
      </div>
      <h6 class="text-uppercase">  CONTRIBUTION </h6>
      <h5>{{ $contribution }}</h5>
  </div>
</div>
</div>
<div class="col-xl-3 col-lg-6">
    <div class="card card-inverse card-info">
      <div class="card-block bg-info">
        <div class="rotate">
          <i class="fa fa-shopping-cart fa-5x"></i>
      </div>
      <h6 class="text-uppercase">VENTE</h6>
      <h6>{{ $vente }}</h6>
  </div>
</div>
</div>
<div class="col-xl-3 col-lg-6">
    <div class="card card-inverse card-warning">
      <div class="card-block bg-warning">
        <div class="rotate">
          <i class="fa fa-share fa-5x"></i>
      </div>
      <h6 class="text-uppercase">MONTANT TOTAL</h6>
      
      <h5>{{ $vente + $contribution + $minerval }}</h5>
  </div>
</div>
</div>
</div>



<div class="row">
    <div class="col-md-6">
     <h4>Liste de ceux qui ont déjà payé {{ $selectedSection }} </h4>
     <div class="row">
         <div class="col">
          <select name="" id="" class="form-control">
                <option value="">Section</option>

                @foreach($sections as $section)

                <option value="{{ $section->id }}">{{ $section->name }}</option>
                @endforeach
            </select>
          
        
         </div>
    <div class="col">
    

        <select name="" wire:model="selectedClasse" id="" class="form-control">
                    <option value="">Classe</option>

                    @foreach($classes as $classe)

                    <option value="{{ $classe->id }}">{{ $classe->name }}</option>
                    @endforeach
          </select>
    
    </div>
<div class="col">
 3
</div>
</div>


</div>
<div class="col-md-6">
 <h4>Liste de ceux qui n'ont pas encore payé</h4>
 
</div>
</div>



<div class="test-element">
    <canvas id="myChart" width="400px"> </canvas>
</div>


</div>


@push('scripts')

<script>
    var chartTyp = ['bar','line','pie','scatter'];

    var ctx = document.getElementById('myChart').getContext('2d');
    var myChart = new Chart(ctx, {
        type: chartTyp[  Math.floor(Math.random() * chartTyp.length) ],
        data: {
            labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
            datasets: [{
                label: '# of Votes',
                data: [12, 19, 3, 5, 2, 3],
                backgroundColor: [
                'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 206, 86, 0.2)',
                'rgba(75, 192, 192, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(255, 159, 64, 0.2)'
                ],
                borderColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }
        }
    });
</script>


@endpush