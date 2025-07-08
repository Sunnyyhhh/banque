<?php
require_once __DIR__ . '/../models/Remboursement.php';
require_once __DIR__ . '/../helpers/Utils.php';

class RemboursementController {
    public static function getAll($id) {
        $remboursements = Remboursement::getRemboursements($id);
        Flight::json($remboursements);
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
}