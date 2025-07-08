<?php
require('fpdf.php');

try {
    $pdo = new PDO("mysql:host=localhost;dbname=systeme_info_matiere", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

class pdf extends FPDF {
    function titretableau() {
        return ["UE", "Intitule", "Credit", "Note/20", "Resultat"];
    }

    function getMention($note) {
        if ($note < 10) return "Insuffisant";
        if ($note < 12) return "P";
        if ($note < 14) return "AB";
        if ($note < 16) return "B";
        if ($note < 18) return "TB";
        return "Excellent";
    }

    function header() {
        $this->Image('logoITU.jpeg',10,6,30);
        $this->SetFont('Arial','B',11);
        $this->Cell(150);
        $this->Cell(30,10,'Annee universitaire 2024-2025',0,0,'C');
        $this->Ln(16);
    }

    function titre() {
        $this->SetTextColor(0, 0, 255);
        $this->SetFont('Arial','B',16);
        $this->Cell(0, 9, 'RELEVE DE NOTES ET RESULTATS', 0, 1, 'C');
        $this->Ln(6);
    }

    function informations($id) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT nom, prenom, date_naissance, id_etudiant, classe FROM eleves WHERE id_etudiant = ?");
        $stmt->execute([$id]);
        $etudiant = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->SetTextColor(0, 0, 0);
        $this->SetFont('Arial','',11);
        $this->Cell(0,6,'Nom :' .$etudiant['nom'],0,1);
        $this->Cell(0,6,'Prenom :' .$etudiant['prenom'],0,1);
        $this->Cell(0,6,'Nee le :' .$etudiant['date_naissance'],0,1);
        $this->Cell(0,6,'Numero inscription :' .$etudiant['id_etudiant'],0,1);
        $this->Cell(0,6,'Inscrit en :' .$etudiant['classe'],0,1);
        $this->Cell(0,6,'a obtenu les notes suivantes :',0,1);
        $this->Ln(12);
    }

    function tableau($id) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM eleves WHERE id_etudiant = ?");
        $stmt->execute([$id]);
        $ligne = $stmt->fetch(PDO::FETCH_ASSOC);
        $classe = $ligne['classe'];

        $stmt = $pdo->prepare("SELECT * FROM matieres WHERE classe = ? AND semestre = ?");
        $stmt->execute([$classe, 1]);
        $matieres_s1 = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $somme_crt = $pdo->prepare("SELECT SUM(NB_CREDITS) FROM MATIERES WHERE CLASSE=? AND SEMESTRE=?");
        $somme_crt->execute([$classe,1]);
        $credits_1 = $somme_crt->fetchAll(PDO::FETCH_ASSOC);

        $notes1 = [];
        $stmt->execute([$classe, 2]);
        $matieres_s2 = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $somme_crt->execute([$classe,2]);
        $credits_2 = $somme_crt->fetchAll(PDO::FETCH_ASSOC);

        $notes2 = [];
        $notesSem = [];
        $titres = $this->titretableau();
        $largeur = [15, 70, 10, 40, 30];
        $this->SetFont('Arial', 'B', 11);
        for ($i = 0; $i < count($titres); $i++) $this->Cell($largeur[$i], 7, $titres[$i], 0, 0, 'C');
        $this->Ln();
        $this->SetFont('Arial', '', 11);

        foreach ($matieres_s1 as $matiere) {
            $this->Cell($largeur[0], 6, $matiere['code_matiere'] ?? '', 0, 0, 'C');
            $this->Cell($largeur[1], 6, $matiere['nom_matiere'] ?? '', 0, 0, 'C');
            $this->Cell($largeur[2], 6, $matiere['nb_credits'] ?? '', 0, 0, 'C');
            $stmt_1 = $pdo->prepare("SELECT * FROM notes WHERE code_matiere=? AND id_etudiant=?");
            $stmt_1->execute([$matiere['code_matiere'], $id]);
            $note_1 = $stmt_1->fetchAll(PDO::FETCH_ASSOC);
            $this->Cell($largeur[3], 6, $note_1[0]['note'] ?? '', 0, 0, 'C');
            $notes1[] = $note_1[0]['note'] * $matiere['nb_credits'];
            $this->Cell($largeur[4], 6, $this->getMention($note_1[0]['note']), 0, 0, 'C');
            $this->Ln();
        }
        $this->Ln(6);
        $this->SetFont('Arial', 'B', 11);
        $this->Cell($largeur[0], 6, "SEMESTRE 1" ?? '', 0, 0, 'C');
        $this->Cell($largeur[1], 6, '', 0, 0, 'C');
        $this->Cell($largeur[2], 6, $credits_1[0]['SUM(NB_CREDITS)'] ?? '', 0, 0, 'C');
        $somme = array_sum($notes1) / $credits_1[0]['SUM(NB_CREDITS)'];
        $this->Cell($largeur[3], 6, $somme ?? '', 0, 0, 'C');
        $notesSem[] = $somme;
        $this->Cell($largeur[4], 6, $this->getMention($somme), 0, 0, 'C');
        $this->Ln(12);
        $this->SetFont('Arial', 'B', 11);
        for ($i = 0; $i < count($titres); $i++) $this->Cell($largeur[$i], 7, $titres[$i], 0, 0, 'C');
        $this->Ln();
        $this->SetFont('Arial', '', 11);

        foreach ($matieres_s2 as $matiere) {
            $this->Cell($largeur[0], 6, $matiere['code_matiere'] ?? '', 0, 0, 'C');
            $this->Cell($largeur[1], 6, $matiere['nom_matiere'] ?? '', 0, 0, 'C');
            $this->Cell($largeur[2], 6, $matiere['nb_credits'] ?? '', 0, 0, 'C');
            $stmt_2 = $pdo->prepare("SELECT * FROM notes WHERE code_matiere=? AND id_etudiant=?");
            $stmt_2->execute([$matiere['code_matiere'], $id]);
            $note_2 = $stmt_2->fetchAll(PDO::FETCH_ASSOC);
            $this->Cell($largeur[3], 6, $note_2[0]['note'] ?? '', 0, 0, 'C');
            $notes2[] = $note_2[0]['note'] * $matiere['nb_credits'];
            $this->Cell($largeur[4], 6, $this->getMention($note_2[0]['note']), 0, 0, 'C');
            $this->Ln();
        }
        $this->Ln(6);
        $this->SetFont('Arial', 'B', 11);
        $this->Cell($largeur[0], 6, "SEMESTRE 2" ?? '', 0, 0, 'C');
        $this->Cell($largeur[1], 6, '', 0, 0, 'C');
        $this->Cell($largeur[2], 6, $credits_2[0]['SUM(NB_CREDITS)'] ?? '', 0, 0, 'C');
        $somme = array_sum($notes2) / $credits_2[0]['SUM(NB_CREDITS)'];
        $this->Cell($largeur[3], 6, $somme ?? '', 0, 0, 'C');
        $notesSem[] = $somme;
        $this->Cell($largeur[4], 6, $this->getMention($somme), 0, 0, 'C');
        $this->Ln(10);
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(0,6,"Resultat general",0,1);
        $this->SetFont('Arial', '', 11);
        $this->Cell(0,7,"Credits :".($credits_2[0]['SUM(NB_CREDITS)']+$credits_1[0]['SUM(NB_CREDITS)']),0,1);
        $this->Cell(0,7,"Moyenne generale :".($sm=array_sum($notesSem)/2),0,1);
        $this->Cell(0,7,"Mention : " . $this->getMention($sm),0,1);
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(0,7,$sm<10?"Recale(e)":"Admis(e)",0,1);
        $this->SetFont('Arial', '', 11);
        $this->Cell(0,7,"Session 03/2025",0,1);
    }

    function footer() {
        $this->SetY(-25);
        $this->SetFont('Arial', '', 11);
        $this->Cell(0,6,'Fait a Antananarivo le 18/03/2025,',0,1,'C');
        $this->SetFont('Arial', 'I', 11);
        $this->Cell(0,6,'Le recteur de l\'IT University',0,1,'C');
    }
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->titre();
$pdf->informations("ETU001234");
$pdf->tableau("ETU001234");
$pdf->Output();
?>