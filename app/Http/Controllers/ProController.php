<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class ProController extends Controller
{


        /**
     * richiesta
     *
     * @param  mixed $idsito
     * @param  mixed $idrichiesta
     * @return void
     */
    public function richiesta($idsito,$idrichiesta)
    {
        $select = "SELECT 
                        hospitality_guest.*
                    FROM 
                        hospitality_guest
                    WHERE 
                        hospitality_guest.idsito = :idsito
                    AND 
                        hospitality_guest.Id = :idrichiesta
                    ORDER BY 
                        hospitality_guest.Id 
                    DESC";
        $result = DB::select($select,['idsito' => $idsito, 'idrichiesta' => $idrichiesta]);
        if(sizeof($result) > 0){
            $record =  $result;
            return $record;
        }else{
            return '';
        }
    }
    
    /**
     * countProposte
     *
     * @param  mixed $idrichiesta
     * @return void
     */
    public function countProposte($idrichiesta)
    {
        $select = "SELECT 
                        hospitality_proposte.Id as num_proposte
                    FROM 
                        hospitality_proposte
                    WHERE 
                        hospitality_proposte.id_richiesta = :idrichiesta";
        $result = DB::select($select, ['idrichiesta' => $idrichiesta]);
        $num_proposte = sizeof($result);

        return $num_proposte;
    }
        

    /**
     * contentutiTesto
     *
     * @param  mixed $IdSito
     * @param  mixed $Id
     * @param  mixed $Cliente
     * @param  mixed $TipoRichiesta
     * @param  mixed $Lingua
     * @return void
     */
    public function contenutiTesto($idsito,$Id,$Cliente,$TipoRichiesta,$Lingua,$custom)
    {
        $select     = "SELECT Testo FROM hospitality_contenuti_web_lingua WHERE IdRichiesta = :idrichiesta AND Lingua = :Lingua AND idsito = :idsito AND Testo != :Testo";
        $text_alt   = DB::select($select,['idrichiesta' => $Id, 'Lingua' => $Lingua, 'idsito' => $idsito, 'Testo' => '']);
        if(sizeof($text_alt) > 0){
            $rs_alt = $text_alt[0];
            $Testo  = str_replace("[cliente]",$Cliente,$rs_alt->Testo);
        }else{
            $etichetta = ($TipoRichiesta=='Preventivo'?'PREVENTIVO_'.$custom:'CONFERMA_'.$custom);
            $q_text = "SELECT 
                                hospitality_dizionario_lingua.testo
                            FROM 
                                hospitality_dizionario 
                            INNER JOIN 
                                hospitality_dizionario_lingua
                            ON
                                hospitality_dizionario_lingua.id_dizionario = hospitality_dizionario.id
                            WHERE 
                                hospitality_dizionario.idsito = :idsito 
                            AND
                                hospitality_dizionario.Lingua = :Lingua
                            AND
                                hospitality_dizionario.etichetta = :etichetta
                            AND
                                hospitality_dizionario_lingua.idsito = :idsito2
                            AND
                                hospitality_dizionario_lingua.Lingua = :Lingua2";
            $res    = DB::select($q_text,['idsito' => $idsito, 'Lingua' => 'it', 'idsito2' => $idsito, 'Lingua2' => $Lingua, 'etichetta' => $etichetta]);
            $rs     = $res[0];
          
            $Testo  = str_replace("[cliente]",$Cliente,$rs->testo);
        }

        return $Testo;
    }


    /**
     * imageVideo
     *
     * @param  mixed $idsito
     * @return void
     */
    public function imageVideo($idsito,$template)
    {
        $array_imgVideo = array();

        $sel_color = "SELECT * FROM hospitality_template_background WHERE idsito = :idsito AND TemplateType = :TemplateType LIMIT 1";
        $res_color = DB::select($sel_color,['idsito' => $idsito, 'TemplateType' => $template]);
        if(sizeof($res_color) > 0){
            $rCol      = $res_color[0];
            $immagine = config('global.settings.BASE_URL_IMG').'uploads/'.$idsito.'/'.$rCol->Immagine;
            $video    = $rCol->Video;
        }else{         
            $immagine = '';
            $video = '';    
        }

        return   array($immagine,$video);
    }
    
    /**
     * totaleServizi
     *
     * @param  mixed $IdSito
     * @param  mixed $id_richiesta
     * @param  mixed $id_proposta
     * @param  mixed $notti
     * @param  mixed $totaleCamere
     * @return void
     */
    public function totaleServizi($idsito,$id_richiesta,$id_proposta,$notti,$totaleCamere)
    {

        $select = "SELECT
                        hospitality_relazione_servizi_proposte.num_notti,
                        hospitality_relazione_servizi_proposte.num_persone,
                        hospitality_tipo_servizi.PrezzoServizio,
                        hospitality_tipo_servizi.CalcoloPrezzo,
                        hospitality_tipo_servizi.Id
                        
                    FROM
                        hospitality_relazione_servizi_proposte
                        INNER JOIN hospitality_tipo_servizi ON hospitality_tipo_servizi.Id = hospitality_relazione_servizi_proposte.servizio_id 
                    WHERE
                        hospitality_relazione_servizi_proposte.id_richiesta = ".$id_richiesta." 
                        AND hospitality_relazione_servizi_proposte.id_proposta = ".$id_proposta."  
                        AND hospitality_relazione_servizi_proposte.idsito = ".$idsito."  
                        AND hospitality_tipo_servizi.idsito = ".$idsito;
        $result = DB::select($select);
        if(sizeof($result)>0){
            $totale                 = 0;
            $totale_tmp_giorno      = array();
            $totale_tmp_percentuale = 0;
            $totale_tmp_tamtum      = 0;
            $totale_tmp_persona     = 0;
            $totale_tmp = array();

            foreach($result as $key => $value){

                if($value->CalcoloPrezzo == "Al giorno") {
                    if($value->PrezzoServizio != 0) {
                        $totale_tmp[$value->Id.'_'.$id_proposta] = (($value->PrezzoServizio*(int)$notti));
                    }
                }
                if($value->CalcoloPrezzo == "A percentuale") {
           
                    $totale_tmp[$value->Id.'_'.$id_proposta] =  '0.00';
                }
                if($value->CalcoloPrezzo == "Una tantum") {
                        
                    $totale_tmp[$value->Id.'_'.$id_proposta] = ($totale_tmp_tamtum+$value->PrezzoServizio);
                }
                if($value->CalcoloPrezzo == "A persona") {
                        
                    $totale_tmp[$value->Id.'_'.$id_proposta] = ($totale_tmp_persona+($value->PrezzoServizio*$value->num_notti*$value->num_persone));
                }

            }
            $somma_totale_servizi = array_sum($totale_tmp);
        }else{
            $somma_totale_servizi = 0;
        }
        return $somma_totale_servizi;
    }


    /**
     * relServiziProposte
     *
     * @param  mixed $IdSito
     * @param  mixed $id_richiesta
     * @param  mixed $id_proposta
     * @return void
     */
    public function relServiziProposte($idsito,$id_richiesta,$id_proposta)
    {
        $q = "  SELECT 
                    hospitality_relazione_servizi_proposte.*
                FROM 
                    hospitality_relazione_servizi_proposte
                WHERE 
                    id_richiesta = :id_richiesta
                AND 
                    id_proposta = :id_proposta
                AND 
                    idsito = :idsito";
            $r = DB::select($q,['idsito' => $idsito, 'id_richiesta' => $id_richiesta, 'id_proposta' => $id_proposta]);
            if(sizeof($r)>0){
                $IdServizio = array();
                foreach($r as $k => $v){
                    $IdServizio[$v->servizio_id]=1;
                }
            }else{
                $IdServizio = array();
            }

        return $IdServizio;
    }


    /**
     * serviziAggiuntivi
     *
     * @param  mixed $IdSito
     * @param  mixed $id_richiesta
     * @param  mixed $id_proposta
     * @param  mixed $Lingua
     * @param  mixed $inclusi
     * @return void
     */
    public function serviziAggiuntivi($idsito,$id_richiesta,$id_proposta,$Lingua,$inclusi=null)
    {
        $datiG              = Session::get('dati_h_guest', []);
        $DataArrivo         = $datiG->DataArrivo;
        $DataPartenza       = $datiG->DataPartenza;
        $DataRichiestaCheck = $datiG->DataRichiesta;
        $NumeroAdulti       = $datiG->NumeroAdulti;        
        $TipoRichiesta      = $datiG->TipoRichiesta;
        $formato            = "%a";
        $Notti              = $this->dateDiff($DataArrivo,$DataPartenza,$formato);
        
        $datiP              = Session::get('dati_p_guest', []);
        $Arrivo             = $datiP->Arrivo;
        $Partenza           = $datiP->Partenza;
        $PrezzoPC           = $datiP->PrezzoP;
        $formato            = "%a";
        $ANotti             = $this->dateDiff($Arrivo,$Partenza,$formato);

        $servizi = array();

        $PrezzoServizio = '';

        $IdServizio = $this->relServiziProposte($idsito,$id_richiesta,$id_proposta);

        // Query per servizi aggiuntivi
            $query  = " SELECT 
                        hospitality_tipo_servizi.*
                    FROM 
                        hospitality_tipo_servizi
                    WHERE 
                        hospitality_tipo_servizi.idsito = ".$idsito."
                    AND 
                        hospitality_tipo_servizi.Abilitato = 1 ";
        if($inclusi){
            $query  .= "AND 
                            hospitality_tipo_servizi.Obbligatorio = ".$inclusi ;
        }
            $query  .= " ORDER BY 
                            hospitality_tipo_servizi.Ordine ASC, 
                            hospitality_tipo_servizi.TipoServizio ASC";

        $risultato_query = DB::select($query);
        $record          = sizeof($risultato_query);

        if(($record)>0){

            foreach($risultato_query as $chiave => $campo){

                $q   = "SELECT 
                              hospitality_tipo_servizi_lingua.Descrizione
                            , hospitality_tipo_servizi_lingua.Servizio
                        FROM 
                            hospitality_tipo_servizi_lingua
                        WHERE 
                            hospitality_tipo_servizi_lingua.servizio_id = :servizio_id
                        AND 
                            hospitality_tipo_servizi_lingua.idsito = :idsito
                        AND 
                            hospitality_tipo_servizi_lingua.lingue = :Lingua";
                $r   = DB::select($q,['idsito' => $idsito, 'Lingua' => $Lingua, 'servizio_id' => $campo->Id]);
                if(sizeof($r)>0){
                    $rec = $r[0];
                    $Descrizione = $rec->Descrizione;
                    $Servizio    = $rec->Servizio;
                }else{
                    $Descrizione = '';
                    $Servizio    = '';
                }
                $qrel   = " SELECT 
                                  hospitality_relazione_servizi_proposte.id as id_relazionale
                                , hospitality_relazione_servizi_proposte.num_persone
                                , hospitality_relazione_servizi_proposte.num_notti
                            FROM 
                                hospitality_relazione_servizi_proposte
                            WHERE 
                                hospitality_relazione_servizi_proposte.id_richiesta = :id_richiesta
                            AND 
                                hospitality_relazione_servizi_proposte.id_proposta = :id_proposta
                            AND 
                                hospitality_relazione_servizi_proposte.servizio_id = :servizio_id";
                $rel    = DB::select($qrel, ['servizio_id' => $campo->Id, 'id_proposta' => $id_proposta, 'id_richiesta' => $id_richiesta]);
                if(sizeof($rel)>0){
                    $recrel = $rel[0];
                    $n_persone = $recrel->num_persone;
                    $n_notti   = $recrel->num_notti;
                }else{
                    $n_persone = 0;
                    $n_notti   = 0;
                }

                $s  = "     SELECT 
                                hospitality_relazione_visibili_servizi_proposte.visibile
                            FROM 
                                hospitality_relazione_visibili_servizi_proposte
                            WHERE 
                                hospitality_relazione_visibili_servizi_proposte.id_richiesta = :id_richiesta
                            AND 
                                hospitality_relazione_visibili_servizi_proposte.id_proposta = :id_proposta
                            AND 
                                hospitality_relazione_visibili_servizi_proposte.servizio_id = :servizio_id";
                $ss = DB::select($s, ['servizio_id' => $campo->Id, 'id_proposta' => $id_proposta, 'id_richiesta' => $id_richiesta]);
                if(sizeof($ss)>0){
                    $rs = $ss[0];
                    $visibile = $rs->visibile;
                }else{
                    $visibile = 0;
                }

                switch($campo->CalcoloPrezzo){
                    case "Al giorno":
                        $num_persone   = '';
                        $num_notti     = ($ANotti!=''?$ANotti:$Notti);
                        $CalcoloPrezzoServizio = ($campo->PrezzoServizio!=0?'<small>('.number_format($campo->PrezzoServizio,2,',','.').' x '.$num_notti.')</small>':'');
                        $PrezzoServizio = ($campo->PrezzoServizio!=0?'<i class="fal fa-euro-sign"></i>&nbsp;&nbsp;'.number_format(($campo->PrezzoServizio*$num_notti),2,',','.'):'<small class="text-green">Gratis</small>');
                        $pulCalcolo = false;
                    break;
                    case "A percentuale":
                      $num_persone   = '';
                      $num_notti     = '';
                      $CalcoloPrezzoServizio = '';
                      $PrezzoServizio = ($campo->PercentualeServizio!=''?'<i class="fa fa-percent"></i>&nbsp;&nbsp;'.number_format(($campo->PercentualeServizio),2):'');
                      $pulCalcolo = false;
                    break;
                    case "Una tantum":
                        $num_persone   = '';
                        $num_notti     = '';
                        $CalcoloPrezzoServizio = '';
                        $PrezzoServizio = ($campo->PrezzoServizio!=0?'<i class="fal fa-euro-sign"></i>&nbsp;&nbsp;'.number_format($campo->PrezzoServizio,2,',','.'):'<small class="text-green">Gratis</small>');
                        $pulCalcolo = false;
                    break;
                    case "A persona":
                        $num_persone = $n_persone;
                        $num_notti   = $n_notti;
                      $CalcoloPrezzoServizio = ($campo->PrezzoServizio!=0?'<small>('.number_format($campo->PrezzoServizio,2,',','.').' x '.$num_notti.' <span style="font-size:80%">gg</span> x '.$num_persone.' <small>pax</small>)</small>':'('.$num_notti.'  <small>gg</small> x '.$num_persone.' <small>pax</small>)');
                      $PrezzoServizio = ($campo->PrezzoServizio!=0?'&nbsp;&nbsp;'.number_format(($campo->PrezzoServizio*$num_notti*$num_persone),2,',','.'):'<small class="text-green">Gratis</small>');
                      $pulCalcolo = true;
                    break;
                  }



                    $servizi[] = [
                        "id"                 => $campo->Id,
                        "titolo"             => $Servizio,
                        "tipoCalcolo"        => $campo->CalcoloPrezzo,
                        "prezzo"             => $campo->PrezzoServizio,
                        "percentuale"        => $campo->PercentualeServizio,
                        "num_persone"        => $num_persone,
                        "num_notti"          => $num_notti,
                        "testo"              => $Descrizione,
                        "img"                => $campo->Icona,
                        "compreso"           => $campo->Obbligatorio,
                        "visibile"           => (!$visibile?0:1),
                        "pulCalcolo"         => $pulCalcolo,
                        "pre-selezionato"    => (($IdServizio[$campo->Id] ?? null) == 1?1:0)
                                              
                    ];
               
            }
        }else{
            $servizi = array();
        }
        return $servizi;
    }

    /**
     * infoBox
     *
     * @param  mixed $IdSito
     * @param  mixed $Id
     * @param  mixed $Lingua
     * @return void
     */
    public function infoBox($idsito,$Id,$Lingua)
    {
            $select = "SELECT 
                                hospitality_info_box_lang.*
                            FROM 
                                hospitality_info_box_lang 
                            INNER JOIN 
                                hospitality_rel_infobox_preventivo
                            ON 
                                hospitality_rel_infobox_preventivo.id_infobox = hospitality_info_box_lang.Id_info_box
                            WHERE 
                                hospitality_info_box_lang.idsito = :idsito 
                            AND
                                hospitality_rel_infobox_preventivo.idsito = :idsito2 
                            AND
                                hospitality_info_box_lang.Lingua = :Lingua
                            AND
                                hospitality_rel_infobox_preventivo.id_richiesta = :Id";
            $res    = DB::select($select, ['idsito' => $idsito, 'idsito2' => $idsito, 'Lingua' => $Lingua, 'Id' => $Id]);
            if(sizeof($res)>0){
                return $res;
            }else{
                return '';
            }

    }
    
    /**
     * informazioniHotel
     *
     * @param  mixed $IdSito
     * @param  mixed $Lingua
     * @return void
     */
    public function informazioniHotel($idsito,$Lingua)
    {
        $info_qy  = "   SELECT
                            hospitality_infohotel_lang.*
                        FROM
                            hospitality_infohotel_lang
                        INNER JOIN 
                            hospitality_infohotel 
                        ON  
                            hospitality_infohotel.Id = hospitality_infohotel_lang.Id_infohotel
                        WHERE
                            hospitality_infohotel_lang.idsito = :idsito
                        AND 
                            hospitality_infohotel_lang.Lingua = :Lingua
                        AND 
                            hospitality_infohotel.Abilitato = :Abilitato";
        $res_info = DB::select($info_qy, ['idsito' => $idsito, 'Lingua' => $Lingua, 'Abilitato' => 1]);
        $tot_info = sizeof($res_info);
        if($tot_info>0){
            $info = $res_info[0];
            return $info;
        }else{
            return '';
        }
    }

    
    /**
     * condizioniGenerali
     *
     * @param  mixed $IdSito
     * @param  mixed $Lingua
     * @param  mixed $id_politiche
     * @return void
     */
    public function condizioniGenerali($idsito,$Lingua,$id_politiche)
    {
        $select  = " SELECT
                            *
                        FROM
                            hospitality_politiche_lingua
                        WHERE
                            idsito = :idsito
                        AND 
                            id_politiche = :id_politiche
                        AND
                             Lingua = :Lingua
                        ORDER BY
                            id DESC";
        $res = DB::select($select, ['Lingua' => $Lingua, 'idsito' => $idsito, 'id_politiche' => $id_politiche]);
        $tot = sizeof($res);
        if($tot>0){
            $rw = $res[0];
            
            $condizioni_generali = $rw->testo;

            return $condizioni_generali ;
        }else{
            return '';
        }
    }

    public function gallery($idsito,$template)
    {
        $qy_Gallery  = "SELECT Id FROM hospitality_tipo_gallery WHERE TargetType = :template AND idsito = :idsito";
        $res = DB::select($qy_Gallery,['idsito' => $idsito, 'template' => $template]);
        if(sizeof($res)>0){
                $rec = $res[0];

                $q_car  = "SELECT * FROM hospitality_tipo_gallery_target WHERE IdTipoGallery = :IdTipoGallery AND idsito = :idsito AND Abilitato = :Abilitato ORDER BY rand() LIMIT 12";
                $qy_carosello = DB::select($q_car,['idsito' => $idsito, 'IdTipoGallery' => $rec->Id, 'Abilitato' => 1]);
                if(sizeof($qy_carosello)>0){
                    return $qy_carosello; 
                }else{
                    return [];
                }
        }else{
            return [];
        }

    }


      /**
       * eventi
       *
       * @param  mixed $IdSito
       * @param  mixed $Lingua
       * @param  mixed $ArrivoData
       * @param  mixed $LatCliente
       * @param  mixed $LonCliente
       * @return void
       */
      public function eventi($idsito,$Lingua,$DataArrivo,$LatCliente,$LonCliente)
      {

        $Eventi = '';
        #EVENTI
        $sel_eventi = "  SELECT hospitality_eventi.Coordinate,
                                hospitality_eventi.DataInizio,
                                hospitality_eventi.DataFine,
                                hospitality_eventi.OraInizio,
                                hospitality_eventi.OraFine,
                                hospitality_eventi.Id,
                                hospitality_eventi.Indirizzo,
                                hospitality_eventi.Immagine,
                                hospitality_eventi_lang.Titolo,
                                hospitality_eventi_lang.Descrizione
                            FROM hospitality_eventi
                                INNER JOIN hospitality_eventi_lang ON hospitality_eventi_lang.Id_eventi = hospitality_eventi.Id
                            WHERE hospitality_eventi.Abilitato = :Abilitato
                                AND hospitality_eventi.idsito = :idsito
                                AND hospitality_eventi_lang.Lingua = :Lingua
                               AND hospitality_eventi.DataInizio >= :DataArrivo
                            ORDER BY hospitality_eventi.DataInizio ASC";
                         
        $res = DB::select($sel_eventi, ['Abilitato' => 1, 'Lingua' => $Lingua, 'idsito' => $idsito,'DataArrivo' =>$DataArrivo]);
        $DE  = sizeof($res);
        if ($DE > 0) {

            //variabili
            $distanzaE = '';
            $distanceE = '';
            $lat       = '';
            $lon       = '';
            $Eventi    = '<div class="row boxcontent m-2">
                            <div class="col p-5 text-left">
                            <h4>'.strtoupper(dizionario('EVENTI')).'</h4>
                                <div class="row gy-5 row-eq-height">';

            foreach($res as $key => $rec){
                // estrapolo latitutine e longitudi del punto interesse
                $coordinateAsText = unpack('x/x/x/x/corder/Ltype/dlon/dlat', $rec->Coordinate);
                if ($coordinateAsText != '') {
                    $lat = $coordinateAsText['lon'];
                    $lon = $coordinateAsText['lat'];
                } else {
                    $lat = '';
                    $lon = '';
                }
                // calcolo la distanza
                $distanzaE = $this->calcola_distanza($LatCliente, $LonCliente, $lat, $lon);
                $distanceE = '';
                foreach ($distanzaE as $unita => $valore) {
                    $distanceE = $unita . ': ' . (number_format($valore,2,',','.')) . '<br/>';
                }
                // giro le date in formato italiano
                $array      = explode("-", $rec->DataInizio);
                $DataInizio = $array[2] . "/" . $array[1] . "/" . $array[0];
                $array2     = explode("-", $rec->DataFine);
                $DataFine   = $array2[2] . "/" . $array2[1] . "/" . $array2[0];


                $Eventi .= '
                                <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
                                    <div class="card col-eq-height">
                                        <img src="'.config('global.settings.BASE_URL_IMG').'uploads/'.$idsito.'/'.$rec->Immagine.'"  class="card-img-top" alt="...">
                                        <div class="card-body">
                                            <h4>'.$rec->Titolo.'</h4>
                                            <p>'.$rec->Descrizione.'</p>
                                            <p>
                                            <i class="far fa-fw fa-calendar-alt"></i> Dal '.$DataInizio.' <i class="far fa-fw fa-calendar-alt"></i> al '.$DataFine.'<br>
                                            '.($rec->Indirizzo!=''?'<i class="far fa-fw fa-address-card"></i>'.$rec->Indirizzo.', '.session('NomeCliente'):'').'  '.($lat!='0'?' a '.$distanceE:'').'
                                            '.($lat!='0'?'<i class="fa fa-fw fa-map-marker"></i><span id="open_map'.$rec->Id.'" onclick="document.getElementById(\'frame_lp\').src = \'/gmap?from_lati='.$lat.'&from_long='.$lon.'&travelmode=DRIVING&idsito='.$idsito.'\'; document.location.href=\'#start_map\'; return false" style="cursor:pointer">'.dizionario('VISUALIZZA_MAPPA').'</span>':'').'
                                            </p>
                                            <script>
                                                $("#open_map'.$rec->Id.'").click(function(){
                                                    $("#b_map").removeAttr("style");
                                                });
                                            </script>
                                        </div>
                                    </div>
                                </div>
                            '."\r\n";

            }
            $Eventi .=	'</div>
                        <div class="pt-5" id="b_map" style="display:none" >       
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <a name="start_map"></a>      
                                <a href="javascript:;" id="close"><i  class="far fa-times-circle bs-dark" aria-hidden="true text-" style="float:right;color:#000000"></i></a>                    
                                <iframe id="frame_lp"  src="/gmap" frameborder="0" width="100%" height="334px" class="mbr pt-1 rounded"></iframe>                                                           
                            </div>                                                  
                        </div>
                        <script>
                            $(document).ready(function() {
                                    $("#close").click(function(){
                                    $("#b_map").css("display","none");
                                });
                                });
                        </script>
                    </div>
                </div>'."\r\n";

            $distanzaE = '';
            $distanceE = '';


        }else{


            $Eventi = '';
        }

            return $Eventi;
      }

      
      /**
       * punti_interesse
       *
       * @param  mixed $IdSito
       * @param  mixed $Lingua
       * @param  mixed $LatCliente
       * @param  mixed $LonCliente
       * @return void
       */
      public function punti_interesse($idsito,$Lingua,$LatCliente,$LonCliente)
      {
        $PuntidiInteresse ='';
        #PUNTI INTERESSE
        $sel_pdi = "SELECT Coordinate,
                        hospitality_pdi.Id,
                        hospitality_pdi.Indirizzo,
                        hospitality_pdi.Immagine,
                        hospitality_pdi_lang.Titolo,
                        hospitality_pdi_lang.Descrizione
                    FROM hospitality_pdi
                        INNER JOIN hospitality_pdi_lang ON hospitality_pdi_lang.Id_pdi = hospitality_pdi.Id
                    WHERE hospitality_pdi.Abilitato = :Abilitato
                        AND hospitality_pdi_lang.Lingua = :Lingua
                        AND hospitality_pdi.idsito = :idsito
                    ORDER BY hospitality_pdi.Ordine ASC";
        $re = DB::select($sel_pdi, ['Abilitato' => 1, 'Lingua' => $Lingua, 'idsito' => $idsito]);
        $DP = sizeof($re);
        if($DP > 0){

            //variabili
            $coor_    = '';
            $distanza = '';
            $distanze = '';
            $lati     = '';
            $longi    = '';
            $PuntidiInteresse  = '<div class="row boxcontent m-2">
                                    <div class="col p-5 text-left">
                                    <h4>'.strtoupper(dizionario('PDI')).'</h4>
                                        <div class="row gy-5 row-eq-height">';

            foreach($re as$key => $rws){

                // estrapolo latitutine e longitudi del punto interesse
                $coordinateAsText = unpack('x/x/x/x/corder/Ltype/dlon/dlat', $rws->Coordinate);
                if ($coordinateAsText != '') {
                    $lati = $coordinateAsText['lon'];
                    $longi = $coordinateAsText['lat'];
                } else {
                    $lati = '';
                    $longi = '';
                }
                // calcolo la distanza
                $distanza = $this->calcola_distanza($LatCliente, $LonCliente, $lati, $longi);
                $distanze = '';
                foreach ($distanza as $unita => $valore) {
                    $distanze = $unita . ': ' . number_format($valore,2,',','.') . '<br/>';
                }

                $PuntidiInteresse .= '  <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
                                            <div class="card col-eq-height">
                                                <img src="'.config('global.settings.BASE_URL_IMG').'uploads/'.$idsito.'/'.$rws->Immagine.'"  class="card-img-top" alt="...">
                                                <div class="card-body">
                                                    <h4>'.$rws->Titolo.'</h4>
                                                    <p>'.$rws->Descrizione.'</p>
                                                    <p>
                                                        '.($rws->Indirizzo!=''?'<i class="far fa-fw fa-address-card"></i>'.$rws->Indirizzo.', '.session('NomeCliente'):'').'  '.($lati!='0'?' a '.$distanze:'').'
                                                        '.($lati!='0'?'<i class="fa fa-fw fa-map-marker"></i><span id="open_map_pdi'.$rws->Id.'" onclick="document.getElementById(\'frame_lp_pdi\').src = \'/gmap?from_lati='.$lati.'&from_long='.$longi.'&travelmode=DRIVING&idsito='.$idsito.'\'; document.location.href=\'#start_map_pdi\'; return false" style="cursor:pointer">'.dizionario('VISUALIZZA_MAPPA').'</span>':'').'
                                                    </p>
                                                    <script>
                                                        $("#open_map_pdi'.$rws->Id.'").click(function(){
                                                            $("#b_map_pdi").removeAttr("style");
                                                        });
                                                    </script>
                                                </div>
                                            </div>
                                        </div>'."\r\n";

            }
            $PuntidiInteresse .=	'</div>
                                    <div class="pt-5" id="b_map_pdi" style="display:none" >       
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                            <a name="start_map_pdi"></a>      
                                            <a href="javascript:;" id="close_pdi"><i  class="far fa-times-circle" aria-hidden="true" style="float:right;color:#000000"></i></a>                    
                                            <iframe id="frame_lp_pdi"  src="/gmap" frameborder="0" width="100%" height="334px" class="mbr pt-1  rounded"></iframe>                                                           
                                        </div>                                                  
                                    </div>
                                    <script>
                                        $(document).ready(function() {
                                                $("#close_pdi").click(function(){
                                                $("#b_map_pdi").css("display","none");
                                            });
                                        });
                                    </script>
                            </div>
                        </div>'."\r\n";

            $distanza = '';
            $distanze = '';

        }else{


            $PuntidiInteresse = '';
        }
            return $PuntidiInteresse;
      }


      public function contentBanner($idsito,$Lingua,$Logo)
      {
            $output = '';
            $sel = "SELECT
                            hospitality_banner_info_lang.Titolo,
                            hospitality_banner_info_lang.Descrizione
                        FROM
                            hospitality_banner_info
                        INNER JOIN
                            hospitality_banner_info_lang
                        ON
                            hospitality_banner_info.Id =  hospitality_banner_info_lang.IdBannerInfo
                        WHERE
                            hospitality_banner_info_lang.idsito = :idsito
                        AND
                            hospitality_banner_info_lang.Lingua = :Lingua
                        AND
                            hospitality_banner_info.Abilitato = :Abilitato";
            $res = DB::select($sel, ['idsito' => $idsito, 'Lingua' => $Lingua, 'Abilitato' => 1]);
            $tot      = sizeof($res);
          if($tot > 0){
              $row  = $res[0];
                  
              $output .= '<link rel="stylesheet" type="text/css" href="/checkin/css/stylebanner_modale.css" />'."\r\n";
              $output .= '<!-- INIZIO BANNER -->
                              <div id="bannerC">
                                  <div class="bannerOC"></div>
                                  <img src="/checkin/images/icona.png" class="icona">
                                  <a class="white" href="javascript:;" data-bs-toggle="modal" data-bs-target="#ANTICOVIDMOD">'.$row->Titolo.'
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
        
        
              $output .= '<div class="modal fade align_custom" id="ANTICOVIDMOD" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog modal-lg" role="document">
                              <div class="modal-content">
                                <div class="modal-header">
                                <i class="fa-light fa-circle-xmark chiudimodale" class="btn btn-secondary" data-bs-dismiss="modal"></i>
                                </div>
                                <div class="modal-body">
                                ' . ($Logo == '' ? '<i class="fa fa-bed fa-5x fa-fw"></i>' : '<img src="' . config('global.settings.BASE_URL_IMG') . 'uploads/loghi/' . $Logo . '" />') . '
                                <br /><br />
                                <titolo>'.$row->Titolo.'</titolo>
                                <sottotitolo>'.$row->Descrizione.'</sottotitolo>
                                </div>
                              </div>
                            </div>
                      '."\r\n";
              $output .= '<style>
                            .close{
                              color:#000!important;
                              z-index: 9999999999999999!important;
                            }
                            .align_custom{
                              top: 0!important;
                              right: 0!important;
                              bottom: 0!important;
                              left: 0!important;
                            }
                            .modal {
                                position: fixed;
                                top: 20%;
                                right: 0;
                                bottom: 0;
                                left: 0;
                                display: none;
                                z-index: 9999999999999999;
                                width: 100%;
                                height: 100%;
                                overflow: auto;
                                background-color: transparent;
                            }
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
        
                  
          }
          return $output;
        }


        
        /**
         * questionario
         *
         * @param  mixed $template
         * @param  mixed $directory
         * @param  mixed $params
         * @param  mixed $request
         * @return void
         */
        public function questionario($template, $directory, $params, Request $request)
        {
            // Decodifica il parametro params per sicurezza
            $decodedParams = base64_decode($params);
            // Verifica che la stringa sia valida
            if (! $decodedParams || ! str_contains($decodedParams, '_')) {
                abort(404, "Formato URL non valido!!");
            }
            // Suddivisione dei parametri separati da "_"
            $parts = explode('_', $decodedParams);
    
            // Controllo per evitare errori se i parametri non sono nel formato corretto
            if (count($parts) !== 3) {
                abort(404, "Formato URL non valido!!");
            }
    
            list($id_richiesta, $idsito, $tipo) = $parts;
        
            $select = "SELECT hospitality_guest.*
                    FROM hospitality_guest
                    WHERE hospitality_guest.idsito = :idsito
                    AND hospitality_guest.Id = :id_richiesta";
            $sel  = DB::select($select,['idsito' => $idsito, 'id_richiesta' => $id_richiesta]);
            $res = sizeof($sel);
            if(($res)>0){
                $rec           = $sel[0];
                $Lingua        = $rec->Lingua;
                $Nome          = $rec->Nome;
                $Cognome       = $rec->Cognome;
                $Email         = $rec->Email;
                $Cliente       = $Nome.' '.$Cognome;
                $Nprenotazione = $rec->NumeroPrenotazione;
            }
    
            $valori_ctrl_script = '';
            $question           = '';
            ###QUESTIONARIO###
            $questionario = "SELECT hospitality_domande_lingua.domanda,hospitality_domande_lingua.domanda_id
                                FROM hospitality_domande
                                INNER JOIN hospitality_domande_lingua ON hospitality_domande_lingua.domanda_id = hospitality_domande.Id
                                WHERE hospitality_domande.idsito = :idsito
                                AND hospitality_domande_lingua.lingue = :Lingua
                                AND hospitality_domande.Abilitato = :Abilitato
                                ORDER BY hospitality_domande.Ordine ASC";
            $res_quest = DB::select($questionario,['idsito' => $idsito, 'Lingua' => $Lingua, 'Abilitato' => 1]);
            $tot_quest = sizeof($res_quest);
                if($tot_quest > 0){
                    foreach($res_quest as $key => $record){
    
                        $valori_ctrl_script .= 'var checked_recensione_'.$record->domanda_id.' = document.querySelector(\'input[name = "recensione_'.$record->domanda_id.'"]:checked\')'."\r\n";
                        $valori_ctrl_script .= 'if(checked_recensione_'.$record->domanda_id.' == null)   error += "Scegli un valore per: '.$record->domanda.'. \n"'."\r\n";
    
                        $question .=' <div class="row">
                                            <div class="col-md-4" style="white-space:nowrap">
                                            <h3 class="maiuscolo">'.$record->domanda.'</h3>
                                            <input type="hidden" name="id_domanda_'.$record->domanda_id.'" value="'.$record->domanda_id.'">
                                            </div>
                                            <div class="col-md-8 text-right" style="padding-top:10px!important">
                                                <table style="float:right;" cellspacing="10" cellpadding="0">
                                                <tr>
                                                    <td class="tc"><input type="radio" name="recensione_'.$record->domanda_id.'" value="1" required></td>
                                                    <td class="tc"><input type="radio" name="recensione_'.$record->domanda_id.'" value="2" required></td>
                                                    <td class="tc"><input type="radio" name="recensione_'.$record->domanda_id.'" value="3" required></td>
                                                    <td class="tc"><input type="radio" name="recensione_'.$record->domanda_id.'" value="4" required></td>
                                                    <td class="tc"><input type="radio" name="recensione_'.$record->domanda_id.'" value="5" required></td>
                                                </tr>
                                                <tr>
                                                    <td class="tc"><i class="far fa-frown fa-2x fa-fw" style="color:#dd4b39"></i></td>
                                                    <td class="tc"><i class="far fa-meh fa-2x fa-fw" style="color:#ff851b"></i></td>
                                                    <td class="tc"><i class="far fa-grin fa-2x fa-fw" style="color:#f39c12"></i></td>
                                                    <td class="tc"><i class="far fa-smile fa-2x fa-fw" style="color:#00c0ef"></i></td>
                                                    <td class="tc"><i class="far fa-smile-wink fa-2x fa-fw" style="color:#00acc1"></i></td>   
                                                </tr>
                                                </table>
                                        </div>
                                    </div>
                                    <div class="row">
                                            <div class="col-md-12">
                                            <textarea class="input_ground" name="risposta_'.$record->domanda_id.'" style="width:100%;height:80px;padding:10px" placeholder="'.dizionario('LASCIA_COMMENTO').'"></textarea>
                                            </div>
                                    </div>';
                    }
    
                }
    
                $rw = $this->social($idsito);
                if($rw->Facebook!=''){
                    $Facebook   = '<a  href="'.$rw->Facebook.'" target="_blank"><i class="fa-brands fa-facebook-f text-secondary"></i></a>';
                }else{
                    $Facebook   = '';
                }
                if($rw->Twitter!=''){
                    $Twitter    = '<a  href="'.$rw->Twitter.'" target="_blank"><img src="/img/x-twitter.png" style="height:24px;margin-top:-5px"></a>';
                }else{
                    $Twitter   = '';
                }
                if($rw->Instagram!=''){
                    $Instagram    = '<a  href="'.$rw->Instagram.'" target="_blank"><i class="fa-brands fa-instagram text-secondary"></i></a>';
                }else{
                    $Instagram   = '';
                }
                if($rw->Pinterest!=''){
                    $Pinterest    = '<a  href="'.$rw->Pinterest.'" target="_blank"><i class="fa-brands fa-pinterest-p text-secondary"></i></a>' ;
                }else{
                    $Pinterest   = '';
                }

                $row = $this->getCliente($idsito);
         
                $NomeCliente         = $row->nome;
                $EmailCliente        = $row->email;
                $Indirizzo           = $row->indirizzo;
                $Localita            = $row->nome_comune;
                $Cap                 = $row->cap;
                $CIR                 = $row->CIR;
                $CIN                 = $row->CIN;
                if(strstr($row->tel,'+39') || strstr($row->tel,'0039')){
                    $tel             = $row->tel;
                }else{
                    $tel             = '+39 '.$row->tel;
                }
                $Provincia           = $row->sigla_provincia;
                $SitoWeb             = 'https://'.$row->web;
                $Logo                = $row->logo;
                $TagManager          = $row->TagManager;
    
                $cs = "SELECT * FROM hospitality_customer_satisfaction WHERE id_richiesta = :id_richiesta AND data_compilazione != :data_compilazione ";
                $res_cs = db::select($cs,['id_richiesta' => $id_richiesta, 'data_compilazione' => '']);
                $tot_cs = sizeof($res_cs);
    
    
                $codeTagMan = $this->tagManager($idsito,$TagManager);
                $head_tagmanager = $codeTagMan[0];
                $body_tagmanager = $codeTagMan[1];
    
                switch($Lingua){
                    case"it":
                      $fraseChat                      = 'Se hai ancora dubbi Chatta con Noi';
                      $tooltipChat                    = 'Per qualsiasi dubbio chatta diretttamente con noi';
                      $gratis                         = 'Gratis';
                      $gentile                        = 'Gentile';
                      break;
                    case"en":
                      $fraseChat                      = 'If you still have doubts, chat with us';
                      $tooltipChat                    = 'If you have any doubts, chat directly with us';
                      $gratis                         = 'Free';
                      $gentile                        = 'Dear';
                      break;
                    case"fr":
                      $fraseChat                      = 'Si vous avez encore des doutes, discutez avec nous';
                      $tooltipChat                    = 'Si vous avez des doutes, discutez directement avec nous';
                      $gratis                         = 'Gratuit';
                      $gentile                        = 'Bonjour ';
                      break;
                    case"de":
                      $fraseChat                      = 'Wenn Sie immer noch Zweifel haben, chatten Sie mit uns';
                      $tooltipChat                    = 'Wenn Sie Zweifel haben, chatten Sie direkt mit uns';
                      $gratis                         = 'Frei';
                      $gentile                        = 'Hallo ';
                      break;
                   }

                $logoTop = ($Logo == '' ? '<i class="fas fa-bed fa-5x fa-fw"></i>' : '<img src="' . config('global.settings.BASE_URL_IMG') . 'uploads/loghi/' . $Logo . '" style="width:100%;max-width:250px;" class="logo">');
                $logoFooter = ($Logo == '' ? '<i class="fas fa-bed fa-5x fa-fw"></i>' : '<img src="' . config('global.settings.BASE_URL_IMG')  . 'uploads/loghi/' . $Logo . '" width="200px">');
                
                $rec = $this->imageVideo($idsito, $template);
                $imgTop = $rec[0];
  

                return view('pro_template/questionario',
                                                                [
                                                                    'valori_ctrl_script' => $valori_ctrl_script,
                                                                    'question'           => $question,
                                                                    'Indirizzo'          => $Indirizzo,
                                                                    'Localita'           => $Localita,
                                                                    'Provincia'          => $Provincia,
                                                                    'Cap'                => $Cap,
                                                                    'CIR'                => $CIR,
                                                                    'CIN'                => $CIN,
                                                                    'SitoWeb'            => $SitoWeb,
                                                                    'EmailCliente'       => $EmailCliente,
                                                                    'NomeCliente'        => $NomeCliente,
                                                                    'Cliente'            => $Cliente,
                                                                    'Email'              => $Email,
                                                                    'Nome'               => $Nome,
                                                                    'Cognome'            => $Cognome,
                                                                    'Facebook'           => $Facebook,
                                                                    'Twitter'            => $Twitter,
                                                                    'Instagram'          => $Instagram,
                                                                    'Pinterest'          => $Pinterest,
                                                                    'tot_cs'             => $tot_cs,
                                                                    'Lingua'             => $Lingua,
                                                                    'head_tagmanager'    => $head_tagmanager,
                                                                    'body_tagmanager'    => $body_tagmanager,
                                                                    'directory'          => $directory,
                                                                    'params'             => $params,
                                                                    'idsito'             => $idsito,
                                                                    'id_richiesta'       => $id_richiesta,
                                                                    'tel'                => $tel,
                                                                    'gentile'            => $gentile,
                                                                    'logoTop'            => $logoTop,
                                                                    'logoFooter'         => $logoFooter,
                                                                    'imgTop'             => $imgTop,
                                                                    'Nprenotazione'     => $Nprenotazione,
                                                                ]
                                                            );
        }


    /**
     * vaglia
     *
     * @param  mixed $idsito
     * @param  mixed $Lingua
     * @param  mixed $id_richiesta
     * @return void
     */
    public function vaglia($idsito,$Lingua,$id_richiesta)
    {
        $vaglia_posta = '';
        $array_pag     = $this->chek_pagamento_altro($idsito,$id_richiesta);
        $tot_pag_check = $array_pag[0];
        $TipoPagamento = $array_pag[1];
        $tot_cc_check  = $this->chek_pagamento_cc($idsito,$id_richiesta);

        switch($Lingua){
            case"it":
              $CAMBIA_VAGLIA        =  'Cambia il pagamento con Vaglia';
            break;
            case"en":
              $CAMBIA_VAGLIA        =  'Change the payment with Postal Order';
            break;
            case"fr":
              $CAMBIA_VAGLIA        =  'Changer le paiement avec Postal Order';
            break;
            case"de":
              $CAMBIA_VAGLIA        =  'Ändern Sie die Zahlung mit Postanweisung';
            break;
          }
        #### VAGLIA ####
        $vp = "SELECT * FROM hospitality_tipo_pagamenti WHERE idsito = :idsito AND Abilitato = :Abilitato  AND TipoPagamento = :TipoPagamento";
        $res_vp = DB::select($vp,['idsito' => $idsito, 'TipoPagamento' => 'Vaglia Postale', 'Abilitato' => 1]);
        $tot_vp = sizeof($res_vp);
        if($tot_vp > 0){
            $row_vp = $res_vp[0];
            $OrdineVP = $row_vp->Ordine;

            $v = "SELECT * FROM hospitality_tipo_pagamenti_lingua WHERE idsito = :idsito AND pagamenti_id = :pagamenti_id AND lingue = :lingue";
            $res_v = DB::select($v,['idsito' => $idsito, 'pagamenti_id' => $row_vp->Id, 'lingue' => $Lingua]);
            $row_v = $res_v[0];
            $Pagamento_vp = $row_v->Pagamento;
            $Descrizione_vp = $row_v->Descrizione;

            $ACCONTO = dizionario('ACCONTO');

            $datiG = Session::get('dati_h_guest', []);
            $AccontoRichiesta = $datiG->AccontoRichiesta;
            $AccontoLibero    = $datiG->AccontoLibero;

            $datiP = Session::get('dati_p_guest', []);
            $AccontoPercentuale = $datiP->AccontoPercentuale;
            $AccontoImporto     = $datiP->AccontoImporto;
            $PrezzoPC           = $datiP->PrezzoP;

            $vaglia_posta .= ' <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12"><div class="card min-height-card-pay m-1 col-eq-height"><div class="card-body">
                                <h4><b>'.$Pagamento_vp.'</b></h4>
                                <span class="text16">'.$Descrizione_vp.'</span><br><br>';

                                        if($AccontoRichiesta != 0 && $AccontoLibero == 0) {
                                            $vaglia_posta .= '<b>'.$ACCONTO.'</b>: '.$AccontoRichiesta.' %  - <b class="text30 text-red">€. '.number_format(($PrezzoPC*$AccontoRichiesta/100),2,',','.').'</b><br><br>';
                                        }
                                        if($AccontoPercentuale != 0 && $AccontoImporto == 0) {
                                            $vaglia_posta .= '<b>'.$ACCONTO.'</b>: '.$AccontoPercentuale.' %  - <b class="text30 text-red">€. '.number_format(($PrezzoPC*$AccontoPercentuale/100),2,',','.').'</b><br><br>';
                                        }
                                        if($AccontoRichiesta == 0 && $AccontoLibero != 0) {
                                            $vaglia_posta .= '<b>'.$ACCONTO.'</b>:  <b class="text30 text-red">€. '.number_format($AccontoLibero,2,',','.').'</b><br><br>';
                                        }
                                        if($AccontoPercentuale == 0 && $AccontoImporto != 0) {
                                            $vaglia_posta .= '<b>'.$ACCONTO.'</b>:  <b class="text30 text-red">€. '.number_format($AccontoImporto,2,',','.').'</b><br><br>';
                                        }

            $vaglia_posta .= '      
                                    <div id="response_vp"></div>
                                    <form  method="POST" id="form_vaglia" name="form_vaglia">
                                            <input type="hidden" name="id_richiesta" value="'.$id_richiesta.'">
                                            <input type="hidden" name="idsito" value="'.$idsito.'">
                                            <input type="hidden" name="TipoPagamento" value="Vaglia Postale">
                                            <input type="hidden" name="action" value="add_payment">
                                            <input name="vg_policy" id="vg_policy" type="radio" value="1" required oninvalid="this.setCustomValidity(\'Questo campo è obbligatorio\')" onchange="this.setCustomValidity(\'\')">
                                            <label for="vg_policy" class="control-label f-12">'.dizionario('ACCETTO_POLITICHE').' <small>(<a href="#condizioni">'.dizionario('LEGGI_POLITICHE').'</a>)</small></label>
                                            <div class="clearfix"></div>';
                        if($tot_pag_check == 0 && $tot_cc_check == 0){
                            $vaglia_posta .='<button type="submit" class="pulsante p-2 noBorder" id="bottone_vaglia" >'.dizionario('SCELGO_VAGLIA').'</button>';
                        }elseif($tot_pag_check > 0 && $tot_cc_check == 0){
                        if($TipoPagamento == 'Vaglia Postale'){
                            $vaglia_posta .= '<span class="text-green">'.dizionario('PAGAMENTOSCELTO').' Vaglia Postale</span>';
                        }else{
                            $vaglia_posta .= '<span class="text-red">'.dizionario('PROPOSTAPAGAMENTOSCELTO').'</span>';
                            $vaglia_posta .='<br><button type="submit" class="pulsante p-2 noBorder" id="bottone_vaglia" >'.$CAMBIA_VAGLIA.'</button>';
                        }
                        }elseif($tot_pag_check == 0 && $tot_cc_check > 0){
                            $vaglia_posta .= '<span class="text-red">'.dizionario('PROPOSTAPAGAMENTOSCELTO').'</span>';
                            $vaglia_posta .='<br><button type="submit" class="pulsante p-2 noBorder" id="bottone_vaglia" >'.$CAMBIA_VAGLIA.'</button>';
                        }


            $vaglia_posta .= '  </form>
                                        <script>
                                            $(document).ready(function() {
                                                $("#form_vaglia").submit(function(){

                                                    var dati = $("#form_vaglia").serialize();
                                                        $.ajaxSetup({
                                                            headers: {
                                                                \'X-CSRF-TOKEN\': $(\'meta[name="csrf-token"]\').attr(\'content\')
                                                            }
                                                        });
                                                        $.ajax({
                                                            url: "/salva_pagamento",
                                                            type: "POST",
                                                            data: dati,
                                                            success: function(res){
                                                                _alert("'.dizionario('SCELGO_VAGLIA').'","'.dizionario('MSG_VAGLIA').'<br>'.dizionario('SCELTAPROPOSTA2').'");
                                                                setTimeout(function(){
                                                                        $("#form_vaglia").fadeOut();
                                                                    },1000);
                                                            },
                                                            error: function(){
                                                                alert("Chiamata fallita, si prega di riprovare...");
                                                            }
                                                            });
                                                        return false; 
                                                });
                                            });
                                        </script>';
                $vaglia_posta .= '</div></div></div>';
        }

        return $vaglia_posta;
    }    
    
     /**
     * bonifico
     *
     * @param  mixed $idsito
     * @param  mixed $Lingua
     * @param  mixed $id_richiesta
     * @return void
     */
    public function bonifico($idsito,$Lingua,$id_richiesta)
    {
        $bonifico_bancario = '';
        $array_pag     = $this->chek_pagamento_altro($idsito,$id_richiesta);
        $tot_pag_check = $array_pag[0];
        $TipoPagamento = $array_pag[1];
        $tot_cc_check  = $this->chek_pagamento_cc($idsito,$id_richiesta);

        switch($Lingua){
            case"it":
              $CAMBIA_BONIFICO      =  'Cambia il pagamento con Bonifico';
            break;
            case"en":
              $CAMBIA_BONIFICO      =  'Change the payment by bank transfer ';
            break;
            case"fr":
              $CAMBIA_BONIFICO      =  'Modifier le paiement par virement bancaire';
            break;
            case"de":
              $CAMBIA_BONIFICO      =  'Ändern Sie die Zahlung per Banküberweisung';
            break;
          }
        #### BONIFICO ####
        $bn = "SELECT * FROM hospitality_tipo_pagamenti WHERE idsito = :idsito AND Abilitato = :Abilitato  AND TipoPagamento = :TipoPagamento";
        $res_bn = DB::select($bn,['idsito' => $idsito,'Abilitato' => 1,'TipoPagamento' => 'Bonifico Bancario']);
        $tot_bn = sizeof($res_bn);
        if($tot_bn > 0){
            $row_bn = $res_bn[0];
            $OrdineBN = $row_bn->Ordine;

            $b = "SELECT * FROM hospitality_tipo_pagamenti_lingua WHERE idsito = :idsito AND pagamenti_id = :pagamenti_id AND lingue = :Lingua";
            $res_b = DB::select($b,['idsito' => $idsito,'pagamenti_id' => $row_bn->Id,'Lingua' => $Lingua]);
            $row_b = $res_b[0];
            $Pagamento_bn = $row_b->Pagamento;
            $Descrizione_bn = $row_b->Descrizione;

            $ACCONTO = dizionario('ACCONTO');

            $datiG = Session::get('dati_h_guest', []);
            $AccontoRichiesta = $datiG->AccontoRichiesta;
            $AccontoLibero    = $datiG->AccontoLibero;

            $datiP = Session::get('dati_p_guest', []);
            $AccontoPercentuale = $datiP->AccontoPercentuale;
            $AccontoImporto     = $datiP->AccontoImporto;
            $PrezzoPC           = $datiP->PrezzoP;

            $bonifico_bancario .= ' <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12"><div class="card min-height-card-pay m-1 col-eq-height"><div class="card-body">
                                    <p><b>'.$Pagamento_bn.'</b></p>
                                    <span class="text16">'.$Descrizione_bn.'</span><br><br>';

                                        if($AccontoRichiesta != 0 && $AccontoLibero == 0) {
                                            $bonifico_bancario .= '<b>'.$ACCONTO.'</b>: '.$AccontoRichiesta.' %  - <b class="text30 text-red">€. '.number_format(($PrezzoPC*$AccontoRichiesta/100),2,',','.').'</b><br><br>';
                                        }
                                        if($AccontoPercentuale != 0 && $AccontoImporto == 0) {
                                            $bonifico_bancario .= '<b>'.$ACCONTO.'</b>: '.$AccontoPercentuale.' %  - <b class="text30 text-red">€. '.number_format(($PrezzoPC*$AccontoPercentuale/100),2,',','.').'</b><br><br>';
                                        }
                                        if($AccontoRichiesta == 0 && $AccontoLibero != 0) {
                                            $bonifico_bancario .= '<b>'.$ACCONTO.'</b>:  <b class="text30 text-red">€. '.number_format($AccontoLibero,2,',','.').'</b><br><br>';
                                        }
                                        if($AccontoPercentuale == 0 && $AccontoImporto != 0) {
                                            $bonifico_bancario .= '<b>'.$ACCONTO.'</b>:  <b class="text30 text-red">€. '.number_format($AccontoImporto,2,',','.').'</b><br><br>';
                                        }

            $bonifico_bancario .= ' <div id="response_bf"></div>
                                    <form  method="POST" id="form_bonifico" name="form_bonifico">
                                            <input type="hidden" name="id_richiesta" value="'.$id_richiesta.'">
                                            <input type="hidden" name="idsito" value="'.$idsito.'">
                                            <input type="hidden" name="TipoPagamento" value="Bonifico">
                                            <input type="hidden" name="action" value="add_payment">
                                            <input name="bf_policy" id="bf_policy" type="radio" value="1" required oninvalid="this.setCustomValidity(\'Questo campo è obbligatorio\')" onchange="this.setCustomValidity(\'\')">
                                            <label for="bf_policy" class="control-label f-12">'.dizionario('ACCETTO_POLITICHE').' <small>(<a href="#condizioni">'.dizionario('LEGGI_POLITICHE').'</a>)</small></label>
                                            <div class="clearfix"></div>';

                    if($tot_pag_check== 0 && $tot_cc_check == 0){
                        $bonifico_bancario .='<button type="submit" class="pulsante p-2 noBorder" id="bottone_bonifico" >'.dizionario('SCELGO_BONIFICO').'</button>';
                    }elseif($tot_pag_check > 0 && $tot_cc_check == 0){
                    if($TipoPagamento == 'Bonifico'){
                        $bonifico_bancario .= '<span class="text-green">'.dizionario('PAGAMENTOSCELTO').' Bonifico Bancario</span>';
                    }else{
                        $bonifico_bancario .= '<span class="text-red">'.dizionario('PROPOSTAPAGAMENTOSCELTO').'</span>';
                        $bonifico_bancario .='<br><button type="submit" class="pulsante p-2 noBorder" id="bottone_bonifico" > '.$CAMBIA_BONIFICO.'</button>';
                    }
                    }elseif($tot_pag_check == 0 && $tot_cc_check > 0){
                        $bonifico_bancario .= '<span class="text-red">'.dizionario('PROPOSTAPAGAMENTOSCELTO').'</span>';
                        $bonifico_bancario .='<br><button type="submit" class="pulsante p-2 noBorder" id="bottone_bonifico" > '.$CAMBIA_BONIFICO.'</button>';
                }

            $bonifico_bancario .= '   </form>
                                        <script>
                                            $(document).ready(function() {
                                                $("#form_bonifico").submit(function(){

                                                    var dati = $("#form_bonifico").serialize();
                                                        $.ajaxSetup({
                                                            headers: {
                                                                \'X-CSRF-TOKEN\': $(\'meta[name="csrf-token"]\').attr(\'content\')
                                                            }
                                                        });
                                                        $.ajax({
                                                            url: "/salva_pagamento",
                                                            type: "POST",
                                                            data: dati,
                                                            success: function(res){
                                                                _alert("'.dizionario('SCELGO_BONIFICO').'","'.dizionario('MSG_BONIFICO').'<br>'.dizionario('SCELTAPROPOSTA2').'");
                                                                setTimeout(function(){
                                                                        $("#form_bonifico").fadeOut();
                                                                    },1000);
                                                            },
                                                            error: function(){
                                                                alert("Chiamata fallita, si prega di riprovare...");
                                                            }
                                                            });
                                                        return false;
                                                });
                                            });
                                        </script>';
                $bonifico_bancario .= '</div></div></div>';
        }
        return $bonifico_bancario;
    }   

    /**
     * carta_credito
     *
     * @param  mixed $idsito
     * @param  mixed $Lingua
     * @param  mixed $id_richiesta
     * @return void
     */
    public function carta_credito($idsito,$Lingua,$id_richiesta)
    {
        $carte_credito = '';
        $array_pag     = $this->chek_pagamento_altro($idsito,$id_richiesta);
        $tot_pag_check = $array_pag[0];
        $TipoPagamento = $array_pag[1];
        $tot_cc_check  = $this->chek_pagamento_cc($idsito,$id_richiesta);

        switch($Lingua){
            case"it":
                $CAMBIA_CARTA_CREDITO =  'Cambia il pagamento con Carta di Credito ';
            break;
            case"en":
                $CAMBIA_CARTA_CREDITO =  'Change your credit card payment ';
            break;
            case"fr":
                $CAMBIA_CARTA_CREDITO =  'Modifier votre paiement par carte de crédit ';
            break;
            case"de":
                $CAMBIA_CARTA_CREDITO =  'Ändern Sie Ihre Kreditkartenzahlung ';
            break;
        }
        #### CARTA DI CREDITO####
        $cc = "SELECT * FROM hospitality_tipo_pagamenti WHERE idsito = :idsito AND Abilitato = :Abilitato AND TipoPagamento = :TipoPagamento";
        $res_cc = DB::select($cc,['idsito' => $idsito, 'TipoPagamento' => 'Carta di Credito', 'Abilitato' => 1 ]);
        $tot_cc = sizeof($res_cc);
        if($tot_cc > 0){
            $row_cc = $res_cc[0];

            $OrdineCC   = $row_cc->Ordine;
            $mastercard = $row_cc->mastercard;
            $visa       = $row_cc->visa;
            $amex       = $row_cc->amex;
            $diners     = $row_cc->diners;

            $c = "SELECT * FROM hospitality_tipo_pagamenti_lingua WHERE idsito = :idsito AND pagamenti_id = :pagamenti_id AND lingue = :lingue";
            $res_c = DB::select($c,['idsito' => $idsito, 'pagamenti_id' => $row_cc->Id, 'lingue' => $Lingua ]);
            $row_c = $res_c[0];
            $Pagamento_cc = $row_c->Pagamento;
            $Descrizione_cc = $row_c->Descrizione;

            $ACCONTO = dizionario('ACCONTO');

            $datiG = Session::get('dati_h_guest', []);
            $AccontoRichiesta = $datiG->AccontoRichiesta;
            $AccontoLibero    = $datiG->AccontoLibero;

            $datiP = Session::get('dati_p_guest', []);
            $AccontoPercentuale = $datiP->AccontoPercentuale;
            $AccontoImporto     = $datiP->AccontoImporto;
            $PrezzoPC           = $datiP->PrezzoP;

            $carte_credito .= ' <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 "><div class="card min-height-card-pay m-1 col-eq-height"><div class="card-body">
                                <h4><b>'.$Pagamento_cc.'</b></h4>
                                <span class="text16">'.$Descrizione_cc.'</span><br><br>';

                                        if($AccontoRichiesta != 0 && $AccontoLibero == 0) {
                                            $carte_credito .= '<b>'.$ACCONTO.'</b>: '.$AccontoRichiesta.' %  - <b class="t40 tcolor">€. '.number_format(($PrezzoPC*$AccontoRichiesta/100),2,',','.').'</b><br><br>';
                                        }
                                        if($AccontoPercentuale != 0 && $AccontoImporto == 0) {
                                            $carte_credito .= '<b>'.$ACCONTO.'</b>: '.$AccontoPercentuale.' %  - <b class="t40 tcolor">€. '.number_format(($PrezzoPC*$AccontoPercentuale/100),2,',','.').'</b><br><br>';
                                        }
                                        if($AccontoRichiesta == 0 && $AccontoLibero != 0) {
                                            $carte_credito .= '<b>'.$ACCONTO.'</b>:  <b class="t40 tcolor">€. '.number_format($AccontoLibero,2,',','.').'</b><br><br>';
                                        }
                                        if($AccontoPercentuale == 0 && $AccontoImporto != 0) {
                                            if($AccontoImporto >= 1){
                                                $carte_credito .= '<b>'.$ACCONTO.'</b>:  <b class="t40 tcolor">€. '.number_format($AccontoImporto,2,',','.').'</b><br><br>';
                                            }else{
                                                $carte_credito .= '<b>'.dizionario('CARTACREDITOGARANZIA').'</b><br><br>';
                                            }

                                        }

                $carte_credito .= ($amex==1?'<i class="fa fa-cc-amex fa-4x fa-fw text-aqua"></i>':'');
                $carte_credito .= ($diners==1?'<i class="fa fa-cc-diners-club fa-4x fa-fw text-light-blue"></i>':'');
                $carte_credito .= ($mastercard==1?'<i class="fa fa-cc-mastercard fa-4x fa-fw text-orange"></i>&nbsp;':'');
                $carte_credito .= ($visa==1?'<i class="fa fa-cc-visa fa-4x fa-fw text-blue"></i>':'');
            $carte_credito .= ' <br><br>
                                    <div id="response_cc"></div>
                                     <div class="m m-x-12 m-m-12 m-s-12 m-s-ha">
                                        <form  method="POST" id="form_cc" name="form_cc">
                                        <div class="form-g">
                                            <label for="cc-number" class="control-label">'.dizionario('N_CARTA').'<small class="text-muted text-light-blue">[<span class="cc-brand"></span>]</small></label>
                                        <input name="nomecartacc" type="hidden" id="nomecartacc">
                                            <input name="cc_number" id="cc-number" type="tel" class="input-lg form-control cc-number err_cc" autocomplete="cc-number" placeholder="•••• •••• •••• ••••" required>
                                        </div>
                                        <div class="form-g">
                                            <label for="cc-exp" class="control-label">'.dizionario('SCADENZA').'</label>
                                            <input name="cc_expiration" id="cc-exp" type="tel" class="input-lg form-control cc-exp err_cc" autocomplete="cc-exp" placeholder="•• / ••" required>
                                        </div>
                                        <div class="form-g">
                                            <label for="cc-cvc" class="control-label">'.dizionario('CODICE').'</label>
                                            <input name="cc_codice" id="cc-cvc" type="tel" class="input-lg form-control cc-cvc err_cc" autocomplete="off" placeholder="•••" required>
                                        </div>
                                        <div class="form-g">
                                            <label for="numeric" class="control-label">'.dizionario('INTESTATARIO').'</label>
                                            <input name="cc_intestatario" id="numeric" type="text" class="input-lg form-control" required>
                                        </div>
                                        <div class="form-g text14">
                                            <input name="cc_policy" id="cc_policy" type="radio" value="1" required>
                                            <label for="cc_policy" class="control-label f-12">'.dizionario('ACCETTO_POLITICHE').' (<a href="#condizioni">'.dizionario('LEGGI_POLITICHE').'</a>)</label>
                                        </div>
                                        <br><br>
                                            <input type="hidden" name="id_richiesta" value="'.$id_richiesta.'">
                                            <input type="hidden" name="idsito" value="'.$idsito.'">
                                            <input type="hidden" name="action" value="add_carta">';

                    if($tot_cc_check == 0 && $tot_pag_check== 0){
                        $carte_credito .='<button type="submit" class="pulsante p-2 noBorder" id="bottone_cc" disabled>'.dizionario('SALVA_CARTA_CREDITO').'</button>';
                        }elseif($tot_cc_check > 0 && $tot_pag_check == 0){
                            $carte_credito .= '<span class="ext-green">'.dizionario('PAGAMENTOSCELTO').' Carta di Credito</span>';
                        }elseif($tot_cc_check == 0 && $tot_pag_check > 0){
                            $carte_credito .= '<span class="text-red">'.dizionario('PROPOSTAPAGAMENTOSCELTO').'</span>';
                            $carte_credito .='<br><button type="submit" class="pulsante p-2 noBorder" id="bottone_cc" disabled>'.$CAMBIA_CARTA_CREDITO.'</button>';
                    }


            $carte_credito .=' </div>
                                        <h2 class="validation"></h2>
                                        </form>
                                        
                                        <script>
                                            $(document).ready(function() {
                                                $("#form_cc").submit(function(){

                                                    var dati = $("#form_cc").serialize();
                                                        $.ajaxSetup({
                                                            headers: {
                                                                \'X-CSRF-TOKEN\': $(\'meta[name="csrf-token"]\').attr(\'content\')
                                                            }
                                                        });
                                                        $.ajax({
                                                            url: "/salva_carta",
                                                            type: "POST",
                                                            data: dati,
                                                            success: function(res){
                                                                _alert("'.dizionario('SALVA_CARTA_CREDITO').'","'.dizionario('MSG_CARTA').'<br>'.dizionario('SCELTAPROPOSTA2').'");
                                                                setTimeout(function(){
                                                                        $("#form_cc").fadeOut();
                                                                    },1000);
                                                            },
                                                            error: function(){
                                                                alert("Chiamata fallita, si prega di riprovare...");
                                                            }
                                                            });
                                                        return false; 
                                                });
                                            });
                                        </script>';
                $carte_credito .= '</div></div></div>';
                $carte_credito .= ' <script src="/js/jquery.payment.min.js"></script>
                                    <style type="text/css" media="screen">
                                        .has-error input {
                                        border-width: 4px;
                                        border-color:#FF0000!important;
                                        border: dotted;
        
                                        }
        
                                        .validation.text-danger:after {
                                        content: \'Validation failed\';
                                        }
        
                                        .validation.text-success:after {
                                        content: \'Validation passed\';
                                        }
        
                                    </style>
        
                                    <script>
                                        jQuery(function($) {
                                        $(\'.cc-number\').payment(\'formatCardNumber\');
                                        $(\'.cc-exp\').payment(\'formatCardExpiry\');
                                        $(\'.cc-cvc\').payment(\'formatCardCVC\');
        
                                        $.fn.toggleInputError = function(erred) {
                                            this.parent(\'.form-g\').toggleClass(\'has-error\', erred);
                                            return this;
                                        };
        
                                        $(\'.cc-cvc\').keyup(function(e) {
        
        
                                            var cardType = $.payment.cardType($(\'.cc-number\').val());
                                            $(\'.cc-number\').toggleInputError(!$.payment.validateCardNumber($(\'.cc-number\').val()));
                                            $(\'.cc-exp\').toggleInputError(!$.payment.validateCardExpiry($(\'.cc-exp\').payment(\'cardExpiryVal\')));
                                            $(\'.cc-cvc\').toggleInputError(!$.payment.validateCardCVC($(\'.cc-cvc\').val(), cardType));
                                            $(\'.cc-brand\').text(cardType);
                                            $(\'#nomecartacc\').val(cardType);
        
                                            $(\'.validation\').removeClass(\'text-danger text-success\');
                                            $(\'.validation\').addClass($(\'.has-error\').length ? \'text-danger\' : \'text-success\');
                                            if(!$(\'.has-error\').length){
                                            $(\'#bottone_cc\').removeAttr(\'disabled\');
                                            }
                                        });
        
                                        });
                                    </script>';
        }
        return $carte_credito;
    }

    /**
     * paypal
     *
     * @param  mixed $idsito
     * @param  mixed $Lingua
     * @param  mixed $id_richiesta
     * @param  mixed $request
     * @return void
     */
    public function paypal($idsito,$Lingua,$id_richiesta, Request $request)
    {
        $paypal = '';
        $array_pag     = $this->chek_pagamento_altro($idsito,$id_richiesta);
        $tot_pag_check = $array_pag[0];
        $TipoPagamento = $array_pag[1];
        $tot_cc_check  = $this->chek_pagamento_cc($idsito,$id_richiesta);

        switch($Lingua){
            case"it":
                $CAMBIA_PAYPAL        =  'Cambia il pagamento con PayPal';
            break;
            case"en":
                $CAMBIA_PAYPAL        =  'Change payment with PayPal';
            break;
            case"fr":
                $CAMBIA_PAYPAL        =  'Modifier le paiement avec PayPal';
            break;
            case"de":
                $CAMBIA_PAYPAL        =  'Zahlung mit PayPal ändern';
            break;
        }
        ### PAYPAL####
        $pp = "SELECT * FROM hospitality_tipo_pagamenti WHERE idsito = :idsito AND Abilitato = :Abilitato  AND TipoPagamento = :TipoPagamento";
        $res_pp = DB::select($pp,['idsito' => $idsito, 'TipoPagamento' => 'PayPal', 'Abilitato' => 1 ]);
        $tot_pp = sizeof($res_pp);

        if($tot_pp > 0){
            $row_pp = $res_pp[0];

            $OrdinePP    = $row_pp->Ordine;
            $EmailPayPal = $row_pp->EmailPayPal;

            $p = "SELECT * FROM hospitality_tipo_pagamenti_lingua WHERE idsito = :idsito AND pagamenti_id = :pagamenti_id AND lingue = :lingue";
            $res_p = DB::select($p,['idsito' => $idsito, 'pagamenti_id' => $row_pp->Id, 'lingue' => $Lingua]);
            $row_p = $res_p[0];
            $Pagamento_pp = $row_p->Pagamento;
            $Descrizione_pp = $row_p->Descrizione;

            $ACCONTO = dizionario('ACCONTO');

            $datiG = Session::get('dati_h_guest', []);
            $AccontoRichiesta   = $datiG->AccontoRichiesta;
            $AccontoLibero      = $datiG->AccontoLibero;
            $NumeroPrenotazione = $datiG->NumeroPrenotazione;
            $Nome               = $datiG->Nome;
            $Cognome            = $datiG->Cognome;
            $Email              = $datiG->Email;

            $datiP = Session::get('dati_p_guest', []);
            $AccontoPercentuale = $datiP->AccontoPercentuale;
            $AccontoImporto     = $datiP->AccontoImporto;
            $PrezzoPC           = $datiP->PrezzoP;
            
            $paypal .= '<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 "><div class="card min-height-card-pay m-1 col-eq-height"><div class="card-body">
                        <h4><b>'.$Pagamento_pp.'</b></h4>
                        <span class="text16">'.$Descrizione_pp.'</span><br><br>
                                <form method="POST" name="paypal_form" id="paypal_form" action="'.config('global.settings.URL_PAYPAL').'">
                                    <input type="hidden" name="business" value="'.$EmailPayPal.'" />
                                        <input type="hidden" name="cmd" value="_xclick" />
                                        <input type="hidden" name="return" value="'.env('APP_URL').''.session('DIRECTORY').'/'.session('PARAM').'/index?result=cGF5cGFs" />
                                        <input type="hidden" name="cancel_return" value="'.env('APP_URL').''.session('DIRECTORY').'/'.session('PARAM').'/index" />
                                        <input type="hidden" name="notify_url" value="'.env('APP_URL').'reg_payment" />
                                        <input type="hidden" name="rm" value="2" />
                                        <input type="hidden" name="currency_code" value="EUR" />
                                        <input type="hidden" name="lc" value="'.strtoupper($Lingua).'" />
                                        <input type="hidden" name="cs" value="0" />
                                        <input type="hidden" name="item_name" value="'.dizionario('OFFERTA').' nr. '.$NumeroPrenotazione.' | '.session('NomeCliente').'" />
                                        <input type="hidden" name="image_url" value="'.config('global.settings.BASE_URL_IMG').'uploads/loghi_siti/'.session('LOGO').'">

                                        <input type="hidden" name="item_number" value="'.$NumeroPrenotazione.'#'.$idsito.'#'.$id_richiesta.'" />

                                        <input type="hidden" name="first_name" value="'.$Nome.'" />
                                        <input type="hidden" name="last_name" value="'.$Cognome.'" />
                                        <input type="hidden" name="country" value="'.strtoupper($Lingua).'" />
                                        <input type="hidden" name="email" value="'.$Email.'" />
                                        <input type="hidden" name="_token" value="'.csrf_token().'" />';

                                    if($AccontoRichiesta != 0 && $AccontoLibero == 0) {
                                        $paypal .= '<b>'.$ACCONTO.'</b>: '.$AccontoRichiesta.' %  - <b class="t40 tcolor">€. '.number_format(($PrezzoPC*$AccontoRichiesta/100),2,',','.').'</b><br><br>';
                                        $paypal .= '<input type="hidden" name="amount" value="'.number_format(($PrezzoPC*$AccontoRichiesta/100) ,2,'.','').'" />';
                                    }
                                    if($AccontoPercentuale != 0 && $AccontoImporto == 0) {
                                        $paypal .= '<b>'.$ACCONTO.'</b>: '.$AccontoPercentuale.' %  - <b class="t40 tcolor">€. '.number_format(($PrezzoPC*$AccontoPercentuale/100),2,',','.').'</b><br><br>';
                                        $paypal .= '<input type="hidden" name="amount" value="'.number_format(($PrezzoPC*$AccontoPercentuale/100) ,2,'.','').'" />';
                                    }
                                    if($AccontoRichiesta == 0 && $AccontoLibero != 0) {
                                        $paypal .= '<b>'.$ACCONTO.'</b>:  <b class="t40 tcolor">€. '.number_format($AccontoLibero,2,',','.').'</b><br><br>';
                                        $paypal .= '<input type="hidden" name="amount" value="'.number_format($AccontoLibero ,2,'.','').'" />';
                                    }
                                    if($AccontoPercentuale == 0 && $AccontoImporto != 0) {
                                        if($AccontoImporto >= 1) {
                                            $paypal .= '<b>'.$ACCONTO.'</b>:  <b class="t40 tcolor">€. '.number_format($AccontoImporto,2,',','.').'</b><br><br>';
                                            $paypal .= '<input type="hidden" name="amount" value="'.number_format($AccontoImporto ,2,'.','').'" />';
                                        }
                                    }

                    $paypal .= ' <label class="control-label f-12">
                                <input name="pp_policy" id="pp_policy" type="radio" value="1" required oninvalid="this.setCustomValidity(\'Questo campo è obbligatorio\')" onchange="this.setCustomValidity(\'\')" />
                                '.dizionario('ACCETTO_POLITICHE').' <small>(<a href="#condizioni">'.dizionario('LEGGI_POLITICHE').'</a>)</small>
                                </label>
                                <div class="clearfix"></div>';
                    if($EmailPayPal !=''){
                            $paypal .= ' <img src="/img/paypal.png" class="img-responsive" style="width:25%" />
                                        <div class="clearfix"></div>';
                            if($tot_cc_check == 0 && $tot_pag_check== 0){
                                $paypal .= '<button type="submit" class="pulsante p-2 noBorder"><i class="fab fa-paypal fa-lg"></i> '.dizionario('PAGA_PAYPAL').'</button>';
                            }elseif($tot_pag_check > 0 && $tot_cc_check == 0){
                            if($TipoPagamento == 'PayPal'){
                                $paypal .= '<span class="text-green">'.dizionario('PAGAMENTOSCELTO').' PayPal</span>';
                            }else{
                                $paypal .= '<span class="text-red">'.dizionario('PROPOSTAPAGAMENTOSCELTO').'</span>';
                                $paypal .= '<br><button type="submit" class="pulsante p-2 noBorder"><i class="fab fa-paypal fa-lg"></i> '.$CAMBIA_PAYPAL.'</button>';
                            }
                            }elseif($tot_pag_check == 0 && $tot_cc_check > 0){
                                $paypal .= '<span class="text-red">'.dizionario('PROPOSTAPAGAMENTOSCELTO').'</span>';
                                $paypal .= '<br><button type="submit" class="pulsante p-2 noBorder"><i class="fab fa-paypal fa-lg"></i> '.$CAMBIA_PAYPAL.'</button>';
                            }
                    }else{
                    $paypal .= '<small class="text-red">Email di riferimento PayPal, non è stata inserita!</small>';
                    }
                    $paypal .= '</form>';
                    $paypal .= '</div></div></div>';
                if($request->result!='' && base64_decode($request->result)=='paypal') {

                    $paypal .= '<script>$("#paypal_form").fadeOut();</script>';

                }

        }
        return $paypal;
    }

    /**
     * gateway_bancario
     *
     * @param  mixed $idsito
     * @param  mixed $Lingua
     * @param  mixed $id_richiesta
     * @param  mixed $request
     * @return void
     */
    public function gateway_bancario($idsito,$Lingua,$id_richiesta, Request $request)
    {
        $gateway_bancario = '';
        $array_pag     = $this->chek_pagamento_altro($idsito,$id_richiesta);
        $tot_pag_check = $array_pag[0];
        $TipoPagamento = $array_pag[1];
        $tot_cc_check  = $this->chek_pagamento_cc($idsito,$id_richiesta);

        switch($Lingua){
            case"it":
                $CAMBIA_CARTA_CREDITO =  'Cambia il pagamento con Carta di Credito ';
            break;
            case"en":
                $CAMBIA_CARTA_CREDITO =  'Change your credit card payment ';
            break;
            case"fr":
                $CAMBIA_CARTA_CREDITO =  'Modifier votre paiement par carte de crédit ';
            break;
            case"de":
                $CAMBIA_CARTA_CREDITO =  'Ändern Sie Ihre Kreditkartenzahlung ';
            break;
        }

        ### GATEWAY BANCARIO ####
        $gb = "SELECT * FROM hospitality_tipo_pagamenti WHERE idsito = :idsito AND Abilitato = :Abilitato  AND TipoPagamento = :TipoPagamento";
        $res_gb = DB::select($gb,['idsito' => $idsito, 'TipoPagamento' => 'Gateway Bancario', 'Abilitato' => 1 ]);
        $tot_gb = sizeof($res_gb);

        if($tot_gb > 0){
            $row_gb = $res_gb[0];


            $OrdineGB   = $row_gb->Ordine;
            $serverURL  = $row_gb->serverURL;
            $tid        = $row_gb->tid;
            $kSig       = $row_gb->kSig;
            $ShopUserRef = $row_gb->ShopUserRef;

            $cgb = "SELECT * FROM hospitality_tipo_pagamenti_lingua WHERE idsito = :idsito AND pagamenti_id = :pagamenti_id AND lingue = :lingue";
            $res_gb = DB::select($cgb,['idsito' => $idsito, 'pagamenti_id' => $row_gb->Id, 'lingue' => $Lingua]);
            $row_gb = $res_gb[0];
            $Pagamento_gb = $row_gb->Pagamento;
            $Descrizione_gb = $row_gb->Descrizione;

            $ACCONTO = dizionario('ACCONTO');

            $datiG = Session::get('dati_h_guest', []);
            $AccontoRichiesta   = $datiG->AccontoRichiesta;
            $AccontoLibero      = $datiG->AccontoLibero;
            $NumeroPrenotazione = $datiG->NumeroPrenotazione;

            $datiP = Session::get('dati_p_guest', []);
            $AccontoPercentuale = $datiP->AccontoPercentuale;
            $AccontoImporto     = $datiP->AccontoImporto;
            $PrezzoPC           = $datiP->PrezzoP;

            $gateway_bancario .= '<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 "><div class="card min-height-card-pay m-1 col-eq-height"><div class="card-body">
                                    <h4><b>'.$Pagamento_gb.'</b></h4>
                                    <span class="text16">'.$Descrizione_gb.'</span><br><br>
                                    <form method="post" name="payway_form" id="payway_form" action="/payway">
                                        <input type="hidden" name="serverURL" value="'.$serverURL.'">
                                        <input type="hidden" name="tid" value="'.$tid.'">
                                        <input type="hidden" name="kSig" value="'.$kSig.'">
                                        <input type="hidden" name="ShopUserRef" value="'.$ShopUserRef.'">
                                        <input type="hidden" name="landID" value="'.strtoupper($Lingua).'" />
                                        <input type="hidden" name="shopID" value="'.$NumeroPrenotazione.'" />
                                        <input type="hidden" name="IdSito" value="'.$idsito.'" />
                                        <input type="hidden" name="IdRichiesta" value="'.$id_richiesta.'" />
                                        <input type="hidden" name="v" value="'.session('PARAM').'" />
                                        <input type="hidden" name="url_back" value="'.env('APP_URL').session('DIRECTORY').'/'.session('PARAM').'/index?result=cGF5d2F5">
                                        <input type="hidden" name="_token" value="'.csrf_token().'" />';

                                    if($AccontoRichiesta != 0 && $AccontoLibero == 0) {
                                        $gateway_bancario .= '<b>'.$ACCONTO.'</b>: '.$AccontoRichiesta.' %  - <b class="text30 text-red">€. '.number_format(($PrezzoPC*$AccontoRichiesta/100),2,',','.').'</b><br><br>';
                                        $gateway_bancario .= '<input type="hidden" name="amount" value="'.(($PrezzoPC*$AccontoRichiesta/100)).'" />';
                                    }
                                    if($AccontoPercentuale != 0 && $AccontoImporto == 0) {
                                        $gateway_bancario .= '<b>'.$ACCONTO.'</b>: '.$AccontoPercentuale.' %  - <b class="text30 text-red">€. '.number_format(($PrezzoPC*$AccontoPercentuale/100),2,',','.').'</b><br><br>';
                                        $gateway_bancario .= '<input type="hidden" name="amount" value="'.(($PrezzoPC*$AccontoPercentuale/100)).'" />';
                                    }
                                    if($AccontoRichiesta == 0 && $AccontoLibero != 0) {
                                        $gateway_bancario .= '<b>'.$ACCONTO.'</b>:  <b class="text30 text-red">€. '.number_format($AccontoLibero,2,',','.').'</b><br><br>';
                                        $gateway_bancario .= '<input type="hidden" name="amount" value="'.($AccontoLibero).'" />';
                                    }
                                    if($AccontoPercentuale == 0 && $AccontoImporto != 0) {
                                        if($AccontoImporto >= 1) {
                                            $gateway_bancario .= '<b>'.$ACCONTO.'</b>:  <b class="text30 text-red">€. '.number_format($AccontoImporto,2,',','.').'</b><br><br>';
                                            $gateway_bancario .= '<input type="hidden" name="amount" value="'.($AccontoImporto).'" />';
                                        }
                                    }

                $gateway_bancario .= '<img src="'.config('global.settings.BASE_URL_IMG').'img/payway_pwsmage.jpg" class="img-responsive" style="width:25%"/>
                                    <div class="clearfix m-2"></div> ';
                $gateway_bancario .= '<label class="control-label f-12">
                                        <input name="gb_policy" id="gb_policy" type="radio" value="1" required oninvalid="this.setCustomValidity(\'Questo campo è obbligatorio\')" onchange="this.setCustomValidity(\'\')" />
                                        '.dizionario('ACCETTO_POLITICHE').' <small>(<a href="#condizioni">'.dizionario('LEGGI_POLITICHE').'</a>)</small>
                                        </label>
                                        <div class="clearfix"></div> ';
            if($kSig !=''){

                if($tot_cc_check == 0 && $tot_pag_check== 0){
                    $gateway_bancario .='<button type="submit" class="pulsante p-2 noBorder">'.dizionario('PAGA_CARTA_CREDITO').' PayWay</button>';
                }elseif($tot_pag_check > 0 && $tot_cc_check == 0){
                    if($TipoPagamento == 'Gateway Bancario'){
                    $gateway_bancario .= '<span class="text-green">'.dizionario('PAGAMENTOSCELTO').' Gateway Bancario PayWay</span>';
                    }else{
                    $gateway_bancario .= '<span class="text-red">'.dizionario('PROPOSTAPAGAMENTOSCELTO').'</span>';
                    $gateway_bancario .='<br><button type="submit" class="pulsante p-2 noBorder">'.$CAMBIA_CARTA_CREDITO.' PayWay</button>';
                    }
                }elseif($tot_pag_check == 0 && $tot_cc_check > 0){
                    $gateway_bancario .= '<span class="text-red">'.dizionario('PROPOSTAPAGAMENTOSCELTO').'</span>';
                    $gateway_bancario .='<br><button type="submit" class="pulsante p-2 noBorder">'.$CAMBIA_CARTA_CREDITO.' PayWay</button>';
                }

            }else{
            $gateway_bancario .= '<small class="text-red">API Key di riferimento PayWay non è stata inserita!</small>';
            }
                $gateway_bancario .= '</form>';
                $gateway_bancario .= '</div></div></div>';
        }
        if($request->result!='' && base64_decode($request->result)=='payway') {

            $sel = 'SELECT TransId FROM hospitality_transazioniId_bcc WHERE id_richiesta = :id_richiesta';
            $qy   = DB::select($sel,['id_richiesta' => $id_richiesta]);
            $recs = sizeof($qy);
            if($recs > 0){
                $rows  = $qy[0];
                $payment_id = $rows->TransId;
            }
            require(public_path('IGFS_CG_API/init/IgfsCgVerify.php'));
            $verify            = new IgfsCgVerify();
            $verify->serverURL = $serverURL; //url test
            $verify->tid       = $tid;
            $verify->kSig      = $kSig;
            $verify->shopID    = $NumeroPrenotazione; // stesso valore della chiamata init
            $verify->paymentID = $payment_id;// NOTA: Leggo il paymentID rilasciato in fase di init es da database
            $verify->execute();

            //il webservice ha risposto
            if($verify->rc == 'IGFS_000' || $verify->rc == 'IGFS_909'){
            // TUTTO OK

                $se = 'SELECT * FROM hospitality_altri_pagamenti WHERE id_richiesta = :id_richiesta';
                $q   = DB::select($se,['id_richiesta' => $id_richiesta]);
                $rec = sizeof($q);
                
                if($rec == 0){

                        DB::table('hospitality_altri_pagamenti')->insert(
                                                                            [
                                                                                'idsito'           => $idsito,
                                                                                'id_richiesta'     => $id_richiesta,
                                                                                'TipoPagamento'    => 'Gateway Bancario',
                                                                                'CRO'              => $payment_id,
                                                                                'data_inserimento' => date('Y-m-d')
                                                                            ]
                                                                        );
                }else{
                    $row = $q[0];
                    if($row->TipoPagamento!= 'Gateway Bancario'){
                        DB::table('hospitality_altri_pagamenti')->where('Id','=',$row->Id)->where('id_richiesta','=',$id_richiesta)->update(
                                                                                                                                                [
                                                                                                                                                    'TipoPagamento'    => 'Gateway Bancario',
                                                                                                                                                    'CRO'              => $payment_id,
                                                                                                                                                    'data_inserimento' => date('Y-m-d')
                                                                                                                                                ]
                                                                                                                                            );
                    }
                }

            }else{
                $gateway_bancario .= $verify->rc.' '.$verify->errorDesc;
            }
        }
        return $gateway_bancario;
    }


    /**
     * gateway_bancario_virtualpay
     *
     * @param  mixed $idsito
     * @param  mixed $Lingua
     * @param  mixed $id_richiesta
     * @param  mixed $request
     * @return void
     */
    public function gateway_bancario_virtualpay($idsito,$Lingua,$id_richiesta, Request $request)
    {
        $gateway_bancario_virtualpay = '';
        $array_pag     = $this->chek_pagamento_altro($idsito,$id_richiesta);
        $tot_pag_check = $array_pag[0];
        $TipoPagamento = $array_pag[1];
        $tot_cc_check  = $this->chek_pagamento_cc($idsito,$id_richiesta);

        switch($Lingua){
            case"it":
                $CAMBIA_CARTA_CREDITO =  'Cambia il pagamento con Carta di Credito ';
            break;
            case"en":
                $CAMBIA_CARTA_CREDITO =  'Change your credit card payment ';
            break;
            case"fr":
                $CAMBIA_CARTA_CREDITO =  'Modifier votre paiement par carte de crédit ';
            break;
            case"de":
                $CAMBIA_CARTA_CREDITO =  'Ändern Sie Ihre Kreditkartenzahlung ';
            break;
        }

        ### GATEWAY BANCARIO VIRTUAL PAY####
        $gbvp = "SELECT * FROM hospitality_tipo_pagamenti WHERE idsito = :idsito AND Abilitato = :Abilitato  AND TipoPagamento = :TipoPagamento";
        $res_gbvp = DB::select($gbvp,['idsito' => $idsito, 'TipoPagamento' => 'Gateway Bancario Virtual Pay', 'Abilitato' => 1 ]);
        $tot_gbvp = sizeof($res_gbvp);

        if($tot_gbvp > 0){
            $row_gbvp = $res_gbvp[0];


            $OrdineGBVP  = $row_gbvp->Ordine;
            $URL         = $row_gbvp->serverURL;
            $ABI         = $row_gbvp->tid;
            $MERCHANT_ID = $row_gbvp->kSig;
            $EMAIL       = $row_gbvp->ShopUserRef;

            $cgbvp            = "SELECT * FROM hospitality_tipo_pagamenti_lingua WHERE idsito = :idsito AND pagamenti_id = :pagamenti_id AND lingue = :lingue";
            $res_cgbvp        = DB::select($cgbvp,['idsito' => $idsito, 'pagamenti_id' => $row_gbvp->Id, 'lingue' => $Lingua]);
            $row_cgbvp        = $res_cgbvp[0];
            $Pagamento_gbvp   = $row_cgbvp->Pagamento;
            $Descrizione_gbvp = $row_cgbvp->Descrizione;

            $KEY = config('global.settings.KEY_VIRTUALPAY');

            $ACCONTO = dizionario('ACCONTO');

            $datiG = Session::get('dati_h_guest', []);
            $AccontoRichiesta   = $datiG->AccontoRichiesta;
            $AccontoLibero      = $datiG->AccontoLibero;
            $NumeroPrenotazione = $datiG->NumeroPrenotazione;

            $datiP = Session::get('dati_p_guest', []);
            $AccontoPercentuale = $datiP->AccontoPercentuale;
            $AccontoImporto     = $datiP->AccontoImporto;
            $PrezzoPC           = $datiP->PrezzoP;

            $gateway_bancario_virtualpay .= '<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 "><div class="card min-height-card-pay m-1 col-eq-height"><div class="card-body">
                                            <h4><b>'.$Pagamento_gbvp.'</b></h4>
                                            <span class="text16">'.$Descrizione_gbvp.'</span><br><br>
                                            <form method="post" name="SENDORDINE" id="virtualpay_form" action="'.$URL.'">
                                            <input type="hidden" name="DIVISA" value="EUR">
                                            <input type="hidden" name="ABI" value="'.$ABI.'">
                                            <input type="hidden" name="MERCHANT_ID" value="'.$MERCHANT_ID.'">
                                            <input type="hidden" name="EMAIL" value="'.$EMAIL.'">
                                            <input type="hidden" name="LINGUA" value="'.strtoupper($Lingua).'" />
                                            <input type="hidden" name="ORDER_ID" value="'.$NumeroPrenotazione.'" />
                                            <input type="hidden" name="URLOK" value="'.env('APP_URL').'virtualpayOK?v='.session('PARAM').'&dir='.session('DIRECTORY').'">
                                            <input type="hidden" name="URLKO" value="'.env('APP_URL').'virtualpayKO?v='.session('PARAM').'&dir='.session('DIRECTORY').'">
                                            <input type="hidden" name="_token" value="'.csrf_token().'" />';

                                            if($AccontoRichiesta != 0 && $AccontoLibero == 0) {
                                                $gateway_bancario_virtualpay .= '<b>'.$ACCONTO.'</b>: '.$AccontoRichiesta.' %  - <b class="text30 text-red">€. '.number_format(($PrezzoPC*$AccontoRichiesta/100),2,',','.').'</b><br><br>';
                                                $importo = number_format(($PrezzoPC*$AccontoRichiesta/100) ,2,',','');
                                                $gateway_bancario_virtualpay .= '<input type="hidden" name="IMPORTO" value="'.$importo.'" />';
                                                $gateway_bancario_virtualpay .= '<input type="hidden" name="ITEMS" value="'.$NumeroPrenotazione.'^Prenotazione soggiorno^1^'.$importo.'">';
                                                $CALCMAC = $MERCHANT_ID.$NumeroPrenotazione.$importo.'EUR'.$ABI.$NumeroPrenotazione.'^Prenotazione soggiorno^1^'.$importo.''.$KEY;
                                                $CALCMAC_TMP = hash('sha256',$CALCMAC);
                                                $MAC = strtoupper($CALCMAC_TMP);
                                                $gateway_bancario_virtualpay .= '<input type="hidden" name="MAC" value="'.$MAC.'">';

                                            }
                                            if($AccontoPercentuale != 0 && $AccontoImporto == 0) {
                                                $gateway_bancario_virtualpay .= '<b>'.$ACCONTO.'</b>: '.$AccontoPercentuale.' %  - <b class="text30 text-red">€. '.number_format(($PrezzoPC*$AccontoPercentuale/100),2,',','.').'</b><br><br>';
                                                $importo = number_format(($PrezzoPC*$AccontoPercentuale/100) ,2,',','');
                                                $gateway_bancario_virtualpay .= '<input type="hidden" name="IMPORTO" value="'.$importo.'" />';
                                                $gateway_bancario_virtualpay .= '<input type="hidden" name="ITEMS" value="'.$NumeroPrenotazione.'^Prenotazione soggiorno^1^'.$importo.'">';
                                                $CALCMAC = $MERCHANT_ID.$NumeroPrenotazione.$importo.'EUR'.$ABI.$NumeroPrenotazione.'^Prenotazione soggiorno^1^'.$importo.$KEY;
                                                $CALCMAC_TMP = hash('sha256',$CALCMAC);
                                                $MAC = strtoupper($CALCMAC_TMP);
                                                $gateway_bancario_virtualpay .= '<input type="hidden" name="MAC" value="'.$MAC.'">';
                                            }
                                            if($AccontoRichiesta == 0 && $AccontoLibero != 0) {
                                                $gateway_bancario_virtualpay .= '<b>'.$ACCONTO.'</b>:  <b class="text30 text-red">€. '.number_format($AccontoLibero,2,',','.').'</b><br><br>';
                                                $importo = number_format($AccontoLibero ,2,',','');
                                                $gateway_bancario_virtualpay .= '<input type="hidden" name="IMPORTO" value="'.$importo.'" />';
                                                $gateway_bancario_virtualpay .= '<input type="hidden" name="ITEMS" value="'.$NumeroPrenotazione.'^Prenotazione soggiorno^1^'.$importo.'">';
                                                $CALCMAC = $MERCHANT_ID.$NumeroPrenotazione.$importo.'EUR'.$ABI.$NumeroPrenotazione.'^Prenotazione soggiorno^1^'.$importo.$KEY;
                                                $CALCMAC_TMP = hash('sha256',$CALCMAC);
                                                $MAC = strtoupper($CALCMAC_TMP);
                                                $gateway_bancario_virtualpay .= '<input type="hidden" name="MAC" value="'.$MAC.'">';
                                            }
                                            if($AccontoPercentuale == 0 && $AccontoImporto != 0) {
                                                if($AccontoImporto >= 1) {
                                                    $gateway_bancario_virtualpay .= '<b>'.$ACCONTO.'</b>:  <b class="text30 text-red">€. '.number_format($AccontoImporto,2,',','.').'</b><br><br>';
                                                    $importo = number_format($AccontoImporto ,2,',','');
                                                    $gateway_bancario_virtualpay .= '<input type="hidden" name="IMPORTO" value="'.$importo.'" />';
                                                    $gateway_bancario_virtualpay .= '<input type="hidden" name="ITEMS" value="'.$NumeroPrenotazione.'^Prenotazione soggiorno^1^'.$importo.'">';
                                                    $CALCMAC = $MERCHANT_ID.$NumeroPrenotazione.$importo.'EUR'.$ABI.$NumeroPrenotazione.'^Prenotazione soggiorno^1^'.$importo.$KEY;
                                                    $CALCMAC_TMP = hash('sha256',$CALCMAC);
                                                    $MAC = strtoupper($CALCMAC_TMP);
                                                    $gateway_bancario_virtualpay .= '<input type="hidden" name="MAC" value="'.$MAC.'">';
                                                }else{
                                                    $gateway_bancario_virtualpay .= '<b>'.dizionario('CARTACREDITOGARANZIA').'</b><br><br>';
                                                }
                                            }

            $gateway_bancario_virtualpay .= '<img src="'.config('global.settings.BASE_URL_IMG').'img/virtualpay_form.jpg" class="img-responsive" style="width:25%"/>
                                            <div class="clearfix m-2"></div>  ';

            $gateway_bancario_virtualpay .= '<label class="control-label f-12">
                                                <input name="gbvp_policy" id="gbvp_policy" type="radio" value="1" required />
                                                '.dizionario('ACCETTO_POLITICHE').' <small>(<a href="#condizioni">'.dizionario('LEGGI_POLITICHE').'</a>)</small>
                                            </label>
                                            <div class="clearfix"></div> ';


            if($MERCHANT_ID !=''){

                if($tot_cc_check == 0 && $tot_pag_check== 0){
                    $gateway_bancario_virtualpay .='<button type="submit" class="pulsante p-2 noBorder">'.dizionario('PAGA_CARTA_CREDITO').' Virtual Pay</button>';
                }elseif($tot_pag_check > 0 && $tot_cc_check == 0){
                    if($TipoPagamento == 'Gateway Bancario Virtual Pay'){
                    $gateway_bancario_virtualpay .= '<span class="text-green">'.dizionario('PAGAMENTOSCELTO').' Gateway Bancario Virtual Pay</span>';
                    }else{
                    $gateway_bancario_virtualpay .= '<span class="text-red">'.dizionario('PROPOSTAPAGAMENTOSCELTO').'</span>';
                    $gateway_bancario_virtualpay .='<br><button type="submit" class="pulsante p-2 noBorder">'.$CAMBIA_CARTA_CREDITO.' Virtual Pay</button>';
                    }
                }elseif($tot_pag_check == 0 && $tot_cc_check > 0){
                $gateway_bancario_virtualpay .= '<span class="text-red">'.dizionario('PROPOSTAPAGAMENTOSCELTO').'</span>';
                $gateway_bancario_virtualpay .='<br><button type="submit" class="pulsante p-2 noBorder">'.$CAMBIA_CARTA_CREDITO.' Virtual Pay</button>';
                }

            }else{
                $gateway_bancario_virtualpay .= '<small class="text-red">MERCHANT ID di riferimento Virtual Pay non è stato inserito!</small>';
            }
                $gateway_bancario_virtualpay .= '</form>';
                $gateway_bancario_virtualpay .= '</div></div></div>';

        }
        return $gateway_bancario_virtualpay;
    }

    
    /**
     * stripe
     *
     * @param  mixed $idsito
     * @param  mixed $Lingua
     * @param  mixed $id_richiesta
     * @param  mixed $request
     * @return void
     */
    public function stripe($idsito,$Lingua,$id_richiesta, Request $request)
    {
        $stripe = '';
        $array_pag     = $this->chek_pagamento_altro($idsito,$id_richiesta);
        $tot_pag_check = $array_pag[0];
        $TipoPagamento = $array_pag[1];
        $tot_cc_check  = $this->chek_pagamento_cc($idsito,$id_richiesta);

        switch($Lingua){
            case"it":
              $CAMBIA_STRIPE        =  'Cambia il pagamento con STRIPE';
            break;
            case"en":
              $CAMBIA_STRIPE        =  'Change payment with STRIPE';
            break;
            case"fr":
              $CAMBIA_STRIPE        =  'Modifier le paiement avec STRIPE';
            break;
            case"de":
              $CAMBIA_STRIPE        =  'Zahlung ändern mit STRIPE';
            break;
          }
            ### STRIPE ####
            $ss = "SELECT * FROM hospitality_tipo_pagamenti WHERE idsito = :idsito AND Abilitato = :Abilitato  AND TipoPagamento = :TipoPagamento";
            $res_ss = DB::select($ss,['idsito' => $idsito, 'TipoPagamento' => 'Stripe', 'Abilitato' => 1 ]);
            $tot_ss = sizeof($res_ss);

            if($tot_ss > 0){
                $row_ss = $res_ss[0];

                $OrdineSS    = $row_ss->Ordine;
                $ApiKeyStripe = $row_ss->ApiKeyStripe;

                $s = "SELECT * FROM hospitality_tipo_pagamenti_lingua WHERE idsito = :idsito AND pagamenti_id = :pagamenti_id AND lingue = :lingue";
                $res_s =  DB::select($s,['idsito' => $idsito, 'pagamenti_id' => $row_ss->Id, 'lingue' => $Lingua]);
                $row_s = $res_s[0];
                $Pagamento_ss = $row_s->Pagamento;
                $Descrizione_ss = $row_s->Descrizione;

                $ACCONTO = dizionario('ACCONTO');

                $datiG = Session::get('dati_h_guest', []);
                $AccontoRichiesta   = $datiG->AccontoRichiesta;
                $AccontoLibero      = $datiG->AccontoLibero;
                $NumeroPrenotazione = $datiG->NumeroPrenotazione;
    
                $datiP = Session::get('dati_p_guest', []);
                $AccontoPercentuale = $datiP->AccontoPercentuale;
                $AccontoImporto     = $datiP->AccontoImporto;
                $PrezzoPC           = $datiP->PrezzoP;

                if($AccontoRichiesta != 0 && $AccontoLibero == 0) {
                    $stripe_txt = '<b>'.$ACCONTO.'</b>: '.$AccontoRichiesta.' %  - <b class="text30 text-red">€. '.number_format(($PrezzoPC*$AccontoRichiesta/100),2,',','.').'</b><br><br>';
                    $stripe_value = number_format(($PrezzoPC*$AccontoRichiesta/100) ,2,'','');
                    }
                    if($AccontoPercentuale != 0 && $AccontoImporto == 0) {
                        $stripe_txt = '<b>'.$ACCONTO.'</b>: '.$AccontoPercentuale.' %  - <b class="text30 text-red">€. '.number_format(($PrezzoPC*$AccontoPercentuale/100),2,',','.').'</b><br><br>';
                        $stripe_value = number_format(($PrezzoPC*$AccontoPercentuale/100) ,2,'','');
                    }
                    if($AccontoRichiesta == 0 && $AccontoLibero != 0) {
                        $stripe_txt = '<b>'.$ACCONTO.'</b>:  <b class="text30 text-red">€. '.number_format($AccontoLibero,2,',','.').'</b><br><br>';
                        $stripe_value = number_format($AccontoLibero ,2,'','');
                    }
                    if($AccontoPercentuale == 0 && $AccontoImporto != 0) {
                        if($AccontoImporto >= 1) {
                            $stripe_txt = '<b>'.$ACCONTO.'</b>:  <b class="text30 text-red">€. '.number_format($AccontoImporto,2,',','.').'</b><br><br>';
                            $stripe_value = number_format($AccontoImporto ,2,'','');
                        }else{
                            $stripe_txt = '<b>'.dizionario('CARTACREDITOGARANZIA').'</b><br><br>';
                        }
                    }

                $stripe .= '<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 "><div class="card min-height-card-pay m-1 col-eq-height"><div class="card-body">
                            <h4><b>'.$Pagamento_ss.'</b></h4>
                            <span class="text16">'.$Descrizione_ss.'</span><br><br>
                            '.$stripe_txt.'
                                <img src="'.config('global.settings.BASE_URL_IMG').'img/stripe.png" class="img-responsive" style="width:25%" />
                                <div class="clearfix m-2"></div> ';

                $stripe .= '
                            <label class="control-label f-12">
                                <input name="ss_policy" id="ss_policy" type="radio" value="1" required oninvalid="this.setCustomValidity(\'Questo campo è obbligatorio\')" onchange="this.setCustomValidity(\'\')" />
                                '.dizionario('ACCETTO_POLITICHE').' <small>(<a href="#condizioni">'.dizionario('LEGGI_POLITICHE').'</a>)</small>
                            </label>
                            <div class="c10"></div>';
                            $selSLink = "SELECT linkStripe FROM hospitality_rel_pagamenti_preventivi WHERE idsito = :idsito AND id_richiesta = :id_richiesta AND GBS = :GBS";
                            $risSLink =  DB::select($selSLink,['idsito' => $idsito, 'id_richiesta' => $id_richiesta, 'GBS' => 1]);
                            if(sizeof($risSLink)>0){
                                $rowSLink = $risSLink[0];
                                if($rowSLink->linkStripe){
                                        if($ApiKeyStripe !=''){
                                                $stripe .= '<div class="clearfix"></div>';
                                                if($tot_cc_check == 0 && $tot_pag_check== 0){
                                                    $stripe .= '<a id="card-button" class="pulsante p-2 noBorder" onclick="check_policy();" target="_blank">Paga con STRIPE </a>';
                                                }elseif($tot_pag_check > 0 && $tot_cc_check == 0){
                                                    if($TipoPagamento == 'Stripe'){
                                                        $stripe .= '<span class="text-green">'.dizionario('PAGAMENTOSCELTO').' Stripe</span>';
                                                    }else{
                                                        $stripe .= '<span class="text-red">'.dizionario('PROPOSTAPAGAMENTOSCELTO').'</span>';
                                                        $stripe .= '<br><button id="card-button" class="pulsante p-2 noBorder" onclick="check_policy();" target="_blank">'.$CAMBIA_STRIPE.' </button>';
                                                    }
                                                }elseif($tot_pag_check == 0 && $tot_cc_check > 0){
                                                    $stripe .= '<span class="text-red">'.dizionario('PROPOSTAPAGAMENTOSCELTO').'</span>';
                                                    $stripe .= '<br><button id="card-button" class="pulsante p-2 noBorder" onclick="check_policy();" target="_blank">'.$CAMBIA_STRIPE.' </button>';
                                                }
                                        }else{
                                            $stripe .= '<small class="text-red">API di riferimento Stripe, non è stata inserita!</small>';
                                        }
                                    }else{
                                        $stripe .= '<small class="text-red">Manca il link creato dalla dashboard di Stripe; non è stato inserito!</small>';
                                    }
                            }

                            $stripe .= ' 
                                <script>
                                    function check_policy(){

                                            if($("#ss_policy").is(":checked")){   
                                                $("#card-button").attr("href","'.$rowSLink->linkStripe.'");  
                                            setTimeout(function(){
                                                    location.href="'.env('APP_URL').session('DIRECTORY').'/'.session('PARAM').'/index/?result=c3RyaXBl";
                                                },1000);
                                                
                                            }else{
                                                alert(\''.dizionario('ACCETTO_POLITICHE').'\');
                                                return false;
                                            }
                                            
                                    }
                                    $(document).ready(function(){
                                        $(".stripe-button-el").attr("style","display:none");
                                    })   
                                </script>
                                <div class="clearfix"></div>';
                    $stripe .='</div></div></div>';

                    if($request->result!='' && base64_decode($request->result)=='stripe') {

                    $stripe .= '<script>$("#card-button").fadeOut();</script>';


                    $stripeToken            = $rowSLink->linkStripe;


                            $se = 'SELECT * FROM hospitality_altri_pagamenti WHERE id_richiesta = :id_richiesta';
                            $q   = DB::select($se,['id_richiesta' => $id_richiesta]);
                            $rec = sizeof($q);
                            if($rec == 0){
                                    DB::table('hospitality_altri_pagamenti')->insert(
                                                                                        [
                                                                                            'idsito'           => $idsito,
                                                                                            'id_richiesta'     => $id_richiesta,
                                                                                            'TipoPagamento'    => 'Stripe',
                                                                                            'CRO'              => $stripeToken,
                                                                                            'data_inserimento' => date('Y-m-d')
                                                                                        ]
                                                                                    );
                            }else{
                                $row = $q[0];
                                if($row->TipoPagamento!= 'Stripe'){
                                    DB::table('hospitality_altri_pagamenti')->where('Id','=',$row->Id)->where('id_richiesta','=',$id_richiesta)->update(
                                                                                                                                                            [
                                                                                                                                                                'TipoPagamento'    => 'Stripe',
                                                                                                                                                                'CRO'              => $stripeToken,
                                                                                                                                                                'data_inserimento' => date('Y-m-d')
                                                                                                                                                            ]
                                                                                                                                                        );
                                }
                            }


                    }

            }

        return $stripe; 
    }
    
    /**
     * nexi
     *
     * @param  mixed $idsito
     * @param  mixed $Lingua
     * @param  mixed $id_richiesta
     * @param  mixed $request
     * @return void
     */
    public function nexi($idsito,$Lingua,$id_richiesta, Request $request)
    {
        $nexi = '';
        $array_pag     = $this->chek_pagamento_altro($idsito,$id_richiesta);
        $tot_pag_check = $array_pag[0];
        $TipoPagamento = $array_pag[1];
        $tot_cc_check  = $this->chek_pagamento_cc($idsito,$id_richiesta);

        switch($Lingua){
            case"it":
              $CAMBIA_NEXI        =  'Cambia il pagamento con NEXI';
            break;
            case"en":
              $CAMBIA_NEXI        =  'Change payment with NEXI';
            break;
            case"fr":
              $CAMBIA_NEXI        =  'Modifier le paiement avec NEXI';
            break;
            case"de":
              $CAMBIA_NEXI        =  'Zahlung ändern mit NEXI';
            break;
          }
        ### NEXI ####
        $nx = "SELECT * FROM hospitality_tipo_pagamenti WHERE idsito = :idsito AND Abilitato = :Abilitato  AND TipoPagamento = :TipoPagamento";
        $res_nx = DB::select($nx,['idsito' => $idsito, 'TipoPagamento' => 'Nexi', 'Abilitato' => 1 ]);
        $tot_nx = sizeof($res_nx);

        if($tot_nx > 0){
            $row_nx = $res_nx[0]; 

            $OrdineNX        = $row_nx->Ordine;
            $ApiKeyNexi      = $row_nx->ApiKeyNexi;
            $SegretKeyNexi   = $row_nx->SegretKeyNexi;
            


            $x = "SELECT * FROM hospitality_tipo_pagamenti_lingua WHERE idsito = :idsito AND pagamenti_id = :pagamenti_id AND lingue = :lingue";
            $res_x = DB::select($x,['idsito' => $idsito, 'pagamenti_id' => $row_nx->Id, 'lingue' => $Lingua]);
            $row_x = $res_x[0];
            $Pagamento_nx   = $row_x->Pagamento;
            $Descrizione_nx = $row_x->Descrizione;

            $ACCONTO = dizionario('ACCONTO');

            $datiG = Session::get('dati_h_guest', []);
            $AccontoRichiesta   = $datiG->AccontoRichiesta;
            $AccontoLibero      = $datiG->AccontoLibero;
            $NumeroPrenotazione = $datiG->NumeroPrenotazione;

            $datiP = Session::get('dati_p_guest', []);
            $AccontoPercentuale = $datiP->AccontoPercentuale;
            $AccontoImporto     = $datiP->AccontoImporto;
            $PrezzoPC           = $datiP->PrezzoP;


            if($AccontoRichiesta != 0 && $AccontoLibero == 0) {
                $nexi_txt = '<b>'.$ACCONTO.'</b>: '.$AccontoRichiesta.' %  - <b class="text30 text-red">€. '.number_format(($PrezzoPC*$AccontoRichiesta/100),2,',','.').'</b><br><br>';
                $nexi_value = number_format(($PrezzoPC*$AccontoRichiesta/100) ,2,'','');
                }
                if($AccontoPercentuale != 0 && $AccontoImporto == 0) {
                    $nexi_txt = '<b>'.$ACCONTO.'</b>: '.$AccontoPercentuale.' %  - <b class="text30 text-red">€. '.number_format(($PrezzoPC*$AccontoPercentuale/100),2,',','.').'</b><br><br>';
                    $nexi_value = number_format(($PrezzoPC*$AccontoPercentuale/100) ,2,'','');
                }
                if($AccontoRichiesta == 0 && $AccontoLibero != 0) {
                    $nexi_txt = '<b>'.$ACCONTO.'</b>:  <b class="text30 text-red">€. '.number_format($AccontoLibero,2,',','.').'</b><br><br>';
                    $nexi_value = number_format($AccontoLibero ,2,'','');
                }
                if($AccontoPercentuale == 0 && $AccontoImporto != 0) {
                    if($AccontoImporto >= 1) {
                        $nexi_txt = '<b>'.$ACCONTO.'</b>:  <b class="text30 text-red">€. '.number_format($AccontoImporto,2,',','.').'</b><br><br>';
                        $nexi_value = number_format($AccontoImporto ,2,'','');
                    }else{
                        $nexi_txt = '<b>'.dizionario('CARTACREDITOGARANZIA').'</b><br><br>';
                    }
                } 

                $nexi .= '<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 "><div class="card min-height-card-pay m-1 col-eq-height"><div class="card-body">
                            <h4><b>'.$Pagamento_nx.'</b></h4>
                            <span class="text16">'.$Descrizione_nx.'</span><br><br>
                            '.$nexi_txt.'
                                <img src="'.config('global.settings.BASE_URL_IMG').'img/LogoNexi_XPay.jpg" class="img-responsive" style="width:25%"/>
                                <div class="clearfix m-2"></div> ';

                $ALIAS = $ApiKeyNexi;
                $CHIAVESEGRETA =   $SegretKeyNexi;


                $nexi .= '  
                                <form method="POST" name="xpay" action="'.config('global.settings.URL_NEXI').'">
                                <input type="hidden" name="_token" value="'.csrf_token().'" />
                                <input name="ne_policy" id="ne_policy" type="radio" value="1" required oninvalid="this.setCustomValidity(\'Questo campo è obbligatorio\')" onchange="this.setCustomValidity(\'\')">
                                <label for="ne_policy" class="control-label f-12">'.dizionario('ACCETTO_POLITICHE').' (<a href="#condizioni">'.dizionario('LEGGI_POLITICHE').'</a>)</label>
                                <div class="ca10"></div> 
                                <div id="politiche_ne" style="display:none">
                                <div class="t14">'.dizionario('INFORMATIVA_PRIVACY').'</div>
                            </div>
                                <div class="ca20"></div> ';

                                $codTrans = $idsito.'_'.$id_richiesta.'_' . date('YmdHis');
                                //$codTrans = "TESTPS_" . date('YmdHis');
                                $divisa = "EUR";
                                $importo = $nexi_value;
                                $merchantServerUrl = env('APP_URL');
                                // Calcolo MAC
                                $mac = sha1('codTrans=' . $codTrans . 'divisa=' . $divisa . 'importo=' . $importo . $CHIAVESEGRETA);


                                // Parametri obbligatori
                                $obbligatori = array(
                                    'alias' => $ALIAS,
                                    'importo' => $importo,
                                    'divisa' => $divisa,
                                    'codTrans' => $codTrans,
                                    'url' =>  $merchantServerUrl.'esito?v='.session('PARAM').'&dir='.session('DIRECTORY'),
                                    'url_back' => $merchantServerUrl.'annullo?v='.session('PARAM').'&dir='.session('DIRECTORY'),
                                    'mac' => $mac, 
                                    'typology' => (isset($request->typology)?'DEFERRED':'IMMEDIATE'), // Imposta il pagamento differito o immediato 
                                );

                                // Parametri facoltativi
                                $facoltativi = array(
                                );

                                $requestParams = array_merge($obbligatori, $facoltativi);

                                foreach ($requestParams as $name => $value) { 
                                    $nexi .= '<input type="hidden" name="'.$name.'" value="'.htmlentities($value).'" />'."\r\n";
                                }
                                    
                                if($ApiKeyNexi !=''){
                                    $nexi .= '<div class="clearfix"></div>';
                                    if($tot_cc_check == 0 && $tot_pag_check == 0){
                                        $nexi .= ' <button type="submit"  class="pulsante p-2 noBorder" id="nexi-button">'.dizionario('PAGA_NEXI').'</button>';
                                    }elseif($tot_pag_check > 0 && $tot_cc_check == 0){
                                        if($TipoPagamento == 'Nexi'){
                                        $nexi .= '<span class="text-green">'.dizionario('PAGAMENTOSCELTO').' Nexi</span>';
                                        }else{
                                        $nexi .= '<span class="text-red">'.dizionario('PROPOSTAPAGAMENTOSCELTO').'</span>';
                                        $nexi .= '<br><button type="submit"  class="pulsante p-2 noBorder" id="nexi-button">'.$CAMBIA_NEXI.'</button>';
                                        }
                                    }elseif($tot_pag_check == 0 && $tot_cc_check > 0){
                                        $nexi .= '<span class="text-red">'.dizionario('PROPOSTAPAGAMENTOSCELTO').'</span>';
                                        $nexi .= '<br><button type="submit"  class="pulsante p-2 noBorder" id="nexi-button">'.$CAMBIA_NEXI.'</button>';
                                    } 
                            }else{
                                $nexi .= '<small class="text-red">API di riferimento Nexi, non è stata inserita!</small>';
                            }
                                    
                    $nexi .= '</form>';
                    $nexi .= '</div></div></div>';
                        if($request->result!='' && base64_decode($request->result)=='nexi') {

                        $nexi .= '<script>$("#nexi-button").fadeOut();</script>';
                    
                        }

        }

        return $nexi;
    }


    /**
     * pro_template
     *
     * @param  mixed $template
     * @param  mixed $directory
     * @param  mixed $params
     * @return void
     */
    public function pro_template($template, $directory, $params, Request $request)
    {
        //$template = 'custom4';
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

        if ($idsito) {
            session(['IDSITO' => $idsito]);
        }

        if($directory){
            session(['DIRECTORY' => $directory]);
        }

        if($params){
            session(['PARAM' => $params]);
        }


        $coord       = $this->coordinateCliente($idsito);
        $array_coord = explode("#",$coord);
        $LatCliente  = $array_coord[1];
        $LonCliente  = $array_coord[0];

        $row = $this->getCliente($idsito);
     
        $NomeCliente         = $row->nome;
        $EmailCliente        = $row->email;
        $Indirizzo           = $row->indirizzo;
        $Localita            = $row->nome_comune;
        $Cap                 = $row->cap;
        $CIR                 = $row->CIR;
        $CIN                 = $row->CIN;
        if(strstr($row->tel,'+39') || strstr($row->tel,'0039')){
            $tel             = $row->tel;
        }else{
            $tel             = '+39 '.$row->tel;
        }
        $Provincia           = $row->sigla_provincia;
        $SitoWeb             = 'https://'.$row->web;
        $Logo                = $row->logo;
        $TagManager          = $row->TagManager;
        $IdAccountAnalytics  = $row->IdAccountAnalytics;
        $IdPropertyAnalytics = $row->IdPropertyAnalytics;
        $ViewIdAnalytics     = $row->ViewIdAnalytics;
        $abilita_mappa       = $row->abilita_mappa;

        if ($NomeCliente) {
            session(['NomeCliente' => $NomeCliente]);
        }
        if ($Logo) {
            session(['LOGO' => $Logo]);
        }

        $codeTagMan = $this->tagManager($idsito,$TagManager);
        $head_tagmanager = $codeTagMan[0];
        $body_tagmanager = $codeTagMan[1];

        $rw = $this->social($idsito);
        if($rw->Facebook!=''){
            $Facebook   = '<a  href="'.$rw->Facebook.'" target="_blank"><i class="fa-brands fa-facebook-f text-secondary"></i></a>';
        }else{
            $Facebook   = '';
        }
        if($rw->Twitter!=''){
            $Twitter    = '<a  href="'.$rw->Twitter.'" target="_blank"><img src="/img/x-twitter.png" style="height:24px;margin-top:-5px"></a>';
        }else{
            $Twitter   = '';
        }
        if($rw->Instagram!=''){
            $Instagram    = '<a  href="'.$rw->Instagram.'" target="_blank"><i class="fa-brands fa-instagram text-secondary"></i></a>';
        }else{
            $Instagram   = '';
        }
        if($rw->Pinterest!=''){
            $Pinterest    = '<a  href="'.$rw->Pinterest.'" target="_blank"><i class="fa-brands fa-pinterest-p text-secondary"></i></a>' ;
        }else{
            $Pinterest   = '';
        }

        $logoTop = ($Logo == '' ? '<i class="fas fa-bed fa-5x fa-fw"></i>' : '<img src="' . config('global.settings.BASE_URL_IMG') . 'uploads/loghi/' . $Logo . '" style="width:100%;max-width:250px;" class="logo">');
        $logoFooter = ($Logo == '' ? '<i class="fas fa-bed fa-5x fa-fw"></i>' : '<img src="' . config('global.settings.BASE_URL_IMG')  . 'uploads/loghi/' . $Logo . '" width="200px">');

        ## DATI RICHIESTA ##
        $richiesta = $this->richiesta($idsito, $id_richiesta);

        if (sizeof($richiesta) == 0) {
            return redirect('/error?sito='.$SitoWeb);
            exit;
       }else{
        $rows = $richiesta[0];  

        Session::put('dati_h_guest', $rows);

        $overfade = '';
        if ($rows->Chiuso == 0) {
            if ($rows->DataScadenza < date('Y-m-d') || $rows->Archivia == 1) {

                switch ($rows->Lingua) {
                    case "it":
                        $proposta_scaduta_title = 'PROPOSTA SCADUTA';
                        $proposta_scaduta_text = 'Vuoi riattivare questa proposta di soggiorno, scrivici in chat e ti risponderemo al più presto!';
                        break;
                    case "en":
                        $proposta_scaduta_title = 'PROPOSAL EXPIRED';
                        $proposta_scaduta_text = 'Do you want to reactivate this stay proposal, write us in chat and we will reply as soon as possible!';
                        break;
                    case "fr":
                        $proposta_scaduta_title = 'PROPOSITION EXPIRÉE';
                        $proposta_scaduta_text = 'Voulez-vous réactiver cette proposition de séjour, écrivez-nous dans le chat et nous vous répondrons dans les plus brefs délais!';
                        break;
                    case "de":
                        $proposta_scaduta_title = 'VORSCHLAG ABGELAUFEN';
                        $proposta_scaduta_text = 'Möchten Sie diesen Aufenthaltsvorschlag reaktivieren, schreiben Sie uns im Chat und wir werden so schnell wie möglich antworten!';
                        break;
                }
                $overfade = '<style>
                                    div.fadeMe {
                                        opacity:    0.5;
                                        background: #000;
                                        width:      100%;
                                        height:     100%;
                                        z-index:    10;
                                        top:        0;
                                        left:       0;
                                        position:   fixed;
                                    }
                            </style>
                            <script language="javascript">_alert("' . $proposta_scaduta_title . '","' . $proposta_scaduta_text . '")</script>';
            }
        }

        $mesi = array(  'it' => array("01" => "Gennaio", "02" => "Febbraio", "03" => "Marzo", "04" => "Aprile", "05" => "Maggio", "06" => "Giugno", "07" => "Luglio", "08" => "Agosto", "09" => "Settembre", "10" => "Ottobre", "11" => "Novembre", "12" => "Dicembre"),
                        'en' => array("01" => "January", "02" => "February", "03" => "March", "04" => "April", "05" => "May", "06" => "June", "07" => "July", "08" => "August", "09" => "September", "10" => "October", "11" => "November", "12" => "December"),
                        'fr' => array("01" => "Janvier", "02" => "Février", "03" => "Mars", "04" => "Avril", "05" => "Mai", "06" => "Juin", "07" => "Juillet", "08" => "Août", "09" => "Septembre", "10" => "Octobre", "11" => "Novembre", "12" => "Décembre"),
                        'de' => array("01" => "Januar", "02" => "Februar", "03" => "März", "04" => "April", "05" => "Mai", "06" => "Juni", "07" => "Juli", "08" => "August", "09" => "September", "10" => "Oktober", "11" => "November", "12" => "Dezember"),
                    );


        $Id                  = $rows->Id;
        $Chiuso              = $rows->Chiuso;
        $id_politiche        = $rows->id_politiche;
        $AccontoRichiesta    = $rows->AccontoRichiesta;
        $AccontoLibero       = $rows->AccontoLibero;
        $Operatore           = stripslashes($rows->ChiPrenota);
        $TipoRichiesta       = $rows->TipoRichiesta;
        $Nome                = stripslashes($rows->Nome);
        $Cognome             = stripslashes($rows->Cognome);
        $Email               = $rows->Email;
        $Cellulare           = $rows->Cellulare;
        $Lingua              = $rows->Lingua;
        if ($Lingua) {
            session(['LINGUA' => $Lingua]);
        }
        $DataRichiestaCheck  = $rows->DataRichiesta;
        $DataR_tmp           = explode("-", $rows->DataRichiesta);
        $DataRichiesta       = $DataR_tmp[2] . '/' . $DataR_tmp[1] . '/' . $DataR_tmp[0];
        $ArrivoData          = $rows->DataArrivo;
        $PartenzaData        = $rows->DataPartenza;
        $DataA_tmp           = explode("-", $rows->DataArrivo);
        $DataArrivo          = $DataA_tmp[2] . '/' . $DataA_tmp[1] . '/' . $DataA_tmp[0];
        $DataArrivo_estesa   = $DataA_tmp[2] . ' ' . $mesi[$Lingua][$DataA_tmp[1]] . ' ' . $DataA_tmp[0];
        $DataP_tmp           = explode("-", $rows->DataPartenza);
        $DataPartenza        = $DataP_tmp[2] . '/' . $DataP_tmp[1] . '/' . $DataP_tmp[0];
        $DataPartenza_estesa = $DataP_tmp[2] . ' ' . $mesi[$Lingua][$DataP_tmp[1]] . ' ' . $DataP_tmp[0];
        if($rows->DataValiditaVoucher != '') {
                $DataValiditaVoucher_tmp = explode("-", $rows->DataValiditaVoucher);
                $DataValiditaVoucher     = $DataValiditaVoucher_tmp[2] . '/' . $DataValiditaVoucher_tmp[1] . '/' . $DataValiditaVoucher_tmp[0];
        } else {
                $DataValiditaVoucher = '';          
        }
        $Nprenotazione           = $rows->NumeroPrenotazione;
        $NumeroPrenotazione      = $rows->NumeroPrenotazione . '/' . $Id;
        $FontePrenotazione       = $rows->FontePrenotazione;
        $NumeroAdulti            = $rows->NumeroAdulti;
        $NumeroBambini           = $rows->NumeroBambini;
        if($rows->DataScadenza != '') {
            $DataS_tmp           = explode("-", $rows->DataScadenza);
            $DataScadenza        = $DataS_tmp[2] . '/' . $DataS_tmp[1] . '/' . $DataS_tmp[0];
            $DataScadenza_estesa = $DataS_tmp[2] . ' ' . $mesi[$Lingua][$DataS_tmp[1]] . ' ' . $DataS_tmp[0];
        } else {
            $DataScadenza        = '';
            $DataScadenza_estesa = '';
        }

        $start                   = mktime(24, 0, 0, $DataA_tmp[1], $DataA_tmp[2], $DataA_tmp[0]);
        $end                     = mktime(01, 0, 0, $DataP_tmp[1], $DataP_tmp[2], $DataP_tmp[0]);
        $formato                 = "%a";
        $Notti                   = $this->dateDiff($rows->DataArrivo, $rows->DataPartenza, $formato);
        if($rows->DataInvio != '') {
                $DataI_tmp               = explode("-", $rows->DataInvio);
                $DataInvio               = $DataI_tmp[2] . '/' . $DataI_tmp[1] . '/' . $DataI_tmp[0];
        } else {
                $DataInvio               = '';          
        }
        $Cliente                 = $Nome . ' ' . $Cognome;
        ## FINE DATI RICHIESTA ##
    }
        ## COUNT PROPOSTE ##
        $Nproposte = $this->countProposte($id_richiesta);
        ## FINE COUNT PROPOSTE ##

        ## NUOVA AGGIUNTA DIZIONARIO PER QUESTO NUOVO TEMPLATE ##
        switch($Lingua){
            case"it":
              $titoloProposte                 = 'ABBIAMO <strong>'.$Nproposte.' </strong> '.($Nproposte == 1?'PROPOSTA': 'PROPOSTE').' PER TE';
              $titoloServiziInclusi           = 'APPROFITTA ORA DEI SERVIZI AGGIUNTIVI';
              $sottoTitoloServiziInclusi      = 'Servizi compresi nelle nostre proposte';
              $titoloServiziAggiuntivi        = 'Servizi aggiunti o da aggiungere alla tua offerta per personalizzare la tua esperienza';
              $dettagli                       = 'Dettagli';
              $visualizzaMaggioriInformazioni = 'Visualizza maggiori informazioni';
              $maggioriInformazioni           = 'Maggiori informazioni';
              $visualizzaCondizioniTariffarie = 'Visualizza le Condizioni Tariffarie';
              $selezionaQuestaProposta        = 'Seleziona questa proposta';
              $selezionaAltraProposta         = 'Seleziona un\'altra proposta';
              $haiSelezionatoQuestaProposta   = 'Hai selezionato questa proposta';
              $servizioCompresoProposta       = 'Questo servizio è compreso nella tua proposta';
              $aggiungiQuestoServizio         = 'Aggiungi questo servizio';
              $haiSelezionatoQuestoServizio   = 'Hai selezionato questo servizio';
              $rimuoviQuestoServizio          = 'Rimuovi questo servizio';
              $calcolaCostoServizio           = 'Calcola il costo del servizio';
              $textTotale                     = 'Totale';
              $fraseChat                      = 'Se hai ancora dubbi Chatta con Noi';
              $tooltipChat                    = 'Per qualsiasi dubbio chatta diretttamente con noi';
              $gratis                         = 'Gratis';
              $gentile                        = 'Gentile';
              $ABILITA                        = 'Aggiungi Servizio';
              $OBBLIGATORIO                   = 'Incluso';
              $IMPOSTO                        = 'Incluso in questa proposta';
              $A_PERCENTUALE                  = 'A percentuale';
              $prezzoServizio                 = 'Prezzo del servizio';
              $LabelPrezzoServizio            = 'Prezzo del servizio';
              $textInfoSconto                 = 'Lo sconto viene applicato solo sul totale della proposta che la struttura ricettiva, al momento della creazione del preventivo ha compilato; qualsiasi modifica apportata aggiungendo od eliminando servizi aggiuntivi, non agirà sulla cifra delle sconto!';
              $PrezzoServizio                 = 'Prezzo Servizio';
              $NumeroGiorni                   = 'Numero Giorni';
              $ServizioAPersona               = 'a Persona';
              break;
            case"en":
              $titoloProposte                 = 'WE HAVE <strong>'.$Nproposte.' </strong> '.($Nproposte == 1?'PROPOSAL': 'PROPOSALS').' FOR YOU';
              $titoloServiziInclusi           = 'NOW TAKE ADVANTAGE OF THE ADDITIONAL SERVICES';
              $sottoTitoloServiziInclusi      = 'Services included in our proposals';
              $titoloServiziAggiuntivi        = 'Added or additional services to personalize your experience';
              $dettagli                       = 'Details';
              $visualizzaMaggioriInformazioni = 'View more information';
              $maggioriInformazioni           = 'More information';
              $visualizzaCondizioniTariffarie = 'View Tariff Conditions';
              $selezionaQuestaProposta        = 'Select this proposal';
              $selezionaAltraProposta         = 'Select another proposal';
              $haiSelezionatoQuestaProposta   = 'You have selected this proposal';
              $servizioCompresoProposta       = 'This service is included in your proposal';
              $aggiungiQuestoServizio         = 'Add this service';
              $haiSelezionatoQuestoServizio   = 'You have selected this service';
              $rimuoviQuestoServizio          = 'Remove this service';
              $calcolaCostoServizio           = 'Calculate the service cost';
              $textTotale                         = 'Total';
              $fraseChat                      = 'If you still have doubts, chat with us';
              $tooltipChat                    = 'If you have any doubts, chat directly with us';
              $gratis                         = 'Free';
              $gentile                        = 'Dear';
              $ABILITA                        = 'Add Service';
              $OBBLIGATORIO                   = 'Included';
              $IMPOSTO                        = 'Included in this proposal';
              $A_PERCENTUALE                  = 'By percentage';
              $prezzoServizio                 = 'Price of the service';
              $LabelPrezzoServizio            = 'Price of the service';
              $textInfoSconto                 = 'The discount is applied only to the total of the proposal that the accommodation facility, at the time of creating the quote, has compiled; any modifications made by adding or removing additional services will not affect the discount amount!';
              $PrezzoServizio                 = 'Price Service';
              $NumeroGiorni                   = 'Number of Days';
              $ServizioAPersona               = 'per person';
              break;
            case"fr":
              $titoloProposte                 = 'NOUS AVONS <strong>'.$Nproposte.' </strong> '.($Nproposte == 1?'PROPOSITION': 'PROPOSITIONS').' POUR VOUS';
              $titoloServiziInclusi           = 'PROFITEZ MAINTENANT DES SERVICES SUPPLÉMENTAIRES';
              $sottoTitoloServiziInclusi      = 'Services inclus dans nos propositions';
              $titoloServiziAggiuntivi        = 'Services ajoutés ou à ajouter à votre offre pour personnaliser votre expérience';
              $dettagli                       = 'Détails';
              $visualizzaMaggioriInformazioni = 'Afficher plus d\'informations';
              $maggioriInformazioni           = 'Plus d\'informations';
              $visualizzaCondizioniTariffarie = 'Afficher les Conditions Tarifaires';
              $selezionaQuestaProposta        = 'Sélectionner cette proposition';
              $selezionaAltraProposta         = 'Sélectionnez une autre proposition';
              $haiSelezionatoQuestaProposta   = 'Vous avez sélectionné cette proposition';
              $servizioCompresoProposta       = 'Ce service est inclus dans votre proposition';
              $aggiungiQuestoServizio         = 'Ajouter ce service';
              $haiSelezionatoQuestoServizio   = 'Vous avez sélectionné ce service';
              $rimuoviQuestoServizio          = 'Supprimer ce service';
              $calcolaCostoServizio           = 'Calculer le coût du service';
              $textTotale                         = 'Total';
              $fraseChat                      = 'Si vous avez encore des doutes, discutez avec nous';
              $tooltipChat                    = 'Si vous avez des doutes, discutez directement avec nous';
              $gratis                         = 'Gratuit';
              $gentile                        = 'Bonjour ';
              $ABILITA                        = 'Ajouter un service';
              $OBBLIGATORIO                   = 'Inclus';
              $IMPOSTO                        = 'Inclus dans cette proposition';
              $A_PERCENTUALE                  = 'Par pourcentage';
              $prezzoServizio                 = 'Prix ​​de la prestation';
              $LabelPrezzoServizio            = 'Prix du service';
              $textInfoSconto                 = 'La remise est appliquée uniquement sur le total de la proposition que l\'établissement d\'hébergement, au moment de la création du devis, a compilé ; toute modification apportée en ajoutant ou en supprimant des services supplémentaires n\'affectera pas le montant de la remise!';
              $PrezzoServizio                 = 'Service de prix';
              $NumeroGiorni                   = 'Nombre de jours';
              $ServizioAPersona               = 'par personne';
              break;
            case"de":
              $titoloProposte                 = 'WIR HABEN <strong>'.$Nproposte.' </strong> '.($Nproposte == 1?'VORSCHLAG': 'VORSCHLÄGE').' FÜR DICH';
              $titoloServiziInclusi           = 'NUTZE JETZT DIE ZUSÄTZLICHEN SERVICES';
              $sottoTitoloServiziInclusi      = 'Dienstleistungen in unseren Angeboten enthalten';
              $titoloServiziAggiuntivi        = 'Hinzugefügte oder hinzuzufügende Dienstleistungen, um Ihr Erlebnis zu personalisieren';
              $dettagli                       = 'Details';
              $visualizzaMaggioriInformazioni = 'Weitere Informationen anzeigen';
              $maggioriInformazioni           = 'Weitere Informationen';
              $visualizzaCondizioniTariffarie = 'Tarifbedingungen anzeigen';
              $selezionaQuestaProposta        = 'Diese Option auswählen';
              $selezionaAltraProposta         = 'Wählen Sie einen anderen Vorschlag aus';
              $haiSelezionatoQuestaProposta   = 'Sie haben diese Option ausgewählt';
              $servizioCompresoProposta       = 'Dieser Service ist in Ihrem Angebot enthalten';
              $aggiungiQuestoServizio         = 'Diesen Service hinzufügen';
              $haiSelezionatoQuestoServizio   = 'Sie haben diesen Service ausgewählt';
              $rimuoviQuestoServizio          = 'Diesen Service entfernen';
              $calcolaCostoServizio           = 'Servicekosten berechnen';
              $textTotale                         = 'Gesamt';
              $fraseChat                      = 'Wenn Sie immer noch Zweifel haben, chatten Sie mit uns';
              $tooltipChat                    = 'Wenn Sie Zweifel haben, chatten Sie direkt mit uns';
              $gratis                         = 'Frei';
              $gentile                        = 'Hallo ';
              $ABILITA                        = 'Service hinzufügen';
              $OBBLIGATORIO                   = 'Inbegriffen ';
              $IMPOSTO                        = 'In diesem Vorschlag enthalten';
              $A_PERCENTUALE                  = 'In Prozent';
              $prezzoServizio                 = 'Preis der Dienstleistung';
              $LabelPrezzoServizio            = 'Preis der Dienstleistung';
              $textInfoSconto                 = 'Der Rabatt wird nur auf den Gesamtbetrag des Angebots angewendet, den die Unterkunftseinrichtung zum Zeitpunkt der Erstellung des Angebots zusammengestellt hat; Änderungen durch das Hinzufügen oder Entfernen zusätzlicher Dienstleistungen beeinflussen nicht den Rabattbetrag!';
              $PrezzoServizio                 = 'Preisservice';
              $NumeroGiorni                   = 'Anzahl der Tage';
              $ServizioAPersona               = 'pro Person';
              break;
           }

        ## TESTO DEL MESSAGGIO ##
        $testo_messaggio    = dizionario('ALLA_CO') . ' ' . $NomeCliente . ',' . "\r\n" . dizionario('CONTENUTO_MSG');
        $testo_saluti       = dizionario('CORDIALMENTE') . "\r\n" . $Cliente;
        $testo_riferimento  = 'Rif. nr. <b>' . $Nprenotazione . '</b> - Fonte di Provenienza: <b>' . $FontePrenotazione . '</b> - Preventivo Intestato a <b>' . $Cliente . '</b> del <b>' . $DataRichiesta . '</b> inviato il <b>' . $DataInvio . '</b>';
        ## FINE TESTO DEL MESSAGGIO ##

        ## IMPOSTAZIONI VARIABILI PER LAYOUT
        $data_arrivo   = $DataArrivo;
        $data_partenza = $DataPartenza;
        $adulti        = $NumeroAdulti;
        $bambini       = $NumeroBambini;
        $numero        = $NumeroPrenotazione;
        $data          = $DataRichiesta;
        $scadenza      = $DataScadenza;

        ## TESTO INTRODUTTIVO ##
        $Testo = $this->contenutiTesto($idsito, $Id, $Cliente, $TipoRichiesta, $Lingua,strtoupper($template));

        ## IMMAGNE TOP E VIDEO ##
        
        $rec = $this->imageVideo($idsito, $template);
        $imgTop = $rec[0];
        $video  = $rec[1];
        if ($video) {
            $streamVideo = '<div class="container p-2 mx-auto my-5 ">
                                <div class="ratio ratio-16x9 videocontainer">
                                    <iframe src="' . $video . '" title="YouTube video" allowfullscreen></iframe>
                                </div>
                            </div>' . "\r\n";
        } else {
            $streamVideo = '';
        }

        ## PROPOSTE ##

        $hr = "SELECT
                    hospitality_proposte.Id as IdProposta,
                    hospitality_proposte.Arrivo as Arrivo,
                    hospitality_proposte.Partenza as Partenza,
                    hospitality_proposte.NomeProposta as NomeProposta,
                    hospitality_proposte.TestoProposta as TestoProposta,
                    hospitality_proposte.CheckProposta as CheckProposta,
                    hospitality_proposte.PrezzoL as PrezzoL,
                    hospitality_proposte.PrezzoP as PrezzoP,
                    hospitality_proposte.AccontoPercentuale as AccontoPercentuale,
                    hospitality_proposte.AccontoImporto as AccontoImporto,
                    hospitality_proposte.AccontoTariffa as AccontoTariffa,
                    hospitality_proposte.AccontoTesto as AccontoTesto
                FROM
                    hospitality_proposte
                WHERE
                    hospitality_proposte.id_richiesta = :id_richiesta";
        $res = DB::select($hr,['id_richiesta' => $Id]);            
        $r_n = sizeof($res);
    if($r_n > 0){

        $tagProposte        = '';
        $tabProposte        = '';
        $contproposte       = 1;
        $n                  = 1;
        $NomeProposta       = '';
        $TestoProposta      = '';
        $CheckProposta      = '';
        $TipoCamere         = '';
        $PrezzoL            = '';
        $PrezzoP            = '';
        $PrezzoPC           = '';
        $sistemazione       = '';
        $percentuale_sconto = '';
        $AccontoPercentuale = '';
        $AccontoImporto     = '';
        $AccontoTariffa     = '';
        $AccontoTesto       = '';
        $Arrivo             = '';
        $Partenza           = '';
        $A                  = '';
        $P                  = '';
        $FCamere            = '';
        $servizi            = '';
        $testoProp          = '';
        $valore             = '';
        $servInc            = '';
        $servFac            = '';
        $serviziFac         = '';
        $percentualeCaparra = '';
        $valoreCaparra      = ''; 
        $boxProposta        = '';
        $totaleServizi      = '';
        $ImportoSconto      = '';
        $proposta           = '';
        $imp_sconto         = '';

        foreach ($res as $chiave => $value) {

            Session::put('dati_p_guest', $value);

            $PrezzoL            = number_format($value->PrezzoL,2,',','.');
            $PrezzoP_format     = number_format($value->PrezzoP,2,',','.');
            $PrezzoP            = $value->PrezzoP;
            $PrezzoPC           = $value->PrezzoP;
            $NomeProposta       = stripslashes($value->NomeProposta);
            $TestoProposta      = stripslashes($value->TestoProposta);
            $CheckProposta      = $value->CheckProposta;
            $IdProposta         = $value->IdProposta;
            $AccontoPercentuale = $value->AccontoPercentuale;
            $AccontoImporto     = $value->AccontoImporto;
            $AccontoTariffa     = stripslashes($value->AccontoTariffa);
            $AccontoTesto       = stripslashes($value->AccontoTesto);
            $A                  = $value->Arrivo;
            $P                  = $value->Partenza;
            if($value->Arrivo!=''){
                $A_tmp              = explode("-",$value->Arrivo);
                $Arrivo             = $A_tmp[2].'/'.$A_tmp[1].'/'.$A_tmp[0];
            }else{
                $Arrivo = '';
            }
            if($value->Partenza!=''){
                $P_tmp              = explode("-",$value->Partenza);
                $Partenza           = $P_tmp[2].'/'.$P_tmp[1].'/'.$P_tmp[0];
            }else{
                $Partenza = '';
            }
            if($A!='') {
                $Astart             = mktime(24,0,0,$A_tmp[1],$A_tmp[2],$A_tmp[0]);
                $Arrivo_estesa      = $A_tmp[2].' '.$mesi[$Lingua][$A_tmp[1]].' '.$A_tmp[0];
            }
            if($P!=''){
                $Aend               = mktime(01,0,0,$P_tmp[1],$P_tmp[2],$P_tmp[0]);
                $Partenza_estesa    = $P_tmp[2].' '.$mesi[$Lingua][$P_tmp[1]].' '.$P_tmp[0];
            }
            $formato="%a";
            $ANotti = $this->dateDiff($value->Arrivo,$value->Partenza,$formato);

            $select_sconti = "  SELECT 
                                hospitality_relazione_sconto_proposte.* 
                            FROM 
                                hospitality_relazione_sconto_proposte
                            WHERE 
                                hospitality_relazione_sconto_proposte.idsito = :idsito
                            AND 
                                hospitality_relazione_sconto_proposte.id_richiesta = :id_richiesta
                            AND 
                                hospitality_relazione_sconto_proposte.id_proposta = :id_proposta";
            $result_sconti = DB::select($select_sconti,['idsito' => $idsito, 'id_richiesta' => $Id, 'id_proposta' => $IdProposta]);
            if(sizeof($result_sconti)>0){
                $rec_sconti    = $result_sconti[0];
                $imp_sconto    = $rec_sconti->sconto;

                if($imp_sconto != 0 && $imp_sconto != ''){
                    $percentuale_sconto =  $imp_sconto;
                    /*calcolo l'importo dello sconto*/
                    $selSconto     = "SELECT SUM(hospitality_richiesta.Prezzo) as prezzo_camere FROM hospitality_richiesta WHERE hospitality_richiesta.id_richiesta = :id_richiesta AND hospitality_richiesta.id_proposta = :id_proposta";
                    $resSconto     = DB::select($selSconto,['id_richiesta' => $Id, 'id_proposta' => $IdProposta]);
                    $recSconto     = $resSconto[0];
                    $ImpSconto     = (($recSconto->prezzo_camere*$percentuale_sconto)/100);
                    $ImportoSconto = number_format($ImpSconto,2,',','.');
                    $noFormatImportoSconto = $ImpSconto;
                } 
            }else{
                $imp_sconto = '';
                $ImportoSconto      = '';
                $percentuale_sconto = '';
            }

            $select2 = "SELECT
                        hospitality_richiesta.NumeroCamere,
                        hospitality_richiesta.Prezzo,
                        hospitality_richiesta.NumAdulti,
                        hospitality_richiesta.NumBambini,
                        hospitality_richiesta.EtaB,
                        hospitality_richiesta.Id as id_etaB,
                        hospitality_tipo_camere.Id as IdCamera,
                        hospitality_tipo_camere.TipoCamere as TipoCamere,
                        hospitality_tipo_camere.Servizi as Servizi,
                        hospitality_camere_testo.Camera as TitoloCamera,
                        hospitality_camere_testo.Descrizione as TestoCamera,
                        hospitality_tipo_soggiorno.TipoSoggiorno as TipoSoggiorno,
                        hospitality_tipo_soggiorno_lingua.Soggiorno as TitoloSoggiorno,
                        hospitality_tipo_soggiorno_lingua.Descrizione as TestoSoggiorno
                    FROM
                        hospitality_richiesta
                        INNER JOIN hospitality_tipo_camere ON hospitality_tipo_camere.Id = hospitality_richiesta.TipoCamere
                        INNER JOIN hospitality_camere_testo ON hospitality_camere_testo.camere_id = hospitality_tipo_camere.Id
                        INNER JOIN hospitality_tipo_soggiorno ON hospitality_tipo_soggiorno.Id = hospitality_richiesta.TipoSoggiorno
                        INNER JOIN hospitality_tipo_soggiorno_lingua ON hospitality_tipo_soggiorno_lingua.soggiorni_id = hospitality_tipo_soggiorno.Id
                        WHERE hospitality_tipo_camere.idsito = :idsito
                        AND hospitality_camere_testo.idsito = :idsito2
                        AND hospitality_tipo_soggiorno.idsito = :idsito3
                        AND hospitality_tipo_soggiorno_lingua.idsito = :idsito4
                        AND hospitality_richiesta.id_proposta = :IdProposta 
                        AND hospitality_camere_testo.lingue = :Lingua 
                        AND hospitality_tipo_camere.Abilitato = :Abilitato
                        AND hospitality_tipo_soggiorno_lingua.lingue = :Lingua2
                        AND hospitality_tipo_soggiorno.Abilitato = :Abilitato2 
                        ORDER BY hospitality_richiesta.Id ASC" ;
            $result2 = DB::select($select2,[
                                            'idsito'  => $idsito,
                                            'idsito2' => $idsito,
                                            'idsito3' => $idsito,
                                            'idsito4' => $idsito,
                                            'Lingua'  => $Lingua,
                                            'Lingua2' => $Lingua,
                                            'IdProposta' => $IdProposta,
                                            'Abilitato'  => 1,
                                            'Abilitato2' => 1
                                        ]);

            $totale = 0;
            $EtaB  = '';
            $EtaB_ = '';
            $x                    = 1;
            $Servizi              = '';
            $serv                 = '';
            $servizi              = '';
            $services             = '';
            $image_room           = '';
            $Nomi_camera          = '';
            $FCamere              = '';
            $camere               = '';
            foreach ($result2 as $key => $val) {

                $Servizi         = $val->Servizi;

                $sel_bamb = "SELECT hospitality_richiesta.NumAdulti,hospitality_richiesta.NumBambini,hospitality_richiesta.EtaB FROM hospitality_richiesta WHERE  hospitality_richiesta.Id = :Id";
                $res_bamb = DB::select($sel_bamb,['Id' => $val->id_etaB]);
                if(sizeof($res_bamb)>0){
                    $rec_B    = $res_bamb[0];

                    $EtaB           = $rec_B->EtaB;
                    $NumAdulti      = $rec_B->NumAdulti;
                    $NumBambini     = $rec_B->NumBambini;
                }else{
                    $EtaB       = '';
                    $NumBambini = '';

                }
                $NumeroCamere    = $val->NumeroCamere;
                $IdCamera        = $val->IdCamera;
                $TipoCamere      = $val->TipoCamere;
                $TitoloCamera    = $val->TitoloCamera;
                $TestoCamera     = $val->TestoCamera;
                $TipoSoggiorno   = $val->TipoSoggiorno;
                $TitoloSoggiorno = $val->TitoloSoggiorno;
                $TestoSoggiorno  = $val->TestoSoggiorno;
                $Subtotale       = number_format(($NumeroCamere*$val->Prezzo),2,',','.');
                $Prezzo          = number_format($val->Prezzo,2,',','.');
                $totale_tmp      = ($val->NumeroCamere*$val->Prezzo);
                $totale          = ($totale_tmp + $totale);

                $FCamere .= $val->TitoloSoggiorno.' - Nr. '.$val->NumeroCamere.' '.$val->TipoCamere.' '.($DataRichiestaCheck > config('global.settings.DATA_QUOTO_V2') ?($NumAdulti!=0?'A.'.$NumAdulti:'').' '.($NumBambini!=0?'B.'.$NumBambini:'').' '.($EtaB!='' && $EtaB!=0?'<small>'.dizionario('ETA').' '.$EtaB.'</small>':''):'').'- €. '.number_format($val->Prezzo,2,',','.').' - ';


                if($Servizi != ''){

                    $serv = explode(",",$Servizi);
                    $services = array();
                    foreach ($serv as $key => $value) {
                            $q = "SELECT * FROM hospitality_servizi_camere_lingua WHERE Servizio LIKE '%".addslashes($serv[$key])."%' AND idsito = ".$idsito."  ";
                            $r = DB::select($q);
                            $record = $r[0];
                            $id_servizio = $record->servizi_id;

                                if($id_servizio){
                                    $qy = "SELECT * FROM hospitality_servizi_camere_lingua WHERE servizi_id = ".$id_servizio." AND lingue = '".$Lingua."' AND idsito = ".$idsito." ";
                                    $rs = DB::select($qy);
                                    $val = $rs[0];
                                    $services[$record->servizi_id] = $val->Servizio;
                                }
                    }

                    if(!empty($services)){
                        $servizi = implode(", ",$services);
                    }


                }

                $sel    = "SELECT Foto FROM hospitality_gallery_camera WHERE IdCamera = :IdCamera AND idsito = :idsito";
                $res    = DB::select($sel,['idsito' => $idsito, 'IdCamera' => $IdCamera]);
                $nFoto = 1;
                $idRand = rand();
                $image_room ='<div id="carousel'.$idRand.'" class="carousel slide" data-bs-ride="carousel">
                                    <div class="carousel-inner">'."\r\n";
                    if(sizeof($res)==0){
                        $image_room .='<div class="carousel-item active">
                                            <img src="'.url('newTemplate/img/generic_room.jpg').'" class="d-block w-100" alt="..."/>
                                        </div> '."\r\n";  
                    }else{                
                        foreach($res as $k => $v) {

                                $image_room .='<div class="carousel-item'.($nFoto==1?' active':'').'">
                                                    <img src="'.config('global.settings.BASE_URL_IMG').'uploads/'.$idsito.'/'.$v->Foto.'" class="d-block w-100" alt="..."/>
                                                </div> '."\r\n";  

                            $nFoto++;    
                        }
                    }
                    $image_room .='<button class="carousel-control-prev" type="button" data-bs-target="#carousel'.$idRand.'" data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Previous</span>
                                    </button>
                                    <button class="carousel-control-next" type="button" data-bs-target="#carousel'.$idRand.'" data-bs-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Next</span>
                                    </button>
                                </div>
                            </div>'."\r\n"; 


                $camere .='<div class="clearfix p-b-5"></div>
                            <div class="row camera camera'.$x.' m-0 p-0">
                                <div class="col-12 col-md-6 immagine p-0 m-0">
                                        '.$image_room.''."\r\n";
                if($x==1){
                    $camere .='         <div class="sunto">
                                            <div class="titolo">'.dizionario('PROPOSTA') .' '.$contproposte.'</div>
                                            <div class="prezzo"></div>
                                            <div class="arrivo">'.$Arrivo.'</div>
                                            <div class="partenza">'.$Partenza.'</div>
                                            <div class="persone">'.dizionario('ADULTI').' '.$adulti.' '.($bambini > 0? '- '.dizionario('BAMBINI').' '.$bambini:'').'</div>
                                            '.($bambini > 0? '<div class="persone">'.dizionario('ETA').' '.$EtaB.' '.dizionario('ANNI').'</div>' : '').'
                                        </div>'."\r\n";
                }
                $camere .='     </div>
                                <div class="col-12 col-md-6 p-5 content">
                                    <h4 class="titolo_camera">'.$TitoloSoggiorno.'<br>'.$TitoloCamera.' <br>'.dizionario('PREZZO_CAMERA').' <i class="fa fa-euro"></i> '.$Prezzo.'</h4>
                                    <div style="clear:both;padding-bottom:10px"></div>
                                    <div class="testo_camera">'.(strlen(strip_tags($TestoCamera))>=200?substr(strip_tags($TestoCamera),0,200).'...':$TestoCamera).'</div>
                                    <div class="pulsante p2 " data-bs-toggle="modal" data-bs-target="#infoProposta'.$n.'_'.$x.'">'.$maggioriInformazioni.' <i class="fa-sharp fa-solid fa-info"></i></div>
                                    <!--INFOR PROPOSTA-->
                                    <div class="modal fade" id="infoProposta'.$n.'_'.$x.'" tabindex="-1">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-body">
                                                    <i class="fa-light fa-circle-xmark chiudimodale" class="btn btn-secondary" data-bs-dismiss="modal"></i>
                                                    '.($servizi!=''?
                                                    '<h4 class="titolo_camera">'.dizionario('SERVIZI_CAMERA').'</h4>
                                                    <div class="testo_camera p-2">'.$servizi.'</div>'
                                                    :'').'
                                                    <h4 class="titolo_camera">'.$TitoloSoggiorno.'</h4>
                                                    <div class="testo_camera p-2">'.$TestoSoggiorno.'</div>
                                                    <h4 class="titolo_camera">'.$TitoloCamera.'</h4>
                                                    <div class="testo_camera p-2">'.$TestoCamera.'</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    </div>
                            </div>'."\r\n";
                $x++;
            }

            if($AccontoPercentuale != 0 && $AccontoImporto == 0) {
                $percentualeCaparra = $AccontoPercentuale.' %';
                $valoreCaparra      = '€ '.number_format(($PrezzoPC*$AccontoPercentuale/100),2,',','.');
                $noFormatValoreCaparra  = ($PrezzoPC*$AccontoPercentuale/100);
            }else{
                $valoreCaparra      = '';
                $noFormatValoreCaparra  = '';
            }
            if($AccontoPercentuale == 0 && $AccontoImporto != 0) {
                if($AccontoImporto >= 1){
                    $percentualeCaparra = '';
                    $valoreCaparra      = '€ '.number_format($AccontoImporto,2,',','.');   
                    $noFormatValoreCaparra  = $AccontoImporto;         
                }else{
                    $percentualeCaparra = '';
                    $valoreCaparra      = dizionario('CARTACREDITOGARANZIA'); 
                    $noFormatValoreCaparra  = '';
                }

            }               

            ## TAG PROPOSTE ##
            $tagProposte .='  <a href="#proposte" onclick="resetProposta();">
                            <div class="col box" proposta="'.$contproposte.'" titolo="'.dizionario('PROPOSTA').' '.$contproposte.'" prezzo="'.$PrezzoP_format.'" sconto="'.($imp_sconto!=''?$imp_sconto.' %':'').'" valore_sconto="'.($imp_sconto!=''?$ImportoSconto:'').'" caparra="'.($percentualeCaparra!=''?$percentualeCaparra:'').'" valore_caparra="'.($valoreCaparra!=''?$valoreCaparra:'').'" totale_camere="'.number_format($totale,2,',','.').'">
                                <div class="testo">'.dizionario('PROPOSTA').'<br>€ '.$PrezzoP_format.'</div>'
                                .$contproposte.'
                            </div>
                        </a>'."\r\n";
            if($TipoRichiesta == 'Preventivo') {
                $FCamere = substr($FCamere,0,-2);
                $valore = ucfirst(strtolower(dizionario('ARRIVO'))).' '.$Arrivo.' - '.ucfirst(strtolower(dizionario('PARTENZA'))).' '.$Partenza.' - '.$FCamere .'  -  '.dizionario('ADULTI').' '.$NumeroAdulti.' '.($NumeroBambini!='0'?' - '.dizionario('BAMBINI').' '.$NumeroBambini:'').'  - Totale €. '.$PrezzoP.'';
                $boxProposta = ucfirst(strtolower(dizionario('ARRIVO'))).' '.$Arrivo.' - '.ucfirst(strtolower(dizionario('PARTENZA'))).' '.$Partenza.'<br>'.dizionario('ADULTI').' '.$NumeroAdulti.' '.($NumeroBambini!='0'?' - '.dizionario('BAMBINI').' '.$NumeroBambini:'');
            }
            ## TAB PROPOSTE ##
            $tabProposte .='<div class="col box" onclick="resetProposta();" proposta="'.$contproposte.'" titolo="'.dizionario('PROPOSTA').' '.$contproposte.'" prezzo="'.$PrezzoP_format.'" sconto="'.($imp_sconto!=''?$imp_sconto.' %':'').'" valore_sconto="'.($imp_sconto!=''?$ImportoSconto:'').'" caparra="'.($percentualeCaparra!=''?$percentualeCaparra:'').'" valore_caparra="'.($valoreCaparra!=''?$valoreCaparra:'').'"  totale_camere="'.number_format($totale,2,',','.').'">
                            <div class="plus" data-bs-toggle="tooltip"  title="'.$selezionaQuestaProposta.'"><i class="fa-solid fa-message-plus"></i></div>
                            <div class="check" data-bs-toggle="tooltip"  title="'.$haiSelezionatoQuestaProposta.'"><i class="fa-solid fa-message-check"></i></div>
                            <div class="titolo">'.dizionario('PROPOSTA').' '.$contproposte.'</div>
                            <div class="prezzo">€ '.$PrezzoP_format.'</div> 
                        </div>'."\r\n";
            ## SCRIPT CHE AGISCE SUI TAG E TAB PROPOSTE E RESETTA TUTI I VALORI ##
            $tabProposte .='<script>
                            function resetProposta() {
                                $(".servizio").removeClass("open");
                                $(".servizio .compreso").addClass("open");
                                $(".re_calc").empty();
                                $(".clean_calc").empty();
                                $(".clean_explane").empty();
                                $("#NewTotale").empty();
                                $("#TextNewTotale").empty();
                                $("#clone").empty();
                                $("button.pulModal.bg-red").html("'.$calcolaCostoServizio.'");
                                $("button.pulModal.bg-red").removeClass("bg-red");
                                $("select.notti[disabled=\'disabled\']").attr("disabled",false);
                                $("select.persone[disabled=\'disabled\']").attr("disabled",false);
                            }
                        </script>'."\r\n";



            $totaleServizi = $this->totaleServizi($idsito,$id_richiesta,$IdProposta,$ANotti,$totale);   
            $proposta .='<div class="proposta_container m-0 p-0" proposta="'.$contproposte.'"  idprop="'.$IdProposta.'" riepilogo="'.$valore.'" confermaProposta="'.$boxProposta.'" totaleServizi="'.$totaleServizi.'">
                            <div class="col-12 p-5 content">'."\r\n";
                if($NomeProposta!='' || $TestoProposta!=''){
                    $proposta .='   <div class="pacchetto">
                                            <b>'.$NomeProposta.'</b>
                                            <p>'.nl2br($TestoProposta).'</p>
                                    </div>'."\r\n";
                }
            $proposta .='            <h3 class="titolo">'.dizionario('PROPOSTA').' '.$contproposte.'</h3>'."\r\n";
            if($AccontoTariffa!='' || $AccontoTesto!=''){        
            $proposta .='        <div class="pulsante p2" data-bs-toggle="modal" data-bs-target="#condizioniTariffarie'.$contproposte.'">'.$visualizzaCondizioniTariffarie.' <i class="fa-sharp fa-solid fa-comments-dollar"></i></div>
                                <!--CONDIZIONI TARIFFARIE-->
                                <div class="modal fade" id="condizioniTariffarie'.$contproposte.'" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-body">
                                                <i class="fa-light fa-circle-xmark chiudimodale" class="btn btn-secondary" data-bs-dismiss="modal"></i>
                                                    <h4>'.($AccontoTariffa!=''?$AccontoTariffa:'').'</h4>
                                                    <p>'.nl2br($AccontoTesto).'</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>'."\r\n";
            }
            $proposta .='     </div>
                            '.$camere.'
                        </div>'."\r\n";



            ## CODICE SERVIZI INCLUSI ##
            $ServiziInclusi = $this->serviziAggiuntivi($idsito, $id_richiesta, $IdProposta, $Lingua, 1);
            $checkServiziInclusi = sizeof($ServiziInclusi);
            $contservizi=1;

            $servInc .= '<!--SERVIZI COMPRESI-->
                            <div class="row servizi_aggiuntivi_compresi" proposta="'.$contproposte.'">
                                <div class="row">
                                    <div class="col p-5 gy-2 text-center">
                                    '.($TipoRichiesta=='Preventivo' ? '<h3>'.$titoloServiziInclusi.'</h3>' :'').'
                                        <strong>'.$sottoTitoloServiziInclusi.'</strong>
                                    </div>
                                </div>
                                <div class="col gy-2 servizi">'."\r\n";

            foreach ($ServiziInclusi as $chiave => $val) {

                switch ($val['tipoCalcolo']) {
                    case "Al giorno":
                        $calcoloprezzo  = dizionario('AL_GIORNO');
                        $num_persone    = '';
                        $num_notti      = ($ANotti!=''?$ANotti:$Notti);
                        $ExplaneCalcolo = ($val['prezzo']!=0?'('.number_format($val['prezzo'],2,',','.').' X '.$num_notti.' gg)': '');
                        $TotServizio    = ($val['prezzo']!=0?'€ '.number_format(($val['prezzo']*$num_notti),2,',','.'):'');
                        $AttributoCalc  = 'valore="'.$val['prezzo'].'#'.$val['tipoCalcolo'].'#'.$val['id'].'"';
                        break;
                    case "A percentuale":
                        $calcoloprezzo  = $A_PERCENTUALE;
                        $num_persone    = '';
                        $num_notti      = '';
                        $ExplaneCalcolo = '';
                        $TotServizio    = '';
                        $AttributoCalc  = 'valore="'.$val['percentuale'].'#'.$val['tipoCalcolo'].'#'.$val['id'].'"';
                        break;
                    case "Una tantum":
                        $calcoloprezzo  = dizionario('UNA_TANTUM');
                        $num_persone    = '';
                        $num_notti      = '';
                        $ExplaneCalcolo = '';
                        $TotServizio    = '';
                        $AttributoCalc  = 'valore="'.$val['prezzo'].'#'.$val['tipoCalcolo'].'#'.$val['id'].'"';
                        break;
                    case "A persona":
                        $calcoloprezzo  = dizionario('A_PERSONA');
                        $num_persone    = $val['num_persone'];
                        $num_notti      = $val['num_notti'];
                        $ExplaneCalcolo = ($val['prezzo']!=0?'('.number_format($val['prezzo'],2,',','.').' X '.$num_notti.' gg X '.$num_persone.' pax)': '('.$num_notti.' gg X '.$num_persone.' pax)');
                        $TotServizio    = ($val['prezzo']!=0?number_format(($val['prezzo']*$num_notti*$num_persone),2,',','.'):'');
                        $AttributoCalc  = 'valore="'.$val['prezzo'].'#'.$val['tipoCalcolo'].'#'.$val['id'].'"';
                        break;
                }

                $servInc .= '<div class="servizio compreso" servizio="'.$contservizi.'" id="PrezzoServizio'.$n.'_'.$val['id'].'" '.$AttributoCalc.'>'."\r\n";
                $servInc .= '<div data-bs-toggle="tooltip" title="'.$val['testo'].'" class="immago" style="'.(strstr($val['img'],".png")?'background-image:url('.config('global.settings.BASE_URL_IMG').'uploads/icon_service/servizio.jpg)':'background-image:url('.config('global.settings.BASE_URL_IMG').'uploads/'.$idsito.'/'.$val['img'].')').'"></div>'."\r\n";
                    $servInc .= '<div data-bs-toggle="tooltip"  title="'.$servizioCompresoProposta.'">'."\r\n";
                    $servInc .= '<div class="titolo nowrap" data-bs-toggle="tooltip" title="'.$val['titolo'].'">' . (strlen($val['titolo'])>20?substr($val['titolo'],0,20).'...':$val['titolo']) . '</div>'."\r\n";
                        $servInc .= '<div class="sottotitolo">'.$calcoloprezzo.'</div>'."\r\n";
                    if($val['tipoCalcolo'] == 'A percentuale' && $val['percentuale'] != ''){
                        $servInc .= '<div class="prezzo">'.$val['percentuale'].'%</div>'."\r\n";
                    }else{
                        if($val['prezzo']>0){
                            $servInc .= '<div class="prezzo">€ '.number_format($val['prezzo'],2,',','.').'</div>'."\r\n";
                        }else{
                            $servInc .= '<div class="prezzo">'.$gratis.'</div>'."\r\n";
                        }
                    }
                    if($val['pre-selezionato'] == 1){
                        $servInc .='<div class="f-11">'.$ExplaneCalcolo.'</div><div class="clearfix m-2"></div><div class="f-12">'.$TotServizio.'</div>'."\r\n";
                    }
                    if($val['testo'] != ''){
                        $servInc .= '<div class="dettagli" data-bs-toggle="modal" data-bs-target="#infoServizio'.$n.'_'.$val['id'].'"><i class="fa-sharp fa-solid fa-circle-info"></i> '.$dettagli.'</div>'."\r\n";
                    }

                    $servInc .= '</div>'."\r\n";
                    $servInc .= '</div>'."\r\n";
                    $servInc .= '
                                <!--INFO SERVIZIO-->
                                <div class="modal fade" id="infoServizio'.$n.'_'.$val['id'].'" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-body">
                                                <i class="fa-light fa-circle-xmark chiudimodale" class="btn btn-secondary" data-bs-dismiss="modal"></i>
                                                <div class="m-2">'.$val['testo'].'</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>'."\r\n";




            $contservizi++;
            }
            $servInc .= '</div>
                    </div>'."\r\n";




            ##  CODICE SERVIZI AGGIUNTIVI ##
            $serviziFac = $this->serviziAggiuntivi($idsito, $id_richiesta, $IdProposta, $Lingua);
            //print_r($serviziFac);
            $checkServizi = sizeof($serviziFac);
            $contserviziFac = 1;
            $PrezzoServizio = '';
            if($checkServizi > 0){
                $servFac .= '<!--SERVIZI AGGIUNTIVI-->
                                <div class="row servizi_aggiuntivi_facoltativi" proposta="'.$contproposte.'">
                                    <div class="row">
                                        <div class="col p-5 gy-2 text-center">
                                        '.($TipoRichiesta=='Preventivo' ? ' <strong>'.$titoloServiziAggiuntivi.'</strong>' :'' ).'
                                        </div>
                                    </div>'."\r\n";
                foreach ($serviziFac as $ch => $vl) {
                    switch ($vl['tipoCalcolo']) {
                        case "Al giorno":
                            $calcoloprezzo  = dizionario('AL_GIORNO');
                            $num_persone    = '';
                            $num_notti      = ($ANotti!= ''?$ANotti:$Notti);
                            $ExplaneCalcolo = ($vl['prezzo']!=0?'('.number_format($vl['prezzo'],2,',','.').' X '.$num_notti .' gg)': '');
                            $TotServizio    = ($vl['prezzo']!=0?'€ '.number_format(($vl['prezzo']*$num_notti),2,',','.'):'');
                            $AttributoCalc  = 'valore="'.$vl['prezzo'].'#'.$vl['tipoCalcolo'].'#'.$vl['id'].'"';
                            break;
                        case "A percentuale":
                            $calcoloprezzo  = $A_PERCENTUALE;
                            $num_persone    = '';
                            $num_notti      = '';
                            $ExplaneCalcolo = '';
                            $TotServizio    = '';
                            $AttributoCalc  = 'valore="'.$vl['percentuale'].'#'.$vl['tipoCalcolo'].'#'.$vl['id'].'"';
                            break;
                        case "Una tantum":
                            $calcoloprezzo  = dizionario('UNA_TANTUM');
                            $num_persone    = '';
                            $num_notti      = '';
                            $ExplaneCalcolo = '';
                            $TotServizio    = '';
                            $AttributoCalc  = 'valore="'.$vl['prezzo'].'#'.$vl['tipoCalcolo'].'#'.$vl['id'].'"';
                            break;
                        case "A persona":
                            $calcoloprezzo  = dizionario('A_PERSONA');
                            $num_persone    = $vl['num_persone'];
                            $num_notti      = $vl['num_notti'];
                            $ExplaneCalcolo = ($vl['prezzo']!=0?'('.number_format($vl['prezzo'],2,',','.').' X '.$num_notti.' gg X '.$num_persone.' pax)': '('.$num_notti.' gg X '.$num_persone.' pax)');
                            $TotServizio    = ($vl['prezzo']!=0?number_format(($vl['prezzo']*$num_notti*$num_persone),2,',','.'):'');
                            $AttributoCalc  = 'valore="'.$vl['prezzo'].'#'.$vl['tipoCalcolo'].'#'.$vl['id'].'"';
                            break;
                    }

                    if (($TipoRichiesta=='Conferma'? $vl['pre-selezionato'] == 1 : $vl['visibile'] == true)) {
                        if ($vl['compreso'] == false) {
                            $servFac .= '<div class="servizio '.($vl['pre-selezionato'] == 1?'compreso open':'').'" servizio="' . $contserviziFac . '" id="PrezzoServizio'.$n.'_'.$vl['id'].'" '.$AttributoCalc.'>'."\r\n";
                            if ($vl['img'] != '') {
                                $servFac .= '<div '.($vl['tipoCalcolo'] == 'A persona' ? 'data-bs-toggle="tooltip" title="'.$prezzoServizio.' € '.number_format($vl['prezzo'],2,',','.').'"' : '').' class="immago" style="' . (strstr($vl['img'], ".png") ? 'background-image:url(' . config('global.settings.BASE_URL_IMG') . 'uploads/icon_service/servizio.jpg)' : 'background-image:url(' . config('global.settings.BASE_URL_IMG') . 'uploads/' . $idsito . '/' . $vl['img'] . ')') . '"></div>'."\r\n";
                            } else {
                                $servFac .= '<div '.($vl['tipoCalcolo'] == 'A persona' ? 'data-bs-toggle="tooltip" title="'.$prezzoServizio.' € '.number_format($vl['prezzo'],2,',','.').'"' : '').' class="immago" style="background-image:url(/img/servizio.jpg)"></div>'."\r\n";
                            }
                            $servFac .= '<div class="titolo nowrap" data-bs-toggle="tooltip" title="'.$vl['titolo'].'">' . (strlen($vl['titolo'])>20?substr($vl['titolo'],0,20).'...':$vl['titolo']) . '</div>'."\r\n";
                            $servFac .= '<div class="sottotitolo">' . $calcoloprezzo . '</div>'."\r\n";
                            if($vl['tipoCalcolo'] == 'A percentuale' && $vl['percentuale'] != ''){
                                $servFac .= '<div class="prezzo">'.$vl['percentuale'].'%</div>'."\r\n";
                            }else{
                                if ($vl['prezzo'] > 0) {
                                    $servFac .= '<div class="prezzo nowrap">€ '.($vl['tipoCalcolo'] == 'A persona' ? number_format($vl['prezzo'],2,',','.').''.($vl['pre-selezionato'] == 0 ?'<br><span class="f-11" id="explane_remove_calcolo'.$n.'_'.$vl['id'].'"> '.$calcolaCostoServizio.'</span>':'') : number_format($vl['prezzo'],2,',','.')).'</div>'."\r\n";
                                } else {
                                    $servFac .= '<div class="prezzo">' . $gratis . '</div>'."\r\n";
                                }
                            }
                            if($vl['pre-selezionato'] == 1){
                                $servFac .='<div class="f-11">'.$ExplaneCalcolo.'</div><div class="clearfix m-2"></div><div class="f-12">'.$TotServizio.'</div>'."\r\n";

                            }else{
                                $servFac .='<div id="response_calc_servizio'.$n.'_'.$vl['id'].'" class="re_calc"></div><div class="f-11 clean_explane" id="explane_calcolo'.$n.'_'.$vl['id'].'"></div><div class="clearfix m-2"></div><div class="f-12 clean_calc" id="totale_calcolo'.$n.'_'.$vl['id'].'"></div>'."\r\n";
                            }

                            if ($vl['testo'] != '') {
                                $servFac .= '<div class="dettagli" id="dettaglio'.$n.'_'.$vl['id'].'" data-bs-toggle="tooltip"  title="' . $visualizzaMaggioriInformazioni . '" ><t data-bs-toggle="modal" data-bs-target="#infoServizio'.$n.'_'.$vl['id'].'"><i class="fa-sharp fa-solid fa-circle-info"></i> ' . $dettagli . '</t></div>'."\r\n";
                            }
                            if($vl['pre-selezionato'] != 1){
                                if ($vl['pulCalcolo'] != 1) {
                                    $servFac .= '<div class="plus" data-bs-toggle="tooltip"  title="' . $aggiungiQuestoServizio . '"><i class="fa-solid fa-message-plus"></i></div>'."\r\n";
                                
                                    $servFac .= '<div class="check" data-bs-toggle="tooltip"  title="' . $haiSelezionatoQuestoServizio . '"><i class="fa-solid fa-message-check"></i></div>'."\r\n";
                                }
                            }
                            if ($vl['pulCalcolo'] == 1) {
                                if($vl['pre-selezionato'] == 0){
                                    $servFac .= '<div data-bs-toggle="modal" data-bs-target="#calcoloServizio'.$n.'_'.$vl['id'].'"><i class="fa-solid fa-calculator-simple calc" data-bs-toggle="tooltip"  title="' . $calcolaCostoServizio . '"></i></div>'."\r\n";
                                }       
                            }
                            
                            $servFac .= '</div>'."\r\n";
                        
                            if($vl['pre-selezionato'] == 0 && $vl['pulCalcolo'] == 0 ){
                                $servFac .= '<script>
                                                $("#PrezzoServizio'.$n.'_'.$vl['id'].'").on("click",function () {

                                                    $("#PrezzoServizio'.$n.'_'.$vl['id'].'").toggleClass("open");

                                                    if($(this).hasClass("open")==true){

                                                        var input_on = \'<input type="hidden" id="PrezzoServizioClone'.$n.'_'.$vl['id'].'" name="PrezzoServizioClone'.$n.'['.$vl['id'].']">\';
                                                        $("#clone").append(input_on);
                                                    
                                                        var check = 1;

                                                    }else{
                                                    
                                                        $("#PrezzoServizioClone'.$n.'_'.$vl['id'].'").remove();
                                                    
                                                        var check = 0;
                                                    }

                                                    var notti               = '.($ANotti!=''?$ANotti: $Notti).'
                                                    var tipoCalcolo         = "'.$vl['tipoCalcolo'].'";
                                                    var prezzoServizio      = '.$vl['prezzo'].';
                                                    var percentualeServizio = "'.$vl['percentuale'].'";
                                                    var idsito              = '.$idsito.';
                                                    var n_proposta          = '.$n.';
                                                    var id_richiesta        = '.$id_richiesta.';
                                                    var id_proposta         = '.$IdProposta.';
                                                    var id_servizio         = '.$vl['id'].';
                                                    var totaleServizi       = $(".totale_servizi").text();
                                                    var totaleProposta      = $(".totale").text();
                                                    var totaleSconto        = $(".valore_sconto").text();
                                                    var totaleCaparra       = $(".valore_caparra").text();
                                                    var percentualeCaparra  = $(".percentuale_caparra").text();

                                                    $.ajax({
                                                        type: "POST",
                                                        url: "/calc_prezzo_servizio",
                                                        data: {     "_token"             : "' . csrf_token() . '",
                                                                    "notti"              : notti,
                                                                    "idsito"             : idsito,
                                                                    "n_proposta"         : n_proposta,
                                                                    "id_servizio"        : id_servizio,
                                                                    "id_richiesta"       : id_richiesta,
                                                                    "id_proposta"        : id_proposta,
                                                                    "tipoCalcolo"        : tipoCalcolo,
                                                                    "prezzoServizio"     : prezzoServizio,
                                                                    "percentualeServizio": percentualeServizio,
                                                                    "totaleServizi"      : totaleServizi,
                                                                    "totaleProposta"     : totaleProposta,
                                                                    "totaleSconto"       : totaleSconto,
                                                                    "totaleCaparra"      : totaleCaparra,
                                                                    "percentualeCaparra" : percentualeCaparra,
                                                                    "check"              : check
                                                                },
                                                        success: function(response){
                                                            
                                                            $("#response_calc_servizio'.$n.'_'.$vl['id'].'").html(response);
                                                        },
                                                        error: function(){
                                                            alert("Chiamata fallita, si prega di riprovare...");
                                                        }
                                                    });
                                                    
                                                })
                                            </script>'."\r\n";
                            }

                            $servFac .= '<!--INFO SERVIZIO-->
                                        <div class="modal fade" id="infoServizio'.$n.'_'.$vl['id'].'" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-body">
                                                        <i class="fa-light fa-circle-xmark chiudimodale" class="btn btn-secondary" data-bs-dismiss="modal"></i>
                                                        <div class="m-2">'.$vl['testo'].'</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>'."\r\n";

                            $servFac .= '<!--CALCOLO SERVIZIO-->
                                    <div class="modal fade" id="calcoloServizio'.$n.'_'.$vl['id'].'" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-body">
                                                    <i class="fa-light fa-circle-xmark chiudimodale" class="btn btn-secondary" data-bs-dismiss="modal"></i>
                                                    <div class="row small">
                                                        <div class="col-md-4 small nowrap">
                                                            <div class="form-group">
                                                                <label for="prezzo' . $n . '_' . $vl['id'] . '">'.$LabelPrezzoServizio .'</label>
                                                                <input type="text" id="prezzo' . $n . '_' . $vl['id'] . '" name="prezzo' . $n . '_' . $vl['id'] . '" class="form-control" value="' . $vl['prezzo'] . '" readonly />
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 small nowrap">
                                                            <div class="form-group">
                                                                    <label for="Nnotti' . $n . '_' . $vl['id'] . '">'.$NumeroGiorni.'</label>
                                                                    <select id="Nnotti' . $n . '_' . $vl['id'] . '" name="Nnotti' . $n . '_' . $vl['id'] . '"  class="form-control notti" >  
                                                                        <option value="1" '.(($ANotti!=''?$ANotti: $Notti)==1?'selected="selected"':'').'>1</option>
                                                                        <option value="2" '.(($ANotti!=''?$ANotti: $Notti)==2?'selected="selected"':'').'>2</option>
                                                                        <option value="3" '.(($ANotti!=''?$ANotti: $Notti)==3?'selected="selected"':'').'>3</option>
                                                                        <option value="4" '.(($ANotti!=''?$ANotti: $Notti)==4?'selected="selected"':'').'>4</option>
                                                                        <option value="5" '.(($ANotti!=''?$ANotti: $Notti)==5?'selected="selected"':'').'>5</option>
                                                                        <option value="6" '.(($ANotti!=''?$ANotti: $Notti)==6?'selected="selected"':'').'>6</option>
                                                                        <option value="7" '.(($ANotti!=''?$ANotti: $Notti)==7?'selected="selected"':'').'>7</option>
                                                                        <option value="8" '.(($ANotti!=''?$ANotti: $Notti)==8?'selected="selected"':'').'>8</option>
                                                                        <option value="9" '.(($ANotti!=''?$ANotti: $Notti)==9?'selected="selected"':'').'>9</option>
                                                                        <option value="10" '.(($ANotti!=''?$ANotti: $Notti)==10?'selected="selected"':'').'>10</option>
                                                                        <option value="11" '.(($ANotti!=''?$ANotti: $Notti)==11?'selected="selected"':'').'>11</option>
                                                                        <option value="12" '.(($ANotti!=''?$ANotti: $Notti)==12?'selected="selected"':'').'>12</option>
                                                                        <option value="13" '.(($ANotti!=''?$ANotti: $Notti)==13?'selected="selected"':'').'>13</option>
                                                                        <option value="14" '.(($ANotti!=''?$ANotti: $Notti)==14?'selected="selected"':'').'>14</option>
                                                                        <option value="15" '.(($ANotti!=''?$ANotti: $Notti)==15?'selected="selected"':'').' >15</option>
                                                                        <option value="16" '.(($ANotti!=''?$ANotti: $Notti)==16?'selected="selected"':'').'>16</option>
                                                                        <option value="17" '.(($ANotti!=''?$ANotti: $Notti)==17?'selected="selected"':'').'>17</option>
                                                                        <option value="18" '.(($ANotti!=''?$ANotti: $Notti)==18?'selected="selected"':'').'>18</option>
                                                                        <option value="19" '.(($ANotti!=''?$ANotti: $Notti)==19?'selected="selected"':'').'>19</option>
                                                                        <option value="20" '.(($ANotti!=''?$ANotti: $Notti)==20?'selected="selected"':'').'>20</option>
                                                                        <option value="21" '.(($ANotti!=''?$ANotti: $Notti)==21?'selected="selected"':'').'>21</option>
                                                                        <option value="22" '.(($ANotti!=''?$ANotti: $Notti)==22?'selected="selected"':'').'>22</option>
                                                                        <option value="23" '.(($ANotti!=''?$ANotti: $Notti)==23?'selected="selected"':'').'>23</option>
                                                                        <option value="24" '.(($ANotti!=''?$ANotti: $Notti)==24?'selected="selected"':'').'>24</option>
                                                                        <option value="25" '.(($ANotti!=''?$ANotti: $Notti)==25?'selected="selected"':'').'>25</option>
                                                                        <option value="26" '.(($ANotti!=''?$ANotti: $Notti)==26?'selected="selected"':'').'>26</option>
                                                                        <option value="27" '.(($ANotti!=''?$ANotti: $Notti)==27?'selected="selected"':'').'>27</option>
                                                                        <option value="28" '.(($ANotti!=''?$ANotti: $Notti)==28?'selected="selected"':'').'>28</option>
                                                                        <option value="29" '.(($ANotti!=''?$ANotti: $Notti)==29?'selected="selected"':'').'>29</option>
                                                                        <option value="30" '.(($ANotti!=''?$ANotti: $Notti)==30?'selected="selected"':'').'>30</option>
                                                                    </select>
                                                                </div>
                                                        </div>
                                                        <div class="col-md-4 small nowrap">
                                                            <div class="form-group">
                                                                    <label for="NPersone' . $n . '_' . $vl['id'] . '">' . (strlen($vl['titolo'])>20?substr($vl['titolo'],0,20).'...':$vl['titolo']) . ' '.$ServizioAPersona.'</label>
                                                                    <select id="NPersone' . $n . '_' . $vl['id'] . '" name="NPersone' . $n . '_' . $vl['id'] . '" class="form-control persone" >
                                                                        <option value="0" selected="selected">0</option>
                                                                        <option value="1" '.($NumAdulti==1 ?'selected="selected"':'').'>1</option>
                                                                        <option value="2" '.($NumAdulti==2 ?'selected="selected"':'').'>2</option>
                                                                        <option value="3" '.($NumAdulti==3 ?'selected="selected"':'').'>3</option>
                                                                        <option value="4" '.($NumAdulti==4 ?'selected="selected"':'').'>4</option>
                                                                        <option value="5" '.($NumAdulti==5 ?'selected="selected"':'').'>5</option>
                                                                        <option value="6" '.($NumAdulti==6 ?'selected="selected"':'').'>6</option>
                                                                        <option value="7" '.($NumAdulti==7 ?'selected="selected"':'').'>7</option>
                                                                        <option value="8" '.($NumAdulti==8 ?'selected="selected"':'').'>8</option>
                                                                        <option value="9" '.($NumAdulti==9 ?'selected="selected"':'').'>9</option>
                                                                        <option value="10" '.($NumAdulti==10 ?'selected="selected"':'').'>10</option>
                                                                    </select>
                                                                </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12 text-center">
                                                            <input type="hidden"  id="check' . $n . '_' . $vl['id'] . '" name="check' . $n . '_' . $vl['id'] . '">
                                                            <input type="hidden" id="id_servizio' . $n . '_' . $vl['id'] . '" name="id_servizio' . $n . '_' . $vl['id'] . '" value="' . $vl['id'] . '">
                                                            <button type="button" class="pulsante p-2 pulModal" id="send_re_calc' . $n . '_' . $vl['id'] . '" data-bs-dismiss="modal" aria-label="Close" style="border:0px !important;">'.$calcolaCostoServizio.'</button>
                                                        </div>
                                                    </div>
                                                    <script>
                                                        $(document).ready(function() {
                                                                $("#num_persone_' . $n . '_' . $vl['id'] . '").on("show.bs.modal", function (event) {
                                                                    var button = $(event.relatedTarget);
                                                                    var xnotti = button.data("notti");
                                                                    var prezzo = button.data("prezzo");
                                                                    var id_servizio = button.data("id_servizio");
                                                                    var modal = $(this);
                                                                    modal.find(".modal-body select#Nnotti' . $n . '_' . $vl['id'] . '").val(xnotti);
                                                                    modal.find(".modal-body input#prezzo' . $n . '_' . $vl['id'] . '").val(prezzo);
                                                                    modal.find(".modal-body input#id_servizio' . $n . '_' . $vl['id'] . '").val(id_servizio);
                                                                });
                                                                $("#send_re_calc' . $n . '_' . $vl['id'] . '").on("click",function(){

                                                                    var idsito              = ' . $idsito . ';
                                                                    var n_proposta          = ' . $n . ';
                                                                    var lingua              = "' . $Lingua . '";
                                                                    var id_servizio         = $("#id_servizio' . $n . '_' . $vl['id'] . '").val();
                                                                    var notti               = $("#Nnotti' . $n . '_' . $vl['id'] . '").val();
                                                                    var prezzoServizio      = $("#prezzo' . $n . '_' . $vl['id'] . '").val();
                                                                    var NPersone            = $("#NPersone' . $n . '_' . $vl['id'] . '").val();
                                                                    var id_proposta         = ' . $IdProposta . ';
                                                                    var id_richiesta        = '.$id_richiesta.';
                                                                    var totaleServizi       = $(".totale_servizi").text();
                                                                    var totaleProposta      = $(".totale").text();
                                                                    var totaleCaparra       = $(".valore_caparra").text();
                                                                    var percentualeCaparra  = $(".percentuale_caparra").text();

                                                                    $("#PrezzoServizio'.$n.'_'.$vl['id'].'").toggleClass("open");

                                                                    if($("#PrezzoServizio'.$n.'_'.$vl['id'].'").hasClass("open")==true){

                                                                        var input_on = \'<input type="hidden" id="PrezzoServizioClone'.$n.'_'.$vl['id'].'" name="PrezzoServizioClone'.$n.'['.$vl['id'].']">\';
                                                                        $("#clone").append(input_on);

                                                                        var input_Nnotti = \'<input type="hidden" id="NumeroNotti' . $n . '_' . $vl['id'] . '" name="NumeroNotti' . $n . '_' . $vl['id'] . '" >\';
                                                                        $("#clone").append(input_Nnotti);

                                                                        var input_NPersone = \'<input type="hidden" id="NumeroPersone' . $n . '_' . $vl['id'] . '" name="NumeroPersone' . $n . '_' . $vl['id'] . '">\';
                                                                        $("#clone").append(input_NPersone);

                                                                        var check = 1;

                                                                    }else{
                                                                    
                                                                        $("#NumeroPersone' . $n . '_' . $vl['id'] . '").remove();
                                                                        $("#NumeroNotti' . $n . '_' . $vl['id'] . '").remove();
                                                                        $("#PrezzoServizioClone'.$n.'_'.$vl['id'].'").remove();
                                                                    
                                                                        var check = 0;
                                                                    }
                                                                

                                                                    $("#NumeroNotti' . $n . '_' . $vl['id'] . '").val(notti);
                                                                    $("#NumeroPersone' . $n . '_' . $vl['id'] . '").val(NPersone);

                                                                    $.ajax({
                                                                        type: "POST",
                                                                        url: "/calc_prezzo_servizio_a_persona",
                                                                        data: {
                                                                            "_token"             : "' . csrf_token() . '",
                                                                            "notti"              : notti,
                                                                            "NPersone"           : NPersone,
                                                                            "idsito"             : '.$idsito.',
                                                                            "lingua"             : lingua,
                                                                            "n_proposta"         : n_proposta,
                                                                            "id_servizio"        : id_servizio,
                                                                            "id_richiesta"       : id_richiesta,
                                                                            "id_proposta"        : id_proposta,
                                                                            "prezzoServizio"     : prezzoServizio,
                                                                            "totaleServizi"      : totaleServizi,
                                                                            "totaleProposta"     : totaleProposta,
                                                                            "totaleCaparra"      : totaleCaparra,
                                                                            "percentualeCaparra" : percentualeCaparra,
                                                                            "check"              : check
                                                                        },
                                                                        success: function(response){
                                                                            $("#response_calc_servizio'.$n.'_'.$vl['id'].'").html(response);
                                                                        },
                                                                        error: function(){
                                                                            alert("Chiamata fallita, si prega di riprovare...");
                                                                        }
                                                                    });

                                                            });

                                                        });
                                                    </script>
                                                </div>
                                            </div>
                                        </div>
                                    </div>'."\r\n";

                            $contserviziFac++;
                        }
                    }
                }
                $servFac .='</div>'."\r\n";


            }
            ##  FINE CODICE SERVIZI AGGIUNTIVI ##



            $contproposte++;
            $n++;
        }

    }
    ## FINE TAG PROPOSTE ##

    ## INFO BOX ##
    $arrayInfoBox = $this->infoBox($idsito, $Id, $Lingua);
    $infobox = '';
    if (!empty($arrayInfoBox) && !is_null($arrayInfoBox)) {
        foreach ($arrayInfoBox as $key => $value) {
            $infobox .= '<div class="tag">
                        <strong>' . $value->Titolo . '</strong><br>
                        ' . $value->Descrizione . '
                    </div>' . "\r\n";
        }
    }
    ## FINE INFO BOX ##

    ## INFORMAZIONI HOTEL ##
    $info = $this->informazioniHotel($idsito, $Lingua);
    $infoHotel = '';
    $infohotelTitolo = '';
    $infohotelTesto = '';
    if ($info != '') {
        $infohotelTitolo = $info->Titolo;
        $infohotelTesto = $info->Descrizione;
        $infoHotel = '<div class="row boxcontent m-2">
                    <div class="col p-5 text-left">
                        <h4>' . strtoupper($infohotelTitolo) . '</h4>
                        ' . $infohotelTesto . '
                    </div>
                    </div>' . "\r\n";
    }
    ## FINE INFORMAZIONI HOTEL ##

    ## CONDIZIONI GENERALI ##
    $condizioni = $this->condizioniGenerali($idsito, $Lingua, $id_politiche);
    $condizioniGenerali = '';
    if ($condizioni != '') {
        $condizioniGenerali = '<a name="condizioni"></a>
                                <div class="row boxcontent m-2">
                                    <div class="col p-5 text-left">
                                        ' . $condizioni . '
                                    </div>
                                </div>' . "\r\n";
    }
    ## FINE CONDIZIONI GENERALI ##

    ## FOTO GALLERY ##
    $gallery = $this->gallery($idsito, $template);
    ## FINE FOTO GALLERY ##

    ## GOOGLE MAP ##
    if ($abilita_mappa == 1) {
        if ($LatCliente != '' && $LonCliente != '') {
            $Mappa = ' <div class="col-12 col-md-7 p-0 m-0 order-1 order-md-1">
                    <div class="GM2">
                        <div id="map-container" class="google"></div>
                    </div>
                </div>';
        }
    } else {
        $Mappa = '';
    }
    ## GOOGLE MAP ##

    ## BOX BANNER COVID ##
    $bannerCovid = $this->contentBanner($idsito, $Lingua, $Logo);
    ## FINE BOX BANNER COVID ##

    ## BOX EVENTI ##
    $Eventi = $this->eventi($idsito, $Lingua, $DataArrivo, $LatCliente, $LonCliente);
    ## FINE BOX EVENTI ##

    ## BOX PDI ##
    $puntiInteresse = $this->punti_interesse($idsito, $Lingua, $LatCliente, $LonCliente);
    ## FINE BOX PDI ##


    $tot_cc = $this->tot_check_pagamento($idsito,$id_richiesta,'Carta di Credito');
    $tot_vp = $this->tot_check_pagamento($idsito,$id_richiesta,'Vaglia Postale');
    $tot_bn = $this->tot_check_pagamento($idsito,$id_richiesta,'Bonifico Bancario');







    return view('pro_template/index',
                                            [
                                                'directory'               => $directory,
                                                'id_richiesta'            => $id_richiesta,
                                                'idsito'                  => $idsito,
                                                'tipo'                    => $tipo,
                                                'Lingua'                  => $Lingua,
                                                'NomeCliente'             => session('NomeCliente'),
                                                'abilita_mappa'           => $abilita_mappa,
                                                'latitudine'              => $LatCliente,
                                                'longitudine'             => $LonCliente,
                                                'tot_cc'                  => $tot_cc,
                                                'tot_vp'                  => $tot_vp,
                                                'tot_bn'                  => $tot_bn,
                                                'tot_cc_check'            => $this->chek_pagamento_cc($idsito,$id_richiesta),
                                                'Nprenotazione'           => $Nprenotazione,
                                                'IdSito'                  => $idsito,
                                                'head_tagmanager'         => $head_tagmanager,
                                                'overfade'                => $overfade,
                                                'body_tagmanager'         => $body_tagmanager,
                                                'SitoWeb'                 => $SitoWeb,
                                                'result'                  => ($request->result != '' ? $request->result: ''),
                                                'logoFooter'              => $logoFooter,
                                                'logoTop'                 => $logoTop,
                                                'TipoRichiesta'           => $TipoRichiesta,
                                                'Chiuso'                  => $Chiuso,
                                                'AccontoRichiesta'        => $AccontoRichiesta,
                                                'AccontoLibero'           => $AccontoLibero,
                                                'AccontoPercentuale'      => $AccontoPercentuale,
                                                'AccontoImporto'          => $AccontoImporto,
                                                'Nome'                    => $Nome,
                                                'Cognome'                 => $Cognome,
                                                'NumeroPrenotazione'      => $NumeroPrenotazione,
                                                'DataRichiesta'           => $DataRichiesta,
                                                'DataScadenza'            => $DataScadenza,
                                                'ordinamento_pagamenti'   => $this->ordinamento_pagamenti($idsito,$id_richiesta,$request),
                                                'testo_messaggio'         => $testo_messaggio,
                                                'Cellulare'               => $Cellulare,
                                                'sistemazione'            => $sistemazione,
                                                'testo_saluti'            => $testo_saluti,
                                                'InformativaPrivacy'      => '',
                                                'bannerCovid'             => $bannerCovid,
                                                'testo_riferimento'       => $testo_riferimento,
                                                'EmailCliente'            => $EmailCliente,
                                                'Email'                   => $Email,
                                                'Cliente'                 => $Cliente,
                                                'Id'                      => $id_richiesta,
                                                'testo'                   => dizionario('INFORMATIVA_PRIVACY'),
                                                'check_preno_esiste'      => $this->check_preno_esiste($Nprenotazione, $idsito),
                                                'IdRichiesta'             => $id_richiesta,
                                                'Operatore'               => $Operatore,
                                                'Testo'                   => $Testo,
                                                'DataArrivo'              => $DataArrivo,
                                                'DataPartenza'            => $DataPartenza,
                                                'Arrivo'                  => $Arrivo,
                                                'Partenza'                => $Partenza,
                                                'adulti'                  => $adulti,
                                                'bambini'                 => $bambini,
                                                'eta'                     => $EtaB,
                                                'infoHotel'               => $infoHotel,
                                                'Eventi'                  => $Eventi,
                                                'puntiInteresse'          => $puntiInteresse,
                                                'Mappa'                   => $Mappa,
                                                'condizioniGenerali'      => $condizioniGenerali,
                                                'Indirizzo'               => $Indirizzo,
                                                'Localita'                => $Localita,
                                                'Provincia'               => $Provincia,
                                                'Cap'                     => $Cap,
                                                'CIR'                     => $CIR,
                                                'CIN'                     => $CIN,
                                                'Facebook'                => $Facebook,
                                                'Twitter'                 => $Twitter,
                                                'Instagram'               => $Instagram,
                                                'Pinterest'               => $Pinterest,
                                                'tel'                     => $tel,
                                                'imgTop'                  => $imgTop,
                                                'tagProposte'             => $tagProposte,
                                                'gentile'                 => $gentile,
                                                'infobox'                 => $infobox,
                                                'streamVideo'             => $streamVideo,
                                                'titoloProposte'          => $titoloProposte,
                                                'tabProposte'             => $tabProposte,
                                                'proposta'                => $proposta,
                                                'checkServiziInclusi'     => $checkServiziInclusi,
                                                'check_controllo_servizi' => $this->check_controllo_servizi($idsito),
                                                'servInc'                 => $servInc,
                                                'textTotale'              => $textTotale,
                                                'textInfoSconto'          => $textInfoSconto,
                                                'scadenza'                => $scadenza,
                                                'numero'                  => $numero,
                                                'data'                    => $data,
                                                'selezionaAltraProposta'  => $selezionaAltraProposta,
                                                'tooltipChat'             => $tooltipChat,
                                                'serviziFac'              => $serviziFac,
                                                'servFac'                 => $servFac,
                                                'servizi'                 => $servizi,
                                                'fraseChat'               => $fraseChat,
                                                'gallery'                 => $gallery,
                                                'Nproposte'               => $Nproposte,

                                            ]
                                        );
    }
}
