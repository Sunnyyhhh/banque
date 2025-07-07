<?php
require_once __DIR__ . '/../db.php';

class TypePret {
    public static function getAll() {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM type_pret");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function create($data) {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO type_pret (nom,taux) VALUES (?, ?)");
        $stmt->execute([$data->nom, $data->taux]);
        return $db->lastInsertId();
    }

    public static function countPretValide()
    {
        $db = getDB();
        $stmt = $db->prepare("select *from pret join utilisateur where ");
        return $db->lastInsertId();
    }
}