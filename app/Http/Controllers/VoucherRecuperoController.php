<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Datetime;

class VoucherRecuperoController extends Controller
{
    public function voucher_rec($directory, $params, Request $request)
    {

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

        $select = " SELECT
                            hospitality_carte_credito.*
                        FROM
                            hospitality_carte_credito
                        WHERE
                            hospitality_carte_credito.id_richiesta = :id_richiesta
                        AND
                            hospitality_carte_credito.idsito = :idsito";

        $result       = DB::select($select,['id_richiesta' => $id_richiesta, 'idsito' => $idsito]);
        $check_cc     = sizeof($result);

        if($check_cc > 0){

            $row = $result[0];

            $TipologiaPagamento = $row->carta;

            $GiaPagatoCC = true;
        }else{
            $GiaPagatoCC = false;

            $TipologiaPagamento = '';
        }

        $select2 = "SELECT
                        hospitality_altri_pagamenti.*
                    FROM
                        hospitality_altri_pagamenti
                    WHERE
                        hospitality_altri_pagamenti.id_richiesta = :id_richiesta
                    AND
                        hospitality_altri_pagamenti.idsito = :idsito";

        $result2       = DB::select($select2,['id_richiesta' => $id_richiesta, 'idsito' => $idsito]);
        $check_pay     = sizeof($result2);

        if($check_pay > 0){

            $row2 = $result2[0];

            $TipologiaPagamento = $row2->TipoPagamento;

            $GiaPagatoPAY = true;
        }else{
            $GiaPagatoPAY = false;

            $TipologiaPagamento = '';
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

        $codeTagMan = $this->tagManager($idsito,$TagManager);
        $head_tagmanager = $codeTagMan[0];
        $body_tagmanager = $codeTagMan[1];

        $select = "SELECT hospitality_guest.*
                FROM hospitality_guest
                WHERE hospitality_guest.idsito = :idsito
                AND hospitality_guest.Id = :id_richiesta";
        $sel  = DB::select($select,['idsito' => $idsito, 'id_richiesta' => $id_richiesta]);
        $res = sizeof($sel);
        if(($res)>0){
            $value                    = $sel[0];
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
            $DataS_tmp                = explode("-",$value->DataScadenza);
            $DataScadenza             = $DataS_tmp[2].'/'.$DataS_tmp[1].'/'.$DataS_tmp[0];
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
        }

        $testo_condizioni_generali = $this->condizioni_generali($idsito,$Lingua);

        switch($Lingua){
            case "it":
                $FRASE_CAPARRA = 'L\'importo richiesto è stato pagato tramite [tipopagamento]';
                $NESSUN_PAGAMENTO = 'Nessun pagamento è stato ancora effettuato!';
                $saldo_text = 'Cifra a saldo';
                $TOTALE_PREZZO_CAMERE =  'sul totale prezzo camere';
                $CARTA_A_GARANZIA = 'L\'importo richiesto è stato pagato tramite [tipopagamento]';
            break;
            case "en":
                $FRASE_CAPARRA = 'The requested amount has been paid via [tipopagamento]';
                $NESSUN_PAGAMENTO = 'No payment has been made yet! ';
                $saldo_text = 'Amount still to be paid';
                $TOTALE_PREZZO_CAMERE =  'on the total room price';
                $CARTA_A_GARANZIA = 'The requested amount has been paid via [tipopagamento]';
            break;
            case "fr":
                $FRASE_CAPARRA = 'Le montant demandé a été payé via [tipopagamento]';
                $NESSUN_PAGAMENTO = 'Aucun paiement n\'a encore été effectué! ';
                $saldo_text = 'Montant restant à payer';
                $TOTALE_PREZZO_CAMERE =  'sur le prix total de la chambre';
                $CARTA_A_GARANZIA = 'Le montant demandé a été payé via [tipopagamento]';
            break;
            case "de":
                $FRASE_CAPARRA = 'Der angeforderte Betrag wurde über [tipopagamento] bezahlt';
                $NESSUN_PAGAMENTO = 'Es wurde noch keine Zahlung geleistet!' ;
                $saldo_text = 'Noch zu zahlender Betrag';
                $TOTALE_PREZZO_CAMERE =  'auf den gesamten Zimmerpreis';
                $CARTA_A_GARANZIA = 'Der angeforderte Betrag wurde über [tipopagamento] bezahlt';
            break;
    
        }

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
                FROM 
                    hospitality_proposte
                WHERE 
                    hospitality_proposte.id_richiesta = :id_richiesta";

        $hr  = DB::select($selP,['id_richiesta' => $Id]);
        $r_n = sizeof($hr);


        $NomeProposta       = '';
        $TestoProposta      = '';
        $CheckProposta      = '';
        $TipoCamere         = '';
        $PrezzoL            = '';
        $PrezzoP            = '';
        $PrezzoPC           = '';
        $percentuale_sconto = '';
        $AccontoPercentuale = '';
        $AccontoImporto     = '';
        $AccontoTariffa     = '';
        $AccontoTesto       = '';
        $Arrivo             = '';
        $Partenza           = '';
        $A                  = '';
        $P                  = '';
        $valore             = '';
        $Servizi            = '';
        $services           = '';
        $id_servizio        = '';
        $SERVIZIAGGIUNTIVI  = '';
        $datealternative    = '';
        $VAUCHERCamere      = '';
        $ImpSconto          = '';
        $imp_sconto = '';

        foreach($hr as $ky => $value){


                $PrezzoL            = number_format($value->PrezzoL,2,',','.');
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
                if($value->Arrivo){
                    $A_tmp          = explode("-",$value->Arrivo);
                    $A              = $value->Arrivo;
                    $Arrivo         = $A_tmp[2].'/'.$A_tmp[1].'/'.$A_tmp[0];
                }else{
                    $A              = '';
                    $Arrivo         = '';
                }
                if($value->Partenza){
                    $P_tmp          = explode("-",$value->Partenza);
                    $P              = $value->Partenza;
                    $Partenza       = $P_tmp[2].'/'.$P_tmp[1].'/'.$P_tmp[0];
                }else{
                    $P              = '';
                    $Partenza       = '';
                }
                if($A!='') {
                    $Astart         = mktime(24,0,0,$A_tmp[1],$A_tmp[2],$A_tmp[0]);
                }else{
                    $Astart         = '';
                }
                if($P!=''){
                    $Aend           = mktime(01,0,0,$P_tmp[1],$P_tmp[2],$P_tmp[0]);
                }else{
                    $Aend           = '';
                }
                if($Astart != '' && $Aend != ''){
                    $formato            = "%a";
                    $ANotti             = $this->dateDiff($value->Arrivo,$value->Partenza,$formato);
                }else{
                    $ANotti             = '';
                }

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
                $result_sconti = DB::select($select_sconti,['idsito' => $idsito,'id_richiesta' => $Id,'id_proposta' => $IdProposta]);
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
                        $resSconto     = DB::select($selSconto,['id_richiesta' => $Id,'id_proposta' => $IdProposta]);
                        $recSconto     = $resSconto[0];
                        $ImpSconto    = (($recSconto->prezzo_camere*$percentuale_sconto)/100);
                        $ImportoSconto = number_format($ImpSconto,2,',','.');
                    } 
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
                    $x = 1;
                    $Servizi = '';
                    $serv    = '';
                    $servizi = '';
                    $services = '';
                    $image_room = '';
                    $Prezzo = '';
                    foreach($result2 as $key => $val){

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
                        $Prezzo          = number_format($val->Prezzo,2,',','.');
                        $Prezzo          = floatval($Prezzo);

             
                        $VAUCHERCamere .= '<p>'.$val->TitoloSoggiorno.' <i class=\'fa fa-angle-right\'></i> Nr. '.$val->NumeroCamere.' '.$val->TipoCamere.' '.($DataRichiestaCheck >  config('global.settings.DATA_QUOTO_V2') ?($NumAdulti!=0?$ico_adulti:'').' '.($NumBambini!=0?$ico_bimbi:'').' '.($EtaB!=0?''.dizionario('ETA').' '.$EtaB.' ':''):'').' - €. '.number_format($val->Prezzo,2,',','.').'</p>';




                    if($A != '' && $P != ''){
                        if($DataArrivo != $Arrivo || $DataPartenza != $Partenza){

                                if($TipoRichiesta=='Preventivo'){

                                    $datealternative .='<div class="row">
                                                        <div class="col-md-12">
                                                        <b>'.dizionario('DATEALTERNATIVE').':</b><br><i class="fa fa-calendar"></i> '.dizionario('DATA_ARRIVO').' '.$Arrivo.' <i class="fa fa-calendar"></i> '.dizionario('DATA_PARTENZA').' '.$Partenza.' <i class="fa fa-long-arrow-right"></i> '.dizionario('NOTTI').' '.$ANotti.'
                                                        </div>
                                                    </div>
                                                    <hr class="line_white">';

                                }elseif($TipoRichiesta=='Conferma'){
                                    if($DataArrivo != $Arrivo ){
                                        $DataArrivo   = $Arrivo;
                                        $Notti = $ANotti;
                                    }
                                    if($DataPartenza != $Partenza){
                                        $DataPartenza   = $Partenza;
                                        $Notti = $ANotti;
                                    }
                                }

                        }
                    }



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
                            $SERVIZIAGGIUNTIVI .='<style>
                                                    .iconaDimension {
                                                        width:auto !important;
                                                        height:32px !important;
                                                    }
                                                    .bg-transparent{
                                                        background:transparent !important;
                                                        background-color:transparent !important;
                                                    }
                                                    .small-padding{
                                                        padding:2px !important;
                                                    }
                                                </style>
                                                <table class="table table-responsive bg-transparent">
                                                    <tr>
                                                        <td class="no_border_td" colspan="5" style="width:100%" >'.dizionario('SERVIZI_AGGIUNTIVI').'</td>
                                                    </tr>';
                            
                            foreach($risultato_query as $key => $campo){


                                $q   = "SELECT hospitality_tipo_servizi_lingua.* FROM hospitality_tipo_servizi_lingua  WHERE hospitality_tipo_servizi_lingua.servizio_id = :servizio_id AND hospitality_tipo_servizi_lingua.idsito = :idsito AND hospitality_tipo_servizi_lingua.lingue = :Lingua";
                                $r   = DB::select($q,['servizio_id' => $campo->Id,'idsito' => $idsito,'Lingua' => $Lingua]);
                                $rec = $r[0];


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
                                    $num_persone = '';
                                    $CalcoloPrezzoServizio = ($campo->PrezzoServizio!=0?'<small>('.number_format($campo->PrezzoServizio,2,',','.').' x '.($ANotti!=''?$ANotti:$Notti).')</small>':'');
                                    $PrezzoServizio = ($campo->PrezzoServizio!=0?'<i class="fa fa-euro"></i>&nbsp;&nbsp;'.number_format(($campo->PrezzoServizio*($ANotti!=''?$ANotti:$Notti)),2,',','.'):'<small class="text-green">Gratis</small>');
                                break;
                                case "A percentuale":
                                    $calcoloprezzo = $A_PERCENTUALE;
                                    $num_persone = '';
                                    $CalcoloPrezzoServizio = '';
                                    $PrezzoServizio = ($campo->PercentualeServizio!=''?'<i class="fa fa-percent"></i>&nbsp;&nbsp;'.number_format(($campo->PercentualeServizio),2):'');
                                break;
                                case "Una tantum":
                                    $calcoloprezzo = dizionario('UNA_TANTUM');
                                    $num_persone = '';
                                    $CalcoloPrezzoServizio = '';
                                    $PrezzoServizio = ($campo->PrezzoServizio!=0?'<i class="fa fa-euro"></i>&nbsp;&nbsp;'.number_format($campo->PrezzoServizio,2,',','.'):'<small class="text-green">Gratis</small>');
                                break;
                                case "A persona":
                                    $calcoloprezzo = dizionario('A_PERSONA');
                                    $num_persone = ($campo->num_persone=='' ? $NumeroAdulti : $campo->num_persone);
                                    $num_notti = $campo->num_notti;
                                    $CalcoloPrezzoServizio = '<span style="font-size:80%">'.($campo->PrezzoServizio!=0?'<small>('.number_format($campo->PrezzoServizio,2,',','.').' x '.$num_notti.' <small>gg</small> x '.$num_persone.' <small>pax</small>)</small>':'('.$num_notti.' <small>gg</small> x '.$num_persone.' <small>pax</small>)').'</span>';
                                    $PrezzoServizio = ($campo->PrezzoServizio!=0?'<i class="fa fa-euro"></i>&nbsp;&nbsp;'.number_format(($campo->PrezzoServizio*$num_notti*$num_persone),2,',','.'):'<small class="text-green">Gratis</small>');
                                break;
                                }

                                    $SERVIZIAGGIUNTIVI .='<tr>
                                                            <td class="no_border_td text-center small-padding"> '.((!$IdServizioScelto[$campo->Id] && $IdServizio[$campo->Id]==1)? '<small><i class="fa fa-user"></i></small>':'').'</td>
                                                            <td class="no_border_td text-center small-padding"><img src="'.config('global.settings.BASE_URL_IMG').'uploads/'.$idsito.'/'.$campo->Icona.'" class="iconaDimension"></td>
                                                            <td class="no_border_td text-left small-padding">'.$rec->Servizio.'</td>
                                                            <td class="no_border_td text-left small-padding">'.$calcoloprezzo.' '.$CalcoloPrezzoServizio.'</td>
                                                            <td class="no_border_td text-left small-padding">'.$PrezzoServizio.'</td>

                                                        </tr>';
                                


                            }
                            $SERVIZIAGGIUNTIVI .='</table>';
                        }
                    }else{

                            // Query per servizi aggiuntivi
                            $query  = "SELECT hospitality_tipo_servizi.*,hospitality_relazione_servizi_proposte.num_persone,hospitality_relazione_servizi_proposte.num_notti 
                                        FROM hospitality_relazione_servizi_proposte
                                        INNER JOIN hospitality_tipo_servizi ON hospitality_relazione_servizi_proposte.servizio_id = hospitality_tipo_servizi.Id
                                        WHERE hospitality_tipo_servizi.idsito = :idsito
                                        AND hospitality_relazione_servizi_proposte.id_proposta = :id_proposta
                                        ORDER BY hospitality_tipo_ser ASC, hospitality_tipo_servizi.TipoServizio ASC";
                            $risultato_query = DB::select($query,['idsito' => $idsito, 'id_proposta' => $IdProposta]);
                            $record          = sizeof($risultato_query);
                            if(($record)>0){
                                $SERVIZIAGGIUNTIVI .='<style>
                                                        .iconaDimension {
                                                            width:auto !important;
                                                            height:32px !important;
                                                        }
                                                        .bg-transparent{
                                                            background:transparent !important;
                                                            background-color:transparent !important;
                                                        }
                                                        .small-padding{
                                                            padding:2px !important;
                                                        }
                                                    </style>
                                                    <table class="table table-responsive bg-transparent">
                                                        <tr>
                                                            <td class="no_border_td" colspan="4" style="width:100%" > '.dizionario('SERVIZI_AGGIUNTIVI').'</td>
                                                        </tr>';
                              
                                foreach($risultato_query as $key => $campo){

                                    $q   = "SELECT hospitality_tipo_servizi_lingua.* FROM hospitality_tipo_servizi_lingua  WHERE hospitality_tipo_servizi_lingua.servizio_id = :servizio_id AND hospitality_tipo_servizi_lingua.idsito = :idsito AND hospitality_tipo_servizi_lingua.lingue = :lingua";
                                    $r   = DB::select($q,['servizio_id' => $campo->Id,'idsito' => $idsito,'lingua' => $Lingua ]);
                                    $rec = $r[0];


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
                                        $num_persone = '';
                                        $CalcoloPrezzoServizio = ($campo->PrezzoServizio!=0?'<small>('.number_format($campo->PrezzoServizio,2,',','.').' x '.($ANotti!=''?$ANotti:$Notti).')</small>':'');
                                        $PrezzoServizio = ($campo->PrezzoServizio!=0?'<i class="fa fa-euro"></i>&nbsp;&nbsp;'.number_format(($campo->PrezzoServizio*($ANotti!=''?$ANotti:$Notti)),2,',','.'):'<small class="text-green">Gratis</small>');
                                    break;
                                    case "A percentuale":
                                        $calcoloprezzo = $A_PERCENTUALE;
                                        $num_persone = '';
                                        $CalcoloPrezzoServizio = '';
                                        $PrezzoServizio = ($campo->PercentualeServizio!=''?'<i class="fa fa-percent"></i>&nbsp;&nbsp;'.number_format(($campo->PercentualeServizio),2):'');
                                    break;
                                    case "Una tantum":
                                        $calcoloprezzo = dizionario('UNA_TANTUM');
                                        $num_persone = '';
                                        $CalcoloPrezzoServizio = '';
                                        $PrezzoServizio = ($campo->PrezzoServizio!=0?'<i class="fa fa-euro"></i>&nbsp;&nbsp;'.number_format($campo->PrezzoServizio,2,',','.'):'<small class="text-green">Gratis</small>');
                                    break;
                                    case "A persona":
                                        $calcoloprezzo = dizionario('A_PERSONA');
                                        $num_persone = ($campo->num_persone=='' ? $NumeroAdulti : $campo->num_persone);
                                        $num_notti = $campo->num_notti;
                                        $CalcoloPrezzoServizio = '<span style="font-size:80%">'.($campo->PrezzoServizio!=0?'<small>('.number_format($campo->PrezzoServizio,2,',','.').' x '.$num_notti.' <small>gg</small> x '.$num_persone.' <small>pax</small>)</small>':'('.$num_notti.' <small>gg</small> x '.$num_persone.' <small>pax</small>)').'</span>';
                                        $PrezzoServizio = ($campo->PrezzoServizio!=0?'<i class="fa fa-euro"></i>&nbsp;&nbsp;'.number_format(($campo->PrezzoServizio*$num_notti*$num_persone),2,',','.'):'<small class="text-green">Gratis</small>');
                                    break;
                                    }
                                   
                                        $SERVIZIAGGIUNTIVI .='<tr>
                                                                <td style="width:10%" class="panel-body-warning border_td_white text-center"><img src="'.config('global.settings.BASE_URL_IMG').'uploads/'.$IdSito.'/'.$campo->Icona.'" class="iconaDimension"></td>
                                                                <td style="width:40%"  class="panel-body-warning border_td_white"><p>
                                                                '.($rec->Descrizione!=''?'<a href="javascript:;" data-toggle="tooltip" title="'.(strlen($rec->Descrizione)<=300?stripslashes(strip_tags($rec->Descrizione)):substr(stripslashes(strip_tags($rec->Descrizione)),0,300).'...').'"><i class="fa fa-info-circle text-green" aria-hidden="true"></i></a>':'').' '.$rec->Servizio.'</p></td>
                                                                <td style="width:25%"  class="panel-body-warning border_td_white text-center"><p>'.$calcoloprezzo.' '.$CalcoloPrezzoServizio.'</p></td>
                                                                <td style="width:25%"  class="panel-body-warning border_td_white text-center"><p>'.$PrezzoServizio.'</p></td>

                                                            </tr>';
                                   


                                }
                                $SERVIZIAGGIUNTIVI .='</table>';
                            }

                        

                    }



                }

        }


        return view('/voucher_rec',
                                                    [
                                                        'TipologiaPagamento'        => $TipologiaPagamento,
                                                        'GiaPagatoCC'               => $GiaPagatoCC,   
                                                        'GiaPagatoPAY'              => $GiaPagatoPAY,
                                                        'FRASE_CAPARRA'             => $FRASE_CAPARRA,
                                                        'NESSUN_PAGAMENTO'          => $NESSUN_PAGAMENTO,
                                                        'saldo_text'                => $saldo_text,
                                                        'TOTALE_PREZZO_CAMERE'      => $TOTALE_PREZZO_CAMERE,
                                                        'CARTA_A_GARANZIA'          => $CARTA_A_GARANZIA,
                                                        'testo_condizioni_generali' => $testo_condizioni_generali,
                                                        'Indirizzo'                 => $Indirizzo,
                                                        'Localita'                  => $Localita,
                                                        'Provincia'                 => $Provincia,
                                                        'Cap'                       => $Cap,
                                                        'CIR'                       => $CIR,
                                                        'CIN'                       => $CIN,
                                                        'SitoWeb'                   => $SitoWeb,
                                                        'tel'                       => $tel,
                                                        'Logo'                      => $Logo,
                                                        'EmailCliente'              => $EmailCliente,
                                                        'NomeCliente'               => $NomeCliente,
                                                        'Cliente'                   => $Cliente,
                                                        'Email'                     => $Email,
                                                        'Nome'                      => $Nome,
                                                        'Cognome'                   => $Cognome,
                                                        'Lingua'                    => $Lingua,
                                                        'DataArrivo'                => $DataArrivo,
                                                        'DataPartenza'              => $DataPartenza,
                                                        'NumeroPrenotazione'        => $NumeroPrenotazione,
                                                        'DataRichiesta'             => $DataRichiesta,
                                                        'head_tagmanager'           => $head_tagmanager,
                                                        'body_tagmanager'           => $body_tagmanager,
                                                        'directory'                 => $directory,
                                                        'params'                    => $params,
                                                        'idsito'                    => $idsito,
                                                        'id_richiesta'              => $id_richiesta,
                                                        'SERVIZIAGGIUNTIVI'         => $SERVIZIAGGIUNTIVI,
                                                        'NomeProposta'              => $NomeProposta,
                                                        'TestoProposta'             => $TestoProposta,
                                                        'NumeroAdulti'              => $NumeroAdulti,
                                                        'NumeroBambini'             => $NumeroBambini,
                                                        'EtaBambini1'               => $EtaBambini1,
                                                        'EtaBambini2'               => $EtaBambini2,
                                                        'EtaBambini3'               => $EtaBambini3,
                                                        'EtaBambini4'               => $EtaBambini4,
                                                        'EtaBambini5'               => $EtaBambini5,
                                                        'EtaBambini6'               => $EtaBambini6,
                                                        'Notti'                     => $Notti,
                                                        'datealternative'           => $datealternative,
                                                        'VAUCHERCamere'             => $VAUCHERCamere,
                                                        'imp_sconto'                => $imp_sconto,
                                                        'percentuale_sconto'        => $percentuale_sconto,
                                                        'Prezzo'                    => $Prezzo,
                                                        'ImpSconto'                 => $ImpSconto,
                                                        'AccontoRichiesta'          => $AccontoRichiesta,
                                                        'AccontoLibero'             => $AccontoLibero,
                                                        'AccontoPercentuale'        => $AccontoPercentuale,
                                                        'AccontoImporto'            => $AccontoImporto,
                                                        'PrezzoL'                   => $PrezzoL,
                                                        'PrezzoP'                   => $PrezzoP,
                                                        'PrezzoPC'                  => $PrezzoPC,
                                                        'DataValiditaVoucher'       => $DataValiditaVoucher,

                                                    ]
                                                );

            

    }

    public function condizioni_generali($idsito,$Lingua)
    {
        $testo = '';
        #PCONDIZIONI GENERALI E POLITICHE DI CANCELLAZIONE PER VOUCHER
        $sel_cond_voucher = "SELECT
                                    hospitality_politiche_lingua.*
                                FROM
                                    hospitality_politiche_lingua
                                INNER JOIN
                                    hospitality_politiche ON hospitality_politiche.id = hospitality_politiche_lingua.id_politiche
                                WHERE
                                    hospitality_politiche_lingua.idsito = :idsito
                                AND
                                    hospitality_politiche.idsito = :idsito2
                                AND
                                    hospitality_politiche.tipo = :tipo
                                AND
                                    hospitality_politiche_lingua.Lingua = :Lingua
                                ORDER BY
                                    id DESC
                                LIMIT 1";
        $re_cond_v  = DB::select($sel_cond_voucher,['Lingua' => $Lingua, 'idsito' => $idsito , 'idsito2' => $idsito, 'tipo' => 1]);
        $tot_cond_v = sizeof($re_cond_v);
       
            if($tot_cond_v > 0){
                $rw_v = $re_cond_v[0];
                $testo =  $rw_v->testo;
            }else{
                $sel_cg = "SELECT * FROM hospitality_politiche_lingua WHERE idsito = :idsito AND Lingua = :Lingua ORDER BY id DESC LIMIT 1";
                $re_cg = DB::select($sel_cg,['Lingua' => $Lingua, 'idsito' => $idsito]);
                $tot_cg = sizeof($re_cg);
                if($tot_cg > 0){
                    $rw = $re_cg[0];
                    $testo =   $rw->testo;
                }
            }

        return $testo;
    }
}
