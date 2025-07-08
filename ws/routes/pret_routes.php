<?php
require_once __DIR__ . '/../controllers/PretController.php';

Flight::route('POST /pret', ['PretController', 'insertionPret']);
Flight::route('POST /pret/approuver', ['PretController', 'approuverPret']);
Flight::route('GET /pret/valides', ['PretController', 'getPretsValide']);
Flight::route('GET /pret/rembourser', ['PretController', 'rembourserPret']);