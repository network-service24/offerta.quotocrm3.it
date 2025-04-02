<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class Custom6Controller extends Controller
{
    public function custom6_template($directory, $params)
    {
        $template = 'custom6';
        session(['TEMPLATE' => $template]);
        // Decodifica il parametro params per sicurezza
        $decodedParams = base64_decode($params);
        // Verifica che la stringa sia valida
        if (!$decodedParams || !str_contains($decodedParams, '_')) {
            abort(404, "Formato URL non valido");
        }
        // Suddivisione dei parametri separati da "_"
        $parts = explode('_', $decodedParams);

        // Controllo per evitare errori se i parametri non sono nel formato corretto
        if (count($parts) !== 3) {
            abort(404, "Formato URL non valido");
        }

        list($id_richiesta, $idsito, $tipo) = $parts;

        return view('custom6_template/index', compact('directory', 'id_richiesta', 'idsito', 'tipo'));

    }
}
