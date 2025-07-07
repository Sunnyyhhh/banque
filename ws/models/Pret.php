<?php
require_once __DIR__ . '/../db.php';

class Pret {
    public static function insertionPret($data) {
        $db = getDB();
        $stmt = $db->prepare(
            "INSERT INTO pret (id_client, id_type_pret, montant, date_pret, duree_mois, date_echeance, interet, date_validation, statut) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $data->id_client,
            $data->id_type_pret,
            $data->montant,
            $data->date_pret,
            $data->duree_mois,
            $data->date_echeance,
            $data->interet,
            $data->date_validation ?? date('Y-m-d'),
            $data->statut ?? 0
        ]);
        return $db->lastInsertId();
    }
}