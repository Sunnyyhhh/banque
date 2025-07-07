<?php
require_once __DIR__ . '/../models/Utilisateur.php';
require_once __DIR__ . '/../models/TypePret.php';
require_once __DIR__ . '/../helpers/Utils.php';

class UtilisateurController {

    public static function login() {
        $email = $_POST['email'] ?? '';
        $mot_de_passe = $_POST['mot_de_passe'] ?? '';

        $utilisateur = Utilisateur::login($email);

        //nb de prets valides
        $valides=TypePret::countPretValide();

        //nb de prets non valides
        $non_valides = TypePret::countPretNonValide();

        //nb de types de pret
        $nb_type=TypePret::countTypePret();
        
        if ($utilisateur && $mot_de_passe === $utilisateur['mot_de_passe'])  {
            Flight::json([
                'status' => 'success',
                'utilisateur' => [
                    'id' => $utilisateur['id_utilisateur'],
                    'nom' => $utilisateur['nom'],
                    'prenom' => $utilisateur['prenom'],
                    'email' => $utilisateur['email'],
                    'statut' => $utilisateur['statut'],
                    'solde' => $utilisateur['solde']
                ],
                'nb_valides'=>$valides,
                'nb_non_valides'=>$non_valides,
                'type_pret'=>$nb_type,
            ]);
        } else {
            Flight::json([
                'status' => 'error',
                'message' => 'Email ou mot de passe invalide.'
            ], 401);
        }
    }

}
