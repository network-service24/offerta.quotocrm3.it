
<div class="boxquoto" id="chat">
	<i class="fas fa-caret-up"></i>
	<div class="box6" style="vertical-align: top">		
            <form id="form_chat" name="form_chat" method="post" >  
             <div class="form">                                                              
                <textarea  rows="10"  name="chat" id="chatmsg" placeholder="<?=dizionario('HOTELCHAT')?>" required></textarea>
             </div>
                  <input type="hidden" name="id_guest" value="<?=$IdRichiesta?>">
                  <input type="hidden" name="NumeroPrenotazione" value="<?=$Nprenotazione?>">
                  <input type="hidden" name="user" value="<?=$Cliente?>">
                  <input type="hidden" name="lang" value="<?=$Lingua?>"> 
                  <input type="hidden" name="idsito" value="<?=$IdSito?>"> 
                  <input type="hidden" name="action" value="add_chat">  
                  <input type="submit" class="pulsante m-x-tc"  id="send_msg" value="<?=dizionario('INVIA')?>" />                                                      

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
	</div>
	
	<?php
		$box='chat'; //ID del box contenitore
		$frase1= dizionario('VISUALIZZA').' '.dizionario('CONVERSAZIONE');
		$frase2= dizionario('NASCONDI').' '.dizionario('CONVERSAZIONE');
		$bollino='<i class="fal fa-comments"></i>'; //font awesome di riferimento
	?>

	@if(str()->contains(request()->getRequestUri(), '/chat'))
		<?php
			$oc="1";//1 aperto - 2 chiuso
			$style = 'class="height600 scroll overflow_auto"';
		?>
	@else
		<?php
			$oc="0";//1 aperto - 2 chiuso
			$style = 'class="height600 scroll overflow_auto"';
		?>
	@endif	

	@include('/smart/include/inc_OC.php'); 
	

	<div class="box6 t14 content" id="discussione" >		
		<!--inizio ciclo conversazione-->
 		<div id="balloon" <?=$style?>></div>
		<!--fine ciclo conversazione-->
		<div class="ca"></div>
	</div>
</div>
<style>
	#chat.scrolled{
		position: fixed;
		top: 55px;
		left: 50%;
		transform: translateX(-93%);
		max-width: 800px;
		width: 100%;
		z-index: 1011;
		box-shadow: 0 0 10px #000;
	}
	#chat.scrolled .bollino{
		display: none;
	}
	#chat .fa-caret-up{
		position: absolute;
		color: #FFF;
		font-size: 30px;
		top: -20px;
		left: 40px;
		display: none;
	}
	#chat.scrolled .fa-caret-up{
		display: block;
	}
	#chat.scrolled textarea{
		font-size: 12px;
	}
	#chat .box6{
		position: relative;
		vertical-align: top;
	}
	#chat .form{
		position: relative;
		width: calc(100% - 160px);
		float: left;
	}
	#chat .form textarea{
		width: 100%;
		margin: 0px;
		height: 60px;

	}
	#chat .pulsante{
		width: 150px;
		float: right;
		height: 60px;
		line-height: 60px;

	}
	#chat .linea{
		border-bottom: 1px dotted <?=$colore2?>;
	}


@media screen and (max-width: 1500px) {
    #chat.scrolled {
        position: relative;
        top: auto;
        left: auto;
        transform: translateX(0);
        max-width: 2000px;
        width: 100%;
        z-index: 1011;
        box-shadow:none;
    }
    #chat.scrolled .bollino {
        display: none;
    }
    #chat .fa-caret-up {
        position: absolute;
        color: #FFF;
        font-size: 30px;
        top: -20px;
        left: 40px;
        display: none;
    }
    #chat.scrolled .fa-caret-up {
        display: block;
    }
    #chat.scrolled textarea {
        font-size: 12px;
    }
}
</style>