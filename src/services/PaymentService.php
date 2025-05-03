<?php
// src/services/PaymentService.php
namespace Adminlocal\EcoRide\services;

use PDO;
use RuntimeException;

class PaymentService
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Traite le paiement d'un covoiturage et historise dans transactions.
     *
     * @param int $covoiturageId ID du covoiturage
     * @param int $passagerId    ID de l'utilisateur qui paie
     * @param int $conducteurId  ID de l'utilisateur qui reçoit
     * @param int $totalCredits  Nombre de crédits payés par le passager
     * @throws RuntimeException si le compte plateforme n'existe pas
     * @throws \PDOException en cas d'erreur SQL
     */
    public function processRidePayment(
        int $covoiturageId,
        int $passagerId,
        int $conducteurId,
        int $totalCredits
    ): void {
        // 1) Calcul de la commission et de la part du chauffeur
        $commission  = 2;
        $driverShare = $totalCredits - $commission;

        // 2) Récupérer l'ID du compte plateforme (role = 1)
        $stmt = $this->pdo->query(
            "SELECT utilisateur_id
               FROM utilisateurs
              WHERE role = 1
              LIMIT 1"
        );
        $platform = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$platform) {
            throw new RuntimeException("Compte plateforme introuvable (role = 1).");
        }
        $platformId = (int) $platform['utilisateur_id'];

        // 3) Mise à jour des crédits
        $upd = $this->pdo->prepare(
            "UPDATE utilisateurs
               SET credit = credit - :amount
             WHERE utilisateur_id = :uid"
        );

        // a) Débiter le passager
        $upd->execute([
            ':amount' => $totalCredits,
            ':uid'    => $passagerId,
        ]);

        // b) Créditer le chauffeur
        $upd->execute([
            ':amount' => -$driverShare,
            ':uid'    => $conducteurId,
        ]);

        // c) Créditer la plateforme
        $upd->execute([
            ':amount' => -$commission,
            ':uid'    => $platformId,
        ]);

        // 4) Insertion dans transactions
        $ins = $this->pdo->prepare(
            "INSERT INTO transactions
                (covoiturage_id, emetteur_id, recepteur_id, montant, type_transaction)
            VALUES
                (:ride, :sender, :receiver, :montant, :type)"
        );

        // a) Ligne de paiement (passager → conducteur)
        $ins->execute([
            ':ride'     => $covoiturageId,
            ':sender'   => $passagerId,
            ':receiver' => $conducteurId,
            ':montant'  => $driverShare,
            ':type'     => 'paiement',
        ]);

        // b) Ligne de commission (passager → plateforme)
        $ins->execute([
            ':ride'     => $covoiturageId,
            ':sender'   => $passagerId,
            ':receiver' => $platformId,
            ':montant'  => $commission,
            ':type'     => 'commission',
        ]);
    }
}
