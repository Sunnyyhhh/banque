<?php
require_once __DIR__ . '/../models/Pret.php';
require_once __DIR__ . '/../helpers/Utils.php';
require_once __DIR__ . '/../models/TypePret.php';
require_once __DIR__ . '/../models/Etablissement.php';

class PretController {
    public static function insertionPret() {
        try {
            $data = Flight::request()->data;
            $id = Pret::insertionPret($data);
            Flight::json(['message' => 'Prêt inséré avec succès', 'id' => $id], 200);
        } catch (Throwable $e) {
            Flight::json(['message' => 'Erreur lors de l\'insertion du prêt: ' . $e->getMessage()], 500);
        }
    }

    public static function getById($id) {
        $Prets = TypePret::getById($id);
        Flight::json($Prets);
    }

    public static function approuverPret() {
        try {
            $data = Flight::request()->data;
            $id = $data->id;
            $date_validation = $data->date_validation;
            if (!$id || !$date_validation) {
                Flight::json(['message' => 'ID ou date de validation manquant'], 400);
                return;
            }

            $db = getDB();
            $stmt = $db->prepare("SELECT montant FROM pret WHERE id = ?");
            $stmt->execute([$id]);
            $pret = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$pret) {
                Flight::json(['message' => 'Prêt introuvable'], 404);
                return;
            }

            Pret::approuverPret($date_validation, $id);
            Pret::genererRemboursements($id);

            Flight::json(['message' => 'Prêt approuvé avec génération des remboursements'], 200);
        } catch (Throwable $e) {
            Flight::json(['message' => 'Erreur: ' . $e->getMessage()], 500);
        }
    }
    
    public static function getPretsValide() {
        try {
            $prets = Pret::getPretsValide();
            Flight::json($prets, 200);
        } catch (Throwable $e) {
            Flight::json(['message' => 'Erreur lors de la récupération des prêts validés: ' . $e->getMessage()], 500);
        }
    }

    public static function simulerPret() {
        try {
            $data = Flight::request()->data;
            $montant = $data->montant;
            $duree_mois = $data->duree_mois;
            $date_pret = $data->date_pret;
            $taux = $data->taux;
            $assurance = $data->assurance;

            if (!isset($montant) || !isset($duree_mois) || !isset($date_pret) || !isset($taux) || !isset($assurance)) {
                Flight::json(['message' => 'Données manquantes pour la simulation'], 400);
                return;
            }

            $remboursements = Pret::genererRemboursementsSimulation($data->id_groupe,$montant, $duree_mois, $date_pret, $taux, $assurance);

            if (empty($remboursements)) {
                Flight::json(['message' => 'Aucun remboursement généré'], 500);
                return;
            }

            Flight::json([
                'message' => 'Simulation effectuée avec succès',
                'remboursements' => $remboursements
            ], 200);
        } catch (Throwable $e) {
            Flight::json(['message' => 'Erreur lors de la simulation: ' . $e->getMessage()], 500);
        }
    }

    public static function rembourserPret() {
        try {
            $data = Flight::request()->data;

            if (!isset($data->montant) || !isset($data->duree_mois) || !isset($data->date_pret) || !isset($data->taux) || !isset($data->assurance)) {
                Flight::json(['message' => 'Données manquantes pour le remboursement'], 400);
                return;
            }

            $remboursements = Pret::genererRemboursementsSimulation($data->id_groupe,$data->montant, $data->duree_mois, $data->date_pret, $data->taux, $data->assurance);

            if (empty($remboursements)) {
                Flight::json(['message' => 'Aucun remboursement généré'], 500);
                return;
            }

            Flight::json([
                'message' => 'Remboursements générés avec succès',
                'remboursements' => $remboursements
            ], 200);
        } catch (Throwable $e) {
            Flight::json(['message' => 'Erreur lors du remboursement: ' . $e->getMessage()], 500);
        }
    }

    public static function getRemboursementsGroupes() {
        try {
            $groupes = Pret::getGroupes();
            if (empty($groupes)) {
                Flight::json(['message' => 'Aucun remboursement trouvé'], 404);
                return;
            }
            Flight::json([
                'message' => 'Remboursements groupés récupérés avec succès',
                'groupes' => $groupes
            ], 200);
        } catch (Throwable $e) {
            file_put_contents(__DIR__ . '/debug.log', "Erreur getRemboursementsGroupes: " . $e->getMessage() . "\n", FILE_APPEND);
            Flight::json(['message' => 'Erreur lors de la récupération des remboursements groupés: ' . $e->getMessage()], 500);
        }
    }

     public static function getByGroupe() {
        try {
            $id_groupe = Flight::request()->query['id_groupe'];
            if (!isset($id_groupe) || !is_numeric($id_groupe) || $id_groupe <= 0) {
                Flight::json(['message' => 'ID de groupe manquant ou invalide'], 400);
                return;
            }

            $remboursements = Pret::getByGroupe($id_groupe);
            if (empty($remboursements)) {
                Flight::json(['message' => 'Aucun remboursement trouvé pour ce groupe'], 404);
                return;
            }

            Flight::json([
                'message' => 'Remboursements récupérés avec succès',
                'remboursements' => $remboursements
            ], 200);
        } catch (Throwable $e) {
            file_put_contents(__DIR__ . '/debug.log', "Erreur getByGroupe: " . $e->getMessage() . "\n", FILE_APPEND);
            Flight::json(['message' => 'Erreur lors de la récupération des remboursements: ' . $e->getMessage()], 500);
        }
    }
}