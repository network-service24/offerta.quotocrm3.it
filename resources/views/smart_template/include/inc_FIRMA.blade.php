<?php
    switch($Lingua){
        case 'it':
            $FIRMA = "Realizzato da";
        break;
        case 'en':
            $FIRMA = "Realized by";
        break;
        case 'de':
            $FIRMA = "Realisiert von";
        break;
        case 'ru':
            $FIRMA = "Реализовано";
        break;
        };
?>
<style>
#FIRMANS {
	position: relative;
	clear:both;
	background-image: url(https://offerta.quotocrm.it/img/ns-firma-icona.png);
	background-repeat:no-repeat;
	padding-left: 18px;
	white-space: nowrap;
	font-size: 10px;
	margin-top:10px;
	padding:3px 8px;
	left: 50%;
	transform: translateX(-50%);
	-webkit-transform: translateX(-50%);
}
.FIRMADX {
	float:right;
	background-position: center right;
	padding-right: 18px !important;
}
.FIRMASX {
	float:left;
	background-position: center left;
	padding-left: 18px !important;
}
.FIRMAMD {
	text-align: center;
	background-position: center;
	padding-top: 40px !important;
	margin-top: -20px !important;
}
@media (max-width: 576px) {
    .FIRMASX {
	    float:none;
		text-align: center;
		background-position: center;
		padding-top: 40px !important;
		color: #333;
    }
}
</style>
<div id="FIRMANS" class="FIRMAMD"><?=$FIRMA;?>
  <a href="https://www.network-service.it/" target="_blank">Network Service</a>
</div>


