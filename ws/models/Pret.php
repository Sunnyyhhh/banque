<?php
require_once __DIR__ . '/../db.php';

class Pret {
    public static function insertionPret($data) {
        $db = getDB();
        $stmt = $db->prepare(
            "INSERT INTO pret (id_client, id_type_pret, montant, date_pret, duree_mois, date_echeance, date_validation, statut,assurance) 
            VALUES ( ?, ?, ?, ?, ?, ?, ?, ?,?)"
        );
        $stmt->execute([
            $data->id_client,
            $data->id_type_pret,
            $data->montant,
            $data->date_pret,
            $data->duree_mois,
            $data->date_echeance,
            $data->date_validation ?? date('Y-m-d'),
            $data->statut ?? 0,
            $data->assurance
        ]);
        return $db->lastInsertId();
    }

    public static function getPretById($id) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM pret WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function approuverPret($id,$date) {
        $db = getDB();
        $stmt = $db->prepare("UPDATE pret SET statut = 1, date_validation = ? WHERE id = ?");
        $stmt->execute([$date,$id]);
    }

    public static function getPretsValide()
    {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM pret WHERE statut = 1");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC); 
    }

/*public static function genererRemboursementsSimulation($id_groupe,$montant, $duree_mois, $date_pret, $taux, $assurance) {
        $db = getDB();

        $C = floatval($montant);
        $n = intval($duree_mois);
        $i = floatval($taux) / 100 / 12; // taux mensuel
        $A = ($C * $i) / (1 - pow(1 + $i, -$n)); // annuité constante
        $assuranceMensuelle = ($C * floatval($assurance) / 100) / 12; // assurance mensuelle

        $date = new DateTime($date_pret);
        $capital_restant = $C;
        $remboursements = [];

        $stmtInsert = $db->prepare("INSERT INTO remboursement (id_groupe,montant, date_remboursement, interet, assurance) VALUES (?,?, ?, ?, ?)");

        for ($k = 0; $k < $n; $k++) {
            $dateStr = $date->format('Y-m-d');
            $interetMensuel = $capital_restant * $i;
            $principal = $A - $interetMensuel;
            $capital_restant -= $principal;

            $stmtInsert->execute([
                round($A + $assuranceMensuelle, 2),
                $dateStr,
                round($interetMensuel, 2),
                round($assuranceMensuelle, 2)
            ]);

            $remboursements[] = [
                'id_groupe' => $id_groupe,
                'mois' => $k + 1,
                'montant' => round($A + $assuranceMensuelle, 2),
                'interet' => round($interetMensuel, 2),
                'principal' => round($principal, 2),
                'capital_restant' => round(max(0, $capital_restant), 2),
                'date_remboursement' => $dateStr,
                'assurance' => round($assuranceMensuelle, 2)
            ];

            $date->modify('+1 month');
        }

        return $remboursements;
    }*/

    public static function genererRemboursementsSimulation($id_groupe, $montant, $duree_mois, $date_pret, $taux, $assurance) {
        $db = getDB();
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $C = floatval($montant);
        $n = intval($duree_mois);
        $i = floatval($taux) / 100 / 12; // taux mensuel
        $A = ($C * $i) / (1 - pow(1 + $i, -$n)); // annuité constante
        $assuranceMensuelle = ($C * floatval($assurance) / 100) / 12; // assurance mensuelle

        $date = new DateTime($date_pret);
        $capital_restant = $C;
        $remboursements = [];

        $stmtInsert = $db->prepare(
            "INSERT INTO remboursement (id_groupe, montant, date_remboursement, interet, assurance) 
            VALUES (?, ?, ?, ?, ?)"
        );

        file_put_contents(__DIR__ . '/debug.log', "Début insertion: id_groupe=$id_groupe, montant=$montant, duree_mois=$duree_mois, date_pret=$date_pret, taux=$taux, assurance=$assurance\n", FILE_APPEND);

        for ($k = 0; $k < $n; $k++) {
            $dateStr = $date->format('Y-m-d');
            $interetMensuel = $capital_restant * $i;
            $principal = $A - $interetMensuel;
            $capital_restant -= $principal;

            $montantTotal = round($A + $assuranceMensuelle, 2);
            $interetMensuel = round($interetMensuel, 2);
            $assuranceMensuelle = round($assuranceMensuelle, 2);

            try {
                $stmtInsert->execute([
                    $id_groupe,
                    $montantTotal,
                    $dateStr,
                    $interetMensuel,
                    $assuranceMensuelle
                ]);
            } catch (PDOException $e) {
                file_put_contents(__DIR__ . '/debug.log', "Erreur insertion #$k: " . $e->getMessage() . "\n", FILE_APPEND);
                throw new Exception("Erreur lors de l'insertion dans remboursement: " . $e->getMessage());
            }

            $remboursements[] = [
                'id_groupe' => $id_groupe,
                'mois' => $k + 1,
                'montant' => $montantTotal,
                'interet' => $interetMensuel,
                'principal' => round($principal, 2),
                'capital_restant' => round(max(0, $capital_restant), 2),
                'date_remboursement' => $dateStr,
                'assurance' => $assuranceMensuelle
            ];

            $date->modify('+1 month');
        }

        file_put_contents(__DIR__ . '/debug.log', "Fin insertion: " . count($remboursements) . " remboursements générés\n", FILE_APPEND);

        return $remboursements;
    }


    public static function getGroupes()
    {
        $db = getDB();
        $stmt = $db->prepare(" select *from remboursement group by montant;");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getByGroupe($id_groupe)
    {
        $db = getDB();
        //$stmt = $db->prepare("SELECT * FROM remboursement WHERE id_groupe = ?");
        $stmt = $db->prepare("SELECT id_groupe, MIN(montant) as montant, MIN(date_remboursement) as date_remboursement FROM remboursement GROUP BY id_groupe;");
        $stmt->execute([$id_groupe]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}