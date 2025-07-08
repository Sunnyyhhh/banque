<?php
require 'vendor/autoload.php';
require 'db.php';
require 'routes/etudiant_routes.php';
require 'routes/utilisateur_routes.php';
require 'routes/pret_routes.php';
require 'routes/typePret_routes.php';
require 'routes/etablissement_routes.php';
require 'routes/remboursement_routes.php';

require 'routes/pret_admin_routes.php';
require 'routes/statistique_routes.php';
require 'routes/simulation_routes.php';

Flight::start();

