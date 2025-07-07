<?php
require_once __DIR__ . '/../models/Etablissement.php';

class EtablissementController {
    public static function addFonds() {
        error_log('Appel de addFonds');
        $data = Flight::request()->data;
        $montant = $data->montant;

        error_log('Montant reçu: ' . $montant);

        if (!is_numeric($montant) || $montant <= 0) {
            error_log('Erreur: Montant invalide');
            Flight::json(['message' => 'Montant invalide'], 400);
            return;
        }

        try {
            $etablissement_id = Etablissement::getSingleId();
            error_log('ID établissement: ' . ($etablissement_id ? $etablissement_id : 'null'));

            if (!$etablissement_id) {
                error_log('Erreur: Aucun établissement trouvé');
                Flight::json(['message' => 'Aucun établissement trouvé'], 404);
                return;
            }

            if (Etablissement::addFonds($etablissement_id, $montant)) {
                error_log('Succès: Fonds ajoutés');
                Flight::json(['message' => 'Fonds ajoutés avec succès']);
            } else {
                error_log('Erreur: Échec de l\'ajout des fonds');
                Flight::json(['message' => 'Échec de l\'ajout des fonds'], 500);
            }
        } catch (Exception $e) {
            error_log('Erreur serveur: ' . $e->getMessage());
            Flight::json(['message' => 'Erreur serveur: ' . $e->getMessage()], 500);
        }
    }
}