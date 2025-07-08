<?php
require_once __DIR__ . '/../models/Pret.php';
require_once __DIR__ . '/../helpers/Utils.php';
require_once __DIR__ . '/../models/TypePret.php';
require_once __DIR__ . '/../models/Etablissement.php';

class PretController {
    public static function insertionPret() {
        try{
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
        $id = Flight::request()->data->id;
        if (!$id) {
            Flight::json(['message' => 'ID manquant'], 400);
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

        $montant = $pret['montant'];
        Etablissement::addFonds(0, -$montant); 

        Pret::approuverPret($id);

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

    public function rembourserPret() {
        try {
            $data = Flight::request()->data;

            if (!isset($data->id) || !isset($data->id_type_pret)) {
                Flight::json(['message' => 'ID ou ID de type de prêt manquant'], 400);
                return;
            }

            //avoir les donnees du type pret
            $donnees_typePret=TypePret::getTypePretById($data->id_type_pret);
            $taux=$donnees_typePret['taux']/12;
            $nb_annees=$donnees_typePret['nb_annees'];

            //avoir la somme empruntee
            $somme_empruntee=$data->$montant;

            //calcul de l'amortissement
            $ammortissement = ($somme_empruntee * $taux) / (1 - pow((1 + $taux), -$nb_annees));

            //rembourser pret a chaque fois
            Pret::rembourserPret($data->id, $data->id_type_pret);
            Flight::json(['message' => 'Prêt remboursé avec succès'], 200);
        } catch (Throwable $e) {
            Flight::json(['message' => 'Erreur lors du remboursement: ' . $e->getMessage()], 500);
        }
    }

}