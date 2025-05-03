<?php
declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use Adminlocal\EcoRide\Helpers\MongoHelper;

$collection = MongoHelper::getCollection('reclamations');
$reclamations = $collection->find();

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des réclamations</title>
</head>
<body>
    <h1>Réclamations enregistrées</h1>

    <table border="1" cellpadding="10">
        <thead>
            <tr>
                <th>Commentaire</th>
                <th>Statut ID</th>
                <th>Utilisateur ID</th>
                <th>Utilisateur Concerné</th>
                <th>Covoiturage ID</th>
                <th>Date Signalement</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reclamations as $reclamation): ?>
                <tr>
                    <td><?= htmlspecialchars($reclamation['commentaire'] ?? '') ?></td>
                    <td><?= htmlspecialchars((string)($reclamation['statut_id'] ?? '')) ?></td>
                    <td><?= htmlspecialchars((string)($reclamation['utilisateur_id'] ?? '')) ?></td>
                    <td><?= htmlspecialchars((string)($reclamation['utilisateur_concerne'] ?? '')) ?></td>
                    <td><?= htmlspecialchars((string)($reclamation['covoiturage_id'] ?? '')) ?></td>
                    <td><?= htmlspecialchars($reclamation['date_signal'] ?? '') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
