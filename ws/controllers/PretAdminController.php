<?php
require_once __DIR__ . '/../models/PretAdmin.php';

class PretAdminController {
    public static function lister() {
        $prets = PretAdmin::getAll();
        Flight::json($prets);
    }
}