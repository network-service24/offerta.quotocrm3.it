@if($tot_info>0)
<div class="boxquoto" id="infohotel">
	<div class="box6">
		<h3><?=strtoupper($infohotel)?></h3>
	</div>
	<?php
		$box     ='infohotel'; //ID del box contenitore
		$frase1  = dizionario('VISUALIZZA').' '. $infohotel;
		$frase2  = dizionario('NASCONDI').' '.$infohotel;
		$bollino ='<i class="fal fa-info-circle"></i>'; //font awesome di riferimento
		$oc      ="0";//1 aperto - 0 chiuso
	?>
	@include('smart_template/include/inc_OC')
	<div class="box6 t14 content">
		<?=$infohotelTesto?>
		<div class="ca20"></div>
	</div>
</div>
@endif