<?php
declare(strict_types=1);

namespace Adminlocal\EcoRide\Tests\Services;

use Adminlocal\EcoRide\Services\PaymentService;
use PDO;
use RuntimeException;
use PHPUnit\Framework\TestCase;

final class PaymentServiceTest extends TestCase
{
    private PDO $pdo;

    protected function setUp(): void
    {
        // 1) Crée une base SQLite mémoire
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // 2) Crée la table utilisateurs et transactions
        $this->pdo->exec(<<<SQL
CREATE TABLE utilisateurs (
    utilisateur_id INTEGER PRIMARY KEY,
    role INTEGER NOT NULL,
    credit INTEGER NOT NULL
);
CREATE TABLE transactions (
    transaction_id INTEGER PRIMARY KEY AUTOINCREMENT,
    covoiturage_id INTEGER NOT NULL,
    emetteur_id INTEGER NOT NULL,
    recepteur_id INTEGER NOT NULL,
    montant INTEGER NOT NULL,
    type_transaction TEXT NOT NULL
);
SQL
        );

        // 3) Insère les comptes : passager (id=1), conducteur (2), plateforme (role=1, id=3)
        $stmt = $this->pdo->prepare('INSERT INTO utilisateurs(utilisateur_id, role, credit) VALUES (:id, :role, :credit)');
        $stmt->execute([':id'=>1, ':role'=>0, ':credit'=>100]);
        $stmt->execute([':id'=>2, ':role'=>0, ':credit'=>0]);
        $stmt->execute([':id'=>3, ':role'=>1, ':credit'=>0]);
    }

    public function testProcessRidePaymentUpdatesCreditsAndTransactions(): void
    {
        // On injecte un faux DatabaseConnectionInterface qui retourne notre PDO SQLite
   $dbConnection = new class($this->pdo) implements \Adminlocal\EcoRide\Database\DatabaseConnectionInterface {
       private \PDO $pdo;
       public function __construct(\PDO $pdo) { $this->pdo = $pdo; }
       public function getPdo(): \PDO { return $this->pdo; }
   };
   $service = new PaymentService($dbConnection);

        // Lorsque le passager (1) paie 50 crédits au trajet 42
        $service->processRidePayment(42, 1, 2, 50);

        // 1) Vérifie les crédits
        $credits = $this->pdo->query('SELECT utilisateur_id, credit FROM utilisateurs')->fetchAll(PDO::FETCH_KEY_PAIR);
        // passager : 100 - 50 = 50
        $this->assertSame(50, (int)$credits[1]);
        // conducteur : 0 + (50 - 2) = 48
        $this->assertSame(48, (int)$credits[2]);
        // plateforme : 0 + 2 = 2
        $this->assertSame(2, (int)$credits[3]);

        // 2) Vérifie les lignes dans transactions
        $rows = $this->pdo->query('SELECT covoiturage_id, emetteur_id, recepteur_id, montant, type_transaction FROM transactions ORDER BY transaction_id')->fetchAll(PDO::FETCH_ASSOC);
        $this->assertCount(2, $rows);

        // Ligne de paiement
        $this->assertSame(42, (int)$rows[0]['covoiturage_id']);
        $this->assertSame(1,  (int)$rows[0]['emetteur_id']);
        $this->assertSame(2,  (int)$rows[0]['recepteur_id']);
        $this->assertSame(48, (int)$rows[0]['montant']);
        $this->assertSame('paiement', $rows[0]['type_transaction']);

        // Ligne de commission
        $this->assertSame(42, (int)$rows[1]['covoiturage_id']);
        $this->assertSame(1,  (int)$rows[1]['emetteur_id']);
        $this->assertSame(3,  (int)$rows[1]['recepteur_id']);
        $this->assertSame(2,  (int)$rows[1]['montant']);
        $this->assertSame('commission', $rows[1]['type_transaction']);
    }

    public function testThrowsIfPlatformNotFound(): void
    {
        // Supprime la plateforme
        $this->pdo->exec('DELETE FROM utilisateurs WHERE role = 1');

       // On injecte un objet anonyme implémentant DatabaseConnectionInterface
    $dbConnection = new class($this->pdo) implements \Adminlocal\EcoRide\Database\DatabaseConnectionInterface {
        private \PDO $pdo;
        public function __construct(\PDO $pdo) { $this->pdo = $pdo; }
        public function getPdo(): \PDO { return $this->pdo; }
    };
    $service = new PaymentService($dbConnection);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Compte plateforme introuvable');
        $service->processRidePayment(1, 1, 2, 10);
    }
}
