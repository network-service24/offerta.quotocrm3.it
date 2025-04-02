<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SettaSessione
{
    public function handle(Request $request, Closure $next)
    {
        $params = $request->route('params'); // Es. 'id_richiesta_idsito_tipo'

        if ($params) {

            $decodedParams = base64_decode($params);
            // Assumiamo che 'params' sia nel formato 'id_richiesta_idsito_tipo'
            $parts = explode('_',$decodedParams);

            list($id_richiesta, $idsito, $tipo) = $parts;


                session(['IDSITO' => $idsito]);

                session(['IDRICHIESTA' => $id_richiesta]);
        
        }

        return $next($request);
    }
}
