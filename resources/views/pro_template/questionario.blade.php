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
    <?=$head_tagmanager?>
</head>
<body>
    <?=$body_tagmanager?>
    <a name="top"></a>
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
        <a href="#quest">
            <div class="pulsante"><?=dizionario('VISUALIZZA').' '.dizionario('QUESTIONARIO')?> <i class="fa-thin fa-chevrons-down"></i></div>
        </a>
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
            <div class="col-12 col-lg-12 p-5 text-center align-items-center">
            <span class="testoQuestionario"><?=str_replace("[cliente]",($Nome.' '.$Cognome),dizionario('TESTO_QUESTIONARIO'))?></span>
                      <div class="ca50"></div>
                        <? if($tot_cs > 0){?>
                              <span class="t30"><?=dizionario('NO_QUESTIONARIO')?></span>
                        <?}else{?>
                            <p>
                            LEGENDA:    <img src="{{config('global.settings.BASE_URL_IMG')}}img/emoji/bad.png" style="width:20px;height:20px" data-toogle="tooltip" title="Bad [valore = 1]">(1)
                                        <img src="{{config('global.settings.BASE_URL_IMG')}}img/emoji/semi_bad.png" style="width:20px;height:20px" data-toogle="tooltip" title="Semi Bad  [valore = 2]">(2)
                                        <img src="{{config('global.settings.BASE_URL_IMG')}}img/emoji/medium.png" style="width:20px;height:20px" data-toogle="tooltip" title="Medium  [valore = 3]">(3)
                                        <img src="{{config('global.settings.BASE_URL_IMG')}}img/emoji/semi_good.png" style="width:20px;height:20px" data-toogle="tooltip" title="Semi Good  [valore = 4]">(4)
                                        <img src="{{config('global.settings.BASE_URL_IMG')}}img/emoji/good.png" style="width:20px;height:20px" data-toogle="tooltip" title="Good  [valore = 5]">(5)

                          </p>
                          <div class="ca50"></div>
                          <a name="quest"></a>
                          <script language="javascript">
                                function controlla()
                                {          
                                var d = window.document.reg
                                var title_error = "ERRORE! Impossibile inviare il modulo! \n\n"
                                var error = ""

                                <?=$valori_ctrl_script?>

                                    if (error) {
                                        alert(title_error + error); 
                                        return(false);
                                    } else {
                                        return(true);
                                    }                   
                                }
                            </script>
                           <form id="form_quest" name="form_quest" method="post">                                           
                             <?=$question?>
                             <div class="ca20"></div>
                             <input type="hidden" name="email_hotel" value="<?=$EmailCliente?>">
                                    <input type="hidden" name="nome_hotel" value="<?=$NomeCliente?>"> 
                                    <input type="hidden" name="email_utente" value="<?=$Email?>">
                                    <input type="hidden" name="nome_utente" value="<?=$Cliente?>">
                                    <input type="hidden" name="id_richiesta" value="<?=$id_richiesta?>">
                                    <input type="hidden" name="idsito" value="<?=$idsito?>"> 
                                    <input type="hidden" name="Lingua" value="<?=$Lingua?>"> 
                                    <input type="hidden" name="data_compilazione" value="<?=date('Y-m-d')?>">
                                    <input type="hidden" name="action" value="send_quest">                                                             
                                    <button type="submit" class="pulsante p-2 noBorder" id="send_msg" onclick="return controlla();"><?=dizionario('INVIA_GIUDIZI')?></button>                      
                          </form> 
                          <script>
                                $(document).ready(function() {
                                    $("#form_quest").submit(function () {   
                                        var  dati   = $("#form_quest").serialize(); 
                                        $.ajaxSetup({
                                            headers: {
                                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                            }
                                        });
                                        $.ajax({
                                            url: "/save_questionario",
                                            type: "POST",
                                            data: dati,
                                            success: function(response) {
                                                _alert("OK!","<?php echo dizionario('THANKS_QUESTIONARIO')?>");
                                                setTimeout(function() { 
                                                    window.location.href = "<?php echo $SitoWeb?>";
                                                }, 2000);                 
                                            }
                                        });
                                        return false;                                     
                                    });
                                });
                            </script>
                      <?}?>
        </div>
    </div>
   
    {{--FOOTER--}}
    <div class="container-fluid footer p-0 m-0">
        <div class="row p-0 m-0">
            <div class="col-12 col-md-12 p-5 text-center m-0 d-flex align-items-center order-2  order-md-1">
                <div class="d-display-inline w-100">
                    <?=$logoFooter?>
                    <div class="azienda"><?=$NomeCliente?></div>
                    <div class="indirizzo"><?=$Indirizzo?> <?=$Localita?> - <?=$Cap?> (<?=$Provincia?>)</div>
                    <div class="web"><?=$SitoWeb?></div>
                    <div class="email"><?=$EmailCliente?></div>
                    {{--SOCIAL--}}
                    <div class="social text-center p-2">
                        <?=$Facebook?> 
                        <?=$Instagram?> 
                        <?=$Twitter?> 
                        <?=$Pinterest?>
                    </div>
                </div>
            </div>      
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
                            <button type="submit" class="btn btn-primary"><?=dizionario('INVIA')?> <?=dizionario('MESSAGGIO')?> <i class="fa fa-angle-double-right"></i></button>
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
                                    url: "/ballon_smart",
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
 
    {{--POWERED BY NETWORK SERVICE--}}
    <a href="https://www.quoto.travel" target="_blank">
        <div class="poweredbynetworkservice">Powered By QUOTO! - Network Service s.r.l</div>
    </a>
    <div id="to-top" title="Torna in alto!"></div>  
</body>

</html>