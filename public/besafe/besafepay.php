<?php
//composer require besafepay/besafepay-php

// config.php
define('BESAFEPAY_API_KEY', 'la_tua_api_key');
define('BESAFEPAY_API_URL', 'https://api.besafepay.com');  // O l'endpoint corretto


// payment.php
require_once 'config.php';

function createPayment($order_id, $amount, $currency, $return_url) {
    // URL dell'API BeSafePay per la creazione di una transazione
    $url = BESAFEPAY_API_URL . "/v1/transactions";
    
    // Dati della transazione
    $data = [
        'amount' => $amount,       // Importo in formato decimale
        'currency' => $currency,   // Valuta (es. EUR, USD)
        'order_id' => $order_id,   // ID dell'ordine
        'return_url' => $return_url, // URL di ritorno post-pagamento
    ];

    // Inizializzazione della sessione cURL
    $ch = curl_init($url);

    // Impostazione delle opzioni cURL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . BESAFEPAY_API_KEY,
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    // Esecuzione della richiesta e recupero della risposta
    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    // Gestione degli errori
    if ($error) {
        die('Error in cURL request: ' . $error);
    }

    // Decodifica della risposta JSON
    $responseData = json_decode($response, true);
    
    if ($responseData['status'] == 'success') {
        return $responseData['payment_url'];  // URL di pagamento per il cliente
    } else {
        die('Error in transaction: ' . $responseData['message']);
    }
}

// Esempio di utilizzo
$order_id = 123456;
$amount = 100.00;  // L'importo del pagamento
$currency = 'EUR'; // La valuta
$return_url = 'https://tuocrm.it/return.php'; // URL a cui l'utente verrà reindirizzato dopo il pagamento

$paymentUrl = createPayment($order_id, $amount, $currency, $return_url);

// Reindirizza il cliente alla pagina di pagamento BeSafePay
header('Location: ' . $paymentUrl);
exit();






// return.php
require_once 'config.php';

function verifyPayment($transaction_id) {
    // URL dell'API BeSafePay per la verifica della transazione
    $url = BESAFEPAY_API_URL . "/v1/transactions/" . $transaction_id;

    // Inizializzazione della sessione cURL
    $ch = curl_init($url);

    // Impostazione delle opzioni cURL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . BESAFEPAY_API_KEY
    ]);

    // Esecuzione della richiesta e recupero della risposta
    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    // Gestione degli errori
    if ($error) {
        die('Error in cURL request: ' . $error);
    }

    // Decodifica della risposta JSON
    $responseData = json_decode($response, true);
    
    if ($responseData['status'] == 'success') {
        // Transazione verificata con successo
        // Esegui le azioni per completare l'ordine (aggiornare stato, inviare email, etc.)
        return true;
    } else {
        // Transazione fallita
        return false;
    }
}

// Recupero dell'ID della transazione dalla query string (ad esempio: return.php?transaction_id=123456)
if (isset($_GET['transaction_id'])) {
    $transaction_id = $_GET['transaction_id'];

    if (verifyPayment($transaction_id)) {
        echo "Pagamento completato con successo!";
    } else {
        echo "Errore durante il pagamento.";
    }
} else {
    echo "ID transazione mancante.";
}
