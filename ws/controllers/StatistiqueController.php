<?php
require_once __DIR__ . '/../models/Statistique.php';
require_once __DIR__ . '/../helpers/Utils.php';


class StatistiqueController {
    public static function getAll() {
        $remboursement = Statistique::getAll();
        Flight::json($remboursement);
    }
 
}
