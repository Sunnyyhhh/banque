<?php
require_once __DIR__ . '/../models/TypePret.php';
require_once __DIR__ . '/../helpers/Utils.php';

class TypePretController {
    public static function getAll() {
        $TypePrets = TypePret::getAll();
        Flight::json($TypePrets);
    }

    public static function getAllType() {
        $TypePrets = TypePret::getAll();
        Flight::json(['typePrets' => $TypePrets]);

    }

    public static function create() {
        $data = Flight::request()->data;
        $id = TypePret::create($data);
        Flight::json(['message' => 'Type Pret ajoute', 'id' => $id]);
    }

    public static function getValides()
    {
        $nb_valides=TypePret::countPretValide();
        Flight::json($nb_valides);
    }
}