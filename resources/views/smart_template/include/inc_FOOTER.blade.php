<footer>
	<div class="footer">
		<div class="m m-x-3 m-x-tr m-m-6 m-xs-12 m-xs-tc">
			<div class="box4">
				<?=($Logo ==''?'<i class="fas fa-bed fa-5x fa-fw"></i>':'<img src="'.config('global.settings.BASE_URL_IMG').'uploads/loghi/'.$Logo.'" class="logo">')?>
			</div>
		</div>
		<div class="m m-x-9 m-x-tl m-m-6 m-xs-12 m-xs-tc">
			<div class="box4 t14 w400">
				<b><?=$NomeCliente?></b><br> <?=$Indirizzo?> <?=$Localita?> - <?=$Cap?> (<?=$Provincia?>)
				<br><?=$SitoWeb?>
				<?php echo ($CIR!=''?'<br>CIR: '.$CIR:''); ?>
				<?php echo ($CIN!=''?'<br>CIN: '.$CIN:''); ?>
				 <div>
			                        <?=$Facebook?>
                                     <?=$Twitter?>
                                     <?=$GooglePlus?>
                                     <?=$Instagram?>
                                     <?=$Linkedin?>
                                     <?=$Pinterest?>

                </div>
			</div>
		</div>
		<div class="ca"></div>
		<p style="margin: 0;font-size: 11px;line-height: 14px;text-align: right"><em>Powered By <img src="/img/logo_quoto.png" style="width:100px">  <a href="https://www.network-service.it" target="_blank">Network Service s.r.l.</a></small> </em></p>
		<div class="ca10"></div>
	</div>
</footer>
