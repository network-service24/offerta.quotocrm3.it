<!DOCTYPE html>
<html lang="<?=$Lingua?>">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta name="description" content="QUOTO!: software CRM per la gestione di preventivi, conferme e prenotazioni in hotel.">
    <!-- Autore e Proprietario intellettuale -->
    <meta name="author" content="Marcello Visigalli">
    <!-- Gestore del Software -->
    <meta name="copyright" content="Network Service srl">
    <!-- Editor usato -->
    <meta name="generator" content="Laravel 10 | editor VsCode">

    <title>{{$NomeCliente}} | {{env('APP_NAME')}}</title>
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/voucher.css')}}" rel="stylesheet">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <!-- /.container -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script src="https://use.fontawesome.com/b0c0c4297d.js"></script>

    <?=$head_tagmanager?>
    <script>
            $(document).ready(function(){
                $('[data-toogle="tooltip"]').tooltip();
                $('[data-tooltip="tooltip"]').tooltip();
        });

    </script>
</head>
<body>
<?=$body_tagmanager?>
<!-- Page Content -->
<div class="container">
    <div class="card">
        <div class="row">
            <div class="col-md-12">

                <div class="row invoice-contact">
                    <div class="col-md-8 col-xs-12 col-sm-12">
                        <div class="invoice-box row">
                            <div class="col-sm-12  pd50">
                                <table class="table table-responsive invoice-table table-borderless">
                                    <tbody>
                                        <tr>
                                            <td style="border:0px!important"><?=($Logo ==''?'<i class="fa fa-bed fa-5x fa-fw"></i>':'<img src="'.config('global.settings.BASE_URL_IMG').'uploads/loghi/'.$Logo.'"  class="logo" />')?></td>
                                        </tr>
                                        <tr>
                                            <td class="nowrap"><?=$NomeCliente?></b></td>
                                        </tr>
                                        <tr>
                                            <td class="nowrap"><?=$Indirizzo?> <?=$Localita?> - <?=$Cap?> (<?=$Provincia?>)</td>
                                        </tr>
                                        <tr>
                                            <td><?=$tel?> - <?=$SitoWeb?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-xs-12 col-sm-12 text-center">
                        <h4><?=dizionario('STAMPA')?></h4>
                        <p><a href="javascript:;" onclick="print();" class="text-smallgray no-print" title="Print"><i class="fa fa-print fa-4x"></i></a></p>
                    </div>
                </div>
                <div class="card-block pd50">
                    <div class="row">
                        <div class="col-md-6 col-xs-12 col-sm-12 invoice-client-info">
                        <h3 class="m-0"><?=$Nome.' '.$Cognome?></h3>
                        <table class="table table-responsive invoice-table invoice-order table-borderless">
                                <tbody>
                                    <tr>
                                        <th class="col-md-6 col-xs-12 col-sm-12 nowrap"><?=dizionario('DATA_ARRIVO')?> :</th>
                                        <td class="col-md-6 col-xs-12 col-sm-12"><?=$DataArrivo?></td>
                                    </tr>
                                    <tr>
                                        <th class="col-md-6 col-xs-12 col-sm-12 nowrap"><?=dizionario('DATA_PARTENZA')?> :</th>
                                        <td class="col-md-6 col-xs-12 col-sm-12"><?=$DataPartenza?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-6 col-sm-12 col-xs-12">
                        <h3 class="m-b-20"><?=dizionario('OFFERTA')?> nr. <?=$NumeroPrenotazione?></span></h6>
                            <table class="table table-responsive invoice-table invoice-order table-borderless">
                                <tbody>
                                    <tr>
                                        <th class="col-md-6 col-xs-12 col-sm-12 nowrap"><?=dizionario('DEL')?> :</th>
                                        <td class="col-md-6 col-xs-12 col-sm-12"><?=$DataRichiesta?></td>
                                    </tr>
                                    <tr>
                                        <th class="col-md-6 col-xs-12 col-sm-12 nowrap">nr :</th>
                                        <td class="col-md-6 col-xs-12 col-sm-12">
                                            <?=$NumeroPrenotazione?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 t16">
                            <?php
                                if(strlen(strip_tags(dizionario('TESTO_VOUCHER_RECUPERO')))> 10){
                                        $testo_buono = str_replace("[cliente]",($Nome.' '.$Cognome),dizionario('TESTO_VOUCHER_RECUPERO'));
                                        $testo_buono = str_replace("[datascadenza]",$DataValiditaVoucher,$testo_buono);
                                        echo $testo_buono;
                                }
                            ?>
                            <div class="capad10top"></div>

                            <?php echo (($NomeProposta!='' || $TestoProposta!='')?'<p>'.$NomeProposta.'</p><p>'.nl2br($TestoProposta).'</p><div class="capad10top"></div>':''); ?>
                            <?php echo '<div><p>'.dizionario('SOGGIORNO_PER_NR_ADULTI').' <b>'.$NumeroAdulti .'</b> - '.($NumeroBambini!='0'?dizionario('NR_BAMBINI').' <b>'.$NumeroBambini .'</b> - '.($EtaBambini1!='0' && $EtaBambini1!=''?$EtaBambini1.' '.dizionario('ANNI').' ':'').($EtaBambini2!='0' && $EtaBambini2!=''?$EtaBambini2.' '.dizionario('ANNI').' ':'').($EtaBambini3!='0' && $EtaBambini3!=''?$EtaBambini3.' '.dizionario('ANNI').' ':'').($EtaBambini4!='0' && $EtaBambini4!=''?$EtaBambini4.' '.dizionario('ANNI').' ':'').($EtaBambini5!='' && $EtaBambini5!='0'?$EtaBambini5.' '.dizionario('ANNI').' ':'').($EtaBambini6!='' && $EtaBambini6!='0'?$EtaBambini6.' '.dizionario('ANNI').' ':'').' ':'').dizionario('NOTTI').' <b>'.$Notti.'</b></p></div><div class="capad10top"></div>'; ?>
                            <?php echo '<p><b>'.dizionario('SOLUZIONECONFERMATA').':</b></p>'. $datealternative.' <div class="capad10top"></div> <p>'.$VAUCHERCamere .'</p> '.($imp_sconto != 0 && $imp_sconto != '' ?($percentuale_sconto?'<small><em>('.$percentuale_sconto.'% '.dizionario('SCONTO').' €. '.number_format(($Prezzo-$ImpSconto),2,',','.').' '.$TOTALE_PREZZO_CAMERE.')</em></small>':''):'').' <div class="capad10top"></div> <p>'.$SERVIZIAGGIUNTIVI.' </p><div class="capad10top"></div>'; ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 text-right pd50r t16">
                        <?php echo '<p>'.(($PrezzoL!='0,00' && $PrezzoL > $PrezzoP)?'Prezzo List. €.<strike>'.$PrezzoL.'</strike> <i class=\'fa fa-angle-right\'></i>':'').'  Prezzo&nbsp;&nbsp;&nbsp;<b>€.'.number_format($PrezzoP,2,',','.').' </b></p><div class="capad10top"></div>';?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 text-right pd50r t16">
                        <?
                                    if($AccontoRichiesta != 0 && $AccontoLibero == 0) {
                                        $saldo   = ($PrezzoP-($PrezzoP*$AccontoRichiesta/100));
                                        echo '<p>'.dizionario('ACCONTO').': '.$AccontoRichiesta.' %  - <b>€. '.number_format(($PrezzoPC*$AccontoRichiesta/100),2,',','.').'</b></p><div class="capad10top"></div>';
                                    }
                                    if($AccontoRichiesta == 0 && $AccontoLibero != 0) {
                                        $saldo   = ($PrezzoP-$AccontoLibero);
                                        echo '<p>'.dizionario('ACCONTO').':  <b>€. '.number_format($AccontoLibero,2,',','.').'</b></p><div class="capad10top"></div>';
                                    }

                                    if($AccontoPercentuale != 0 && $AccontoImporto == 0) {
                                        $saldo   = ($PrezzoP-($PrezzoP*$AccontoPercentuale/100));
                                        echo '<p>'.dizionario('ACCONTO').': '.$AccontoPercentuale.' %  - <b>€. '.number_format(($PrezzoPC*$AccontoPercentuale/100),2,',','.').'</b></p><div class="capad10top"></div>';
                                    }

                                    if($AccontoPercentuale == 0 && $AccontoImporto != 0) {
                                        $saldo   = ($PrezzoP-$AccontoImporto);
                                        if($AccontoImporto >= 1) {
                                            echo '<p>'.dizionario('ACCONTO').':  <b>€. '.number_format($AccontoImporto,2,',','.').'</b></p><div class="capad10top"></div>';
                                        }else{
                                            echo '<p>'.dizionario('CARTACREDITOGARANZIA').'</p><div class="capad10top"></div>';
                                        }
                                    }
                                    if($PrezzoP==$saldo){
                                        $etichetta_saldo = $saldo_text.' <b>€.0,00</b>';
                                    }else{
                                        if($AccontoPercentuale == 0 && $AccontoImporto <= 1) {
                                            $saldo   = $PrezzoPC;
                                        }
                                        $etichetta_saldo = $saldo_text.' <b>€. '.number_format(floatval($saldo),2,',','.').'</b>';
                                    }
                                    echo $etichetta_saldo ;
                                ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                                <?php if($GiaPagatoCC == true || $GiaPagatoPAY == true){?>
                                    <div class="alert alert-warning alert-dismissable text-center">
                                        <h3 class="text-red">
                                        <?
                                            if($AccontoRichiesta != 0 && $AccontoLibero == 0) {
                                                echo '<p>€. '.number_format(($PrezzoPC*$AccontoRichiesta/100),2,',','.').'</p>';
                                            }
                                            if($AccontoRichiesta == 0 && $AccontoLibero != 0) {
                                                echo '<p>€. '.number_format($AccontoLibero,2,',','.').'</p>';
                                            }

                                            if($AccontoPercentuale != 0 && $AccontoImporto == 0) {
                                                echo '<p>€. '.number_format(($PrezzoPC*$AccontoPercentuale/100),2,',','.').'</p>';
                                            }

                                            if($AccontoPercentuale == 0 && $AccontoImporto != 0) {
                                                if($AccontoImporto >= 1) {
                                                    echo '<p>€. '.number_format($AccontoImporto,2,',','.').'</p>';
                                                }else{
                                                    echo '<p>'.dizionario('CARTACREDITOGARANZIA').'</p>';
                                                }
                                            }

                                        ?>
                                        </h3>
                                        <?php
                                            $frase = str_replace("[tipopagamento]",$TipologiaPagamento,dizionario('FRASE_RECUPERO_CAPARRA'));
                                            $frase = str_replace("[datavalidita]",$DataValiditaVoucher,$frase);
                                            echo $frase;
                                        ?>                                    
                                    </div>
                                <?}?>                           
                        </div>
                    </div>
                    <div style="clear:both"></div>
                    <div class="row" style="padding-bottom:20px">
                    <div class="col-md-12 tfooter"><em><b>Legenda:</b> <i class="fa fa-user" style="padding-left:10px;padding-right:10px"></i> Servizio scelto dal cliente in fase di conferma <br><small style="padding-left:100px;">(Service chosen by the customer during the confirmation phase)</small></em></div>
                    </div>
                    <div style="clear:both"></div>
                    <div class="row  pd50bottom">
                        <div class="col-sm-12 tfooter">
                                <h4><?=dizionario('CONDIZIONI_GENERALI')?></h4>
                                <?=$testo_condizioni_generali?>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12 text-center">
        <a href="javascript:;" onclick="print();" class="text-smallgray no-print" title="Print"><i class="fa fa-print fa-4x"></i></a>
    </div>
</div>
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8 text-right">
        <small>Powered By <img src="<?=url('/img/logo_quoto.png')?>" style="width:100px"> Network Service s.r.l.</small>
    </div>
    <div class="col-md-2"></div>
</div>
</body>
</html>
