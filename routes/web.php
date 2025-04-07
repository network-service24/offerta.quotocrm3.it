<?php

use App\Http\Controllers\CheckinController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Custom1Controller;
use App\Http\Controllers\Custom2Controller;
use App\Http\Controllers\Custom3Controller;
use App\Http\Controllers\Custom4Controller;
use App\Http\Controllers\Custom5Controller;
use App\Http\Controllers\Custom6Controller;
use App\Http\Controllers\Custom7Controller;
use App\Http\Controllers\Custom8Controller;
use App\Http\Controllers\Custom9Controller;
use App\Http\Controllers\DefaultController;
use App\Http\Controllers\SmartController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\VoucherRecuperoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

/* Route::get('/', function () {
    return view('welcome');
}); */

### CONTROLLER FUNZIONI GENERICHE PER TUTTI GLI ALTRI TEMPLATE ###
Route::post('/calc_prezzo_serv_landing', [Controller::class, 'calc_prezzo_serv_landing']);
Route::post('/calc_prezzo_serv_a_persona_landing', [Controller::class, 'calc_prezzo_serv_a_persona_landing']);
Route::post('/salva_carta', [Controller::class, 'salva_carta']);
Route::post('/salva_pagamento', [Controller::class, 'salva_pagamento']);
Route::post('/aggiungi_chat', [Controller::class, 'aggiungi_chat']);
Route::match(['get', 'post'], '/ballon', [Controller::class, 'ballon']);
Route::match(['get', 'post'], '/ballon_smart', [Controller::class, 'ballon_smart']);
Route::post('/calc_prezzo_servizio', [Controller::class, 'calc_prezzo_servizio']);
Route::post('/calc_prezzo_servizio_a_persona', [Controller::class, 'calc_prezzo_servizio_a_persona']);
Route::get('/gmap', [Controller::class, 'gmap']);
Route::post('/accetta_proposta', [Controller::class, 'accetta_proposta']);
/** registrazione pagamento paypal async */
Route::post('/reg_payment', [Controller::class, 'reg_payment'])->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
/** registrazione pagamento gateway bancario BCC payway async */
Route::post('/payway', [Controller::class, 'payway']);
/** registrazione pagamento gateway bancario VIRTAUL PAY async */
Route::post('/virtualpayKO', [Controller::class, 'virtualpayKO']);
Route::post('/virtualpayOK', [Controller::class, 'virtualpayOK']);
/** registrazione pagamento NEXI async */
Route::get('/esito', [Controller::class, 'esito']);
Route::get('/annullo', [Controller::class, 'annullo']);
/** contatore aperture ed utente online per landing template */
Route::get('/{template}/{directory}/{params}/count', [Controller::class, 'count'])->where([
 'template'  => '[a-zA-Z0-9._-]+', // Accetta lettere, numeri, underscore, punti e trattini
 'directory' => '[a-zA-Z0-9._-]+', // Accetta lettere, numeri, underscore, punti e trattini
 'params'    => '[a-zA-Z0-9=+/]+', // Pattern per "id_richiesta_idsito_tipo"
]);
/** contatore aperture ed utente online per landing defautl */
Route::get('/{directory}/{params}/count', [Controller::class, 'count_default'])->where([
 'directory' => '[a-zA-Z0-9._-]+', // Accetta lettere, numeri, underscore, punti e trattini
 'params'    => '[a-zA-Z0-9=+/]+', // Pattern per "id_richiesta_idsito_tipo"
]);
/** registrazione salvataggio questionario */
Route::post('/save_questionario', [Controller::class, 'save_questionario']);
### FINE CONTROLLER FUNZIONI GENERICHE PER TUTTI GLI ALTRI TEMPLATE ###

### CONTROLLER VOUCHER ###
Route::middleware(['settaSessione', 'dizionario'])
 ->get('/{directory}/{params}/voucher', [VoucherController::class, 'voucher'])
 ->where([
  'directory' => '[a-zA-Z0-9._-]+', // Accetta lettere, numeri, underscore, punti e trattini
  'params'    => '[a-zA-Z0-9=+/]+', // Pattern per "id_richiesta_idsito_tipo"
 ]);
### FINE CONTROLLER VOUCHER ###

### CONTROLLER VOUCHER RECUPERO ###
Route::middleware(['settaSessione', 'dizionario'])
 ->get('/{directory}/{params}/voucher_rec', [VoucherRecuperoController::class, 'voucher_rec'])
 ->where([
  'directory' => '[a-zA-Z0-9._-]+', // Accetta lettere, numeri, underscore, punti e trattini
  'params'    => '[a-zA-Z0-9=+/]+', // Pattern per "id_richiesta_idsito_tipo"
 ]);
### FINE CONTROLLER VOUCHER RECUPERO ###

## TEMPLATE DEFAULT ##
Route::middleware(['settaSessione', 'dizionario'])
 ->get('/{directory}/{params}/index', [DefaultController::class, 'default_template'])
 ->where([
  'directory' => '[a-zA-Z0-9._-]+', // Accetta lettere, numeri, underscore, punti e trattini
  'params'    => '[a-zA-Z0-9=+/]+', // Pattern per "id_richiesta_idsito_tipo"
 ]);
Route::middleware(['settaSessione', 'dizionario'])
 ->get('/{directory}/{params}/chat', [DefaultController::class, 'default_template'])
 ->where([
  'directory' => '[a-zA-Z0-9._-]+', // Accetta lettere, numeri, underscore, punti e trattini
  'params'    => '[a-zA-Z0-9=+/]+', // Pattern per "id_richiesta_idsito_tipo"
 ]);
Route::middleware(['settaSessione', 'dizionario'])
 ->get('/{directory}/{params}/questionario', [DefaultController::class, 'questionario'])
 ->where([
  'directory' => '[a-zA-Z0-9._-]+', // Accetta lettere, numeri, underscore, punti e trattini
  'params'    => '[a-zA-Z0-9=+/]+', // Pattern per "id_richiesta_idsito_tipo"
 ]);

## FINE TEMPLATE DEFAULT ##

## TEMPLATE SMART ##
Route::middleware(['settaSessione', 'dizionario'])
 ->get('/smart/{directory}/{params}/index', [SmartController::class, 'smart_template'])
 ->where([
  'directory' => '[a-zA-Z0-9._-]+', // Accetta lettere, numeri, underscore, punti e trattini
  'params'    => '[a-zA-Z0-9=+/]+', // Pattern per "id_richiesta_idsito_tipo"
 ]);
Route::middleware(['settaSessione', 'dizionario'])
 ->get('/smart/{directory}/{params}/chat', [SmartController::class, 'smart_template'])
 ->where([
  'directory' => '[a-zA-Z0-9._-]+', // Accetta lettere, numeri, underscore, punti e trattini
  'params'    => '[a-zA-Z0-9=+/]+', // Pattern per "id_richiesta_idsito_tipo"
 ]);
Route::middleware(['settaSessione', 'dizionario'])
 ->get('/smart/{directory}/{params}/questionario', [SmartController::class, 'questionario'])
 ->where([
  'directory' => '[a-zA-Z0-9._-]+', // Accetta lettere, numeri, underscore, punti e trattini
  'params'    => '[a-zA-Z0-9=+/]+', // Pattern per "id_richiesta_idsito_tipo"
 ]);

## TEMPLATE CUSTOM 1 ##
Route::middleware(['settaSessione', 'dizionario'])
 ->get('/custom1/{directory}/{params}/index', [Custom1Controller::class, 'custom1_template'])
 ->where([
  'directory' => '[a-zA-Z0-9._-]+', // Accetta lettere, numeri, underscore, punti e trattini
  'params'    => '[a-zA-Z0-9=+/]+', // Pattern per "id_richiesta_idsito_tipo"
 ]);
Route::middleware(['settaSessione', 'dizionario'])
 ->get('/custom1/{directory}/{params}/chat', [Custom1Controller::class, 'custom1_template'])
 ->where([
  'directory' => '[a-zA-Z0-9._-]+', // Accetta lettere, numeri, underscore, punti e trattini
  'params'    => '[a-zA-Z0-9=+/]+', // Pattern per "id_richiesta_idsito_tipo"
 ]);
Route::middleware(['settaSessione', 'dizionario'])
 ->get('/custom1/{directory}/{params}/questionario', [Custom1Controller::class, 'questionario'])
 ->where([
  'directory' => '[a-zA-Z0-9._-]+', // Accetta lettere, numeri, underscore, punti e trattini
  'params'    => '[a-zA-Z0-9=+/]+', // Pattern per "id_richiesta_idsito_tipo"
 ]);

## TEMPLATE CUSTOM 2 ##
Route::middleware(['settaSessione', 'dizionario'])
 ->get('/custom2/{directory}/{params}/index', [Custom2Controller::class, 'custom2_template'])
 ->where([
  'directory' => '[a-zA-Z0-9._-]+', // Accetta lettere, numeri, underscore, punti e trattini
  'params'    => '[a-zA-Z0-9=+/]+', // Pattern per "id_richiesta_idsito_tipo"
 ]);
Route::middleware(['settaSessione', 'dizionario'])
 ->get('/custom2/{directory}/{params}/chat', [Custom2Controller::class, 'custom2_template'])
 ->where([
  'directory' => '[a-zA-Z0-9._-]+', // Accetta lettere, numeri, underscore, punti e trattini
  'params'    => '[a-zA-Z0-9=+/]+', // Pattern per "id_richiesta_idsito_tipo"
 ]);
Route::middleware(['settaSessione', 'dizionario'])
 ->get('/custom2/{directory}/{params}/questionario', [Custom2Controller::class, 'questionario'])
 ->where([
  'directory' => '[a-zA-Z0-9._-]+', // Accetta lettere, numeri, underscore, punti e trattini
  'params'    => '[a-zA-Z0-9=+/]+', // Pattern per "id_richiesta_idsito_tipo"
 ]);

## TEMPLATE CUSTOM 3 ##
Route::middleware(['settaSessione', 'dizionario'])
 ->get('/custom3/{directory}/{params}/index', [Custom3Controller::class, 'custom3_template'])
 ->where([
  'directory' => '[a-zA-Z0-9._-]+', // Accetta lettere, numeri, underscore, punti e trattini
  'params'    => '[a-zA-Z0-9=+/]+', // Pattern per "id_richiesta_idsito_tipo"
 ]);
Route::middleware(['settaSessione', 'dizionario'])
 ->get('/custom3/{directory}/{params}/chat', [Custom3Controller::class, 'custom3_template'])
 ->where([
  'directory' => '[a-zA-Z0-9._-]+', // Accetta lettere, numeri, underscore, punti e trattini
  'params'    => '[a-zA-Z0-9=+/]+', // Pattern per "id_richiesta_idsito_tipo"
 ]);
Route::middleware(['settaSessione', 'dizionario'])
 ->get('/custom3/{directory}/{params}/questionario', [Custom3Controller::class, 'questionario'])
 ->where([
  'directory' => '[a-zA-Z0-9._-]+', // Accetta lettere, numeri, underscore, punti e trattini
  'params'    => '[a-zA-Z0-9=+/]+', // Pattern per "id_richiesta_idsito_tipo"
 ]);

## TEMPLATE CUSTOM 4 ##
Route::middleware(['settaSessione', 'dizionario'])
 ->get('/custom4/{directory}/{params}/index', [Custom4Controller::class, 'custom4_template'])
 ->where([
  'directory' => '[a-zA-Z0-9._-]+', // Accetta lettere, numeri, underscore, punti e trattini
  'params'    => '[a-zA-Z0-9=+/]+', // Pattern per "id_richiesta_idsito_tipo"
 ]);
Route::middleware(['settaSessione', 'dizionario'])
 ->get('/custom4/{directory}/{params}/chat', [Custom4Controller::class, 'custom4_template'])
 ->where([
  'directory' => '[a-zA-Z0-9._-]+', // Accetta lettere, numeri, underscore, punti e trattini
  'params'    => '[a-zA-Z0-9=+/]+', // Pattern per "id_richiesta_idsito_tipo"
 ]);
Route::middleware(['settaSessione', 'dizionario'])
 ->get('/custom4/{directory}/{params}/questionario', [Custom4Controller::class, 'questionario'])
 ->where([
  'directory' => '[a-zA-Z0-9._-]+', // Accetta lettere, numeri, underscore, punti e trattini
  'params'    => '[a-zA-Z0-9=+/]+', // Pattern per "id_richiesta_idsito_tipo"
 ]);

## TEMPLATE CUSTOM 5 ##
Route::middleware(['settaSessione', 'dizionario'])
 ->get('/custom5/{directory}/{params}/index', [Custom5Controller::class, 'custom5_template'])
 ->where([
  'directory' => '[a-zA-Z0-9._-]+', // Accetta lettere, numeri, underscore, punti e trattini
  'params'    => '[a-zA-Z0-9=+/]+', // Pattern per "id_richiesta_idsito_tipo"
 ]);
Route::middleware(['settaSessione', 'dizionario'])
 ->get('/custom5/{directory}/{params}/chat', [Custom5Controller::class, 'custom5_template'])
 ->where([
  'directory' => '[a-zA-Z0-9._-]+', // Accetta lettere, numeri, underscore, punti e trattini
  'params'    => '[a-zA-Z0-9=+/]+', // Pattern per "id_richiesta_idsito_tipo"
 ]);
Route::middleware(['settaSessione', 'dizionario'])
 ->get('/custom5/{directory}/{params}/questionario', [Custom5Controller::class, 'questionario'])
 ->where([
  'directory' => '[a-zA-Z0-9._-]+', // Accetta lettere, numeri, underscore, punti e trattini
  'params'    => '[a-zA-Z0-9=+/]+', // Pattern per "id_richiesta_idsito_tipo"
 ]);

## TEMPLATE CUSTOM 6 ##
Route::middleware(['settaSessione', 'dizionario'])
 ->get('/custom6/{directory}/{params}/index', [Custom6Controller::class, 'custom6_template'])
 ->where([
  'directory' => '[a-zA-Z0-9._-]+', // Accetta lettere, numeri, underscore, punti e trattini
  'params'    => '[a-zA-Z0-9=+/]+', // Pattern per "id_richiesta_idsito_tipo"
 ]);
Route::middleware(['settaSessione', 'dizionario'])
 ->get('/custom6/{directory}/{params}/chat', [Custom6Controller::class, 'custom6_template'])
 ->where([
  'directory' => '[a-zA-Z0-9._-]+', // Accetta lettere, numeri, underscore, punti e trattini
  'params'    => '[a-zA-Z0-9=+/]+', // Pattern per "id_richiesta_idsito_tipo"
 ]);
Route::middleware(['settaSessione', 'dizionario'])
 ->get('/custom6/{directory}/{params}/questionario', [Custom6Controller::class, 'questionario'])
 ->where([
  'directory' => '[a-zA-Z0-9._-]+', // Accetta lettere, numeri, underscore, punti e trattini
  'params'    => '[a-zA-Z0-9=+/]+', // Pattern per "id_richiesta_idsito_tipo"
 ]);

## TEMPLATE CUSTOM 7 ##
Route::middleware(['settaSessione', 'dizionario'])
 ->get('/custom7/{directory}/{params}/index', [Custom7Controller::class, 'custom7_template'])
 ->where([
  'directory' => '[a-zA-Z0-9._-]+', // Accetta lettere, numeri, underscore, punti e trattini
  'params'    => '[a-zA-Z0-9=+/]+', // Pattern per "id_richiesta_idsito_tipo"
 ]);
Route::middleware(['settaSessione', 'dizionario'])
 ->get('/custom7/{directory}/{params}/chat', [Custom7Controller::class, 'custom7_template'])
 ->where([
  'directory' => '[a-zA-Z0-9._-]+', // Accetta lettere, numeri, underscore, punti e trattini
  'params'    => '[a-zA-Z0-9=+/]+', // Pattern per "id_richiesta_idsito_tipo"
 ]);
Route::middleware(['settaSessione', 'dizionario'])
 ->get('/custom7/{directory}/{params}/questionario', [Custom7Controller::class, 'questionario'])
 ->where([
  'directory' => '[a-zA-Z0-9._-]+', // Accetta lettere, numeri, underscore, punti e trattini
  'params'    => '[a-zA-Z0-9=+/]+', // Pattern per "id_richiesta_idsito_tipo"
 ]);

## TEMPLATE CUSTOM 8 ##
Route::middleware(['settaSessione', 'dizionario'])
 ->get('/custom8/{directory}/{params}/index', [Custom8Controller::class, 'custom8_template'])
 ->where([
  'directory' => '[a-zA-Z0-9._-]+', // Accetta lettere, numeri, underscore, punti e trattini
  'params'    => '[a-zA-Z0-9=+/]+', // Pattern per "id_richiesta_idsito_tipo"
 ]);
Route::middleware(['settaSessione', 'dizionario'])
 ->get('/custom8/{directory}/{params}/chat', [Custom8Controller::class, 'custom8_template'])
 ->where([
  'directory' => '[a-zA-Z0-9._-]+', // Accetta lettere, numeri, underscore, punti e trattini
  'params'    => '[a-zA-Z0-9=+/]+', // Pattern per "id_richiesta_idsito_tipo"
 ]);
Route::middleware(['settaSessione', 'dizionario'])
 ->get('/custom8/{directory}/{params}/questionario', [Custom8Controller::class, 'questionario'])
 ->where([
  'directory' => '[a-zA-Z0-9._-]+', // Accetta lettere, numeri, underscore, punti e trattini
  'params'    => '[a-zA-Z0-9=+/]+', // Pattern per "id_richiesta_idsito_tipo"
 ]);

## TEMPLATE CUSTOM 9 ##
Route::middleware(['settaSessione', 'dizionario'])
 ->get('/custom9/{directory}/{params}/index', [Custom9Controller::class, 'custom9_template'])
 ->where([
  'directory' => '[a-zA-Z0-9._-]+', // Accetta lettere, numeri, underscore, punti e trattini
  'params'    => '[a-zA-Z0-9=+/]+', // Pattern per "id_richiesta_idsito_tipo"
 ]);
Route::middleware(['settaSessione', 'dizionario'])
 ->get('/custom9/{directory}/{params}/chat', [Custom9Controller::class, 'custom9_template'])
 ->where([
  'directory' => '[a-zA-Z0-9._-]+', // Accetta lettere, numeri, underscore, punti e trattini
  'params'    => '[a-zA-Z0-9=+/]+', // Pattern per "id_richiesta_idsito_tipo"
 ]);
Route::middleware(['settaSessione', 'dizionario'])
 ->get('/custom9/{directory}/{params}/questionario', [Custom9Controller::class, 'questionario'])
 ->where([
  'directory' => '[a-zA-Z0-9._-]+', // Accetta lettere, numeri, underscore, punti e trattini
  'params'    => '[a-zA-Z0-9=+/]+', // Pattern per "id_richiesta_idsito_tipo"
 ]);

## CHECKIN TEMPLATE ##
Route::middleware(['settaSessione', 'dizionario'])
 ->get('/checkin/{directory}/{params}/index', [CheckinController::class, 'checkin_online'])
 ->where([
  'directory' => '[a-zA-Z0-9._-]+', // Accetta lettere, numeri, underscore, punti e trattini
  'params'    => '[a-zA-Z0-9=+/]+', // Pattern per "id_richiesta_idsito_tipo"
 ]);
## FINE CHECKIN TEMPLATE ##
