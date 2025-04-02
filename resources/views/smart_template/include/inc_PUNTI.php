@if(!empty(PDI))
	<div class="boxquoto" id="punti">
		<div class="box6">
			<h3><?=strtoUpper(dizionario('PDI'))?></h3>
		</div>
		<?php
			$box     ='punti'; //ID del box contenitore
			$frase1  = dizionario('VISUALIZZA').' '.dizionario('PDI');
			$frase2  = dizionario('NASCONDI').' '.dizionario('PDI');
			$bollino ='<i class="fal fa-map-marker"></i>'; //font awesome di riferimento
			$oc      ="0";//1 aperto - 0 chiuso
		?>
		@include('/smart/include/inc_OC.php'); 
		<div class="box6 t14 content">
				<?=$PuntidiInteresse?>
			<div class="ca10"></div>
		
			<div  id="b_map_pdi" style="display:none" >       
				<div class="m m-x-12">
					<a name="start_map_pdi"></a>      
					<a href="javascript:;" id="close_pdi"><i  class="far fa-times-circle fa-2x" aria-hidden="true" style="float:right"></i></a>                    
					<iframe id="frame_lp_pdi"  src="/gmap" frameborder="0" width="100%" height="334px" class="mbr"></iframe>                                                           
				</div>                                                  
			</div>

			<script>
				$(document).ready(function() {
					$("#close_pdi").click(function(){
						$("#b_map_pdi").css("display","none");
					});
				});
			</script>
			<div class="ca10"></div>
		</div> 	
	</div>
@endif