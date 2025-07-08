<?php
require_once __DIR__ . '/../db.php';

class Utilisateur {

    public static function login($email) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM utilisateur WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function getAll() {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM utilisateur");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getSolde() 
    {
        $db = getDB();
        $stmt = $db->prepare("SELECT SUM(fonds_disponibles) AS total_fonds FROM fonds");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total_fonds'] !== null ? $result['total_fonds'] : 0;
    }


}
