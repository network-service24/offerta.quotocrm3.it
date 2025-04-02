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

class SmartController extends Controller
{

    public function get_modifica_servizi_aggiuntivi($n,$id_richiesta,$id_proposta,$Lingua)
    {
        global $db4,$db5,$Notti,$ANotti,$DataArrivo,$Arrivo,$DataPartenza,$Partenza,$PrezzoPC,$DataRichiestaCheck;
      
            $q = "SELECT * FROM hospitality_relazione_servizi_proposte WHERE id_richiesta = ".$id_richiesta." AND id_proposta = ".$id_proposta;
            $r = $db4->query($q);
            $IdServizio = array();
            while($v = $db4->fetch($r)){
                $IdServizio[$v['servizio_id']]=1;
            }
                    // Query per servizi aggiuntivi
            $query  = "SELECT hospitality_tipo_servizi.* FROM hospitality_tipo_servizi
                            WHERE hospitality_tipo_servizi.idsito = ".IDSITO."
                            AND hospitality_tipo_servizi.Abilitato = 1
                            ORDER BY hospitality_tipo_servizi.Ordine ASC, hospitality_tipo_servizi.TipoServizio ASC";
            $risultato_query = $db4->query($query);
            $record          = $db4->num_rows($risultato_query);
            if(($record)>0){
      
              switch($Lingua){
                case "it":
                  $ABILITA      = 'Aggiungi Servizio';
                  $OBBLIGATORIO = 'Incluso';
                  $IMPOSTO      = 'Incluso in questa proposta';
                break;
                case "en":
                  $ABILITA      = 'Add Service';
                  $OBBLIGATORIO = 'Included';
                  $IMPOSTO      = 'Included in this proposal ';
                break;
                case "fr":
                  $ABILITA      = 'Ajouter un service';
                  $OBBLIGATORIO = 'Inclus';
                  $IMPOSTO      = 'Inclus dans cette proposition ';
                break;
                case "de":
                  $ABILITA      = 'Service hinzufügen';
                  $OBBLIGATORIO = 'Inbegriffen ';
                  $IMPOSTO      = 'In diesem Vorschlag enthalten';
                break;
              }
      
                $lista_servizi_aggiuntivi .='
                                               
                                                      <div class="m m-x-2 m-s-0 m-x-tl t18 w300 boxservizi">'.SERVIZIO.'</div>
                                                      <div class="m m-x-2 m-s-0 m-x-tl  t18 w300 boxservizi"></div>
                                                      <div class="m m-x-3 m-s-0 m-x-tl  t18 w300 boxservizi">'.CALCOLO.'</div>
                                                      <div class="m m-x-1 m-s-0 m-x-tl  t18 w300 nowrap boxservizi" id="add-serv">'.$ABILITA.'</div>
                                                      <div class="m m-x-1 m-s-0 m-x-tl  t18 w300 boxservizi"></div>
                                                      <div class="m m-x-3 m-s-0 m-x-tr  t18 w300 nowrap boxservizi" id="price-serv">'.PREZZO_SERVIZIO.'</div>
                                                
                                                  <div class="ca10"></div>';
      
                while($campo = $db4->fetch($risultato_query)){
      
                    $q   = "SELECT hospitality_tipo_servizi_lingua.Descrizione,hospitality_tipo_servizi_lingua.Servizio FROM hospitality_tipo_servizi_lingua  WHERE hospitality_tipo_servizi_lingua.servizio_id = ".$campo['Id']." AND hospitality_tipo_servizi_lingua.idsito = ".IDSITO." AND hospitality_tipo_servizi_lingua.lingue = '".$Lingua."'";
                    $r   = $db5->query($q);
                    $rec = $db5->fetch($r);
      
                    $qrel   = "SELECT hospitality_relazione_servizi_proposte.id as id_relazionale,hospitality_relazione_servizi_proposte.num_persone,hospitality_relazione_servizi_proposte.num_notti FROM hospitality_relazione_servizi_proposte WHERE hospitality_relazione_servizi_proposte.id_richiesta = ".$id_richiesta." AND hospitality_relazione_servizi_proposte.id_proposta = ".$id_proposta." AND hospitality_relazione_servizi_proposte.servizio_id = ".$campo['Id'];
                    $rel    = $db5->query($qrel);
                    $recrel = $db5->fetch($rel);
      
                    $s  = "SELECT hospitality_relazione_visibili_servizi_proposte.visibile FROM hospitality_relazione_visibili_servizi_proposte  WHERE hospitality_relazione_visibili_servizi_proposte.id_richiesta = ".$id_richiesta." AND hospitality_relazione_visibili_servizi_proposte.id_proposta = ".$id_proposta." AND hospitality_relazione_visibili_servizi_proposte.servizio_id = ".$campo['Id']."";
                    $ss = $db5->query($s);
                    $rs = $db5->fetch($ss);
      
                    if($TipoRichiesta=='Preventivo'){
                      if($DataArrivo != $Arrivo || $DataPartenza != $Partenza){
                        $n_notti = $ANotti;
                      }else{
                        $n_notti = $Notti;
                      }
                    }elseif($TipoRichiesta=='Conferma'){
                      if($DataArrivo != $Arrivo ){
                          $n_notti = $ANotti;
                      }
                      if($DataPartenza != $Partenza){
                          $n_notti = $ANotti;
                      }
                    }
                    switch($Lingua){
                      case "it":
                        $A_PERCENTUALE = 'A percentuale';
                        $CLICKVIEW     = 'Clicca per visualizzare la spiegazione!';
                        $TEXT_EXPLANE  = '<small><small>Il calcolo "A percentuale" <a href="javascript:;" id="pul_long_text_percent'.$n.'_'.$campo['Id'].'" title="'.$CLICKVIEW.'">...<i class="fa fa-info-circle"></i></a> <span id="long_text_percent'.$n.'_'.$campo['Id'].'" style="display:none">viene effettuato sull\'importo originale della proposta ('.number_format($PrezzoPC,2,',','.').')<br>Ossia sul totale soggiorno prima di qualsiasi intervento sui servizi aggiuntivi!</span></small></small>';
                      break;
                      case "en":
                        $A_PERCENTUALE = 'By percentage';
                        $CLICKVIEW     = 'Click to view the explanation!';
                        $TEXT_EXPLANE  = '<small><small>The "A percentage" <a href="javascript:;" id="pul_long_text_percent'.$n.'_'.$campo['Id'].'" title="'.$CLICKVIEW.'">...<i class="fa fa-info-circle"></i></a> <span id="long_text_percent'.$n.'_'.$campo['Id'].'" style="display:none"> calculation is made on the original amount of the proposal ('.number_format($PrezzoPC,2,',','.').')<br>That is on the total stay before any intervention on additional services! </span></small></small>';
                      break;
                      case "fr":
                        $A_PERCENTUALE = 'Par pourcentage';
                        $CLICKVIEW     = 'Cliquez pour voir l\'explication!';
                        $TEXT_EXPLANE  = '<small><small>Le calcul du "pourcentage A" <a href="javascript:;" id="pul_long_text_percent'.$n.'_'.$campo['Id'].'" title="'.$CLICKVIEW.'">...<i class="fa fa-info-circle"></i></a> <span id="long_text_percent'.$n.'_'.$campo['Id'].'" style="display:none"> est effectué sur le montant initial de la proposition  ('.number_format($PrezzoPC,2,',','.').')<br>Soit sur le séjour total avant toute intervention sur des prestations complémentaires! </span></small></small>';
                      break;
                      case "de":
                        $A_PERCENTUALE = 'In Prozent';
                        $CLICKVIEW     = 'Klicken Sie hier, um die Erklärung anzuzeigen!';
                        $TEXT_EXPLANE  = '<small><small>Die Berechnung "Ein Prozentsatz" <a href="javascript:;" id="pul_long_text_percent'.$n.'_'.$campo['Id'].'" title="'.$CLICKVIEW.'">...<i class="fa fa-info-circle"></i></a> <span id="long_text_percent'.$n.'_'.$campo['Id'].'" style="display:none"> erfolgt anhand des ursprünglichen Betrags des Vorschlags ('.number_format($PrezzoPC,2,',','.').')<br>Das ist der Gesamtaufenthalt vor jeder Intervention bei zusätzlichen Dienstleistungen!</span></small></small>';
                      break;
                    }
                    switch($campo['CalcoloPrezzo']){
                      case "Al giorno":
                          $calcoloprezzo = AL_GIORNO;
                          $obbligatory   = ($campo['Obbligatorio']==1?' <small>('.$OBBLIGATORIO.')</small>':'');
                          $num_persone   = '';
                          $CalcoloPrezzoServizio = ($campo['PrezzoServizio']!=0?'<small>('.number_format($campo['PrezzoServizio'],2,',','.').' x '.($ANotti!=''?$ANotti:$Notti).')</small>':'');
                          $PrezzoServizio = ($campo['PrezzoServizio']!=0?'<i class="fal fa-euro-sign"></i>&nbsp;&nbsp;'.number_format(($campo['PrezzoServizio']*($ANotti!=''?$ANotti:$Notti)),2,',','.'):'<small class="text-green">Gratis</small>');
                      break;
                      case "A percentuale":
                        $calcoloprezzo = $A_PERCENTUALE;
                        $obbligatory   = ($campo['Obbligatorio']==1?' <small>('.$OBBLIGATORIO.')</small>':'');
                        $num_persone   = '';
                        $CalcoloPrezzoServizio = '';
                        $PrezzoServizio = ($campo['PercentualeServizio']!=''?'<i class="fa fa-percent"></i>&nbsp;&nbsp;'.number_format(($campo['PercentualeServizio']),2):'');
                      break;
                      case "Una tantum":
                          $calcoloprezzo = UNA_TANTUM;
                          $obbligatory   = ($campo['Obbligatorio']==1?' <small>('.$OBBLIGATORIO.')</small>':'');
                          $num_persone   = '';
                          $CalcoloPrezzoServizio = '';
                          $PrezzoServizio = ($campo['PrezzoServizio']!=0?'<i class="fal fa-euro-sign"></i>&nbsp;&nbsp;'.number_format($campo['PrezzoServizio'],2,',','.'):'<small class="text-green">Gratis</small>');
                      break;
                      case "A persona":
                        $calcoloprezzo = A_PERSONA;
                        $obbligatory   = ($campo['Obbligatorio']==1?' <small>('.$OBBLIGATORIO.')</small>':'');
                        $num_persone   = $recrel['num_persone'];
                        $num_notti     = $recrel['num_notti'];
                        $CalcoloPrezzoServizio = ($campo['PrezzoServizio']!=0?'<small>('.number_format($campo['PrezzoServizio'],2,',','.').' x '.$num_notti.' <span style="font-size:80%">gg</span> x '.$num_persone.' <small>pax</small>)</small>':'('.$num_notti.'  <small>gg</small> x '.$num_persone.' <small>pax</small>)');
                        $PrezzoServizio = ($campo['PrezzoServizio']!=0?'&nbsp;&nbsp;'.number_format(($campo['PrezzoServizio']*$num_notti*$num_persone),2,',','.'):'<small class="text-green">Gratis</small>');
                      break;
                    }
            
              if($DataRichiestaCheck >= DATA_SERVIZI_VISIBILI){
                if($rs['visibile']==1){             
                      $lista_servizi_aggiuntivi .=' <div class="m m-x-12 rigaservizi">
                                                    <div class="m m-x-2 m-s-12 m-x-tl boxservizi titolo"><strong><span class="nowrap">'
                                                    .($campo['Icona']!=''?'<img id="TD'.$campo['Id'].'" src="'.BASE_URL_SITO.'uploads/'.IDSITO.'/'.$campo['Icona'].'" class="iconaservizi">&nbsp;':'').$rec['Servizio'].'</span></strong></div>
                                                    <div class="m m-x-2 m-s-12 m-x-tc boxservizi">'.($rec['Descrizione']!=''?'<a href="javascript:;" data-toggle="tooltip" title="'.(strlen($rec['Descrizione'])<=300?stripslashes(strip_tags($rec['Descrizione'])):substr(stripslashes(strip_tags($rec['Descrizione'])),0,300).'...').'"><i class="fa fa-info-circle text-green" aria-hidden="true"></i></a>':'').' </div>';                                                               
                      $lista_servizi_aggiuntivi .=' <div class="m m-x-3 m-s-12 m-x-tl boxservizi">'.$calcoloprezzo.' '.$CalcoloPrezzoServizio.'</div> ';
                      
                      $lista_servizi_aggiuntivi .=' <div class="m m-x-1 m-s-12 m-x-tl boxservizi nowrap">';
                  if($campo['CalcoloPrezzo'] == 'A percentuale' && $campo['PercentualeServizio'] != ''){ 
                      $lista_servizi_aggiuntivi .='   
                                                        '.($campo['Obbligatorio']==1?$obbligatory.'<div class="text_explan_percent" style="display:none">'.$TEXT_EXPLANE.'</div>':($IdServizio[$campo['Id']]==1?'<small>('.$IMPOSTO.')</small>'.'<div class="text_explan_percent" style="display:none">'.$TEXT_EXPLANE.'</div>':'<input type="checkbox" class="PrezzoServizio'.$n.'"  id="PrezzoServizio'.$n.'_'.$campo['Id'].'" name="PrezzoServizio'.$n.'['.$campo['Id'].']" value="'.$campo['PercentualeServizio'].'#'.$campo['CalcoloPrezzo'].'#'.$campo['Id'].'"  '.($IdServizio[$campo['Id']]==1?'checked="checked"':'').'>'));
                  }else{
                      $lista_servizi_aggiuntivi .='  
                                                        '.($campo['Obbligatorio']==1?$obbligatory:($IdServizio[$campo['Id']]==1?'<small>('.$IMPOSTO.')</small>':'<input type="checkbox" class="PrezzoServizio'.$n.'" id="PrezzoServizio'.$n.'_'.$campo['Id'].'" name="PrezzoServizio'.$n.'['.$campo['Id'].']" value="'.$campo['PrezzoServizio'].'#'.$campo['CalcoloPrezzo'].'#'.$campo['Id'].'" '.($campo['Obbligatorio']==1?'disabled="disabled"':'').' '.($IdServizio[$campo['Id']]==1?'checked="checked"':'').'>'));
                  }   
                      $lista_servizi_aggiuntivi .=' </div>';
                      $lista_servizi_aggiuntivi .=' <div class="m m-x-1 m-s-12 m-x-tl boxservizi"><div id="valori_serv_'.$n.'_'.$campo['Id'].'" class="nowrap" style="font-size:75%"></div><div id="pulsante_calcola_'.$n.'_'.$campo['Id'].'" class="nowrap" style="font-size:75%"></div><div id="spiegazione_prezzo_servizio_'.$n.'_'.$campo['Id'].'" class="nowrap" style="font-size:75%"></div></div>';         
                      $lista_servizi_aggiuntivi .=' <div class="m m-x-3 m-s-3 m-x-tr m-s-tl boxservizi prezzo"><div id="Prezzo_Servizio_'.$n.'_'.$campo['Id'].'">'.$PrezzoServizio.'</div><input type="hidden" name="notti'.$n.'_'.$campo['Id'].'" id="notti'.$n.'_'.$campo['Id'].'"/><input type="hidden" name="num_persone_'.$n.'_'.$campo['Id'].'" id="num_persone_'.$n.'_'.$campo['Id'].'" /><input type="hidden" id="RecPrezzo_Servizio_'.$n.'_'.$campo['Id'].'" name="RecPrezzo_Servizio_'.$n.'_'.$campo['Id'].'"></div>
            
                                                        </div>
                                                        <div class="ca"></div>';
            
                      $modali_servizi_aggiuntivi .=' <script>
                                                            <!-- funzione per eliminare nowrap class-->
                                                            checkScreenDimension("'.$campo['Id'].'");
      
                                                            $(document).ready(function(){
      
                                                              <!-- funzione visualizzare il TUTTO contenuto testuale del servizio a percentuale -->
                                                              $("#pul_long_text_percent'.$n.'_'.$campo['Id'].'").on("click",function(){
                                                                $("#long_text_percent'.$n.'_'.$campo['Id'].'").show(\'slide\');
                                                                $("#pul_long_text_percent'.$n.'_'.$campo['Id'].'").hide();
                                                              });
      
                                                                $("#PrezzoServizio'.$n.'_'.$campo['Id'].'").change(function(){       
                                                                  
                                                                  <!-- funzione visualizzare la prima parte di contenuto testuale del servizio a percentuale -->
                                                                  $(".text_explan_percent").show(\'slide\');
      
                                                                            if(this.checked == true){
      
                                                                              var input_on = \'<input type="hidden" id="PrezzoServizioClone'.$n.'_'.$campo['Id'].'" name="PrezzoServizioClone'.$n.'['.$campo['Id'].']">\';
                                                                              $("#clone").append(input_on);
                                                                             
                                                                              var check = 1;
      
                                                                            }else{
                                                                             
                                                                              $("#PrezzoServizioClone'.$n.'_'.$campo['Id'].'").remove();
                                                                           
                                                                              var check = 0;
                                                                            }
                                                                                                                                          
                                                                            var s_tmp     = "'.$DataArrivo.'";
                                                                            var e_tmp     = "'.$DataPartenza.'";
                                                                            var start_tmp = s_tmp.split("/");
                                                                            var end_tmp   = e_tmp.split("/");
                                                                            var dal       = s_tmp;
                                                                            var al        = e_tmp;
                                                                            var start     = new Date(start_tmp[2],(start_tmp[1]-1),start_tmp[0],24,0,0).getTime()/1000;
                                                                            var end       = new Date(end_tmp[2],(end_tmp[1]-1),end_tmp[0],1,0,0).getTime()/1000;
                                                                            var notti     = '.($ANotti!=''?$ANotti:$Notti).';/*Math.ceil(Math.abs(end - start) / 86400);*/
                                                                            var ReCalPrezzo  = $("#ReCalPrezzo'.$n.'_'.$id_proposta.'").val();
                                                                            var idsito       = '.IDSITO.';
                                                                            var n_proposta   = '.$n.';
                                                                            var id_proposta  = '.$id_proposta.';
                                                                            var id_servizio  = '.$campo['Id'].';
                                                                            var RecPrezzo_Ser= $("#RecPrezzo_Servizio_'.$n.'_'.$campo['Id'].'").val();
                                                                            var ReCalCap     = $("#ReCalCaparra'.$n.'_'.$id_proposta.'").val();
                                                                            var PercCaparra  = $("#PercentualeCaparra'.$n.'_'.$id_proposta.'").val();
                                                                            
                                                                            $.ajax({
                                                                                type: "POST",
                                                                                url: "'.BASE_URL_ROOT.'ajax/calc_prezzo_serv_landing.php",
                                                                                data: "notti=" + notti + "&dal=" + dal + "&al=" + al + "&n_proposta=" + n_proposta + "&id_servizio=" + id_servizio + "&idsito=" + idsito + "&ReCalPrezzo=" + ReCalPrezzo + "&check=" + check + "&RecPrezzo_Ser=" + RecPrezzo_Ser+ "&id_proposta=" + id_proposta+ "&ReCalCaparra=" + ReCalCap+ "&PercCaparra=" + PercCaparra,
                                                                                dataType: "html",
                                                                                success: function(data){
                                                                                    $("#valori_serv_'.$n.'_'.$campo['Id'].'").html(data);
                                                                                    $("#pulsante_calcola_'.$n.'_'.$campo['Id'].'").show();
                                                                                },
                                                                                error: function(){
                                                                                    alert("Chiamata fallita, si prega di riprovare...");
                                                                                }
                                                                            });
                                                                      
                                                                      
                                                                });
      
                                                            });
                                                    </script>
                                                    <div class="modal fade" id="modal_persone_'.$n.'_'.$campo['Id'].'"  role="dialog" aria-labelledby="myModalLabel">
                                                        <div class="modal-dialog" role="document">
                                                            <div class="modal-content" style="overflow:hidden;position:relative;">
                                                                  <div class="modal-header">
                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="overflow:hidden;position:relative;"><span aria-hidden="true">&times;</span></button>
                                                                    <h4 class="modal-title" id="myModalLabel">Inserisci i dati utili per il calcolo del prezzo servizio</h4>
                                                                  </div>
                                                                <div class="modal-body">
                                                                    <div class="row small">
                                                                        <div class="col-md-4 small nowrap">
                                                                            <div class="form-group">
                                                                                <label for="prezzo'.$n.'_'.$campo['Id'].'">Prezzo Servizio</label>
                                                                                <input type="text" id="prezzo'.$n.'_'.$campo['Id'].'" name="prezzo'.$n.'_'.$campo['Id'].'" class="form-control" value="'.$campo['PrezzoServizio'].'" readonly />
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-4 small nowrap">
                                                                            <div class="form-group">
                                                                                    <label for="Nnotti'.$n.'_'.$campo['Id'].'">Numero Giorni</label>
                                                                                    <select id="Nnotti'.$n.'_'.$campo['Id'].'" name="Nnotti'.$n.'_'.$campo['Id'].'"  class="form-control" >
                                                                                        <option value="1">1</option>
                                                                                        <option value="2">2</option>
                                                                                        <option value="3">3</option>
                                                                                        <option value="4">4</option>
                                                                                        <option value="5">5</option>
                                                                                        <option value="6">6</option>
                                                                                        <option value="7">7</option>
                                                                                        <option value="8">8</option>
                                                                                        <option value="9">9</option>
                                                                                        <option value="10">10</option>
                                                                                        <option value="11">11</option>
                                                                                        <option value="12">12</option>
                                                                                        <option value="13">13</option>
                                                                                        <option value="14">14</option>
                                                                                        <option value="15">15</option>
                                                                                        <option value="16">16</option>
                                                                                        <option value="17">17</option>
                                                                                        <option value="18">18</option>
                                                                                        <option value="19">19</option>
                                                                                        <option value="20">20</option>
                                                                                        <option value="21">21</option>
                                                                                        <option value="22">22</option>
                                                                                        <option value="23">23</option>
                                                                                        <option value="24">24</option>
                                                                                        <option value="25">25</option>
                                                                                        <option value="26">26</option>
                                                                                        <option value="27">27</option>
                                                                                        <option value="28">28</option>
                                                                                        <option value="29">29</option>
                                                                                        <option value="30">30</option>
                                                                                    </select>
                                                                                </div>
                                                                        </div>
                                                                        <div class="col-md-4 small nowrap">
                                                                            <div class="form-group">
                                                                                    <label for="NPersone'.$n.'_'.$campo['Id'].'">Numero Persone</label>
                                                                                    <select id="NPersone'.$n.'_'.$campo['Id'].'" name="NPersone'.$n.'_'.$campo['Id'].'" class="form-control" >
                                                                                        <option value="" selected="selected">--</option>
                                                                                        <option value="1">1</option>
                                                                                        <option value="2">2</option>
                                                                                        <option value="3">3</option>
                                                                                        <option value="4">4</option>
                                                                                        <option value="5">5</option>
                                                                                        <option value="6">6</option>
                                                                                        <option value="7">7</option>
                                                                                        <option value="8">8</option>
                                                                                        <option value="9">9</option>
                                                                                        <option value="10">10</option>
                                                                                    </select>
                                                                                </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-md-12 text-center">
                                                                            <input type="hidden" id="check'.$n.'_'.$campo['Id'].'" name="check'.$n.'_'.$campo['Id'].'">
                                                                            <input type="hidden" id="id_servizio'.$n.'_'.$campo['Id'].'" name="id_servizio'.$n.'_'.$campo['Id'].'" value="'.$campo['Id'].'">
                                                                            <button type="button" class="btn btn-success" id="send_re_calc'.$n.'_'.$campo['Id'].'" data-dismiss="modal" aria-label="Close">Calcola prezzo servizio</button>
                                                                        </div>
                                                                    </div>
                                                                    <script>
                                                                        $(document).ready(function() {
                                                                                $("#num_persone_'.$n.'_'.$campo['Id'].'").on("show.bs.modal", function (event) {
                                                                                    var button = $(event.relatedTarget);
                                                                                    var xnotti = button.data("notti");
                                                                                    var prezzo = button.data("prezzo");
                                                                                    var id_servizio = button.data("id_servizio");
                                                                                    var modal = $(this);
                                                                                    modal.find(".modal-body select#Nnotti'.$n.'_'.$campo['Id'].'").val(xnotti);
                                                                                    modal.find(".modal-body input#prezzo'.$n.'_'.$campo['Id'].'").val(prezzo);
                                                                                    modal.find(".modal-body input#id_servizio'.$n.'_'.$campo['Id'].'").val(id_servizio);
                                                                                });
                                                                                $("#send_re_calc'.$n.'_'.$campo['Id'].'").on("click",function(){
                                                                                    var check         = 1;
                                                                                    var idsito        = '.IDSITO.';
                                                                                    var n_proposta    = '.$n.';
                                                                                    var id_servizio   = $("#id_servizio'.$n.'_'.$campo['Id'].'").val();
                                                                                    var notti         = $("#Nnotti'.$n.'_'.$campo['Id'].'").val();
                                                                                    var prezzo        = $("#prezzo'.$n.'_'.$campo['Id'].'").val();
                                                                                    var NPersone      = $("#NPersone'.$n.'_'.$campo['Id'].'").val();
                                                                                    var ReCalPrezzo   = $("#ReCalPrezzo'.$n.'_'.$id_proposta.'").val();
                                                                                    var ReCalCap      = $("#ReCalCaparra'.$n.'_'.$id_proposta.'").val();
                                                                                    var PercCaparra   = $("#PercentualeCaparra'.$n.'_'.$id_proposta.'").val();
                                                                                    var id_proposta   = '.$id_proposta.';
                                                                                    var input_Nnotti = \'<input type="hidden" id="NumeroNotti'.$n.'_'.$campo['Id'].'" name="NumeroNotti'.$n.'_'.$campo['Id'].'" >\';
                                                                                    $("#clone").append(input_Nnotti);
                                                                                    $("#NumeroNotti'.$n.'_'.$campo['Id'].'").val(notti);
                                                                                    var input_NPersone = \'<input type="hidden" id="NumeroPersone'.$n.'_'.$campo['Id'].'" name="NumeroPersone'.$n.'_'.$campo['Id'].'">\';
                                                                                    $("#clone").append(input_NPersone);
                                                                                    $("#NumeroPersone'.$n.'_'.$campo['Id'].'").val(NPersone);
                                                                                    $.ajax({
                                                                                        type: "POST",
                                                                                        url: "'.BASE_URL_ROOT.'ajax/calc_prezzo_serv_a_persona_landing.php",
                                                                                        data: "action=re_calc&notti=" + notti + "&prezzo=" + prezzo + "&NPersone=" + NPersone + "&n_proposta=" + n_proposta + "&id_servizio=" + id_servizio + "&idsito=" + idsito + "&ReCalPrezzo=" + ReCalPrezzo + "&check=" + check+ "&id_proposta=" + id_proposta+ "&ReCalCaparra=" + ReCalCap+ "&PercCaparra=" + PercCaparra,
                                                                                        dataType: "html",
                                                                                        success: function(data){
                                                                                            $("#valori_serv_'.$n.'_'.$campo['Id'].'").html(data);
                                                                                            $("#pulsante_calcola_'.$n.'_'.$campo['Id'].'").hide();
                                                                                            $("input[data-tipo=persone'.$n.'_'.$campo['Id'].']").remove();
                                                                                            $("input[data-tipo=notti'.$n.'_'.$campo['Id'].']").remove();
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
                                                          </div> ';
                      }
                    }else{
                      $lista_servizi_aggiuntivi .=' <div class="m m-x-12 rigaservizi">
                                      <div class="m m-x-2 m-s-12 m-x-tl boxservizi titolo"><strong><span class="nowrap">'
                                      .($campo['Icona']!=''?'<img id="TD'.$campo['Id'].'" src="'.BASE_URL_SITO.'uploads/'.IDSITO.'/'.$campo['Icona'].'" class="iconaservizi">&nbsp;':'').$rec['Servizio'].'</span></strong></div>
                                      <div class="m m-x-2 m-s-12 m-x-tc boxservizi">'.($rec['Descrizione']!=''?'<a href="javascript:;" data-toggle="tooltip" title="'.(strlen($rec['Descrizione'])<=300?stripslashes(strip_tags($rec['Descrizione'])):substr(stripslashes(strip_tags($rec['Descrizione'])),0,300).'...').'"><i class="fa fa-info-circle text-green" aria-hidden="true"></i></a>':'').' </div>';                                                               
                      $lista_servizi_aggiuntivi .=' <div class="m m-x-3 m-s-12 m-x-tl boxservizi">'.$calcoloprezzo.' '.$CalcoloPrezzoServizio.'</div> ';
      
                      $lista_servizi_aggiuntivi .=' <div class="m m-x-1 m-s-12 m-x-tl boxservizi nowrap">';
                      if($campo['CalcoloPrezzo'] == 'A percentuale' && $campo['PercentualeServizio'] != ''){ 
                      $lista_servizi_aggiuntivi .='   
                                          '.($campo['Obbligatorio']==1?$obbligatory.'<div class="text_explan_percent" style="display:none">'.$TEXT_EXPLANE.'</div>':($IdServizio[$campo['Id']]==1?'<small>('.$IMPOSTO.')</small>'.'<div class="text_explan_percent" style="display:none">'.$TEXT_EXPLANE.'</div>':'<input type="checkbox" class="PrezzoServizio'.$n.'"  id="PrezzoServizio'.$n.'_'.$campo['Id'].'" name="PrezzoServizio'.$n.'['.$campo['Id'].']" value="'.$campo['PercentualeServizio'].'#'.$campo['CalcoloPrezzo'].'#'.$campo['Id'].'"  '.($IdServizio[$campo['Id']]==1?'checked="checked"':'').'>'));
                      }else{
                      $lista_servizi_aggiuntivi .='  
                                          '.($campo['Obbligatorio']==1?$obbligatory:($IdServizio[$campo['Id']]==1?'<small>('.$IMPOSTO.')</small>':'<input type="checkbox" class="PrezzoServizio'.$n.'" id="PrezzoServizio'.$n.'_'.$campo['Id'].'" name="PrezzoServizio'.$n.'['.$campo['Id'].']" value="'.$campo['PrezzoServizio'].'#'.$campo['CalcoloPrezzo'].'#'.$campo['Id'].'" '.($campo['Obbligatorio']==1?'disabled="disabled"':'').' '.($IdServizio[$campo['Id']]==1?'checked="checked"':'').'>'));
                      }   
                      $lista_servizi_aggiuntivi .=' </div>';
                      $lista_servizi_aggiuntivi .=' <div class="m m-x-1 m-s-12 m-x-tl boxservizi"><div id="valori_serv_'.$n.'_'.$campo['Id'].'" class="nowrap" style="font-size:75%"></div><div id="pulsante_calcola_'.$n.'_'.$campo['Id'].'" class="nowrap" style="font-size:75%"></div><div id="spiegazione_prezzo_servizio_'.$n.'_'.$campo['Id'].'" class="nowrap" style="font-size:75%"></div></div>';         
                      $lista_servizi_aggiuntivi .=' <div class="m m-x-3 m-s-3 m-x-tr m-s-tl boxservizi prezzo"><div id="Prezzo_Servizio_'.$n.'_'.$campo['Id'].'">'.$PrezzoServizio.'</div><input type="hidden" name="notti'.$n.'_'.$campo['Id'].'" id="notti'.$n.'_'.$campo['Id'].'"/><input type="hidden" name="num_persone_'.$n.'_'.$campo['Id'].'" id="num_persone_'.$n.'_'.$campo['Id'].'" /><input type="hidden" id="RecPrezzo_Servizio_'.$n.'_'.$campo['Id'].'" name="RecPrezzo_Servizio_'.$n.'_'.$campo['Id'].'"></div>
      
                                          </div>
                                          <div class="ca"></div>';
      
                      $modali_servizi_aggiuntivi .=' <script>
                                              <!-- funzione per eliminare nowrap class-->
                                              checkScreenDimension("'.$campo['Id'].'");
      
                                              $(document).ready(function(){
      
                                                <!-- funzione visualizzare il TUTTO contenuto testuale del servizio a percentuale -->
                                                $("#pul_long_text_percent'.$n.'_'.$campo['Id'].'").on("click",function(){
                                                  $("#long_text_percent'.$n.'_'.$campo['Id'].'").show(\'slide\');
                                                  $("#pul_long_text_percent'.$n.'_'.$campo['Id'].'").hide();
                                                });
      
                                                  $("#PrezzoServizio'.$n.'_'.$campo['Id'].'").change(function(){       
                                                    
                                                    <!-- funzione visualizzare la prima parte di contenuto testuale del servizio a percentuale -->
                                                    $(".text_explan_percent").show(\'slide\');
      
                                                              if(this.checked == true){
      
                                                                var input_on = \'<input type="hidden" id="PrezzoServizioClone'.$n.'_'.$campo['Id'].'" name="PrezzoServizioClone'.$n.'['.$campo['Id'].']">\';
                                                                $("#clone").append(input_on);
                                                              
                                                                var check = 1;
      
                                                              }else{
                                                              
                                                                $("#PrezzoServizioClone'.$n.'_'.$campo['Id'].'").remove();
                                                            
                                                                var check = 0;
                                                              }
                                                                                                                            
                                                              var s_tmp     = "'.$DataArrivo.'";
                                                              var e_tmp     = "'.$DataPartenza.'";
                                                              var start_tmp = s_tmp.split("/");
                                                              var end_tmp   = e_tmp.split("/");
                                                              var dal       = s_tmp;
                                                              var al        = e_tmp;
                                                              var start     = new Date(start_tmp[2],(start_tmp[1]-1),start_tmp[0],24,0,0).getTime()/1000;
                                                              var end       = new Date(end_tmp[2],(end_tmp[1]-1),end_tmp[0],1,0,0).getTime()/1000;
                                                              var notti     = '.($ANotti!=''?$ANotti:$Notti).';/*Math.ceil(Math.abs(end - start) / 86400);*/
                                                              var ReCalPrezzo  = $("#ReCalPrezzo'.$n.'_'.$id_proposta.'").val();
                                                              var idsito       = '.IDSITO.';
                                                              var n_proposta   = '.$n.';
                                                              var id_proposta  = '.$id_proposta.';
                                                              var id_servizio  = '.$campo['Id'].';
                                                              var RecPrezzo_Ser= $("#RecPrezzo_Servizio_'.$n.'_'.$campo['Id'].'").val();
                                                              var ReCalCap     = $("#ReCalCaparra'.$n.'_'.$id_proposta.'").val();
                                                              var PercCaparra  = $("#PercentualeCaparra'.$n.'_'.$id_proposta.'").val();
                                                              
                                                              $.ajax({
                                                                  type: "POST",
                                                                  url: "'.BASE_URL_ROOT.'ajax/calc_prezzo_serv_landing.php",
                                                                  data: "notti=" + notti + "&dal=" + dal + "&al=" + al + "&n_proposta=" + n_proposta + "&id_servizio=" + id_servizio + "&idsito=" + idsito + "&ReCalPrezzo=" + ReCalPrezzo + "&check=" + check + "&RecPrezzo_Ser=" + RecPrezzo_Ser+ "&id_proposta=" + id_proposta+ "&ReCalCaparra=" + ReCalCap+ "&PercCaparra=" + PercCaparra,
                                                                  dataType: "html",
                                                                  success: function(data){
                                                                      $("#valori_serv_'.$n.'_'.$campo['Id'].'").html(data);
                                                                      $("#pulsante_calcola_'.$n.'_'.$campo['Id'].'").show();
                                                                  },
                                                                  error: function(){
                                                                      alert("Chiamata fallita, si prega di riprovare...");
                                                                  }
                                                              });
                                                        
                                                        
                                                  });
      
                                              });
                                      </script>
                                      <div class="modal fade" id="modal_persone_'.$n.'_'.$campo['Id'].'"  role="dialog" aria-labelledby="myModalLabel">
                                          <div class="modal-dialog" role="document">
                                              <div class="modal-content" style="overflow:hidden;position:relative;">
                                                    <div class="modal-header">
                                                      <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="overflow:hidden;position:relative;"><span aria-hidden="true">&times;</span></button>
                                                      <h4 class="modal-title" id="myModalLabel">Inserisci i dati utili per il calcolo del prezzo servizio</h4>
                                                    </div>
                                                  <div class="modal-body">
                                                      <div class="row small">
                                                          <div class="col-md-4 small nowrap">
                                                              <div class="form-group">
                                                                  <label for="prezzo'.$n.'_'.$campo['Id'].'">Prezzo Servizio</label>
                                                                  <input type="text" id="prezzo'.$n.'_'.$campo['Id'].'" name="prezzo'.$n.'_'.$campo['Id'].'" class="form-control" value="'.$campo['PrezzoServizio'].'" readonly />
                                                              </div>
                                                          </div>
                                                          <div class="col-md-4 small nowrap">
                                                              <div class="form-group">
                                                                      <label for="Nnotti'.$n.'_'.$campo['Id'].'">Numero Giorni</label>
                                                                      <select id="Nnotti'.$n.'_'.$campo['Id'].'" name="Nnotti'.$n.'_'.$campo['Id'].'"  class="form-control" >
                                                                          <option value="1">1</option>
                                                                          <option value="2">2</option>
                                                                          <option value="3">3</option>
                                                                          <option value="4">4</option>
                                                                          <option value="5">5</option>
                                                                          <option value="6">6</option>
                                                                          <option value="7">7</option>
                                                                          <option value="8">8</option>
                                                                          <option value="9">9</option>
                                                                          <option value="10">10</option>
                                                                          <option value="11">11</option>
                                                                          <option value="12">12</option>
                                                                          <option value="13">13</option>
                                                                          <option value="14">14</option>
                                                                          <option value="15">15</option>
                                                                          <option value="16">16</option>
                                                                          <option value="17">17</option>
                                                                          <option value="18">18</option>
                                                                          <option value="19">19</option>
                                                                          <option value="20">20</option>
                                                                          <option value="21">21</option>
                                                                          <option value="22">22</option>
                                                                          <option value="23">23</option>
                                                                          <option value="24">24</option>
                                                                          <option value="25">25</option>
                                                                          <option value="26">26</option>
                                                                          <option value="27">27</option>
                                                                          <option value="28">28</option>
                                                                          <option value="29">29</option>
                                                                          <option value="30">30</option>
                                                                      </select>
                                                                  </div>
                                                          </div>
                                                          <div class="col-md-4 small nowrap">
                                                              <div class="form-group">
                                                                      <label for="NPersone'.$n.'_'.$campo['Id'].'">Numero Persone</label>
                                                                      <select id="NPersone'.$n.'_'.$campo['Id'].'" name="NPersone'.$n.'_'.$campo['Id'].'" class="form-control" >
                                                                          <option value="" selected="selected">--</option>
                                                                          <option value="1">1</option>
                                                                          <option value="2">2</option>
                                                                          <option value="3">3</option>
                                                                          <option value="4">4</option>
                                                                          <option value="5">5</option>
                                                                          <option value="6">6</option>
                                                                          <option value="7">7</option>
                                                                          <option value="8">8</option>
                                                                          <option value="9">9</option>
                                                                          <option value="10">10</option>
                                                                      </select>
                                                                  </div>
                                                          </div>
                                                      </div>
                                                      <div class="row">
                                                          <div class="col-md-12 text-center">
                                                              <input type="hidden" id="check'.$n.'_'.$campo['Id'].'" name="check'.$n.'_'.$campo['Id'].'">
                                                              <input type="hidden" id="id_servizio'.$n.'_'.$campo['Id'].'" name="id_servizio'.$n.'_'.$campo['Id'].'" value="'.$campo['Id'].'">
                                                              <button type="button" class="btn btn-success" id="send_re_calc'.$n.'_'.$campo['Id'].'" data-dismiss="modal" aria-label="Close">Calcola prezzo servizio</button>
                                                          </div>
                                                      </div>
                                                      <script>
                                                          $(document).ready(function() {
                                                                  $("#num_persone_'.$n.'_'.$campo['Id'].'").on("show.bs.modal", function (event) {
                                                                      var button = $(event.relatedTarget);
                                                                      var xnotti = button.data("notti");
                                                                      var prezzo = button.data("prezzo");
                                                                      var id_servizio = button.data("id_servizio");
                                                                      var modal = $(this);
                                                                      modal.find(".modal-body select#Nnotti'.$n.'_'.$campo['Id'].'").val(xnotti);
                                                                      modal.find(".modal-body input#prezzo'.$n.'_'.$campo['Id'].'").val(prezzo);
                                                                      modal.find(".modal-body input#id_servizio'.$n.'_'.$campo['Id'].'").val(id_servizio);
                                                                  });
                                                                  $("#send_re_calc'.$n.'_'.$campo['Id'].'").on("click",function(){
                                                                      var check         = 1;
                                                                      var idsito        = '.IDSITO.';
                                                                      var n_proposta    = '.$n.';
                                                                      var id_servizio   = $("#id_servizio'.$n.'_'.$campo['Id'].'").val();
                                                                      var notti         = $("#Nnotti'.$n.'_'.$campo['Id'].'").val();
                                                                      var prezzo        = $("#prezzo'.$n.'_'.$campo['Id'].'").val();
                                                                      var NPersone      = $("#NPersone'.$n.'_'.$campo['Id'].'").val();
                                                                      var ReCalPrezzo   = $("#ReCalPrezzo'.$n.'_'.$id_proposta.'").val();
                                                                      var ReCalCap      = $("#ReCalCaparra'.$n.'_'.$id_proposta.'").val();
                                                                      var PercCaparra   = $("#PercentualeCaparra'.$n.'_'.$id_proposta.'").val();
                                                                      var id_proposta   = '.$id_proposta.';
                                                                      var input_Nnotti = \'<input type="hidden" id="NumeroNotti'.$n.'_'.$campo['Id'].'" name="NumeroNotti'.$n.'_'.$campo['Id'].'" >\';
                                                                      $("#clone").append(input_Nnotti);
                                                                      $("#NumeroNotti'.$n.'_'.$campo['Id'].'").val(notti);
                                                                      var input_NPersone = \'<input type="hidden" id="NumeroPersone'.$n.'_'.$campo['Id'].'" name="NumeroPersone'.$n.'_'.$campo['Id'].'">\';
                                                                      $("#clone").append(input_NPersone);
                                                                      $("#NumeroPersone'.$n.'_'.$campo['Id'].'").val(NPersone);
                                                                      $.ajax({
                                                                          type: "POST",
                                                                          url: "'.BASE_URL_ROOT.'ajax/calc_prezzo_serv_a_persona_landing.php",
                                                                          data: "action=re_calc&notti=" + notti + "&prezzo=" + prezzo + "&NPersone=" + NPersone + "&n_proposta=" + n_proposta + "&id_servizio=" + id_servizio + "&idsito=" + idsito + "&ReCalPrezzo=" + ReCalPrezzo + "&check=" + check+ "&id_proposta=" + id_proposta+ "&ReCalCaparra=" + ReCalCap+ "&PercCaparra=" + PercCaparra,
                                                                          dataType: "html",
                                                                          success: function(data){
                                                                              $("#valori_serv_'.$n.'_'.$campo['Id'].'").html(data);
                                                                              $("#pulsante_calcola_'.$n.'_'.$campo['Id'].'").hide();
                                                                              $("input[data-tipo=persone'.$n.'_'.$campo['Id'].']").remove();
                                                                              $("input[data-tipo=notti'.$n.'_'.$campo['Id'].']").remove();
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
                                            </div> ';
      
                    }
                  }
                }
                $lista_servizi_aggiuntivi .= $modali_servizi_aggiuntivi;
            
      
            return '<div class="m m-x-12 t16">'.$lista_servizi_aggiuntivi.'</div><div class="ca10"></div>';
    }



    public function smart_template($directory, $params, Request $request)
    {
        $template = 'smart';
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
            $Facebook   = '<a  href="'.$rw->Facebook.'" target="_blank"><i class="fab fa-facebook-square fa-2x"></i></a>';
        }else{
            $Facebook   = '';
        }
        if($rw->Twitter!=''){
            $Twitter    = '<a  href="'.$rw->Twitter.'" target="_blank"><img src="/img/x-twitter.png" style="margin-top:-18px"></a>';
        }else{
            $Twitter   = '';
        }
        if($rw->GooglePlus!=''){
            $GooglePlus    = '<a  href="'.$rw->GooglePlus.'" target="_blank"><i class="fab fa-google-plus-square fa-2x"></i></a>';
        }else{
            $GooglePlus   = '';
        }
        if($rw->Instagram!=''){
            $Instagram    = '<a  href="'.$rw->Instagram.'" target="_blank"><i class="fab fa-instagram fa-2x"></i></a>';
        }else{
            $Instagram   = '';
        }
        if($rw->Linkedin!=''){
            $Linkedin    = '<a  href="'.$rw->Linkedin.'" target="_blank"><i class="fab fa-linkedin fa-2x"></i></a>' ;
        }else{
            $Linkedin   = '';
        }
        if($rw->Pinterest!=''){
            $Pinterest    = '<a  href="'.$rw->Pinterest.'" target="_blank"><i class="fab fa-pinterest-square fa-2x"></i></a>' ;
        }else{
            $Pinterest   = '';
        }


        $mesi=array('it'=>
                        array("01" => "Gennaio","02" => "Febbraio","03" => "Marzo","04" => "Aprile","05" => "Maggio","06" => "Giugno","07" => "Luglio","08" => "Agosto","09" => "Settembre","10" => "Ottobre","11" => "Novembre","12" => "Dicembre"),
                    'en'=>
                        array("01" => "January","02" => "February","03" => "March","04" => "April","05" => "May","06" => "June","07" => "July","08" => "August","09" => "September","10" => "October","11" => "November","12" => "December"),
                    'fr'=>
                        array("01" => "Janvier","02" => "Février","03" => "Mars","04" => "Avril","05" => "Mai","06" => "Juin","07" => "Juillet","08" => "Août","09" => "Septembre","10" => "Octobre","11" => "Novembre","12" => "Décembre"),
                    'de'=>
                        array("01" => "Januar","02" => "Februar","03" => "März","04" => "April","05" => "Mai","06" => "Juni","07" => "Juli","08" => "August","09" => "September","10" => "Oktober","11" => "November","12" => "Dezember"),
                        );

        // query per estrarre dati della richiesta prenotazione
        $select = "SELECT hospitality_guest.*
                                FROM hospitality_guest
                                WHERE hospitality_guest.idsito = :idsito
                                AND hospitality_guest.Id = :id_richiesta
                                ORDER BY hospitality_guest.Id DESC";
        $sel  = DB::select($select,['idsito' => $idsito, 'id_richiesta' => $id_richiesta]);
        $check_p = sizeof($sel);
        if($check_p==0){
            return redirect('/error?sito='.$SitoWeb);
            exit;
        }else{
            $value = $sel[0];

            Session::put('dati_h_guest', $value);

            $Id                       = $value->Id;
            $Chiuso                   = $value->Chiuso;
            $id_politiche             = $value->id_politiche;
            $AccontoRichiesta         = $value->AccontoRichiesta;
            $AccontoLibero            = $value->AccontoLibero;
            $Operatore                = stripslashes($value->ChiPrenota);
            $TipoRichiesta            = $value->TipoRichiesta;
            $Nome                     = stripslashes($value->Nome);
            $Cognome                  = stripslashes($value->Cognome);
            $Email                    = $value->Email;
            $Cellulare                = $value->Cellulare;
            $Lingua                   = $value->Lingua;
            $DataRichiestaCheck       = $value->DataRichiesta;
            $DataR_tmp                = explode("-",$value->DataRichiesta);
            $DataRichiesta            = $DataR_tmp[2].'/'.$DataR_tmp[1].'/'.$DataR_tmp[0];
            $DataA_tmp                = explode("-",$value->DataArrivo);
            $DataArrivo               = $DataA_tmp[2].'/'.$DataA_tmp[1].'/'.$DataA_tmp[0];
            $DataP_tmp                = explode("-",$value->DataPartenza);
            $DataPartenza             = $DataP_tmp[2].'/'.$DataP_tmp[1].'/'.$DataP_tmp[0];
            if($value->DataValiditaVoucher){
                $DataValiditaVoucher_tmp  = explode("-",$value->DataValiditaVoucher);
                $DataValiditaVoucher      = $DataValiditaVoucher_tmp[2].'/'.$DataValiditaVoucher_tmp[1].'/'.$DataValiditaVoucher_tmp[0];
            }else{
                $DataValiditaVoucher      = '';
            }
            $Nprenotazione            = $value->NumeroPrenotazione;
            $NumeroPrenotazione       = $value->NumeroPrenotazione.'/'.$Id;
            $FontePrenotazione        = $value->FontePrenotazione;
            $NumeroAdulti             = $value->NumeroAdulti;
            $NumeroBambini            = $value->NumeroBambini;
            if($value->DataScadenza){
                $DataS_tmp                = explode("-",$value->DataScadenza);
                $DataScadenza             = $DataS_tmp[2].'/'.$DataS_tmp[1].'/'.$DataS_tmp[0];
                $DataScadenza_estesa = $DataS_tmp[2].' '.$mesi[$Lingua][$DataS_tmp[1]].' '.$DataS_tmp[0];
            }
            $EtaBambini1              = $value->EtaBambini1;
            $EtaBambini2              = $value->EtaBambini2;
            $EtaBambini3              = $value->EtaBambini3;
            $EtaBambini4              = $value->EtaBambini4;
            $EtaBambini5              = $value->EtaBambini5;
            $EtaBambini6              = $value->EtaBambini6;
            $start                    = mktime(24,0,0,$DataA_tmp[1],$DataA_tmp[2],$DataA_tmp[0]);
            $end                      = mktime(01,0,0,$DataP_tmp[1],$DataP_tmp[2],$DataP_tmp[0]);
            $formato                  ="%a";
            $Notti                    = $this->dateDiff($value->DataArrivo,$value->DataPartenza,$formato);
            if($value->DataInvio){
                $DataI_tmp            = explode("-",$value->DataInvio);
                $DataInvio            = $DataI_tmp[2].'/'.$DataI_tmp[1].'/'.$DataI_tmp[0];
            }else{
                $DataInvio            = '';
            }

            $Cliente                  = $Nome.' '.$Cognome;
    
            $DataArrivo_estesa   = $DataA_tmp[2].' '.$mesi[$Lingua][$DataA_tmp[1]].' '.$DataA_tmp[0];
            $DataPartenza_estesa = $DataP_tmp[2].' '.$mesi[$Lingua][$DataP_tmp[1]].' '.$DataP_tmp[0];


            if ($Lingua) {
                session(['LINGUA' => $Lingua]);
            }

            $overfade = '';
            if($value->Chiuso==0){
                if($value->DataScadenza < date('Y-m-d') || $value->Archivia == 1 ){
                   switch($value->Lingua){
                    case"it":
                      $proposta_scaduta_title = 'PROPOSTA SCADUTA';
                      $proposta_scaduta_text  = 'Vuoi riattivare questa proposta di soggiorno, scrivici in chat e ti risponderemo al più presto!';
                    break;
                    case"en":
                      $proposta_scaduta_title = 'PROPOSAL EXPIRED';
                      $proposta_scaduta_text  = 'Do you want to reactivate this stay proposal, write us in chat and we will reply as soon as possible!';
                    break;
                    case"fr":
                      $proposta_scaduta_title = 'PROPOSITION EXPIRÉE';
                      $proposta_scaduta_text  = 'Voulez-vous réactiver cette proposition de séjour, écrivez-nous dans le chat et nous vous répondrons dans les plus brefs délais!';
                    break;
                    case"de":
                      $proposta_scaduta_title = 'VORSCHLAG ABGELAUFEN';
                      $proposta_scaduta_text  = 'Möchten Sie diesen Aufenthaltsvorschlag reaktivieren, schreiben Sie uns im Chat und wir werden so schnell wie möglich antworten!';
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
                <script language="javascript">_alert("'.$proposta_scaduta_title.'","'.$proposta_scaduta_text.'")</script>';
                }
            }

            $array_op = $this->immagine_operatore($idsito,$Operatore);
            $ImgOp    = $array_op[0];
            $disable  = $array_op[1];

            $testoLanding =  $this->testiDefault($idsito,$Id,$Lingua,$TipoRichiesta,$Cliente);
        }

        #GESTIONE COLORI TEMPLATE
        $sel_color = "SELECT * FROM hospitality_template_background WHERE idsito = :idsito AND TemplateName = :TemplateName LIMIT 1";
        $res_color = DB::select($sel_color,['idsito' => $idsito, 'TemplateName' => 'smart']);
        $rCol      = $res_color[0];
        $colore1   = $rCol->Background;
        $colore2   = $rCol->Pulsante;
        $font      = $rCol->Font;
        switch($font){
            case "'Lato', sans-serif";
                $font_libreria = 'Lato:100,100i,300,300i,400,400i,700,700i,900,900i';
            break;
            case "'Lora', serif";
                $font_libreria = 'Lora:400,400i,700,700i"';
            break;
            case "'Open Sans', sans-serif";
                $font_libreria = 'Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i';
            break;
            case "'Playfair Display', serif";
                $font_libreria = 'Playfair+Display:400,400i,700,700i,900,900i';
            break;
            case "'Raleway', sans-serif";
                $font_libreria = 'Raleway';
            break;
            case "'Roboto', sans-serif";
                $font_libreria = 'Roboto:100,100i,300,300i,400,400i,500,500i,700,700i,900,900i';
            break;
            case "'Roboto Slab', serif";
                $font_libreria = 'Roboto+Slab:100,300,400,700';
            break;
            case "'Ubuntu', sans-serif";
                $font_libreria = 'Ubuntu:300,300i,400,400i,500,500i,700,700i';
            break;
            case "'Montserrat', sans-serif";
                $font_libreria = 'Montserrat:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i';
            break;
        }
        $immagine1 = config('global.settings.BASE_URL_IMG').'uploads/'.$idsito.'/'.$rCol->Immagine;
        $immagine2 = config('global.settings.BASE_URL_IMG').'uploads/'.$idsito.'/'.$rCol->Immagine2;
        //
        $logo                =($Logo ==''?'<i class="fas fa-bed fa-5x fa-fw"></i>':'<img src="'.config('global.settings.BASE_URL_IMG').'uploads/loghi/'.$Logo.'" class="logo">');
        $logotitle           = $NomeCliente;
        $intestazionemobile1 = $NomeCliente;
        $intestazionemobile2 = $Localita;
        $coloresfondo        ="#A4A4A4";//colore di sfondo della pagina

        $ownerimg            = ($ImgOp==''?'<img src="/img/receptionists.png">':'<img src="'.config('global.settings.BASE_URL_IMG').'uploads/'.$idsito.'/'.$ImgOp.'">');//immagine del proprietario
        $telefono            = $tel;
        $sitoweb             = $SitoWeb;

        $tot_cc = $this->tot_check_pagamento($idsito,$id_richiesta,'Carta di Credito');
        $tot_vp = $this->tot_check_pagamento($idsito,$id_richiesta,'Vaglia Postale');
        $tot_bn = $this->tot_check_pagamento($idsito,$id_richiesta,'Bonifico Bancario');

        return view('smart_template/index',
            [
                'directory'             => $directory,
                'id_richiesta'          => $id_richiesta,
                'idsito'                => $idsito,
                'tipo'                  => $tipo,
                'Lingua'                => $Lingua,
                'NomeCliente'           => session('NomeCliente'),
                'abilita_mappa'         => $abilita_mappa,
                'latitudine'            => $LatCliente,
                'longitudine'           => $LonCliente,
                'tot_cc'                => $tot_cc,
                'tot_vp'                => $tot_vp,
                'tot_bn'                => $tot_bn,
                'tot_cc_check'          => $this->chek_pagamento_cc($idsito,$id_richiesta),
                'Nprenotazione'         => $Nprenotazione,
                'IdSito'                => $idsito,
                'head_tagmanager'       => $head_tagmanager,
                'overfade'              => $overfade,
                'body_tagmanager'       => $body_tagmanager,
                'SitoWeb'               => $SitoWeb,
                'result'                => ($request->result != '' ? $request->result : ''),
                'Logo'                  => $Logo,
                'logo'                  => $logo,
                'TipoRichiesta'         => $TipoRichiesta,
                'Chiuso'                => $Chiuso,
                'AccontoRichiesta'      => $AccontoRichiesta,
                'AccontoLibero'         => $AccontoLibero,
                'AccontoPercentuale'    => $AccontoPercentuale,
                'AccontoImporto'        => $AccontoImporto,
                'Nome'                  => $Nome,
                'Cognome'               => $Cognome,
                'NumeroPrenotazione'    => $NumeroPrenotazione,
                'DataRichiesta'         => $DataRichiesta,
                'DataScadenza'          => $DataScadenza,
                'ordinamento_pagamenti' => $this->ordinamento_pagamenti($idsito,$id_richiesta,$request),
                'testo_messaggio'       => $testo_messaggio,
                'Cellulare'             => $Cellulare,
                'sistemazione'          => $sistemazione,
                'testo_saluti'          => $testo_saluti,
                'InformativaPrivacy'    => $this->InformativaPrivacy($idsito),
                'content_banner'        => $this->content_banner($idsito,$Lingua,$Logo),
                'testo_riferimento'     => $testo_riferimento,
                'EmailCliente'          => $EmailCliente,
                'Email'                 => $Email,
                'Cliente'               => $Cliente,
                'Id'                    => $id_richiesta,
                'testo'                 => dizionario('INFORMATIVA_PRIVACY'),
                'check_preno_esiste'    => $this->check_preno_esiste($Nprenotazione, $idsito),
                'IdRichiesta'           => $id_richiesta,
                'TopImage'              => $this->get_TopImage($idsito, $TipoRichiesta, $Lingua),
                'disable'               => $disable,
                'Operatore'             => $Operatore,
                'ImgOp'                 => $ImgOp,
                'Testo'                 => $testoLanding,
                'DataArrivo'            => $DataArrivo,
                'DataPartenza'          => $DataPartenza,
                'proposta'              => $proposta,
                'infohotel'             => $this->info_hotel($idsito,$Lingua,$TipoRichiesta),
                'Eventi'                => $this->eventi($idsito, $Lingua, $DataArrivo),
                'PuntidiInteresse'      => $this->punti_interesse($idsito, $Lingua),
                'carosello'             => $this->get_carosello($idsito),
                'Mappa'                 => $Mappa,
                'condizioni_generali'   => $this->condizioni_generali($idsito, $Lingua,$id_politiche),
                'Indirizzo'             => $Indirizzo,
                'Localita'              => $Localita,
                'Provincia'             => $Provincia,
                'Cap'                   => $Cap,
                'CIR'                   => $CIR,
                'CIN'                   => $CIN,
                'Facebook'              => $Facebook,
                'Twitter'               => $Twitter,
                'GooglePlus'            => $GooglePlus,
                'Instagram'             => $Instagram,
                'Linkedin'              => $Linkedin,
                'Pinterest'             => $Pinterest,

            ]
        );

    }
}
