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
        $stmt = $db->prepare("INSERT INTO type_pret (nom,taux,nb_annees) VALUES (?,?,?)");
        $stmt->execute([$data->nom, $data->taux, $data->nb_annees]);
        return $db->lastInsertId();
    }

    public static function countPretValide()
    {
        $db = getDB();
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM pret WHERE statut = 1");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $result['total'];
    }

    public static function countPretNonValide()
    {
        $db = getDB();
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM pret WHERE statut = 0");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $result['total'];
    }

    public static function countTypePret()
    {
        $db = getDB();
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM type_pret");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $result['total'];
    }


}