<?php
include($_SERVER['DOCUMENT_ROOT']."/class/mysql.class.php");
$db = new mysql;
$db->connect();

$cod_tmp       = base64_decode($_REQUEST['v']);
$cod_tmp       = explode("_",$cod_tmp);
$IdRichiesta   = $cod_tmp[0];
$IdSito        = $cod_tmp[1];
$TipoRichiesta = $cod_tmp[2];
$Id_Rich       = $cod_tmp[3];
// Pagamento semplice - Esito
$type          = $_REQUEST['type'];
$dir           = $_REQUEST['dir'];

$nx = "SELECT * FROM hospitality_tipo_pagamenti WHERE idsito= ".$IdSito." AND Abilitato = 1  AND TipoPagamento = 'Nexi'";
$res_nx = $db->query($nx);
$row_nx = $db->fetch($res_nx); 

// Chiave segreta 
$CHIAVESEGRETA = $row_nx['SegretKeyNexi'];  // Sostituire con il valore fornito da Nexi

// Controllo che ci siano tutti i parametri di ritorno obbligatori per calcolare il MAC
$requiredParams = array('codTrans', 'esito', 'importo', 'divisa', 'data', 'orario', 'codAut', 'mac');
foreach ($requiredParams as $param) {
    if (!isset($_REQUEST[$param])) {
        echo 'Paramentro mancante ' . $param;
        exit;
    }
}

// Calcolo MAC con i parametri di ritorno
$macCalculated = sha1('codTrans=' . $_REQUEST['codTrans'] .
        'esito=' . $_REQUEST['esito'] .
        'importo=' . $_REQUEST['importo'] .
        'divisa=' . $_REQUEST['divisa'] .
        'data=' . $_REQUEST['data'] .
        'orario=' . $_REQUEST['orario'] .
        'codAut=' . $_REQUEST['codAut'] .
        $CHIAVESEGRETA
);

// Verifico corrispondenza tra MAC calcolato e parametro mac di ritorno
if ($macCalculated != $_REQUEST['mac']) {
    echo 'Errore MAC: ' . $macCalculated . ' non corrisponde a ' . $_REQUEST['mac'];
    exit;
}

// Nel caso in cui non ci siano errori gestisco il parametro esito
if ($_REQUEST['esito'] == 'OK') {
   // echo 'La transazione ' . $_REQUEST['codTrans'] . " è avvenuta con successo; codice autorizzazione: " . $_REQUEST['codAut'];
      ##### Registrazione pagamento con nexi
      $requiredParams = array('codTrans', 'esito', 'importo', 'divisa', 'data', 'orario', 'codAut', 'mac');
      foreach ($requiredParams as $param) {
          if (!isset($_REQUEST[$param])) {
              echo 'Paramentro mancante ' . $param;
              exit;
          }
      } 

      // Calcolo MAC con i parametri di ritorno
       $macCalculated = sha1('codTrans=' . $_REQUEST['codTrans'] .
              'esito=' . $_REQUEST['esito'] .
              'importo=' . $_REQUEST['importo'] .
              'divisa=' . $_REQUEST['divisa'] .
              'data=' . $_REQUEST['data'] .
              'orario=' . $_REQUEST['orario'] .
              'codAut=' . $_REQUEST['codAut'] .
              $CHIAVESEGRETA
      );

      // Verifico corrispondenza tra MAC calcolato e parametro mac di ritorno
      if ($macCalculated != $_REQUEST['mac']) {
          echo 'Errore MAC: ' . $macCalculated . ' non corrisponde a ' . $_REQUEST['mac'];
          exit;
      } 

      // Nel caso in cui non ci siano errori gestisco il parametro esito
      if ($_REQUEST['esito'] == 'OK') {
          //$nexi = 'La transazione ' . $_REQUEST['codTrans'] . " è avvenuta con successo; codice autorizzazione: " . $_REQUEST['codAut'];
          
          $q = $db->query('SELECT * FROM hospitality_altri_pagamenti WHERE id_richiesta = '.$IdRichiesta);
          $rec = $db->num_rows($q);
          $row = $db->fetch($q);
          if($rec == 0){
                  $insert ="INSERT INTO hospitality_altri_pagamenti(idsito,
                                                      id_richiesta,
                                                      TipoPagamento,
                                                      CRO,
                                                      data_inserimento)
                                                      VALUES ('".$IdSito."',
                                                      '".$IdRichiesta."',
                                                      'Nexi',
                                                      '".$_REQUEST['codTrans']."',
                                                      '".date('Y-m-d')."')";

                  $db->query($insert);
          }else{
                  if($row['TipoPagamento']!= 'Nexi'){

                          $update ="UPDATE hospitality_altri_pagamenti SET
                                  TipoPagamento = 'Stripe', data_inserimento = '".date('Y-m-d')."', CRO = '".$_REQUEST['codTrans']."'
                                  WHERE Id = ".$row['Id']." AND id_richiesta = ".$IdRichiesta."";

                      $db->query($update);
                  }
          } 
      } 
      //else {
          //$nexi = 'La transazione ' . $_REQUEST['codTrans'] . " è stata rifiutata; descrizione errore: " . $_REQUEST['messaggio'];
      //}
      header('location: https://'.$_SERVER['HTTP_HOST'].'/'.($type!=''?$type.'/':'').($dir !=''?$dir.'/':'').$_REQUEST['v'].'/bmV4aQ==/index/');
} else {
    echo 'La transazione ' . $_REQUEST['codTrans'] . " è stata rifiutata; descrizione errore: " . $_REQUEST['messaggio'];
}
                    
?>