<?php
// public/index.php

// 1) Définir BASE_PATH comme la racine du projet
if (! defined('BASE_PATH')) {
    define('BASE_PATH', realpath(__DIR__ . '/..'));
}

// 2) Autoload + .env
require_once BASE_PATH . '/vendor/autoload.php';
if (file_exists(BASE_PATH . '/.env')) {
    Dotenv\Dotenv::createImmutable(BASE_PATH)->safeLoad();
}

// 3) Erreurs & session
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 4) Import des helpers et middleware
use function Adminlocal\EcoRide\Helpers\renderError;
use function Adminlocal\EcoRide\Middleware\requireJwtAuth;

// 4) Démarrage de la session (pour CSRF, inactivité…)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 5) Protection CSRF pour les POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submitted    = (string) ($_POST['csrf_token'] ?? '');
    $sessionToken = (string) ($_SESSION['csrf_token'] ?? '');
    error_log(sprintf('CSRF DEBUG — session="%s", submitted="%s"', $sessionToken, $submitted));
    if ($sessionToken === '' || !hash_equals($sessionToken, $submitted)) {
        header('Location: /login?error=csrf');
        exit;
    }
}
// Génération du token CSRF si nécessaire
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// 6) Charger la configuration BDD
try {
    $pdo = require BASE_PATH . '/src/config.php';
} catch (Throwable $e) {
    echo '<h1>Erreur de connexion à la BDD</h1><pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
    exit;
}

// 7) Middleware JWT
require_once BASE_PATH . '/src/Middleware/requireJwtAuth.php';

// 8) Gestion d'inactivité (session still used for CSRF)
$inactive = 600;
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $inactive)) {
    session_unset(); session_destroy();
    header('Location: /inactivite'); exit;
}
$_SESSION['last_activity'] = time();

// === ROUTEUR SIMPLIFIÉ ===
$uri = strtok($_SERVER['REQUEST_URI'], '?');

switch ($uri) {
    case '/login':
        $mainView    = 'forms/login.php';
        $pageTitle   = 'Connexion - EcoRide';
        $extraStyles = [
            '/assets/style/styleFormLogin.css',
            '/assets/style/styleCovoiturage.css',
            '/assets/style/styleIndex.css',
            '/assets/style/styleBarreRecherche.css'
        ];
        break;

    case '/loginPost':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require BASE_PATH . '/src/controllers/post/loginPost.php';
        } else {
            header('Location: /login');
        }
        exit;

    case '/employe':
        // JWT authentication
        requireJwtAuth();
        require BASE_PATH . '/src/controllers/principal/employe.php';
        exit;

    case '/admin':
        requireJwtAuth();
        $hideTitle = true;
        $mainView  = 'views/adminDashboard.php';
        require BASE_PATH . '/src/layout.php';
        exit;

    case '/':
    case '/index':
    case '/index.php':
        $barreRecherche= 'views/barreRecherche.php';
        $mainView      = 'views/accueil.php';
        $pageTitle     = 'Accueil - EcoRide';
        $extraStyles   = ['/assets/style/styleIndex.css','/assets/style/styleBarreRecherche.css'];
        break;

    case '/login':
        $mainView    = 'forms/login.php';
        $pageTitle   = 'Connexion - EcoRide';
        $extraStyles = [
            '/assets/style/styleFormLogin.css',
            '/assets/style/styleCovoiturage.css',
            '/assets/style/styleIndex.css',
            '/assets/style/styleBarreRecherche.css'
        ];
        break;

    case '/loginPost':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once BASE_PATH . '/src/controllers/post/loginPost.php';
        } else {
            header('Location: /login');
        }
        exit;

    case '/registerForm':
        $mainView    = 'views/registerForm.php';
        $pageTitle   = 'Créer un compte - EcoRide';
        $extraStyles = ['/assets/style/styleFormLogin.css'];
        break;

    case '/registerPost':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once BASE_PATH . '/src/controllers/post/registerPost.php';
        } else {
            renderError(405);
        }
        exit;

    // --- Routes privées (JWT) ---
    case '/employe':
        requireJwtAuth();
        require_once BASE_PATH . '/src/controllers/principal/employe.php';
        exit;

    case '/admin':
        requireJwtAuth();
        $hideTitle = true;
        $mainView  = 'views/adminDashboard.php';
        $extraStyles = ['/assets/style/styleIndex.css'];
        break;

    case '/notes-a-valider':
        requireJwtAuth();
        require_once BASE_PATH . '/src/controllers/principal/notesAVerifier.php';
        exit;

    case '/detail-covoiturage':
        requireJwtAuth();
        require_once BASE_PATH . '/src/controllers/principal/detailCovoiturage.php';
        exit;

    case '/delete-covoiturage':
        requireJwtAuth();
        require_once BASE_PATH . '/src/controllers/principal/deleteCovoiturage.php';
        exit;

    case '/covoiturageForm':
        requireJwtAuth();
        $mainView    = 'forms/covoiturageForm.php';
        $pageTitle   = 'Créer un covoiturage - EcoRide';
        $extraStyles = ['/assets/style/styleFormLogin.css', '/assets/style/styleIndex.css'];
        break;

    case '/vehiculeForm':
        requireJwtAuth();
        $mainView    = 'views/vehiculeForm.php';
        $pageTitle   = 'Ajouter une voiture - EcoRide';
        $extraStyles = ['/assets/style/styleFormLogin.css', '/assets/style/styleBigTitle.css', '/assets/style/styleIndex.css'];
        break;

    case '/covoiturage':
        requireJwtAuth();
        $barreRecherche = 'views/barreRecherche.php';
        $mainView       = 'views/covoiturage.php';
        $pageTitle      = 'Rechercher un covoiturage - EcoRide';
        $extraStyles    = [
            '/assets/style/styleFormLogin.css',
            '/assets/style/styleCovoiturage.css',
            '/assets/style/styleIndex.css',
            '/assets/style/styleBarreRecherche.css'
        ];
        break;

    case '/covoiturageStatutsSwitch':
        requireJwtAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require BASE_PATH . '/src/controllers/principal/statutsSwitch.php';
            exit;
        }
        break;

    case '/stats/data1':
        requireJwtAuth();
        header('Content-Type: application/json');
        require_once BASE_PATH . '/src/controllers/principal/data1.php';
        exit;

    case '/stats/data2':
        requireJwtAuth();
        header('Content-Type: application/json');
        require_once BASE_PATH . '/src/controllers/principal/data2.php';
        exit;

    case '/stats':
    case '/stats/':
        requireJwtAuth();
        $mainView    = 'views/stats.php';
        $pageTitle   = 'Statistiques - EcoRide';
        $extraStyles = ['/assets/style/styleIndex.css', '/assets/style/styleCovoiturage.css'];
        break;

    case '/reclamationForm':
        requireJwtAuth();
        $mainView    = 'controllers/principal/reclamationForm.php';
        $pageTitle   = 'Signaler un problème - EcoRide';
        $extraStyles = ['/assets/style/styleFormLogin.css'];
        break;

    case '/reclamationPost':
        requireJwtAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require BASE_PATH . '/src/controllers/post/reclamationPost.php';
            exit;
        } else {
            renderError(405);
        }
        break;

    case '/reclamations-problemes':
        requireJwtAuth();
        require_once BASE_PATH . '/src/controllers/principal/reclamationsProblemes.php';
        exit;

    case '/reclamationTraitee':
        requireJwtAuth();
        require_once BASE_PATH . '/src/controllers/post/reclamationTraitee.php';
        exit;

    case '/reclamationResolue':
        requireJwtAuth();
        require_once BASE_PATH . '/src/controllers/post/reclamationResolue.php';
        exit;

    case '/participerCovoiturage':
        requireJwtAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require BASE_PATH . '/src/controllers/principal/participerCovoiturage.php';
            exit;
        }
        break;

    case '/updateRolePost':
        requireJwtAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require BASE_PATH . '/src/controllers/post/updateRolePost.php';
            exit;
        } else {
            renderError(405);
        }
        break;

    case '/confirmerTrajet':
        requireJwtAuth();
        require BASE_PATH . '/src/controllers/post/confirmerTrajet.php';
        exit;

    case '/noteForm':
        requireJwtAuth();
        $mainView    = 'views/noteForm.php';
        $pageTitle   = 'Notez votre covoiturage - EcoRide';
        $extraStyles = [
            '/assets/style/styleFormLogin.css',
            '/assets/style/styleCovoiturage.css',
            '/assets/style/styleIndex.css'
        ];
        break;

    case '/notePost':
        requireJwtAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once BASE_PATH . '/src/controllers/post/notePost.php';
            exit;
        } else {
            renderError(405);
        }
        break;

    case '/toggleAvisStatut':
        requireJwtAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require BASE_PATH . '/src/controllers/post/toggleAvisStatut.php';
            exit;
        } else {
            renderError(405);
        }
        break;

    case '/covoiturageDemarrer':
        requireJwtAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require BASE_PATH . '/src/controllers/post/covoiturageDemarrer.php';
            exit;
        } else {
            renderError(405);
        }
        break;

    case '/covoiturageTerminer':
        requireJwtAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require BASE_PATH . '/src/controllers/post/covoiturageTerminer.php';
            exit;
        } else {
            renderError(405);
        }
        break;

    case '/covoiturageAnnuler':
        requireJwtAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require BASE_PATH . '/src/controllers/post/covoiturageAnnuler.php';
            exit;
        } else {
            renderError(405);
        }
        break;

    case '/deleteVoiture':
        requireJwtAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            renderError(405);
        }
        require_once BASE_PATH . '/src/controllers/principal/deleteVoiture.php';
        exit;

    case '/deconnexion':
        requireJwtAuth();
        require_once BASE_PATH . '/src/controllers/principal/deconnexion.php';
        exit;

    case '/inactivite':
        $mainView    = 'views/inactivite.php';
        $pageTitle   = 'Inactivité - EcoRide';
        $extraStyles = ['/assets/style/styleIndex.css'];
        break;

    case '/suspendu':
        $mainView    = 'views/suspendu.php';
        $pageTitle   = 'Compte suspendu - EcoRide';
        $extraStyles = ['/assets/style/styleIndex.css'];
        break;

    case '/utilisateur':
        requireJwtAuth();
        require_once __DIR__ . '/utilisateur.php';
        exit;

    case '/modifCompteForm':
        requireJwtAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            header('Location: /admin');
            exit;
        }
        require BASE_PATH . '/src/forms/modifCompteForm.php';
        exit;

    case '/modifCompteAction':
        requireJwtAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin');
            exit;
        }
        require BASE_PATH . '/src/forms/modifCompteAction.php';
        exit;

    case '/compteur_api.php':
        // exécuter sans layout
        require BASE_PATH . '/public/compteur_api.php';
        exit;

    default:
        renderError(404);
}

// 10) Affichage de la vue ou du layout
if (PHP_SAPI === 'cli') {
    require_once BASE_PATH . '/public/' . $mainView;
} else {
    require_once BASE_PATH . '/src/layout.php';
    exit;
}
