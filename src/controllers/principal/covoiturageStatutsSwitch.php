<?php
namespace Adminlocal\EcoRide\Controllers\Post;

// 1) Démarrer la session si nécessaire
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// 2) Charger la config PDO
require_once BASE_PATH . '/config/database.php';

// 3) Traitement de la requête POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = (int) $_POST['id'];

    // Récupérer les infos du covoiturage
    $stmtGetStatus = $pdo->prepare(
        "SELECT statut_id, prix_personne, utilisateur 
           FROM covoiturage 
          WHERE covoiturage_id = ?"
    );
    $stmtGetStatus->execute([$id]);
    $covoiturage = $stmtGetStatus->fetch(\PDO::FETCH_ASSOC);

    if (!$covoiturage) {
        exit("Covoiturage introuvable");
    }

    $statutActuel  = (int) $covoiturage['statut_id'];
    $prixPersonne  = (float) $covoiturage['prix_personne'];
    $conducteurId  = (int)   $covoiturage['utilisateur'];

    // Récupérer le pseudo du chauffeur
    $stmtGetPseudo = $pdo->prepare(
        "SELECT pseudo 
           FROM utilisateurs 
          WHERE utilisateur_id = ?"
    );
    $stmtGetPseudo->execute([$conducteurId]);
    $conducteurPseudo = $stmtGetPseudo->fetchColumn();

    // === STATUT 1 → Préparation ou validation
    if ($statutActuel === 1) {
        $stmtCheck = $pdo->prepare(
            "SELECT COUNT(*) 
               FROM reservations 
              WHERE covoiturage_id = ?"
        );
        $stmtCheck->execute([$id]);
        $nbReservations = (int) $stmtCheck->fetchColumn();

        $nouveauStatut = $nbReservations > 0 ? 3 : 2;

        $stmtUpdate = $pdo->prepare(
            "UPDATE covoiturage 
                SET statut_id = ? 
              WHERE covoiturage_id = ?"
        );
        $stmtUpdate->execute([$nouveauStatut, $id]);

        header("Location: /utilisateur");
        exit;
    }

    // === STATUT 3 → En attente d'avis
    elseif ($statutActuel === 3) {
        $stmtCountIns = $pdo->prepare(
            "SELECT COUNT(*) 
               FROM reservations 
              WHERE covoiturage_id = ?"
        );
        $stmtCountIns->execute([$id]);
        $nbPassagers = (int) $stmtCountIns->fetchColumn();

        $stmtCountNotes = $pdo->prepare(
            "SELECT COUNT(DISTINCT passager_id) 
               FROM notes 
              WHERE covoiturage_id = ?"
        );
        $stmtCountNotes->execute([$id]);
        $nbNotes = (int) $stmtCountNotes->fetchColumn();

        $nouveauStatut = ($nbPassagers > 0 && $nbNotes === $nbPassagers)
            ? 4
            : $statutActuel;
    }

    // === STATUT 4 → Finalisation et paiements
    elseif ($statutActuel === 4) {
        $stmtPassagers = $pdo->prepare(
            "SELECT utilisateur_pseudo 
               FROM reservations 
              WHERE covoiturage_id = ?"
        );
        $stmtPassagers->execute([$id]);
        $passagers = $stmtPassagers->fetchAll(\PDO::FETCH_COLUMN);

        $nbPassagers = count($passagers);

        // Débiter chaque passager
        foreach ($passagers as $passagerPseudo) {
            $stmtDebit = $pdo->prepare(
                "UPDATE utilisateurs 
                    SET credit = credit - ? 
                  WHERE pseudo = ?"
            );
            $stmtDebit->execute([$prixPersonne, $passagerPseudo]);
        }

        // Créditer le chauffeur
        if ($nbPassagers > 0) {
            $revenu = ($prixPersonne * $nbPassagers) - (2 * $nbPassagers);
            $stmtCredit = $pdo->prepare(
                "UPDATE utilisateurs 
                    SET credit = credit + ? 
                  WHERE pseudo = ?"
            );
            $stmtCredit->execute([$revenu, $conducteurPseudo]);
        }

        // Créditer l'admin (2 crédits par passager)
        if ($nbPassagers > 0) {
            $gainAdmin = 2 * $nbPassagers;
            $stmtAdmin = $pdo->prepare(
                "UPDATE utilisateurs 
                    SET credit = credit + ? 
                  WHERE role = 1"
            );
            $stmtAdmin->execute([$gainAdmin]);
        }

        $nouveauStatut = 2;

        $stmtUpdate = $pdo->prepare(
            "UPDATE covoiturage 
                SET statut_id = ? 
              WHERE covoiturage_id = ?"
        );
        $stmtUpdate->execute([$nouveauStatut, $id]);

        header("Location: /employe");
        exit;
    }

    // Mise à jour du statut si nécessaire
    if (isset($nouveauStatut) && $nouveauStatut !== $statutActuel) {
        $stmtUpdate = $pdo->prepare(
            "UPDATE covoiturage 
                SET statut_id = ? 
              WHERE covoiturage_id = ?"
        );
        $stmtUpdate->execute([$nouveauStatut, $id]);
    }
}
