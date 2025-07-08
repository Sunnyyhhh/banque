<?php
require_once __DIR__ . '/../controllers/RemboursementController.php';

Flight::route('POST /remboursement/listeById/@id', ['RemboursementController', 'getAll']);
