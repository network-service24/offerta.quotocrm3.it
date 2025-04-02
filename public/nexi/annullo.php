<?php
// Pagamento semplice - Esito

// Pagamento semplice - Esito
$dir           = $_REQUEST['dir'];
$type          = $_REQUEST['type'];
//echo "Pagamento Annullato!";
header('location: https://'.$_SERVER['HTTP_HOST'].'/'.($type!=''?$type.'/':'').($dir !=''?$dir.'/':'').$_REQUEST['v'].'/index/');
?>