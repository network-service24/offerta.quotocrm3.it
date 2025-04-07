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

class Custom1Controller extends Controller
{
    
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

            $vaglia_posta .= ' <div class="ca20"></div>
                                <hr>
                                <h3 class="w400">'.$Pagamento_vp.'</h3>
                                <div class="ca10"></div>
                                    <span class="t16">'.$Descrizione_vp.'</span><br><br>
                                    <div class="t30">';

                                        if($AccontoRichiesta != 0 && $AccontoLibero == 0) {
                                            $vaglia_posta .= '<b>'.$ACCONTO.'</b>: '.$AccontoRichiesta.' %  - <b class="t40 tcolor">€. '.number_format(($PrezzoPC*$AccontoRichiesta/100),2,',','.').'</b><br><br>';
                                        }
                                        if($AccontoPercentuale != 0 && $AccontoImporto == 0) {
                                            $vaglia_posta .= '<b>'.$ACCONTO.'</b>: '.$AccontoPercentuale.' %  - <b class="t40 tcolor">€. '.number_format(($PrezzoPC*$AccontoPercentuale/100),2,',','.').'</b><br><br>';
                                        }
                                        if($AccontoRichiesta == 0 && $AccontoLibero != 0) {
                                            $vaglia_posta .= '<b>'.$ACCONTO.'</b>:  <b class="t40 tcolor">€. '.number_format($AccontoLibero,2,',','.').'</b><br><br>';
                                        }
                                        if($AccontoPercentuale == 0 && $AccontoImporto != 0) {
                                            $vaglia_posta .= '<b>'.$ACCONTO.'</b>:  <b class="t40 tcolor">€. '.number_format($AccontoImporto,2,',','.').'</b><br><br>';
                                        }

            $vaglia_posta .= '      </div>
                                    <div id="response_vp"></div>
                                    <form  method="POST" id="form_vaglia" name="form_vaglia">
                                            <input type="hidden" name="id_richiesta" value="'.$id_richiesta.'">
                                            <input type="hidden" name="idsito" value="'.$idsito.'">
                                            <input type="hidden" name="TipoPagamento" value="Vaglia Postale">
                                            <input type="hidden" name="action" value="add_payment">
                                            <input name="vg_policy" id="vg_policy" type="radio" value="1" required oninvalid="this.setCustomValidity(\'Questo campo è obbligatorio\')" onchange="this.setCustomValidity(\'\')">
                                            <label for="vg_policy" class="control-label text14">'.dizionario('ACCETTO_POLITICHE').' <small>(<a href="#" id="vg_politiche" onclick="scroll_to(\'Condizioni\', 70, 1000);">'.dizionario('LEGGI_POLITICHE').'</a>)</small></label>
                                            <div class="clear"></div>';
                        if($tot_pag_check == 0 && $tot_cc_check == 0){
                            $vaglia_posta .='<button type="submit" class="pulsante" id="bottone_vaglia" >'.dizionario('SCELGO_VAGLIA').'</button>';
                        }elseif($tot_pag_check > 0 && $tot_cc_check == 0){
                        if($TipoPagamento == 'Vaglia Postale'){
                            $vaglia_posta .= '<span class="tcolor2">'.dizionario('PAGAMENTOSCELTO').' Vaglia Postale</span>';
                        }else{
                            $vaglia_posta .= '<span class="tcolor">'.dizionario('PROPOSTAPAGAMENTOSCELTO').'</span>';
                            $vaglia_posta .='<br><button type="submit" class="pulsante" id="bottone_vaglia" >'.$CAMBIA_VAGLIA.'</button>';
                        }
                        }elseif($tot_pag_check == 0 && $tot_cc_check > 0){
                            $vaglia_posta .= '<span class="tcolor">'.dizionario('PROPOSTAPAGAMENTOSCELTO').'</span>';
                            $vaglia_posta .='<br><button type="submit" class="pulsante" id="bottone_vaglia" >'.$CAMBIA_VAGLIA.'</button>';
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
                                        </script>
                                        <div class="ca10"></div>';

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

            $bonifico_bancario .= '<div class="ca20"></div>
                                    <hr>
                                    <h3 class="w400">'.$Pagamento_bn.'</h3>
                                    <div class="ca10"></div>
                                    <span class="t16">'.$Descrizione_bn.'</span><br><br>
                                    <div class="t30">';

                                        if($AccontoRichiesta != 0 && $AccontoLibero == 0) {
                                            $bonifico_bancario .= '<b>'.$ACCONTO.'</b>: '.$AccontoRichiesta.' %  - <b class="t40 tcolor">€. '.number_format(($PrezzoPC*$AccontoRichiesta/100),2,',','.').'</b><br><br>';
                                        }
                                        if($AccontoPercentuale != 0 && $AccontoImporto == 0) {
                                            $bonifico_bancario .= '<b>'.$ACCONTO.'</b>: '.$AccontoPercentuale.' %  - <b class="t40 tcolor">€. '.number_format(($PrezzoPC*$AccontoPercentuale/100),2,',','.').'</b><br><br>';
                                        }
                                        if($AccontoRichiesta == 0 && $AccontoLibero != 0) {
                                            $bonifico_bancario .= '<b>'.$ACCONTO.'</b>:  <b class="t40 tcolor">€. '.number_format($AccontoLibero,2,',','.').'</b><br><br>';
                                        }
                                        if($AccontoPercentuale == 0 && $AccontoImporto != 0) {
                                            $bonifico_bancario .= '<b>'.$ACCONTO.'</b>:  <b class="t40 tcolor">€. '.number_format($AccontoImporto,2,',','.').'</b><br><br>';
                                        }

            $bonifico_bancario .= ' </div><div id="response_bf"></div>
                                    <form  method="POST" id="form_bonifico" name="form_bonifico">
                                            <input type="hidden" name="id_richiesta" value="'.$id_richiesta.'">
                                            <input type="hidden" name="idsito" value="'.$idsito.'">
                                            <input type="hidden" name="TipoPagamento" value="Bonifico">
                                            <input type="hidden" name="action" value="add_payment">
                                            <input name="bf_policy" id="bf_policy" type="radio" value="1" required oninvalid="this.setCustomValidity(\'Questo campo è obbligatorio\')" onchange="this.setCustomValidity(\'\')">
                                            <label for="bf_policy" class="control-label text14">'.dizionario('ACCETTO_POLITICHE').' <small>(<a class="vmb" section="condizioni">'.dizionario('LEGGI_POLITICHE').'</a>)</small></label>
                                            <div class="clear"></div>';

                    if($tot_pag_check== 0 && $tot_cc_check == 0){
                        $bonifico_bancario .='<button type="submit" class="pulsante" id="bottone_bonifico" >'.dizionario('SCELGO_BONIFICO').'</button>';
                    }elseif($tot_pag_check > 0 && $tot_cc_check == 0){
                    if($TipoPagamento == 'Bonifico'){
                        $bonifico_bancario .= '<span class="tcolor2">'.dizionario('PAGAMENTOSCELTO').' Bonifico Bancario</span>';
                    }else{
                        $bonifico_bancario .= '<span class="tcolor">'.dizionario('PROPOSTAPAGAMENTOSCELTO').'</span>';
                        $bonifico_bancario .='<br><button type="submit" class="pulsante" id="bottone_bonifico" > '.$CAMBIA_BONIFICO.'</button>';
                    }
                    }elseif($tot_pag_check == 0 && $tot_cc_check > 0){
                        $bonifico_bancario .= '<span class="tcolor">'.dizionario('PROPOSTAPAGAMENTOSCELTO').'</span>';
                        $bonifico_bancario .='<br><button type="submit" class="pulsante" id="bottone_bonifico" > '.$CAMBIA_BONIFICO.'</button>';
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

            $carte_credito .= '  <div class="ca20"></div>
                                    <hr>
                                    <h3 class="w400">'.$Pagamento_cc.'</h3>
                                    <div class="ca10"></div>
                                    <span class="t16">'.$Descrizione_cc.'</span>
                                    <div class="ca20"></div>
                                    <div class="t30">';

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
                $carte_credito .= '</div>';
                $carte_credito .= ($amex==1?'<i class="fab fa-cc-amex fa-4x fa-fw text-aqua"></i>':'');
                $carte_credito .= ($diners==1?'<i class="fab fa-cc-diners-club fa-4x fa-fw text-light-blue"></i>':'');
                $carte_credito .= ($mastercard==1?'<i class="fab fa-cc-mastercard fa-4x fa-fw text-orange"></i>&nbsp;':'');
                $carte_credito .= ($visa==1?'<i class="fab fa-cc-visa fa-4x fa-fw text-blue"></i>':'');
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
                                            <label for="cc_policy" class="control-label">'.dizionario('ACCETTO_POLITICHE').' (<a class="vmb" section="condizioni">'.dizionario('LEGGI_POLITICHE').'</a>)</label>
                                        </div>
                                        <br><br>
                                            <input type="hidden" name="id_richiesta" value="'.$id_richiesta.'">
                                            <input type="hidden" name="idsito" value="'.$idsito.'">
                                            <input type="hidden" name="action" value="add_carta">';

                    if($tot_cc_check == 0 && $tot_pag_check== 0){
                        $carte_credito .='<button type="submit" class="pulsante" id="bottone_cc" disabled>'.dizionario('SALVA_CARTA_CREDITO').'</button>';
                        }elseif($tot_cc_check > 0 && $tot_pag_check == 0){
                            $carte_credito .= '<span class="tcolor2">'.dizionario('PAGAMENTOSCELTO').' Carta di Credito</span>';
                        }elseif($tot_cc_check == 0 && $tot_pag_check > 0){
                            $carte_credito .= '<span class="tcolor">'.dizionario('PROPOSTAPAGAMENTOSCELTO').'</span>';
                            $carte_credito .='<br><button type="submit" class="pulsante" id="bottone_cc" disabled>'.$CAMBIA_CARTA_CREDITO.'</button>';
                    }


            $carte_credito .='    </div>     
                                        <div class="ca10"></div>
                                        <h3 class="validation"></h3>
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
            
            $paypal .= '<div class="ca20"></div>
                        <hr>
                        <h3 class="w400">'.$Pagamento_pp.'</h3>
                        <div class="ca10"></div>
                            <span class="t16">'.$Descrizione_pp.'</span><br><br>
                            <div class="t30">
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

                    $paypal .= ' <label style="width:100%" class="t14">
                                <input name="pp_policy" id="pp_policy" type="radio" value="1" required oninvalid="this.setCustomValidity(\'Questo campo è obbligatorio\')" onchange="this.setCustomValidity(\'\')" />
                                '.dizionario('ACCETTO_POLITICHE').' <small>(<a class="vmb" section="condizioni">'.dizionario('LEGGI_POLITICHE').'</a>)</small>
                                </label>
                                <div class="ca20"></div>';
                    if($EmailPayPal !=''){
                            $paypal .= ' <img src="/img/paypal.png" class="img-responsive" style="width:25%" />
                                        <div class="ca20"></div>';
                            if($tot_cc_check == 0 && $tot_pag_check== 0){
                                $paypal .= '<button type="submit" class="pulsante"><i class="fab fa-paypal fa-lg"></i> '.dizionario('PAGA_PAYPAL').'</button>';
                            }elseif($tot_pag_check > 0 && $tot_cc_check == 0){
                            if($TipoPagamento == 'PayPal'){
                                $paypal .= '<span class="tcolor2 t14">'.dizionario('PAGAMENTOSCELTO').' PayPal</span>';
                            }else{
                                $paypal .= '<span class="tcolor t14">'.dizionario('PROPOSTAPAGAMENTOSCELTO').'</span>';
                                $paypal .= '<br><button type="submit" class="pulsante"><i class="fab fa-paypal fa-lg"></i> '.$CAMBIA_PAYPAL.'</button>';
                            }
                            }elseif($tot_pag_check == 0 && $tot_cc_check > 0){
                                $paypal .= '<span class="tcolor t14">'.dizionario('PROPOSTAPAGAMENTOSCELTO').'</span>';
                                $paypal .= '<br><button type="submit" class="pulsante"><i class="fab fa-paypal fa-lg"></i> '.$CAMBIA_PAYPAL.'</button>';
                            }
                    }else{
                    $paypal .= '<small class="tcolor t14">Email di riferimento PayPal, non è stata inserita!</small>';
                    }
                    $paypal .= '</form>
                                </div>';

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

            $gateway_bancario .= '<div class="ca20"></div>
                            <hr>
                            <h3 class="w400">'.$Pagamento_gb.'</h3>
                            <div class="ca10"></div>
                                <span class="t16">'.$Descrizione_gb.'</span><br><br>
                                <div class="t30">
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
                                    <div class="clear"></div> ';
                $gateway_bancario .= '<label class="t14">
                                        <input name="gb_policy" id="gb_policy" type="radio" value="1" required oninvalid="this.setCustomValidity(\'Questo campo è obbligatorio\')" onchange="this.setCustomValidity(\'\')" />
                                        '.dizionario('ACCETTO_POLITICHE').' <small>(<a class="vmb" section="condizioni">'.dizionario('LEGGI_POLITICHE').'</a>)</small>
                                        </label>
                                        <div class="clearfix"></div> ';
            if($kSig !=''){

                if($tot_cc_check == 0 && $tot_pag_check== 0){
                    $gateway_bancario .='<button type="submit" class="pulsante">'.dizionario('PAGA_CARTA_CREDITO').' PayWay</button>';
                }elseif($tot_pag_check > 0 && $tot_cc_check == 0){
                    if($TipoPagamento == 'Gateway Bancario'){
                    $gateway_bancario .= '<span class="tcolor2 t14">'.dizionario('PAGAMENTOSCELTO').' Gateway Bancario PayWay</span>';
                    }else{
                    $gateway_bancario .= '<span class="tcolor t14">'.dizionario('PROPOSTAPAGAMENTOSCELTO').'</span>';
                    $gateway_bancario .='<br><button type="submit" class="pulsante">'.$CAMBIA_CARTA_CREDITO.' PayWay</button>';
                    }
                }elseif($tot_pag_check == 0 && $tot_cc_check > 0){
                    $gateway_bancario .= '<span class="tcolor t14">'.dizionario('PROPOSTAPAGAMENTOSCELTO').'</span>';
                    $gateway_bancario .='<br><button type="submit" class="pulsante">'.$CAMBIA_CARTA_CREDITO.' PayWay</button>';
                }

            }else{
            $gateway_bancario .= '<small class="tcolor t14">API Key di riferimento PayWay non è stata inserita!</small>';
            }
                $gateway_bancario .= '</form></div>';

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

            $gateway_bancario_virtualpay .= '<div class="ca20"></div>
                      <hr>
                      <h3 class="w400">'.$Pagamento_gbvp.'</h3>
                      <div class="ca10"></div>
                        <span class="t16">'.$Descrizione_gbvp.'</span><br><br>
                        <div class="t30">
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
                                            <div class="ca10"></div>  ';

            $gateway_bancario_virtualpay .= '<label class="t14">
                                                <input name="gbvp_policy" id="gbvp_policy" type="radio" value="1" required />
                                                '.dizionario('ACCETTO_POLITICHE').' <small>(<a class="vmb" section="condizioni">'.dizionario('LEGGI_POLITICHE').'</a>)</small>
                                            </label>
                                            <div class="ca10"></div> ';


            if($MERCHANT_ID !=''){

                if($tot_cc_check == 0 && $tot_pag_check== 0){
                    $gateway_bancario_virtualpay .='<button type="submit" class="pulsante">'.dizionario('PAGA_CARTA_CREDITO').' Virtual Pay</button>';
                }elseif($tot_pag_check > 0 && $tot_cc_check == 0){
                    if($TipoPagamento == 'Gateway Bancario Virtual Pay'){
                    $gateway_bancario_virtualpay .= '<span class="tcolor2 t14">'.dizionario('PAGAMENTOSCELTO').' Gateway Bancario Virtual Pay</span>';
                    }else{
                    $gateway_bancario_virtualpay .= '<span class="tcolor t14">'.dizionario('PROPOSTAPAGAMENTOSCELTO').'</span>';
                    $gateway_bancario_virtualpay .='<br><button type="submit" class="pulsante">'.$CAMBIA_CARTA_CREDITO.' Virtual Pay</button>';
                    }
                }elseif($tot_pag_check == 0 && $tot_cc_check > 0){
                $gateway_bancario_virtualpay .= '<span class="tcolor t14">'.dizionario('PROPOSTAPAGAMENTOSCELTO').'</span>';
                $gateway_bancario_virtualpay .='<br><button type="submit" class="pulsante">'.$CAMBIA_CARTA_CREDITO.' Virtual Pay</button>';
                }

            }else{
                $gateway_bancario_virtualpay .= '<small class="tcolor t14">MERCHANT ID di riferimento Virtual Pay non è stato inserito!</small>';
            }
                $gateway_bancario_virtualpay .= '</form></div>';


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

                $stripe .= '<div class="ca20"></div>
                            <hr>
                            <h3 class="w400">'.$Pagamento_ss.'</h3>
                            <div class="ca10"></div>
                                <span class="t16">'.$Descrizione_ss.'</span><br><br>
                                <div class="t30">
                                '.$stripe_txt.'
                                <img src="'.config('global.settings.BASE_URL_IMG').'img/stripe.png" class="img-responsive" style="width:25%" />
                                <div class="c10"></div> ';

                $stripe .= '
                            <label class="t14">
                                <input name="ss_policy" id="ss_policy" type="radio" value="1" required oninvalid="this.setCustomValidity(\'Questo campo è obbligatorio\')" onchange="this.setCustomValidity(\'\')" />
                                '.dizionario('ACCETTO_POLITICHE').' <small>(<a class="vmb" section="condizioni">'.dizionario('LEGGI_POLITICHE').'</a>)</small>
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
                                                    $stripe .= '<a id="card-button" class="pulsante" onclick="check_policy();" target="_blank">Paga con STRIPE </a>';
                                                }elseif($tot_pag_check > 0 && $tot_cc_check == 0){
                                                    if($TipoPagamento == 'Stripe'){
                                                        $stripe .= '<span class="tcolor2 t14">'.dizionario('PAGAMENTOSCELTO').' Stripe</span>';
                                                    }else{
                                                        $stripe .= '<span class="tcolor t14">'.dizionario('PROPOSTAPAGAMENTOSCELTO').'</span>';
                                                        $stripe .= '<br><button id="card-button" class="pulsante" onclick="check_policy();" target="_blank">'.$CAMBIA_STRIPE.' </button>';
                                                    }
                                                }elseif($tot_pag_check == 0 && $tot_cc_check > 0){
                                                    $stripe .= '<span class="tcolor t14">'.dizionario('PROPOSTAPAGAMENTOSCELTO').'</span>';
                                                    $stripe .= '<br><button id="card-button" class="pulsante" onclick="check_policy();" target="_blank">'.$CAMBIA_STRIPE.' </button>';
                                                }
                                        }else{
                                            $stripe .= '<small class="tcolor t14">API di riferimento Stripe, non è stata inserita!</small>';
                                        }
                                    }else{
                                        $stripe .= '<small class="tcolor t14">Manca il link creato dalla dashboard di Stripe; non è stato inserito!</small>';
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
                                ';
                    $stripe .= '
                                </div>';

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

                $nexi .= '<div class="ca20"></div>
                            <hr>
                            <h3 class="w400">'.$Pagamento_nx.'</h3>
                            <div class="ca10"></div>
                                <span class="t16">'.$Descrizione_nx.'</span><br><br>
                                <div class="t30">
                                '.$nexi_txt.'
                                <img src="'.config('global.settings.BASE_URL_IMG').'img/LogoNexi_XPay.jpg" class="img-responsive" style="width:25%"/>
                                <div class="clear"></div> ';

                $ALIAS = $ApiKeyNexi;
                $CHIAVESEGRETA =   $SegretKeyNexi;


                $nexi .= '  
                                <form method="POST" name="xpay" action="'.config('global.settings.URL_NEXI').'">
                                <input type="hidden" name="_token" value="'.csrf_token().'" />
                                <input name="ne_policy" id="ne_policy" type="radio" value="1" required oninvalid="this.setCustomValidity(\'Questo campo è obbligatorio\')" onchange="this.setCustomValidity(\'\')">
                                <label for="ne_policy" class="t14">'.dizionario('ACCETTO_POLITICHE').' (<a class="vmb" section="condizioni">'.dizionario('LEGGI_POLITICHE').'</a>)</label>
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
                                    $nexi .= '<div class="ca20"></div>';
                                    if($tot_cc_check == 0 && $tot_pag_check == 0){
                                        $nexi .= ' <button type="submit"  class="pulsante" id="nexi-button">'.dizionario('PAGA_NEXI').'</button>';
                                    }elseif($tot_pag_check > 0 && $tot_cc_check == 0){
                                        if($TipoPagamento == 'Nexi'){
                                        $nexi .= '<span class="tcolor2 t14">'.dizionario('PAGAMENTOSCELTO').' Nexi</span>';
                                        }else{
                                        $nexi .= '<span class="tcolor t14">'.dizionario('PROPOSTAPAGAMENTOSCELTO').'</span>';
                                        $nexi .= '<br><button type="submit"  class="pulsante" id="nexi-button">'.$CAMBIA_NEXI.'</button>';
                                        }
                                    }elseif($tot_pag_check == 0 && $tot_cc_check > 0){
                                        $nexi .= '<span class="tcolor t14">'.dizionario('PROPOSTAPAGAMENTOSCELTO').'</span>';
                                        $nexi .= '<br><button type="submit"  class="pulsante" id="nexi-button">'.$CAMBIA_NEXI.'</button>';
                                    } 
                            }else{
                                $nexi .= '<small class="tcolor t14">API di riferimento Nexi, non è stata inserita!</small>';
                            }
                                    
                    $nexi .= '</form>
                            </div>';

                        if($request->result!='' && base64_decode($request->result)=='nexi') {

                        $nexi .= '<script>$("#nexi-button").fadeOut();</script>';
                    
                        }

        }

        return $nexi;
    }


 
    
    /**
     * condizioni_generali
     *
     * @param  mixed $idsito
     * @param  mixed $Lingua
     * @param  mixed $id_politiche
     * @return void
     */
    public function condizioni_generali($idsito,$Lingua,$id_politiche)
    {
        $condizioni_generali = '';
        #PCONDIZIONI GENERALI E POLITICHE DI CANCELLAZIONE
        $sel_cg = "SELECT *
                    FROM hospitality_politiche_lingua
                    WHERE idsito = :idsito
                        AND id_politiche = :id_politiche
                        AND Lingua = :Lingua
                    ORDER BY id DESC";
        $re_cg = DB::select($sel_cg, ['id_politiche' => $id_politiche, 'Lingua' => $Lingua, 'idsito' => $idsito]);
        $tot_cg = sizeof($re_cg);
        if($tot_cg > 0){
        $rw = $re_cg[0];

            $condizioni_generali = $rw->testo;
        }

        return $condizioni_generali;
    } 

    /**
     * eventi
     *
     * @param  mixed $idsito
     * @param  mixed $Lingua
     * @param  mixed $DataArrivo
     * @return void
     */
    public function eventi($idsito, $Lingua, $DataArrivo)
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

            // azzero variabili
            $distanzaE = '';
            $distanceE = '';
            $lat       = '';
            $lon       = '';
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
                $coord       = $this->coordinateCliente($idsito);
                $array_coord = explode("#", $coord);
                $LatCliente  = $array_coord[1];
                $LonCliente  = $array_coord[0];
                // calcolo la distanza
                $distanzaE = $this->calcola_distanza($LatCliente, $LonCliente, $lat, $lon);

                foreach ($distanzaE as $unita => $valore) {
                    $distanceE = $unita . ': ' . (number_format($valore, 2, ',', '.')) . '<br/>';
                }
                // giro le date in formato italiano
                $array      = explode("-", $rec->DataInizio);
                $DataInizio = $array[2] . "/" . $array[1] . "/" . $array[0];
                $array2     = explode("-", $rec->DataFine);
                $DataFine   = $array2[2] . "/" . $array2[1] . "/" . $array2[0];

                $Eventi .= '<div class="m m-x-4 m-s-6 m-xs-12">
                                <div class="box5">
                                <div class="m m-x-12">
                                    <img src="'.config('global.settings.BASE_URL_IMG').'uploads/'.$idsito.'/'.$rec->Immagine.'" width="100%" class="mbr">
                                </div>
                                <div class="ca10"></div>
                                <div class="m m-x-12">
                                    <div class="bcolor3 mbr">
                                    <div class="box5 LineHeight18 h200 scroll" style="height:240px!important;overflow:auto!important">
                                        <h4>'.$rec->Titolo.'</h4>
                                        <p>'.$rec->Descrizione.'</p>
                                        <p>
                                        <i class="far fa-fw fa-calendar-alt"></i> Dal '.$DataInizio.' <i class="far fa-fw fa-calendar-alt"></i> al '.$DataFine.'<br>
                                        '.($rec->Indirizzo!=''?'<i class="far fa-fw fa-address-card"></i>'.$rec->Indirizzo.', '.session('NomeCliente'):'').'  '.($lat!='0'?' a '.$distanceE:'').'
                                        ' . ($lat != '0' ? '<i class="fa fa-fw fa-map-marker"></i><span id="open_map' . $rec->Id . '" onclick="document.getElementById(\'frame_lp\').src = \'/gmap?from_lati=' . $lat . '&from_long=' . $lon . '&travelmode=DRIVING&idsito=' . $idsito . '\'; document.location.href=\'#start_map\'; return false" style="cursor:pointer">' . dizionario('VISUALIZZA_MAPPA') . '</span>' : '') . '
                                        </p>
                                    </div>
                                    </div>
                                </div>
                                <script>
                                    $("#open_map'.$rec->Id.'").click(function(){
                                        $("#b_map").removeAttr("style");
                                    });
                                </script>
                            </div>
                            </div> ';

            }
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
     * @param  mixed $idsito
     * @param  mixed $Lingua
     * @return void
     */
    public function punti_interesse($idsito, $Lingua)
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

            // azzero variabili
            $coor_ = '';
            $distanza = '';
            $distanze = '';
            $lati      = '';
            $longi      = '';
            foreach($re as $key => $rws){

                // estrapolo latitutine e longitudi del punto interesse
                $coordinateAsText = unpack('x/x/x/x/corder/Ltype/dlon/dlat', $rws->Coordinate);
                if ($coordinateAsText != '') {
                    $lati = $coordinateAsText['lon'];
                    $longi = $coordinateAsText['lat'];
                } else {
                    $lati = '';
                    $longi = '';
                }
                $coord       = $this->coordinateCliente($idsito);
                $array_coord = explode("#",$coord);
                $LatCliente  = $array_coord[1];
                $LonCliente  = $array_coord[0];
                // calcolo la distanza
                $distanza = $this->calcola_distanza($LatCliente, $LonCliente, $lati, $longi);
                $distanze = '';
                foreach ($distanza as $unita => $valore) {
                    $distanze = $unita . ': ' . number_format($valore,2,',','.') . '<br/>';
                }

                $PuntidiInteresse .= '<div class="m m-x-4 m-s-6 m-xs-12 ">
                                        <div class="box5">
                                            <div class="m m-x-12">
                                                <img src="'.config('global.settings.BASE_URL_IMG').'uploads/'.$idsito.'/'.$rws->Immagine.'" width="100%"  class="mbr" >
                                            </div>
                                            <div class="ca10"></div>
                                            <div class="m m-x-12 ">
                                                <div class="bcolor3 mbr">
                                                <div class="box5 LineHeight18 h200 scroll" style="height:240px!important;overflow:auto!important">
                                                    <h4>'.$rws->Titolo.'</h4>
                                                    <p>'.$rws->Descrizione.'</p>
                                                    <p>
                                                        '.($rws->Indirizzo!=''?'<i class="fa fa-fw fa-thumb-tack"></i>'.$rws->Indirizzo.', '.session('NomeCliente'):'').'  '.($lati!='0'?' a '.$distanze:'').'
                                                        '.($lati!='0'?'<i class="fa fa-fw fa-map-marker"></i><span id="open_map_pdi'.$rws->Id.'" onclick="document.getElementById(\'frame_lp_pdi\').src = \'/gmap?from_lati='.$lati.'&from_long='.$longi.'&travelmode=DRIVING&idsito='.$idsito.'\'; document.location.href=\'#start_map_pdi\'; return false" style="cursor:pointer">'.dizionario('VISUALIZZA_MAPPA').'</span>':'').'
                                                    </p>
                                                    </div>
                                                </div>
                                            </div>
                                        <script>
                                            $("#open_map_pdi'.$rws->Id.'").click(function(){
                                                $("#b_map_pdi").removeAttr("style");
                                            });
                                        </script>
                                        </div>
                                    </div>   ';



            }
            $distanza = '';
            $distanze = '';


        }else{
            $PuntidiInteresse = '';
        }

        return $PuntidiInteresse;
    }




    /**
     * get_carosello
     *
     * @param  mixed $idsito
     * @return void
     */
    public function get_carosello($idsito)
    {
        $carosello = '';
        $slide = '';
        $sel = "SELECT Id FROM hospitality_tipo_gallery WHERE TargetType = :TargetType AND idsito = :idsito";
        $res  = DB::select($sel,['TargetType' => 'custom1', 'idsito' => $idsito]);
        if(sizeof($res )>0){
                $IdGallery = $res[0]->Id;
                $sel = "SELECT * FROM hospitality_tipo_gallery_target WHERE IdTipoGallery = :IdTipoGallery AND idsito = :idsito AND Abilitato = :Abilitato ORDER BY rand() LIMIT 12";
                $q_carosello  = DB::select($sel,['IdTipoGallery' => $IdGallery, 'Abilitato' => 1, 'idsito' => $idsito]);
                $r = sizeof($q_carosello);
       
                if($r > 0){
                    $n = 1;
                    foreach($q_carosello as $key => $rs){
                    
                            $carosello .='<img src="'.config('global.settings.BASE_URL_IMG').'uploads/'.$idsito.'/'.$rs->Immagine.'" onclick="openModal();currentSlide('.$n.')" class="hover-shadow img-responsive img_gallery">';
                            $slide .='     <div class="mySlides">
                                                <div class="numbertext">'.$n.' / '.$r.'</div>
                                                <img src="'.config('global.settings.BASE_URL_IMG').'uploads/'.$idsito.'/'.$rs->Immagine.'" style="width:100%">
                                            </div>';
                    $n++;
                }
                        $carosello .= '<div id="myModal" class="modal">
                                        <span class="close cursor" onclick="closeModal()">&times;</span>
                                        <div class="modal-content">';
                
                
                        $carosello .= $slide;
                
                        $carosello .='    <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
                                        <a class="next" onclick="plusSlides(1)">&#10095;</a>
                
                                        <div class="caption-container">
                                            <p id="caption"></p>
                                        </div>
                
                                        </div>
                                    </div>';
                }else{
                    $carosello = '';
                }
        }else{
            $carosello = '';
        }
            return $carosello;

    }
    /**
     * info_hotel
     *
     * @param  mixed $idsito
     * @param  mixed $Lingua
     * @param  mixed $TipoRichiesta
     * @return void
     */
    public function info_hotel($idsito,$Lingua,$TipoRichiesta): array
    {
        $infohotel ='';
        #INFOHOTEL
        $info_qy  = "SELECT hospitality_infohotel_lang.*
                        FROM hospitality_infohotel_lang
                            INNER JOIN hospitality_infohotel ON hospitality_infohotel.Id = hospitality_infohotel_lang.Id_infohotel
                        WHERE hospitality_infohotel_lang.idsito = :idsito
                            AND hospitality_infohotel_lang.Lingua = :Lingua
                            AND hospitality_infohotel.Abilitato = :Abilitato";
        $res_info = DB::select($info_qy, ['Abilitato' => 1, 'Lingua' => $Lingua, 'idsito' => $idsito]);
        $tot_info = sizeof($res_info);
        if($tot_info>0){
            $info           = $res_info[0];
            $infohotel      = $info->Titolo ;
            $infohotelTesto = $info->Descrizione;
        }else{
            $infohotel      = '';
            $infohotelTesto = '';
        }

        return ['tot_info' => $tot_info, 'infohotel' => $infohotel, 'infohotelTesto' => $infohotelTesto];
    }

        
    /**
     * get_modifica_servizi_aggiuntivi
     *
     * @param  mixed $n
     * @param  mixed $id_richiesta
     * @param  mixed $id_proposta
     * @param  mixed $Lingua
     * @return void
     */
    public function get_modifica_servizi_aggiuntivi($n,$id_richiesta,$id_proposta,$Lingua)
    {
        $modali_servizi_aggiuntivi = '';  
        $datiG              = Session::get('dati_h_guest', []);
            $idsito             = $datiG->idsito;
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


        $q          = "SELECT * FROM hospitality_relazione_servizi_proposte WHERE id_richiesta = :id_richiesta AND id_proposta = :id_proposta";
        $r          = DB::select($q, ['id_richiesta' => $id_richiesta, 'id_proposta' => $id_proposta]);
        $IdServizio = [];
        if (!empty($r)) {
            foreach ($r as $k => $v) {
                $servizio_id = $v->servizio_id ?? null;
                if ($servizio_id !== null) {
                    $IdServizio[$v->servizio_id] = 1; 
                }             
            }
        }
        // Query per servizi aggiuntivi
        $query = "SELECT
                    hospitality_tipo_servizi.*
                FROM
                    hospitality_tipo_servizi
                WHERE
                    hospitality_tipo_servizi.idsito = :idsito
                AND
                    hospitality_tipo_servizi.Abilitato = :Abilitato
                ORDER BY
                    hospitality_tipo_servizi.Ordine ASC,
                    hospitality_tipo_servizi.TipoServizio ASC";
        $risultato_query = DB::select($query, ['idsito' => $idsito, 'Abilitato' => 1]);
        $record          = sizeof($risultato_query);
        if (($record) > 0) {
      
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
      
                $lista_servizi_aggiuntivi ='
                                               
                                                      <div class="m m-x-2 m-s-0 m-x-tl t18 w300 boxservizi">'.dizionario('SERVIZIO').'</div>
                                                      <div class="m m-x-2 m-s-0 m-x-tl  t18 w300 boxservizi"></div>
                                                      <div class="m m-x-3 m-s-0 m-x-tl  t18 w300 boxservizi">'.dizionario('CALCOLO').'</div>
                                                      <div class="m m-x-1 m-s-0 m-x-tl  t18 w300 nowrap boxservizi" id="add-serv">'.$ABILITA.'</div>
                                                      <div class="m m-x-1 m-s-0 m-x-tl  t18 w300 boxservizi"></div>
                                                      <div class="m m-x-3 m-s-0 m-x-tr  t18 w300 nowrap boxservizi" id="price-serv">'.dizionario('PREZZO_SERVIZIO').'</div>
                                                
                                                  <div class="ca10"></div>';
      
                foreach ($risultato_query as $key => $campo) {
      
                    $q   = "SELECT hospitality_tipo_servizi_lingua.Descrizione,hospitality_tipo_servizi_lingua.Servizio FROM hospitality_tipo_servizi_lingua  WHERE hospitality_tipo_servizi_lingua.servizio_id = :servizio_id AND hospitality_tipo_servizi_lingua.idsito = :idsito AND hospitality_tipo_servizi_lingua.lingue = :lingue";
                    $r   = DB::select($q, ['servizio_id' => $campo->Id, 'idsito' => $idsito, 'lingue' => $Lingua]);
                    if(sizeof($r)>0){
                        $rec = $r[0];
                        $Descrizione = $rec->Descrizione;
                        $Servizio    = $rec->Servizio;
                    }else{
                        $Descrizione = '';
                        $Servizio    = '';
                    }
    
                    $qrel   = "SELECT hospitality_relazione_servizi_proposte.id as id_relazionale,hospitality_relazione_servizi_proposte.num_persone,hospitality_relazione_servizi_proposte.num_notti FROM hospitality_relazione_servizi_proposte WHERE hospitality_relazione_servizi_proposte.id_richiesta = :id_richiesta AND hospitality_relazione_servizi_proposte.id_proposta = :id_proposta AND hospitality_relazione_servizi_proposte.servizio_id = :servizio_id";
                    $rel    = DB::select($qrel, ['servizio_id' => $campo->Id, 'id_proposta' => $id_proposta, 'id_richiesta' => $id_richiesta]);
                    if(sizeof($rel)>0){
                        $recrel = $rel[0];
                        $n_persone = $recrel->num_persone;
                        $n_notti   = $recrel->num_notti;
                    }else{
                        $n_persone = 0;
                        $n_notti   = 0;
                    }
                   
    
                    $s  = "SELECT hospitality_relazione_visibili_servizi_proposte.visibile FROM hospitality_relazione_visibili_servizi_proposte  WHERE hospitality_relazione_visibili_servizi_proposte.id_richiesta = :id_richiesta AND hospitality_relazione_visibili_servizi_proposte.id_proposta = :id_proposta AND hospitality_relazione_visibili_servizi_proposte.servizio_id = :servizio_id ";
                    $ss = DB::select($s, ['servizio_id' => $campo->Id, 'id_proposta' => $id_proposta, 'id_richiesta' => $id_richiesta]);
                    if(sizeof($ss)>0){
                        $rs = $ss[0];
                        $visibile = $rs->visibile;
                    }else{
                        $visibile = 0;
                    }
      
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
                        $TEXT_EXPLANE  = '<small><small>Il calcolo "A percentuale" <a href="javascript:;" id="pul_long_text_percent'.$n.'_'.$campo->Id.'" title="'.$CLICKVIEW.'">...<i class="fa fa-info-circle"></i></a> <span id="long_text_percent'.$n.'_'.$campo->Id.'" style="display:none">viene effettuato sull\'importo originale della proposta ('.number_format($PrezzoPC,2,',','.').')<br>Ossia sul totale soggiorno prima di qualsiasi intervento sui servizi aggiuntivi!</span></small></small>';
                      break;
                      case "en":
                        $A_PERCENTUALE = 'By percentage';
                        $CLICKVIEW     = 'Click to view the explanation!';
                        $TEXT_EXPLANE  = '<small><small>The "A percentage" <a href="javascript:;" id="pul_long_text_percent'.$n.'_'.$campo->Id.'" title="'.$CLICKVIEW.'">...<i class="fa fa-info-circle"></i></a> <span id="long_text_percent'.$n.'_'.$campo->Id.'" style="display:none"> calculation is made on the original amount of the proposal ('.number_format($PrezzoPC,2,',','.').')<br>That is on the total stay before any intervention on additional services! </span></small></small>';
                      break;
                      case "fr":
                        $A_PERCENTUALE = 'Par pourcentage';
                        $CLICKVIEW     = 'Cliquez pour voir l\'explication!';
                        $TEXT_EXPLANE  = '<small><small>Le calcul du "pourcentage A" <a href="javascript:;" id="pul_long_text_percent'.$n.'_'.$campo->Id.'" title="'.$CLICKVIEW.'">...<i class="fa fa-info-circle"></i></a> <span id="long_text_percent'.$n.'_'.$campo->Id.'" style="display:none"> est effectué sur le montant initial de la proposition  ('.number_format($PrezzoPC,2,',','.').')<br>Soit sur le séjour total avant toute intervention sur des prestations complémentaires! </span></small></small>';
                      break;
                      case "de":
                        $A_PERCENTUALE = 'In Prozent';
                        $CLICKVIEW     = 'Klicken Sie hier, um die Erklärung anzuzeigen!';
                        $TEXT_EXPLANE  = '<small><small>Die Berechnung "Ein Prozentsatz" <a href="javascript:;" id="pul_long_text_percent'.$n.'_'.$campo->Id.'" title="'.$CLICKVIEW.'">...<i class="fa fa-info-circle"></i></a> <span id="long_text_percent'.$n.'_'.$campo->Id.'" style="display:none"> erfolgt anhand des ursprünglichen Betrags des Vorschlags ('.number_format($PrezzoPC,2,',','.').')<br>Das ist der Gesamtaufenthalt vor jeder Intervention bei zusätzlichen Dienstleistungen!</span></small></small>';
                      break;
                    }
                    switch($campo->CalcoloPrezzo){
                      case "Al giorno":
                          $calcoloprezzo = dizionario('AL_GIORNO');
                          $obbligatory   = ($campo->Obbligatorio==1?' <small>('.$OBBLIGATORIO.')</small>':'');
                          $CalcoloPrezzoServizio = ($campo->PrezzoServizio!=0?'<small>('.number_format($campo->PrezzoServizio,2,',','.').' x '.($ANotti!=''?$ANotti:$Notti).')</small>':'');
                          $PrezzoServizio = ($campo->PrezzoServizio!=0?'<i class="fal fa-euro-sign"></i>&nbsp;&nbsp;'.number_format(($campo->PrezzoServizio*($ANotti!=''?$ANotti:$Notti)),2,',','.'):'<small class="text-green">Gratis</small>');
                      break;
                      case "A percentuale":
                        $calcoloprezzo = $A_PERCENTUALE;
                        $obbligatory   = ($campo->Obbligatorio==1?' <small>('.$OBBLIGATORIO.')</small>':'');
                        $CalcoloPrezzoServizio = '';
                        $PrezzoServizio = ($campo->PercentualeServizio!=''?'<i class="fa fa-percent"></i>&nbsp;&nbsp;'.number_format(($campo->PercentualeServizio),2):'');
                      break;
                      case "Una tantum":
                          $calcoloprezzo = dizionario('UNA_TANTUM');
                          $obbligatory   = ($campo->Obbligatorio==1?' <small>('.$OBBLIGATORIO.')</small>':'');
                          $CalcoloPrezzoServizio = '';
                          $PrezzoServizio = ($campo->PrezzoServizio!=0?'<i class="fal fa-euro-sign"></i>&nbsp;&nbsp;'.number_format($campo->PrezzoServizio,2,',','.'):'<small class="text-green">Gratis</small>');
                      break;
                      case "A persona":
                        $calcoloprezzo = dizionario('A_PERSONA');
                        $obbligatory   = ($campo->Obbligatorio==1?' <small>('.$OBBLIGATORIO.')</small>':'');
                        $num_persone           = $n_persone;
                        $num_notti             = $n_notti;
                        $CalcoloPrezzoServizio = ($campo->PrezzoServizio!=0?'<small>('.number_format($campo->PrezzoServizio,2,',','.').' x '.$num_notti.' <span style="font-size:80%">gg</span> x '.$num_persone.' <small>pax</small>)</small>':'('.$num_notti.'  <small>gg</small> x '.$num_persone.' <small>pax</small>)');
                        $PrezzoServizio = ($campo->PrezzoServizio!=0?'&nbsp;&nbsp;'.number_format(($campo->PrezzoServizio*$num_notti*$num_persone),2,',','.'):'<small class="text-green">Gratis</small>');
                      break;
                    }
            
              if($DataRichiestaCheck >= config('global.settings.DATA_SERVIZI_VISIBILI')){
                if ($visibile == 1) {           
                      $lista_servizi_aggiuntivi .=' <div class="m m-x-12 rigaservizi">
                                                    <div class="m m-x-2 m-s-12 m-x-tl boxservizi titolo"><strong><span class="nowrap">'
                                                    .($campo->Icona!=''?'<img id="TD'.$campo->Id.'" src="'.config('global.settings.BASE_URL_IMG').'uploads/'.$idsito.'/'.$campo->Icona.'" class="iconaservizi">&nbsp;':'').$Servizio.'</span></strong></div>
                                                    <div class="m m-x-2 m-s-12 m-x-tc boxservizi">'.($Descrizione!=''?'<a href="javascript:;" data-toggle="tooltip" title="'.(strlen($Descrizione)<=300?stripslashes(strip_tags($Descrizione)):substr(stripslashes(strip_tags($Descrizione)),0,300).'...').'"><i class="fa fa-info-circle text-green" aria-hidden="true"></i></a>':'').' </div>';                                                               
                      $lista_servizi_aggiuntivi .=' <div class="m m-x-3 m-s-12 m-x-tl boxservizi">'.$calcoloprezzo.' '.$CalcoloPrezzoServizio.'</div> ';
                      
                      $lista_servizi_aggiuntivi .=' <div class="m m-x-1 m-s-12 m-x-tl boxservizi nowrap">';
                  if($campo->CalcoloPrezzo == 'A percentuale' && $campo->PercentualeServizio != ''){ 
                      $lista_servizi_aggiuntivi .='   
                                                        '.($campo->Obbligatorio==1?$obbligatory.'<div class="text_explan_percent" style="display:none">'.$TEXT_EXPLANE.'</div>':(($IdServizio[$campo->Id] ?? null) == 1?'<small>('.$IMPOSTO.')</small>'.'<div class="text_explan_percent" style="display:none">'.$TEXT_EXPLANE.'</div>':'<input type="checkbox" class="PrezzoServizio'.$n.'"  id="PrezzoServizio'.$n.'_'.$campo->Id.'" name="PrezzoServizio'.$n.'['.$campo->Id.']" value="'.$campo->PercentualeServizio.'#'.$campo->CalcoloPrezzo.'#'.$campo->Id.'"  '.(($IdServizio[$campo->Id] ?? null) == 1?'checked="checked"':'').'>'));
                  }else{
                      $lista_servizi_aggiuntivi .='  
                                                        '.($campo->Obbligatorio==1?$obbligatory:(($IdServizio[$campo->Id] ?? null) == 1?'<small>('.$IMPOSTO.')</small>':'<input type="checkbox" class="PrezzoServizio'.$n.'" id="PrezzoServizio'.$n.'_'.$campo->Id.'" name="PrezzoServizio'.$n.'['.$campo->Id.']" value="'.$campo->PrezzoServizio.'#'.$campo->CalcoloPrezzo.'#'.$campo->Id.'" '.($campo->Obbligatorio==1?'disabled="disabled"':'').' '.(($IdServizio[$campo->Id] ?? null) == 1?'checked="checked"':'').'>'));
                  }   
                      $lista_servizi_aggiuntivi .=' </div>';
                      $lista_servizi_aggiuntivi .=' <div class="m m-x-1 m-s-12 m-x-tl boxservizi"><div id="valori_serv_'.$n.'_'.$campo->Id.'" class="nowrap" style="font-size:75%"></div><div id="pulsante_calcola_'.$n.'_'.$campo->Id.'" class="nowrap" style="font-size:75%"></div><div id="spiegazione_prezzo_servizio_'.$n.'_'.$campo->Id.'" class="nowrap" style="font-size:75%"></div></div>';         
                      $lista_servizi_aggiuntivi .=' <div class="m m-x-3 m-s-3 m-x-tr m-s-tl boxservizi prezzo"><div id="Prezzo_Servizio_'.$n.'_'.$campo->Id.'">'.$PrezzoServizio.'</div><input type="hidden" name="notti'.$n.'_'.$campo->Id.'" id="notti'.$n.'_'.$campo->Id.'"/><input type="hidden" name="num_persone_'.$n.'_'.$campo->Id.'" id="num_persone_'.$n.'_'.$campo->Id.'" /><input type="hidden" id="RecPrezzo_Servizio_'.$n.'_'.$campo->Id.'" name="RecPrezzo_Servizio_'.$n.'_'.$campo->Id.'"></div>
            
                                                        </div>
                                                        <div class="ca"></div>';
            
                      $modali_servizi_aggiuntivi .=' <script>
                                                            <!-- funzione per eliminare nowrap class-->
                                                            checkScreenDimension("'.$campo->Id.'");
      
                                                            $(document).ready(function(){
      
                                                              <!-- funzione visualizzare il TUTTO contenuto testuale del servizio a percentuale -->
                                                              $("#pul_long_text_percent'.$n.'_'.$campo->Id.'").on("click",function(){
                                                                $("#long_text_percent'.$n.'_'.$campo->Id.'").show(\'slide\');
                                                                $("#pul_long_text_percent'.$n.'_'.$campo->Id.'").hide();
                                                              });
      
                                                                $("#PrezzoServizio'.$n.'_'.$campo->Id.'").change(function(){       
                                                                  
                                                                  <!-- funzione visualizzare la prima parte di contenuto testuale del servizio a percentuale -->
                                                                  $(".text_explan_percent").show(\'slide\');
      
                                                                            if(this.checked == true){
      
                                                                              var input_on = \'<input type="hidden" id="PrezzoServizioClone'.$n.'_'.$campo->Id.'" name="PrezzoServizioClone'.$n.'['.$campo->Id.']">\';
                                                                              $("#clone").append(input_on);
                                                                             
                                                                              var check = 1;
      
                                                                            }else{
                                                                             
                                                                              $("#PrezzoServizioClone'.$n.'_'.$campo->Id.'").remove();
                                                                           
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
                                                                            var idsito       = '.$idsito.';
                                                                            var n_proposta   = '.$n.';
                                                                            var id_proposta  = '.$id_proposta.';
                                                                            var id_servizio  = '.$campo->Id.';
                                                                            var RecPrezzo_Ser= $("#RecPrezzo_Servizio_'.$n.'_'.$campo->Id.'").val();
                                                                            var ReCalCap     = $("#ReCalCaparra'.$n.'_'.$id_proposta.'").val();
                                                                            var PercCaparra  = $("#PercentualeCaparra'.$n.'_'.$id_proposta.'").val();
                                                                            
                                                                            $.ajax({
                                                                                type: "POST",
                                                                                url: "/calc_prezzo_serv_landing",
                                                                                data: {"_token": "' . csrf_token() . '","idsito": idsito,"notti":notti,"dal":dal,"al":al,"n_proposta":n_proposta,"id_servizio":id_servizio,"ReCalPrezzo":ReCalPrezzo,"check":check,"RecPrezzo_Ser":RecPrezzo_Ser,"id_proposta":id_proposta,"ReCalCaparra":ReCalCap,"PercCaparra":PercCaparra},
                                                                                success: function(res){
                                                                                    $("#valori_serv_'.$n.'_'.$campo->Id.'").html(res);
                                                                                    $("#pulsante_calcola_'.$n.'_'.$campo->Id.'").show();
                                                                                },
                                                                                error: function(){
                                                                                    alert("Chiamata fallita, si prega di riprovare...");
                                                                                }
                                                                            });
                                                                      
                                                                      
                                                                });
      
                                                            });
                                                    </script>
                                                    <div class="modal fade" id="modal_persone_'.$n.'_'.$campo->Id.'"  role="dialog" aria-labelledby="myModalLabel">
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
                                                                                <label for="prezzo'.$n.'_'.$campo->Id.'">Prezzo Servizio</label>
                                                                                <input type="text" id="prezzo'.$n.'_'.$campo->Id.'" name="prezzo'.$n.'_'.$campo->Id.'" class="form-control" value="'.$campo->PrezzoServizio.'" readonly />
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-4 small nowrap">
                                                                            <div class="form-group">
                                                                                    <label for="Nnotti'.$n.'_'.$campo->Id.'">Numero Giorni</label>
                                                                                    <select id="Nnotti'.$n.'_'.$campo->Id.'" name="Nnotti'.$n.'_'.$campo->Id.'"  class="form-control" >
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
                                                                                    <label for="NPersone'.$n.'_'.$campo->Id.'">Numero Persone</label>
                                                                                    <select id="NPersone'.$n.'_'.$campo->Id.'" name="NPersone'.$n.'_'.$campo->Id.'" class="form-control" >
                                                                                        <option value="" selected="selected">--</option>
                                                                                        <option value="1" '.($NumeroAdulti==1 ?'selected="selected"':'').'>1</option>
                                                                                        <option value="2" '.($NumeroAdulti==2 ?'selected="selected"':'').'>2</option>
                                                                                        <option value="3" '.($NumeroAdulti==3 ?'selected="selected"':'').'>3</option>
                                                                                        <option value="4" '.($NumeroAdulti==4 ?'selected="selected"':'').'>4</option>
                                                                                        <option value="5" '.($NumeroAdulti==5 ?'selected="selected"':'').'>5</option>
                                                                                        <option value="6" '.($NumeroAdulti==6 ?'selected="selected"':'').'>6</option>
                                                                                        <option value="7" '.($NumeroAdulti==7 ?'selected="selected"':'').'>7</option>
                                                                                        <option value="8" '.($NumeroAdulti==8 ?'selected="selected"':'').'>8</option>
                                                                                        <option value="9" '.($NumeroAdulti==9 ?'selected="selected"':'').'>9</option>
                                                                                        <option value="10" '.($NumeroAdulti==10 ?'selected="selected"':'').'>10</option>
                                                                                    </select>
                                                                                </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-md-12 text-center">
                                                                            <input type="hidden" id="check'.$n.'_'.$campo->Id.'" name="check'.$n.'_'.$campo->Id.'">
                                                                            <input type="hidden" id="id_servizio'.$n.'_'.$campo->Id.'" name="id_servizio'.$n.'_'.$campo->Id.'" value="'.$campo->Id.'">
                                                                            <button type="button" class="btn btn-success" id="send_re_calc'.$n.'_'.$campo->Id.'" data-dismiss="modal" aria-label="Close">Calcola prezzo servizio</button>
                                                                        </div>
                                                                    </div>
                                                                    <script>
                                                                        $(document).ready(function() {
                                                                                $("#num_persone_'.$n.'_'.$campo->Id.'").on("show.bs.modal", function (event) {
                                                                                    var button = $(event.relatedTarget);
                                                                                    var xnotti = button.data("notti");
                                                                                    var prezzo = button.data("prezzo");
                                                                                    var id_servizio = button.data("id_servizio");
                                                                                    var modal = $(this);
                                                                                    modal.find(".modal-body select#Nnotti'.$n.'_'.$campo->Id.'").val(xnotti);
                                                                                    modal.find(".modal-body input#prezzo'.$n.'_'.$campo->Id.'").val(prezzo);
                                                                                    modal.find(".modal-body input#id_servizio'.$n.'_'.$campo->Id.'").val(id_servizio);
                                                                                });
                                                                                $("#send_re_calc'.$n.'_'.$campo->Id.'").on("click",function(){
                                                                                    var check         = 1;
                                                                                    var idsito        = '.$idsito.';
                                                                                    var n_proposta    = '.$n.';
                                                                                    var id_servizio   = $("#id_servizio'.$n.'_'.$campo->Id.'").val();
                                                                                    var notti         = $("#Nnotti'.$n.'_'.$campo->Id.'").val();
                                                                                    var prezzo        = $("#prezzo'.$n.'_'.$campo->Id.'").val();
                                                                                    var NPersone      = $("#NPersone'.$n.'_'.$campo->Id.'").val();
                                                                                    var ReCalPrezzo   = $("#ReCalPrezzo'.$n.'_'.$id_proposta.'").val();
                                                                                    var ReCalCap      = $("#ReCalCaparra'.$n.'_'.$id_proposta.'").val();
                                                                                    var PercCaparra   = $("#PercentualeCaparra'.$n.'_'.$id_proposta.'").val();
                                                                                    var id_proposta   = '.$id_proposta.';
                                                                                    var input_Nnotti = \'<input type="hidden" id="NumeroNotti'.$n.'_'.$campo->Id.'" name="NumeroNotti'.$n.'_'.$campo->Id.'" >\';
                                                                                    $("#clone").append(input_Nnotti);
                                                                                    $("#NumeroNotti'.$n.'_'.$campo->Id.'").val(notti);
                                                                                    var input_NPersone = \'<input type="hidden" id="NumeroPersone'.$n.'_'.$campo->Id.'" name="NumeroPersone'.$n.'_'.$campo->Id.'">\';
                                                                                    $("#clone").append(input_NPersone);
                                                                                    $("#NumeroPersone'.$n.'_'.$campo->Id.'").val(NPersone);
                                                                                    $.ajax({
                                                                                        type: "POST",
                                                                                        url: "/calc_prezzo_serv_a_persona_landing",
                                                                                        data: {"_token": "' . csrf_token() . '","idsito": idsito,"notti":notti,"prezzo":prezzo,"NPersone":NPersone,"n_proposta":n_proposta,"id_servizio":id_servizio,"ReCalPrezzo":ReCalPrezzo,"check":check,"id_proposta":id_proposta,"ReCalCaparra":ReCalCap,"PercCaparra":PercCaparra},
                                                                                        success: function(res){
                                                                                            $("#valori_serv_'.$n.'_'.$campo->Id.'").html(res);
                                                                                            $("#pulsante_calcola_'.$n.'_'.$campo->Id.'").hide();
                                                                                            $("input[data-tipo=persone'.$n.'_'.$campo->Id.']").remove();
                                                                                            $("input[data-tipo=notti'.$n.'_'.$campo->Id.']").remove();
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
                                      .($campo->Icona!=''?'<img id="TD'.$campo->Id.'" src="'.config('global.settings.BASE_URL_IMG').'uploads/'.$idsito.'/'.$campo->Icona.'" class="iconaservizi">&nbsp;':'').$Servizio.'</span></strong></div>
                                      <div class="m m-x-2 m-s-12 m-x-tc boxservizi">'.($Descrizione!=''?'<a href="javascript:;" data-toggle="tooltip" title="'.(strlen($Descrizione)<=300?stripslashes(strip_tags($Descrizione)):substr(stripslashes(strip_tags($Descrizione)),0,300).'...').'"><i class="fa fa-info-circle text-green" aria-hidden="true"></i></a>':'').' </div>';                                                               
                      $lista_servizi_aggiuntivi .=' <div class="m m-x-3 m-s-12 m-x-tl boxservizi">'.$calcoloprezzo.' '.$CalcoloPrezzoServizio.'</div> ';
      
                      $lista_servizi_aggiuntivi .=' <div class="m m-x-1 m-s-12 m-x-tl boxservizi nowrap">';
                      if($campo->CalcoloPrezzo == 'A percentuale' && $campo->PercentualeServizio != ''){ 
                      $lista_servizi_aggiuntivi .='   
                                          '.($campo->Obbligatorio==1?$obbligatory.'<div class="text_explan_percent" style="display:none">'.$TEXT_EXPLANE.'</div>':(($IdServizio[$campo->Id] ?? null) == 1?'<small>('.$IMPOSTO.')</small>'.'<div class="text_explan_percent" style="display:none">'.$TEXT_EXPLANE.'</div>':'<input type="checkbox" class="PrezzoServizio'.$n.'"  id="PrezzoServizio'.$n.'_'.$campo->Id.'" name="PrezzoServizio'.$n.'['.$campo->Id.']" value="'.$campo->PercentualeServizio.'#'.$campo->CalcoloPrezzo.'#'.$campo->Id.'"  '.(($IdServizio[$campo->Id] ?? null) == 1?'checked="checked"':'').'>'));
                      }else{
                      $lista_servizi_aggiuntivi .='  
                                          '.($campo->Obbligatorio==1?$obbligatory:(($IdServizio[$campo->Id] ?? null) == 1?'<small>('.$IMPOSTO.')</small>':'<input type="checkbox" class="PrezzoServizio'.$n.'" id="PrezzoServizio'.$n.'_'.$campo->Id.'" name="PrezzoServizio'.$n.'['.$campo->Id.']" value="'.$campo->PrezzoServizio.'#'.$campo->CalcoloPrezzo.'#'.$campo->Id.'" '.($campo->Obbligatorio==1?'disabled="disabled"':'').' '.(($IdServizio[$campo->Id] ?? null) == 1?'checked="checked"':'').'>'));
                      }   
                      $lista_servizi_aggiuntivi .=' </div>';
                      $lista_servizi_aggiuntivi .=' <div class="m m-x-1 m-s-12 m-x-tl boxservizi"><div id="valori_serv_'.$n.'_'.$campo->Id.'" class="nowrap" style="font-size:75%"></div><div id="pulsante_calcola_'.$n.'_'.$campo->Id.'" class="nowrap" style="font-size:75%"></div><div id="spiegazione_prezzo_servizio_'.$n.'_'.$campo->Id.'" class="nowrap" style="font-size:75%"></div></div>';         
                      $lista_servizi_aggiuntivi .=' <div class="m m-x-3 m-s-3 m-x-tr m-s-tl boxservizi prezzo"><div id="Prezzo_Servizio_'.$n.'_'.$campo->Id.'">'.$PrezzoServizio.'</div><input type="hidden" name="notti'.$n.'_'.$campo->Id.'" id="notti'.$n.'_'.$campo->Id.'"/><input type="hidden" name="num_persone_'.$n.'_'.$campo->Id.'" id="num_persone_'.$n.'_'.$campo->Id.'" /><input type="hidden" id="RecPrezzo_Servizio_'.$n.'_'.$campo->Id.'" name="RecPrezzo_Servizio_'.$n.'_'.$campo->Id.'"></div>
      
                                          </div>
                                          <div class="ca"></div>';
      
                      $modali_servizi_aggiuntivi .=' <script>
                                              <!-- funzione per eliminare nowrap class-->
                                              checkScreenDimension("'.$campo->Id.'");
      
                                              $(document).ready(function(){
      
                                                <!-- funzione visualizzare il TUTTO contenuto testuale del servizio a percentuale -->
                                                $("#pul_long_text_percent'.$n.'_'.$campo->Id.'").on("click",function(){
                                                  $("#long_text_percent'.$n.'_'.$campo->Id.'").show(\'slide\');
                                                  $("#pul_long_text_percent'.$n.'_'.$campo->Id.'").hide();
                                                });
      
                                                  $("#PrezzoServizio'.$n.'_'.$campo->Id.'").change(function(){       
                                                    
                                                    <!-- funzione visualizzare la prima parte di contenuto testuale del servizio a percentuale -->
                                                    $(".text_explan_percent").show(\'slide\');
      
                                                              if(this.checked == true){
      
                                                                var input_on = \'<input type="hidden" id="PrezzoServizioClone'.$n.'_'.$campo->Id.'" name="PrezzoServizioClone'.$n.'['.$campo->Id.']">\';
                                                                $("#clone").append(input_on);
                                                              
                                                                var check = 1;
      
                                                              }else{
                                                              
                                                                $("#PrezzoServizioClone'.$n.'_'.$campo->Id.'").remove();
                                                            
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
                                                              var idsito       = '.$idsito.';
                                                              var n_proposta   = '.$n.';
                                                              var id_proposta  = '.$id_proposta.';
                                                              var id_servizio  = '.$campo->Id.';
                                                              var RecPrezzo_Ser= $("#RecPrezzo_Servizio_'.$n.'_'.$campo->Id.'").val();
                                                              var ReCalCap     = $("#ReCalCaparra'.$n.'_'.$id_proposta.'").val();
                                                              var PercCaparra  = $("#PercentualeCaparra'.$n.'_'.$id_proposta.'").val();
                                                              
                                                              $.ajax({
                                                                  type: "POST",
                                                                  url: "/calc_prezzo_serv_landing",
                                                                 data: {"_token": "' . csrf_token() . '","idsito": idsito,"notti":notti,"dal":dal,"al":al,"n_proposta":n_proposta,"id_servizio":id_servizio,"ReCalPrezzo":ReCalPrezzo,"check":check,"RecPrezzo_Ser":RecPrezzo_Ser,"id_proposta":id_proposta,"ReCalCaparra":ReCalCap,"PercCaparra":PercCaparra},
                                                                  success: function(res){
                                                                      $("#valori_serv_'.$n.'_'.$campo->Id.'").html(res);
                                                                      $("#pulsante_calcola_'.$n.'_'.$campo->Id.'").show();
                                                                  },
                                                                  error: function(){
                                                                      alert("Chiamata fallita, si prega di riprovare...");
                                                                  }
                                                              });
                                                        
                                                        
                                                  });
      
                                              });
                                      </script>
                                      <div class="modal fade" id="modal_persone_'.$n.'_'.$campo->Id.'"  role="dialog" aria-labelledby="myModalLabel">
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
                                                                  <label for="prezzo'.$n.'_'.$campo->Id.'">Prezzo Servizio</label>
                                                                  <input type="text" id="prezzo'.$n.'_'.$campo->Id.'" name="prezzo'.$n.'_'.$campo->Id.'" class="form-control" value="'.$campo->PrezzoServizio.'" readonly />
                                                              </div>
                                                          </div>
                                                          <div class="col-md-4 small nowrap">
                                                              <div class="form-group">
                                                                      <label for="Nnotti'.$n.'_'.$campo->Id.'">Numero Giorni</label>
                                                                      <select id="Nnotti'.$n.'_'.$campo->Id.'" name="Nnotti'.$n.'_'.$campo->Id.'"  class="form-control" >
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
                                                                      <label for="NPersone'.$n.'_'.$campo->Id.'">Numero Persone</label>
                                                                      <select id="NPersone'.$n.'_'.$campo->Id.'" name="NPersone'.$n.'_'.$campo->Id.'" class="form-control" >
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
                                                              <input type="hidden" id="check'.$n.'_'.$campo->Id.'" name="check'.$n.'_'.$campo->Id.'">
                                                              <input type="hidden" id="id_servizio'.$n.'_'.$campo->Id.'" name="id_servizio'.$n.'_'.$campo->Id.'" value="'.$campo->Id.'">
                                                              <button type="button" class="btn btn-success" id="send_re_calc'.$n.'_'.$campo->Id.'" data-dismiss="modal" aria-label="Close">Calcola prezzo servizio</button>
                                                          </div>
                                                      </div>
                                                      <script>
                                                          $(document).ready(function() {
                                                                  $("#num_persone_'.$n.'_'.$campo->Id.'").on("show.bs.modal", function (event) {
                                                                      var button = $(event.relatedTarget);
                                                                      var xnotti = button.data("notti");
                                                                      var prezzo = button.data("prezzo");
                                                                      var id_servizio = button.data("id_servizio");
                                                                      var modal = $(this);
                                                                      modal.find(".modal-body select#Nnotti'.$n.'_'.$campo->Id.'").val(xnotti);
                                                                      modal.find(".modal-body input#prezzo'.$n.'_'.$campo->Id.'").val(prezzo);
                                                                      modal.find(".modal-body input#id_servizio'.$n.'_'.$campo->Id.'").val(id_servizio);
                                                                  });
                                                                  $("#send_re_calc'.$n.'_'.$campo->Id.'").on("click",function(){
                                                                      var check         = 1;
                                                                      var idsito        = '.$idsito.';
                                                                      var n_proposta    = '.$n.';
                                                                      var id_servizio   = $("#id_servizio'.$n.'_'.$campo->Id.'").val();
                                                                      var notti         = $("#Nnotti'.$n.'_'.$campo->Id.'").val();
                                                                      var prezzo        = $("#prezzo'.$n.'_'.$campo->Id.'").val();
                                                                      var NPersone      = $("#NPersone'.$n.'_'.$campo->Id.'").val();
                                                                      var ReCalPrezzo   = $("#ReCalPrezzo'.$n.'_'.$id_proposta.'").val();
                                                                      var ReCalCap      = $("#ReCalCaparra'.$n.'_'.$id_proposta.'").val();
                                                                      var PercCaparra   = $("#PercentualeCaparra'.$n.'_'.$id_proposta.'").val();
                                                                      var id_proposta   = '.$id_proposta.';
                                                                      var input_Nnotti = \'<input type="hidden" id="NumeroNotti'.$n.'_'.$campo->Id.'" name="NumeroNotti'.$n.'_'.$campo->Id.'" >\';
                                                                      $("#clone").append(input_Nnotti);
                                                                      $("#NumeroNotti'.$n.'_'.$campo->Id.'").val(notti);
                                                                      var input_NPersone = \'<input type="hidden" id="NumeroPersone'.$n.'_'.$campo->Id.'" name="NumeroPersone'.$n.'_'.$campo->Id.'">\';
                                                                      $("#clone").append(input_NPersone);
                                                                      $("#NumeroPersone'.$n.'_'.$campo->Id.'").val(NPersone);
                                                                      $.ajax({
                                                                          type: "POST",
                                                                          url: "/calc_prezzo_serv_a_persona_landing",
                                                                          data: {"_token": "' . csrf_token() . '","idsito": idsito,"notti":notti,"prezzo":prezzo,"NPersone":NPersone,"n_proposta":n_proposta,"id_servizio":id_servizio,"ReCalPrezzo":ReCalPrezzo,"check":check,"id_proposta":id_proposta,"ReCalCaparra":ReCalCap,"PercCaparra":PercCaparra},
                                                                          success: function(res){
                                                                              $("#valori_serv_'.$n.'_'.$campo->Id.'").html(res);
                                                                              $("#pulsante_calcola_'.$n.'_'.$campo->Id.'").hide();
                                                                              $("input[data-tipo=persone'.$n.'_'.$campo->Id.']").remove();
                                                                              $("input[data-tipo=notti'.$n.'_'.$campo->Id.']").remove();
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



    public function custom1_template($directory, $params, Request $request)
    {
        $template = 'custom1';
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
            $ArrivoData               = $value->DataArrivo;
            $DataA_tmp                = explode("-",$value->DataArrivo);
            $DataArrivo               = $DataA_tmp[2].'/'.$DataA_tmp[1].'/'.$DataA_tmp[0];
            $PartenzaData             = $value->DataPartenza;
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
            }else{
                $DataScadenza        = '';
                $DataScadenza_estesa = '';
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
        $sel_color = "SELECT * FROM hospitality_template_background WHERE idsito = :idsito AND TemplateType = :TemplateType LIMIT 1";
        $res_color = DB::select($sel_color,['idsito' => $idsito, 'TemplateType' => 'custom1']);
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

        $condizioni_tariffa  = dizionario('CONDIZIONI_TARIFFA');
        $sconto              = dizionario('SCONTO');
        $eti1                = dizionario('ARRIVO');
        $eti2                = dizionario('PARTENZA');
        $eti3                = dizionario('PERSONE');
        $eti4                = dizionario('CAMERE');
        $eti5                = dizionario('TRATTAMENTO');
        $eti6                = dizionario('PREZZO_TOTALE');
        $eti7                = dizionario('SCONTO').':';
        $eti8                = dizionario('SOGGIORNO');
        $eti9                = dizionario('QUANTITA');
        $eti10               = dizionario('PREZZO_UNITARIO');
        $eti11               = dizionario('SUBTOTALE');
        $eti12               = '<i class="fal fa-check"></i> '.dizionario('PRENOTA_SUBITO');
        $eti13               = dizionario('CAPARRA_RICHIESTA').':';
        #ICONE
        $eti1fa='<i class="fal fa-arrow-right"></i>';
        $eti2fa='<i class="fal fa-arrow-left"></i>';
        $eti3fa='<i class="fal fa-male"></i>';
        $eti4fa='<i class="fal fa-star"></i>';
        $eti5fa='<i class="fal fa-bell"></i>';
        $eti6fa='<i class="fal fa-euro-sign"></i>';


        $selP = "SELECT
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
                    FROM hospitality_proposte
                    WHERE hospitality_proposte.id_richiesta = :id_richiesta";
        $hr = DB::select($selP,['id_richiesta' => $Id]);
        $r_n = sizeof($hr);

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
        $Arrivo              = '';
        $Partenza            = '';
        $A                   = '';
        $P                   = '';

        $n = 1;


        $valore               = '';
        $servizi              = '';
        $Servizi              = '';
        $services             = '';
        $id_servizio          = '';
        $camere               = '';
        $FCamere              = '';
        $proposta_titolo      = '';
        $proposta_specchietto = '';
        $proposta_camera      = '';
        $proposta_conteggio   = '';
        $proposta_form        = '';
        $Nomi_camera          = '';
        $Subtotale            = '';
        $totale               = '';
        $tariffe              = '';
        $AccontoTariffa       = '';
        $AccontoTesto         = '';
        $NumAdulti            = '';
        $ANotti               = '';
        $NumBambini           = '';
        $Arrivo_estesa        = '';
        $Partenza_estesa      = '';
        $alternative          = '';
        $SERVIZIAGGIUNTIVI    = '';
        $VAUCHERCamere        = '';
        $TitoloSoggiorno    = '';

        foreach($hr as $Key => $value) {

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
                    } else{
                        if(($PrezzoL!='0,00')){
                        $percentuale_sconto_calcolo = (100-(100*$value->PrezzoP)/$value->PrezzoL);
                        $percentuale_sconto =  str_replace(",00", "",number_format((100-(100*$value->PrezzoP)/$value->PrezzoL),2,',','.'));
                        $ImportoSconto = number_format(($value->PrezzoL-$value->PrezzoP),2,',','.');
                        }
                    }
                    if($imp_sconto != 0 && $imp_sconto != ''){
                        /*calcolo l'importo dello sconto*/
                        $selSconto     = "SELECT SUM(hospitality_richiesta.Prezzo) as prezzo_camere FROM hospitality_richiesta WHERE hospitality_richiesta.id_richiesta = :id_richiesta AND hospitality_richiesta.id_proposta = :id_proposta";
                        $resSconto     = DB::select($selSconto,['id_richiesta' => $Id, 'id_proposta' => $IdProposta]);
                        if(sizeof($resSconto)>0){
                            $recSconto     = $resSconto[0];
                            $ImpSconto    = (($recSconto->prezzo_camere*$percentuale_sconto)/100);
                            $ImportoSconto = number_format($ImpSconto,2,',','.');
                        }
                    } 

                }else{
                    $ImportoSconto      = '';
                    $percentuale_sconto = '';
                }
      

                    $select2 = "SELECT hospitality_richiesta.NumeroCamere,
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
                                    FROM hospitality_richiesta
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
                    $x                    = 1;
                    $Servizi              = '';
                    $serv                 = '';
                    $servizi              = '';
                    $services             = '';
                    $image_room           = '';
                    $Nomi_camera          = '';


                    $proposta_camera .= ' <div class="m m-x-12 camera" id="camera'.$n.'">';

                    if($NomeProposta!='' || $TestoProposta!=''){
                    $proposta_camera .='
                                <div class="m m-x-12">
                                    <div class="box3">
                                        <b>'.$NomeProposta.'</b>
                                        <p>'.nl2br($TestoProposta).'</p>
                                    </div>
                                </div>
                                <div class="ca"></div>';
                    }

                $totale = 0;
                $EtaB  = '';
                $EtaB_ = '';
                    foreach($result2 as $key => $val) {

                        $Servizi         = $val->Servizi;
                        $NumeroCamere    = $val->NumeroCamere;

                        $sel_bamb = "SELECT hospitality_richiesta.NumAdulti,hospitality_richiesta.NumBambini,hospitality_richiesta.EtaB FROM hospitality_richiesta WHERE  hospitality_richiesta.Id = :Id";
                        $res_bamb = DB::select($sel_bamb,['Id' => $val->id_etaB]);
                        if(sizeof($res_bamb)>0){

                            $rec_B      = $res_bamb[0];

                            $EtaB       = $rec_B->EtaB;
                            $NumAdulti  = $rec_B->NumAdulti;
                            $NumBambini = $rec_B->NumBambini;

                            switch($NumAdulti){
                                case 1:
                                    $ico_adulti = '<i class="fa fa-male"></i>';
                                break;
                                case 2:
                                    $ico_adulti = '<i class="fa fa-male"></i><i class="fa fa-male"></i>';
                                break;
                                case 3:
                                    $ico_adulti = '<i class="fa fa-male"></i><i class="fa fa-male"></i><i class="fa fa-male"></i>';
                                break;
                                case 4:
                                    $ico_adulti = '<i class="fa fa-male"></i><i class="fa fa-male"></i><i class="fa fa-male"></i><i class="fa fa-male"></i>';
                                break;
                                case 5:
                                    $ico_adulti = '<i class="fa fa-male"></i><i class="fa fa-male"></i><i class="fa fa-male"></i><i class="fa fa-male"></i><i class="fa fa-male"></i>';
                                break;
                                case 6:
                                    $ico_adulti = '<i class="fa fa-male"></i><i class="fa fa-male"></i><i class="fa fa-male"></i><i class="fa fa-male"></i><i class="fa fa-male"></i><i class="fa fa-male"></i>';
                                break;
                                default:
                                    $ico_adulti = $NumAdulti;
                                break;
                            }
                            switch($NumBambini){
                                case 1:
                                    $ico_bimbi = '<i class="fa fa-child"></i>';
                                break;
                                case 2:
                                    $ico_bimbi = '<i class="fa fa-child"></i><i class="fa fa-child"></i>';
                                break;
                                case 3:
                                    $ico_bimbi = '<i class="fa fa-child"></i><i class="fa fa-child"></i><i class="fa fa-child"></i>';
                                break;
                                case 4:
                                    $ico_bimbi = '<i class="fa fa-child"></i><i class="fa fa-child"></i><i class="fa fa-child"></i><i class="fa fa-child"></i>';
                                break;
                                case 5:
                                    $ico_bimbi = '<i class="fa fa-child"></i><i class="fa fa-child"></i><i class="fa fa-child"></i><i class="fa fa-child"></i><i class="fa fa-child"></i>';
                                break;
                                case 6:
                                    $ico_bimbi = '<i class="fa fa-child"></i><i class="fa fa-child"></i><i class="fa fa-child"></i><i class="fa fa-child"></i><i class="fa fa-child"></i><i class="fa fa-child"></i>';
                                break;
                                default:
                                    $ico_bimbi = $NumBambini;
                                break;
                            }
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

                        $VAUCHERCamere .= '<p>'.$val->TitoloSoggiorno.' <i class=\'fa fa-angle-right\'></i> Nr. '.$val->NumeroCamere.' '.$val->TipoCamere.' '.($DataRichiestaCheck > config('global.settings.DATA_QUOTO_V2') ?($NumAdulti!=0?$ico_adulti:'').' '.($NumBambini!=0?$ico_bimbi:'').' '.($EtaB!='' && $EtaB!=0?''.dizionario('ETA').' '.$EtaB.' ':''):'').' - €. '.number_format($val->Prezzo,2,',','.').'</p>';

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
                            $image_room  = '';

                            foreach($res as $k => $v){

                                $image_room .='<li style="height:425px!important"><img src="'.config('global.settings.BASE_URL_IMG').'uploads/'.$idsito.'/'.$v->Foto.'" /></li>';

                            }

                            if ($x>1){
                                $proposta_camera .='<hr class="twhite" style="margin:0px!important">';
                            }

                            $proposta_camera .='<!--CAMERE '.$x.'-->
                                            <script>
                                                    $(document).ready(function(){
                                                            $("#slider'.$x.'_'.$IdCamera.'_'.$IdProposta.'").responsiveSlides({
                                                                    auto: true,
                                                                    pager: false,
                                                                    nav: true,
                                                                    namespace: "centered-btns"
                                                                });
                                                    });
                                                </script>

                                                <div class="m m-x-12 riga">
                                                    <div class="m m-x-6 m-xs-12  m-img">
                                                    <div class="callbacks_container">
                                                        <ul class="rslides" id="slider'.$x.'_'.$IdCamera.'_'.$IdProposta.'">
                                                        '.$image_room.'
                                                        </ul>
                                                    </div>
                                                    </div>
                                                    <div class="m m-x-6 m-xs-12 m-x-tl t18  ">
                                                    <div class="box3">
                                                        <h4><b>'.$TitoloCamera.'</b></h4>
                                                        <div class="ca20"></div>
                                                        '.$TestoCamera.'<br><br>
                                                        <strong>'.dizionario('SERVIZI_CAMERA').'</strong><br>
                                                        '.$servizi.'<br><br>
                                                        <strong>'.$TitoloSoggiorno.'</strong><br>
                                                        '.$TestoSoggiorno.'
                                                    </div>
                                                    </div>
                                                </div>';


                        $camere  .='<div class="m m-x-12 riga t16 txs11 w700">
                                        <div class="m m-x-3">
                                            <div class="box6">
                                                <a href="javascript:;" data-toggle="tooltip" data-placement="right" id="example'.$IdCamera.'" title="'.(strlen($TestoSoggiorno)>=300?strip_tags(substr($TestoSoggiorno,0,300).' ...'):strip_tags($TestoSoggiorno)).'"><i class="fa fa-info-circle text-green" aria-hidden="true"></i></a> '.$TitoloSoggiorno.' - '.($DataRichiestaCheck > config('global.settings.DATA_QUOTO_V2')?'nr.'.$NumeroCamere:'').' '.$TitoloCamera.'
                                            </div>
                                        </div>
                                        <div class="m m-x-3 m-x-tc">
                                            <div class="box6">
                                                '.($DataRichiestaCheck > config('global.settings.DATA_QUOTO_V2') ?'<span data-toggle="tooltip" data-placement="right" data-html="true" title="'.($NumAdulti!=0?dizionario('ADULTI').' <b>'.$NumAdulti.'</b>':'').' <br>'.($NumBambini!=0?dizionario('BAMBINI').' <b>' .$NumBambini.'</b>':'').'">'.($NumAdulti!=0?$ico_adulti:'').' '.($NumBambini!=0?$ico_bimbi:'').' '.($EtaB!='' && $EtaB!=0?''.dizionario('ETA').' '.$EtaB.'':'').'</span>':'').'
                                            </div>
                                        </div>
                                        <div class="m m-x-3 m-x-tc">
                                            <div class="box6">
                                                '.($DataRichiestaCheck > config('global.settings.DATA_QUOTO_V2')?($ANotti!=''?$ANotti:$Notti):$NumeroCamere) .'
                                            </div>
                                        </div>
                                        <div class="m m-x-3 m-x-tr">
                                            <div class="box6">
                                                <i class="fal fa-euro-sign"></i> '.$Prezzo .'
                                            </div>
                                        </div>
                                    </div>';


                        $Nomi_camera .= $TitoloCamera.'<br>';
                        $x++;

                    }
                    $EtaB = '';
                    $EtaB_ = '';
                    $proposta_camera .= '</div>';

                    $FCamere = substr($FCamere,0,-2);


            if($TipoRichiesta == 'Preventivo') {

                $valore = ucfirst(strtolower(dizionario('ARRIVO'))).' '.$DataArrivo.' - '.ucfirst(strtolower(dizionario('PARTENZA'))).' '.$DataPartenza.' - '.$FCamere .'  -  '.dizionario('ADULTI').' '.$NumeroAdulti.' '.($NumeroBambini!='0'?' - '.dizionario('BAMBINI').' '.$NumeroBambini:'').'  - Totale €. '.$PrezzoP.'';

            }


        $proposta_titolo .= ' <div class="m m-eqtab tab2019" id="tab'.$n.'" proposta="'.$n.'">
                                <titolo>'.($TipoRichiesta == 'Preventivo'?dizionario('PROPOSTA'):dizionario('SOLUZIONECONFERMATA')).' '.$n.'</titolo>
                                <prezzo><i class="fal fa-euro-sign"></i> <span id="PrezzoTitolo'.$n.'">'.$PrezzoP_format.'</span></prezzo>
                                </div>
                                <script>
                                $(document).ready(function(){
                                    $("#tab'.$n.'").on("click",function(){
                                    var n_prop = '.$n.';
                                    $("#clone").empty();
                                    $("#TextNewTotale").empty();
                                    $("#NumeroProposta").val(\'\');
                                    $("#NewTotale").val(\'\'); 
                                    $("#PrezzoSpecchietto'.$n.'").html(\''.$PrezzoP_format.'\');
                                    $("#PrezzoTitolo'.$n.'").html(\''.$PrezzoP_format.'\');
                                    $("#PrezzoForm'.$n.'").html(\''.$PrezzoP_format.'\');
                                    $("#PrezzoPC'.$n.'_'.$IdProposta.'").html(\''.$PrezzoP_format.'\');
                                    $("#ReCalPrezzo'.$n.'_'.$IdProposta.'").val(\''.$PrezzoP.'\');
                                    $("#ReCalCaparra'.$n.'_'.$IdProposta.'").html(\''.number_format(($PrezzoPC*$AccontoPercentuale/100),2,',','.').'\');
                                    if(n_prop == 1){
                                        $(".PrezzoServizio2").prop("checked",false);                
                                        $(".PrezzoServizio3").prop("checked",false);       
                                        $(".PrezzoServizio4").prop("checked",false);     
                                        $(".PrezzoServizio5").prop("checked",false);                                 
                                    }
                                    if(n_prop == 2){
                                        $(".PrezzoServizio1").prop("checked",false);
                                        $(".PrezzoServizio3").prop("checked",false);
                                        $(".PrezzoServizio4").prop("checked",false);
                                        $(".PrezzoServizio5").prop("checked",false);               
                                    }
                                    if(n_prop == 3){     
                                        $(".PrezzoServizio2").prop("checked",false);
                                        $(".PrezzoServizio1").prop("checked",false);
                                        $(".PrezzoServizio4").prop("checked",false);       
                                        $(".PrezzoServizio5").prop("checked",false);
                                    }
                                    if(n_prop == 4){ 
                                        $(".PrezzoServizio2").prop("checked",false);
                                        $(".PrezzoServizio3").prop("checked",false);                           
                                        $(".PrezzoServizio1").prop("checked",false);                            
                                        $(".PrezzoServizio5").prop("checked",false);                               
                                    }
                                    if(n_prop == 5){                          
                                        $(".PrezzoServizio2").prop("checked",false);                           
                                        $(".PrezzoServizio3").prop("checked",false);                             
                                        $(".PrezzoServizio4").prop("checked",false);                             
                                        $(".PrezzoServizio1").prop("checked",false);                               
                                    }
                                    });
                                });
                            </script> ';
        $proposta_specchietto .='<div class="m m-x-4 contenitore" id="contenitore'.$n.'">';
        if($A != '' && $P != '' && $A != '0000-00-00' && $P != '0000-00-00'){
            if($TipoRichiesta=='Preventivo'){

            if($ArrivoData != $A || $PartenzaData != $P){
                $proposta_specchietto .='<linea class="twhite">'.$eti1fa.' '.dizionario('ARRIVO').' ('.strtoupper(dizionario('DATEALTERNATIVE')).')<div class="w300 t18">'.$Arrivo_estesa.'</div></linea>
                                        <linea class="twhite">'.$eti1fa.' '.dizionario('PARTENZA').' ('.strtoupper(dizionario('DATEALTERNATIVE')).')<div class="w300 t18">'.$Partenza_estesa.'</div></linea>';
            }

            }elseif($TipoRichiesta=='Conferma'){

                if($DataArrivo != $Arrivo ){
                    $DataArrivo_estesa   = $Arrivo_estesa;
                    $Notti = $ANotti;
                }
                if($DataPartenza != $Partenza){
                    $DataPartenza_estesa   = $Partenza_estesa;
                    $Notti = $ANotti;
                }

            }


        }
        $proposta_specchietto .='<linea class="twhite">'.$eti1fa.$eti1.'<div class="w300 t18">'.$DataArrivo_estesa.'</div></linea>
                                <linea class="twhite">'.$eti2fa.$eti2.'<div class="w300 t18">'.$DataPartenza_estesa.'</div></linea>
                                <linea class="twhite">'.$eti3fa.$eti3.'<div class="w300 t18">'.dizionario('ADULTI').' '.$NumeroAdulti .' '.($NumeroBambini!='0'?' - '.dizionario('BAMBINI').' '.$NumeroBambini .' '.($EtaBambini1!='' && $EtaBambini1!='0'?'<br>'.$EtaBambini1.' '.dizionario('ANNI').' ':'').($EtaBambini2!='' && $EtaBambini2!='0'?' - '.$EtaBambini2.' '.dizionario('ANNI').' ':'').($EtaBambini3!='' && $EtaBambini3!='0'?' - '.$EtaBambini3.' '.dizionario('ANNI').' ':'').($EtaBambini4!='' && $EtaBambini4!='0'?' - '.$EtaBambini4.' '.dizionario('ANNI').' ':'').($EtaBambini5!='' && $EtaBambini5!='0'?' - '.$EtaBambini5.' '.dizionario('ANNI').' ':'').($EtaBambini6!='' && $EtaBambini6!='0'?' - '.$EtaBambini6.' '.dizionario('ANNI').' ':'').' '.($EtaB!='' && $EtaB!=0?''.dizionario('ETA').': '.$EtaB.'':''):'').'<br>'.dizionario('NOTTI').' '.($DataRichiestaCheck > config('global.settings.DATA_QUOTO_V2')?($ANotti!=''?$ANotti:$Notti):$Notti).'</div></linea>
                                <linea class="twhite">'.$eti4fa.$eti4.'<div class="w300 t18">'.$Nomi_camera.'</div></linea>
                                <linea class="twhite">'.$eti5fa.$eti5.'<div class="w300 t18">'.$TitoloSoggiorno.'</div></linea>
                                <linea class="twhite">'.$eti6fa.$eti6.'<div class="w300 t12">'.dizionario('SCADENZA').' '.$DataScadenza_estesa.'</div>
                                    <totale>'.(($PrezzoL!='0,00' || $PrezzoL > $PrezzoP)?$PrezzoL:'').'</totale>
                                    <sconto>'.(($PrezzoL!='0,00')?' '.$sconto.' '.$percentuale_sconto.' %':'').'</sconto>
                                    <newtotale id="PrezzoSpecchietto'.$n.'">'.$PrezzoP_format.'</newtotale>
                                </linea>';

        $proposta_specchietto .= '</div>';

        if($A != '' && $P != '' && $A != '0000-00-00' && $P != '0000-00-00'){

            if($TipoRichiesta=='Preventivo'){

                if($ArrivoData != $A || $PartenzaData != $P){

                    $alternative = ucfirst(strtolower(dizionario('ARRIVO').' '.dizionario('DATEALTERNATIVE'))).' '.$Arrivo.' - '.ucfirst(strtolower(dizionario('PARTENZA').' '.dizionario('DATEALTERNATIVE'))).' '.$Partenza.' - ';
                }
            }elseif($TipoRichiesta=='Conferma'){

                if($DataArrivo != $Arrivo ){
                    $DataArrivo_estesa   = $Arrivo_estesa;
                    $Notti = $ANotti;
                }
                if($DataPartenza != $Partenza){
                    $DataPartenza_estesa   = $Partenza_estesa;
                    $Notti = $ANotti;
                }

            }
        }
        $proposta_form .='  <script>
                                $(document).ready(function(){
                                    $("#fproposta'.$n.'").on("click",function(){
                                    var n_prop = '.$n.';
                                    $("#clone").empty();
                                    $("#TextNewTotale").empty();
                                    $("#NumeroProposta").val(\'\');
                                    $("#NewTotale").val(\'\'); 
                                    $("#PrezzoSpecchietto'.$n.'").html(\''.$PrezzoP_format.'\');
                                    $("#PrezzoTitolo'.$n.'").html(\''.$PrezzoP_format.'\');
                                    $("#PrezzoForm'.$n.'").html(\''.$PrezzoP_format.'\');
                                    $("#PrezzoPC'.$n.'_'.$IdProposta.'").html(\''.$PrezzoP_format.'\');
                                    $("#ReCalPrezzo'.$n.'_'.$IdProposta.'").val(\''.$PrezzoP.'\');
                                    $("#ReCalCaparra'.$n.'_'.$IdProposta.'").html(\''.number_format(($PrezzoPC*$AccontoPercentuale/100),2,',','.').'\');
                                    if(n_prop == 1){
                                        $(".PrezzoServizio2").prop("checked",false);                
                                        $(".PrezzoServizio3").prop("checked",false);       
                                        $(".PrezzoServizio4").prop("checked",false);     
                                        $(".PrezzoServizio5").prop("checked",false);                                 
                                    }
                                    if(n_prop == 2){
                                        $(".PrezzoServizio1").prop("checked",false);
                                        $(".PrezzoServizio3").prop("checked",false);
                                        $(".PrezzoServizio4").prop("checked",false);
                                        $(".PrezzoServizio5").prop("checked",false);               
                                    }
                                    if(n_prop == 3){     
                                        $(".PrezzoServizio2").prop("checked",false);
                                        $(".PrezzoServizio1").prop("checked",false);
                                        $(".PrezzoServizio4").prop("checked",false);       
                                        $(".PrezzoServizio5").prop("checked",false);
                                    }
                                    if(n_prop == 4){ 
                                        $(".PrezzoServizio2").prop("checked",false);
                                        $(".PrezzoServizio3").prop("checked",false);                           
                                        $(".PrezzoServizio1").prop("checked",false);                            
                                        $(".PrezzoServizio5").prop("checked",false);                               
                                    }
                                    if(n_prop == 5){                          
                                        $(".PrezzoServizio2").prop("checked",false);                           
                                        $(".PrezzoServizio3").prop("checked",false);                             
                                        $(".PrezzoServizio4").prop("checked",false);                             
                                        $(".PrezzoServizio1").prop("checked",false);                               
                                    }
                                    });
                                });
                            </script> 
                            <div class="m m-x-12 fproposta twhite" id="fproposta'.$n.'" propostaid="'.$n.'" idprop="'. $IdProposta .'" propostatitolo="'.$alternative.$valore.'">
                                <i class="fal fa-circle"></i>
                                <i class="fal fa-check-circle"></i>
                                <div class="m m-x-12 riga titolo">'.dizionario('PROPOSTA').' '.$n.'</div>
                                <div class="m m-x-12 specchietto">';
        if($A != '' && $P != '' && $A != '0000-00-00' && $P != '0000-00-00'){
            if($ArrivoData != $A || $PartenzaData != $P){
                $proposta_form .='  <linea class="twhite">'.$eti1fa.' '.dizionario('ARRIVO').' ('.strtoupper(dizionario('DATEALTERNATIVE')).')<div class="w300 t18">'.$Arrivo_estesa.'</div></linea>
                                    <linea class="twhite">'.$eti1fa.' '.dizionario('PARTENZA').' ('.strtoupper(dizionario('DATEALTERNATIVE')).')<div class="w300 t18">'.$Partenza_estesa.'</div></linea>';
            }
        }
        $proposta_form .='     <linea class="twhite">'.$eti1fa.$eti1.'<div class="w300 t18">'.$DataArrivo_estesa.'</div></linea>
                                <linea class="twhite">'.$eti2fa.$eti2.'<div class="w300 t18">'.$DataPartenza_estesa.'</div></linea>
                                <linea class="twhite">'.$eti3fa.$eti3.'<div class="w300 t18">'.dizionario('ADULTI').' '.$NumeroAdulti .' '.($NumeroBambini!='0'?' - '.dizionario('BAMBINI').' '.$NumeroBambini .' '.($EtaBambini1!='' && $EtaBambini1!='0'?'<br>'.$EtaBambini1.' '.dizionario('ANNI').' ':'').($EtaBambini2!='' && $EtaBambini2!='0'?' - '.$EtaBambini2.' '.dizionario('ANNI').' ':'').($EtaBambini3!='' && $EtaBambini3!='0'?' - '.$EtaBambini3.' '.dizionario('ANNI').' ':'').($EtaBambini4!='' && $EtaBambini4!='0'?' - '.$EtaBambini4.' '.dizionario('ANNI').' ':'').($EtaBambini5!='' && $EtaBambini5!='0'?' - '.$EtaBambini5.' '.dizionario('ANNI').' ':'').($EtaBambini6!='' && $EtaBambini6!='0'?' - '.$EtaBambini6.' '.dizionario('ANNI').' ':'').' '.($EtaB!='' && $EtaB!=0?''.dizionario('ETA').': '.$EtaB.'':''):'').'<br>'.dizionario('NOTTI').' '.($DataRichiestaCheck > config('global.settings.DATA_QUOTO_V2')?($ANotti!=''?$ANotti:$Notti):$Notti).'</div></linea>
                                <linea class="twhite">'.$eti4fa.$eti4.'<div class="w300 t18">'.$Nomi_camera.'</div></linea>
                                <linea class="twhite">'.$eti5fa.$eti5.'<div class="w300 t18">'.$TitoloSoggiorno.'</div></linea>
                                <linea class="twhite">'.$eti6fa.$eti6.'<div class="w300 t12">'.dizionario('SCADENZA').' '.$DataScadenza_estesa.'</div>
                                    <totale>'.(($PrezzoL!='0,00' || $PrezzoL > $PrezzoP)?$PrezzoL:'').'</totale>
                                    <sconto>'.(($PrezzoL!='0,00')?' '.$sconto.' '.$percentuale_sconto.' %':'').'</sconto>
                                    <newtotale id="PrezzoForm'.$n.'">'.$PrezzoP_format.'</newtotale>
                                    </linea>
                                </div>
                            </div>';

        if($DataArrivo_estesa == $Arrivo_estesa && $DataPartenza_estesa == $Partenza_estesa){
            $etichetteDate =  '( '.$DataArrivo_estesa.' / '.$DataPartenza_estesa.' )';
        }else{
            $etichetteDate = '( '.dizionario('DATEALTERNATIVE').': '.$Arrivo_estesa.' / '.$Partenza_estesa.' ) ' .$DataArrivo_estesa.' / '.$DataPartenza_estesa;
        }

        $proposta_conteggio .= '<!--CONTEGGIO PROPOSTE-->
                                <div class="m m-x-12 conto" id="conto'.$n.'">
                                <div class="m m-x-12 titolo bcolor twhite">
                                    <div class="box4">
                                    <h3>'.($TipoRichiesta == 'Preventivo'?dizionario('PROPOSTA'):dizionario('SOLUZIONECONFERMATA')).' '.$n.' '.$etichetteDate.'</h3>
                                    <div class="m m-x-12 t14">'.dizionario('SCADENZA').' '.$DataScadenza_estesa.'</div>
                                    </div>
                                </div>
                                <div class="m m-x-12 riga t18 txs11 w700 tu">
                                    <div class="m m-x-3">
                                    <div class="box6">'.$eti8.'</div>
                                    </div>
                                    <div class="m m-x-3 m-x-tc">
                                    <div class="box6">
                                    '.($DataRichiestaCheck > config('global.settings.DATA_QUOTO_V2')?ucfirst(strtolower(dizionario('PERSONE'))):'').'</div>
                                    </div>
                                    <div class="m m-x-3 m-x-tc">
                                    <div class="box6">'.($DataRichiestaCheck > config('global.settings.DATA_QUOTO_V2')?dizionario('NOTTI'):'Nr. '.dizionario('CAMERA')).'</div>
                                    </div>
                                    <div class="m m-x-3 m-x-tr">
                                    <div class="box6">'.dizionario('PREZZO_CAMERA').'</div>
                                    </div>
                                    <div class="ca"></div>
                                </div>';

        $proposta_conteggio .= $camere;
        if($TipoRichiesta == 'Conferma'){
            // Query per servizi aggiuntivi
            $query  = "SELECT
                            hospitality_tipo_servizi.*,
                            hospitality_relazione_servizi_proposte.id_richiesta,
                            hospitality_relazione_servizi_proposte.num_persone,
                            hospitality_relazione_servizi_proposte.num_notti
                        FROM hospitality_relazione_servizi_proposte
                            INNER JOIN hospitality_tipo_servizi ON hospitality_relazione_servizi_proposte.servizio_id = hospitality_tipo_servizi.Id
                        WHERE hospitality_tipo_servizi.idsito = :idsito
                            AND hospitality_relazione_servizi_proposte.id_proposta = :IdProposta
                        ORDER BY hospitality_tipo_servizi.TipoServizio ASC";
                $risultato_query = DB::select($query,['idsito' => $idsito,'IdProposta' => $IdProposta]);
                $record          = sizeof($risultato_query);
            if(($record)>0){

                #### CONTROLLO I SERVIZI AGGIUNTIVI SCELTI DALL'UTENTE FINALE
                $q = "SELECT * FROM hospitality_relazione_servizi_proposte WHERE id_richiesta = :IdRichiesta AND id_proposta = :IdProposta";
                $r = DB::select($q,['IdRichiesta' => $id_richiesta,'IdProposta' => $IdProposta]);
                $IdServizio = array();
                foreach($r as $k => $v){
                    $IdServizio[$v->servizio_id]=1;
                }

                $quy   = "SELECT Id FROM hospitality_guest WHERE NumeroPrenotazione = :NumeroPrenotazione  AND TipoRichiesta = :TipoRichiesta AND idsito = :idsito";
                $ese   = DB::select($quy,['NumeroPrenotazione' => $Nprenotazione,'idsito' => $idsito,'TipoRichiesta' => 'Preventivo']);                
                $exist = sizeof($ese);

                if($exist>0){
                    $cc    = $ese[0];

                    $qry = "SELECT hospitality_relazione_servizi_proposte.servizio_id FROM hospitality_relazione_servizi_proposte WHERE id_richiesta = :id_richiesta AND idsito = :idsito";
                    $exe = DB::select($qry,['id_richiesta' => $cc->Id,'idsito' => $idsito]);
                    $relexist = sizeof($exe);
                    $IdServizioScelto = array();
                    if($relexist>0){
                        foreach($exe as $ky => $vl){
                            $IdServizioScelto[$vl->servizio_id]=1;
                        }
                    }
                }
                ### FINE CONTROLLO

                $SERVIZIAGGIUNTIVI .='<div class="ca10"></div><table class="table table-bordered no_border t16">
                                        <tr>
                                            <td class="no_border w300 tu" colspan="5"></td>
                                        </tr>
                                        <tr>
                                                <td class="m-x-3 no_border m-x-tl  t18 w300">'.dizionario('SERVIZIO').'</td>
                                                <td class="m-x-3 no_border m-x-tc  t18 w300 "></td>
                                                <td class="m-x-3 no_border m-x-tc  t18 w300">'.dizionario('CALCOLO').'</td>
                                                <td class="m-x-3 no_border m-x-tr  t18 w300">'.dizionario('PREZZO_SERVIZIO').'</td>

                                            </tr>';
                $n_notti = '';
                foreach($risultato_query as $ky => $campo){

                    $q   = "SELECT hospitality_tipo_servizi_lingua.* FROM hospitality_tipo_servizi_lingua  WHERE hospitality_tipo_servizi_lingua.servizio_id = :servizio_id AND hospitality_tipo_servizi_lingua.idsito = :idsito AND hospitality_tipo_servizi_lingua.lingue = :Lingua";
                    $r   = DB::select($q,['servizio_id' => $campo->Id,'idsito' => $idsito,'Lingua' => $Lingua]);
                    $rec = $r[0];

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
                        break;
                        case "en":
                            $A_PERCENTUALE = 'By percentage';
                        break;
                        case "fr":
                            $A_PERCENTUALE = 'Par pourcentage';
                        break;
                        case "de":
                            $A_PERCENTUALE = 'In Prozent';
                        break;
                    }
                    
                    switch($campo->CalcoloPrezzo){
                    case "Al giorno":
                        $calcoloprezzo = dizionario('AL_GIORNO');
                        $CalcoloPrezzoServizio = ($campo->PrezzoServizio!=0?'<small>('.number_format($campo->PrezzoServizio,2,',','.').' x '.($ANotti!=''?$ANotti:$Notti).')</small>':'');
                        $PrezzoServizio = ($campo->PrezzoServizio!=0?'<i class="fal fa-euro-sign"></i>&nbsp;&nbsp;'.number_format(($campo->PrezzoServizio*($ANotti!=''?$ANotti:$Notti)),2,',','.'):'<small class="text-green">Gratis</small>');
                    break;
                    case "A percentuale":
                        $calcoloprezzo = $A_PERCENTUALE;
                        $CalcoloPrezzoServizio = '';
                        $PrezzoServizio = ($campo->PercentualeServizio!=''?'<i class="fa fa-percent"></i>&nbsp;&nbsp;'.number_format(($campo->PercentualeServizio),2):'');
                    break;
                    case "Una tantum":
                        $calcoloprezzo = dizionario('UNA_TANTUM');
                        $CalcoloPrezzoServizio = '';
                        $PrezzoServizio = ($campo->PrezzoServizio!=0?'<i class="fal fa-euro-sign"></i>&nbsp;&nbsp;'.number_format($campo->PrezzoServizio,2,',','.'):'<small class="text-green">Gratis</small>');
                    break;
                    case "A persona":
                        $calcoloprezzo = dizionario('A_PERSONA');
                        $num_persone = $campo->num_persone;
                        $num_notti = $campo->num_notti;
                        $CalcoloPrezzoServizio = '<span style="font-size:80%">'.($campo->PrezzoServizio!=0?'<small>('.number_format($campo->PrezzoServizio,2,',','.').' x '.$num_notti.' <small>gg</small> x '.$num_persone.' <small>pax</small>)</small>':'('.$num_notti.' <small>gg</small> x '.$num_persone.' <small>pax</small>)').'</span>';
                        $PrezzoServizio = ($campo->PrezzoServizio!=0?'<i class="fal fa-euro-sign"></i>&nbsp;&nbsp;'.number_format(($campo->PrezzoServizio*$num_notti*$num_persone),2,',','.'):'<small class="text-green">Gratis</small>');
                    break;
                    }

                    $SERVIZIAGGIUNTIVI .='<tr class="riga">
                                            <td class="m-x-3 no_border m-x-tl"><img src="'.config('global.settings.BASE_URL_IMG').'uploads/'.$idsito.'/'.$campo->Icona.'" class="iconaDimension pad-left"> &nbsp;&nbsp;'.$rec->Servizio.'</td>
                                            <td class="m-x-3 no_border m-x-tc">'.($rec->Descrizione!=''?'<a href="javascript:;" data-toggle="tooltip" title="'.(strlen($rec->Descrizione)<=300?stripslashes(strip_tags($rec->Descrizione)):substr(stripslashes(strip_tags($rec->Descrizione)),0,300).'...').'"><i class="fa fa-info-circle text-green" aria-hidden="true"></i></a>':'').' </td>
                                            <td class="m-x-3 no_border m-x-tc">'.$calcoloprezzo.' '.$CalcoloPrezzoServizio.'</td>
                                            <td class="m-x-3 no_border m-x-tr">'.$PrezzoServizio.'</td>

                                        </tr>';



                }
                $SERVIZIAGGIUNTIVI .='</table>';
            } 
        }else{
        $ck_serv = $this->check_controllo_servizi($idsito);
        if($ck_serv == 1){
            $SERVIZIAGGIUNTIVI  = $this->get_modifica_servizi_aggiuntivi($n,$Id,$IdProposta,$Lingua);
        }else{

        // Query per servizi aggiuntivi
            $query  = "SELECT hospitality_tipo_servizi.*,hospitality_relazione_servizi_proposte.num_persone,hospitality_relazione_servizi_proposte.num_notti FROM hospitality_relazione_servizi_proposte
                        INNER JOIN hospitality_tipo_servizi ON hospitality_relazione_servizi_proposte.servizio_id = hospitality_tipo_servizi.Id
                        WHERE hospitality_tipo_servizi.idsito = :idsito
                        AND hospitality_relazione_servizi_proposte.id_proposta = :id_proposta
                        ORDER BY hospitality_tipo_ser ASC, hospitality_tipo_servizi.TipoServizio ASC";
            $risultato_query = DB::select($query,['idsito' => $idsito, 'id_proposta' => $IdProposta]);
            $record          = sizeof($risultato_query);
            if(($record)>0){
                $SERVIZIAGGIUNTIVI .='<div class="ca10"></div><table class="table table-bordered no_border t16">
                                        <tr>
                                            <td class="no_border w300 tu" colspan="4"></td>
                                        </tr>
                                        <tr>
                                                <td class="m-x-3 no_border m-x-tl  t18 w300">'.dizionario('SERVIZIO').'</td>
                                                <td class="m-x-3 no_border m-x-tc  t18 w300 "></td>
                                                <td class="m-x-3 no_border m-x-tc  t18 w300">'.dizionario('CALCOLO').'</td>
                                                <td class="m-x-3 no_border m-x-tr  t18 w300">'.dizionario('PREZZO_SERVIZIO').'</td>

                                            </tr>';
                $n_notti = '';
                foreach($risultato_query as $key => $campo){

                    $q   = "SELECT hospitality_tipo_servizi_lingua.* FROM hospitality_tipo_servizi_lingua  WHERE hospitality_tipo_servizi_lingua.servizio_id = :servizio_id AND hospitality_tipo_servizi_lingua.idsito = :idsito AND hospitality_tipo_servizi_lingua.lingue = :lingua";
                    $r   = DB::select($q,['servizio_id' => $campo->Id,'idsito' => $idsito,'lingua' => $Lingua ]);
                    $rec = $r[0];

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
                        break;
                        case "en":
                            $A_PERCENTUALE = 'By percentage';
                        break;
                        case "fr":
                            $A_PERCENTUALE = 'Par pourcentage';
                        break;
                        case "de":
                            $A_PERCENTUALE = 'In Prozent';
                        break;
                    }
                    
                    switch($campo->CalcoloPrezzo){
                        case "Al giorno":
                            $calcoloprezzo = dizionario('AL_GIORNO');
                            $CalcoloPrezzoServizio = ($campo->PrezzoServizio!=0?'<small>('.number_format($campo->PrezzoServizio,2,',','.').' x '.($ANotti!=''?$ANotti:$Notti).')</small>':'');
                            $PrezzoServizio = ($campo->PrezzoServizio!=0?'<i class="fal fa-euro-sign"></i>&nbsp;&nbsp;'.number_format(($campo->PrezzoServizio*($ANotti!=''?$ANotti:$Notti)),2,',','.'):'<small class="text-green">Gratis</small>');
                        break;
                        case "A percentuale":
                            $calcoloprezzo = $A_PERCENTUALE;
                            $CalcoloPrezzoServizio = '';
                            $PrezzoServizio = ($campo->PercentualeServizio!=''?'<i class="fa fa-percent"></i>&nbsp;&nbsp;'.number_format(($campo->PercentualeServizio),2):'');
                        break;
                        case "Una tantum":
                            $calcoloprezzo = dizionario('UNA_TANTUM');
                            $CalcoloPrezzoServizio = '';
                            $PrezzoServizio = ($campo->PrezzoServizio!=0?'<i class="fal fa-euro-sign"></i>&nbsp;&nbsp;'.number_format($campo->PrezzoServizio,2,',','.'):'<small class="text-green">Gratis</small>');
                        break;
                        case "A persona":
                            $calcoloprezzo = dizionario('A_PERSONA');
                            $num_persone = $campo->num_persone;
                            $num_notti = $campo->num_notti;
                            $CalcoloPrezzoServizio = '<span style="font-size:80%">'.($campo->PrezzoServizio!=0?'<small>('.number_format($campo->PrezzoServizio,2,',','.').' x '.$num_notti.' <small>gg</small> x '.$num_persone.' <small>pax</small>)</small>':'('.$num_notti.' <small>gg</small> x '.$num_persone.' <small>pax</small>)').'</span>';
                            $PrezzoServizio = ($campo->PrezzoServizio!=0?'<i class="fal fa-euro-sign"></i>&nbsp;&nbsp;'.number_format(($campo->PrezzoServizio*$num_notti*$num_persone),2,',','.'):'<small class="text-green">Gratis</small>');
                        break;
                    }

                    $SERVIZIAGGIUNTIVI .='<tr class="riga">
                                            <td class="m-x-3 no_border m-x-tl"><img src="'.config('global.settings.BASE_URL_IMG').'uploads/'.$idsito.'/'.$campo->Icona.'" class="iconaDimension pad-left"> &nbsp;&nbsp;'.$rec->Servizio.'</td>
                                            <td class="m-x-3 no_border m-x-tc">'.($rec->Descrizione!=''?'<a href="javascript:;" data-toggle="tooltip" title="'.(strlen($rec->Descrizione)<=300?stripslashes(strip_tags($rec->Descrizione)):substr(stripslashes(strip_tags($rec->Descrizione)),0,300).'...').'"><i class="fa fa-info-circle text-green" aria-hidden="true"></i></a>':'').' </td>
                                            <td class="m-x-3 no_border m-x-tc">'.$calcoloprezzo.' '.$CalcoloPrezzoServizio.'</td>
                                            <td class="m-x-3 no_border m-x-tr">'.$PrezzoServizio.'</td>

                                        </tr>';



                }
                $SERVIZIAGGIUNTIVI .='</table>';
            } 


        }

        }


        $proposta_conteggio .= $SERVIZIAGGIUNTIVI;

        switch($Lingua){
            case"it":
                $etichetta_sconto = '<small class="t12 nowrap">Sconto escluso dal calcolo e scelta dei servizi aggiuntivi</small>';
            break;
            case"en":
                $etichetta_sconto = '<small>The discount is excluded on the calculation and choice of additional services</small>';
            break;
            case"fr":
                $etichetta_sconto = '<small>La remise est exclue sur le calcul et le choix des services supplémentaires</small>';
            break;
            case"de":
                $etichetta_sconto = '<small>Der Rabatt ist bei der Berechnung und Auswahl der Zusatzleistungen ausgeschlossen</small>';
            break;
        }

        if(($PrezzoL!='0,00')){
            $proposta_conteggio .='<div class="m m-x-12 riga t16">
                                        <div class="m m-x-3">
                                            <div class="box6">'.$sconto.' '.($TipoRichiesta=='Preventivo'?'<br>'.$etichetta_sconto:'').'</div>
                                        </div>
                                        <div class="m m-x-3 m-x-tc">
                                            <div class="box6">'.$percentuale_sconto.' %</div>
                                        </div>
                                        <div class="m m-x-3 m-x-tc">
                                            <div class="box6">&nbsp;</div>
                                        </div>
                                        <div class="m m-x-3 m-x-tr">
                                            <div class="box6">- <i class="fal fa-euro-sign"></i> '.$ImportoSconto .'</div>
                                        </div>
                                        </div>';
        }

        $proposta_conteggio .='  <div class="ca1 bcolor"></div>
                                <div class="m m-x-12 totale">
                                    <div class="m m-x-6 m-xs-12 m-xs-tr">
                                    <div class="box6">'.$eti6.'</div>
                                    </div>
                                    <div class="m m-x-6 m-x-tr m-xs-12">
                                    <div class="box6">
                                        <div class="tcolor t30 w700"><i class="fal fa-euro-sign"></i> <span id="PrezzoPC'.$n.'_'.$IdProposta.'">'.number_format($PrezzoPC,2,',','.').'</span><input type="hidden" id="ReCalPrezzo'.$n.'_'.$IdProposta.'" name="ReCalPrezzo" value="'.$PrezzoPC.'" /></div>
                                        <!--<div class="tcolor t14 w700">'.$DataScadenza_estesa.'</div>-->
                                        <div class="ca10"></div>';
        if($TipoRichiesta == 'Preventivo'){
            $proposta_conteggio .='    <div class="pulsante pulsante2" section="preno" propostaid="'.$n.'">'.$eti12.'</div>';
        }
            $proposta_conteggio .=' <div class="ca10"></div>';
        if($AccontoPercentuale != 0 && $AccontoImporto == 0) {
            $proposta_conteggio .='<div class="t20">'.dizionario('CAPARRA').' <span class="tcolor w700">'.$AccontoPercentuale.' %  &nbsp;&nbsp; <i class="fal fa-euro-sign"></i> <span id="ReCalCaparra'.$n.'_'.$IdProposta.'">'.number_format(($PrezzoPC*$AccontoPercentuale/100),2,',','.').'</span></span><input type="hidden" id="PercentualeCaparra'.$n.'_'.$IdProposta.'" name="PercentualeCaparra'.$n.'_'.$IdProposta.'" value="'.$AccontoPercentuale.'" /></div>';
        }
        if($AccontoPercentuale == 0 && $AccontoImporto != 0) {
            if($AccontoImporto >= 1){
                $proposta_conteggio .='<div class="t20">'.dizionario('CAPARRA').' <span class="tcolor w700"><i class="fal fa-euro-sign"></i> '.number_format($AccontoImporto,2,',','.').'</span></div>';
            }else{
                $proposta_conteggio .= '<div class="t20">'.dizionario('CARTACREDITOGARANZIA').'</div>';
            }

        }
            $proposta_conteggio .=' </div>
                                    </div>
                                </div>';
                if($AccontoTariffa!='' || $AccontoTesto!=''){

                    $proposta_conteggio .= '<div class="m m-x-12 bcolor3 t15">
                                            <div class="box5">
                                            <p><span id="tarif'.$n.'" style="cursor:pointer"><i class="fa fa-question-circle" aria-hidden="true"></i> '.($AccontoTariffa!=''?$AccontoTariffa:$condizioni_tariffa).'</span>
                                            <br />
                                                <div id="cond_tarif'.$n.'" style="display:none">
                                                    '.nl2br($AccontoTesto).'
                                                </div>
                                            </p>
                                            </div>
                                        </div>
                                        <script>
                                            $( "#tarif'.$n.'" ).click(function() {
                                                $( "#cond_tarif'.$n.'" ).toggle( "slow", function() {
                                                    // Animation complete.
                                                });
                                            });
                                        </script>';

            }

            $proposta_conteggio .='</div>';


        $n++;


        }
        if($abilita_mappa == 1){
            if($LatCliente !='' && $LonCliente != ''){
              $Mappa ='<div class="GM2">
                            <div id="map-container" class="google"></div>
                        </div>';
            }
          }
        $array_infoH = $this->info_hotel($idsito,$Lingua,$TipoRichiesta);
        $tot_info = $array_infoH['tot_info'];
        $infohotel = $array_infoH['infohotel'];
        $infohotelTesto = $array_infoH['infohotelTesto'];

        #TESTO DEL MESSAGGIO
        $testo_messaggio = dizionario('ALLA_CO').' '.$NomeCliente.','."\r\n";
        $testo_messaggio .= dizionario('CONTENUTO_MSG')."\r\n";
        $testo_saluti = dizionario('CORDIALMENTE')."\r\n";
        $testo_saluti .= $Cliente;
        $testo_riferimento = 'Rif. nr. <b>'.$NumeroPrenotazione.'</b> - Fonte di Provenienza: <b>'.$FontePrenotazione.'</b> - Preventivo Intestato a <b>'.$Cliente.'</b> del <b>'.$DataRichiesta.'</b> inviato il <b>'.$DataInvio.'</b>'."\r\n";



        return view('custom1_template/index',
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
                'result'                => ($request->result != '' ? $request->result: ''),
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
                'disable'               => $disable,
                'Operatore'             => $Operatore,
                'ImgOp'                 => $ImgOp,
                'Testo'                 => $testoLanding,
                'DataArrivo'            => $DataArrivo,
                'DataPartenza'          => $DataPartenza,
                'infohotel'             => $infohotel,
                'infohotelTesto'        => $infohotelTesto,
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
                'colore1'               => $colore1,
                'colore2'               => $colore2,
                'font'                  => $font,
                'font_libreria'         => $font_libreria,
                'immagine1'             => $immagine1,
                'immagine2'             => $immagine2,
                'logotitle'             => $logotitle,
                'intestazionemobile1'   => $intestazionemobile1,
                'intestazionemobile2'   => $intestazionemobile2,
                'coloresfondo'          => $coloresfondo,
                'ownerimg'              => $ownerimg,
                'telefono'              => $telefono,
                'proposta_form'         => $proposta_form,
                'proposta_titolo'       => $proposta_titolo,
                'proposta_specchietto'  => $proposta_specchietto,
                'proposta_camera'       => $proposta_camera,
                'proposta_conteggio'    => $proposta_conteggio,
                'DataScadenza_estesa'   =>  $DataScadenza_estesa,
                'tot_info'              => $tot_info,
            ]
        );

    }


    public function questionario($directory, $params, Request $request)
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
            $rec     = $sel[0];
            $Lingua  = $rec->Lingua;
            $Nome    = $rec->Nome;
            $Cognome = $rec->Cognome;
            $Email   = $rec->Email;
            $Cliente = $Nome.' '.$Cognome;
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
                                        <table style="float:right;">
                                            <tr>
                                            <td style="padding: 0 5px 0 5px !important;text-align: center !important;"><input type="radio" name="recensione_'.$record->domanda_id.'" value="1" required></td>
                                            <td style="padding: 0 5px 0 5px !important;text-align: center !important;"><input type="radio" name="recensione_'.$record->domanda_id.'" value="2" required></td>
                                            <td style="padding: 0 5px 0 5px !important;text-align: center !important;"><input type="radio" name="recensione_'.$record->domanda_id.'" value="3" required></td>
                                            <td style="padding: 0 5px 0 5px !important;text-align: center !important;"><input type="radio" name="recensione_'.$record->domanda_id.'" value="4" required></td>
                                            <td style="padding: 0 5px 0 5px !important;text-align: center !important;"><input type="radio" name="recensione_'.$record->domanda_id.'" value="5" required></td>
                                            </tr>
                                            <tr>
                                            <td style="padding: 0 5px 0 5px !important;text-align: center !important;"><img src="'.config('global.settings.BASE_URL_IMG').'img/emoji/bad.png"  data-toogle="tooltip" title="Bad [valore = 1]"></td>
                                            <td style="padding: 0 5px 0 5px !important;text-align: center !important;"><img src="'.config('global.settings.BASE_URL_IMG').'img/emoji/semi_bad.png" data-toogle="tooltip" title="Semi Bad [valore = 2]"></td>
                                            <td style="padding: 0 5px 0 5px !important;text-align: center !important;"><img src="'.config('global.settings.BASE_URL_IMG').'img/emoji/medium.png"  data-toogle="tooltip" title="Medium [valore = 3]"></td>
                                            <td style="padding: 0 5px 0 5px !important;text-align: center !important;"><img src="'.config('global.settings.BASE_URL_IMG').'img/emoji/semi_good.png"  data-toogle="tooltip" title="Semi Good [valore = 4]"></td>
                                            <td style="padding: 0 5px 0 5px !important;text-align: center !important;"><img src="'.config('global.settings.BASE_URL_IMG').'img/emoji/good.png"  data-toogle="tooltip" title="Good [valore = 5]"></td>                                   
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


            $sel_color = "SELECT * FROM hospitality_template_background WHERE idsito = :idsito AND TemplateType = :TemplateType LIMIT 1";
            $res_color = DB::select($sel_color,['idsito' => $idsito, 'TemplateType' => 'custom1']);
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

            $logo                =($Logo ==''?'<i class="fas fa-bed fa-5x fa-fw"></i>':'<img src="'.config('global.settings.BASE_URL_IMG').'uploads/loghi/'.$Logo.'" class="logo">');
            $logotitle           = $NomeCliente;
            $intestazionemobile1 = $NomeCliente;
            $intestazionemobile2 = $Localita;
            $coloresfondo        ="#A4A4A4";//colore di sfondo della pagina






            return view('custom1_template/questionario',
                                                            [
                                                                'valori_ctrl_script'  => $valori_ctrl_script,
                                                                'question'            => $question,
                                                                'Indirizzo'           => $Indirizzo,
                                                                'Localita'            => $Localita,
                                                                'Provincia'           => $Provincia,
                                                                'Cap'                 => $Cap,
                                                                'CIR'                 => $CIR,
                                                                'CIN'                 => $CIN,
                                                                'SitoWeb'             => $SitoWeb,
                                                                'Logo'                => $Logo,
                                                                'EmailCliente'        => $EmailCliente,
                                                                'NomeCliente'         => $NomeCliente,
                                                                'Cliente'             => $Cliente,
                                                                'Email'               => $Email,
                                                                'Nome'                => $Nome,
                                                                'Cognome'             => $Cognome,
                                                                'Facebook'            => $Facebook,
                                                                'Twitter'             => $Twitter,
                                                                'GooglePlus'          => $GooglePlus,
                                                                'Instagram'           => $Instagram,
                                                                'Linkedin'            => $Linkedin,
                                                                'Pinterest'           => $Pinterest,
                                                                'tot_cs'              => $tot_cs,
                                                                'Lingua'              => $Lingua,
                                                                'head_tagmanager'     => $head_tagmanager,
                                                                'body_tagmanager'     => $body_tagmanager,
                                                                'directory'           => $directory,
                                                                'params'              => $params,
                                                                'idsito'              => $idsito,
                                                                'id_richiesta'        => $id_richiesta,
                                                                'colore1'             => $colore1,
                                                                'colore2'             => $colore2,
                                                                'font'                => $font,
                                                                'font_libreria'       => $font_libreria,               
                                                                'coloresfondo'        => $coloresfondo,
                                                                'logo'                => $logo,
                                                                'logotitle'           => $logotitle,
                                                                'intestazionemobile1' => $intestazionemobile1,
                                                                'intestazionemobile2' => $intestazionemobile2,
                                                                'telefono'            => $tel,
                                                            ]
                                                        );
    }









}
