<!DOCTYPE html>
<html lang="<?=$Lingua?>">

<head>
    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0">

    <meta name="description" content="QUOTO!: software CRM per la gestione di preventivi, conferme e prenotazioni in hotel.">
    {{-- Autore e Proprietario intellettuale --}}
    <meta name="author" content="Marcello Visigalli">
    {{-- Gestore del Software --}}
    <meta name="copyright" content="Network Service srl">
    {{-- Editor usato --}}
    <meta name="generator" content="Laravel 10 | editor VsCode">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{$NomeCliente}} | {{env('APP_NAME')}}</title>
    {{--BOOTSTRAP--}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    {{--ATTIVO IL TOOLTIP--}}
    {{--JQUERY--}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    {{--FONT AWESOME--}}
    <script src="https://kit.fontawesome.com/bb19c175f4.js" crossorigin="anonymous"></script>
    {{--GOOGLE FONTS--}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,400&display=swap" rel="stylesheet">
    {{-- IMAGEFILL --}}
    <script src="{{asset('v3/js/imagesloaded.4.1.1.min.js')}}"></script>
    <script src="{{asset('v3/js/jquery-imagefill.min.js')}}"></script>
    {{-- FANCY BOX--}}
    <script src="{{asset('v3/fancybox3/jquery.fancybox.min.js')}}"></script>
    <link rel="stylesheet" href="{{asset('v3/fancybox3/jquery.fancybox.min.css')}}" />
    {{--JS--}}
    <script src="{{asset('newTemplate/js/main.js')}}"></script>
    <script src="{{asset('newTemplate/js/_alert.js')}}"></script>
    {{-- CAPTCHA INVISIBLE--}}
    <script type="text/javascript" src="https://www.google.com/recaptcha/api.js<?=($Lingua!= 'it'?'?hl='.$Lingua:'')?>" async defer></script>
    {{-- CHIAVE GOOGLE MAP QUOTO API JAVASCRIPT E DIRECTION--}}
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCEhD0s4UEJdItPacNMZNLE_aoyLYGAHL8"></script>

    @if($abilita_mappa == 1)
        @if($latitudine !='' && $longitudine != '')
            <script>
                function init_map() {
                    var isDraggable = $(document).width() > 1024 ? true : false;
                    var var_location = new google.maps.LatLng(<?php echo $latitudine?>,<?php echo $longitudine?>);

                            var var_mapoptions = {
                                center: var_location,
                                zoom: 16
                            };

                    var var_marker = new google.maps.Marker({
                    position: var_location,
                    map: var_map,
                    scrollwheel: false,
                    draggable: isDraggable,
                    title:"<?php echo $NomeCliente?>"});

                    var var_map = new google.maps.Map(document.getElementById("map-container"),
                    var_mapoptions);

                    var_marker.setMap(var_map);

                }

                google.maps.event.addDomListener(window, 'load', init_map);

            </script>
        @endif
    @endif
    {{--STILE--}}
    <link rel="stylesheet" href="{{asset('newTemplate/css/style.css')}}">
    <link rel="stylesheet" href="{{asset('newTemplate/css/_alert.css')}}">
    <style>
        .slider {
            position: relative;
            background-image: url("<?php echo $imgTop?>");
            background-position: center center;
            background-repeat: no-repeat;
            background-size: cover;
            width: 100%;
            height: 100vh;
        }
    </style>
    <script>
        window.dataLayer = window.dataLayer || []; 
        dataLayer.push({'event': 'Init', 'NumeroPrenotazione': '<?=$Nprenotazione?>#<?=$IdSito?>'});
    </script>
    <?=$head_tagmanager?>
</head>
<body>
    <?=$overfade ?>
    <div class="fadeMe"></div>
    <?=$body_tagmanager?>

    @if($result!='' && $result=='0')

        <script language="javascript">_alert("ERROR","Errore generico email non inviata")</script>

    @elseif($result!='' && $result=='1')

        <script language="javascript">_alert("{{dizionario('SOLUZIONECONFERMATA')}}","{{dizionario('SCELTAPROPOSTA')}}\n\n{{dizionario('SCELTAPROPOSTA2')}}")</script>

    @elseif($result!='' && $result=='2') 

            <script language="javascript">_alert("{{dizionario('MESSAGGIO')}}","{{dizionario('SCELTAPROPOSTAFATTA')}}")</script>

    @elseif($result!='' && base64_decode($result)=='paypal') 

        <script language="javascript">_alert("{{dizionario('PAGA_PAYPAL')}}","{{dizionario('MSG_PAYPAL')}}\n\n{{dizionario('SCELTAPROPOSTA2')}}");</script>

    @elseif($result!='' && base64_decode($result)=='payway') 

        <script language="javascript">_alert("{{dizionario('PAGA_CARTA_CREDITO')}} PayWay","{{dizionario('MSG_PAYPAL')}}\n\n{{dizionario('SCELTAPROPOSTA2')}}");</script>

    @elseif($result!='' && base64_decode($result)=='virtual_pay') 

    <script language="javascript">_alert("{{dizionario('PAGA_CARTA_CREDITO')}} Virtual Pay","{{dizionario('MSG_PAYPAL')}}\n\n{{dizionario('SCELTAPROPOSTA2')}}");</script>

    @elseif($result!='' && base64_decode($result)=='stripe') 

        <script language="javascript">_alert("{{dizionario('PAGA_STRIPE')}}","{{dizionario('MSG_STRIPE')}}\n\n{{dizionario('SCELTAPROPOSTA2')}}");</script>

    @elseif($result!='' && base64_decode($result)=='nexi') 

        <script language="javascript">_alert("{{dizionario('PAGA_NEXI')}}","{{dizionario('MSG_NEXI')}}\n\n{{dizionario('SCELTAPROPOSTA2')}}");</script>
    @endif

    @if(request()->is('*/chat'))
        <script>
                $(document).ready(function () {
                    $("#Chat").modal("show");
                });
            </script>
    @endif
       
    <a name="top"></a>
    {{--PROPOSTE LATERALI--}}
    <div class="proposte-laterali">
        <?php echo $tagProposte;?>
    </div>
    {{--MENU--}}
    <div class="menu container-fluid p-0 m-0">
        <div class="row p-0 m-0">
            <div class="col-4 p-3 m-0">
                <a href="#top">
                    <?=$logoTop?>
                </a>
            </div>
            <div class="col-8 m-0 text-end d-flex align-items-center">
                <div class="d-display-inline w-100 text-end p-3">
                    <a href="" data-bs-toggle="modal" data-bs-target="#Chat"><i class="fa-sharp fa-solid fa-comments chat" data-bs-toggle="tooltip" title="<?=dizionario('MESSAGGIO_PER_NOI')?>"></i></a>
                    <a href="tel:<?=$tel?>" data-bs-toggle="tooltip" title="Phone"><i class="fa-sharp fa-solid fa-phone icona"></i></a>
                    <a href="<?=$SitoWeb?>" target="_blank" data-bs-toggle="tooltip" title="<?=dizionario('VISITA_NOSTRO_SITO')?>"><i class="fa-sharp fa-solid fa-earth-americas icona"></i></a>
                </div>
            </div>
        </div>
    </div>
    {{--INTRO--}}
    <div class="intro container-fluid text-center p-10 p-md-15 p-lg-20 m-0">
        <h1><?=$gentile?> <strong><?=$Nome?> <?=$Cognome?></strong></h1>
        @if($TipoRichiesta=='Conferma' && $Chiuso == 0)
            <a href="#pagamenti">
                <div class="pulsante"><?=dizionario('VISUALIZZA').' '.dizionario('ACCONTO_OFFERTA');?> <i class="fa-thin fa-chevrons-down"></i></div>
            </a>
        @else
            <a href="#proposte">
                <div class="pulsante"><?=dizionario('VISUALIZZA').' '.dizionario('PROPOSTE');?> <i class="fa-thin fa-chevrons-down"></i></div>
            </a>
        @endif
    </div>
    {{--IMMAGINE TOP--}}
    <div class="container-fluid slider min-vh-100 p-0 m-0">
        <div class="row slider">
            <div class="col"></div>
        </div>
    </div>
    <a name="start"></a>
    {{--INDEX--}}
    <div class="container">
        <div class="row">
            <div class="col-12 <?=($infobox != ''?'col-lg-6':'col-lg-12')?> p-5 text-center align-items-center">
            <figure class="text-center">
                {{--RIEPILOGO OFFERTA, SCADENZA, ECC--}}
                <blockquote class="blockquote">
                    <small><strong><?=($TipoRichiesta == 'Preventivo'?dizionario('PREVENTIVO'): dizionario('CONFERMA'))?>  <?=dizionario('DA')?> <?=$NomeCliente?></strong></small>
                </blockquote>
                <figcaption class="blockquote-footer">
                    <small><?=dizionario('OFFERTA')?> nr. <?=$NumeroPrenotazione?> <?=dizionario('DEL')?> <?=$DataRichiesta?> <?=($TipoRichiesta == 'Preventivo'?'&nbsp;&nbsp;'.dizionario('SCADENZA').' '.$DataScadenza.'': '')?></small>
                </figcaption>
            </figure>
            <div class="text-secondary">
                <hr>
            </div>
            {{--TESTO PREVENTIVO O CONFERMA--}}
                <?=$Testo.' <br />'.dizionario('CORDIALMENTE').'<br /> '.$Operatore?>
            </div>
            {{--TAGS--}}
            @if($infobox != '')
                <div class="col-12 col-lg-6 p-5 text-left">
                    <?=$infobox?>
                </div>
            @endif
        </div>
    </div>
    @if($TipoRichiesta=='Conferma' && $Chiuso == 0){
        {{--MODULO DI PAGAMENTO--}}
        <a name="pagamenti"></a>
        <div class="container my-5">
            <div class="row boxcontent m-2">
                <div class="col p-5 text-left">
                    <h4><?=strtoupper(dizionario('ACCONTO_OFFERTA'))?></h4>
                    <p>
                        <?=ucfirst($Nome)?> <?=ucfirst($Cognome)?> <i class="fas fa-long-arrow-alt-right fas-sx"></i> <?=dizionario('OFFERTA')?> nr. <?=$NumeroPrenotazione?> <?=dizionario('DEL')?> <?=$DataRichiesta?>
                        @if($AccontoRichiesta != 0 && $AccontoLibero != 0 || $AccontoPercentuale != 0 || $AccontoImporto != 0)
                            <br><?=dizionario('SCADENZA_OFFERTA')?> <?=$DataScadenza?>
                        @else
                            <br><?=dizionario('SCADENZA')?> <?=dizionario('OFFERTA')?> <?=$DataScadenza?>
                        @endif
                    </p>    
                    <div class="row row-eq-height">           
                        @if($ordinamento_pagamenti)
                            @foreach ($ordinamento_pagamenti as $chiave_pagamenti => $valore_pagamenti)
                                <?php echo $valore_pagamenti;?>
                            @endforeach
                        @endif
                    </div>
                </div> 
            </div>
        </div>
    @endif
    {{--VIDEO--}}
    <?=$streamVideo?>
    {{--PROPOSTE--}}
    <a name="proposte"></a>
    <div class="container">
        <div class="row">
            <div class="col-12 p-5 text-center">
                <h3><?=$titoloProposte?></h3>
            </div>
        </div>
        <div class="row proposte g-0">
            <?php echo $tabProposte;?>
        </div>
        <div class="row boxproposta">
            <?=$proposta?>
        </div>
    </div>
    {{--SERVIZI--}}
    <a name="servizi"></a>
    <div class="container">

            @if($checkServiziInclusi > 0)
                @if($check_preventivo_BOT == false)
                    <?php echo $servInc;?>
               @endif
            @endif
        
        <?php $ck_serv = $check_controllo_servizi; ?>
            @if($ck_serv == 1)
                @if(!empty($serviziFac) && !is_null($serviziFac))
                    <?php echo $servFac;?>
                @endif
            @endif
       
        </div>
    </div>

    {{--CALCOLI--}}
    <div class="container calcoli my-5">
        <div class="row">
            <div class="col-12 col-lg-8 m-2 m-lg-0">
                <div class="row linea">
                    <div class="col-8 sx"><?=$textTotale?> <?=ucfirst(strtolower(dizionario('CAMERE')))?>

                    </div>
                    <div class="col-4 dx totale_camere"></div>
                </div>
                <div class="row linea TS">
                    <div class="col-8 sx"><?=$textTotale?> <?=dizionario('SERVIZI_AGGIUNTIVI')?></div>
                    <div class="col-4 dx totale_servizi"></div>
                </div>
                <div class="row linea SC">
                    <div class="col-8 sx"><?=dizionario('SCONTO')?>: <sc class="sconto"></sc> <i class="fa fa-info-circle" data-bs-toggle="tooltip" title="<?=$textInfoSconto?>"></i></div>
                    <div class="col-4 dx valore_sconto"></div>
                </div>        
                <div class="row linea">
                    <div class="col-8 sx">
                        <strong><?=$textTotale?></strong>
                        <div class="scadenza"><?=dizionario('SCADENZA')?>: <?=$scadenza?></div>
                        </div>
                    <div class="col-4 dx totale"></div>
                </div>
                <div class="row linea caparra">
                    <div class="col-8 sx">
                    <?=dizionario('CAPARRA_RICHIESTA')?> <capa class="percentuale_caparra"></capa>
                        </div>
                    <div class="col-4 dx valore_caparra"></div>
                </div>
            </div>

            <div class="col-12 col-lg-4 m-2 m-lg-0">
                <div class="mirrorbox">
                    <div class="titolo"></div>
                    <div class="richiesta"><?=dizionario('OFFERTA')?> n°: <?=$numero?> del <?=$data?></div>
                    <div class="confermaProposta"></div>
                    <?php if($TipoRichiesta=='Conferma'){?>
                        <div>
                            <span class="arrivo"><?=ucfirst(strtolower(dizionario('ARRIVO')))?>: <?=$Arrivo?></span> - <span class="partenza"><?=ucfirst(strtolower(dizionario('PARTENZA')))?>: <?=$Partenza?></span>
                        </div>
                        <div>
                            <span class="adulti"><?=dizionario('ADULTI')?> <?=$adulti?></span> @if($bambini>0) -  <span class="Bambini"><?=dizionario('BAMBINI')?> <?=$bambini?></span> <span class="Eta"><?=dizionario('ETA')?>: <?=$eta?></span>@endif
                        </div>
                    <?}?>
                    <form id="form_msg" name="form_msg" method="post" action="/accetta_proposta_pro">
                        <div class="formproposta">   
                            <div class="riepilogoProposta"></div>                         
                            <input type="hidden" name="NumeroProposta" id="NumeroProposta" />
                            <input type="hidden" name="NewTotale" id="NewTotale" />
                            <div id="TextNewTotale" style="display:none"></div>
                            <div id="clone"></div>
                        </div> 
                        <input type="hidden" name="messaggio" id="messaggio" value="<?=$testo_messaggio?>">
                        <input type="hidden" name="saluti" id="saluti" value="<?=$testo_saluti?>">
                        <input type="hidden" name="nome" id="nome" value="<?=$Nome?>">
                        <input type="hidden" name="cognome" id="cognome" value="<?=$Cognome?>">
                        <input type="hidden" name="policy_soggiorno" id="policy_soggiorno"  value="1">
                        <input type="hidden" name="profilazione" id="profilazione" value="1">
                        <input type="hidden" name="marketing" id="marketing" value="1">
                        <input type="hidden" name="riferimenti" value="<?=$testo_riferimento?>">
                        <input type="hidden" name="email_hotel" value="<?=$EmailCliente?>">
                        <input type="hidden" name="nome_hotel" value="<?=$NomeCliente?>">
                        <input type="hidden" name="email_utente" value="<?=$Email?>">
                        <input type="hidden" name="nome_utente" value="<?=$Cliente?>">
                        <input type="hidden" name="tipo_richiesta" value="<?=$TipoRichiesta?>">
                        <input type="hidden" name="id_richiesta" value="<?=$id_richiesta?>">
                        <input type="hidden" name="idsito" value="<?=$idsito?>">
                        <input type="hidden" name="lang" value="<?=$Lingua?>">
                        <input type="hidden" name="ip" value="{{ request()->ip() }}">
                        <input type="hidden" name="agent" value="{{ request()->header('User-Agent') }}">
                        <input type="hidden" name="action" value="send_mail">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div id="recaptcha" class="g-recaptcha" data-sitekey="6Lf5BZklAAAAAN9mVy9ob9cKPnmzWhMvGU5hDA2m" data-callback="onCompleted" data-size="invisible"></div>
                        @if($check_preno_esiste == 0)
                            <div id="view_form_loading"></div>
                            <button type="submit" class="pulsante" id="send_msg" style="border:0px !important;"><?=dizionario('CONFERMA').' '.dizionario('PROPOSTA')?> <i class="fa-light fa-badge-check"></i></button>
                            <script>
                                            $("#form_msg").on("submit",function(){
                                                $("#view_form_loading").html('<img src="/img/Ellipsis-1s-200px.svg">');
                                                $("#send_form").hide();                                         
                                            })
                            </script>
                            <script>
                                $(function(){

                                    var formQuoto = document.getElementById("form_msg")
                                    formQuoto.addEventListener("submit", function (event) {
                                        console.log('form inviato.');

                                        if (!grecaptcha.getResponse()) {
                                            console.log('captcha non ancora completato');

                                            event.preventDefault(); //prevent form submit
                                            grecaptcha.execute();
                                        } else {
                                            console.log('form realmente inviato');
                                        }
                                    });

                                    onCompleted = function () {
                                        console.log('captcha completato');
                                        formQuoto.submit();
                                    }
                                })
                                $("#form_msg").on("submit",function(){                                    
                                    $("#send_msg").hide();                                           
                                })
                            </script>
                        @else
                            <div class="clearfix mt-1">
                                <small class="text-secondary-emphasis">
                                    @switch($Lingua)
                                        @case('it')
                                            Modulo già inviato, scelta già effettuata, richiesta di prenotazione già inviata!
                                            @break

                                        @case('en')
                                            Form already sent, choice already made, booking request already sent!
                                            @break

                                        @case('fr')
                                            Formulaire déjà envoyé, choix déjà fait, demande de réservation déjà envoyée!
                                            @break

                                        @case('de')
                                            Formular bereits gesendet, Auswahl bereits getroffen, Buchungsanfrage bereits gesendet!
                                            @break

                                        @default
                                            Lingua non supportata.
                                    @endswitch
                                </small>
                            </div>      
                        @endif
                    </form>
                    <div class="scadenza <?=($TipoRichiesta=='Conferma'?'p-2':'')?>"><?=dizionario('SCADENZA')?> <?=dizionario('OFFERTA')?>:<?=$scadenza?></div>
                    @if($TipoRichiesta=='Preventivo')<a href="#proposte" id="selProp"><div class="pulsante p2"><?=$selezionaAltraProposta?><i class="fa-thin fa-chevrons-up"></i></div></a>@endif
                    <div class="chat" data-bs-toggle="modal" data-bs-target="#Chat" style="cursor: pointer;"><i class="fa-sharp fa-solid fa-comments" data-bs-toggle="tooltip" title="<?=$tooltipChat?>"></i> <?=$fraseChat?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="container my-5">
        {{--INFORMAZIONI HOTEL--}}
        <?=$infoHotel?>
        {{--EVENTI--}}
        <?=$Eventi?>
        {{--PDI--}}
        <?=$puntiInteresse?>  
        {{--CONDIZIONI GENERALI--}}
        <?=$condizioniGenerali?>
    </div>
    {{--GALLERY--}}
    @if (count($gallery) >= 9)
    <div class="container-fluid photogallery p-0 m-0 vh-100">
        <div class="row h-100 p-0 m-0">
            <div class="col-12 col-md-5 h-100 h-md-100">
                <div class="row h-25">           
                    @foreach($gallery as $key => $value)
                        @if($key <= 2)
                            <div class="col-4 fillimg"><img src="<?php echo config('global.settings.BASE_URL_IMG')?>uploads/<?php echo $idsito?>/<?php echo $value->Immagine?>" alt=""></div> 
                        @endif
                    @endforeach
                </div>
                <div class="row h-75">
                    <div class="col-6 fillimg">
                        @foreach($gallery as $key => $value)
                            @if($key == 3)
                                <img src="<?php echo config('global.settings.BASE_URL_IMG')?>uploads/<?php echo $idsito?>/<?php echo $value->Immagine?>" alt="">
                            @endif
                        @endforeach
                    </div>
                    <div class="col-6 fillimg">
                        @foreach($gallery as $key => $value)
                            @if($key == 4)
                                <img src="<?php echo config('global.settings.BASE_URL_IMG')?>uploads/<?php echo $idsito?>/<?php echo $value->Immagine?>" alt="">
                            @endif
                        @endforeach
                     
                        @foreach($gallery as $key => $value)
                            @if($key == 0)
                                    <a href="<?php echo config('global.settings.BASE_URL_IMG')?>uploads/<?php echo $idsito?>/<?php echo $value->Immagine?>" data-fancybox="PG">
                                        <div class="pulsante"><?php echo dizionario('VISUALIZZA')?> Photogallery <i class="fa-thin fa-images"></i></div>
                                    </a> 
                            @else
                                    <a href="<?php echo config('global.settings.BASE_URL_IMG')?>uploads/<?php echo $idsito?>/<?php echo $value->Immagine?>" data-fancybox="PG"></a>
                            @endif
                        @endforeach
                        
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3 h-sm-25 h-md-100 fillimg">
                @foreach($gallery as $key => $value)
                    @if($key == 5)
                        <img src="<?php echo config('global.settings.BASE_URL_IMG')?>uploads/<?php echo $idsito?>/<?php echo $value->Immagine?>" alt=""> 
                    @endif
                @endforeach
            </div>
            <div class="col-12 col-md-4 h-sm-25 h-sm-100">
                <div class="row h-75">
                    @foreach($gallery as $key => $value)
                        @if($key == 6)
                            <div class="col-6 fillimg"><img src="<?php echo config('global.settings.BASE_URL_IMG')?>uploads/<?php echo $idsito?>/<?php echo $value->Immagine?>" alt=""></div> 
                        @endif
                    @endforeach
                    @foreach($gallery as $key => $value)
                        @if($key == 7)
                            <div class="col-6 fillimg"><img src="<?php echo config('global.settings.BASE_URL_IMG')?>uploads/<?php echo $idsito?>/<?php echo $value->Immagine?>" alt=""></div> 
                        @endif
                    @endforeach
                </div>
                <div class="row h-25">
                    @foreach($gallery as $key => $value)
                        @if($key == 8)
                            <div class="col-12 fillimg"><img src="<?php echo config('global.settings.BASE_URL_IMG')?>uploads/<?php echo $idsito?>/<?php echo $value->Immagine?>" alt=""></div> 
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@else
    <div class="border-bottom border-secondary border-3"></div>
@endif
    {{--FOOTER--}}
    <div class="container-fluid footer p-0 m-0">
        <div class="row p-0 m-0">
            <div class="col-12 <?=($Mappa != ''?'col-md-5':'col-md-12')?> p-5 text-center m-0 d-flex align-items-center order-2  order-md-1">
                <div class="d-display-inline w-100">
                    <?=$logoFooter?>
                    <div class="azienda"><?=$NomeCliente?></div>
                    <div class="indirizzo"><?=$Indirizzo?> <?=$Localita?> - <?=$Cap?> (<?=$Provincia?>)</div>
                    <div class="web"><?=$SitoWeb?></div>
                    <div class="email"><?=$EmailCliente?>
                    <?php echo ($CIR!=''?'<br>CIR: '.$CIR:''); ?>
                    <?php echo ($CIN!=''?'<br>CIN: '.$CIN:''); ?></div>
                    {{--SOCIAL--}}
                    <div class="social text-center p-2">
                        <?=$Facebook?> 
                        <?=$Instagram?> 
                        <?=$Twitter?> 
                        <?=$Pinterest?>
                    </div>
                </div>
            </div>
            {{-- GOOGLE MAP --}}
            <?=$Mappa?>           
        </div>
    </div>
    {{-- MODALI --}}
    {{--CHAT--}}
    <div class="modal fade" id="Chat" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-body">
                    <i class="fa-light fa-circle-xmark chiudimodale" class="btn btn-secondary" data-bs-dismiss="modal"></i>
                    <form id="form_chat" name="form_chat" method="post">
                        <div class="form-group">
                            <label for="Messaggio" class="text-red"><em><?=dizionario('HOTELCHAT')?></em></label>
                            <textarea class="form-control" rows="10"  name="chat" id="chatmsg" required></textarea>
                        </div>

                        <div class="form-group text-end p-2 bg-light">
                            <input type="hidden" name="id_guest" value="<?=$id_richiesta?>">
                            <input type="hidden" name="NumeroPrenotazione" value="<?=$Nprenotazione?>">
                            <input type="hidden" name="user" value="<?=$Cliente?>">
                            <input type="hidden" name="lang" value="<?=$Lingua?>">
                            <input type="hidden" name="idsito" value="<?=$idsito?>">
                            <input type="hidden" name="action" value="add_chat">
                            <button type="submit" class="pulsante" style="border:0px !important;"><?=dizionario('INVIA')?> <?=dizionario('MESSAGGIO')?> <i class="fa fa-angle-double-right"></i></button>
                        </div>
                    </form>
                    <script>
                        function print_balloon(Nprenotazione,idsito){
                                $.ajaxSetup({
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    }
                                });
                                $.ajax({
                                    url: "/ballon",
                                    type: "POST",
                                    data: {"Nprenotazione": Nprenotazione,"idsito": idsito},
                                        success: function(response) {
                                            $("#balloon").html(response);
                                        }
                                });
                        }
                        $(document).ready(function() {
                            $("#form_chat").submit(function(){
                                $('#discussione').removeAttr('style');                      
                                var dati = $("#form_chat").serialize();   
                                    $.ajaxSetup({
                                        headers: {
                                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                                }
                                    });                                                         
                                    $.ajax({
                                        url: '/aggiungi_chat',
                                        type: "POST",
                                        data: dati,
                                            success: function(data) {                                                                                                          
                                                $("#chatmsg").val('');
                                                print_balloon(<?=$Nprenotazione ?>,<?=$idsito?>);                                                      
                                            }                                                                    
                                    });                                                               
                                    return false; // con false senza refresh della pagina
                            });                                                                                                                                                                                                                                                                    
                        });
                    </script>  
                    <script>
                        $(document).ready(function() {
                            print_balloon(<?=$Nprenotazione ?>,<?=$idsito?>);   
                        });
                    </script>
                    <br><br><br>
                    <div id="balloon"></div>
                </div>
            </div>
        </div>
    </div>
    @if($Nproposte == 1)
        <script>
            $(function(){
                $(".proposte-laterali").hide();
                $(".proposte").hide();
                $("#selProp").hide();
            })
        </script>
    @endif  
    {{--POWERED BY NETWORK SERVICE--}}
    <a href="https://www.quoto.travel" target="_blank">
        <div class="poweredbynetworkservice">Powered By QUOTO! - Network Service s.r.l</div>
    </a>
    <?=$bannerCovid?> 
    <div id="to-top" title="Torna in alto!"></div>  
</body>

</html>