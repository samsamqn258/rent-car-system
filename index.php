<?php
require_once 'configs/config.php';
require_once 'configs/database.php';

// Establish database connection
$database = new Database();
$db = $database->getConnection();

// Get the request URI
$request_uri = $_SERVER['REQUEST_URI'];
$base_uri = str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']);
$route = str_replace($base_uri, '', $request_uri);

// Remove query string from route
if (strpos($route, '?') !== false) {
    $route = substr($route, 0, strpos($route, '?'));
}

// Parse route segments
$segments = explode('/', trim($route, '/'));
$controller_name = !empty($segments[0]) ? $segments[0] : 'home';
$action = !empty($segments[1]) ? $segments[1] : 'index';
$param = !empty($segments[2]) ? $segments[2] : null;
$param2 = !empty($segments[3]) ? $segments[3] : null;

// Route the request to the appropriate controller
switch ($controller_name) {
    case 'auth':
        require_once 'controllers/AuthController.php';
        $controller = new AuthController($db);

        switch ($action) {
            case 'login':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $controller->login();
                } else {
                    $controller->showLoginForm();
                }
                break;
            case 'register':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $controller->register();
                } else {
                    $controller->showRegisterForm();
                }
                break;
            case 'register_owner':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $controller->registerOwner();
                } else {
                    $controller->showOwnerRegisterForm();
                }
                break;
            case 'logout':
                $controller->logout();
                break;
            default:
                http_response_code(404);
                echo "Page not found";
                break;
        }
        break;

    case 'cars':
        require_once 'controllers/CarController.php';
        $controller = new CarController($db);

        switch ($action) {
            case 'search':
                $controller->search();
                break;
            case 'searchAddress':
                $controller->searchAddress();
                break;
            case 'details':
                $controller->details($param);
                break;
            case 'add':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $controller->addCar();
                } else {
                    $controller->showAddForm();
                }
                break;
            case 'edit':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $controller->updateCar($param);
                } else {
                    $controller->showEditForm($param);
                }
                break;
            case 'approve':
                $controller->approveCar($param);
                break;
            case 'reject':
                $controller->rejectCar($param);
                break;
            default:
                http_response_code(404);
                echo "Page not found";
                break;
        }
        break;

    case 'booking':
        require_once 'controllers/BookingController.php';
        $controller = new BookingController($db);

        switch ($action) {
            case 'create':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $controller->createBooking($param);
                } else {
                    $controller->showBookingForm($param);
                }
                break;
            case 'payment':
                $controller->showPaymentPage($param);
                break;
            case 'process_payment':
                $controller->processPayment($param);
                break;
            case 'callback':
                $controller->paymentCallback();
                break;
            case 'details':
                $controller->showBookingDetails($param);
                break;
            case 'update_status':
                $controller->updateBookingStatus($param);
                break;
            case 'cancel':
                $controller->cancelBooking($param);
                break;
            default:
                http_response_code(404);
                echo "Page not found";
                break;
        }
        break;

    case 'review':
        require_once 'controllers/ReviewController.php';
        $controller = new ReviewController($db);

        switch ($action) {
            case 'create':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $controller->createReview($param);
                } else {
                    $controller->showReviewForm($param);
                }
                break;
            case 'car':
                $controller->getCarReviews($param);
                break;
            default:
                http_response_code(404);
                echo "Page not found";
                break;
        }
        break;

    case 'user':
        require_once 'controllers/UserController.php';
        $controller = new UserController($db);

        switch ($action) {
            case 'profile':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $controller->updateProfile();
                } else {
                    $controller->showProfile();
                }
                break;
            case 'bookings':
                require_once 'controllers/BookingController.php';
                $booking_controller = new BookingController($db);
                $booking_controller->userBookings();
                break;
            case 'change_password':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $controller->changePassword();
                } else {
                    $controller->showChangePasswordForm();
                }
                break;
            default:
                http_response_code(404);
                echo "Page not found";
                break;
        }
        break;

    case 'owner':
        require_once 'controllers/OwnerController.php';
        $controller = new OwnerController($db);

        switch ($action) {
            case 'cars':
                $controller->manageCars();
                break;
            case 'bookings':
                require_once 'controllers/BookingController.php';
                $booking_controller = new BookingController($db);
                $booking_controller->ownerBookings();
                break;
            case 'contracts':
                $controller->manageContracts();
                break;
            case 'add_contract':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $controller->createContract();
                }
                break;
            case 'revenue':
                $controller->viewRevenue();
                break;
            default:
                http_response_code(404);
                echo "Page not found";
                break;
        }
        break;

    case 'admin':
        require_once 'controllers/AdminController.php';
        $controller = new AdminController($db);

        switch ($action) {
            case 'dashboard':
                $controller->dashboard();
                break;
            case 'users':
                $controller->manageUsers();
                break;
            case 'contracts':
                $controller->carOwnerContractService();
                break;
            case 'cars':
                $controller->manageCars();
                break;
             case 'bookings':
                    $controller->manageBookings();
                    break;
            case 'promotions':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    if ($param == 'add') {
                        $controller->addPromotion();
                    } else if ($param == 'edit') {
                        $controller->updatePromotion($param2);
                    } else if ($param == 'delete') {
                        $controller->deletePromotion($param2);
                    }
                } else {
                    $controller->managePromotions();
                }
                break;
            case 'statistics':
                $controller->viewStatistics();
                break;
            case 'block_user':
                $controller->blockUser($param);
                break;
            case 'unblock_user':
                $controller->unblockUser($param);
                break;
            default:
                http_response_code(404);
                echo "Page not found";
                break;
        }
        break;

    case 'payment':
        require_once 'controllers/PaymentController.php';
        $controller = new PaymentController($db);

        switch ($action) {
            case 'callback':
                $controller->callback();
                break;
            case 'ipn':
                $controller->ipn();
                break;
            default:
                http_response_code(404);
                echo "Page not found";
                break;
        }
        break;

    case 'home':
    default:
        // Home page or 404
        if ($controller_name === 'home') {
            require_once 'controllers/HomeController.php';
            $controller = new HomeController($db);
            $controller->index();
        } else {
            http_response_code(404);
            include 'views/shared/404.php';
        }
        break;
}