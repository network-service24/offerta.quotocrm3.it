<div class="boxquoto" id="condizioni">
    <div class="box6">
        <h3><?=strtoUpper(dizionario('CONDIZIONI_GENERALI'))?></h3>
    </div>
    <?php
        $box     ='condizioni'; //ID del box contenitore
        $frase1  = dizionario('VISUALIZZA').' '.dizionario('CONDIZIONI');
        $frase2  = dizionario('NASCONDI').' '.dizionario('CONDIZIONI');
        $bollino ='<i class="fal fa-question-circle"></i>'; //font awesome di riferimento
        $oc      ="0";//1 aperto - 0 chiuso
      
	?>
    @include('/smart/include/inc_OC.php'); 
    
        <div class="box6 t14 content">
            <?=$condizioni_generali?>
        </div>
</div>