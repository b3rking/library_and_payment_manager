<?php

use App\Http\Controllers\EleveController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



/*===============================================
/ API ROUTING - b3rking
/
/ routes to the api will start with the version and then the endpoint
/ ex: /v1/students/store
/*===============================================*/


// main endpoint!
Route::get('/', function() {
    return response([
        'success' => 'true',
        'data' => [
            'message' => 'welcome on our API. this is the main endpoint for a list of all api endpoint and usage refer to the docs available at the adress bellow.',
            'docs-link' => '/api-docs']]);
});


// versioning, all endpoint for the first version will go in this group.
Route::prefix('v1')->group(function() {
    Route::get('eleve_api', [EleveController::class, 'apiEleve'])->name('eleve_api');
});