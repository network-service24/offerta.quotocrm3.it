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
    <link href="{{ asset('css/'.$FoglioStile.'') }}" rel="stylesheet">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <!-- /.container -->
    <script type="text/javascript" src="https://www.google.com/recaptcha/api.js<?=($Lingua=='it'?'':'?hl='.$Lingua)?>" async defer></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
   <!-- CHIAVE GOOGLE MAP QUOTO API JAVASCRIPT E DIRECTION-->
   <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCEhD0s4UEJdItPacNMZNLE_aoyLYGAHL8"></script>
   <!-- CHIAVE GOOGLE MAP SITI-->
   <!--<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCEhD0s4UEJdItPacNMZNLE_aoyLYGAHL8"></script>-->
    <script>
            $(document).ready(function(){
                $('[data-toogle="tooltip"]').tooltip();
                $('[data-tooltip="tooltip"]').tooltip();
        });
    </script>
    @if($abilita_mappa == 1)
        @if($latitudine !='' && $longitudine != '')
            <script>
                function init_map() {
                    var isDraggable = $(document).width() > 1024 ? true : false;
                    var var_location = new google.maps.LatLng(<?=$latitudine?>,<?=$longitudine?>);

                            var var_mapoptions = {
                                center: var_location,
                                zoom: 16
                            };

                    var var_marker = new google.maps.Marker({
                    position: var_location,
                    map: var_map,
                    scrollwheel: false,
                    draggable: isDraggable,
                    title:"<?=$NomeCliente?>"});

                    var var_map = new google.maps.Map(document.getElementById("map-container"),
                    var_mapoptions);

                    var_marker.setMap(var_map);

                }

                google.maps.event.addDomListener(window, 'load', init_map);

            </script>
        @endif
    @endif
    <script language="javascript" type="text/javascript">

    function check(c) {
        $('.tuaclasse').prop('checked', false);
        $(c).prop('checked', true);
    }
    function check_proposta(n) {
        //$('#proposta'+n).prop('checked', true);
        if(n == 1){
            $("#proposta1").prop("checked",true); 
            $("#proposta2").prop("checked",false);                
            $("#proposta3").prop("checked",false);       
            $("#proposta4").prop("checked",false);     
            $("#proposta5").prop("checked",false);                                 
        }
        if(n == 2){
            $("#proposta2").prop("checked",true); 
            $("#proposta1").prop("checked",false);
            $("#proposta3").prop("checked",false);
            $("#proposta4").prop("checked",false);
            $("#proposta5").prop("checked",false);               
        }
        if(n == 3){    
            $("#proposta3").prop("checked",true);  
            $("#proposta2").prop("checked",false);
            $("#proposta1").prop("checked",false);
            $("#proposta4").prop("checked",false);       
            $("#proposta5").prop("checked",false);
        }
        if(n == 4){ 
            $("#proposta4").prop("checked",true); 
            $("#proposta2").prop("checked",false);
            $("#proposta3").prop("checked",false);                           
            $("#proposta1").prop("checked",false);                            
            $("#proposta5").prop("checked",false);                               
        }
        if(n == 5){ 
            $("#proposta5").prop("checked",true);                          
            $("#proposta2").prop("checked",false);                           
            $("#proposta3").prop("checked",false);                             
            $("#proposta4").prop("checked",false);                             
            $("#proposta1").prop("checked",false);                               
        }
    }
    </script>
    @if($tot_cc >0)
        <script src="{{ asset('js/jquery.payment.min.js') }}"></script>
        <style type="text/css" media="screen">
            .has-error input {
            border-width: 2px;
            }

            .validation.text-danger:after {
            content: 'Validation failed';
            }

            .validation.text-success:after {
            content: 'Validation passed';
            }
        </style>
        <script>
            jQuery(function($) {
                $('.cc-number').payment('formatCardNumber');
                $('.cc-exp').payment('formatCardExpiry');
                $('.cc-cvc').payment('formatCardCVC');

                $.fn.toggleInputError = function(erred) {
                this.parent('.form-g').toggleClass('has-error', erred);
                return this;
                };

                $('.cc-cvc').keyup(function(e) {


                var cardType = $.payment.cardType($('.cc-number').val());
                $('.cc-number').toggleInputError(!$.payment.validateCardNumber($('.cc-number').val()));
                $('.cc-exp').toggleInputError(!$.payment.validateCardExpiry($('.cc-exp').payment('cardExpiryVal')));
                $('.cc-cvc').toggleInputError(!$.payment.validateCardCVC($('.cc-cvc').val(), cardType));
                $('.cc-brand').text(cardType);
                $('#nomecartacc').val(cardType);

                $('.validation').removeClass('text-danger text-success');
                $('.validation').addClass($('.has-error').length ? 'text-danger' : 'text-success');
                if(!$('.has-error').length){
                    $('#bottone_cc').removeAttr('disabled');
                }
                });

            });
        </script>
    @endif
    <style>
    .scroll::-webkit-scrollbar {
        width: 6px;
    }

    .scroll::-webkit-scrollbar-track {
        -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
        border-radius: 4px;
    }

    .scroll::-webkit-scrollbar-thumb {
        border-radius: 4px;
        -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.5);
    }
    .panel-body-warning-mobile {
        font-size: 12px!important;
        line-height: 1.6!important;
        background-color: #ededed!important;
    }
    </style>
    <script>
        /* modifiche ai pulsanti di axione, arcihiva, elimina, aggingi alla mailing list, ecc in base alla dimensioni dello schermo */
        function checkScreenDimension(id) {
            var winWidth = $(window).width();
            var winHeight = $(window).height();
            if (winWidth < 800) {
                $("#TD"+id+"").hide();
                $("tbody tr td.panel-body-warning").each(function(){
                $(this).removeClass("panel-body-warning");
                $(this).addClass("panel-body-warning-mobile");
                });

            }
        }
    </script>
    <script src="{{ asset('js/_alert.js') }}"></script>
    <link href="{{ asset('css/_alert.css') }}" rel="stylesheet" />
    <script>
            window.dataLayer = window.dataLayer || []; 
            dataLayer.push({'event': 'Init', 'NumeroPrenotazione': '<?=$Nprenotazione?>#<?=$idsito?>'});
    </script>
    <?=$head_tagmanager?>
</head>
<body>
<?=$overfade;?>
<div class="fadeMe"></div>
  <?=$body_tagmanager?>
    <!-- Navigation -->
    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
       <div class="container">
          <div class="col-md-3"></div>
          <div class="col-md-9"  id="nav_contenuto">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </div>
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <li>
                        <a href="{{$SitoWeb}}" target="_blank"><i class="fa fa-desktop fa-lg fa-fw text-white"></i> <?=dizionario('VISITA_NOSTRO_SITO')?></a>
                    </li>
                    @if(!$result)
                      <li>
                          <a href="#" id="link_msg" onclick="scroll_to('ancor_chat', 70, 1000);"><i class="fa fa-comments-o fa-lg fa-fw text-white"></i> <?=dizionario('MESSAGGIO_PER_NOI')?></a>
                      </li>
                    @endif
                </ul>
            </div>
            <!-- /.navbar-collapse -->
            </div>
        </div>
        <!-- /.container -->
    </nav>
    <!-- Page Content -->
    <div class="container">
        <div class="row">
            <div class="col-md-3" id="sidebar">
                <p class="lead"><?=($Logo ==''?'<i class="fa fa-bed fa-5x fa-fw"></i>':'<img src="'.config('global.settings.BASE_URL_IMG').'uploads/loghi/'.$Logo.'" />')?></p>
                <div class="list-group">
                    <a href="#" onclick="scroll_to('Proposte', 100, 1000);" class="list-group-item"><i class="fa fa-angle-right" aria-hidden="true"></i> <?=($TipoRichiesta == 'Preventivo'?dizionario('PROPOSTE'):dizionario('SOGGIORNI'))?></a>
                        @if(!empty($Eventi))
                            <a href="#" onclick="scroll_to('Eventi', 50, 1000);" class="list-group-item"><i class="fa fa-angle-right" aria-hidden="true"></i> <?=dizionario('EVENTI')?></a>
                        @endif
                        @if(!empty($PuntidiInteresse))
                            <a href="#" onclick="scroll_to('Pdi', 50, 1000);" class="list-group-item"><i class="fa fa-angle-right" aria-hidden="true"></i> <?=dizionario('PDI')?></a>
                        @endif

                    @if($TipoRichiesta == 'Conferma' && $Chiuso == 0)
                        @if($tot_cc > 0 || $tot_vp > 0 || $tot_bn > 0 )
                            @if($tot_cc_check > 0)
                                <br><span class="text-green"><?=dizionario('DATI_CARTA')?></span><br>
                            @else
                                <a href="#" onclick="scroll_to('ancor_carta', 70, 1000);" class="list-group-item" id="button_carta"><i class="fa fa-angle-right" aria-hidden="true"></i> <?=dizionario('ACCONTO_OFFERTA')?></a>
                            @endif
                        @endif
                    @endif
                </div>            
                @if(!$result)
                   <button href="#" onclick="scroll_to('ancor_chat', 70, 1000);" class="btn btn-primary" id="button_msg"><?=dizionario('CONTATTA_HOTEL')?> <i class="fa fa-angle-double-right" aria-hidden="true"></i></button>
                @endif             
            </div>
            <div class="col-md-9" id="contenuto">
            <!-- FORM MESSAGE -->
            
              @if($result!='' && $result=='0')

                <script language="javascript">_alert("ERROR","Errore generico!")</script>

              @elseif($result!='' && $result=='1') 

                <script language="javascript">_alert("<?dizionario('SOLUZIONECONFERMATA')?>","<?=dizionario('SCELTAPROPOSTA')?>\n\n<?=dizionario('SCELTAPROPOSTA2')?>")</script>

              @elseif($result!='' && $result=='2') 

                <script language="javascript">_alert("<?=dizionario('MESSAGGIO')?>","<?=dizionario('SCELTAPROPOSTAFATTA')?>")</script>

              @elseif($result!='' && base64_decode($result)=='paypal') 

                <script language="javascript">_alert("<?=dizionario('PAGA_PAYPAL')?>","<?=dizionario('MSG_PAYPAL')?>\n\n<?=dizionario('SCELTAPROPOSTA2')?>");</script>

              @elseif($result!='' && base64_decode($result)=='payway') 

                <script language="javascript">_alert("<?=dizionario('PAGA_CARTA_CREDITO')?> PayWay","<?=dizionario('MSG_PAYPAL')?>\n\n<?=dizionario('SCELTAPROPOSTA2')?>");</script>

              @elseif($result!='' && base64_decode($result)=='virtual_pay') 

                <script language="javascript">_alert("<?=dizionario('PAGA_CARTA_CREDITO')?> Virtual Pay","<?=dizionario('MSG_PAYPAL')?>\n\n<?=dizionario('SCELTAPROPOSTA2')?>");</script>

              @elseif($result!='' && base64_decode($result)=='stripe') 

                <script language="javascript">_alert("<?=dizionario('PAGA_CARTA_CREDITO')?> Stripe","<?=dizionario('MSG_STRIPE')?>\n\n<?=dizionario('SCELTAPROPOSTA2')?>");</script>
                
              @elseif($result!='' && base64_decode($result)=='nexi') 

                <script language="javascript">_alert("<?=dizionario('PAGA_NEXI')?>","<?=dizionario('MSG_NEXI')?>\n\n<?=dizionario('SCELTAPROPOSTA2')?>");</script>
             @endif
           
            <?
              if(substr($_SERVER['REQUEST_URI'],-5)=='chat/'){
                $stile='style="display:none"';
              }else{
                    if($TipoRichiesta == 'Conferma'){
                      $stile='style="display:block"';
                   }else{
                      $stile='style="display:none"';
                 }
              }
           ?>
           @if($AccontoRichiesta != 0 || $AccontoLibero != 0 || $AccontoPercentuale != 0 || $AccontoImporto != 0) 
            <div id="ancor_carta"></div>
                <div class="thumbnail caption" id="carta" <?=$stile?>>
                  <div class="row">
                    <div class="col-md-12">
                        <div class="caption-full">
                        <i class="fa fa-times" aria-hidden="true" id="chiudi3"></i>
                        <h1><?=ucfirst($Nome)?> <?=ucfirst($Cognome)?>
                        <small> <i class="fa fa-long-arrow-right fa-sx"></i> <?=dizionario('OFFERTA')?> nr. <?=$NumeroPrenotazione?> <?=dizionario('DEL')?> <?=$DataRichiesta?> </small></h1>
                        @if($AccontoRichiesta != 0 && $AccontoLibero != 0 || $AccontoPercentuale != 0 || $AccontoImporto != 0)
                            <h3><?=dizionario('SCADENZA_OFFERTA')?> <span class="text-red"><?=$DataScadenza?></span></h3>
                        @else
                            <h3><?=dizionario('SCADENZA')?> <?=dizionario('OFFERTA')?> <span class="text-red"><?=$DataScadenza?></span></h3>
                        @endif
                        
                        @if($ordinamento_pagamenti)
                            @foreach ($ordinamento_pagamenti as $chiave_pagamenti => $valore_pagamenti)
                              <?=$valore_pagamenti;?>
                            @endforeach
                        @endif
                     

                        
                     </div>
                  </div>
                </div>
                <!-- FORM CARTA -->
            @endif
            @if (session('captcha'))
              <script language="javascript">alert("<?=session('captcha')?>")</script>
            @endif
            <div id="ancor_msg"></div>
                <div class="thumbnail caption" id="msg" style="display:none">
                  <div class="row">
                    <div class="col-md-12">
                        <div class="caption-full">
                        <i class="fa fa-times" aria-hidden="true" id="chiudi"></i>

                          <form id="form_msg" name="form_msg" method="post" action="/accetta_proposta">
                              <div class="form-group">
                                  <label for="Messaggio"><?=dizionario('MESSAGGIO')?></label>
                                  <textarea class="form-control" rows="3" placeholder="<?=dizionario('MESSAGGIO')?>" name="messaggio" id="messaggio" ><?=$testo_messaggio?></textarea>
                              </div>
                              <div class="form-group">
                                <div class="row">
                                  <div class="col-md-6">
                                  <label for="nome"><?=dizionario('NOME')?></label>
                                    <input type="text" class="form-control" placeholder="Nome" name="nome" id="nome" value="<?=$Nome?>"  readonly>
                                  </div>
                                  <div class="col-md-6">
                                  <label for="cognome"><?=dizionario('COGNOME')?></label>
                                    <input type="text" class="form-control" placeholder="Cognome" name="cognome" id="cognome" value="<?=$Cognome?>"  readonly>
                                  </div>
                                </div>
                              </div>
                              @if($Cellulare =='')
                                <div class="form-group">
                                    <div class="row">
                                    <div class="col-md-6">
                                    <label for="cellulare"><?=dizionario('TELEFONO')?></label>
                                        <input type="text" class="form-control" placeholder="Cellulare e/o telefono" name="Cellulare" id="Cellulare"  required>
                                    </div>
                                    </div>
                                </div>
                              @endif
                              <?=$sistemazione?>
                              <script>
                                    $(document).ready(function() {
                                        $("#button_conf1").click(function(){
                                          if($("#NumeroProposta").val()!=''){
                                            $("#modifiche_serv").show();
                                          }else{
                                            $("#modifiche_serv").hide();
                                          }
                                        });
                                        $("#button_conf2").click(function(){
                                          if($("#NumeroProposta").val()!=''){
                                            $("#modifiche_serv").show();
                                          }else{
                                            $("#modifiche_serv").hide();
                                          }
                                        });
                                        $("#button_conf3").click(function(){
                                          if($("#NumeroProposta").val()!=''){
                                            $("#modifiche_serv").show();
                                          }else{
                                            $("#modifiche_serv").hide();
                                          }
                                        });
                                        $("#button_conf4").click(function(){
                                          if($("#NumeroProposta").val()!=''){
                                            $("#modifiche_serv").show();
                                          }else{
                                            $("#modifiche_serv").hide();
                                          }
                                        });
                                        $("#button_conf5").click(function(){
                                          if($("#NumeroProposta").val()!=''){
                                            $("#modifiche_serv").show();
                                          }else{
                                            $("#modifiche_serv").hide();
                                          }
                                        });
                                    });                              
                              </script>
                             <div style="padding-left:10px!important" id="modifiche_serv">
                                  <label>Dopo le vostre scelte sui servizi aggiuntivi, la proposta N° <input type="text" name="NumeroProposta" id="NumeroProposta" style="background:transparent!important; border:0px!important; width:20px!important"/> ora ha un:</label><br>                                  
                                  <input type="hidden" name="NewTotale" id="NewTotale" />
                                  <div id="TextNewTotale"></div>
                                  <div id="clone"></div>
                              </div>
                              <div class="form-group">
                                  <label for="Messaggio"><?=dizionario('SALUTI')?></label>
                                  <textarea class="form-control" rows="2" placeholder="<?=dizionario('SALUTI')?>" name="saluti" id="saluti" ><?=$testo_saluti?></textarea>
                              </div>
                              <div style="padding-left:10px!important" class="text14">
                              <input name="marketing" id="marketing" type="checkbox" value="1"> <?=dizionario('CONSENSOMARKETING')?><br>
                              <span id="view_profilazione" style="display:none">
                                    <input name="profilazione" id="profilazione" type="checkbox" value="1"> <?=dizionario('CONSENSOPROFILAZIONE')?>
                                    <br>
                                </span>
                                  <input name="policy_soggiorno" id="policy_soggiorno" type="radio" value="1"  required> <?=dizionario('ACCONSENTI_PRIVACY_POLICY_SOGGIORNO')?>
                                  <div id="politiche_soggiorno" style="display:none">
                                    <div class="row">
                                      <div class="col-md-12">
                                        <small>
                                        <br>
                                           <?=$InformativaPrivacy;?>
                                          <br><br><?=$testo?>
                                        </small>
                                      </div>
                                    </div>
                                 </div>
                                   <script>
                                      $(document).ready(function() {
                                            $("#sblocca_politiche").click(function(){
                                                $( "#politiche_soggiorno" ).toggle();
                                            });
                                            $("#marketing").on('click',function() {
                                                $("#view_profilazione").toggle();
                                            });
                                        });
                                  </script>
                              </div>
                              <input type="hidden" name="riferimenti" value="<?=$testo_riferimento?>">
                              <input type="hidden" name="email_hotel" value="<?=$EmailCliente?>">
                              <input type="hidden" name="nome_hotel" value="<?=$NomeCliente?>">
                              <input type="hidden" name="email_utente" value="<?=$Email?>">
                              <input type="hidden" name="nome_utente" value="<?=$Cliente?>">
                              <input type="hidden" name="tipo_richiesta" value="<?=$TipoRichiesta?>">
                              <input type="hidden" name="id_richiesta" value="<?=$Id?>">
                              <input type="hidden" name="t" value="<?=$tipo?>">
                              <input type="hidden" name="idsito" value="<?=session('IDSITO')?>">
                              <input type="hidden" name="lang" value="<?=$Lingua?>">
                              <input type="hidden" name="ip" value="<?=$_SERVER['REMOTE_ADDR']?>">
                              <input type="hidden" name="agent" value="<?=$_SERVER['HTTP_USER_AGENT']?>">
                              <input type="hidden" name="action" value="send_mail">
                              <input type="hidden" name="_token" value="{{csrf_token()}}">
                              @if($check_preno_esiste==0)
                                  <div id="view_form_loading"></div>
                                  <div style="clear:both;height:5px"></div>
                                  <div class="g-recaptcha" data-sitekey="6Lf4WPQUAAAAAMkEu-YZZqebuJwkLa6lEAhkR0kv"></div>
                                  <div style="clear:both;height:5px"></div>
                                  <button type="submit" class="btn btn-primary" id="send_msg"><?=dizionario('INVIA')?> <?=dizionario('MESSAGGIO')?> <i class="fa fa-angle-double-right"></i></button>
                                    <script>
                                            $("#form_msg").on("submit",function(){
                                                $("#view_form_loading").html('<div class="clearfix">&nbsp;</div><div class="row"><div class="col-md-4"></div><div class="col-md-4 text-center"><img src="/img/Ellipsis-1s-200px.gif" alt="Salvataggio in corso"></div><div class="col-md-4"></div></div><div class="clearfix"></div><div class="row"><div class="col-md-4"></div><div class="col-md-4 text-center"><small>Salvataggio in corso..., attendere il termine!</small></div><div class="col-md-4"></div></div><div class="clearfix">&nbsp;</div>');
                                                $("#send_msg").hide();                                           
                                            })
                                    </script>
                              @else
                                    <?php 
                                        switch($Lingua){
                                            case "it":
                                                echo 'Modulo già inviato, scelta già effettuata, richiesta di prenotazione già inviata!';
                                            break;
                                            case "en":
                                                echo 'Form already sent, choice already made, booking request already sent!';
                                            break;
                                            case "fr":
                                                echo 'Formulaire déjà envoyé, choix déjà fait, demande de réservation déjà envoyée!';
                                            break;
                                            case "de":
                                                echo 'Formular bereits gesendet, Auswahl bereits getroffen, Buchungsanfrage bereits gesendet!';
                                            break;
                                        }
                                    ?>       
                            @endif
                          </form>
                        </div>
                     </div>
                  </div>
                </div>
                <!-- FORM MESSAGE -->
                <!-- form chat -->
                <div id="ancor_chat"></div>
                <div id="contenitore_chat" <?=($DataScadenza < date('Y-m-d')?'style="position:relative!important;z-index:999999!important;"':'')?>>
                <div class="thumbnail caption" id="chat" <?=(substr($_SERVER['REQUEST_URI'],-5)=='chat/'?'':'style="display:none"')?>>
                <div class="row">
                            <div class="col-md-12">
                                  <div class="caption-full">
                                      <i class="fa fa-times" aria-hidden="true" id="chiudi2"></i>
                                        <form id="form_chat" name="form_chat" method="post" >
                                              <div class="form-group">
                                                  <label for="Messaggio" class="text-red"><em><?=dizionario('HOTELCHAT')?></em></label>
                                                  <textarea class="form-control" rows="10"  name="chat" id="chatmsg" required></textarea>
                                              </div>
                                              <input type="hidden" name="id_guest" value="<?=$IdRichiesta?>">
                                              <input type="hidden" name="NumeroPrenotazione" value="<?=$Nprenotazione?>">
                                              <input type="hidden" name="user" value="<?=$Cliente?>">
                                              <input type="hidden" name="lang" value="<?=$Lingua?>">
                                              <input type="hidden" name="idsito" value="<?=$idsito?>">
                                              <input type="hidden" name="action" value="add_chat">
                                              <button type="submit" class="btn btn-primary" id="send_msg"><?=dizionario('INVIA')?> <?=dizionario('MESSAGGIO')?> <i class="fa fa-angle-double-right"></i></button>
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
                                                                success: function(res) {
                                                                    $("#chatmsg").val('');
                                                                    print_balloon(<?=$Nprenotazione ?>,<?=$idsito?>);
                                                                }
                                                          });
                                                        return false; 
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
                  </div>
                <!-- form chat -->
                <div class="thumbnail">
                   <?=$TopImage?>
                   <br>
                   <div class="caption-full">
                      <div class="row">
                          <div class="col-md-9">
                            <h1>
                              <em>
                                <?=($TipoRichiesta == 'Preventivo'? dizionario('IL_SUO').' '.dizionario('PREVENTIVO'): dizionario('CONFERMA'))?>  <?=dizionario('DA')?> <?=$NomeCliente?><br>
                                <small class="small_dark"><?=dizionario('OFFERTA')?> nr. <?=$NumeroPrenotazione?> <?=dizionario('DEL')?> <?=$DataRichiesta?> <?=($TipoRichiesta == 'Preventivo'?'<small>&nbsp;&nbsp;'.dizionario('SCADENZA').' <span class="text-red">'.$DataScadenza.'</span></small>': '')?></small>
                              </em>
                            </h1>

                          </div>
                          <div class="col-md-2" id="allineamento">
                              <div><small><b><?=dizionario('CREATA_DA')?></b><br><em><?=($disable==false?$Operatore:'')?></em></small></div>
                          </div>
                          <div class="col-md-1">
                              <div><?=($ImgOp==''?'<img src="/img/receptionists.png" style="width:50px;height:50px" class="img-circle">':'<img src="'.config('global.settings.BASE_URL_IMG').'uploads/'.$idsito.'/'.$ImgOp.'" style="width:50px;height:50px" class="img-circle">')?></div>
                          </div>
                      </div>
                    <br>               
                    @if($Testo != '')
                        <div class="row">
                            <div class="col-md-12">
                                <div class="caption">
                                    <?=$Testo;?>
                                </div>
                            </div>
                        </div>
                    @endif
    
                  <br>
                   <div class="row">
                    <div class="col-md-6">
                      <div class="alert alert-success alert-dismissable">
                        <div class="row">
                            <div class="col-md-4">
                               <div align="center" > <i class="fa fa-calendar fa-5x color_calendar"></i> </div>
                            </div>
                            <div class="col-md-8">
                              <div align="center" class="blu-text-head"><h3><?=dizionario('DATA_ARRIVO')?></h3><h2><?=$DataArrivo?></h2></div>
                            </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-6">
                        <div class="alert alert-success alert-dismissable">
                          <div class="row">
                                <div class="col-md-4">
                                    <div align="center" > <i class="fa fa-calendar fa-5x color_calendar"></i> </div>
                                </div>
                                <div class="col-md-8">
                                  <div align="center" class="blu-text-head"><h3><?=dizionario('DATA_PARTENZA')?></h3><h2><?=$DataPartenza?></h2></div>
                                </div>
                            </div>
                        </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-12"><?=$proposta?></div>
                  </div>
                  <?=$infohotel?>
                     <?=$Eventi?>
                        <div class="row" id="b_map" style="display:none">
                          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                              <a name="start_map"></a>
                              <div><i id="close" class="fa fa-times-circle-o fa-2x" aria-hidden="true" style="cursor:pointer !important"></a></i></div>
                              <iframe id="frame_lp"  src="/gmap" frameborder="0" width="100%" height="334px"></iframe>
                          </div>
                       </div>
                     <script>
                      $("#close").click(function(){
                              $("#b_map").css("display","none");
                          });
                      </script>

                     <?=$PuntidiInteresse?>
                        <div class="row" id="b_map_pdi" style="display:none">
                          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                              <a name="start_map_pdi"></a>
                              <div><i id="close_pdi" class="fa fa-times-circle-o fa-2x" aria-hidden="true" style="cursor:pointer !important"></a></i></div>
                              <iframe id="frame_lp_pdi"  src="/gmap" frameborder="0" width="100%" height="334px"></iframe>
                          </div>
                       </div>
                     <script>
                      $("#close_pdi").click(function(){
                              $("#b_map_pdi").css("display","none");
                          });
                      </script>
                      <?=$carosello?>
                      <?=$Mappa?>
                      <?=$condizioni_generali?>
                </div>
            </div>
        </div>
    </div>
      <script>
        $( document ).ready(function() {
          $("#licenza").click(function(){
            window.open('<?=config('global.settings.url')?>licenza.html','licenza','toolbar=no,scrollbars=no,resizable=no,top=500,left=500,width=400,height=120');
          });
        });
      </script>
    <!-- /.container -->
    <div class="container">
      <footer>
        <div class="row">
          <div class="col-md-3"></div>
          <div class="col-md-9">
            <h3><?=dizionario('ANCORA_DOMANDE')?></h3>
            <button href="#" onclick="scroll_to('ancor_chat', 70, 1000);" class="btn btn-warning" id="button2_footer"><i class="fa fa-comments-o fa-2x"></i> <?=dizionario('SCRIVICI_SE_HAI_BISOGNO')?></button>
             <br><br>
              <div class="row">
                  <div class="col-lg-8 col-md-8">
                   <b class="red-text"><?=$NomeCliente?></b><br> <?=$Indirizzo?> <?=$Localita?> - <?=$Cap?> (<?=$Provincia?>) - <?=$SitoWeb?>
                   <br><?php echo ($CIR!=''?'CIR: '.$CIR:''); ?> <?php echo ($CIN!=''?'CIN: '.$CIN:''); ?>
                  </div>
                    <div class="col-md-4 text-right">
                         <?=$Facebook?>
                         <?=$Twitter?>
                         <?=$GooglePlus?>
                         <?=$Instagram?>
                         <?=$Linkedin?>
                         <?=$Pinterest?>
                    </div>
              </div>
            <hr class="line_white">
            <div class="right copyright"><small>Powered By <img src="/img/logo_quoto.png" style="width:100px">  <a href="https://www.network-service.it" target="_blank">Network Service s.r.l.</a></small> </div>
          </div>
        </div>
      </footer>
    </div>
    <script src="{{ asset('js/responsiveslides.min.js') }}"></script>
    <!-- bxSlider CSS file -->
    <link href="{{ asset('css/responsiveslides.min.css') }}" rel="stylesheet" />
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap-tooltip.min.js') }}"></script>
    <script src="https://use.fontawesome.com/b0c0c4297d.js"></script>
    <link href="{{ asset('css/bootstrap-social.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/ekko-lightbox.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/dark.min.css') }}" rel="stylesheet">
    <script src="{{ asset('js/ekko-lightbox.min.js') }}"></script>
    <script src="{{ asset('js/function.min.js') }}"></script>
    <?php echo $content_banner;?> 
</body>
</html>