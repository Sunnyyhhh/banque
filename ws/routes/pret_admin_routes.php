<?php
require_once __DIR__ . '/../controllers/PretAdminController.php';

Flight::route('GET /pret_admin', ['PretAdminController', 'lister']);