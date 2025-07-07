<?php
require_once __DIR__ . '/../models/Utilisateur.php';
require_once __DIR__ . '/../helpers/Utils.php';

class UtilisateurController {

    public static function login() {
        $email = $_POST['email'] ?? '';
        $mot_de_passe = $_POST['mot_de_passe'] ?? '';

        $utilisateur = Utilisateur::login($email);

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
                ]
            ]);
        } else {
            Flight::json([
                'status' => 'error',
                'message' => 'Email ou mot de passe invalide.'
            ], 401);
        }
    }

}
