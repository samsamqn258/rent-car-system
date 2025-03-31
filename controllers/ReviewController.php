<?php
require_once 'models/Review.php';
require_once 'models/Booking.php';
require_once 'utils/Validator.php';

class ReviewController
{
    private $db;
    private $review;
    private $booking;
    private $validator;

    public function __construct($db)
    {
        $this->db = $db;
        $this->review = new Review($db);
        $this->booking = new Booking($db);
        $this->validator = new Validator();
    }

    // Display review form for a booking
    public function showReviewForm($booking_id)
    {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = "Bạn phải đăng nhập để đánh giá.";
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }

        // Get booking details
        $this->booking->id = $booking_id;

        if (!$this->booking->readOne()) {
            $_SESSION['error'] = "Không tìm thấy thông tin đặt xe.";
            header('Location: ' . BASE_URL . '/user/bookings');
            exit;
        }

        // Check if booking belongs to the user
        if ($this->booking->user_id != $_SESSION['user_id']) {
            $_SESSION['error'] = "Bạn không có quyền đánh giá đơn đặt xe này.";
            header('Location: ' . BASE_URL . '/user/bookings');
            exit;
        }

        // Check if booking is completed
        if ($this->booking->booking_status != 'completed') {
            $_SESSION['error'] = "Bạn chỉ có thể đánh giá sau khi hoàn tất chuyến đi.";
            header('Location: ' . BASE_URL . '/booking/details/' . $booking_id);
            exit;
        }

        // Check if booking has already been reviewed
        $this->review->booking_id = $booking_id;

        if ($this->review->hasReviewed()) {
            $_SESSION['error'] = "Bạn đã đánh giá cho chuyến đi này rồi.";
            header('Location: ' . BASE_URL . '/booking/details/' . $booking_id);
            exit;
        }

        // Prepare data for the view
        $car_brand = $this->booking->car_brand;
        $car_model = $this->booking->car_model;
        $car_image = $this->booking->car_image;
        $car_id = $this->booking->car_id;

        include 'views/review/create.php';
    }

    // Process review submission
    public function createReview($booking_id)
    {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = "Bạn phải đăng nhập để đánh giá.";
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }

        // Validate required fields
        $required_fields = ['rating', 'comment'];
        $validation_errors = $this->validator->validateRequired($_POST, $required_fields);

        if (!empty($validation_errors)) {
            $_SESSION['error'] = "Vui lòng sửa các lỗi sau: " . implode(', ', $validation_errors);
            $_SESSION['form_data'] = $_POST;
            header('Location: ' . BASE_URL . '/review/create/' . $booking_id);
            exit;
        }

        // Get booking details
        $this->booking->id = $booking_id;

        if (!$this->booking->readOne()) {
            $_SESSION['error'] = "Không tìm thấy thông tin đặt xe.";
            header('Location: ' . BASE_URL . '/user/bookings');
            exit;
        }

        // Check if booking belongs to the user
        if ($this->booking->user_id != $_SESSION['user_id']) {
            $_SESSION['error'] = "Bạn không có quyền đánh giá đơn đặt xe này.";
            header('Location: ' . BASE_URL . '/user/bookings');
            exit;
        }

        // Check if booking is completed
        if ($this->booking->booking_status != 'completed') {
            $_SESSION['error'] = "Bạn chỉ có thể đánh giá sau khi hoàn tất chuyến đi.";
            header('Location: ' . BASE_URL . '/booking/details/' . $booking_id);
            exit;
        }

        // Check if booking has already been reviewed
        $this->review->booking_id = $booking_id;

        if ($this->review->hasReviewed()) {
            $_SESSION['error'] = "Bạn đã đánh giá cho chuyến đi này rồi.";
            header('Location: ' . BASE_URL . '/booking/details/' . $booking_id);
            exit;
        }

        // Set review properties
        $this->review->booking_id = $booking_id;
        $this->review->user_id = $_SESSION['user_id'];
        $this->review->car_id = $this->booking->car_id;
        $this->review->rating = $_POST['rating'];
        $this->review->comment = $_POST['comment'];

        // Create review
        if ($this->review->create()) {
            $_SESSION['success'] = "Cảm ơn bạn đã đánh giá! Phản hồi của bạn giúp ích rất nhiều cho cộng đồng.";
            header('Location: ' . BASE_URL . '/cars/details/' . $this->booking->car_id);
        } else {
            $_SESSION['error'] = "Có lỗi xảy ra khi gửi đánh giá. Vui lòng thử lại.";
            $_SESSION['form_data'] = $_POST;
            header('Location: ' . BASE_URL . '/review/create/' . $booking_id);
        }

        exit;
    }

    // Get reviews for a specific car
    public function getCarReviews($car_id)
    {
        $this->review->car_id = $car_id;
        $reviews_stmt = $this->review->readByCar();
        $reviews = [];

        while ($row = $reviews_stmt->fetch(PDO::FETCH_ASSOC)) {
            $reviews[] = $row;
        }

        header('Content-Type: application/json');
        echo json_encode($reviews);
    }

    // Get average rating for a car
    public function getCarRating($car_id)
    {
        $this->review->car_id = $car_id;
        $rating_data = $this->review->getAverageRating();

        header('Content-Type: application/json');
        echo json_encode($rating_data);
    }
}
