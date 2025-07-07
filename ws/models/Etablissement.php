<?php
require_once __DIR__ . '/../db.php';

class Etablissement {
    public static function getSingleId() {
        try {
            $db = getDB();
            $stmt = $db->query("SELECT id FROM fonds LIMIT 1");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            error_log('getSingleId: ' . ($result ? 'ID=' . $result['id'] : 'Aucun établissement'));
            return $result ? $result['id'] : null;
        } catch (Exception $e) {
            error_log('Erreur getSingleId: ' . $e->getMessage());
            throw $e;
        }
    }

    


public static function addFonds($id, $montant) {
    try {
        $db = getDB();
        // Insère une nouvelle ligne dans la table etablissement avec le montant
        $stmt = $db->prepare("INSERT INTO fonds (fonds_disponibles, date_ajout) VALUES (?, CURRENT_TIMESTAMP)");
        $stmt->execute([$montant]);
        $success = $stmt->rowCount() > 0;
        error_log('addFonds: ' . ($success ? 'Nouveau fonds inséré' : 'Échec'));
        return $success;
    } catch (Exception $e) {
        error_log('Erreur addFonds: ' . $e->getMessage());
        throw $e;
    }
}

public static function getSolde() {
    try {
        $db = getDB();
        $stmt = $db->query("SELECT SUM(fonds_disponibles) AS total FROM fonds");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['total'] : 0;
    } catch (Exception $e) {
        error_log('Erreur getSolde: ' . $e->getMessage());
        return 0;
    }
}

}