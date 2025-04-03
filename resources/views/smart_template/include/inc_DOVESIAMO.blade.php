@if($abilita_mappa == 1)
<div class="boxquoto" id="dovesiamo">
	<div class="box6">
		<h3><?=strtoUpper(dizionario('DOVE_SIAMO'))?></h3>
	</div>
	<?php
	$box     ='dovesiamo'; //ID del box contenitore
	$frase1  = dizionario('VISUALIZZA').' '.dizionario('MAPPA');
	$frase2  = dizionario('NASCONDI').' '.dizionario('MAPPA');
	$bollino ='<i class="fal fa-location-arrow"></i>'; //font awesome di riferimento
	$oc      ="0";//1 aperto - 0 chiuso
	?>

@include('smart_template/include/inc_OC')
	
	<div class="box6 t14 content">
		<?=$Mappa?>
	</div>
</div>
@endif