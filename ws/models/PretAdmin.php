<?php
require_once __DIR__ . '/../db.php';

class PretAdmin {
    public static function getAll() {
        $db = getDB();
        $sql = "SELECT p.id, u.nom AS client_nom, u.prenom AS client_prenom, p.montant, p.date_pret, p.duree_mois, p.date_echeance, t.nom AS type_pret
                FROM pret p
                JOIN utilisateur u ON p.id_client = u.id_utilisateur
                JOIN type_pret t ON p.id_type_pret = t.id
                WHERE p.statut = 0";
        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}