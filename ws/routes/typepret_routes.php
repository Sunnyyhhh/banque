<?php
require_once __DIR__ . '/../controllers/TypePretController.php';

Flight::route('GET /typePret', ['TypePretController', 'getAll']);
Flight::route('GET /GetPret', ['TypePretController', 'getAllType']);
Flight::route('GET /typePret/@id', ['TypePretController', 'getById']);
Flight::route('POST /typePret', ['TypePretController', 'create']);
Flight::route('PUT /typePret/@id', ['TypePretController', 'update']);
Flight::route('DELETE /typePret/@id', ['TypePretController', 'delete']);

