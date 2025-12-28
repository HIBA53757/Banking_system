<?php

require_once "database.php";
require_once "classes/client.php";
require_once "classes/compte.php";
require_once "classes/courant.php";
require_once "classes/epargne.php";
require_once "classes/Transaction.php";

// Get PDO connection
$pdo = Database::get_instance()->connection();

// Helper to print with <br>
function printLine($text) {
    echo $text . "<br>";
}

printLine("=== TEST CLIENTS ===");

try {
    // CREATE CLIENT
    $client = new Client($pdo, "Ali", "ali_test_unique" . rand(1000,9999) . "@gmail.com");
    $client->save();
    printLine("Client créé : ID " . $client->getId() . " | " . $client->getEmail());
    printLine("");

    // LIST ALL CLIENTS
    printLine("--- Liste des clients ---");
    $clients = Client::show();
    foreach ($clients as $c) {
        printLine("[" . $c['id'] . "] " . $c['nom'] . " - " . $c['email']);
    }

    printLine("");
    printLine("=== TEST COMPTES ===");

    // CREATE COMPTES WITH UNIQUE NUMBERS
    $numeroCourant = "CC" . rand(1000, 9999);
    $compteCourant = new Compte_Courant($pdo, $numeroCourant, 100, $client->getId());
    $compteCourant->create();
    printLine("Compte Courant créé : ID " . $compteCourant->getId() . " | Numero: " . $compteCourant->getNumero() . " | Solde: " . $compteCourant->getSolde());

    $numeroEpargne = "CE" . rand(1000, 9999);
    $compteEpargne = new Compte_Epargne($pdo, $numeroEpargne, 500, $client->getId());
    $compteEpargne->create();
    printLine("Compte Epargne créé : ID " . $compteEpargne->getId() . " | Numero: " . $compteEpargne->getNumero() . " | Solde: " . $compteEpargne->getSolde());

    printLine("");
    printLine("=== TEST TRANSACTIONS ===");

    $transaction = new Transaction($pdo);

    // DEPOSIT
    $transaction->deposer($compteCourant, 50);
    printLine("Dépôt sur compte Courant : +50 | Nouveau solde = " . $compteCourant->getSolde());

    // WITHDRAWAL
    $transaction->retirer($compteCourant, 30);
    printLine("Retrait sur compte Courant : -30 | Nouveau solde = " . $compteCourant->getSolde());

    // DEPOSIT on savings
    $transaction->deposer($compteEpargne, 200);
    printLine("Dépôt sur compte Epargne : +200 | Nouveau solde = " . $compteEpargne->getSolde());

    // WITHDRAWAL on savings
    $transaction->retirer($compteEpargne, 100);
    printLine("Retrait sur compte Epargne : -100 | Nouveau solde = " . $compteEpargne->getSolde());

    printLine("");
    printLine("=== AFFICHAGE COMPTES PAR CLIENT ===");
    foreach ([$compteCourant, $compteEpargne] as $compte) {
        printLine("Compte ID: " . $compte->getId() . " | Numero: " . $compte->getNumero() . " | Solde: " . $compte->getSolde() . " | Type: " . get_class($compte));
    }

    printLine("");
    printLine("=== AFFICHAGE DES OPERATIONS ===");
    $stmt = $pdo->query("SELECT o.id, o.montant, o.type, o.date_operation, c.numero, c.client_id 
                         FROM operation o 
                         JOIN compte c ON o.compte_id = c.id
                         ORDER BY o.date_operation ASC");
    $operations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($operations as $op) {
        printLine("Op ID: " . $op['id'] . " | Type: " . $op['type'] . " | Montant: " . $op['montant'] .
             " | Compte: " . $op['numero'] . " | Client ID: " . $op['client_id'] .
             " | Date: " . $op['date_operation']);
    }

    printLine("");
    printLine("=== FIN DES TESTS ===");

} catch (Exception $e) {
    printLine("Erreur : " . $e->getMessage());
}
