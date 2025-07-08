<?php
require_once __DIR__ . '/../models/Simulation.php';
require_once __DIR__ . '/../helpers/Utils.php';

class SimulationController {
    public static function create() {
    $data = Flight::request()->data;
    error_log("SimulationController::create appelé avec : " . json_encode($data));
    $id = Simulation::insertionRemboursement($data);
    Flight::json(['message' => 'simulation ajouté', 'id' => $id]);
}


    public static function liste() {
    $remboursements = Simulation::listeRemboursements();
    Flight::json($remboursements);
}

}