<?php
require_once __DIR__ . '/../controllers/SimulationController.php';
require_once __DIR__ . '/../controllers/PretController.php';


Flight::route('POST /simulations', ['SimulationController', 'create']);
Flight::route('GET /simulations/liste', ['SimulationController', 'liste']);
Flight::route('POST /simulationsRemboursement',['PretController', 'simulerPret']);

