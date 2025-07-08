<?php
require_once __DIR__ . '/../db.php';

class Statistique {
    public static function getAll() {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM remboursement");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}