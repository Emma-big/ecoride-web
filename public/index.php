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
$debug = (getenv('APP_DEBUG') === 'true');
ini_set('display_errors', $debug ? '1' : '0');
ini_set('display_startup_errors', $debug ? '1' : '0');
error_reporting($debug ? E_ALL : 0);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 4) Helpers & Middleware
require_once BASE_PATH . '/src/Helpers/ErrorHelper.php';
use function Adminlocal\EcoRide\Helpers\renderError;

require_once BASE_PATH . '/src/Middleware/requireJwtAuth.php';
use function Adminlocal\EcoRide\Middleware\requireJwtAuth;

// 5) CSRF protection pour les POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submitted    = (string) ($_POST['csrf_token'] ?? '');
    $sessionToken = (string) ($_SESSION['csrf_token'] ?? '');
    if ($sessionToken === '' || !hash_equals($sessionToken, $submitted)) {
        header('Location: /login?error=csrf');
        exit;
    }
}
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// 6) Connexion BDD
try {
    $pdo = require BASE_PATH . '/src/config.php';
} catch (Throwable $e) {
    echo '<h1>Erreur BDD</h1><pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
    exit;
}

// 7) Inactivité
$inactive = 600;
if (isset($_SESSION['last_activity']) && time() - $_SESSION['last_activity'] > $inactive) {
    session_unset();
    session_destroy();
    header('Location: /inactivite');
    exit;
}
$_SESSION['last_activity'] = time();

// 8) Dispatcher
$uri = strtok($_SERVER['REQUEST_URI'], '?');

switch ($uri) {
    case '/login':
    require_once BASE_PATH . '/src/forms/login.php';
    exit;

    case '/loginPost':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require BASE_PATH . '/src/controllers/post/loginPost.php';
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

    case '/registerPostEmploye':
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        require_once BASE_PATH . '/src/controllers/post/registerPostEmploye.php';
    } else {
        renderError(405);
    }
    exit;  
        
    case '/registerVehiculePost':
        require_once BASE_PATH . '/src/controllers/post/registerVehiculePost.php';
        exit;  
        
    case '/registerCovoituragePost':
        require_once BASE_PATH . '/src/controllers/post/registerCovoituragePost.php';
        exit;    

    case '/contact':
    // pas de require(layout) ni exit dans le controller !
    require BASE_PATH . '/src/controllers/principal/contact.php';
    break;

    case '/mentions-legales':
        $mainView  = 'views/mentions-legales.php';
        $pageTitle = 'Mentions légales - EcoRide';
        require BASE_PATH . '/src/layout.php';
        exit;

    case '/':
    case '/index':
    case '/index.php':
        $barreRecherche= 'views/barreRecherche.php';
        $gKey           = $_ENV['GOOGLE_API_KEY'] ?? '';
        $mainView      = 'views/accueil.php';
        $pageTitle     = 'Accueil - EcoRide';
        $extraStyles   = ['/assets/style/styleIndex.css','/assets/style/styleBarreRecherche.css'];
        break;

    case '/covoiturage':
        // active la barre de recherche et l’API Google Maps
        $barreRecherche = 'views/barreRecherche.php';
        $gKey           = $_ENV['GOOGLE_API_KEY'] ?? '';
        $mainView       = 'views/covoiturage.php';
        $pageTitle      = 'Rechercher un covoiturage - EcoRide';
        $extraStyles    = [
            '/assets/style/styleFormLogin.css',
            '/assets/style/styleCovoiturage.css',
            '/assets/style/styleIndex.css',
            '/assets/style/styleBarreRecherche.css'
        ];
        break;

    case '/contactPost':
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        require_once BASE_PATH . '/src/controllers/post/contactPost.php';
    } else {
        renderError(405);
    }
    exit; 
    
    case '/confirmationContact':
        // on inclut directement le view, qui fait ob_start() + require layout
        require __DIR__ . '/../src/views/confirmationContact.php';
        // IMPORTANT : ne pas faire `break;` si le view fait déjà le require layout
        exit;
    
    case '/detail-covoiturage':
        require BASE_PATH . '/src/controllers/principal/detailCovoiturage.php';

    case '/covoiturage':
        $pageTitle   = 'Rechercher un covoiturage – EcoRide';
        $extraStyles = ['/assets/style/styleCovoiturage.css'];
        ob_start();
        require BASE_PATH . '/src/views/covoiturage.php';
        $mainContent = ob_get_clean();
        break;  
        
    case '/updateVehiculePost':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once BASE_PATH . '/src/controllers/post/updateVehiculePost.php';
            exit;
        } else {
            renderError(405);
        }
        break; 
        
    case '/confirmation-avis':
        $mainView    = 'views/confirmation-avis.php';
        $pageTitle   = 'Confirmation de l\'avis - EcoRide';
        $extraStyles = [
            '/assets/style/styleFormLogin.css',
            '/assets/style/styleBigTitle.css'
        ];
        break;  
        
    case '/inactivite':
        $pageTitle   = 'Inactivité – EcoRide';
        $extraStyles = ['/assets/style/styleIndex.css'];
        ob_start();
        require BASE_PATH . '/src/views/inactivite.php';
        $mainContent = ob_get_clean();
        break;

    case '/suspendu':
        // Si l’utilisateur n’est pas suspendu (role !== 4), on le redirige vers l’accueil
        if (($_SESSION['user']['role'] ?? null) !== 4) {
        header('Location: /index');
        exit;
        }
        $pageTitle   = 'Compte suspendu – EcoRide';
        $extraStyles = ['/assets/style/styleIndex.css'];
        ob_start();
        require BASE_PATH . '/src/views/suspendu.php';
        $mainContent = ob_get_clean();
        break;  

    case '/modifCompteAction':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin');
            exit;
        }
        ob_start();
        require BASE_PATH . '/src/forms/modifCompteAction.php';
        $mainContent = ob_get_clean();
        break;
        
    case '/compteur_api.php':
        require BASE_PATH . '/public/compteur_api.php';
        exit;    

    // PROTECTED
     case '/employe':
        requireJwtAuth();
        require BASE_PATH . '/src/controllers/principal/employe.php';
        exit;

    case '/admin':
        requireJwtAuth();
        $hideTitle   = true;
        $pageTitle   = 'Admin – EcoRide';
        $extraStyles = ['/assets/style/styleIndex.css'];
        ob_start();
        require BASE_PATH . '/src/views/adminDashboard.php';
        $mainContent = ob_get_clean();
        break;

    case '/notes-a-valider':
        requireJwtAuth();
        $mainView  = 'views/notesAVerifier.php';
        $pageTitle = 'Notes à valider - EcoRide';
        require BASE_PATH . '/src/layout.php';
        exit;
  
    case '/delete-covoiturage':
        requireJwtAuth();
        ob_start();
        require BASE_PATH . '/src/controllers/principal/deleteCovoiturage.php';
        $mainContent = ob_get_clean();
        break;

    case '/covoiturageForm':
        requireJwtAuth();
        $pageTitle   = 'Créer un covoiturage – EcoRide';
        $extraStyles = [
            '/assets/style/styleFormLogin.css',
            '/assets/style/styleIndex.css'
        ];
        ob_start();
        require BASE_PATH . '/src/forms/covoiturageForm.php';
        $mainContent = ob_get_clean();
        break;

    case '/vehiculeForm':
        requireJwtAuth();
        require BASE_PATH . '/src/views/vehiculeForm.php';
        exit;

    case '/covoiturageStatutsSwitch':
        requireJwtAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require BASE_PATH . '/src/controllers/principal/statutsSwitch.php';
            exit;
        }
        renderError(405);
        break;

    case '/stats/data1':
        requireJwtAuth();
        header('Content-Type: application/json');
        require BASE_PATH . '/src/controllers/principal/data1.php';
        exit;

    case '/stats/data2':
        requireJwtAuth();
        header('Content-Type: application/json');
        require BASE_PATH . '/src/controllers/principal/data2.php';
        exit;

    case '/stats':
    case '/stats/':
        requireJwtAuth();
        $pageTitle   = 'Statistiques – EcoRide';
        $extraStyles = ['/assets/style/styleIndex.css'];
        ob_start();
        require BASE_PATH . '/src/views/stats.php';
        $mainContent = ob_get_clean();
        break;

    case '/reclamationForm':
        requireJwtAuth();
        $pageTitle   = 'Signaler un problème – EcoRide';
        $extraStyles = ['/assets/style/styleFormLogin.css'];
        ob_start();
        require BASE_PATH . '/src/controllers/principal/reclamationForm.php';
        $mainContent = ob_get_clean();
        break;

    case '/reclamationPost':
        requireJwtAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require BASE_PATH . '/src/controllers/post/reclamationPost.php';
        } else {
            renderError(405);
        }
        exit;

    case '/reclamations-problemes':
        requireJwtAuth();
        ob_start();
        require BASE_PATH . '/src/controllers/principal/reclamationsProblemes.php';
        $mainContent = ob_get_clean();
        break;

    case '/reclamationTraitee':
        requireJwtAuth();
        ob_start();
        require BASE_PATH . '/src/controllers/post/reclamationTraitee.php';
        $mainContent = ob_get_clean();
        break;

    case '/reclamationResolue':
        requireJwtAuth();
        ob_start();
        require BASE_PATH . '/src/controllers/post/reclamationResolue.php';
        $mainContent = ob_get_clean();
        break;

    case '/participerCovoiturage':
        requireJwtAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            ob_start();
            require BASE_PATH . '/src/controllers/principal/participerCovoiturage.php';
            $mainContent = ob_get_clean();
        } else {
            renderError(405);
        }
        break;

    case '/updateRolePost':
        requireJwtAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            ob_start();
            require BASE_PATH . '/src/controllers/post/updateRolePost.php';
            $mainContent = ob_get_clean();
        } else {
            renderError(405);
        }
        break;
     
    case '/confirmerTrajet':
        requireJwtAuth();
        ob_start();
        require BASE_PATH . '/src/controllers/post/confirmerTrajet.php';
        $mainContent = ob_get_clean();
        break;

    case '/noteForm':
        requireJwtAuth();
        $pageTitle   = 'Notez votre covoiturage – EcoRide';
        $extraStyles = ['/assets/style/styleFormLogin.css'];
        ob_start();
        require BASE_PATH . '/src/views/noteForm.php';
        $mainContent = ob_get_clean();
        break;

    case '/notePost':
        requireJwtAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            ob_start();
            require BASE_PATH . '/src/controllers/post/notePost.php';
            $mainContent = ob_get_clean();
        } else {
            renderError(405);
        }
        break;

    case '/toggleAvisStatut':
        requireJwtAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            ob_start();
            require BASE_PATH . '/src/controllers/post/toggleAvisStatut.php';
            $mainContent = ob_get_clean();
        } else {
            renderError(405);
        }
        break;

    case '/covoiturageDemarrer':
        requireJwtAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            ob_start();
            require BASE_PATH . '/src/controllers/post/covoiturageDemarrer.php';
            $mainContent = ob_get_clean();
        } else {
            renderError(405);
        }
        break;

    case '/covoiturageTerminer':
        requireJwtAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            ob_start();
            require BASE_PATH . '/src/controllers/post/covoiturageTerminer.php';
            $mainContent = ob_get_clean();
        } else {
            renderError(405);
        }
        break;

    case '/covoiturageAnnuler':
        requireJwtAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            ob_start();
            require BASE_PATH . '/src/controllers/post/covoiturageAnnuler.php';
            $mainContent = ob_get_clean();
        } else {
            renderError(405);
        }
        break;

    case '/deleteVoiture':
        requireJwtAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            renderError(405);
        }
        ob_start();
        require BASE_PATH . '/src/controllers/principal/deleteVoiture.php';
        $mainContent = ob_get_clean();
        break;

    case '/deconnexion':
        requireJwtAuth();
        ob_start();
        require BASE_PATH . '/src/controllers/principal/deconnexion.php';
        $mainContent = ob_get_clean();
        break;

    case '/utilisateur':
        requireJwtAuth();
        ob_start();
        require BASE_PATH . '/public/utilisateur.php';
        $mainContent = ob_get_clean();
        break;

    case '/modifCompteForm':
        requireJwtAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        header('Location: /admin');
        exit;
        }
        ob_start();
        require BASE_PATH . '/src/forms/modifCompteForm.php';
        $mainContent = ob_get_clean();
        $pageTitle   = 'Modifier mon compte – EcoRide';
        $extraStyles = ['/assets/style/styleFormLogin.css'];
        break;
      
    default:
        renderError(404);
}

// 9) Affichage générique
require_once BASE_PATH . '/src/layout.php';
