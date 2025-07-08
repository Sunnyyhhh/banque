<?php
require_once __DIR__ . '/../db.php';

class Remboursement {
    public static function insertionRemboursement($data) {
        $db = getDB();
        $stmt = $db->prepare(
            "INSERT INTO remboursement (id_pret, montant, interet, date_remboursement) 
             VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([
            $data->id_pret,                 
            $data->montant,                 
            $data->interet ?? null,          
            $data->date_remboursement ?? date('Y-m-d') 
        ]);
        return $db->lastInsertId();
    }

    public static function getRemboursements($id_pret) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM remboursement WHERE id_pret = ?");
        $stmt->execute([$id_pret]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC); 
    }
}
