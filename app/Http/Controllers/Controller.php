<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Config;
use Datetime;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    
    /**
     * calc_prezzo_serv_landing
     *
     * @param  mixed $request
     * @return void
     */
    public function calc_prezzo_serv_landing(Request $request)
    {


        $notti              = $request->notti;
        $idsito             = $request->idsito;
        $n_proposta         = $request->n_proposta;
        $id_proposta        = $request->id_proposta;
        $id_servizio        = $request->id_servizio;
        $DataA_tmp          = explode("-",$request->dal);
        $dal                = $DataA_tmp[2].'-'.$DataA_tmp[1].'-'.$DataA_tmp[0];
        $DataP_tmp          = explode("-",$request->al);
        $al                 = $DataP_tmp[2].'-'.$DataP_tmp[1].'-'.$DataP_tmp[0];  
        $ReCalPrezzo        = $request->ReCalPrezzo;
        $check              = $request->check;
        $RecPrezzo_Ser      = $request->RecPrezzo_Ser;
        $ReCalCaparra       = $request->ReCalCaparra;
        $PercentualeCaparra = $request->PercCaparra;
    
        $sql         = "SELECT *
                            FROM hospitality_tipo_servizi
                            WHERE hospitality_tipo_servizi.idsito = :idsito
                                AND hospitality_tipo_servizi.Id = :id_servizio
                                AND hospitality_tipo_servizi.Abilitato = :Abilitato";
        $result      = DB::select($sql,['idsito' => $idsito,'id_servizio' => $id_servizio,'Abilitato' => 1]);
        if(sizeof($result)>0){
            $ret                 = $result[0];
    
            $PrezzoServizio      = $ret->PrezzoServizio;
            $PercentualeServizio = $ret->PercentualeServizio;
        }else{
            $PrezzoServizio      = '';
            $PercentualeServizio = '';
        }

        $testo = '';
    
        if($notti!='' && $notti !='undefined'){
    
            if($ret->CalcoloPrezzo=='Al giorno'){
              
                    $totale_unitaro_servizio = ($PrezzoServizio*$notti);
                    if($check == 1){
                        $totale_soggiorno = ($ReCalPrezzo+$totale_unitaro_servizio);
                    }
                    if($check == 0){
                        $totale_soggiorno = ($ReCalPrezzo-$totale_unitaro_servizio);
                    }
                    if($PrezzoServizio!='' && $PrezzoServizio!='0'){
                        $testo = '<div><small>(<b>'.number_format($PrezzoServizio,2,',','.').'</b> <span class="text-red">X</span> <b>'.$notti.'</b> <span class="text-red">=</span> <b>'.number_format($totale_unitaro_servizio,2,',','.').'</b>)</small></div>';
                    }else{
                        $testo = '';
                    }
                
    
            }elseif($ret->CalcoloPrezzo=='Una tantum'){
                
                    $totale_unitaro_servizio = $PrezzoServizio;
                    if($check == 1){
                        $totale_soggiorno = ($ReCalPrezzo+$totale_unitaro_servizio);
                    }
                    if($check == 0){
                        $totale_soggiorno = ($ReCalPrezzo-$totale_unitaro_servizio);
                    }
                    if($PrezzoServizio!='' && $PrezzoServizio!='0'){
                        $testo = '<div><small>(<b>'.number_format($PrezzoServizio,2,',','.').'</b> <span class="text-red">X</span> <b>'.$notti.'</b> <span class="text-red">=</span> <b>'.number_format($totale_unitaro_servizio,2,',','.').'</b>)</small></div>';
                    }else{
                        $testo = '';
                    }
               
    
            }elseif($ret->CalcoloPrezzo=='A persona'){
               
                    $totale_unitaro_servizio = $PrezzoServizio;
                    if($check == 1){
                        $totale_soggiorno = $ReCalPrezzo;
                    }
                    if($check == 0){
                        $totale_soggiorno = (floatval($ReCalPrezzo)-floatval($RecPrezzo_Ser));
                    } 
    
                    $testo = '<div><button class="btn btn-info btn-xs" type="button" data-toggle="modal" data-prezzo="'.$PrezzoServizio.'" data-notti="'.$notti.'" data-id_servizio="'.$id_servizio.'"  data-target="#modal_persone_'.$n_proposta.'_'.$id_servizio.'">Calcola</button></div>';
                
            }elseif($ret->CalcoloPrezzo=='A percentuale'){
               
                    $totale_unitaro_servizio = $PrezzoServizio;
                    
                    if($check == 1){
                        $aggiungere_perc = (($ReCalPrezzo*$PercentualeServizio)/100);
                        $totale_soggiorno = ceil($ReCalPrezzo+$aggiungere_perc);
                    }
                    if($check == 0){
                        $sottrarre_perc = (($ReCalPrezzo*$PercentualeServizio)/100);
                        $totale_soggiorno = ceil($ReCalPrezzo-$sottrarre_perc);
                    } 
                
            }
    
    
        }else{
            $testo = '';
            $totale_unitaro_servizio = '';
        }
        $output ='<script type="text/javascript">
                    $(document).ready(function() {'."\r\n";
                    
                        
                        if($ret->CalcoloPrezzo=='A persona'){
    
                            $output .= '$("#pulsante_calcola_'.$n_proposta.'_'.$id_servizio.'").html(\''.$testo.'\');'."\r\n";
    
                         }elseif($ret->CalcoloPrezzo=='Al giorno'){
                    
                            $output .=' $("#Prezzo_Servizio_'.$n_proposta.'_'.$id_servizio.'").html(\'<span class="nowrap" style="font-size:70%;padding-right:10px">('.number_format($PrezzoServizio,2,',','.').' <span class="text-red">X</span> '.$notti.')</span> <i class="fal fa-euro-sign"></i>  '.number_format($totale_unitaro_servizio,2,',','.').'\');'."\r\n";                   
    
                         }elseif($ret->CalcoloPrezzo=='Una tantum'){  
    
                            if($PrezzoServizio!='' && $PrezzoServizio!='0'){
    
                                $output .=' $("#Prezzo_Servizio_'.$n_proposta.'_'.$id_servizio.'").html(\'<i class="fal fa-euro-sign"></i> '.($totale_unitaro_servizio!=''?''.number_format($totale_unitaro_servizio,2,',','.').'':'').'\');'."\r\n";
       
                            }else{
                                $output .= '$("#spiegazione_prezzo_servizio_'.$n_proposta.'_'.$id_servizio.'").html(\''.$testo.'\');'."\r\n";
                            }
                        }
                        $output .= ' $("#PrezzoPC'.$n_proposta.'_'.$id_proposta.'").html(\''.number_format($totale_soggiorno,2,',','.').'\');'."\r\n";
                        $output .= ' $("#ReCalPrezzo'.$n_proposta.'_'.$id_proposta.'").val(\''.$totale_soggiorno.'\');'."\r\n";
                        $output .= ' $("#ReCalCaparra'.$n_proposta.'_'.$id_proposta.'").html(\''.number_format(($totale_soggiorno*$PercentualeCaparra/100),2,',','.').'\');'."\r\n";
                        $output .= ' $(".valore_caparra").html(\''.number_format(($totale_soggiorno*$PercentualeCaparra/100),2,',','.').'\');'."\r\n";
                        $output .= ' $("#PrezzoTitolo'.$n_proposta.'").html(\''.number_format($totale_soggiorno,2,',','.').'\');'."\r\n";
                        $output .= ' $("#PrezzoSpecchietto'.$n_proposta.'").html(\''.number_format($totale_soggiorno,2,',','.').'\');'."\r\n";
                        $output .= ' $("#PrezzoForm'.$n_proposta.'").html(\''.number_format($totale_soggiorno,2,',','.').'\');'."\r\n";
                        $output .= ' $("#NewTotale").val(\''.number_format($totale_soggiorno,2,'.','').'\');'."\r\n";
                        $output .= ' $("#TextNewTotale").html(\'&nbsp;<small>Nuovo totale <em>(New Total)</em> € '.number_format($totale_soggiorno,2,',','.').'</small>\');'."\r\n";
                        $output .= ' $("#NumeroProposta").val(\''.$n_proposta.'\');'."\r\n";
        $output .='});
                </script>'."\r\n";
        
        return $output;
    }

    
    /**
     * calc_prezzo_serv_a_persona_landing
     *
     * @param  mixed $request
     * @return void
     */
    public function calc_prezzo_serv_a_persona_landing(Request $request)
    {
        $notti              = $request->notti;
        $idsito             = $request->idsito;
        $n_proposta         = $request->n_proposta;
        $id_proposta        = $request->id_proposta;
        $id_servizio        = $request->id_servizio;
        $action             = $request->action;
        $prezzo             = $request->prezzo;
        $NPersone           = $request->NPersone;
        $ReCalPrezzo        = $request->ReCalPrezzo;
        $check              = $request->check;
        $ReCalCaparra       = $request->ReCalCaparra;
        $PercentualeCaparra = $request->PercCaparra;

       
        if($prezzo == 0){
            $prezzo = 'gratis';
        }else{
            $prezzo = number_format($prezzo,2,',','.');
        }
        $totale_unitaro_servizio = ((floatval($prezzo) * $NPersone)*$notti);
        if($totale_unitaro_servizio == 0){
            $totale_unitaro_servizio = '';
        }else{
            if($check == 1){
                $totale_soggiorno = ($ReCalPrezzo+$totale_unitaro_servizio);
            }
            if($check == 0){
                $totale_soggiorno = ($ReCalPrezzo-$totale_unitaro_servizio);
            }
        }

        $output = '<script type="text/javascript">
                        $(document).ready(function() {';
        $output .= '          $("#RecPrezzo_Servizio_'.$n_proposta.'_'.$id_servizio.'").val(\''.number_format($totale_unitaro_servizio,2,',','.').'\');';
        $output .= '           $("#Prezzo_Servizio_'.$n_proposta.'_'.$id_servizio.'").html(\'<span class="nowrap" style="font-size:70%;padding-right:10px">('.$prezzo.' <span class="text-red">X</span> '.$notti.' <span class="text-red">X</span> '.$NPersone.')</span> <i class="fal fa-euro-sign"></i>  '.number_format($totale_unitaro_servizio,2,',','.').'\');';       
        $output .= '          $("#PrezzoPC'.$n_proposta.'_'.$id_proposta.'").html(\''.number_format($totale_soggiorno,2,',','.').'\');';
        $output .= '          $("#ReCalPrezzo'.$n_proposta.'_'.$id_proposta.'").val(\''.$totale_soggiorno.'\');';  
        $output .= '          $("#ReCalCaparra'.$n_proposta.'_'.$id_proposta.'").html(\''.number_format(($totale_soggiorno*$PercentualeCaparra/100),2,',','.').'\');';

        $output .= '          $("#PrezzoTitolo'.$n_proposta.'").html(\''.number_format($totale_soggiorno,2,',','.').'\');';
        $output .= '          $("#PrezzoSpecchietto'.$n_proposta.'").html(\''.number_format($totale_soggiorno,2,',','.').'\');';
        $output .= '          $("#PrezzoForm'.$n_proposta.'").html(\''.number_format($totale_soggiorno,2,',','.').'\');';
        $output .= '          $("#NewTotale").val(\''.number_format($totale_soggiorno,2,'.','').'\');';
        $output .= '          $("#TextNewTotale").html(\'&nbsp;<small>Nuovo totale <em>(New Total)</em> € '.number_format($totale_soggiorno,2,',','.').'</small>\');';
        $output .= '          $("#NumeroProposta").val(\''.$n_proposta.'\');';
        $output .= '          $("#num_persone_'.$n_proposta.'_'.$id_servizio.'").val(\''.$NPersone.'\');';
        $output .= '          $("#notti'.$n_proposta.'_'.$id_servizio.'").val(\''.$notti.'\');';
        $output .='       
                        });
                    </script>';	

        return $output;
    }
    
    /**
     * salva_carta
     *
     * @param  mixed $request
     * @return void
     */
    public function salva_carta(Request $request)
    {
        $simple_string  = $request->cc_number;
        $ciphering      = "AES-128-CTR";
        $iv_length      = openssl_cipher_iv_length($ciphering);
        $options        = 0;
        $encryption_iv  = '1234567891011121';
        $encryption_key = "W3docs";
        $encryption     = openssl_encrypt($simple_string, $ciphering, $encryption_key, $options, $encryption_iv);

        DB::table('hospitality_carte_credito')->insert(
                                                            [
                                                                'idsito'           => $request->idsito,
                                                                'id_richiesta'     => $request->id_richiesta,
                                                                'carta'            => $request->nomecartacc,
                                                                'numero_carta'     => $encryption,
                                                                'intestatario'     => addslashes($request->cc_intestatario),
                                                                'scadenza'         => $request->cc_expiration,
                                                                'cvv'              => $request->cc_codice,
                                                                'policy'           => $request->cc_policy,
                                                                'data_inserimento' => date('Y-m-d')
                                                            ]
                                                        );
        DB::table('hospitality_cambio_pagamenti')->insert(
                                                            [
                                                                'idsito'           => $request->idsito,
                                                                'id_richiesta'     => $request->id_richiesta,
                                                                'SceltaPagamento'  => $request->nomecartacc,
                                                                'DataOperazione'   => date('Y-m-d H:i:s')
                                                            ]
                                                        );	

		$sql = 'SELECT siti.nome,
				siti.web,
				siti.https,
				siti.email,
				siti.indirizzo,
				siti.cap,
				siti.tel,
				siti.fax,
				comuni.nome_comune as comune,
				province.sigla_provincia as prov,
				users.logo
		FROM siti
		INNER JOIN comuni ON comuni.codice_comune = siti.codice_comune
		INNER JOIN province ON province.codice_provincia = siti.codice_provincia
		INNER JOIN users ON users.idsito = siti.idsito
		WHERE siti.idsito = :idsito';
		$rr  = DB::select($sql,['idsito' => $request->idsito]);
		$row = $rr[0];
		$sito_tmp  = str_replace("http://","",$row->web);
        $sito_tmp  = str_replace("https://","",$sito_tmp);
		$sito_tmp  = str_replace("www.","",$sito_tmp);
		$http = 'https://';
		$SitoWeb            = $http.'www.'.$sito_tmp;
		$NomeHotel          = $row->nome;
		$EmailHotel         = $row->email;
		$tel                = $row->tel;
		$fax                = $row->fax;
		$cap                = $row->cap;
		$indirizzo          = $row->indirizzo;
		$comune             = $row->comune;
		$prov               = $row->prov;
        $Logo               = $row->logo;

		 // query per i dati della richiesta
		$sel  = "SELECT * FROM hospitality_guest  WHERE Id = :Id AND idsito = :idsito";
        $d    = DB::select($sel,['Id' => $request->id_richiesta,'idsito' => $request->idsito]);
		$dati = $d[0];        
		// assegno alcune variabili
		$IdRichiesta        = $dati->Id;
		$NumeroPrenotazione = $dati->NumeroPrenotazione;
		$Nome               = $dati->Nome;
		$Cognome            = $dati->Cognome;    
		$Email              = $dati->Email;
		$Operatore          = $dati->ChiPrenota;
		$EmailOperatore     = $dati->EmailSegretaria;
		$Lingua             = $dati->Lingua;

		switch($Lingua){
			case "it":
				$oggetto_mail_client = $NomeHotel.' - Metodo di pagamento scelto - QUOTO!';
				$testo_mail_client = '<h1>Metodo di pagamento scelto con successo!</h1> 
										<p>Gentile <b>'.$Nome.' '.$Cognome.'</b>, <br>abbiamo registrato correttamente la modalità di pagamento scelta per la prenotazione nr. <b>'.$NumeroPrenotazione.'</b></p>
										<p>Metodo di pagamento scelto: <b>'.$request->nomecartacc.'</b> a garanzia</p>
										<p>A discrezione della struttura, le potrà essere inviata una mail con il voucher di conferma.</p>
										<p>Un saluto</p>';
			break;
			case "en":
				$oggetto_mail_client = $NomeHotel.' - Payment method chosen - QUOTO!';
				$testo_mail_client = '<h1>Payment method chosen successfully!</h1> 
										<p>Dear <b>'.$Nome.' '.$Cognome.'</b>,<br>we have correctly registered the payment method chosen for booking nr. <b>'.$NumeroPrenotazione.'</b></p>
										<p>Payment method chosen: <b>'.$request->nomecartacc.'</b> as guarantee</p>
										<p>At the discretion of the structure, an email may be sent to you with the confirmation voucher.</p>
										<p>A greeting</p>';
			break;
			case "fr":
				$oggetto_mail_client = $NomeHotel.' - Mode de paiement choisi - QUOTO!';
				$testo_mail_client = '<h1>Mode de paiement choisi avec succès!</h1> 
										<p>Cher <b>'.$Nome.' '.$Cognome.'</b>,<br>nous avons correctement enregistré le mode de paiement choisi pour la réservation nr. <b>'.$NumeroPrenotazione.'</b></p>
										<p>Mode de paiement choisi: <b>'.$request->nomecartacc.'</b> en garantie</p>
										<p>Au gré de la structure, un email pourra vous être envoyé avec le bon de confirmation.</p>
										<p>Une salutation</p>';
			break;
			case "de":
				$oggetto_mail_client = $NomeHotel.' - Zahlungsmethode gewählt - QUOTO!';
				$testo_mail_client = '<h1>Zahlungsmethode erfolgreich gewählt!</h1> 
										<p>Lieber <b>'.$Nome.' '.$Cognome.'</b>,<br>Wir haben die gewählte Zahlungsmethode für die Buchungs-Nr. korrekt registriert <b>'.$NumeroPrenotazione.'</b></p>
										<p>Gewählte Zahlungsmethode: <b>'.$request->nomecartacc.'</b> als Garantie</p>
										<p>Je nach Ermessen der Struktur kann Ihnen eine E-Mail mit dem Bestätigungsgutschein zugesandt werden.</p>
										<p>Ein Gruß</p>';
			break;
		}


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

		$mail->addAddress($EmailOperatore, $Operatore);
		$mail->isHTML(true);
		$mail->Subject = $Nome.' '.$Cognome.' - Metodo di pagamento scelto - QUOTO!';

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
									<h1>Metodo di pagamento scelto con successo!</h1> 
									<p>Ciao <b>'.$NomeHotel.'</b>,<br>il cliente <b>'.$Nome.' '.$Cognome.'</b> ha appena scelto la modalità di pagamento per la prenotazione nr. <b>'.$NumeroPrenotazione.'</b></p>
									<p>Metodo di pagamento scelto: <b>'.$request->nomecartacc.'</b> a garanzia</p>
									<p style="font-size:80%">Entra in Quoto! per controllare e procedere eventualmente alla chiusura del preventivo.</p>
									<br>
									<p style="font-size:11px">
										Questa e-mail è stata inviata automaticamente dal software!<br><br>
										Powered By QUOTO! - Network Service s.r.l
									</p>
								</td>
							</tr>
						  </table>
					   </body>
					</html>'; 		      
		
		$mail->msgHTML($messaggio, dirname(__FILE__));
		$mail->AltBody = 'To view the message, please use an HTML compatible email viewer!';
		$mail->send()  ;   

        $mail2 = new PHPMailer;

        $mail2->CharSet   = "UTF-8";
        $mail2->SMTPDebug = 0;
        $mail2->isSMTP();
        $mail2->Host       = env('MAIL_HOST');
        $mail2->SMTPAuth   = true;
        $mail2->Username   = env('MAIL_USERNAME');
        $mail2->Password   = env('MAIL_PASSWORD');
        $mail2->SMTPSecure = env('MAIL_ENCRYPTION');
        $mail2->Port       = env('MAIL_PORT');

		$mail2->setFrom(env('MAIL_FROM_ADDRESS'),env('APP_NAME'));
		$mail2->addAddress($Email, 'QUOTO!');
		$mail2->isHTML(true);
		$mail2->Subject = $oggetto_mail_client;

		 $messaggio2 = '<html>
					  <head>
						<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
						<title>QUOTO!</title>
					  </head>
					  <body>
						  <table cellpadding="0" cellspacing="0" width="100%" border="0">
							<tr>
								<td align="left" valign="top">
								<img src="'.env('APP_URL').'img/logo_email.png">
									'.$testo_mail_client.'
									<p style="font-size:80%">
									<b>'.$NomeHotel.'</b><br>
									'.$indirizzo.' - '.$cap.' '.$comune.' ('.$prov.')<br>
									 Tel. '.$tel.' - E-mail: '.$EmailHotel.' - '.$SitoWeb.'
									</p>
									<br>
									<p style="font-size:11px">
										Questa e-mail è stata inviata automaticamente dal software, non rispondere a questa e-mail!<br><br>
										Powered By QUOTO! - Network Service s.r.l
									</p>
								</td>
							</tr>
						  </table>
					   </body>
					</html>'; 		      
		
		$mail2->msgHTML($messaggio2, dirname(__FILE__));
		$mail2->AltBody = 'To view the message, please use an HTML compatible email viewer!';
		$mail2->send()  ; 


	
    }
    
    /**
     * salva_pagamento
     *
     * @param  mixed $request
     * @return void
     */
    public function salva_pagamento(Request $request)
    {
        $select = 'SELECT * FROM hospitality_altri_pagamenti WHERE id_richiesta = :id_richiesta';
        $q      = DB::select($select,['id_richiesta' => $request->id_richiesta]);
        $rec    = sizeof($q);
       
        if($rec == 0){
        
            DB::table('hospitality_altri_pagamenti')->insert(
                                                                [
                                                                    'idsito'           => $request->idsito,
                                                                    'id_richiesta'     => $request->id_richiesta,
                                                                    'TipoPagamento'    => $request->TipoPagamento,
                                                                    'data_inserimento' => date('Y-m-d')
                                                                ]
                                                            );
            DB::table('hospitality_cambio_pagamenti')->insert(
                                                                [
                                                                    'idsito'           => $request->idsito,
                                                                    'id_richiesta'     => $request->id_richiesta,
                                                                    'SceltaPagamento'  => $request->TipoPagamento,
                                                                    'DataOperazione'   => date('Y-m-d H:i:s')
                                                                ]
                                                            );	
	
        }else{
            $row = $q[0];
            if($request->TipoPagamento != $row->TipoPagamento){

                DB::table('hospitality_altri_pagamenti')->where('Id','=',$row->Id)->where('id_richiesta','=',$request->id_richiesta)->update(['TipoPagamento' => $request->TipoPagamento, 'data_inserimento' => date('Y-m-d')]);

                DB::table('hospitality_cambio_pagamenti')->insert(
                                                                    [
                                                                        'idsito'           => $request->idsito,
                                                                        'id_richiesta'     => $request->id_richiesta,
                                                                        'SceltaPagamento'  => $request->TipoPagamento,
                                                                        'DataOperazione'   => date('Y-m-d H:i:s')
                                                                    ]
                                                                );	
            }
        }
                $sql = 'SELECT siti.nome,
                        siti.web,
                        siti.https,
                        siti.email,
                        siti.indirizzo,
                        siti.cap,
                        siti.tel,
                        siti.fax,
                        comuni.nome_comune as comune,
                        province.sigla_provincia as prov,
                        users.logo
                FROM siti
                INNER JOIN comuni ON comuni.codice_comune = siti.codice_comune
                INNER JOIN province ON province.codice_provincia = siti.codice_provincia
                INNER JOIN users ON users.idsito = siti.idsito
                WHERE siti.idsito = :idsito';
                $rr  = DB::select($sql,['idsito' => $request->idsito]);
                $row = $rr[0];
                $sito_tmp  = str_replace("http://","",$row->web);
                $sito_tmp  = str_replace("https://","",$sito_tmp);
                $sito_tmp  = str_replace("www.","",$sito_tmp);
                $http = 'https://';
                $SitoWeb            = $http.'www.'.$sito_tmp;
                $NomeHotel          = $row->nome;
                $EmailHotel         = $row->email;
                $tel                = $row->tel;
                $fax                = $row->fax;
                $cap                = $row->cap;
                $indirizzo          = $row->indirizzo;
                $comune             = $row->comune;
                $prov               = $row->prov;
                $Logo               = $row->logo;


 				// query per i dati della richiesta
                 $sel  = "SELECT * FROM hospitality_guest  WHERE Id = :Id AND idsito = :idsito";
                 $d    = DB::select($sel,['Id' => $request->id_richiesta,'idsito' => $request->idsito]);
                 $dati = $d[0];        
                 // assegno alcune variabili
                 $IdRichiesta        = $dati->Id;
                 $NumeroPrenotazione = $dati->NumeroPrenotazione;
                 $Nome               = $dati->Nome;
                 $Cognome            = $dati->Cognome;    
                 $Email              = $dati->Email;
                 $Operatore          = $dati->ChiPrenota;
                 $EmailOperatore     = $dati->EmailSegretaria;
                 $Lingua             = $dati->Lingua;

				switch($Lingua){
					case "it":
						$oggetto_mail_client = $NomeHotel.' - Metodo di pagamento scelto - QUOTO!';
						$testo_mail_client = '<h1>Metodo di pagamento scelto con successo!</h1> 
												<p>Gentile <b>'.$Nome.' '.$Cognome.'</b>, <br>abbiamo registrato correttamente la modalità di pagamento scelta per la prenotazione nr. <b>'.$NumeroPrenotazione.'</b></p>
												<p>Metodo di pagamento scelto: <b>'.$request->TipoPagamento.'</b></p>
												<p>A discrezione della struttura, le potrà essere inviata una mail con il voucher di conferma.</p>
												'.($request->TipoPagamento=='Bonifico'?'<p style="color:red">Nel caso di pagamento tramite bonifico bancario il voucher le sarà inviato a ricezione dell\'accredito</p>':'').'
												<p>Un saluto</p>';
					break;
					case "en":
						$oggetto_mail_client = $NomeHotel.' - Payment method chosen - QUOTO!';
						$testo_mail_client = '<h1>Payment method chosen successfully!</h1> 
												<p>Dear <b>'.$Nome.' '.$Cognome.'</b>,<br>we have correctly registered the payment method chosen for booking nr. <b>'.$NumeroPrenotazione.'</b></p>
												<p>Payment method chosen: <b>'.$request->TipoPagamento.'</b></p>
												<p>At the discretion of the structure, an email may be sent to you with the confirmation voucher.</p>
												'.($request->TipoPagamento=='Bonifico'?'<p style="color:red">In the case of payment by bank transfer, the voucher will be sent to you upon receipt of the credit</p>':'').'
												<p>A greeting</p>';
					break;
					case "fr":
						$oggetto_mail_client = $NomeHotel.' - Mode de paiement choisi - QUOTO!';
						$testo_mail_client = '<h1>Mode de paiement choisi avec succès!</h1> 
												<p>Cher <b>'.$Nome.' '.$Cognome.'</b>,<br>nous avons correctement enregistré le mode de paiement choisi pour la réservation nr. <b>'.$NumeroPrenotazione.'</b></p>
												<p>Mode de paiement choisi: <b>'.$request->TipoPagamento.'</b></p>
												<p>Au gré de la structure, un email pourra vous être envoyé avec le bon de confirmation.</p>
												'.($request->TipoPagamento=='Bonifico'?'<p style="color:red">En cas de paiement par virement bancaire, le bon d\'échange vous sera envoyé dès réception de l\'avoir </p>':'').'
												<p>Une salutation</p>';
					break;
					case "de":
						$oggetto_mail_client = $NomeHotel.' - Zahlungsmethode gewählt - QUOTO!';
						$testo_mail_client = '<h1>Zahlungsmethode erfolgreich gewählt!</h1> 
												<p>Lieber <b>'.$Nome.' '.$Cognome.'</b>,<br>Wir haben die gewählte Zahlungsmethode für die Buchungs-Nr. korrekt registriert <b>'.$NumeroPrenotazione.'</b></p>
												<p>Gewählte Zahlungsmethode: <b>'.$request->TipoPagamento.'</b></p>
												<p>Je nach Ermessen der Struktur kann Ihnen eine E-Mail mit dem Bestätigungsgutschein zugesandt werden.</p>
												'.($request->TipoPagamento=='Bonifico'?'<p style="color:red">Bei Zahlung per Banküberweisung wird Ihnen der Gutschein nach Erhalt der Gutschrift zugesandt</p>':'').'
												<p>Ein Gruß</p>';
					break;
				}


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
		        $mail->addAddress($EmailOperatore, $Operatore);
		        $mail->isHTML(true);
		        $mail->Subject = $Nome.' '.$Cognome.' - Metodo di pagamento scelto - QUOTO!';

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
											<h1>Metodo di pagamento scelto con successo!</h1> 
											<p>Ciao <b>'.$NomeHotel.'</b>,<br>il cliente <b>'.$Nome.' '.$Cognome.'</b> ha appena scelto la modalità di pagamento per la prenotazione nr. <b>'.$NumeroPrenotazione.'</b></p>
											<p>Metodo di pagamento scelto: <b>'.$request->TipoPagamento.'</b> </p>
											<p style="font-size:80%">Entra in Quoto! per controllare e procedere eventualmente alla chiusura del preventivo.</p>
											<br>
											<p style="font-size:11px">
												Questa e-mail è stata inviata automaticamente dal software!<br><br>
												Powered By QUOTO! - Network Service s.r.l
											</p>
		                                </td>
		                            </tr>
		                          </table>
	                           </body>
	                        </html>'; 		      
		        
	            $mail->msgHTML($messaggio, dirname(__FILE__));
	            $mail->AltBody = 'To view the message, please use an HTML compatible email viewer!';
	            $mail->send()  ;   


                $mail2 = new PHPMailer;

                $mail2->CharSet   = "UTF-8";
                $mail2->SMTPDebug = 0;
                $mail2->isSMTP();
                $mail2->Host       = env('MAIL_HOST');
                $mail2->SMTPAuth   = true;
                $mail2->Username   = env('MAIL_USERNAME');
                $mail2->Password   = env('MAIL_PASSWORD');
                $mail2->SMTPSecure = env('MAIL_ENCRYPTION');
                $mail2->Port       = env('MAIL_PORT');
        
                $mail2->setFrom(env('MAIL_FROM_ADDRESS'),env('APP_NAME'));
		        $mail2->addAddress($Email, 'QUOTO!');
		        $mail2->isHTML(true);
		        $mail2->Subject = $oggetto_mail_client;

 				$messaggio2 = '<html>
	                          <head>
	                            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	                            <title>QUOTO!</title>
	                          </head>
	                          <body>
		                          <table cellpadding="0" cellspacing="0" width="100%" border="0">
		                            <tr>
		                                <td align="left" valign="top">
		                                <img src="'.env('APP_URL').'img/logo_email.png">
											'.$testo_mail_client.'
											<p style="font-size:80%">
											<b>'.$NomeHotel.'</b><br>
											'.$indirizzo.' - '.$cap.' '.$comune.' ('.$prov.')<br>
											 Tel. '.$tel.' - E-mail: '.$EmailHotel.' - '.$SitoWeb.'
											</p>
											<br>
											<p style="font-size:11px">
                                            	Questa e-mail è stata inviata automaticamente dal software, non rispondere a questa e-mail!<br><br>
                                            	Powered By QUOTO! - Network Service s.r.l
                                            </p>
		                                </td>
		                            </tr>
		                          </table>
	                           </body>
	                        </html>'; 		      
		        
	            $mail2->msgHTML($messaggio2, dirname(__FILE__));
	            $mail2->AltBody = 'To view the message, please use an HTML compatible email viewer!';
	            $mail2->send()  ; 

    }
    
    /**
     * aggiungi_chat
     *
     * @param  mixed $request
     * @return void
     */
    public function aggiungi_chat(Request $request)
    {
        DB::table('hospitality_chat')->insert(
                                                [
                                                    'idsito'             => $request->idsito,
                                                    'NumeroPrenotazione' => $request->NumeroPrenotazione,
                                                    'id_guest'           => $request->id_guest,
                                                    'lang'               => $request->lang,
                                                    'user'               => addslashes($request->user),
                                                    'chat'               => addslashes($request->chat),
                                                    'data'               => date('Y-m-d H:i:s')
                                                ]
                                            );

        $sel  = "SELECT * FROM hospitality_guest  WHERE Id = :Id AND idsito = :idsito";
        $d    = DB::select($sel,['Id' => $request->id_guest,'idsito' => $request->idsito]); 
        $dati = $d[0];        
        // assegno alcune variabili
        $IdRichiesta        = $dati->Id;
        $NumeroPrenotazione = $dati->NumeroPrenotazione;
        $AbilitaInvio       = $dati->AbilitaInvio;
        $TipoRichiesta      = $dati->TipoRichiesta;
        $Chiuso             = $dati->Chiuso;
        $Nome               = $dati->Nome;
        $Cognome            = $dati->Cognome;    
        $Email              = $dati->Email;
        $Operatore          = $dati->ChiPrenota;
        $EmailOperatore     = $dati->EmailSegretaria;

        DB::table('hospitality_chat_notify')->insert(
                                                        [
                                                            'idsito'             => $request->idsito,
                                                            'NumeroPrenotazione' => $request->NumeroPrenotazione,
                                                            'user'               => addslashes($request->user),
                                                        ]
                                                    );


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
        $mail->addAddress($EmailOperatore, $Operatore);
        $mail->isHTML(true);
        $mail->Subject = 'Hai ricevuto un messaggio in Chat da '.$Nome.' '.$Cognome.' - QUOTO!';

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
                                    <h1>Hai ricevuto un messaggio in Chat!</h1> 
                                    <p><b>Tipologia di Proposta:</b> '.($Chiuso==1?'Prenotazione confermata':$TipoRichiesta).' <b>Nr.</b> '.$NumeroPrenotazione.'</p>
                                    <p><b>Inviata  da</b> '.$Nome.' '.$Cognome.'</p>
                                    <p style="font-size:80%">Entra in QUOTO! per controllare il suo contenuto!</p>
                                </td>
                            </tr>
                            </table>
                        </body>
                    </html>'; 		      
        
        $mail->msgHTML($messaggio, dirname(__FILE__));
        $mail->AltBody = 'To view the message, please use an HTML compatible email viewer!';
        $mail->send()  ;             


    }

    public function ballon(Request $request)
    {
        $Nprenotazione = $request->Nprenotazione;
        $idsito        = $request->idsito;

        $output = '';
        $select      = "SELECT hospitality_chat.*
                        FROM hospitality_chat
                            INNER JOIN hospitality_guest ON hospitality_guest.Id = hospitality_chat.id_guest
                        WHERE hospitality_guest.NumeroPrenotazione = :NumeroPrenotazione
                            AND hospitality_chat.idsito = :idsito
                        ORDER BY hospitality_chat.data DESC";
        $result      = DB::select($select,['NumeroPrenotazione' => $Nprenotazione,'idsito' => $idsito]);
        $tot         = sizeof($result);
        if($tot > 0){

            $output .= '  <style>
                            .ballon{
                            font-size:14px!important;
                            width:100%;
                            height:auto;
                            border-radius: 10px 10px 10px 10px;
                            -moz-border-radius: 10px 10px 10px 10px;
                            -webkit-border-radius: 10px 10px 10px 10px;
                            border: 1px solid #d5d2d2;
                                background: rgba(237,237,237,1);
                                background: -moz-linear-gradient(top, rgba(237,237,237,1) 0%, rgba(246,246,246,0.79) 53%, rgba(255,255,255,0.6) 100%);
                                background: -webkit-gradient(left top, left bottom, color-stop(0%, rgba(237,237,237,1)), color-stop(53%, rgba(246,246,246,0.79)), color-stop(100%, rgba(255,255,255,0.6)));
                                background: -webkit-linear-gradient(top, rgba(237,237,237,1) 0%, rgba(246,246,246,0.79) 53%, rgba(255,255,255,0.6) 100%);
                                background: -o-linear-gradient(top, rgba(237,237,237,1) 0%, rgba(246,246,246,0.79) 53%, rgba(255,255,255,0.6) 100%);
                                background: -ms-linear-gradient(top, rgba(237,237,237,1) 0%, rgba(246,246,246,0.79) 53%, rgba(255,255,255,0.6) 100%);
                                background: linear-gradient(to bottom, rgba(237,237,237,1) 0%, rgba(246,246,246,0.79) 53%, rgba(255,255,255,0.6) 100%);
                                filter: progid:DXImageTransform.Microsoft.gradient( startColorstr=\'#ededed\', endColorstr=\'#ffffff\', GradientType=0 );
                            }
                            .clear{
                                clear:both;
                                height:10px;
                            }
                            .messaggi{
                                list-style-type: none;
                                padding:0px;
                            }
                            .user{
                                float:right;
                                width:100%;
                                text-align:right;
                                padding:20px;
                            }
                            .textchat{
                                float:left;
                                text-align:left;
                                padding:20px;
                                position:relative;
                            }
                            .operatore{
                                float:left;
                                width:100%!important;
                                text-align:left!important;
                                padding:20px 20px 0px 20px;
                            }
                            .textchatoperatore{
                                clear:both;
                                float:left;
                                text-align:left;
                                padding:20px;
                                position:relative;
                            }	
                        
                        </style>
                        <ul class="messaggi">';

            foreach($result as $key => $row){

                    $data_tmp = explode(" ",$row->data);
                    $data_d   = explode("-",$data_tmp[0]);
                    $data     = $data_d[2].'-'.$data_d[1].'-'.$data_d[0].' '.$data_tmp[1];            		

                    if($row->operator==1){
                        $sel = "SELECT img FROM hospitality_operatori WHERE  idsito = :idsito AND NomeOperatore = :NomeOperatore AND Abilitato = :Abilitato";
                        $q_img = DB::select($sel,['idsito' => $row->idsito,'NomeOperatore' => $row->user, 'Abilitato' => 1]);
                        if(sizeof($q_img)>0){
                            $img = $q_img[0];
                            $ImgOperatore = $img->img;
    
                            if($ImgOperatore == ''){
                                $ImgOperatore = ''.env('APP_URL').'img/receptionists.png';
                            }else{
                                $CheckImgOp = config('global.settings.BASE_URL_IMG').'uploads/'.$row->idsito.'/'.$ImgOperatore;
                                if (filter_var($CheckImgOp, FILTER_VALIDATE_URL) && @get_headers($CheckImgOp, 1)[0] == 'HTTP/1.1 200 OK') {
                                    $ImgOperatore = $CheckImgOp;
                                }else{
                                    $ImgOperatore = config('global.settings.BASE_URL_IMG').'uploads/loghi/'.$ImgOperatore.'';
                                }
                                	
                            }
                        }
						
                    }

                    $output .='<li>																	        
                                    <div class="ballon">
                                        <div '.($row->operator==0?'class="user"':'class="operatore"').'>
                                            <strong class="text-red">'.$row->user.'</strong> &nbsp;&nbsp;'.($row->operator==0?'<img src="'.env('APP_URL').'img/receptionists.png" style="width:32px;height:32px" class="img-circle">':'<img src="'.$ImgOperatore.'" style="width:32px;height:32px" class="img-circle">').'<br>
                                            <small>ha scritto il '.$data.'</small>
                                        </div>
                                            <div '.($row->operator==0?'class="textchat"':'class="textchatoperatore"').'>
                                                '.nl2br($row->chat).'
                                            </div>
                                            <div class="clear"></div>
                                    </div>														        
                                                        
                            </li><br>';
            }

            $output .='</ul>';

        }

        return $output;
    }
    
    /**
     * ballon_smart
     *
     * @param  mixed $request
     * @return void
     */
    public function ballon_smart(Request $request)
    {
        $ballon_smart = '';

        $mesi=array("01" => "Gennaio","02" => "Febbraio","03" => "Marzo","04" => "Aprile","05" => "Maggio",
                    "06" => "Giugno","07" => "Luglio","08" => "Agosto",
                    "09" => "Settembre","10" => "Ottobre","11" => "Novembre","12" => "Dicembre");

            $select      ="SELECT hospitality_chat.*
                            FROM hospitality_chat
                                INNER JOIN hospitality_guest ON hospitality_guest.Id = hospitality_chat.id_guest
                            WHERE hospitality_guest.NumeroPrenotazione = :NumeroPrenotazione
                                AND hospitality_chat.idsito = :idsito
                            ORDER BY hospitality_chat.data DESC";
            $result      = DB::select($select,['NumeroPrenotazione' => $request->Nprenotazione, 'idsito' => $request->idsito]);
            $tot         = sizeof($result);
            if($tot > 0){


                foreach($result as $key => $row){

                        $data_tmp = explode(" ",$row->data);
                        $data_d   = explode("-",$data_tmp[0]);
                        $data     = $data_d[2].' '.$mesi[$data_d[1]].' '.$data_d[0];            		


                        $ballon_smart .='<div class="m m-x-12 linea">
                                            <div class="m m-x-3 m-x-tr t12">
                                                <div class="box6">
                                                    <div class="autore w700">'.$row->user.'</div>
                                                    <div class="data">'.$data.'</div>
                                                    <div class="ora"><i class="fal fa-clock"></i> '.$data_tmp[1].'</div>
                                                </div>
                                            </div>
                                            <div class="m m-x-9 m-x-tl">
                                                <div class="box6">
                                                    <p>'.nl2br($row->chat).'</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="ca"></div>';
                }


            }  
            
            return $ballon_smart;
    }
        
    /**
     * clean
     *
     * @param  mixed $stringa
     * @return void
     */
    public function clean($stringa)
    {

        $clean_title = str_replace( "€", "", $stringa );
        $clean_title = str_replace( "%", "", $clean_title );
        $clean_title = str_replace( ".", "", $clean_title );
        $clean_title = str_replace( " ", "", $clean_title );
        $clean_title = trim($clean_title);

        return($clean_title);
    }

    /**
     * calc_prezzo_servizio
     *
     * @param  mixed $request
     * @return void
     */
    public function calc_prezzo_servizio(Request $request)
    {
        $output               = '';
        $new_explane_servizio = '';
        $new_totale_servizio  = '';
        $new_totale_servizi   = '';
        $new_totale_proposta  = '';
        $new_totale_caparra   = '';

        $notti               = $request->notti;
        $idsito              = $request->idsito;
        $n_proposta          = $request->n_proposta;
        $id_richiesta        = $request->id_richiesta;
        $id_proposta         = $request->id_proposta;
        $id_servizio         = $request->id_servizio;
        $tipoCalcolo         = $request->tipoCalcolo;
        $prezzoServizio      = $request->prezzoServizio;
        $percentualeServizio = floatval($request->percentualeServizio);
        $totaleServizi_      = $this->clean($request->totaleServizi);
        $totaleServizi_      = str_replace(",", ".", $totaleServizi_);
        $totaleServizi       = floatval($totaleServizi_);
        $totaleProposta_     = $this->clean($request->totaleProposta);
        $totaleProposta_     = str_replace(",", ".", $totaleProposta_);
        $totaleProposta      = floatval($totaleProposta_);
        $totaleCaparra_      = $this->clean($request->totaleCaparra);
        $totaleCaparra_      = str_replace(",", ".", $totaleCaparra_);
        $totaleCaparra       = floatval($totaleCaparra_);
        $percentualeCaparra_ = $this->clean($request->percentualeCaparra);
        $percentualeCaparra_ = str_replace(",", ".", $percentualeCaparra_);
        $percentualeCaparra  = intval($percentualeCaparra_);
        $check               = $request->check;
    
    
    
        if($notti!='' && $notti !='undefined'){
    
    
            if($prezzoServizio!='' || $prezzoServizio!=0){
    
    
                if($tipoCalcolo=='Al giorno'){
    
                    if($check == 1){
                        $new_explane_servizio = '('.number_format($prezzoServizio,2,',','.').' X '.$notti .' gg)';
                        $new_totale_servizio = ($prezzoServizio*$notti);
                        $new_totale_servizi = ($totaleServizi+$new_totale_servizio);
                        $new_totale_proposta = ($totaleProposta+$new_totale_servizio);
                        if($percentualeCaparra != ''){
                            $new_totale_caparra = ($new_totale_proposta*$percentualeCaparra/100);
                        }else{
                            $new_totale_caparra = '';
                        }
                    }
                    if($check == 0){
                        $new_explane_servizio = '';
                        $new_totale_servizio = ($prezzoServizio*$notti);
                        $new_totale_servizi = ($totaleServizi-$new_totale_servizio);
                        $new_totale_proposta = ($totaleProposta-$new_totale_servizio);
                        if($percentualeCaparra != ''){
                            $new_totale_caparra_ = ($new_totale_proposta*$percentualeCaparra/100);
                            $new_totale_caparra = ($new_totale_caparra_-floatval($new_totale_caparra));
                        }else{
                            $new_totale_caparra = '';
                        }
                    }
    
                }elseif($tipoCalcolo=='Una tantum'){
    
                    if($check == 1){
                        $new_explane_servizio = '';
                        $new_totale_servizio = $prezzoServizio;
                        $new_totale_servizi = ($totaleServizi+$new_totale_servizio);
                        $new_totale_proposta = ($totaleProposta+$new_totale_servizio);
                        if($percentualeCaparra != ''){
                            $new_totale_caparra = ($new_totale_proposta*$percentualeCaparra/100);
                        }else{
                            $new_totale_caparra = '';
                        }
                    }
                    if($check == 0){
                        $new_explane_servizio = '';
                        $new_totale_servizio = $prezzoServizio;
                        $new_totale_servizi = ($totaleServizi-$new_totale_servizio);
                        $new_totale_proposta = ($totaleProposta-$new_totale_servizio);
                        if($percentualeCaparra != ''){
                            $new_totale_caparra_ = ($new_totale_proposta*$percentualeCaparra/100);
                            $new_totale_caparra = ($new_totale_caparra_-$new_totale_caparra);
                        }else{
                            $new_totale_caparra = '';
                        }
                    }
    
                }elseif($tipoCalcolo=='A persona'){
    
                        $new_explane_servizio = '';
                        $new_totale_servizio  = '';
                        $new_totale_proposta  = '';
                        $new_totale_caparra   = '';
                    
    
                }elseif($tipoCalcolo=='A percentuale'){
                    
                    if($check == 1){
                        $new_explane_servizio = '';
                        $new_totale_servizio = (($prezzoServizio*$percentualeServizio)/100);
                        $new_totale_servizi = ($totaleServizi+$new_totale_servizio);
                        $new_totale_proposta = ($totaleProposta+$new_totale_servizio);
    
                        if($percentualeCaparra != ''){
                            $new_totale_caparra = ($new_totale_proposta*$percentualeCaparra/100);
                        }else{
                            $new_totale_caparra = '';
                        }
                    }
                    if($check == 0){
                        $new_explane_servizio = '';
                        $new_totale_servizio = (($prezzoServizio*$percentualeServizio)/100);
                        $new_totale_servizi = ($totaleServizi-$new_totale_servizio);
                        $new_totale_proposta = ($totaleProposta-$new_totale_servizio);
    
                        if($percentualeCaparra != ''){
                            $new_totale_caparra_ = ($new_totale_proposta*$percentualeCaparra/100);
                            $new_totale_caparra = ($new_totale_caparra_-$new_totale_caparra);
                        }else{
                            $new_totale_caparra = '';
                        }
                    } 
                }
            }else{
                $new_explane_servizio = '';
                $new_totale_servizio = '';
                $new_totale_servizi   = '';
                $new_totale_proposta = '';
                $new_totale_caparra  = '';
            }
    
        }else{
            $new_explane_servizio = '';
            $new_totale_servizio = '';
            $new_totale_servizi   = '';
            $new_totale_proposta = '';
            $new_totale_caparra  = '';
        }
    
    
        $output = '<script type="text/javascript">
                        $(document).ready(function() {'."\r\n";
        $output .=  '      $("#explane_calcolo'.$n_proposta.'_'.$id_servizio.'").html(\''.$new_explane_servizio.'\');'."\r\n";
        $output .=  '      $("#totale_calcolo'.$n_proposta.'_'.$id_servizio.'").html(\'€ '.number_format($new_totale_servizio,2,',','.').'\');'."\r\n";
        if($percentualeCaparra != ''){
            $output .=  '  $(".valore_caparra").html(\'€ '.number_format($new_totale_caparra,2,',','.').'\');'."\r\n";
        }
        $output .=  '      $(".totale").html(\'€ '.number_format($new_totale_proposta,2,',','.').'\');'."\r\n";
        $output .=  '      $(".totale_servizi").html(\'€ '.number_format($new_totale_servizi,2,',','.').'\');'."\r\n";
        $output .=  '      $("#NewTotale").val(\''.number_format($new_totale_proposta,2,'.','').'\');'."\r\n";
        $output .=  '      $("#TextNewTotale").html(\'&nbsp;<small>Nuovo totale <em>(New Total)</em> € '.number_format($new_totale_proposta,2,',','.').'</small>\');'."\r\n";
        $output .=  '      $("#NumeroProposta").val(\''.$n_proposta.'\');'."\r\n";
        $output .= '   });'."\r\n";
        $output .= '</script>'."\r\n"; 
    
        return $output;
    }
    
    /**
     * calc_prezzo_servizio_a_persona
     *
     * @param  mixed $request
     * @return void
     */
    public function calc_prezzo_servizio_a_persona(Request $request)
    {
        $output               = '';
        $new_explane_servizio = '';
        $new_totale_servizio  = '';
        $new_totale_servizi   = '';
        $new_totale_proposta  = '';
        $new_totale_caparra   = '';

        $notti               = $request->notti;
        $NPersone            = $request->NPersone;
        $idsito              = $request->idsito;
        $lingua              = $request->lingua;
        $n_proposta          = $request->n_proposta;
        $id_richiesta        = $request->id_richiesta;
        $id_proposta         = $request->id_proposta;
        $id_servizio         = $request->id_servizio;
        $prezzoServizio      = $request->prezzoServizio;
        $totaleServizi_      = $this->clean($request->totaleServizi);
        $totaleServizi_      = str_replace(",", ".", $totaleServizi_);
        $totaleServizi       = floatval($totaleServizi_);
        $totaleProposta_     = $this->clean($request->totaleProposta);
        $totaleProposta_     = str_replace(",", ".", $totaleProposta_);
        $totaleProposta      = floatval($totaleProposta_);
        $totaleCaparra_      = $this->clean($request->totaleCaparra);
        $totaleCaparra_      = str_replace(",", ".", $totaleCaparra_);
        $totaleCaparra       = floatval($totaleCaparra_);
        $percentualeCaparra_ = $this->clean($request->percentualeCaparra);
        $percentualeCaparra_ = str_replace(",", ".", $percentualeCaparra_);
        $percentualeCaparra  = intval($percentualeCaparra_);
        $check               = $request->check;
    
    
    switch($lingua){
        case"it":
            $calcolaCostoServizio           = 'Calcola il costo del servizio';
            $rimuoviQuestoServizio          = 'Clicca e rimuovi questo servizio';
        break;
        case"en":
            $calcolaCostoServizio           = 'Calculate the service cost';
            $rimuoviQuestoServizio          = 'Click and remove this service';
        break;    
        case"fr":
            $calcolaCostoServizio           = 'Calculer le coût du service';
            $rimuoviQuestoServizio          = 'Cliquez et supprimer ce service';
        break;    
        case"de":
            $calcolaCostoServizio           = 'Servicekosten berechnen';
            $rimuoviQuestoServizio          = 'Klicken Sie auf diesen Service';
        break;
    }
    
    
        if($notti!='' && $notti !='undefined'){

            if($prezzoServizio!='' && $prezzoServizio!=0){    
    
                    if($check == 1){
                        $new_explane_servizio = '('.number_format($prezzoServizio,2,',','.').' X '.$notti .' gg X '.$NPersone.' pax )';
    
                        $new_totale_servizio = (($prezzoServizio*$notti)*$NPersone) ;
                        $new_totale_servizi = ($totaleServizi+$new_totale_servizio);
                        $new_totale_proposta = ($totaleProposta+$new_totale_servizio);
                        if($percentualeCaparra != ''){
                            $new_totale_caparra = ($new_totale_proposta*$percentualeCaparra/100);
                        }else{
                            $new_totale_caparra = '';
                        }
                    }
                    if($check == 0){
                        $new_explane_servizio = '';
    
                        $new_totale_servizio = (($prezzoServizio*$notti)*$NPersone);
                        $new_totale_servizi = ($totaleServizi-$new_totale_servizio);
                        $new_totale_proposta = ($totaleProposta-$new_totale_servizio);
                        if($percentualeCaparra != ''){
                            $new_totale_caparra_ = ($new_totale_proposta*$percentualeCaparra/100);
                            $new_totale_caparra = ($new_totale_caparra_-floatval($new_totale_caparra));
                        }else{
                            $new_totale_caparra = '';
                        }
                    }
    
            }else{
                $new_explane_servizio = '';
                $new_totale_servizio = '';
                $new_totale_servizi   = '';
                $new_totale_proposta = '';
                $new_totale_caparra  = '';
            }
    
        }else{
            $new_explane_servizio = '';
            $new_totale_servizio = '';
            $new_totale_servizi   = '';
            $new_totale_proposta = '';
            $new_totale_caparra  = '';
        }
    
    
        $output = '<script type="text/javascript">
                        $(document).ready(function() {'."\r\n";
            $output .= '    $("#check'.$n_proposta.'_'.$id_servizio.'").val(\''.$check.'\');'."\r\n";
    
        if($check == 1){
            $output .=  '      $("#remove_calcolo'.$n_proposta.'_'.$id_servizio.'").show();'."\r\n";
            $output .= '       $("#send_re_calc'.$n_proposta.'_'.$id_servizio.'").addClass("bg-red");'."\r\n";
            $output .= '       $("#send_re_calc'.$n_proposta.'_'.$id_servizio.'").html(\''.$rimuoviQuestoServizio.'\');'."\r\n";
            $output .= '       $("#Nnotti'.$n_proposta.'_'.$id_servizio.'").attr("disabled",true);'."\r\n";
            $output .= '       $("#NPersone'.$n_proposta.'_'.$id_servizio.'").attr("disabled",true);'."\r\n";
            $output .= '       $("#dettaglio'.$n_proposta.'_'.$id_servizio.'").hide();'."\r\n";
        }else{
            $output .=  '      $("#remove_calcolo'.$n_proposta.'_'.$id_servizio.'").hide();'."\r\n";
            $output .= '       $("#send_re_calc'.$n_proposta.'_'.$id_servizio.'").removeClass("bg-red");'."\r\n";
            $output .= '       $("#send_re_calc'.$n_proposta.'_'.$id_servizio.'").html(\''.$calcolaCostoServizio.'\');'."\r\n";
            $output .= '       $("#Nnotti'.$n_proposta.'_'.$id_servizio.'").attr("disabled",false);'."\r\n";
            $output .= '       $("#NPersone'.$n_proposta.'_'.$id_servizio.'").attr("disabled",false);'."\r\n";
            $output .= '       $("#dettaglio'.$n_proposta.'_'.$id_servizio.'").show();'."\r\n";
        }
        $output .=  '      $("#explane_calcolo'.$n_proposta.'_'.$id_servizio.'").html(\''.$new_explane_servizio.'\');'."\r\n";
        $output .=  '      $("#totale_calcolo'.$n_proposta.'_'.$id_servizio.'").html(\'€ '.number_format($new_totale_servizio,2,',','.').'\');'."\r\n";
        if($percentualeCaparra != ''){
            $output .=  '      $(".valore_caparra").html(\'€ '.number_format($new_totale_caparra,2,',','.').'\');'."\r\n";
        }
        $output .=  '      $(".totale").html(\'€ '.number_format($new_totale_proposta,2,',','.').'\');'."\r\n";
        $output .=  '      $(".totale_servizi").html(\'€ '.number_format($new_totale_servizi,2,',','.').'\');'."\r\n";
        $output .=  '      $("#NewTotale").val(\''.number_format($new_totale_proposta,2,'.','').'\');'."\r\n";
        $output .=  '      $("#TextNewTotale").html(\'&nbsp;<small>Nuovo totale <em>(New Total)</em> € '.number_format($new_totale_proposta,2,',','.').'</small>\');'."\r\n";
        $output .=  '      $("#NumeroProposta").val(\''.$n_proposta.'\');'."\r\n";
        $output .= '   });'."\r\n";
        $output .= '</script>'."\r\n"; 
    
        return $output;
    }  

    
    /**
     * gmap
     *
     * @param  mixed $request
     * @return void
     */
    public function gmap(Request $request)
    {
        $sql = 'SELECT 
                    siti.coordinate,
                    siti.nome,
                    siti.web,
                    siti.indirizzo,
                    siti.cap,
                    comuni.nome_comune,
                    province.nome_provincia,
                    province.sigla_provincia,
                    regioni.nome_regione,
                    users.logo
                FROM siti 
                INNER JOIN comuni ON comuni.codice_comune        = siti.codice_comune
                INNER JOIN province ON province.codice_provincia = siti.codice_provincia
                INNER JOIN regioni ON regioni.codice_regione     = siti.codice_regione
                INNER JOIN users ON users.idsito                 = siti.idsito
                WHERE siti.idsito                                = :idsito';
            $rr = DB::select($sql,['idsito' => session('IDSITO')]); 
            $DatiCliente = $rr[0];

            $NomeCliente = addslashes($DatiCliente->nome);
            $Indirizzo   = addslashes($DatiCliente->indirizzo);
            $Localita    = addslashes($DatiCliente->nome_comune);
            $Cap         = $DatiCliente->cap;
            $Provincia   = $DatiCliente->sigla_provincia;
            $Logo        = $DatiCliente->logo;
            $coordinateAsText = unpack('x/x/x/x/corder/Ltype/dlon/dlat', $DatiCliente->coordinate);
            if ($coordinateAsText != '') {
                $latitudine = $coordinateAsText['lon'];
                $longitudine = $coordinateAsText['lat'];
            } else {
                $latitudine = '';
                $longitudine = '';
            }

            return view('/gmap',
                                                    [
                                                        'NomeCliente' => $NomeCliente,
                                                        'Indirizzo'   => $Indirizzo,
                                                        'Localita'    => $Localita,
                                                        'Cap'         => $Cap,
                                                        'Provincia'   => $Provincia,
                                                        'Logo'        => $Logo,
                                                        'latitudine'  => $latitudine,
                                                        'longitudine' => $longitudine,
                                                        'from_lati'   => $request->from_lati,
                                                        'from_long'   => $request->from_long,
                                                        'travelmode'  => $request->travelmode,

                                                    ]
                                                );
    }

    /**
     * verifyCaptcha
     *
     * @param  mixed $response
     * @param  mixed $remoteip
     * @param  mixed $chiave_segreta_recaptcha
     * @return void
     */
    public function verifyCaptcha($response, $remoteip, $chiave_segreta_recaptcha)
    {

        $url = "https://www.google.com/recaptcha/api/siteverify";
        $url .= "?secret=" . urlencode(stripslashes($chiave_segreta_recaptcha));
        $url .= "&response=" . urlencode(stripslashes($response));
        $url .= "&remoteip=" . urlencode(stripslashes($remoteip));

        $response = file_get_contents($url);
        $response = json_decode($response, true);

        return (object) $response;
    }

    /**
     * get_account_analytics
     *
     * @param  mixed $idsito
     * @return void
     */
    public function get_account_analytics($idsito)
    {

        $sql = "SELECT IdAccountAnalytics,measurement_id,api_secret FROM siti WHERE idsito = :idsito";
        $rs  = DB::select($sql, ['idsito' => $idsito]);
        if (sizeof($rs) > 0) {
            $rc = $rs[0];

            return $rc;
        }

    }

    /**
     * accetta_proposta
     *
     * @param  mixed $request
     * @return void
     */
    public function accetta_proposta(Request $request)
    {

        $sql = 'SELECT siti.nome,
                                    siti.web,
                                    siti.https,
                                    siti.email,
                                    siti.indirizzo,
                                    siti.cap,
                                    siti.tel,
                                    siti.fax,
                                    comuni.nome_comune as comune,
                                    province.sigla_provincia as prov,
                                    users.logo
                            FROM siti
                            INNER JOIN comuni ON comuni.codice_comune = siti.codice_comune
                            INNER JOIN province ON province.codice_provincia = siti.codice_provincia
                            INNER JOIN users ON users.idsito = siti.idsito
                            WHERE siti.idsito = :idsito';

        $rr = DB::select($sql, ['idsito' => $request->idsito]);

        $row = $rr[0];

        $sito_tmp = str_replace("http://", "", $row->web);
        $sito_tmp = str_replace("https://", "", $sito_tmp);
        $sito_tmp = str_replace("www.", "", $sito_tmp);
        if ($row->https == 1) {
            $http = 'https://';
        } else {
            $http = 'http://';
        }
        $SitoWeb    = $http . 'www.' . $sito_tmp;
        $NomeHotel  = $row->nome;
        $EmailHotel = $row->email;
        $tel        = $row->tel;
        $fax        = $row->fax;
        $cap        = $row->cap;
        $indirizzo  = $row->indirizzo;
        $comune     = $row->comune;
        $prov       = $row->prov;
        $logo       = $row->logo;
        $Lingua     = $request->lang;

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

        $mail->setFrom(env('MAIL_FROM_ADDRESS'), $request->nome_utente);

        // EMAIL INDIRIZZATA AL HOTEL
        $mail->addAddress($request->email_hotel, $request->nome_hotel);

        $mail->Subject = 'Conferma proposta soggiorno (' . $request->nome_hotel . ')';

        include_once public_path('email_template/conferma_mail.php');

        $contenuto = $top . $contenuto_html . $contenuto_html_h . $contenuto_html_close . $close;

        $mail->msgHTML($contenuto, dirname(__FILE__));

        $mail->AltBody = 'This is a plain-text message body';

        $recaptchaResponse = $request->input('g-recaptcha-response');

        if (isset($recaptchaResponse)) {

            $remoteip  = $_SERVER["REMOTE_ADDR"];
            $recaptcha = $recaptchaResponse;

            $response = $this->verifyCaptcha($recaptcha, $remoteip, '6Lf4WPQUAAAAALWdUSPnZvVwGKrwcJHxfkaOHrt_');

            if ($response->success) {

                if (! empty($request->proposta)) {
                    //send the message, check for errors

                    if (! $mail->send()) {

                        $risposta = '0';

                    } else {

                        // AGGIORNAMWENTO DA PREVENTIVO A CONFERMA
                        if ($request->tipo_richiesta == 'Preventivo') {

                            $select = "SELECT * FROM hospitality_guest WHERE Id = :id_richiesta";
                            $ris    = DB::select($select, ['id_richiesta' => $request->id_richiesta]);
                            $rws    = $ris[0];

                            $select2    = "SELECT * FROM hospitality_guest WHERE idsito = :idsito AND NumeroPrenotazione = :NumeroPrenotazione AND TipoRichiesta = :TipoRichiesta";
                            $ris2       = DB::select($select2, ['idsito' => $rws->idsito, 'NumeroPrenotazione' => $rws->NumeroPrenotazione, 'TipoRichiesta' => 'Conferma']);
                            $check_conf = sizeof($ris2);
                            // se la conferma NON è già presente
                            $risposta = '2';

                            if ($check_conf == 0) {

                                $mail2 = new PHPMailer;

                                $mail2->CharSet   = "UTF-8";
                                $mail2->SMTPDebug = 0;
                                $mail2->isSMTP();
                                $mail2->Host       = env('MAIL_HOST');
                                $mail2->SMTPAuth   = true;
                                $mail2->Username   = env('MAIL_USERNAME');
                                $mail2->Password   = env('MAIL_PASSWORD');
                                $mail2->SMTPSecure = env('MAIL_ENCRYPTION');
                                $mail2->Port       = env('MAIL_PORT');

                                $mail2->setFrom(env('MAIL_FROM_ADDRESS'), $request->nome_utente);

                                $mail2->Subject = 'Copia Conferma proposta soggiorno (' . $request->nome_hotel . ')';
                                // COPIA EMAIL INDIRIZZATA AL CLIENTE
                                $mail2->addAddress($request->email_utente, $request->nome_utente);

                                $contenuto2 = $top . $contenuto_html . $contenuto_html_c . $contenuto_html_close . $close;

                                $mail2->msgHTML($contenuto2, dirname(__FILE__));

                                $mail2->AltBody = 'This is a plain-text message body';

                                $mail2->send();

                                $risposta = '1';

                                $riepilogo_prop = '';

                                foreach ($request->proposta as $chiave => $valore) {
                                    $riepilogo_prop .= $v . ($request->NewTotale != '' ? ' Nuovo totale (New Total) € ' . number_format($request->NewTotale, 2, ',', '.') : '');
                                }

                                DB::table('hospitality_guest')->insert(
                                    [
                                        'id_politiche'              => $rws->id_politiche,
                                        'id_template'               => $rws->id_template,
                                        'AccontoRichiesta'          => $rws->AccontoRichiesta,
                                        'AccontoLibero'             => $rws->AccontoLibero,
                                        'DataRichiesta'             => date('Y-m-d'),
                                        'TipoRichiesta'             => 'Conferma',
                                        'TipoVacanza'               => $rws->TipoVacanza,
                                        'ChiPrenota'                => addslashes($rws->ChiPrenota),
                                        'EmailSegretaria'           => $rws->EmailSegretaria,
                                        'idsito'                    => $rws->idsito,
                                        'MultiStruttura'            => addslashes($rws->MultiStruttura),
                                        'Nome'                      => addslashes($rws->Nome),
                                        'Cognome'                   => addslashes($rws->Cognome),
                                        'Email'                     => $rws->Email,
                                        'PrefissoInternazionale'    => $rws->PrefissoInternazionale,
                                        'Cellulare'                 => ($rws->Cellulare == '' ? $request->Cellulare : $rws->Cellulare),
                                        'Lingua'                    => $rws->Lingua,
                                        'DataArrivo'                => $rws->DataArrivo,
                                        'DataPartenza'              => $rws->DataPartenza,
                                        'NumeroPrenotazione'        => $rws->NumeroPrenotazione,
                                        'NumeroAdulti'              => $rws->NumeroAdulti,
                                        'NumeroBambini'             => $rws->NumeroBambini,
                                        'EtaBambini1'               => $rws->EtaBambini1,
                                        'EtaBambini2'               => $rws->EtaBambini2,
                                        'EtaBambini3'               => $rws->EtaBambini3,
                                        'EtaBambini4'               => $rws->EtaBambini4,
                                        'EtaBambini5'               => $rws->EtaBambini5,
                                        'EtaBambini6'               => $rws->EtaBambini6,
                                        'FontePrenotazione'         => $rws->FontePrenotazione,
                                        'TipoPagamento'             => $rws->TipoPagamento,
                                        'Note'                      => addslashes($rws->Note),
                                        'AbilitaInvio'              => 1,
                                        'CheckConsensoPrivacy'      => $request->policy_soggiorno,
                                        'CheckConsensoMarketing'    => $request->marketing,
                                        'CheckConsensoProfilazione' => $request->profilazione,
                                        'Ip'                        => $request->ip,
                                        'Agent'                     => addslashes($request->agent),
                                        'DataVoucherRecSend'        => $rws->DataVoucherRecSend,
                                        'DataValiditaVoucher'       => $rws->DataValiditaVoucher,
                                        'RequestProposta'           => addslashes($riepilogo_prop),
                                        'CodiceSconto'              => addslashes($rws->CodiceSconto),
                                    ]
                                );

                                $Id_richiesta = DB::getPdo()->lastInsertId();

                                // UPDATE dello stato del preventivo in accettato
                                DB::table('hospitality_guest')->where('Id', '=', $request->id_richiesta)->update(['Accettato' => 1]);

                                $check_proposta = [];
                                foreach ($request->proposta as $k => $v) {
                                    $check_proposta = $k;
                                }
                                $selectP = "SELECT * FROM hospitality_rel_pagamenti_preventivi WHERE id_richiesta = :id_richiesta";
                                $risP    = DB::select($selectP, ['id_richiesta' => $request->id_richiesta]);
                                $rwsP    = sizeof($risP);
                                if ($rwsP > 0) {
                                    $value = $risP[0];
                                    DB::table('hospitality_rel_pagamenti_preventivi')->insert([
                                        'idsito'       => $value->idsito,
                                        'id_richiesta' => $Id_richiesta,
                                        'CC'           => $value->CC,
                                        'BB'           => $value->BB,
                                        'VP'           => $value->VP,
                                        'PP'           => $value->PP,
                                        'GB'           => $value->GB,
                                        'GBVP'         => $value->GBVP,
                                        'GBS'          => $value->GBS,
                                        'linkStripe'   => $value->linkStripe,
                                        'GBNX'         => $value->GBNX,
                                    ]);

                                }

                                $select2 = "SELECT * FROM hospitality_proposte WHERE Id = :Id";
                                $ris2    = DB::select($select2, ['Id' => $check_proposta]);
                                $rws2    = $ris2[0];

                                DB::table('hospitality_proposte')->insert([
                                    'id_richiesta'       => $Id_richiesta,
                                    'Arrivo'             => $rws2->Arrivo,
                                    'Partenza'           => $rws2->Partenza,
                                    'NomeProposta'       => addslashes($rws2->NomeProposta),
                                    'TestoProposta'      => addslashes($rws2->TestoProposta),
                                    'CheckProposta'      => 1,
                                    'PrezzoL'            => $rws2->PrezzoL,
                                    'PrezzoP'            => ($request->NewTotale == '' ? $rws2->PrezzoP : $request->NewTotale),
                                    'AccontoPercentuale' => $rws2->AccontoPercentuale,
                                    'AccontoImporto'     => $rws2->AccontoImporto,
                                    'AccontoTariffa'     => addslashes($rws2->AccontoTariffa),
                                    'AccontoTesto'       => addslashes($rws2->AccontoTesto),
                                ]);

                                $IdProposta = DB::getPdo()->lastInsertId();

                                $selectSC = "SELECT * FROM hospitality_relazione_sconto_proposte WHERE id_richiesta = :id_richiesta AND id_proposta = :id_proposta";
                                $risSC    = DB::select($selectSC, ['id_proposta' => $check_proposta, 'id_richiesta' => $request->id_richiesta]);
                                $rwsSC    = sizeof($risSC);
                                if ($rwsSC > 0) {
                                    $valSC = $risSC[0];
                                    DB::table('hospitality_relazione_sconto_proposte')->insert([
                                        'idsito'       => $valSC->idsito,
                                        'id_richiesta' => $Id_richiesta,
                                        'id_proposta'  => $IdProposta,
                                        'sconto'       => $valSC->sconto,
                                    ]);

                                }
                                ######################################### NUOVO METODO PER SALVARE I SERVIZI AGGIUNTIVI SCELTI LATO UTENTE NELLA LANDING ####################################
                                if ($request->NewTotale == '') {

                                    $select4 = "SELECT * FROM hospitality_relazione_servizi_proposte WHERE id_proposta = :id_proposta";
                                    $ris4    = DB::select($select4, ['id_proposta' => $check_proposta]);
                                    $rws4    = sizeof($ris4);
                                    if ($rws4 > 0) {
                                        foreach ($ris4 as $key => $value) {
                                            DB::table('hospitality_relazione_servizi_proposte')->insert([
                                                'idsito'       => $value->idsito,
                                                'id_richiesta' => $Id_richiesta,
                                                'id_proposta'  => $IdProposta,
                                                'servizio_id'  => $value->servizio_id,
                                                'num_persone'  => $value->num_persone,
                                                'num_notti'    => $value->num_notti,
                                            ]);
                                        }
                                    }

                                } else {

                                    $select4 = "SELECT * FROM hospitality_relazione_servizi_proposte WHERE id_proposta = :id_proposta";
                                    $ris4    = DB::select($select4, ['id_proposta' => $check_proposta]);
                                    $rws4    = sizeof($ris4);
                                    if ($rws4 > 0) {
                                        foreach ($ris4 as $key => $value) {
                                            DB::table('hospitality_relazione_servizi_proposte')->insert([
                                                'idsito'       => $value->idsito,
                                                'id_richiesta' => $Id_richiesta,
                                                'id_proposta'  => $IdProposta,
                                                'servizio_id'  => $value->servizio_id,
                                                'num_persone'  => $value->num_persone,
                                                'num_notti'    => $value->num_notti,
                                            ]);
                                        }
                                    }

                                    $prezzoServizioClone = $request->input('PrezzoServizioClone' . $request->input('NumeroProposta'));
                                    if ($prezzoServizioClone != '') {

                                        $numeroPersone = '';
                                        $NumeroNotti   = '';

                                        foreach ($prezzoServizioClone as $key => $vl) {

                                            $numeroPersone = $request->input('NumeroPersone' . $request->input('NumeroProposta') . '_' . $key);
                                            $NumeroNotti   = $request->input('NumeroNotti' . $request->input('NumeroProposta') . '_' . $key);

                                            DB::table('hospitality_relazione_servizi_proposte')->insert([
                                                'idsito'       => session('IDSITO'),
                                                'id_richiesta' => $Id_richiesta,
                                                'id_proposta'  => $IdProposta,
                                                'servizio_id'  => $key,
                                                'num_persone'  => $numeroPersone,
                                                'num_notti'    => $NumeroNotti,
                                            ]);
                                        }
                                    }

                                }
                                ######################################### ####################################

                                $select3 = "SELECT * FROM hospitality_richiesta WHERE id_proposta = :id_proposta AND TipoSoggiorno != :TipoSoggiorno AND NumeroCamere != :NumeroCamere AND TipoCamere != :TipoCamere AND Prezzo != :Prezzo ORDER BY Id DESC";
                                $ris3    = DB::select($select3, ['id_proposta' => $check_proposta, 'TipoSoggiorno' => '', 'NumeroCamere' => 0, 'TipoCamere' => '', 'Prezzo' => 0]);

                                $num_cam  = 1;
                                $proposta = '';

                                foreach ($ris3 as $key => $rws3) {

                                    DB::table('hospitality_richiesta')->insert([
                                        'id_richiesta'  => $Id_richiesta,
                                        'id_proposta'   => $IdProposta,
                                        'TipoSoggiorno' => $rws3->TipoSoggiorno,
                                        'NumeroCamere'  => $rws3->NumeroCamere,
                                        'TipoCamere'    => $rws3->TipoCamere,
                                        'NumAdulti'     => $rws3->NumAdulti,
                                        'NumBambini'    => $rws3->NumBambini,
                                        'EtaB'          => $rws3->EtaB,
                                        'Prezzo'        => $rws3->Prezzo,
                                    ]);

                                    ######################################### ARRAY UTILE AL CURL PER ANALITICS####################################
/*
                                    $sel3 = "SELECT Id as idCamera,TipoCamere as camera FROM hospitality_tipo_camere WHERE Id = :Id AND idsito = :idsito";
                                    $res3 = DB::select($sel3, ['Id' => $rws3->TipoCamere, 'idsito' => $rws->idsito]);
                                    $rec3 = $res3[0];

                                    $sel4 = "SELECT TipoSoggiorno as soggiorno FROM hospitality_tipo_soggiorno WHERE Id = :Id AND idsito = :idsito";
                                    $res4 = DB::select($sel4, ['Id' => $rws3->TipoSoggiorno, 'idsito' => $rws->idsito]);
                                    $rec4 = $res4[0];

                                    $clean_camera   = str_replace('&', ' ', $rec3->camera);
                                    $array_camere[] = ["item_name" => "quoto - $clean_camera", "quantity" => "1", "price" => $rws3->Prezzo];

                                    $proposta .= '&pr' . $num_cam . 'id=' . $rec3->idCamera . '&pr' . $num_cam . 'nm=QUOTO - ' . str_replace("&", " ", $rec3->camera) . ' - ' . str_replace("&", " ", $rec4->soggiorno) . ' - dal ' . $rws2->Arrivo . ' al ' . $rws2->Partenza . '&pr' . $num_cam . 'ca=' . str_replace("&", " ", $rec3->camera) . ' - ' . str_replace("&", " ", $rec4->soggiorno) . '&pr' . $num_cam . 'qt=' . $rws3->NumeroCamere . '&pr' . $num_cam . 'pr=' . $rws3->Prezzo . '$pr' . $num_cam;
*/
                                    ######################################### ARRAY UTILE AL CURL PER ANALITICS####################################

                                    $num_cam++;
                                }

                                // ##############CURL VERSO ANALYTICS PER IMPUTARE I DATI DI QUOTO IN ANALYTICS##############
                                // Solo se la provenienza è da Sito Web
                               /*
                                if ($rws->FontePrenotazione == 'Sito Web') {

                                    $dati_analytics   = $this->get_account_analytics($rws->idsito);
                                    $AccountAnalytics = $dati_analytics->IdAccountAnalytics;
                                    $measurement_id   = $dati_analytics->measurement_id;
                                    $api_secret       = $dati_analytics->api_secret;

                                    if ($AccountAnalytics != '') {

                                        $select    = "SELECT CLIENT_ID FROM hospitality_client_id WHERE NumeroPrenotazione = :NumeroPrenotazione AND idsito = :idsito";
                                        $result    = DB::select($select, ['NumeroPrenotazione' => $rws->NumeroPrenotazione, 'idsito' => $rws->idsito]);
                                        $record    = $result[0];
                                        $CLIENT_ID = $record->CLIENT_ID;

                                        if ($CLIENT_ID != '') {

          
                                            if ($api_secret != '' && $measurement_id != '') { // solo se i campi measurement_id e api_secret sono compilati

                                                $CLIENT_ID_GA4 = $CLIENT_ID;

                                                $stringa_dati = ["client_id" => $CLIENT_ID_GA4,
                                                    "events"                          => [["name" => "purchase",
                                                        "params"                                                => ["items" => $array_camere,
                                                            "affiliation"                                                            => "quoto",
                                                            "currency"                                                               => "EUR",
                                                            "transaction_id"                                                         => $rws['NumeroPrenotazione'],
                                                            "value"                                                                  => str_replace(",", ".", ($request->NewTotale == '' ? $rws2->PrezzoP : $request->NewTotale))]]],
                                                ];

                                                $data = json_encode($stringa_dati);
                                                $url  = 'https://www.google-analytics.com/mp/collect?api_secret=' . $api_secret . '&measurement_id=' . $measurement_id;
                                                $ch   = curl_init();
                                                curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
                                                curl_setopt($ch, CURLOPT_URL, $url);
                                                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
                                                curl_setopt($ch, CURLOPT_POST, true);
                                                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);
                                                curl_exec($ch);
                                                curl_close($ch);

                                            }

                                        } // fine se account è inserito su suiteweb

                                    } // fine se client id è presente

                                } // fine if solo se la provenienza è da Sito Web
                                */
                                  // ##############CURL VERSO ANALYTICS PER IMPUTARE I DATI DI QUOTO IN ANALYTICS##############

                            } // fine if se è già presente la conferma

                        } // FINE AGGIORNAMWENTO DA PREVENTIVO A CONFERMA

                    }

                } // se la varibaile proposta non è vuota
                $re_url = ''.(!empty(session('TEMPLATE'))?'/'.session('TEMPLATE'):'').'/' . session('DIRECTORY'). '/' . session('PARAM'). '/index?result=' . $risposta;
                return redirect($re_url);

            } else {
                // ritorno alla pagina KO
                $re_url = ''.(!empty(session('TEMPLATE'))?'/'.session('TEMPLATE'):'').'/' . session('DIRECTORY'). '/' . session('PARAM'). '/index';
                return redirect($re_url)->with('captcha', 'Controllo CAPTCHA negativo!');
            }
        } else {
            // ritorno alla pagina KO
            $re_url = ''.(!empty(session('TEMPLATE'))?'/'.session('TEMPLATE'):'').'/' . session('DIRECTORY'). '/' . session('PARAM'). '/index';
            return redirect($re_url)->with('captcha', 'Controllo CAPTCHA mancante, senza il form non viene spedito, contattare amminisratore del sito!');
        } // if recaptcha

    }
    
    /**
     * reg_payment
     *
     * @param  mixed $request
     * @return void
     */
    public function reg_payment(Request $request)
    {
        Log::info('IPN ricevuto da PayPal', $request->all());

        $postdata = $request->all();
        $postdata['cmd'] = '_notify-validate';

        $paypalUrl = app()->environment('production') ?
            'https://ipnpb.paypal.com/cgi-bin/webscr' :
            'https://ipnpb.sandbox.paypal.com/cgi-bin/webscr';

        $response = Http::asForm()->post($paypalUrl, $postdata);

        Log::debug('IPN postdata', $postdata);
        Log::debug('IPN response', ['body' => $response->body()]);

        if (trim($response->body()) === 'VERIFIED') {
            $txn_id         = $request->input('txn_id');
            $payment_status = $request->input('payment_status');
            $item_number    = $request->input('item_number');
            $amount         = $request->input('mc_gross');

            $array_valori = explode('#', $item_number);
            $idsito = $array_valori[1] ?? null;
            $id_richiesta = $array_valori[2] ?? null;

            if (in_array($payment_status, ['Completed', 'Pending']) && $idsito && $id_richiesta) {
                DB::table('hospitality_altri_pagamenti')->updateOrInsert(
                    ['id_richiesta' => $id_richiesta],
                    [
                        'idsito'           => $idsito,
                        'TipoPagamento'    => 'PayPal',
                        'CRO'              => $txn_id,
                        'data_inserimento' => now(),
                    ]
                );
            }

            return response('OK', 200);
        }

        return response('IPN non valido', 400);
    }
    
    /**
     * payway
     *
     * @param  mixed $request
     * @return void
     */
    public function payway(Request $request)
    {
        require(public_path('IGFS_CG_API/init/IgfsCgInit.php'));

        $init = new IgfsCgInit();

        $init->serverURL = $request->serverURL;

        //$init->disableCheckSSLCert();

        $init->timeout = 15000;

        $init->tid = $request->tid;

        $init->kSig = $request->kSig;

        $init->shopID = $request->shopID;

        $init->shopUserRef = $request->shopUserRef;

        $init->trType = "PURCHASE";

        $init->currencyCode = "EUR";

        $init->amount = ($request->amount*100);

        $init->landID = $request->landID;

        $init->notifyURL = $request->url_back;

        $init->errorURL = env('APP_URL'). 'errorpay';

        

        if(!$init->execute()){

            return redirect($init->errorURL."?rc=".urlencode($init->rc)."&errorDesc=".urlencode($init->errorDesc));

        }

            $payment_id = $init->paymentID;

            DB::table()->insert(
                [
                    'idsito'          => $request->IdSito,
                    'id_richiesta'    => $request->IdRichiesta,
                    'TransId'         => $payment_id,
                    'data_operazione' => date('Y-m-d')
                ]
            );

            return redirect($init->redirectURL);
    }

    
    /**
     * virtualpayKO
     *
     * @param  mixed $request
     * @return void
     */
    public function virtualpayKO(Request $request)
    {
        return redirect((!empty(session('TEMPLATE'))?'/'.session('TEMPLATE'):'').'/'.session('DIRECTORY').'/'.session('PARAM').'/index');
    }
        
    /**
     * virtualpayOK
     *
     * @param  mixed $request
     * @return void
     */
    public function virtualpayOK(Request $request)
    {
        if($request->TRANSACTION_ID!='' && ($request->COD_AUT =='TESTOK' || $request->COD_AUT =='OK')) {

            $datiG = Session::get('dati_h_guest', []);
            $idsito       = $datiG->idsito;
            $id_richiesta = $datiG->Id;

            DB::table('hospitality_transazioniId_bcc')->insert(
                                                                    [
                                                                        'idsito'          => $idsito,
                                                                        'id_richiesta'    => $id_richiesta,
                                                                        'TransId'         => $request->TRANSACTION_ID,
                                                                        'data_operazione' => date('Y-m-d')
                                                                    ]
                                                                );

        
        
                $sel = 'SELECT * FROM hospitality_altri_pagamenti WHERE id_richiesta = :id_richiesta';
                $q   = DB::select($sel,['id_richiesta' => $id_richiesta]);
                $rec = sizeof($q);
                
                  if($rec == 0){

                    DB::table('hospitality_altri_pagamenti')->insert(
                                                                        [
                                                                            'idsito'           => $idsito,
                                                                            'id_richiesta'     => $id_richiesta,
                                                                            'TipoPagamento'    => 'Gateway Bancario Virtual Pay',
                                                                            'CRO'              => $request->TRANSACTION_ID,
                                                                            'data_inserimento' => date('Y-m-d')
                                                                        ]
                                                                    );

                  }else{
                    $row = $q[0];
                    if($row->TipoPagamento != 'Gateway Bancario Virtual Pay'){

                        DB::table('hospitality_altri_pagamenti')->where('Id','=',$row->Id)->where('id_richiesta','=',$id_richiesta)->update(
                                                                                                                                            [
                                                                                                                                                'TipoPagamento'    => 'Gateway Bancario Virtual Pay',
                                                                                                                                                'data_inserimento' => date('Y-m-d'),
                                                                                                                                                'CRO'              => $request->TRANSACTION_ID
                                                                                                                                            ]
                                                                                                                                        );

                    }
                  }
        
        
          }
        return redirect((!empty(session('TEMPLATE'))?'/'.session('TEMPLATE'):'').'/'.session('DIRECTORY').'/'.session('PARAM').'/index?result=dmlydHVhbF9wYXk');
    }

    
    /**
     * annullo
     *
     * @param  mixed $request
     * @return void
     */
    public function annullo(Request $request)
    {
        return redirect((!empty(session('TEMPLATE'))?'/'.session('TEMPLATE'):'').'/'.session('DIRECTORY').'/'.session('PARAM').'/index');
    }

    public function esito(Request $request)
    {
        // Decodifica il parametro params per sicurezza
        $decodedParams = base64_decode($request->v);
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

        $nx = "SELECT * FROM hospitality_tipo_pagamenti WHERE idsito = :idsito AND Abilitato = :Abilitato  AND TipoPagamento = :TipoPagamento";
        $res_nx = DB::select($nx,['idsito' => $idsito,'TipoPagamento' => 'Nexi', 'Abilitato' => 1]);
        $row_nx = $res_nx[0]; 
        
        // Chiave segreta 
        $CHIAVESEGRETA = $row_nx->SegretKeyNexi;  // Sostituire con il valore fornito da Nexi

        $requiredParams = ['codTrans', 'esito', 'importo', 'divisa', 'data', 'orario', 'codAut', 'mac'];

        foreach ($requiredParams as $param) {
            if (!request()->has($param)) {
                return response()->json(['error' => 'Parametro mancante: ' . $param], 400);
            }
        }
        
        // Calcolo MAC con i parametri di ritorno
        $macCalculated = sha1(
            'codTrans=' . $request->input('codTrans') .
            'esito=' . $request->input('esito') .
            'importo=' . $request->input('importo') .
            'divisa=' . $request->input('divisa') .
            'data=' . $request->input('data') .
            'orario=' . $request->input('orario') .
            'codAut=' . $request->input('codAut').$CHIAVESEGRETA 
        );
        
        // Verifico corrispondenza tra MAC calcolato e parametro mac di ritorno
        if ($macCalculated !== $request->input('mac')) {
            return response()->json([
                'error' => 'Errore MAC',
                'message' => 'MAC calcolato (' . $macCalculated . ') non corrisponde a quello ricevuto (' . $request->input('mac') . ')'
            ], 400);
        }
        
        // Nel caso in cui non ci siano errori gestisco il parametro esito
        if ($request->esito == 'OK') {

            $requiredParams = ['codTrans', 'esito', 'importo', 'divisa', 'data', 'orario', 'codAut', 'mac'];

            foreach ($requiredParams as $param) {
                if (!request()->has($param)) {
                    return response()->json(['error' => 'Parametro mancante: ' . $param], 400);
                }
            }
              // Calcolo MAC con i parametri di ritorno
              $macCalculated = sha1(
                'codTrans=' . $request->input('codTrans') .
                'esito=' . $request->input('esito') .
                'importo=' . $request->input('importo') .
                'divisa=' . $request->input('divisa') .
                'data=' . $request->input('data') .
                'orario=' . $request->input('orario') .
                'codAut=' . $request->input('codAut').$CHIAVESEGRETA 
            );
        
            // Verifico corrispondenza tra MAC calcolato e parametro mac di ritorno
            if ($macCalculated !== $request->input('mac')) {
                return response()->json([
                    'error' => 'Errore MAC',
                    'message' => 'MAC calcolato (' . $macCalculated . ') non corrisponde a quello ricevuto (' . $request->input('mac') . ')'
                ], 400);
            }
        
            $sel = 'SELECT * FROM hospitality_altri_pagamenti WHERE id_richiesta = :id_richiesta';
            $q   = DB::select($sel,['id_richiesta' => $id_richiesta]);
            $rec = sizeof($q);
            if($rec == 0){
                DB::table('hospitality_altri_pagamenti')->insert(
                                                                    [
                                                                        'idsito'           => $idsito,
                                                                        'id_richiesta'     => $id_richiesta,
                                                                        'TipoPagamento'    => 'Nexi',
                                                                        'CRO'              => $request->codTrans,
                                                                        'data_inserimento' => date('Y-m-d')
                                                                    ]
                                                                );

            }else{
                $row = $q[0];
                if($row->TipoPagamento != 'Nexi'){

                    DB::table('hospitality_altri_pagamenti')->where('Id','=',$row->Id)->where('id_richiesta','=',$id_richiesta)->update(
                                                                                                                                        [
                                                                                                                                            'TipoPagamento'    => 'Nexi',
                                                                                                                                            'data_inserimento' => date('Y-m-d'),
                                                                                                                                            'CRO'              => $request->codTrans
                                                                                                                                        ]
                                                                                                                                    );
                }
            } 
              
            return redirect((!empty(session('TEMPLATE'))?'/'.session('TEMPLATE'):'').'/'.session('DIRECTORY').'/'.session('PARAM').'/index?result=bmV4aQ==');

        } else {
            return 'La transazione ' . $request->codTrans . " è stata rifiutata; descrizione errore: " . $request->messaggio;
        }
    }



    public function user_online($idsito,$id_richiesta)
    {
          $now = mktime(date('h'),0,0,date('m'),date('d'),date('Y'));
      
          $select = "SELECT * FROM hospitality_user_online WHERE idsito = :idsito AND SessionId = :SessionId AND IdRichiesta = :IdRichiesta";
          $result = DB::select($select,['idsito' => $idsito, 'IdRichiesta' => $id_richiesta, 'SessionId' => session_id()]);
          $check  = sizeof($result);
          if($check > 0){
            DB::table('hospitality_user_online')->where('SessionId', '=', session_id())->where('idsito', '=', $idsito)->where('IdRichiesta', '=', $id_richiesta)->update(['online_timestamp' => $now]);
          }else{
            DB::table('hospitality_user_online')->insert(['SessionId' => session_id(),'Idsito' => $idsito,'IdRichiesta' => $id_richiesta, 'online_timestamp' => $now]);
          }      
      }

    public function count($template, $directory, $params)
    {
        if (empty($directory) || empty($params)) {
            abort(404, "Parametri mancanti.");
        }
        // Decodifica il parametro params per sicurezza
        $decodedParams = base64_decode($params, true);
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

        ## FUNZIONE PER CONTROLLARE SU UTENTE ONLINE
        $this->user_online($idsito,$id_richiesta);
        
        DB::table('hospitality_traccia_email')->insert(['IdRichiesta' => $id_richiesta,'Idsito' => $idsito,'DataAzione' => date('Y-m-d H:i:s')]);
             
        return redirect('/'.$template.'/'.$directory.'/'.$params.'/index'); 

    }

    public function count_default($directory, $params)
    {
        if (empty($directory) || empty($params)) {
            abort(404, "Parametri mancanti.");
        }
        // Decodifica il parametro params per sicurezza
        $decodedParams = base64_decode($params, true);
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

        ## FUNZIONE PER CONTROLLARE SU UTENTE ONLINE
        $this->user_online($idsito,$id_richiesta);
        
        DB::table('hospitality_traccia_email')->insert(['IdRichiesta' => $id_richiesta,'Idsito' => $idsito,'DataAzione' => date('Y-m-d H:i:s')]);
             
        return redirect('/'.$directory.'/'.$params.'/index'); 

    }
    
    /**
     * save_questionario
     *
     * @param  mixed $request
     * @return void
     */
    public function save_questionario(Request $request)
    {
        $quest = "SELECT hospitality_domande_lingua.domanda_id
                    FROM hospitality_domande
                        INNER JOIN hospitality_domande_lingua ON hospitality_domande_lingua.domanda_id = hospitality_domande.Id
                    WHERE hospitality_domande.idsito = :idsito
                        AND hospitality_domande_lingua.lingue = :Lingua
                        AND hospitality_domande.Abilitato = :Abilitato
                    ORDER BY hospitality_domande.Ordine ASC";
        $res_q = DB::select($quest,['idsito' => $request->idsito, 'Lingua' => $request->Lingua, 'Abilitato' => 1]);
        foreach($res_q as $Key => $rec){

            DB::table('hospitality_customer_satisfaction')->insert(
                [
                    'id_richiesta'      => $request->id_richiesta,
                    'idsito'            => $request->idsito,
                    'id_domanda'        => $request->input('id_domanda_'.$rec->domanda_id),
                    'risposta'          => addslashes($request->input('risposta_'.$rec->domanda_id)),
                    'recensione'        => $request->input('recensione_'.$rec->domanda_id),
                    'data_compilazione' => $request->data_compilazione
                ]
            );
        }

    }


    /**
     * getCliente
     *
     * @param  mixed $idsito
     * @return void
     */
    public function getCliente($idsito)
    {
        
        $sql = 'SELECT  siti.abilita_mappa,
                            siti.nome,
                            siti.web,
                            siti.https,
                            siti.email,
                            siti.indirizzo,
                            siti.tel,
                            siti.cap,
                            siti.TagManager,
                            siti.IdAccountAnalytics,
                            siti.IdPropertyAnalytics,
                            siti.ViewIdAnalytics,
                            siti.CIR,
                            siti.CIN,
                            comuni.nome_comune,
                            province.nome_provincia,
                            province.sigla_provincia,
                            regioni.nome_regione,
                            users.logo
                    FROM siti
                    INNER JOIN comuni ON comuni.codice_comune = siti.codice_comune
                    INNER JOIN province ON province.codice_provincia = siti.codice_provincia
                    INNER JOIN regioni ON regioni.codice_regione = siti.codice_regione
                    INNER JOIN users ON users.idsito = siti.idsito
                    WHERE siti.idsito = :idsito';
        $rr  = DB::select($sql,['idsito' => $idsito]);
        $row = $rr[0];

        return $row;
    }

    /**
     * tagManager
     *
     * @param  mixed $idsito
     * @param  mixed $TagManager
     * @return void
     */
    public function tagManager($idsito,$TagManager=null)
    {
        $head_tagmanager = '';
        $body_tagmanager = '';
        #TAGMNAGER
        if($TagManager==''){
            $sel_tag        = "SELECT * FROM hospitality_tagmanager WHERE idsito= :idsito LIMIT 1";
            $res_tag        = DB::select($sel_tag,['idsito' => $idsito]);

            if(sizeof($res_tag)>0){

                $rTag           = $res_tag[0];
                $CodeTagManager = $rTag->TagManager;
                if(strlen($CodeTagManager)>3){
            
                    $head_tagmanager ='<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({\'gtm.start\':
                    new Date().getTime(),event:\'gtm.js\'});var f=d.getElementsByTagName(s)[0],
                    j=d.createElement(s),dl=l!=\'dataLayer\'?\'&l=\'+l:\'\';j.async=true;j.src=
                    \'https://www.googletagmanager.com/gtm.js?id=\'+i+dl;f.parentNode.insertBefore(j,f);
                    })(window,document,\'script\',\'dataLayer\',\''.$CodeTagManager.'\');</script>'."\r\n";
            
                    $body_tagmanager ='<noscript><iframe src="https://www.googletagmanager.com/ns.html?id='.$CodeTagManager.'"
                    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>'."\r\n";
                }else{
                    $head_tagmanager ='';
                    $body_tagmanager ='';
                }
            }
        }else{
            if(strlen($TagManager)>3){
                $head_tagmanager ='<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({\'gtm.start\':
                new Date().getTime(),event:\'gtm.js\'});var f=d.getElementsByTagName(s)[0],
                j=d.createElement(s),dl=l!=\'dataLayer\'?\'&l=\'+l:\'\';j.async=true;j.src=
                \'https://www.googletagmanager.com/gtm.js?id=\'+i+dl;f.parentNode.insertBefore(j,f);
                })(window,document,\'script\',\'dataLayer\',\''.$TagManager.'\');</script>'."\r\n";
        
                $body_tagmanager ='<noscript><iframe src="https://www.googletagmanager.com/ns.html?id='.$TagManager.'"
                height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>'."\r\n";

            }else{
                $head_tagmanager ='';
                $body_tagmanager ='';
            }
        }

        return  [$head_tagmanager,$body_tagmanager];
    }
    
   /**
     * dateDiff
     *
     * @param  mixed $data1
     * @param  mixed $data2
     * @param  mixed $formato
     * @return void
     */
    public function dateDiff($data1, $data2, $formato)
    {

        $datetime1 = new DateTime($data1);
        $datetime2 = new DateTime($data2);

        $interval = $datetime1->diff($datetime2);

        return $interval->format($formato);
    }

    /**
    * check_preno_esiste
    *
    * @param  mixed $NumeroPrenotazione
    * @param  mixed $idsito
    * @return void
    */
   public function check_preno_esiste($NumeroPrenotazione, $idsito)
   {
       $select = "SELECT * FROM hospitality_guest WHERE NumeroPrenotazione = :NumeroPrenotazione AND idsito = :idsito AND TipoRichiesta = :TipoRichiesta";
       $ris    = DB::select($select, ['NumeroPrenotazione' => $NumeroPrenotazione, 'idsito' => $idsito, 'TipoRichiesta' => 'Conferma']);
       $check  = sizeof($ris);

       return $check;
   }

 
    /**
     * content_banner
     *
     * @param  mixed $idsito
     * @param  mixed $Lingua
     * @param  mixed $Logo
     * @return void
     */
    public function content_banner($idsito,$Lingua,$Logo)
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
        $tot = sizeof($res);
        if ($tot > 0) {
            $row = $res[0];

            $output = '<link rel="stylesheet" type="text/css" href="/checkin/css/stylebanner_modale.css" />' . "\r\n";
            $output .= '<!-- INIZIO BANNER -->
                            <div id="bannerC">
                                <div class="bannerOC"></div>
                                <img src="/checkin/images/icona.png" class="icona">
                                <a class="white" href="#" data-toggle="modal" data-target="#ANTICOVIDMOD">' . $row->Titolo . '
                                </a>
                            </div>' . "\r\n";
            $output .= '	<script>
                                $(document).scroll(function () {
                                    if ($(window).scrollTop() > 100) {
                                        $("#bannerC").addClass(\'close\');

                                    }
                                });
                                $("#bannerC .bannerOC ").click(function(){
                                    $("#bannerC").toggleClass(\'close\');
                                })
                            </script> ' . "\r\n";

            $output .= '<div class="modal fade align_custom" id="ANTICOVIDMOD" tabindex="-1" role="dialog" aria-hidden="true">
                          <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                              <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                  <span aria-hidden="true" style="color:#000">&times;</span>
                                </button>
                              </div>
                              <div class="modal-body">
                              ' . ($Logo == '' ? '<i class="fa fa-bed fa-5x fa-fw"></i>' : '<img src="' . config('global.settings.BASE_URL_IMG') . 'uploads/loghi/' . $Logo . '" />') . '
                              <br /><br />
                              <titolo>' . $row->Titolo . '</titolo>
                              <sottotitolo>' . $row->Descrizione . '</sottotitolo>
                              </div>
                            </div>
                          </div>
                    ' . "\r\n";
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
                              top: 0;
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
                        </style>' . "\r\n";

        }
        return $output;
    }



    
    /**
     * CheckPrenoChiusa
     *
     * @param  mixed $id
     * @return void
     */
    public function CheckPrenoChiusa($id)
    {

        $select = "SELECT * FROM hospitality_guest WHERE Id = :Id AND Chiuso = :Chiuso";
        $ris    = DB::select($select, ['Id' => $id, 'Chiuso' => 1]);
        $check  = sizeof($ris);
        return $check;
    }
    
    /**
     * getlastid
     *
     * @param  mixed $tabella
     * @return void
     */
    public function getlastid($tabella)
    {

        $qry = DB::select("SELECT MAX(id) as Id FROM $tabella");
        if (sizeof($qry) > 0) {

            $dato = $qry[0];

            $lastid = $dato->Id;

            return ($lastid);
        }
    }
    

    
    /**
     * check_controllo_servizi
     *
     * @param  mixed $idsito
     * @return void
     */
    public function check_controllo_servizi($idsito)
    {

        $sel = "SELECT * FROM hospitality_tipo_servizi_config  WHERE idsito = :idsito AND AbilitatoLatoLandingPage = :AbilitatoLatoLandingPage";
        $res = DB::select($sel, ['idsito' => $idsito, 'AbilitatoLatoLandingPage' => 1]);
        $tot = sizeof($res);

        if ($tot > 0) {
            $output = 1;
        } else {
            $output = 0;
        }

        return $output;
    }
    



    
    /**
     * InformativaPrivacy
     *
     * @param  mixed $idsito
     * @return void
     */
    public function InformativaPrivacy($idsito)
    {
        $informativa =  dizionario('INFORMATIVA_PRIVACY');

        $sql_dati = 'SELECT
                            *,
                            siti.nome as nome_sito
                        FROM
                            siti
                            LEFT JOIN rel_anagra_siti on rel_anagra_siti.idsito = siti.idsito
                            LEFT JOIN anagrafica on anagrafica.idanagra = rel_anagra_siti.idanagra
                            LEFT JOIN comuni on comuni.codice_comune = siti.codice_comune
                            LEFT JOIN province on province.codice_provincia = siti.codice_provincia
                        WHERE
                            siti.idsito = :idsito
                        LIMIT 1';

        $dati_ = DB::select($sql_dati, ['idsito' => $idsito]);
        if (sizeof($dati_) > 0) {
            $dati = $dati_[0];

            $dati->citta = $dati->nome_comune;

            $dati->provincia = $dati->sigla_provincia;

            foreach ($dati as $k => $v) {
                $informativa = str_replace('{!' . $k . '!}', '<b>' . $v . '</b>', $informativa);
            }

            $informativa = str_replace("[struttura]", $dati->nome_sito, $informativa);

            return $informativa;
        }

    }
    
    /**
     * calcola_distanza
     *
     * @param  mixed $latitude1
     * @param  mixed $longitude1
     * @param  mixed $latitude2
     * @param  mixed $longitude2
     * @return void
     */
    public function calcola_distanza($latitude1, $longitude1, $latitude2, $longitude2)
    {
        $kilometers = 0;

        $theta  = $longitude1 - $longitude2;
        $miles  = (sin(@deg2rad($latitude1)) * sin(@deg2rad($latitude2))) + (cos(@deg2rad($latitude1)) * cos(@deg2rad($latitude2)) * cos(@deg2rad($theta)));
        $miles  = acos($miles);
        $miles  = rad2deg($miles);
        $miles  = $miles * 60 * 1.1515;
        $feet   = $miles * 5280;
        $yards  = $feet / 3;
        $km     = $miles * 1.609344;
        $meters = $kilometers * 1000;

        return compact('km');
    }
    
    /**
     * coordinateCliente
     *
     * @param  mixed $idsito
     * @return void
     */
    public function coordinateCliente($idsito)
    {

        $sql = 'SELECT coordinate FROM siti WHERE siti.idsito = :idsito';
        $res = DB::select($sql, ['idsito' => $idsito]);
        if(sizeof($res)>0){
            $rec = $res[0];
            $coordinateAsText = unpack('x/x/x/x/corder/Ltype/dlon/dlat', $rec->coordinate);
            if ($coordinateAsText != '') {
                $LatCliente = $coordinateAsText['lat'];
                $LonCliente = $coordinateAsText['lon'];
            } else {
                $LatCliente = '';
                $LonCliente = '';
            }
        }
        return $LatCliente.'#'.$LonCliente ;
    }

    /**
     * tot_check_pagamento
     *
     * @param  mixed $idsito
     * @param  mixed $id_richiesta
     * @param  mixed $Tpag
     * @return void
     */
    public function tot_check_pagamento($idsito,$id_richiesta,$Tpag)
    {
        $sel = "SELECT * FROM hospitality_tipo_pagamenti WHERE idsito = :idsito AND Abilitato = :Abilitato AND TipoPagamento = :TipoPagamento";
        $res = DB::select($sel,['idsito' => $idsito, 'TipoPagamento' => $Tpag, 'Abilitato' => 1 ]);
        $tot = sizeof($res);

        return $tot;
    }
    /**
     * chek_pagamento_cc
     *
     * @param  mixed $idsito
     * @param  mixed $id_richiesta
     * @return void
     */
    public function chek_pagamento_cc($idsito,$id_richiesta)
    {
        #check di controllo per vedere se è già stato effettuato un pagamento (CC)
        $cc_check = "SELECT * FROM hospitality_carte_credito WHERE idsito= :idsito AND id_richiesta = :id_richiesta";
        $res_cc_check = DB::select($cc_check,['idsito' => $idsito, 'id_richiesta' => $id_richiesta]);
        $tot_cc_check = sizeof($res_cc_check);

        return $tot_cc_check;
    }
    
    /**
     * chek_pagamento_altro
     *
     * @param  mixed $idsito
     * @param  mixed $id_richiesta
     * @return void
     */
    public function chek_pagamento_altro($idsito,$id_richiesta)
    {
         #check di controllo per vedere se è già stato effettuato un pagamento (ALTRI PAGAMENTO)
        $check = "SELECT * FROM hospitality_altri_pagamenti WHERE idsito= :idsito AND id_richiesta = :id_richiesta";
        $res_check = DB::select($check,['idsito' => $idsito, 'id_richiesta' => $id_richiesta]);
        $tot_pag_check = sizeof($res_check);
        if($tot_pag_check > 0){
            $pagament = $res_check[0];
            $TipoPagamento = $pagament->TipoPagamento;
        }else{
            $TipoPagamento = '';
        }
        return array($tot_pag_check,$TipoPagamento);
    }
    
    /**
     * ordine_pagamenti
     *
     * @param  mixed $idsito
     * @param  mixed $TipoPagamento
     * @return void
     */
    public function ordine_pagamenti($idsito,$TipoPagamento)
    {
        $ordine = '';
        $sel = "SELECT * FROM hospitality_tipo_pagamenti WHERE idsito = :idsito AND Abilitato = :Abilitato  AND TipoPagamento = :TipoPagamento";
        $res = DB::select($sel,['idsito' => $idsito, 'TipoPagamento' => $TipoPagamento, 'Abilitato' => 1]);
        $tot = sizeof($res);
        if($tot > 0){
            $row = $res[0];
            $ordine = $row->Ordine;
        }
        return $ordine;
    }

    
    /**
     * ordinamento_pagamenti
     *
     * @param  mixed $idsito
     * @param  mixed $id_richiesta
     * @return void
     */
    public function ordinamento_pagamenti($idsito,$id_richiesta,Request $request)
    {
        $ordinamento_pagamenti = array();

        $selPA = "SELECT * FROM hospitality_rel_pagamenti_preventivi WHERE idsito = :idsito AND id_richiesta = :id_richiesta";
        $risPA = DB::select($selPA,['idsito' => $idsito, 'id_richiesta' => $id_richiesta]);
        $recPA = sizeof($risPA);
        if($recPA > 0){
      
            $recordPA = $risPA[0];

            $VP   = ($recordPA->VP==1 ? $this->vaglia($idsito,session('LINGUA'),$id_richiesta) : ''); 
            $BN   = ($recordPA->BB==1 ? $this->bonifico($idsito,session('LINGUA'),$id_richiesta) : ''); 
            $CC   = ($recordPA->CC==1 ? $this->carta_credito($idsito,session('LINGUA'),$id_richiesta) : ''); 
            $PP   = ($recordPA->PP==1 ? $this->paypal($idsito,session('LINGUA'),$id_richiesta,$request) : ''); 
            $GB   = ($recordPA->GB==1 ? $this->gateway_bancario($idsito,session('LINGUA'),$id_richiesta,$request) : ''); 
            $GBVP = ($recordPA->GBVP==1 ? $this->gateway_bancario_virtualpay($idsito,session('LINGUA'),$id_richiesta,$request) : ''); 
            $SS   = ($recordPA->GBS==1 ? $this->stripe($idsito,session('LINGUA'),$id_richiesta,$request) : ''); 
            $NX   = ($recordPA->GBNX==1 ? $this->nexi($idsito,session('LINGUA'),$id_richiesta,$request) : ''); 

            $OrdineVP   = $this->ordine_pagamenti($idsito,'Vaglia Postale');
            $OrdineBN   = $this->ordine_pagamenti($idsito,'Bonifico Bancario');
            $OrdineCC   = $this->ordine_pagamenti($idsito,'Carta di Credito');
            $OrdinePP   = $this->ordine_pagamenti($idsito,'PayPal');
            $OrdineGB   = $this->ordine_pagamenti($idsito,'Gateway Bancario');
            $OrdineGBVP = $this->ordine_pagamenti($idsito,'Gateway Bancario Virtual Pay');
            $OrdineSS   = $this->ordine_pagamenti($idsito,'Stripe');
            $OrdineNX   = $this->ordine_pagamenti($idsito,'Nexi');

            $ordinamento_pagamenti = array( 
                                            $OrdineVP   => $VP, 
                                            $OrdineBN   => $BN,
                                            $OrdineCC   => $CC, 
                                            $OrdinePP   => $PP, 
                                            $OrdineGB   => $GB, 
                                            $OrdineGBVP => $GBVP, 
                                            $OrdineSS   => $SS, 
                                            $OrdineNX   => $NX
                                        );

            ksort($ordinamento_pagamenti);

        }
        return $ordinamento_pagamenti;
    }

      
    /**
     * immagine_operatore
     *
     * @param  mixed $idsito
     * @param  mixed $Operatore
     * @return void
     */
    public function immagine_operatore($idsito,$Operatore)
    {
        $select = "SELECT img,NomeOperatore FROM hospitality_operatori WHERE  idsito = :idsito AND NomeOperatore = :Operatore AND Abilitato = :Abilitato";
        $q_img = DB::select($select,['idsito' => $idsito, 'Operatore' => $Operatore, 'Abilitato' => 1]);
        if(sizeof($q_img)>0){
            $img   = $q_img[0];
            $ImgOp = $img->img;

            if($img->NomeOperatore == ''){
                $disable = true;
            }else{
                $disable = false;
            }
        }else{
            $disable = false;
            $ImgOp   = '';
        }
        return array($ImgOp,$disable);
    }
    
    /**
     * social
     *
     * @param  mixed $idsito
     * @return void
     */
    public function social($idsito)
    {
        // query per estrarre dati social del cliente
        $sel = "SELECT * FROM hospitality_social WHERE idsito = :idsito";
        $hs = DB::select($sel,['idsito' => $idsito]);
        if(sizeof($hs)>0){
            return $hs[0];
        }else{
            return [];
        }

    }


        /**
     * testiDefault
     *
     * @param  mixed $idsito
     * @param  mixed $IdRichiesta
     * @param  mixed $Lingua
     * @param  mixed $TipoRichiesta
     * @param  mixed $Cliente
     * @return void
     */
    public function testiDefault($idsito,$IdRichiesta,$Lingua,$TipoRichiesta,$Cliente)
    {
        $TestoDefault = '';
        $TestoCustom  = '';

        $select = "SELECT * FROM hospitality_contenuti_web WHERE TipoRichiesta = :TipoRichiesta AND Lingua = :Lingua AND idsito = :idsito AND Abilitato = :Abilitato";
        $q_text = DB::select($select,['TipoRichiesta' => $TipoRichiesta,'Lingua' => $Lingua,'idsito' => $idsito,'Abilitato' => 1]);
        if(sizeof($q_text)>0){
            $rs = $q_text[0];
            $TestoDefault  = str_replace("[cliente]",$Cliente,$rs->Testo);
        }
     
        $select2         = "SELECT * FROM hospitality_contenuti_web_lingua WHERE IdRichiesta = :IdRichiesta AND Lingua = :Lingua AND idsito = :idsito";
        $q_text_alt      =  DB::select($select2,['IdRichiesta' => $IdRichiesta,'Lingua' => $Lingua,'idsito' => $idsito]);
        $check_testo_alt = sizeof($q_text_alt);
        if($check_testo_alt>0){
            $rs_alt = $q_text_alt[0];
            $TestoCustom  = str_replace("[cliente]",$Cliente,$rs_alt->Testo);
        }

        if($check_testo_alt>0 && $rs_alt->Testo!=''){
            $outputTesto = $TestoCustom;
        }else{
            $outputTesto = $TestoDefault;
        }

        return stripslashes($outputTesto);
    }






    

}
