<?php
require_once __DIR__ . '/../db.php';

class Simulation {
   public static function insertionRemboursement($data) {
    $db = getDB();
    try {
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
    } catch (PDOException $e) {
        error_log("Erreur d'insertion remboursement : " . $e->getMessage());
        return null;
    }
}


    public static function listeRemboursements() {
    $db = getDB();
    $stmt = $db->prepare("
        SELECT r.montant, p.date_pret, p.duree_mois
        FROM remboursement r
        JOIN pret p ON r.id_pret = p.id
        ORDER BY r.date_remboursement ASC
    ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

}
