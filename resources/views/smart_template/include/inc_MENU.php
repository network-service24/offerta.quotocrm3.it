<div id="menu">
	<div class="menu">
		<?=($Logo ==''?'<i class="fas fa-bed fa-5x fa-fw"></i>':'<img src="'.config('global.settings.BASE_URL_IMG').'uploads/loghi/'.$Logo.'" class="logo">')?>
		@if(!$result)
				<div class="vm" section="chat"><?=dizionario('MESSAGGIO_PER_NOI')?></div>
		@endif
		@if(!empty($Eventi))
				<div class="vm" section="eventi"><?=dizionario('EVENTI')?></div>
		@endif
        @if(!empty($PDI))
	            <div class="vm" section="punti"><?=dizionario('PDI')?></div>
		@endif       				
		@if($TipoRichiesta == 'Conferma')
            <div class="vm" section="pagamento"><?=dizionario('ACCONTO_OFFERTA')?></div>
		@endif		
		<div class="vm" section="photogallery">Photogallery</div>
		<div class="vm" section="condizioni"><?=dizionario('CONDIZIONI')?></div>
		<div class="vm"><a href="<?=$SitoWeb?>" target="_blank"><?=dizionario('VISITA_NOSTRO_SITO')?></a></div>
		<div class="vm telefono"><i class="fal fa-phone-volume"></i> <?=$telefono?></div>
		@if(!$result)
			<div class="vm preno" section="proposte"><i class="fal fa-check"></i> <?=($TipoRichiesta == 'Preventivo'?dizionario('PROPOSTE'):dizionario('SOGGIORNI'))?></div>
		@endif
	</div>
</div>
<div id="menumb">
	<div class="hamburger"><i class="fal fa-bars"></i></div>
	<div class="closex"><i class="fal fa-times"></i></div>
	<div class="intestazione">
		<div class="riga1"><?=$intestazionemobile1?></div>
		<div class="riga2"><?=$intestazionemobile2?></div>
	</div>
	<div class="menumb">
		@if(!$result)
			<div class="vmb" section="chat"><?=dizionario('MESSAGGIO_PER_NOI')?></div>
		@endif
		@if(!empty($Eventi))
			<div class="vmb" section="eventi"><?=dizionario('EVENTI')?></div>
		@endif
		@if(!empty($PDI))
	        <div class="vmb" section="punti"><?=dizionario('PDI')?></div>
		@endif
		@if($TipoRichiesta == 'Conferma')
            <div class="vmb" section="pagamento"><?=dizionario('ACCONTO_OFFERTA')?></div>
        @endif       
		<div class="vmb" section="photogallery">Photogallery</div>
		<div class="vmb" section="condizioni"><?=dizionario('CONDIZIONI')?></div>
		<div class="vmb"><a href="<?=$SitoWeb?>" target="_blank"><?=dizionario('VISITA_NOSTRO_SITO')?></a></div>
		<div class="vmb telefono"><i class="fal fa-phone-volume"></i> <?=$telefono?></div>
		@if(!$result)
				<div class="vmb preno" section="proposte"><i class="fal fa-check"></i> <?=($TipoRichiesta == 'Preventivo'?dizionario('PROPOSTE'):dizionario('SOGGIORNI'))?></div>
		@endif
	</div>
</div>
<style>
	#menumb{
		position: fixed;
		padding: 15px;
		z-index: 2000;
		top: 0;
		left: 0;
		background-color: #FFF;
		border-bottom: 2px solid <?=$colore1?>;
		box-shadow: 0 0 10px #000;
		width: 100%;
		height: 60px;
		overflow: hidden;
		display: none ;
	}
	#menumb .hamburger{
		font-size: 28px;
		color: <?=$colore1?>;
		cursor: pointer;
		position: absolute;
		left: 15px;
		top: 50%;
		transform: translateY(-50%);		
	}
	#menumb .closex{
		font-size: 28px;
		color: <?=$colore1?>;
		cursor: pointer;
		position: absolute;
		left: 15px;
		top: 15px;
		display: none;	
	}
	#menumb .intestazione{
		font-size: 16px;
		color: <?=$colore1?>;
		cursor: pointer;
		position: absolute;
		right: 15px;
		top: 50%;
		transform: translateY(-50%);
		text-align: right;	
	}
	#menumb .intestazione .riga1{
		font-size: 16px;
		font-weight: 300;
		text-transform: uppercase;
	}
	#menumb .intestazione .riga2{
		font-size: 14px;
		font-weight: 700;
	}
	#menumb  .menumb{
		max-height: 0px;
		border-top: 2px solid <?=$colore1?>;
		padding: 15px 0;
		margin-top: 50px;
	}
	#menumb  .vmb{
		cursor: pointer;
		font-size: 18px;
	    padding: 0px 20px;
	    height: 30px;
	    line-height: 30px;
		border-radius: 5px;
		margin-top: 2px;
	}
	#menumb  .vmb:hover{
		color: #FFF;
		background-color: <?=$colore1?>;
	}
	#menumb .vmb.telefono {
	    border: 1px dotted <?=$colore2?>;
	    padding: 0px 20px;
	    height: 50px;
	    line-height: 50px;
	}

	#menumb .vmb.preno {
	    border: 1px solid <?=$colore2?>;
	    padding: 0px 20px;
	    height: 50px;
	    line-height: 50px;
	    background-color: <?=$colore2?>;
	    color: #FFF;
	}

	#menumb.opened{
		height: 100vh;
		z-index: 1000;
		transition: all .2s ease;
	}
	#menumb.opened .hamburger{
	display: none !important;	
	}
	#menumb.opened .closex{
	display: block !important;	
	}
	#menumb.opened .intestazione{
		top: 15px;
		transform: translateY(0);

	}
	#menumb.opened .menumb{
		max-height: 10000px;
	}
</style>