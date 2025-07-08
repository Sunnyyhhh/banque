<?php
require_once __DIR__ . '/../controllers/RemboursementController.php';
require_once __DIR__ . '/../controllers/PretController.php';


Flight::route('POST /remboursement/listeById/@id', ['RemboursementController', 'getAll']);
Flight::route('GET /remboursements/groupes', ['PretController', 'getRemboursementsGroupes']);

// Route pour récupérer les remboursements d'un groupe spécifique
Flight::route('GET /remboursements/byGroupe', ['PretController', 'getByGroupe']);

// Autres routes (par exemple, pour l'insertion de prêt)
Flight::route('POST /pret', ['PretController', 'insertionPret']);