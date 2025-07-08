<?php
require_once __DIR__ . '/../controllers/SimulationController.php';

Flight::route('POST /simulations', ['SimulationController', 'create']);
Flight::route('GET /simulations/liste', ['SimulationController', 'liste']);

