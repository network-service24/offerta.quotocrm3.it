<?php
//composer require besafepay/besafepay-php

// config.php
define('BESAFEPAY_API_KEY', 'la_tua_api_key');
define('BESAFEPAY_API_URL', 'https://api.besafepay.com');  // O l'endpoint corretto


// create_payment_plan.php
require_once 'config.php';

function createPaymentPlan($order_id, $amount, $currency, $num_installments, $return_url) {
    // URL dell'API BeSafePay per creare un piano di pagamento
    $url = BESAFEPAY_API_URL . "/v1/payment_plans";

    // Calcolare l'importo per ogni rata
    $installment_amount = $amount / $num_installments;

    // Dati per la creazione del piano di pagamento
    $data = [
        'order_id' => $order_id,           // ID dell'ordine
        'total_amount' => $amount,         // Importo totale
        'currency' => $currency,           // Valuta (es. EUR, USD)
        'num_installments' => $num_installments,  // Numero di rate
        'installment_amount' => $installment_amount,  // Importo per rata
        'return_url' => $return_url,      // URL di ritorno dopo il pagamento
        'payment_frequency' => 'monthly', // Frequenza del pagamento (es. mensile)
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
        return $responseData['payment_plan_url'];  // URL di pagamento per il cliente
    } else {
        die('Error in creating payment plan: ' . $responseData['message']);
    }
}

// Esempio di utilizzo
$order_id = 123456;
$amount = 300.00;  // L'importo totale
$currency = 'EUR'; // La valuta
$num_installments = 3;  // Numero di rate
$return_url = 'https://tuocrm.it/return.php'; // URL a cui l'utente verrà reindirizzato dopo il pagamento

$paymentUrl = createPaymentPlan($order_id, $amount, $currency, $num_installments, $return_url);

// Reindirizza il cliente alla pagina di pagamento BeSafePay
header('Location: ' . $paymentUrl);
exit();






// return.php
require_once 'config.php';

function verifyPaymentPlan($transaction_id) {
    // URL dell'API BeSafePay per verificare il pagamento del piano rateizzato
    $url = BESAFEPAY_API_URL . "/v1/payment_plans/" . $transaction_id;

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
        // Transazione approvata
        echo "Pagamento completato con successo!";
        // Aggiorna lo stato dell'ordine e altre azioni necessarie
    } else {
        echo "Errore nel pagamento.";
    }
}

// Recupero dell'ID della transazione dalla query string
if (isset($_GET['transaction_id'])) {
    $transaction_id = $_GET['transaction_id'];
    verifyPaymentPlan($transaction_id);
} else {
    echo "ID transazione mancante.";
}
