<?php
require_once __DIR__ . '/../controllers/PretController.php';

Flight::route('POST /pret', ['PretController', 'insertionPret']);
Flight::route('POST /pret/pdf', ['PretController', 'pdfPret']);