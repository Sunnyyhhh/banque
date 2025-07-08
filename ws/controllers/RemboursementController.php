<?php
require_once __DIR__ . '/../models/Remboursement.php';
require_once __DIR__ . '/../helpers/Utils.php';

class RemboursementController {
    public static function getAll() {
        $Remboursements = Remboursement::getRemboursements(9);
        Flight::json($Remboursements);
    }
}