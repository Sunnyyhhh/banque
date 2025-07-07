<?php
require_once __DIR__ . '/../models/Pret.php';
require_once __DIR__ . '/../helpers/Utils.php';

class PretController {
    public static function insertionPret() {
        try{
        $data = Flight::request()->data;
        $id = Pret::insertionPret($data);
        Flight::json(['message' => 'Prêt inséré avec succès', 'id' => $id], 200);
        } catch (Throwable $e) {
        Flight::json(['message' => 'Erreur lors de l\'insertion du prêt: ' . $e->getMessage()], 500);
        }
    }
}