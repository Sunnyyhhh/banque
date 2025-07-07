<?php
require_once __DIR__ . '/../models/Pret.php';
require_once __DIR__ . '/../helpers/Utils.php';
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
    
   

public static function pdfPret() {
    require_once(__DIR__ . '/fpdf186/fpdf.php'); // Chemin absolu à partir de ce fichier

    // Vérifie que toutes les données POST nécessaires sont présentes
    if (!isset($_POST['id_client'], $_POST['id_type_pret'], $_POST['montant'], $_POST['date_pret'], $_POST['duree_mois'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Paramètres manquants']);
        return;
    }

    $id_client = $_POST['id_client'];
    $id_type_pret = $_POST['id_type_pret'];
    $montant = $_POST['montant'];
    $date_pret = $_POST['date_pret'];
    $duree_mois = $_POST['duree_mois'];

    // Tu peux récupérer ces noms depuis la base
    $client_nom = "Nom Client (ID: $id_client)";
    $type_pret = "Type Prêt (ID: $id_type_pret)";

    // Création du PDF
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, 'Résumé du prêt', 0, 1, 'C');

    $pdf->SetFont('Arial', '', 12);
    $pdf->Ln(10);
    $pdf->Cell(0, 10, "Client : $client_nom", 0, 1);
    $pdf->Cell(0, 10, "Type de prêt : $type_pret", 0, 1);
    $pdf->Cell(0, 10, "Montant : $montant Ar", 0, 1);
    $pdf->Cell(0, 10, "Date du prêt : $date_pret", 0, 1);
    $pdf->Cell(0, 10, "Durée : $duree_mois mois", 0, 1);

    // Envoi du PDF dans la réponse
    header('Content-Type: application/pdf');
    $pdf->Output('pret.pdf', 'I');
    exit(); // Important pour éviter que du HTML ne s'ajoute après
}

}