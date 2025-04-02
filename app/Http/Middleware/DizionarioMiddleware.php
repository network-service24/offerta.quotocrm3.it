<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;

class DizionarioMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle($request, Closure $next)
    {
        $idsito = session('IDSITO');
        $id_richiesta = session('IDRICHIESTA');
        // Recupera la lingua dal database in base all'id_richiesta
        if ($id_richiesta) {
            $Lingua = DB::table('hospitality_guest')
                        ->where('Id', $id_richiesta)
                        ->value('Lingua');

            if ($Lingua) {
                session(['LINGUA' => $Lingua]); // Salva la lingua nella sessione
            }
        }

        if (!empty($idsito) && !empty($Lingua)) {

                $select = "SELECT d.etichetta, dl.testo 
                           FROM hospitality_dizionario AS d
                           INNER JOIN hospitality_dizionario_lingua AS dl 
                               ON dl.id_dizionario = d.id
                           WHERE dl.Lingua = :Lingua AND dl.idsito = :idsito";

                $res = DB::select($select, ['idsito' => $idsito, 'Lingua' => $Lingua]);
                $dizionario = [];

                if (!empty($res)) {
                    foreach ($res as $value) {
                        $dizionario[$value->etichetta] = $value->testo;
                    }
                }



            Config::set('custom.dizionario', $dizionario);
            View::share('customSettings', $dizionario);
        }

        return $next($request);
    }
}
