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

    public static function getPretById($id) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM pret WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function approuverPret($id) {
        $db = getDB();
        $stmt = $db->prepare("UPDATE pret SET statut = 1, date_validation = CURRENT_DATE WHERE id = ?");
        $stmt->execute([$id]);
    }

    public static function getPretsValide()
    {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM pret WHERE statut = 1");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC); 
    }

/*public static function genererRemboursements($id_pret) {
    $db = getDB();

    // 1. Récupérer les infos nécessaires depuis pret + type_pret
    $stmt = $db->prepare("
        SELECT p.montant, p.duree_mois, p.date_pret, t.taux 
        FROM pret p
        JOIN type_pret t ON p.id_type_pret = t.id
        WHERE p.id = ?
    ");
    $stmt->execute([$id_pret]);
    $pret = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$pret) {
        throw new Exception("Prêt introuvable");
    }

    $C = $pret['montant'];
    $n = $pret['duree_mois'];
    $i = $pret['taux'] / 100 / 12; // taux mensuel
    $A = ($C * $i) / (1 - pow(1 + $i, -$n)); // annuité constante

    $date = new DateTime($pret['date_pret']);

    // 2. Insertion des remboursements
    $stmtInsert = $db->prepare("INSERT INTO remboursement (id_pret, montant, interet, date_remboursement) VALUES (?, ?, ?, ?)");

    for ($k = 0; $k < $n; $k++) {
        $dateStr = $date->format('Y-m-d');

        $interetMensuel = $C * $i;
        $principal = $A - $interetMensuel;
        $C -= $principal;

        $stmtInsert->execute([
            $id_pret,
            round($A, 2),
            round($interetMensuel, 2),
            $dateStr
        ]);

        $date->modify('+1 month');
    }

    return true;
}*/

public static function genererRemboursements($id_pret) {
    $db = getDB();

    // 1. Récupérer les infos nécessaires depuis pret + type_pret
    $stmt = $db->prepare("
        SELECT p.montant, p.duree_mois, p.date_pret, t.taux 
        FROM pret p
        JOIN type_pret t ON p.id_type_pret = t.id
        WHERE p.id = ?
    ");
    $stmt->execute([$id_pret]);
    $pret = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$pret) {
        throw new Exception("Prêt introuvable");
    }

    $C = $pret['montant'];
    $n = $pret['duree_mois'];
    $i = $pret['taux'] / 100 / 12; // taux mensuel
    $A = ($C * $i) / (1 - pow(1 + $i, -$n)); // annuité constante

    $date = new DateTime($pret['date_pret']);
    $capital_restant = $C; // Initialisation du capital restant

    // 2. Insertion des remboursements
    $stmtInsert = $db->prepare("INSERT INTO remboursement (id_pret, montant, interet, date_remboursement) VALUES (?, ?, ?, ?)");

    for ($k = 0; $k < $n; $k++) {
        $dateStr = $date->format('Y-m-d');

        $interetMensuel = $capital_restant * $i;
        $principal = $A - $interetMensuel;
        $capital_restant -= $principal;

        $stmtInsert->execute([
            $id_pret,
            round($A, 2),
            round($interetMensuel, 2),
            $dateStr
        ]);

        $date->modify('+1 month');
    }

    return true;
}



}