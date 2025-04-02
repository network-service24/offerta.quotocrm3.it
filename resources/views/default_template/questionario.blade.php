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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script> 
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script src="https://use.fontawesome.com/b0c0c4297d.js"></script>
    <link href="{{ asset('css/bootstrap-social.min.css') }}" rel="stylesheet">
    <script src="{{ asset('js/function.min.js') }}"></script>
    <script>
            $(document).ready(function(){
                $('[data-toogle="tooltip"]').tooltip();
                $('[data-tooltip="tooltip"]').tooltip();
        });
    </script>
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
    <script src="{{ asset('js/_alert.js') }}"></script>
    <link href="{{ asset('css/_alert.css') }}" rel="stylesheet" />
<?=$head_tagmanager?>
</head>
<body>
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
                        <a href="<?=$SitoWeb?>" target="_blank"><i class="fa fa-desktop fa-lg fa-fw text-white"></i> <?=dizionario('VISITA_NOSTRO_SITO')?></a>
                    </li>
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

            </div>
            <div class="col-md-9" id="contenuto">

                <div class="thumbnail">
                   <div class="caption-full">
                      <div class="row">
                          <div class="col-md-12">
                            <h1> <?=dizionario('QUESTIONARIO')?></h1>                 
                          </div>                        
                      </div>
                      <br>
                      <div class="row">
                          <div class="col-md-12">
                            <div class="caption">
                              <?=str_replace("[cliente]",($Nome.' '.$Cognome),dizionario('TESTO_QUESTIONARIO'))?>
                            </div>
                         </div>
                     </div> 
                      <br>
                      <? if($tot_cs > 0){?>
                          <div class="row">
                          <div class="col-md-12">
                            <?=dizionario('NO_QUESTIONARIO')?>
                         </div>
                     </div>
                      <?}else{?>
                         <form id="form_quest" name="form_quest" method="post" onsubmit="return controlla();">                                           
                           <?=$question?>
                           <br>
                            <div class="row">
                              <div class="col-md-12">
                                   <input type="hidden" name="email_hotel" value="<?=$EmailCliente?>">
                                  <input type="hidden" name="nome_hotel" value="<?=$NomeCliente?>"> 
                                  <input type="hidden" name="email_utente" value="<?=$Email?>">
                                  <input type="hidden" name="nome_utente" value="<?=$Cliente?>">
                                  <input type="hidden" name="id_richiesta" value="<?=$id_richiesta?>">
                                  <input type="hidden" name="idsito" value="<?=$idsito?>"> 
                                  <input type="hidden" name="Lingua" value="<?=$Lingua?>"> 
                                  <input type="hidden" name="data_compilazione" value="<?=date('Y-m-d')?>">
                                  <input type="hidden" name="action" value="send_quest">                                                             
                                  <button type="submit" class="btn btn-primary" id="send_msg"><?=dizionario('INVIA_GIUDIZI')?> <i class="fa fa-angle-double-right"></i></button>  
                              </div> 
                            </div>                      
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
        </div>
    </div>

      <script>
        $( document ).ready(function() {
          $("#licenza").click(function(){
            window.open('<?php echo env('APP_NAME')?>licenza.html','licenza','toolbar=no,scrollbars=no,resizable=no,top=500,left=500,width=400,height=120');
          });
        });
      </script>
  </div> <!-- /.container -->

    <!-- /.container -->
    <div class="container">
      <footer>
        <div class="row">
          <div class="col-md-3"></div>
          <div class="col-md-9">
              <div class="row">
                  <div class="col-lg-8 col-md-8">
                   <b class="red-text"><?=$NomeCliente?></b><br> <?=$Indirizzo?> <?=$Localita?> - <?=$Cap?> (<?=$Provincia?>) - <?=$SitoWeb?>
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
            <div class="right copyright"><small>Copyright <span id="licenza">&copy;</span> <?=date('Y')?> <a href="https://www.network-service.it">Network Service s.r.l.</a></small> </div>
          </div>
        </div>
      </footer>           
    </div>
</body>
</html>