<!DOCTYPE html>
<html lang="<?=$Lingua?>">
	<head>
		<meta charset="utf-8">

		<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0">

        <meta name="description" content="QUOTO!: software CRM per la gestione di preventivi, conferme e prenotazioni in hotel.">
		<!-- Autore e Proprietario intellettuale -->
		<meta name="author" content="Marcello Visigalli">
		<!-- Gestore del Software -->
		<meta name="copyright" content="Network Service srl">
        <!-- Editor usato -->
		<meta name="generator" content="Laravel 10 | editor VsCode">

        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{$NomeCliente}} | {{env('APP_NAME')}}</title>

        @include('/smart/include/inc_libraries.php');

		<style>
		@include('/smart/css/style.php');
		</style>

		<script type="text/javascript" src="https://www.google.com/recaptcha/api.js<?=($Lingua=='it'?'':'?hl='.$Lingua)?>" async defer></script>
		<!-- CHIAVE GOOGLE MAP QUOTO API JAVASCRIPT E DIRECTION-->
		<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCEhD0s4UEJdItPacNMZNLE_aoyLYGAHL8"></script>
		<!-- CHIAVE GOOGLE MAP SITI-->
		<!--<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCEhD0s4UEJdItPacNMZNLE_aoyLYGAHL8"></script>-->
		<?php
		if($abilita_mappa == 1){
			if($latitudine !='' && $longitudine != ''){
		                    echo'<script>
		                                  function init_map() {
		                                        var isDraggable = $(document).width() > 1024 ? true : false;
		                                        var var_location = new google.maps.LatLng('.$latitudine.','.$longitudine.');

		                                                var var_mapoptions = {
		                                                  center: var_location,
		                                                  zoom: 16
		                                                };

		                                        var var_marker = new google.maps.Marker({
		                                        position: var_location,
		                                        map: var_map,
		                                        scrollwheel: false,
		                                        draggable: isDraggable,
		                                        title:"'.$NomeCliente.'"});

		                                        var var_map = new google.maps.Map(document.getElementById("map-container"),
		                                        var_mapoptions);

		                                        var_marker.setMap(var_map);

		                                  }

		                                  google.maps.event.addDomListener(window, \'load\', init_map);

		                            </script>';
			}
		}
		?>

       @if($tot_cc >0)

        <script src="{{ asset('js/jquery.payment.min.js')}}"></script>

        <style type="text/css" media="screen">
            .has-error input {
              border-width: 4px;
              border-color:#FF0000!important;
              border: dotted;

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
		</style>
		<script>
			/* modifiche ai pulsanti di axione, arcihiva, elimina, aggingi alla mailing list, ecc in base alla dimensioni dello schermo */
			function checkScreenDimension(id) {
				var winWidth = $(window).width();
				var winHeight = $(window).height();
				if (winWidth < 800) {
					$("#add-serv").removeClass("nowrap");
					$("#price-serv").removeClass("nowrap");
				}
			}
		</script>

        <script src="{{ asset('smart/js/_alert.js')}}"></script>

        <link href="{{ asset('smart/css/_alert.css')}}" rel="stylesheet" />

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

		        <script language="javascript">_alert("ERROR","{{$mail->ErrorInfo}}")</script>

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
			
		
		@include('/smart/include/inc_MENU.php');

		<div id="start"></div>

		@include('/smart/include/inc_CHAT.php');

		@if($TipoRichiesta == 'Conferma' && $Chiuso == 0)
			@include('/smart/include/inc_PAGAMENTO.php');
		@endif

		@include('/smart/include/inc_PROPOSTE.php');

		@if($TipoRichiesta == 'Preventivo')
			@include('/smart/include/inc_PRENOTA.php');
		@endif

		@include('/smart/include/inc_INFOHOTEL.php');

		@include('/smart/include/inc_DOVESIAMO.php');

		@include('/smart/include/inc_EVENTI.php');

		@include('/smart/include/inc_PUNTI.php');

		@include('/smart/include/inc_PHOTOGALLERY.php');

		@include('/smart/include/inc_CONDIZIONI.php');

		@include('/smart/include/inc_FOOTER.php');

		<script src="{{asset('smart/js/main.js')}}"></script>

		<script src="{{asset('js/responsiveslides.min.js')}}"></script>

    	<link href="{{asset('css/responsiveslides.min.css')}}" rel="stylesheet" />

		<?php echo $content_banner?> 
	</body>
</html>
