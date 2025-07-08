<?php
require_once __DIR__ . '/../controllers/StatistiqueController.php';

Flight::route('GET /statistque', ['StatistiqueController', 'getAll']);
