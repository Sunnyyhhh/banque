<?php
require_once __DIR__ . '/../db.php';

class Utilisateur {

    public static function login($email) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM utilisateur WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

}
