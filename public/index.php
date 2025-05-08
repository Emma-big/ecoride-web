<?php
// STEP 1
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// 1) Démarrage de la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2) Définir BASE_PATH comme la racine du projet
if (! defined('BASE_PATH')) {
    define('BASE_PATH', realpath(__DIR__ . '/..'));
}

// 3) Charger le helper d’erreur
require_once BASE_PATH . '/src/Helpers/ErrorHelper.php';
use function Helpers\renderError;

// Gestionnaire des exceptions non capturées → page 500
// (On commente temporairement pendant le debug)
// set_exception_handler(function(\Throwable $e) {
//     renderError(500);
// });

// Gestionnaire des erreurs PHP → transforme en Exception
// set_error_handler(function($severity, $message, $file, $line) {
//     throw new \ErrorException($message, 0, $severity, $file, $line);
// });

// 3.1) Protection CSRF pour toutes les requêtes POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['csrf_token']) || ! hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        renderError(403);
    }
}
// 3.2) Générer un token unique si nécessaire
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// 3.3) Charger l’autoloader Composer et Dotenv AVANT la config
if (file_exists(BASE_PATH . '/vendor/autoload.php')) {
    require_once BASE_PATH . '/vendor/autoload.php';
    Dotenv\Dotenv::createImmutable(BASE_PATH)->safeLoad();
}

// 4) Charger la configuration (PDO, MongoDB…)
$pdo = require BASE_PATH . '/src/config.php';

// STEP 2 : test de la connexion PDO
die('STEP 2 — $pdo is a '. get_class($pdo));

// 5) Gestion de l'inactivité (10 minutes)
$inactive_duration = 600;
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $inactive_duration)) {
    session_unset();
    session_destroy();
    header('Location: /inactivite');
    exit;
}
$_SESSION['last_activity'] = time();

// === ROUTEUR SIMPLIFIÉ ===
$uri = strtok($_SERVER['REQUEST_URI'], '?');

// DEBUG temporaire
file_put_contents(__DIR__.'/../logs/route.log', date('c').' → '.$_SERVER['HTTP_HOST'].' '.$_SERVER['REQUEST_URI'].PHP_EOL, FILE_APPEND);

switch ($uri) {
    case '/':
    case '/index':
    case '/index.php':
        $mainView = 'views/accueil.php';
        $barreRecherche = 'views/barreRecherche.php';
        $pageTitle = 'Accueil - EcoRide';
        $extraStyles = ['/assets/style/styleIndex.css', '/assets/style/styleBarreRecherche.css'];
        break;

    case '/login':
        $mainView = 'forms/login.php';
        $pageTitle = 'Connexion - EcoRide';
        $extraStyles = ['/assets/style/styleFormLogin.css', '/assets/style/styleCovoiturage.css', '/assets/style/styleIndex.css', '/assets/style/styleBarreRecherche.css'];
        break;

    case '/admin':
        require_once __DIR__ . '/admin.php';
        exit;

    case '/modifCompteForm':
        $mainView = 'forms/modifCompteForm.php';
        $pageTitle = 'Modifier un compte - EcoRide';
        $extraStyles = ['/assets/style/styleFormLogin.css'];
        break;

    case '/employe':
        require_once BASE_PATH . '/src/controllers/principal/employe.php';
        exit;

    case '/registerForm':
        $mainView = 'views/registerForm.php';
        $pageTitle = 'Créer un compte - EcoRide';
        $extraStyles = ['/assets/style/styleFormLogin.css'];
        break;

    case '/covoiturageForm':
        $mainView = 'forms/covoiturageForm.php';
        $pageTitle = 'Créer un covoiturage - EcoRide';
        $extraStyles = ['/assets/style/styleFormLogin.css', '/assets/style/styleIndex.css'];
        break;

    case '/vehiculeForm':
        $mainView = 'views/vehiculeForm.php';
        $pageTitle = 'Ajouter une voiture - EcoRide';
        $extraStyles = ['/assets/style/styleFormLogin.css', '/assets/style/styleBigTitle.css', '/assets/style/styleIndex.css'];
        break;

    case '/covoiturage':
        $mainView = 'views/covoiturage.php';
        $barreRecherche = 'views/barreRecherche.php';
        $pageTitle = 'Rechercher un covoiturage - EcoRide';
        $extraStyles = ['/assets/style/styleFormLogin.css', '/assets/style/styleCovoiturage.css', '/assets/style/styleIndex.css', '/assets/style/styleBarreRecherche.css'];
        break;

    case '/detail-covoiturage':
        require_once BASE_PATH . '/src/controllers/principal/detailCovoiturage.php';
        exit;

    case '/delete-covoiturage':
        require_once BASE_PATH . '/src/controllers/principal/deleteCovoiturage.php';
        exit;

    case '/covoiturageStatutsSwitch':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require BASE_PATH . '/src/controllers/principal/statutsSwitch.php';
            exit;
        }
        break;

    case '/statutsSwitch':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require BASE_PATH . '/src/controllers/post/statutsSwitch.php';
            exit;
        }
        break;

    case '/stats/data1':
        header('Content-Type: application/json');
        require_once BASE_PATH . '/src/controllers/principal/data1.php';
        exit;
        
    case '/stats/data2':
        header('Content-Type: application/json');
        require_once BASE_PATH . '/src/controllers/principal/data2.php';
        exit;    

    case '/stats':
    case '/stats/':
        $mainView = 'views/stats.php';
        $pageTitle = 'Statistiques - EcoRide';
        $extraStyles = ['/assets/style/styleIndex.css', '/assets/style/styleCovoiturage.css'];
        break;

    case '/mentions-legales':
        $mainView = 'views/mentions-legales.php';
        $pageTitle = 'Mentions Légales - EcoRide';
        $extraStyles = ['/assets/style/styleIndex.css'];
        break;

    case '/contact':
        $mainView = 'views/contact_form.php';
        $pageTitle = 'Contact - EcoRide';
        $extraStyles = ['/assets/style/styleFormLogin.css', '/assets/style/styleCovoiturage.css', '/assets/style/styleIndex.css', '/assets/style/styleBarreRecherche.css'];
        break;

    case '/contactPost':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once BASE_PATH . '/src/controllers/post/contactPost.php';
            exit;
        }
        break;

    case '/confirmationContact':
        $mainView = 'views/confirmationContact.php';
        $pageTitle = 'Confirmation de contact';
        $extraStyles = ['/assets/style/styleFormLogin.css'];
        break;

    case '/registerCovoituragePost':
        require_once BASE_PATH . '/src/controllers/post/registerCovoituragePost.php';
        exit;

    case '/registerVehiculePost':
        require_once BASE_PATH . '/src/controllers/post/registerVehiculePost.php';
        exit;

    case '/updateVehiculePost':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once BASE_PATH . '/src/controllers/post/updateVehiculePost.php';
            exit;
        } else {
            renderError(405);
        }
        break;        

    case '/registerPost':
        require_once BASE_PATH . '/src/controllers/post/registerPost.php';
        exit;

    case '/registerPostEmploye':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once BASE_PATH . '/src/controllers/post/registerPostEmploye.php';
            exit;
        } else {
            renderError(405);
        }
        break;

    case '/loginPost':
        require_once BASE_PATH . '/src/controllers/post/loginPost.php';
        exit;

    case '/deconnexion':
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
    
    case '/participerCovoiturage':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require BASE_PATH . '/src/controllers/principal/participerCovoiturage.php';
            exit;
        }
        break;
    
    case '/updateRolePost':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require BASE_PATH . '/src/controllers/post/updateRolePost.php';
            exit;
        } else {
            renderError(405);
        }
        break;
    
    case '/reclamationForm':
        $mainView  = 'controllers/principal/reclamationForm.php';
        $pageTitle = 'Signaler un problème - EcoRide';
        $extraStyles = ['/assets/style/styleFormLogin.css'];
        break;
    
    case '/reclamationPost':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require BASE_PATH . '/src/controllers/post/reclamationPost.php';
            exit;
        } else {
            renderError(405);
        }
        break;
    
    case '/reclamations-problemes':
        require_once BASE_PATH . '/src/controllers/principal/reclamationsProblemes.php';
        exit;
    
    case '/reclamationTraitee':
        require_once BASE_PATH . '/src/controllers/post/reclamationTraitee.php';
        exit;
    
    case '/reclamationResolue':
        require_once BASE_PATH . '/src/controllers/post/reclamationResolue.php';
        exit;
    
    case '/confirmerTrajet':
        require BASE_PATH . '/src/controllers/post/confirmerTrajet.php';
        exit;
    
    case '/noteForm':
        $mainView    = 'views/noteForm.php';
        $pageTitle   = 'Notez votre covoiturage - EcoRide';
        $extraStyles = [
            '/assets/style/styleFormLogin.css',
            '/assets/style/styleCovoiturage.css',
            '/assets/style/styleIndex.css'
        ];
        break;
    
    case '/confirmation-avis':
        $mainView    = 'views/confirmation-avis.php';
        $pageTitle   = 'Confirmation de l\'avis - EcoRide';
        $extraStyles = [
            '/assets/style/styleFormLogin.css',
            '/assets/style/styleBigTitle.css'
        ];
        break;
    

    case '/notePost':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once BASE_PATH . '/src/controllers/post/notePost.php';
            exit;
        } else {
            renderError(405);
        }
        break;

    case '/toggleAvisStatut':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require BASE_PATH . '/src/controllers/post/toggleAvisStatut.php';
            exit;
        } else {
            renderError(405);
        }
        break;

    case '/covoiturageDemarrer':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require BASE_PATH . '/src/controllers/post/covoiturageDemarrer.php';
            exit;
        } else {
            renderError(405);
        }
        break;

    case '/covoiturageTerminer':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require BASE_PATH . '/src/controllers/post/covoiturageTerminer.php';
            exit;
        } else {
            renderError(405);
        }
        break;

    case '/notes-a-valider':
        if (empty($_SESSION['user']) || ($_SESSION['user']['role'] ?? 0) != 2) {
            renderError(403);
        }
        require_once BASE_PATH . '/src/controllers/principal/notesAVerifier.php';
        exit;

    case '/covoiturageAnnuler':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require BASE_PATH . '/src/controllers/post/covoiturageAnnuler.php';
            exit;
        } else {
            renderError(405);
        }
        break;

        case '/deleteVoiture':
            // Seule la méthode POST est autorisée
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                renderError(405);
            }
        
            // Le CSRF est déjà vérifié en amont pour toute requête POST
            require_once BASE_PATH . '/src/controllers/principal/deleteVoiture.php';
            exit;
    
    default:
        renderError(404);
    }

// 6) Affichage du layout global
require_once BASE_PATH . '/src/layout.php';
exit;
