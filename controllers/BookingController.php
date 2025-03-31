<?php
require_once 'services/BookingService.php';
require_once 'services/CarService.php';
require_once 'utils/Validator.php';

class BookingController
{
    private $bookingService;
    private $carService;
    private $validator;

    public function __construct($db)
    {
        $this->bookingService = new BookingService($db);
        $this->carService = new CarService($db);
        $this->validator = new Validator();
    }

    // Display booking form
    public function showBookingForm($car_id)
    {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = "You must be logged in to book a car.";
            $_SESSION['redirect_after_login'] = BASE_URL . '/booking/create/' . $car_id;
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }

        // Get car details
        $car_details = $this->carService->getCarDetails($car_id);

        if (!$car_details || $car_details['status'] != 'approved') {
            $_SESSION['error'] = "Car not found or not available for booking.";
            header('Location: ' . BASE_URL . '/cars/search');
            exit;
        }

        // Check if the current user is the owner of the car
        if ($car_details['owner_id'] == $_SESSION['user_id']) {
            $_SESSION['error'] = "You cannot book your own car.";
            header('Location: ' . BASE_URL . '/cars/details/' . $car_id);
            exit;
        }

        include 'views/booking/create.php';
    }

    // Process booking submission
    public function createBooking($car_id)
    {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = "You must be logged in to book a car.";
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }

        // Validate form data
        $required_fields = ['start_date', 'end_date'];
        $validation_errors = $this->validator->validateRequired($_POST, $required_fields);

        if (!empty($validation_errors)) {
            $_SESSION['error'] = "Please fix the following errors: " . implode(', ', $validation_errors);
            $_SESSION['form_data'] = $_POST;
            header('Location: ' . BASE_URL . '/booking/create/' . $car_id);
            exit;
        }

        // Validate dates
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];

        if (strtotime($start_date) < strtotime(date('Y-m-d'))) {
            $_SESSION['error'] = "Start date cannot be in the past.";
            $_SESSION['form_data'] = $_POST;
            header('Location: ' . BASE_URL . '/booking/create/' . $car_id);
            exit;
        }

        if (strtotime($end_date) < strtotime($start_date)) {
            $_SESSION['error'] = "End date must be after start date.";
            $_SESSION['form_data'] = $_POST;
            header('Location: ' . BASE_URL . '/booking/create/' . $car_id);
            exit;
        }

        // Check car availability
        if (!$this->carService->isCarAvailable($car_id, $start_date, $end_date)) {
            $_SESSION['error'] = "Car is not available for the selected dates.";
            $_SESSION['form_data'] = $_POST;
            header('Location: ' . BASE_URL . '/booking/create/' . $car_id);
            exit;
        }

        // Calculate total price
        $total_price = $this->carService->calculateTotalPrice($car_id, $start_date, $end_date);

        if (!$total_price) {
            $_SESSION['error'] = "Error calculating price. Please try again.";
            header('Location: ' . BASE_URL . '/booking/create/' . $car_id);
            exit;
        }

        // Create booking
        $booking_data = [
            'car_id' => $car_id,
            'user_id' => $_SESSION['user_id'],
            'start_date' => $start_date,
            'end_date' => $end_date,
            'total_price' => $total_price
        ];

        $booking_id = $this->bookingService->createBooking($booking_data);

        if ($booking_id) {
            // Redirect to payment page
            header('Location: ' . BASE_URL . '/booking/payment/' . $booking_id);
        } else {
            $_SESSION['error'] = "Failed to create booking. Please try again.";
            header('Location: ' . BASE_URL . '/booking/create/' . $car_id);
        }

        exit;
    }

    // Display booking payment page
    public function showPaymentPage($booking_id)
    {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = "You must be logged in to access this page.";
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }

        // Get booking details
        $booking = $this->bookingService->getBookingDetails($booking_id);

        // Check if booking exists and belongs to the user
        if (!$booking || $booking['user_id'] != $_SESSION['user_id']) {
            $_SESSION['error'] = "Booking not found or you don't have permission to access.";
            header('Location: ' . BASE_URL . '/user/bookings');
            exit;
        }

        // Check if booking is already paid
        if ($booking['payment_status'] == 'paid') {
            $_SESSION['success'] = "This booking is already paid.";
            header('Location: ' . BASE_URL . '/booking/details/' . $booking_id);
            exit;
        }

        include 'views/booking/payment.php';
    }

    // Process payment and redirect to MoMo
    public function processPayment($booking_id)
    {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = "You must be logged in to make a payment.";
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }

        // Get booking details
        $booking = $this->bookingService->getBookingDetails($booking_id);

        // Check if booking exists and belongs to the user
        if (!$booking || $booking['user_id'] != $_SESSION['user_id']) {
            $_SESSION['error'] = "Booking not found or you don't have permission to access.";
            header('Location: ' . BASE_URL . '/user/bookings');
            exit;
        }

        // Create MoMo payment request
        $pay_url = $this->bookingService->createMoMoPayment($booking_id, $booking['total_price'], "orderInfo#" . $booking_id);
        error_log('Pay_url' . $pay_url);
        if ($pay_url) {
            header('Location: ' . $pay_url);
        } else {
            error_log("Failed to create MoMo payment request for booking ID: $booking_id");
            $_SESSION['error'] = "Failed to create payment request. Please try again.";
            header('Location: ' . BASE_URL . '/booking/payment/' . $booking_id);
        }

        exit;
    }

    // Payment callback from MoMo
    public function paymentCallback()
    {
        $response_data = $_GET;

        $result = $this->bookingService->processPaymentCallback($response_data);

        if ($result && $result['success']) {
            if ($result['status'] == 'paid') {
                $_SESSION['success'] = "Payment successful! Your booking is confirmed.";
            } else {
                $_SESSION['error'] = "Payment failed. Please try again.";
            }

            header('Location: ' . BASE_URL . '/booking/details/' . $result['booking_id']);
        } else {
            $_SESSION['error'] = "Error processing payment callback.";
            header('Location: ' . BASE_URL . '/user/bookings');
        }

        exit;
    }

    // Show booking details
    public function showBookingDetails($booking_id)
    {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = "You must be logged in to view booking details.";
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }

        // Get booking details
        $booking = $this->bookingService->getBookingDetails($booking_id);

        // Check if booking exists and user has permission to view
        if (!$booking || ($booking['user_id'] != $_SESSION['user_id'] && $booking['owner_id'] != $_SESSION['user_id'] && $_SESSION['user_role'] != 'admin')) {
            $_SESSION['error'] = "Booking not found or you don't have permission to view.";
            header('Location: ' . BASE_URL . '/user/bookings');
            exit;
        }

        include 'views/booking/details.php';
    }

    // User's booking history
    public function userBookings()
    {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = "You must be logged in to view your bookings.";
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }

        $bookings = $this->bookingService->getUserBookings($_SESSION['user_id']);

        include 'views/user/rental_history.php';
    }

    // Owner bookings management
    public function ownerBookings()
    {
        // Check if user is logged in as owner
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'owner') {
            $_SESSION['error'] = "You must be logged in as a car owner to access this page.";
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }

        $bookings = $this->bookingService->getOwnerBookings($_SESSION['user_id']);

        include 'views/owner/manage_bookings.php';
    }

    // Update booking status (owner)
    public function updateBookingStatus($booking_id)
    {
        // Check if user is logged in as owner
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'owner') {
            $_SESSION['error'] = "You must be logged in as a car owner to perform this action.";
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }

        // Validate required fields
        if (!isset($_POST['status']) || empty($_POST['status'])) {
            $_SESSION['error'] = "Status is required.";
            header('Location: ' . BASE_URL . '/booking/details/' . $booking_id);
            exit;
        }

        $new_status = $_POST['status'];

        // Get booking details to verify ownership
        $booking = $this->bookingService->getBookingDetails($booking_id);

        if (!$booking || $booking['owner_id'] != $_SESSION['user_id']) {
            $_SESSION['error'] = "Booking not found or you don't have permission to update.";
            header('Location: ' . BASE_URL . '/owner/bookings');
            exit;
        }

        // Update booking status
        if ($this->bookingService->updateBookingStatus($booking_id, $new_status)) {
            $_SESSION['success'] = "Booking status updated successfully.";
        } else {
            $_SESSION['error'] = "Failed to update booking status.";
        }

        header('Location: ' . BASE_URL . '/booking/details/' . $booking_id);
        exit;
    }

    // Cancel booking (user)
    public function cancelBooking($booking_id)
    {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = "You must be logged in to cancel a booking.";
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }

        // Get booking details
        $booking = $this->bookingService->getBookingDetails($booking_id);

        // Check if booking exists and belongs to the user
        if (!$booking || $booking['user_id'] != $_SESSION['user_id']) {
            $_SESSION['error'] = "Booking not found or you don't have permission to cancel.";
            header('Location: ' . BASE_URL . '/user/bookings');
            exit;
        }

        // Check if booking can be canceled (not started yet)
        if (strtotime($booking['start_date']) <= strtotime(date('Y-m-d'))) {
            $_SESSION['error'] = "Cannot cancel a booking that has already started.";
            header('Location: ' . BASE_URL . '/booking/details/' . $booking_id);
            exit;
        }

        // Update booking status to canceled
        if ($this->bookingService->updateBookingStatus($booking_id, 'canceled')) {
            $_SESSION['success'] = "Booking canceled successfully.";
        } else {
            $_SESSION['error'] = "Failed to cancel booking.";
        }

        header('Location: ' . BASE_URL . '/user/bookings');
        exit;
    }
}
