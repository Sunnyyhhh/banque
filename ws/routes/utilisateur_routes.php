<?php
require_once __DIR__ . '/../controllers/UtilisateurController.php';

Flight::route('POST /login', ['UtilisateurController', 'login']);
Flight::route('GET /clients', ['UtilisateurController', 'getAll']);

