<?php

namespace App\Http\Controllers;

use Datetime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CheckinController extends Controller
{
    
    /**
     * UpFile
     *
     * @param  mixed $request
     * @return void
     */
    public function UpFile(Request $request)
    {
        $file   = $request->file('file');

        $filename = $request->idsito.'_'.date('dmY').'_'.$file->getClientOriginalName();
 
        $valid_extensions = array("pdf","jpg","jpeg","gif","png");
 
        if(in_array($file->getClientOriginalExtension(), $valid_extensions)) {
 
            $destinationPath = 'checkin/uploads';
 
            $file->move($destinationPath, $filename);
            echo $filename;
        } 
    }
    
    /**
     * insertCheckin
     *
     * @param  mixed $request
     * @return void
     */
    public function insertCheckin(Request $request)
    {
            $idsito          = $request->idsito;
            $Lingua          = $request->lang;
            $prenotazione    = $request->Prenotazione;
            $DataNascita     = $request->DataNascita;
            $DataRilascio    = $request->DataRilascio;
            $DataScadenza    = $request->DataScadenza;
           
            $TipoDocumento   = addslashes($request->TipoDocumento);
            $ComuneEmissione = addslashes($request->ComuneEmissione);
            $StatoEmissione  = addslashes($request->StatoEmissione);
            $Nome            = addslashes($request->Nome);
            $Cognome         = addslashes($request->Cognome);
            $Cittadinanza    = addslashes($request->Cittadinanza);
            $Documento       = addslashes($request->Documento);
                      
            $Indirizzo       = addslashes($request->Indirizzo);
            $StatoNascita    = addslashes($request->StatoNascita); 
            
            $Note            = addslashes($request->Note);  
            $session_id      = Session::getId();    

            
            if($request->Name){
                session(['Name' => $request->Name]);
            }
            if($request->Surname){
                session(['Surname' => $request->Surname]);
            }
            if($request->NumeroPersone){
                session(['NumeroPersone' => $request->NumeroPersone]);
            }

          
            DB::table('hospitality_checkin')->insert(
                                                        [
                                                            'idsito'              => $idsito,
                                                            'session_id'          => $session_id,
                                                            'lang'                => $Lingua,
                                                            'Prenotazione'        => $prenotazione,
                                                            'NumeroPersone'       => $request->NumeroPersone,
                                                            'TipoComponente'      => $request->TipoComponente,
                                                            'TipoDocumento'       => $TipoDocumento,
                                                            'Documento'           => $Documento,
                                                            'NumeroDocumento'     => $request->NumeroDocumento,
                                                            'ComuneEmissione'     => $ComuneEmissione,
                                                            'StatoEmissione'      => $StatoEmissione,
                                                            'DataRilascio'        => $DataRilascio,
                                                            'DataScadenza'        => $DataScadenza,
                                                            'Nome'                => $Nome,
                                                            'Cognome'             => $Cognome,
                                                            'Sesso'               => $request->Sesso,
                                                            'Cittadinanza'        => $Cittadinanza,
                                                            'IdRegione'           => $request->id_regione,
                                                            'CittaBis'            => $request->CittaBis,
                                                            'Provincia'           => $request->Provincia,
                                                            'ProvinciaBis'        => $request->ProvinciaBis,
                                                            'Indirizzo'           => $Indirizzo,
                                                            'Citta'               => $request->Citta,
                                                            'Cap'                 => $request->Cap,
                                                            'DataNascita'         => $DataNascita,
                                                            'Statonascita'        => $StatoNascita,
                                                            'IdRegione2'          => $request->id_regione2,
                                                            'ProvinciaNascita'    => $request->ProvinciaNascita,
                                                            'ProvinciaNascitaBis' => $request->ProvinciaNascitaBis,
                                                            'LuogoNascita'        => $request->LuogoNascita,
                                                            'LuogoNascitaBis'     => $request->LuogoNascitaBis,     
                                                            'Note'                => $Note,
                                                            'data_compilazione'  => date('Y-m-d H:i:s'),
                                                        ]
                                                    );
            
            
            if($request->NumeroPersone > 1){ 
                return redirect('checkin/'.$request->directory.'/'.$request->params.'/1/step');
            }else{
                return redirect('checkin/'.$request->directory.'/'.$request->params.'/conferma');
            }
             


       
    }
    

    public function insertStep(Request $request)
    {
            $idsito          = $request->idsito;
            $Lingua          = $request->lang;
            $prenotazione    = $request->Prenotazione;
            $DataNascita     = $request->DataNascita;
            $DataRilascio    = $request->DataRilascio;
            $DataScadenza    = $request->DataScadenza;
           
            $TipoDocumento   = addslashes($request->TipoDocumento);
            $ComuneEmissione = addslashes($request->ComuneEmissione);
            $StatoEmissione  = addslashes($request->StatoEmissione);
            $Nome            = addslashes($request->Nome);
            $Cognome         = addslashes($request->Cognome);
            $Cittadinanza    = addslashes($request->Cittadinanza);
            $Documento       = addslashes($request->Documento);
                      
            $Indirizzo       = addslashes($request->Indirizzo);
            $StatoNascita    = addslashes($request->StatoNascita); 
            
            $Note            = addslashes($request->Note);  
            $session_id      = Session::getId();    

            


          
            DB::table('hospitality_checkin')->insert(
                                                        [
                                                            'idsito'              => $idsito,
                                                            'session_id'          => $session_id,
                                                            'lang'                => $Lingua,
                                                            'Prenotazione'        => $prenotazione,
                                                            'NumeroPersone'       => $request->NumeroPersone,
                                                            'TipoComponente'      => $request->TipoComponente,
                                                            'TipoDocumento'       => $TipoDocumento,
                                                            'Documento'           => $Documento,
                                                            'NumeroDocumento'     => $request->NumeroDocumento,
                                                            'ComuneEmissione'     => $ComuneEmissione,
                                                            'StatoEmissione'      => $StatoEmissione,
                                                            'DataRilascio'        => $DataRilascio,
                                                            'DataScadenza'        => $DataScadenza,
                                                            'Nome'                => $Nome,
                                                            'Cognome'             => $Cognome,
                                                            'Sesso'               => $request->Sesso,
                                                            'Cittadinanza'        => $Cittadinanza,
                                                            'IdRegione'           => $request->id_regione,
                                                            'CittaBis'            => $request->CittaBis,
                                                            'Provincia'           => $request->Provincia,
                                                            'ProvinciaBis'        => $request->ProvinciaBis,
                                                            'Indirizzo'           => $Indirizzo,
                                                            'Citta'               => $request->Citta,
                                                            'Cap'                 => $request->Cap,
                                                            'DataNascita'         => $DataNascita,
                                                            'Statonascita'        => $StatoNascita,
                                                            'IdRegione2'          => $request->id_regione2,
                                                            'ProvinciaNascita'    => $request->ProvinciaNascita,
                                                            'ProvinciaNascitaBis' => $request->ProvinciaNascitaBis,
                                                            'LuogoNascita'        => $request->LuogoNascita,
                                                            'LuogoNascitaBis'     => $request->LuogoNascitaBis,     
                                                            'Note'                => $Note,
                                                            'data_compilazione'  => date('Y-m-d H:i:s'),
                                                        ]
                                                    );
            
            
            if(session('NumeroPersone') > $request->step){ 
                return redirect('checkin/'.$request->directory.'/'.$request->params.'/'.$request->step.'/step');
            }else{
                return redirect('checkin/'.$request->directory.'/'.$request->params.'/conferma');
            }
             


       
    }

    /**
     * contentBanner
     *
     * @param  mixed $idsito
     * @param  mixed $Lingua
     * @param  mixed $Logo
     * @return void
     */
    public function contentBanner($idsito,$Lingua,$Logo)
    {
        $output = '';
        
        $sel      = "SELECT 
                        hospitality_boxinfo_checkin_lang.Titolo,
                        hospitality_boxinfo_checkin_lang.Descrizione
                    FROM 
                        hospitality_boxinfo_checkin
                    INNER JOIN 
                        hospitality_boxinfo_checkin_lang
                    ON
                        hospitality_boxinfo_checkin.Id =  hospitality_boxinfo_checkin_lang.Id_infohotel                  
                    WHERE 
                        hospitality_boxinfo_checkin_lang.idsito = :idsito
                    AND
                        hospitality_boxinfo_checkin_lang.Lingua = :Lingua
                    AND
                        hospitality_boxinfo_checkin.Abilitato = :Abilitato";
        $res = DB::select($sel, ['idsito' => $idsito, 'Lingua' => $Lingua, 'Abilitato' => 1]);
        $tot      = sizeof($res);
        if($tot > 0){
            $row  = $res[0];
                
            $output = '<link rel="stylesheet" type="text/css" href="/checkin/css/stylebanner_modale.css" />'."\r\n";
            $output .= '<!-- INIZIO BANNER -->
                            <div id="bannerC">
                                <div class="bannerOC"></div>
                                <img src="/checkin/images/icona.png" class="icona">
                                <a class="white" href="#ANTICOVIDMOD" rel="modal:open">'.$row->Titolo.'
                                </a>
                            </div>'."\r\n";   
             $output .= '	<script>
                                $(document).scroll(function () {
                                    if ($(window).scrollTop() > 100) {
                                        $("#bannerC").addClass(\'close\');
    
                                    } 
                                });
                                $("#bannerC .bannerOC ").click(function(){
                                    $("#bannerC").toggleClass(\'close\');
                                })
                            </script> '."\r\n";           
    
    
            $output .= '<div id="ANTICOVIDMOD" class="modal">
                            <div class="pul_print_top"> 
                                <button type="button" class="btn btn-default" onclick="js:window.print()">STAMPA</button> 
                            </div> 
                            '.($Logo == ''?'<i class="fa fa-bed fa-5x fa-fw"></i>':'<img src="'.config('global.settings.BASE_URL_IMG').'uploads/loghi/'.$Logo.'" />').'
                            <br /><br />
                            <titolo>'.$row->Titolo.'</titolo>
                            <sottotitolo>'.$row->Descrizione.'</sottotitolo>
                            <div class="modal-footer"> 
                                <button type="button" class="btn btn-default" onclick="js:window.print()">STAMPA</button> 
                            </div> 
                        </div>'."\r\n";
            $output .= '<style>
                            @media print { 
                                .modal { 
                                    position: absolute; 
                                    left: 0; 
                                    top: 0; 
                                    margin: 0; 
                                    padding: 0; 
                                    overflow: visible!important; 
                                } 
                            } 
                        </style>'."\r\n";
            $output .= '<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.js"></script>'."\r\n";
            $output .= '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.css" />'."\r\n";   
                
        }

        return $output;
    }
        
    
    /**
     * check_Checkin
     *
     * @param  mixed $idsito
     * @param  mixed $Nprenotazione
     * @return void
     */
    public function check_Checkin($idsito,$Nprenotazione)
    {
        $cs  = "SELECT * FROM hospitality_checkin WHERE Prenotazione = :prenotazione AND idsito = :idsito";
        $res = DB::select($cs,['prenotazione' => $Nprenotazione, 'idsito' => $idsito]);
        $tot = sizeof($res);

        return $tot;
        
    }


    public function listaStati()
    {
            $list_stato = '';
        // lista dello stato di emissione
        $result = DB::select("SELECT * FROM stati WHERE nome_stato != '' ORDER BY nome_stato");
        foreach($result as $sw){
            $list_stato .='<option value="'.($sw->nome_stato).'">'.($sw->nome_stato).'</option>';                             
        }
        return $list_stato;  
    }

    public function listaRegioni(Request $request)
    {
        if ($request->id_stato == 'Italia') {
            $output = '';
            $sql = 'SELECT * FROM regioni where id_stato = :id_stato';
            $ret = DB::select($sql, ['id_stato' => 112]);
            if (sizeof($ret)) {
                $output = '<option selected="selected" value="">--</option>'."\r\n";
                foreach($ret as $key => $value) {
                    $output .= '<option value="'.$value->codice_regione.'">'.$value->nome_regione.'</option>'."\r\n";
                }
            } else {
                $output = '';
            }
        }
        return $output;
    }

    public function listaProvince(Request $request)
    {

            $lista_province = '';
            $sql = 'SELECT * FROM province where codice_regione = :codice_regione';
            $ret = DB::select($sql, ['codice_regione' => $request->id_regione]);
            if (sizeof($ret)) {
                $lista_province = '<option selected="selected" value="">--</option>'."\r\n";
                foreach($ret as $key => $value) {
                    $lista_province .= '<option value="'.$value->sigla_provincia.'">'.$value->sigla_provincia.'</option>'."\r\n";
                }       
            } else {
                $lista_province = '';
            }

        return $lista_province;

    }
    
    public function listaComuni(Request $request)
    {

            $output = '';
            $sel = 'SELECT * FROM province where sigla_provincia = :sigla_provincia';
            $res = DB::select($sel, ['sigla_provincia' => $request->sigla_provincia]);
            $codice_provincia = $res[0]->codice_provincia;
            $sql = 'SELECT * FROM comuni where codice_provincia = :codice_provincia';
            $ret = DB::select($sql, ['codice_provincia' => $codice_provincia]);
            if (sizeof($ret)) {
                $output = '<option selected="selected" value="">--</option>'."\r\n";
                foreach($ret as $key => $value) {
                    $output .= '<option value="'.$value->nome_comune.'">'.$value->nome_comune.'</option>'."\r\n";
                }
            } else {
                $output = '';
            }
   
        return $output;

    }

    public function getNomeRegione($id_regione)
    {

            $sql = 'SELECT * FROM regioni where codice_regione = :codice_regione';
            $ret = DB::select($sql, ['codice_regione' => $id_regione]);
            if (sizeof($ret)) {
                $output = $ret[0]->nome_regione;
            } else {
                $output = '';

            }
        
        return $output;
    }

    public function checkin_online($directory, $params, Request $request)
    {
        $template = 'checkin';
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


        $row = $this->getCliente($idsito);
     
        $email       = $row->email;
        $hotel       = $row->nome;
        $indirizzo   = $row->indirizzo;
        $comune      = $row->nome_comune;
        $cap         = $row->cap;
        $CIR         = $row->CIR;
        $CIN         = $row->CIN;
        if(strstr($row->tel,'+39') || strstr($row->tel,'0039')){
            $tel     = $row->tel;
        }else{
            $tel     = '+39 '.$row->tel;
        }
        $prov        = $row->sigla_provincia;
        $SitoWeb     = 'https://'.$row->web;
        $Logo        = $row->logo;
       

        $select = "SELECT hospitality_guest.*
                                FROM hospitality_guest
                                WHERE hospitality_guest.idsito = :idsito
                                AND hospitality_guest.Id = :id_richiesta
                                ORDER BY hospitality_guest.Id DESC";
        $sel  = DB::select($select,['idsito' => $idsito, 'id_richiesta' => $id_richiesta]);
        $rows = $sel[0];
              

        $Id            = $rows->Id;
        $Nome          = stripslashes($rows->Nome);
        $Cognome       = stripslashes($rows->Cognome);
        $Lingua        = $rows->Lingua;
        $DataA_tmp     = explode("-",$rows->DataArrivo);
        $DataArrivo    = $DataA_tmp[2].'-'.$DataA_tmp[1].'-'.$DataA_tmp[0];
        $DataP_tmp     = explode("-",$rows->DataPartenza);
        $DataPartenza  = $DataP_tmp[2].'-'.$DataP_tmp[1].'-'.$DataP_tmp[0];
        $DataR_tmp     = explode("-",$rows->DataRichiesta);
        $DataRichiesta = $DataR_tmp[2].'-'.$DataR_tmp[1].'-'.$DataR_tmp[0];
        $Nprenotazione = $rows->NumeroPrenotazione;
        $NumeroAdulti  = $rows->NumeroAdulti;
        $NumeroBambini = $rows->NumeroBambini;
        $TotalePersone = $NumeroAdulti+$NumeroBambini;
        $NomeCliente   = $Nome .' '.$Cognome;

        $NumeroPersone = $TotalePersone;

        $tot_cs = $this->check_Checkin($idsito,$Nprenotazione);


        // Recupera il valore di 'NumeroPersone' dalla richiesta
        $numeroPersone = $request->input('NumeroPersone', 1); // Default a 1 se non presente
        if(isset($numeroPersone)){
            $valore=(int)$numeroPersone;
        }else{
            $valore = 1;
        }  
        // Imposta la sessione per il numero di persone
        $sessionData = [];
        for ($i = 1; $i <= 10; $i++) {
            $sessionData[$i] = ($i == $numeroPersone) ? "selected" : "";
        }
        Session::put('n_p', $sessionData);

        // Genera l'elenco delle opzioni per il select
        $listPers = '';
        foreach ($sessionData as $key => $value) {
            $listPers .= '<option value="' . $key . '" ' . $value . '>' . $key . '</option>';
        }

        return view('checkin_template/index',
                        [
                            'params'        => $params,
                            'template'      => $template,
                            'directory'     => $directory,
                            'id_richiesta'  => $id_richiesta,
                            'idsito'        => $idsito,
                            'tipo'          => $tipo,
                            'tot_cs'        => $tot_cs,
                            'Lingua'        => $Lingua,
                            'hotel'         => $hotel,
                            'email'         => $email,
                            'indirizzo'     => $indirizzo,
                            'comune'        => $comune,
                            'cap'           => $cap,
                            'CIR'           => $CIR,
                            'CIN'           => $CIN,
                            'tel'           => $tel,
                            'prov'          => $prov,
                            'SitoWeb'       => $SitoWeb,
                            'Logo'          => $Logo,
                            'Nome'          => $Nome,
                            'Cognome'       => $Cognome,
                            'DataArrivo'    => $DataArrivo,
                            'DataPartenza'  => $DataPartenza,
                            'DataRichiesta' => $DataRichiesta,
                            'Nprenotazione' => $Nprenotazione,
                            'NumeroAdulti'  => $NumeroAdulti,
                            'NumeroBambini' => $NumeroBambini,
                            'TotalePersone' => $TotalePersone,
                            'NomeCliente'   => $NomeCliente,
                            'NumeroPersone' => $NumeroPersone,
                            'NomeCliente'   => $NomeCliente,
                            'listPers'      => $listPers,
                            'list_stato'    => $this->listaStati(),
                            'contentBanner' => $this->contentBanner($idsito,$Lingua,$Logo),

                        ]
                    );
    }


    public function step($directory, $params, $step, Request $request)
    {
        $template = 'checkin';
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


        $row = $this->getCliente($idsito);
     
        $email       = $row->email;
        $hotel       = $row->nome;
        $indirizzo   = $row->indirizzo;
        $comune      = $row->nome_comune;
        $cap         = $row->cap;
        $CIR         = $row->CIR;
        $CIN         = $row->CIN;
        if(strstr($row->tel,'+39') || strstr($row->tel,'0039')){
            $tel     = $row->tel;
        }else{
            $tel     = '+39 '.$row->tel;
        }
        $prov        = $row->sigla_provincia;
        $SitoWeb     = 'https://'.$row->web;
        $Logo        = $row->logo;
       

        $select = "SELECT hospitality_guest.*
                                FROM hospitality_guest
                                WHERE hospitality_guest.idsito = :idsito
                                AND hospitality_guest.Id = :id_richiesta
                                ORDER BY hospitality_guest.Id DESC";
        $sel  = DB::select($select,['idsito' => $idsito, 'id_richiesta' => $id_richiesta]);
        $rows = $sel[0];
              

        $Id            = $rows->Id;
        $Nome          = stripslashes($rows->Nome);
        $Cognome       = stripslashes($rows->Cognome);
        $Lingua        = $rows->Lingua;
        $DataA_tmp     = explode("-",$rows->DataArrivo);
        $DataArrivo    = $DataA_tmp[2].'-'.$DataA_tmp[1].'-'.$DataA_tmp[0];
        $DataP_tmp     = explode("-",$rows->DataPartenza);
        $DataPartenza  = $DataP_tmp[2].'-'.$DataP_tmp[1].'-'.$DataP_tmp[0];
        $DataR_tmp     = explode("-",$rows->DataRichiesta);
        $DataRichiesta = $DataR_tmp[2].'-'.$DataR_tmp[1].'-'.$DataR_tmp[0];
        $Nprenotazione = $rows->NumeroPrenotazione;
        $NumeroAdulti  = $rows->NumeroAdulti;
        $NumeroBambini = $rows->NumeroBambini;
        $TotalePersone = $NumeroAdulti+$NumeroBambini;
        $NomeCliente   = $Nome .' '.$Cognome;

        $NumeroPersone = $TotalePersone;


        $select = "SELECT * FROM hospitality_checkin WHERE session_id = :sessione ORDER BY Id DESC";
        $result = DB::select($select,['sessione' => Session::getId()]);
        $row = $result[0];

        $Citta         = $row->Citta;
        $id_regione    = $row->IdRegione;
        $NomeRegione   = $this->getNomeRegione($id_regione);
        $Provincia     = $row->Provincia;
        $Cap           = $row->Cap;
        $Indirizzo     = $row->Indirizzo;
        $Cittadinanza = $row->Cittadinanza;



        $indice =  ($step-1);
        $nome = session('Name')[$indice] ?? null;
        $cognome = session('Surname')[$indice] ?? null;

        $step = ($request->step + 1);


        return view('checkin_template/step',
                        [
                            'step'          => $step,
                            'params'        => $params,
                            'template'      => $template,
                            'directory'     => $directory,
                            'id_richiesta'  => $id_richiesta,
                            'nome'          => $nome,
                            'cognome'       => $cognome,
                            'idsito'        => $idsito,
                            'tipo'          => $tipo,
                            'Lingua'        => $Lingua,
                            'hotel'         => $hotel,
                            'email'         => $email,
                            'indirizzo'     => $indirizzo,
                            'comune'        => $comune,
                            'cap'           => $cap,
                            'CIR'           => $CIR,
                            'CIN'           => $CIN,
                            'tel'           => $tel,
                            'prov'          => $prov,
                            'SitoWeb'       => $SitoWeb,
                            'Logo'          => $Logo,
                            'Nome'          => $Nome,
                            'Cognome'       => $Cognome,
                            'DataArrivo'    => $DataArrivo,
                            'DataPartenza'  => $DataPartenza,
                            'DataRichiesta' => $DataRichiesta,
                            'Nprenotazione' => $Nprenotazione,
                            'NumeroAdulti'  => $NumeroAdulti,
                            'NumeroBambini' => $NumeroBambini,
                            'TotalePersone' => $TotalePersone,
                            'NomeCliente'   => $NomeCliente,
                            'NumeroPersone' => $NumeroPersone,
                            'NomeCliente'   => $NomeCliente,
                            'list_stato'    => $this->listaStati(),
                            'contentBanner' => $this->contentBanner($idsito,$Lingua,$Logo),
                            'Citta'         => $Citta,
                            'id_regione'    => $id_regione,
                            'NomeRegione'   => $NomeRegione,
                            'Provincia'     => $Provincia,
                            'Cap'           => $Cap,
                            'Indirizzo'     => $Indirizzo,
                            'Cittadinanza'  => $Cittadinanza,

                        ]
                    );
    }


    public function conferma($directory, $params, Request $request)
    {
        $template = 'checkin';
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


        $row = $this->getCliente($idsito);
     
        $email       = $row->email;
        $hotel       = $row->nome;
        $indirizzo   = $row->indirizzo;
        $comune      = $row->nome_comune;
        $cap         = $row->cap;
        $CIR         = $row->CIR;
        $CIN         = $row->CIN;
        if(strstr($row->tel,'+39') || strstr($row->tel,'0039')){
            $tel     = $row->tel;
        }else{
            $tel     = '+39 '.$row->tel;
        }
        $prov        = $row->sigla_provincia;
        $SitoWeb     = 'https://'.$row->web;
        $Logo        = $row->logo;
       

        $select = "SELECT hospitality_guest.*
                                FROM hospitality_guest
                                WHERE hospitality_guest.idsito = :idsito
                                AND hospitality_guest.Id = :id_richiesta
                                ORDER BY hospitality_guest.Id DESC";
        $sel  = DB::select($select,['idsito' => $idsito, 'id_richiesta' => $id_richiesta]);
        $rows = $sel[0];
              

        $Id            = $rows->Id;
        $Nome          = stripslashes($rows->Nome);
        $Cognome       = stripslashes($rows->Cognome);
        $Lingua        = $rows->Lingua;
        $Nprenotazione = $rows->NumeroPrenotazione;
    
        
        $mail = new PHPMailer;

        $mail->CharSet   = "UTF-8";
        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->Host       = env('MAIL_HOST');
        $mail->SMTPAuth   = true;
        $mail->Username   = env('MAIL_USERNAME');
        $mail->Password   = env('MAIL_PASSWORD');
        $mail->SMTPSecure = env('MAIL_ENCRYPTION');
        $mail->Port       = env('MAIL_PORT');

        $mail->setFrom(env('MAIL_FROM_ADDRESS'),env('APP_NAME'));

        $mail->addAddress($email, $Nome.' '.$Cognome);
        $mail->isHTML(true);
        $mail->Subject = 'Hai ricevuto un modulo di Check-in On-line da '.$Nome.' '.$Cognome.' - QUOTO!';
   
          $messaggio = '<html>
                            <head>
                              <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
                              <title>QUOTO!</title>
                            </head>
                            <body>
                              <table cellpadding="0" cellspacing="0" width="100%" border="0">
                                <tr>
                                    <td align="left" valign="top">
                                       <img src="'.env('APP_URL').'img/logo_email.png">
                                       <h1>Un modulo di Check-in On-line è stato compilato!</h1> 
                                       <p><b>Prenotazione confermata:</b> <b>Nr.</b> '.$Nprenotazione.'</p>
                                       <p><b>Modulo compilato da</b> '.$Nome.' '.$Cognome.'</p>
                                       <p style="font-size:80%">Entra in QUOTO! per controllare il suo contenuto!</p>
                                     </td>
                                </tr>
                              </table>
                             </body>
                          </html>'; 		      
            
              $mail->msgHTML($messaggio, dirname(__FILE__));
              $mail->AltBody = 'To view the message, please use an HTML compatible email viewer!';
              $mail->send();  


              return view('checkin_template/conferma',
                                                [
                                                    'params'        => $params,
                                                    'template'      => $template,
                                                    'directory'     => $directory,
                                                    'id_richiesta'  => $id_richiesta,
                                                    'idsito'        => $idsito,
                                                    'tipo'          => $tipo,
                                                    'Lingua'        => $Lingua,
                                                    'hotel'         => $hotel,
                                                    'email'         => $email,
                                                    'indirizzo'     => $indirizzo,
                                                    'comune'        => $comune,
                                                    'cap'           => $cap,
                                                    'CIR'           => $CIR,
                                                    'CIN'           => $CIN,
                                                    'tel'           => $tel,
                                                    'prov'          => $prov,
                                                    'SitoWeb'       => $SitoWeb,
                                                    'Logo'          => $Logo,
                                                    'Nome'          => $Nome,
                                                    'Cognome'       => $Cognome,
                                                    'Nprenotazione' => $Nprenotazione,
                                                    'contentBanner' => $this->contentBanner($idsito,$Lingua,$Logo),
                                                ]
                                            );
    }

}