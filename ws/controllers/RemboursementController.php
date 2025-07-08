<?php
require_once __DIR__ . '/../models/Remboursement.php';
require_once __DIR__ . '/../helpers/Utils.php';

class RemboursementController {
    public static function getAll($id) {
        $remboursements = Remboursement::getRemboursements($id);
        Flight::json($remboursements);
    }
}