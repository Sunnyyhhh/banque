<?php
require_once __DIR__ . '/../controllers/EtablissementController.php';

Flight::route('POST /add_fonds', ['EtablissementController', 'addFonds']);
Flight::route('POST /add_fonds', ['EtablissementController', 'addFonds']);

Flight::route('GET /get_solde_etablissement', function() {
    $id = Etablissement::getSingleId();
    if (!$id) {
        Flight::json(['solde' => 0]);
        return;
    }
    $solde = Etablissement::getSolde($id);
    Flight::json(['solde' => $solde]);
});