<?

$select = "SELECT 
                hospitality_dizionario.etichetta
                , hospitality_dizionario_lingua.testo
            FROM 
                hospitality_dizionario 
            INNER JOIN
                hospitality_dizionario_lingua ON hospitality_dizionario_lingua.id_dizionario = hospitality_dizionario.id
            WHERE 
                hospitality_dizionario_lingua.Lingua = '".$Lingua."'
            AND 
                hospitality_dizionario_lingua.idsito = ".IDSITO;
$res    = $dbMysqli->query($select);
$tot_l  = sizeof($res);
if($tot_l > 0){
	foreach($res as $key => $value) {
		define($value['etichetta'],$value['testo']);
	}
}else{
	define( 'VISITA_NOSTRO_SITO','Visita il nostro sito');
	define( 'MESSAGGIO_PER_NOI','Messaggio per noi');
	define( 'PROPOSTE','Proposte');
	define( 'SOGGIORNI','Soggiorni');
	define( 'EVENTI','Eventi');
	define( 'PDI','Punti di Interesse');
	define( 'CONTATTA_HOTEL','Contatta l\'Hotel');
	define( 'MESSAGGIO','Messaggio');
	define( 'INVIA','Invia');
	define( 'IL_SUO' ,'Il suo');
	define( 'DA' ,'da');
	define('CONFERMA','Conferma');
	define('PREVENTIVO','Preventivo');
	define( 'OFFERTA','Offerta');
	define( 'DEL','del');
	define( 'DATA_ARRIVO','Data di Arrivo');
	define( 'DATA_PARTENZA','Data di Partenza');
	define( 'PROPOSTE_PER_NR_ADULTI','Proposte per N° Adulti:');
	define( 'SOGGIORNO_PER_NR_ADULTI','Soggiorno per N° Adulti:');
	define( 'NR_BAMBINI','N° Bambini:');
	define( 'NOTTI', 'N° Notti');
	define( 'ADULTI','Adulti:');
	define( 'BAMBINI','Bambini:');
	define( 'SOLUZIONECONFERMATA','Soluzione Confermata');
	define( 'PROPOSTA', 'Proposta');
	define( 'SOGGIORNO','Soggiorno:');
	define( 'TIPOCAMERA','Tipologia Camera:');
	define( 'SERVIZI_CAMERA','Servizi Camera:');
	define( 'CAMERA','Camera:');
	define( 'PREZZO','Prezzo Totale:');
	define( 'PREZZO_CAMERA','Prezzo Camera:');
	define( 'DA_LISTINO','da listino');
	define( 'E_PROPOSTO',' proposto per il soggiorno ');
	define( 'ALLA_CO','Alla c/o di ');
	define( 'CONTENUTO_MSG','vorremmo accettare o richiedere maggiori informazioni in merito alle offerte soggiorno da voi proposte:');
	define( 'CORDIALMENTE','Cordialmente');
	define( 'VISUALIZZA_MAPPA','Visualizza sulla Mappa');
	define( 'DOVE_SIAMO','Dove Siamo');
	define( 'PROPOSTA_SCELTA','Scegli la proposta ');
	define( 'PLACEHOLDER_PROPOSTA','Scegliere una delle proposte soggiorno, selezionando il checkbox relativo!');
	define( 'SALUTI','Saluti');
	define( 'SELEZIONA_PROPOSTA',' Seleziona la proposta e contatta l\'hotel!');
	define( 'STAMPA', 'VAUCHER PROMEMORIA');
	define( 'ANNI', 'anni');
	define( 'CONDIZIONI_GENERALI', 'Condizioni Generali e Politiche di Cancellazione');
	define( 'CREATA_DA', 'Creato da:');
	define( 'HOTELCHAT', 'Hotel Chat: qualche domanda?');
	define( 'QUESTIONARIO', 'Questionario soddisfazione del cliente');
	define( 'TESTO_QUESTIONARIO', 'Gentile [cliente], <br>esprimi il tuo parere sul soggiorno che hai appena trascorso presso la nostra struttura, per ogni domanda puoi dare un valore di soddisfazione ed un commento!<br> Il tuo pensiero sarà per noi fonte indispensabile per migliorare i nostri servizi in Hotel.');
	define( 'NO_QUESTIONARIO', 'Questionario già compilato!');
	define( 'THANKS_QUESTIONARIO', 'Ringraziandovi per aver compilato questo breve questionario, ci auguriamo di rivedervi presto nel nostro hotel!');
	define( 'LASCIA_COMMENTO', 'Lascia un commento');
	define( 'CARTA_CREDITO', 'Garanzia Carta di Credito');
	define( 'TESTO_CARTA_CREDITO', 'La carta di credito serve solo per garantire la prenotazione!<br> L\'importo del soggiorno non verrà addebitato sulla sua carta di credito, i cui dati rimangono conservati criptati su server sicuro a garanzia della prenotazione fino al giorno del suo arrivo.<br> Il soggiorno verrà pagato direttamente all\'hotel.');
	define( 'SALVA_CARTA_CREDITO', 'Salva Carta di Credito');
	define( 'CARTA', 'Carta');
	define( 'N_CARTA', 'Numero carta');
	define( 'INTESTATARIO', 'Intestatario');
	define( 'SCADENZA', 'Scadenza');
	define( 'CODICE', 'Codice CVV2');
	define( 'MSG_CARTA', 'Salvataggio criptato della Carta avvenuto con successo!<br> Chiudere ora La finestra!');
	define( 'DATI_CARTA', 'Dati Carta di Credito già inseriti!');
	define( 'RIEPILOGO_OFFERTA', 'Riepilogo Offerta');

}
## NUOVA AGGIUNTA DIZIONARIO PER QUESTO NUOVO TEMPLATE ##
switch($Lingua){
	case"it":
	  $titoloProposte                 = 'ABBIAMO <strong>'.$Nproposte.' </strong> '.($Nproposte == 1?'PROPOSTA': 'PROPOSTE').' PER TE';
	  $titoloServiziInclusi           = 'APPROFITTA ORA DEI SERVIZI AGGIUNTIVI';
	  $sottoTitoloServiziInclusi      = 'Servizi compresi nelle nostre proposte';
	  $titoloServiziAggiuntivi        = 'Servizi aggiunti o da aggiungere alla tua offerta per personalizzare la tua esperienza';
	  $dettagli                       = 'Dettagli';
	  $visualizzaMaggioriInformazioni = 'Visualizza maggiori informazioni';
	  $maggioriInformazioni           = 'Maggiori informazioni';
	  $visualizzaCondizioniTariffarie = 'Visualizza le Condizioni Tariffarie';
	  $selezionaQuestaProposta        = 'Seleziona questa proposta';
	  $selezionaAltraProposta         = 'Seleziona un\'altra proposta';
	  $haiSelezionatoQuestaProposta   = 'Hai selezionato questa proposta';
	  $servizioCompresoProposta       = 'Questo servizio è compreso nella tua proposta';
	  $aggiungiQuestoServizio         = 'Aggiungi questo servizio';
	  $haiSelezionatoQuestoServizio   = 'Hai selezionato questo servizio';
	  $rimuoviQuestoServizio          = 'Rimuovi questo servizio';
	  $calcolaCostoServizio           = 'Calcola il costo del servizio';
	  $textTotale                     = 'Totale';
	  $fraseChat                      = 'Se hai ancora dubbi Chatta con Noi';
	  $tooltipChat                    = 'Per qualsiasi dubbio chatta diretttamente con noi';
	  $gratis                         = 'Gratis';
	  $gentile                        = 'Gentile';
	  $ABILITA                        = 'Aggiungi Servizio';
	  $OBBLIGATORIO                   = 'Incluso';
	  $IMPOSTO                        = 'Incluso in questa proposta';
	  $A_PERCENTUALE                  = 'A percentuale';
	  $prezzoServizio                 = 'Prezzo del servizio';
	  $textInfoSconto                 = 'Lo sconto viene applicato solo sul totale della proposta che la struttura ricettiva, al momento della creazione del preventivo ha compilato; qualsiasi modifica apportata aggiungendo od eliminando servizi aggiuntivi, non agirà sulla cifra delle sconto!';
	  $PrezzoServizio                 = 'Prezzo Servizio';
	  $NumeroGiorni                   = 'Numero Giorni';
	  $ServizioAPersona               = 'a Persona';
	  break;
	case"en":
	  $titoloProposte                 = 'WE HAVE <strong>'.$Nproposte.' </strong> '.($Nproposte == 1?'PROPOSAL': 'PROPOSALS').' FOR YOU';
	  $titoloServiziInclusi           = 'NOW TAKE ADVANTAGE OF THE ADDITIONAL SERVICES';
	  $sottoTitoloServiziInclusi      = 'Services included in our proposals';
	  $titoloServiziAggiuntivi        = 'Added or additional services to personalize your experience';
	  $dettagli                       = 'Details';
	  $visualizzaMaggioriInformazioni = 'View more information';
	  $maggioriInformazioni           = 'More information';
	  $visualizzaCondizioniTariffarie = 'View Tariff Conditions';
	  $selezionaQuestaProposta        = 'Select this proposal';
	  $selezionaAltraProposta         = 'Select another proposal';
	  $haiSelezionatoQuestaProposta   = 'You have selected this proposal';
	  $servizioCompresoProposta       = 'This service is included in your proposal';
	  $aggiungiQuestoServizio         = 'Add this service';
	  $haiSelezionatoQuestoServizio   = 'You have selected this service';
	  $rimuoviQuestoServizio          = 'Remove this service';
	  $calcolaCostoServizio           = 'Calculate the service cost';
	  $textTotale                         = 'Total';
	  $fraseChat                      = 'If you still have doubts, chat with us';
	  $tooltipChat                    = 'If you have any doubts, chat directly with us';
	  $gratis                         = 'Free';
	  $gentile                        = 'Dear';
	  $ABILITA                        = 'Add Service';
	  $OBBLIGATORIO                   = 'Included';
	  $IMPOSTO                        = 'Included in this proposal';
	  $A_PERCENTUALE                  = 'By percentage';
	  $prezzoServizio                 = 'Price of the service';
	  $textInfoSconto                 = 'The discount is applied only to the total of the proposal that the accommodation facility, at the time of creating the quote, has compiled; any modifications made by adding or removing additional services will not affect the discount amount!';
	  $PrezzoServizio                 = 'Price Service';
	  $NumeroGiorni                   = 'Number of Days';
	  $ServizioAPersona               = 'per person';
	  break;
	case"fr":
	  $titoloProposte                 = 'NOUS AVONS <strong>'.$Nproposte.' </strong> '.($Nproposte == 1?'PROPOSITION': 'PROPOSITIONS').' POUR VOUS';
	  $titoloServiziInclusi           = 'PROFITEZ MAINTENANT DES SERVICES SUPPLÉMENTAIRES';
	  $sottoTitoloServiziInclusi      = 'Services inclus dans nos propositions';
	  $titoloServiziAggiuntivi        = 'Services ajoutés ou à ajouter à votre offre pour personnaliser votre expérience';
	  $dettagli                       = 'Détails';
	  $visualizzaMaggioriInformazioni = 'Afficher plus d\'informations';
	  $maggioriInformazioni           = 'Plus d\'informations';
	  $visualizzaCondizioniTariffarie = 'Afficher les Conditions Tarifaires';
	  $selezionaQuestaProposta        = 'Sélectionner cette proposition';
	  $selezionaAltraProposta         = 'Sélectionnez une autre proposition';
	  $haiSelezionatoQuestaProposta   = 'Vous avez sélectionné cette proposition';
	  $servizioCompresoProposta       = 'Ce service est inclus dans votre proposition';
	  $aggiungiQuestoServizio         = 'Ajouter ce service';
	  $haiSelezionatoQuestoServizio   = 'Vous avez sélectionné ce service';
	  $rimuoviQuestoServizio          = 'Supprimer ce service';
	  $calcolaCostoServizio           = 'Calculer le coût du service';
	  $textTotale                         = 'Total';
	  $fraseChat                      = 'Si vous avez encore des doutes, discutez avec nous';
	  $tooltipChat                    = 'Si vous avez des doutes, discutez directement avec nous';
	  $gratis                         = 'Gratuit';
	  $gentile                        = 'Bonjour ';
	  $ABILITA                        = 'Ajouter un service';
	  $OBBLIGATORIO                   = 'Inclus';
	  $IMPOSTO                        = 'Inclus dans cette proposition';
	  $A_PERCENTUALE                  = 'Par pourcentage';
	  $prezzoServizio                 = 'Prix ​​de la prestation';
	  $textInfoSconto                 = 'La remise est appliquée uniquement sur le total de la proposition que l\'établissement d\'hébergement, au moment de la création du devis, a compilé ; toute modification apportée en ajoutant ou en supprimant des services supplémentaires n\'affectera pas le montant de la remise!';
	  $PrezzoServizio                 = 'Service de prix';
	  $NumeroGiorni                   = 'Nombre de jours';
	  $ServizioAPersona               = 'par personne';
	  break;
	case"de":
	  $titoloProposte                 = 'WIR HABEN <strong>'.$Nproposte.' </strong> '.($Nproposte == 1?'VORSCHLAG': 'VORSCHLÄGE').' FÜR DICH';
	  $titoloServiziInclusi           = 'NUTZE JETZT DIE ZUSÄTZLICHEN SERVICES';
	  $sottoTitoloServiziInclusi      = 'Dienstleistungen in unseren Angeboten enthalten';
	  $titoloServiziAggiuntivi        = 'Hinzugefügte oder hinzuzufügende Dienstleistungen, um Ihr Erlebnis zu personalisieren';
	  $dettagli                       = 'Details';
	  $visualizzaMaggioriInformazioni = 'Weitere Informationen anzeigen';
	  $maggioriInformazioni           = 'Weitere Informationen';
	  $visualizzaCondizioniTariffarie = 'Tarifbedingungen anzeigen';
	  $selezionaQuestaProposta        = 'Diese Option auswählen';
	  $selezionaAltraProposta         = 'Wählen Sie einen anderen Vorschlag aus';
	  $haiSelezionatoQuestaProposta   = 'Sie haben diese Option ausgewählt';
	  $servizioCompresoProposta       = 'Dieser Service ist in Ihrem Angebot enthalten';
	  $aggiungiQuestoServizio         = 'Diesen Service hinzufügen';
	  $haiSelezionatoQuestoServizio   = 'Sie haben diesen Service ausgewählt';
	  $rimuoviQuestoServizio          = 'Diesen Service entfernen';
	  $calcolaCostoServizio           = 'Servicekosten berechnen';
	  $textTotale                         = 'Gesamt';
	  $fraseChat                      = 'Wenn Sie immer noch Zweifel haben, chatten Sie mit uns';
	  $tooltipChat                    = 'Wenn Sie Zweifel haben, chatten Sie direkt mit uns';
	  $gratis                         = 'Frei';
	  $gentile                        = 'Hallo ';
	  $ABILITA                        = 'Service hinzufügen';
	  $OBBLIGATORIO                   = 'Inbegriffen ';
	  $IMPOSTO                        = 'In diesem Vorschlag enthalten';
	  $A_PERCENTUALE                  = 'In Prozent';
	  $prezzoServizio                 = 'Preis der Dienstleistung';
	  $textInfoSconto                 = 'Der Rabatt wird nur auf den Gesamtbetrag des Angebots angewendet, den die Unterkunftseinrichtung zum Zeitpunkt der Erstellung des Angebots zusammengestellt hat; Änderungen durch das Hinzufügen oder Entfernen zusätzlicher Dienstleistungen beeinflussen nicht den Rabattbetrag!';
	  $PrezzoServizio                 = 'Preisservice';
	  $NumeroGiorni                   = 'Anzahl der Tage';
	  $ServizioAPersona               = 'pro Person';
	  break;
   }
?>                              