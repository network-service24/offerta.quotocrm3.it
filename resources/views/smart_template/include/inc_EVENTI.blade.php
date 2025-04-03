@if($Eventi != '')
<div class="boxquoto" id="eventi">
	<div class="box6">
		<h3><?=strtoUpper(dizionario('EVENTI'))?></h3>
	</div>
	<?php
		$box     ='eventi'; //ID del box contenitore
		$frase1  = dizionario('VISUALIZZA').' '.dizionario('EVENTI');
		$frase2  = dizionario('NASCONDI').' '.dizionario('EVENTI');
		$bollino ='<i class="fal fa-star"></i>'; //font awesome di riferimento
		$oc      ="0";//1 aperto - 0 chiuso
	?>
	@include('smart_template/include/inc_OC')

	<div class="box6 t14 content">
		<?=$Eventi?>
		<div class="ca10"></div>
	
	    <div  id="b_map" style="display:none" >       
		      <div class="m m-x-12">
		          <a name="start_map"></a>      
		          <a href="javascript:;" id="close"><i  class="far fa-times-circle fa-2x" aria-hidden="true" style="float:right"></i></a>                    
		          <iframe id="frame_lp"  src="/gmap" frameborder="0" width="100%" height="334px" class="mbr"></iframe>                                                           
		      </div>                                                  
	   	</div>

		 <script>
		 	$(document).ready(function() {
			  	$("#close").click(function(){
			        $("#b_map").css("display","none");
			     });
		  	});
		  </script>
  		<div class="ca10"></div>
  </div>	
</div>
@endif