<?php
require_once 'services/CarService.php';
require_once 'utils/Validator.php';
require_once 'services/BookingService.php';
class CarController
{
    private $carService;
    private $validator;
    private $bookingService;
    public function __construct($db)
    {
        $this->carService = new CarService($db);
        $this->validator = new Validator();
        $this->bookingService = new BookingService($db);
    }

    // Display car search page
    public function search()
    {
        // Get filter parameters from request
        $search_params = [];

        if (isset($_GET['min_price'])) {
            $search_params['min_price'] = $_GET['min_price'];
        }

        if (isset($_GET['max_price'])) {
            $search_params['max_price'] = $_GET['max_price'];
        }

        if (isset($_GET['brand']) && !empty($_GET['brand'])) {
            $search_params['brand'] = $_GET['brand'];
        }

        if (isset($_GET['car_type']) && !empty($_GET['car_type'])) {
            $search_params['car_type'] = $_GET['car_type'];
        }

        if (isset($_GET['seats']) && $_GET['seats'] > 0) {
            $search_params['seats'] = $_GET['seats'];
        }


        // Get all available cars matching the criteria
        $cars = $this->carService->searchCars($search_params);

        // Get all brands for filter dropdown
        $brands = $this->carService->getAllBrands();

        // Include search view
        include 'views/car/search.php';
    }

    public function searchAddress()
    {
        $search_params = [];

        if (isset($_GET['address']) && !empty($_GET['address'])) {
            $search_params['address'] = $_GET['address'];
        }

        if (isset($_GET['start_date']) && !empty($_GET['start_date'])) {
            $search_params['start_date'] = $_GET['start_date'];
        }

        if (isset($_GET['end_date']) && !empty($_GET['end_date'])) {
            $search_params['end_date'] = $_GET['end_date'];
        }

        // Get available cars based on address and date range
        $cars = $this->carService->searchCarByAddress($search_params);

        $brands = $this->carService->getAllBrands();

        // Include search view
        include 'views/car/search.php';
    }

    // Display car details
    public function details($id)
    {
        $car_details = $this->carService->getCarDetails($id);

        if (!$car_details) {
            // Car not found or not approved
            $_SESSION['error'] = "Car not found or not available.";
            header('Location: ' . BASE_URL . '/cars/search');
            exit;
        }

        include 'views/car/details.php';
    }

    // Display form to add a new car (owner)
    public function showAddForm()
    {
        // Check if user is logged in and is an owner
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'owner') {
            $_SESSION['error'] = "You must be logged in as a car owner to add cars.";
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }

        include 'views/owner/add_car.php';
    }

    // Process add car form submission (owner)
    public function addCar()
    {
        // Check if user is logged in and is an owner
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'owner') {
            $_SESSION['error'] = "You must be logged in as a car owner to add cars.";
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }

        // Validate form data
        $required_fields = ['brand', 'model', 'year', 'car_type', 'seats', 'price_per_day', 'address', 'latitude', 'longitude', 'description'];
        $validation_errors = $this->validator->validateRequired($_POST, $required_fields);

        if (!empty($validation_errors)) {
            $_SESSION['error'] = "Please fix the following errors: " . implode(', ', $validation_errors);
            $_SESSION['form_data'] = $_POST;
            header('Location: ' . BASE_URL . '/cars/add');
            exit;
        }

        // Validate images
        if (!isset($_FILES['car_images']) || empty($_FILES['car_images']['name'][0])) {
            $_SESSION['error'] = "Please upload at least one image of the car.";
            $_SESSION['form_data'] = $_POST;
            header('Location: ' . BASE_URL . '/cars/add');
            exit;
        }

        // Add car with images
        $car_data = [
            'owner_id' => $_SESSION['user_id'],
            'brand' => $_POST['brand'],
            'model' => $_POST['model'],
            'year' => $_POST['year'],
            'car_type' => $_POST['car_type'],
            'seats' => $_POST['seats'],
            'price_per_day' => $_POST['price_per_day'],
            'address' => $_POST['address'],
            'latitude' => $_POST['latitude'],
            'longitude' => $_POST['longitude'],
            'description' => $_POST['description']
        ];

        $car_id = $this->carService->addCar($car_data, $_FILES['car_images']);

        if ($car_id) {
            $_SESSION['success'] = "Car added successfully. It will be available after admin approval.";
            header('Location: ' . BASE_URL . '/owner/cars');
        } else {
            $_SESSION['error'] = "Failed to add car. Please try again.";
            $_SESSION['form_data'] = $_POST;
            header('Location: ' . BASE_URL . '/cars/add');
        }

        exit;
    }

    // Display form to edit car (owner)
    public function showEditForm($id)
    {
        // Check if user is logged in and is an owner
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'owner') {
            $_SESSION['error'] = "You must be logged in as a car owner.";
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }

        // Get car details
        $car_details = $this->carService->getCarDetails($id);

        // Check if car exists and belongs to the owner
        if (!$car_details || $car_details['owner_id'] != $_SESSION['user_id']) {
            $_SESSION['error'] = "Car not found or you don't have permission to edit.";
            header('Location: ' . BASE_URL . '/owner/cars');
            exit;
        }

        include 'views/owner/edit_car.php';
    }

    // Process edit car form submission (owner)
    public function updateCar($id)
    {
        // Check if user is logged in and is an owner
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'owner') {
            $_SESSION['error'] = "You must be logged in as a car owner.";
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }

        // Validate form data
        $required_fields = ['brand', 'model', 'year', 'car_type', 'seats', 'price_per_day', 'address', 'latitude', 'longitude', 'description'];
        $validation_errors = $this->validator->validateRequired($_POST, $required_fields);

        if (!empty($validation_errors)) {
            $_SESSION['error'] = "Please fix the following errors: " . implode(', ', $validation_errors);
            $_SESSION['form_data'] = $_POST;
            header('Location: ' . BASE_URL . '/cars/edit/' . $id);
            exit;
        }

        // Update car with optional new images
        $car_data = [
            'brand' => $_POST['brand'],
            'model' => $_POST['model'],
            'year' => $_POST['year'],
            'car_type' => $_POST['car_type'],
            'seats' => $_POST['seats'],
            'price_per_day' => $_POST['price_per_day'],
            'address' => $_POST['address'],
            'latitude' => $_POST['latitude'],
            'longitude' => $_POST['longitude'],
            'description' => $_POST['description']
        ];

        // Handle primary image selection
        if (isset($_POST['primary_image'])) {
            $car_data['primary_image'] = $_POST['primary_image'];
        }

        $result = $this->carService->updateCar($id, $_SESSION['user_id'], $car_data, $_FILES['car_images'] ?? null);

        if ($result) {
            $_SESSION['success'] = "Car updated successfully.";
            header('Location: ' . BASE_URL . '/owner/cars');
        } else {
            $_SESSION['error'] = "Failed to update car. Please try again.";
            header('Location: ' . BASE_URL . '/cars/edit/' . $id);
        }

        exit;
    }

    // Approve car (admin)
    public function approveCar($id)
    {
        // Check if user is admin
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
            $_SESSION['error'] = "You don't have permission to perform this action.";
            header('Location: ' . BASE_URL);
            exit;
        }

        if ($this->carService->approveCar($id)) {
            $_SESSION['success'] = "Car approved successfully.";
        } else {
            $_SESSION['error'] = "Failed to approve car.";
        }

        header('Location: ' . BASE_URL . '/admin/cars');
        exit;
    }

    // Reject car (admin)
    public function rejectCar($id)
    {
        // Check if user is admin
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
            $_SESSION['error'] = "You don't have permission to perform this action.";
            header('Location: ' . BASE_URL);
            exit;
        }

        if ($this->carService->rejectCar($id)) {
            $_SESSION['success'] = "Car rejected successfully.";
        } else {
            $_SESSION['error'] = "Failed to reject car.";
        }

        header('Location: ' . BASE_URL . '/admin/cars');
        exit;
    }
}
