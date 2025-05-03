<?php
declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use Adminlocal\EcoRide\Helpers\MongoHelper;

// Vérification basique (à améliorer si besoin)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $commentaire = $_POST['commentaire'] ?? '';
    $statut_id = (int) ($_POST['statut_id'] ?? 1);
    $utilisateur_id = (int) ($_POST['utilisateur_id'] ?? 0);
    $utilisateur_concerne = (int) ($_POST['utilisateur_concerne'] ?? 0);
    $covoiturage_id = (int) ($_POST['covoiturage_id'] ?? 0);
    $date_signal = date('Y-m-d H:i:s'); // Date du serveur

    if (!empty($commentaire) && $utilisateur_id > 0) {
        $collection = MongoHelper::getCollection('reclamations');

        $collection->insertOne([
            'commentaire' => $commentaire,
            'statut_id' => $statut_id,
            'utilisateur_id' => $utilisateur_id,
            'utilisateur_concerne' => $utilisateur_concerne,
            'covoiturage_id' => $covoiturage_id,
            'date_signal' => $date_signal,
        ]);

        echo "Réclamation enregistrée avec succès.";
    } else {
        echo "Erreur : données invalides.";
    }
} else {
    echo "Erreur : méthode non autorisée.";
}
