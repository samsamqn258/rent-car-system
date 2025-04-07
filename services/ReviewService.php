<?php
require_once 'models/Review.php';
require_once 'models/Booking.php';
require_once 'models/Car.php';
require_once 'utils/Validator.php';

class ReviewService
{
    private $db;
    private $review;
    private $booking;
    private $car;
    private $validator;

    public function __construct($db)
    {
        $this->db = $db;
        $this->review = new Review($db);
        $this->booking = new Booking($db);
        $this->car = new Car($db);
        $this->validator = new Validator();
    }

    /**
     * Create a new review for a booking
     * 
     * @param int $booking_id Booking ID
     * @param int $user_id User ID
     * @param array $reviewData Review data (rating, comment)
     * @return array Result with success status and message
     */
    public function createReview($booking_id, $user_id, $reviewData)
    {
        // Validate required fields›
        $required_fields = ['rating', 'comment'];
        $validation_errors = $this->validator->validateRequired($reviewData, $required_fields);

        if (!empty($validation_errors)) {
            return [
                'success' => false,
                'message' => "Vui lòng sửa các lỗi sau: " . implode(', ', $validation_errors)
            ];
        }

        // Validate rating value (1-5)
        if (!is_numeric($reviewData['rating']) || $reviewData['rating'] < 1 || $reviewData['rating'] > 5) {
            return [
                'success' => false,
                'message' => "Đánh giá phải là số từ 1 đến 5."
            ];
        }

        // Check if booking exists and belongs to the user
        $this->booking->id = $booking_id;

        if (!$this->booking->readOne()) {
            return [
                'success' => false,
                'message' => "Không tìm thấy thông tin đặt xe."
            ];
        }

        if ($this->booking->user_id != $user_id) {
            return [
                'success' => false,
                'message' => "Bạn không có quyền đánh giá đơn đặt xe này."
            ];
        }

        // Check if booking is completed
        if ($this->booking->booking_status != 'completed') {
            return [
                'success' => false,
                'message' => "Bạn chỉ có thể đánh giá sau khi hoàn tất chuyến đi."
            ];
        }

        // Check if booking has already been reviewed
        $this->review->booking_id = $booking_id;

        if ($this->review->hasReviewed()) {
            return [
                'success' => false,
                'message' => "Bạn đã đánh giá cho chuyến đi này rồi."
            ];
        }

        // Set review properties
        $this->review->booking_id = $booking_id;
        $this->review->user_id = $user_id;
        $this->review->car_id = $this->booking->car_id;
        $this->review->rating = $reviewData['rating'];
        $this->review->comment = $reviewData['comment'];

        // Create review
        if ($this->review->create()) {
            // Recalculate average rating for the car
            $this->refreshCarRating($this->booking->car_id);

            return [
                'success' => true,
                'message' => "Cảm ơn bạn đã đánh giá! Phản hồi của bạn giúp ích rất nhiều cho cộng đồng."
            ];
        } else {
            return [
                'success' => false,
                'message' => "Có lỗi xảy ra khi gửi đánh giá. Vui lòng thử lại."
            ];
        }
    }

    /**
     * Get reviews for a car
     * 
     * @param int $car_id Car ID
     * @param int $limit Limit number of reviews (optional)
     * @param int $offset Offset for pagination (optional)
     * @return array Result with reviews data
     */
    public function getCarReviews($car_id, $limit = null, $offset = null)
    {
        $this->review->car_id = $car_id;
        $reviews_stmt = $this->review->readByCar();

        $reviews = [];
        while ($row = $reviews_stmt->fetch(PDO::FETCH_ASSOC)) {
            // If limit is set, only return that many reviews
            if ($limit && count($reviews) >= $limit) {
                break;
            }

            // If offset is set, skip that many reviews first
            if ($offset && $offset > 0) {
                $offset--;
                continue;
            }

            $reviews[] = $row;
        }

        // Get car rating data
        $rating_data = $this->getCarRating($car_id);

        return [
            'reviews' => $reviews,
            'total_reviews' => $rating_data['review_count'],
            'average_rating' => $rating_data['avg_rating']
        ];
    }

    /**
     * Get reviews by a user
     * 
     * @param int $user_id User ID
     * @return array User's reviews
     */
    public function getUserReviews($user_id)
    {
        $this->review->user_id = $user_id;
        $reviews_stmt = $this->review->readByUser();

        $reviews = [];
        while ($row = $reviews_stmt->fetch(PDO::FETCH_ASSOC)) {
            $reviews[] = $row;
        }

        return $reviews;
    }

    /**
     * Get rating statistics for a car
     * 
     * @param int $car_id Car ID
     * @return array Rating statistics
     */
    public function getCarRating($car_id)
    {
        $this->review->car_id = $car_id;
        return $this->review->getAverageRating();
    }

    /**
     * Check if a booking can be reviewed
     * 
     * @param int $booking_id Booking ID
     * @param int $user_id User ID
     * @return bool True if the booking can be reviewed
     */
    public function canReviewBooking($booking_id, $user_id)
    {
        // Check if booking exists and belongs to the user
        $this->booking->id = $booking_id;

        if (!$this->booking->readOne() || $this->booking->user_id != $user_id) {
            return false;
        }

        // Check if booking is completed and not yet reviewed
        return $this->booking->booking_status == 'completed' && !$this->review->hasReviewed();
    }

    /**
     * Recalculate and update car's average rating
     * 
     * @param int $car_id Car ID
     * @return bool Success status
     */
    private function refreshCarRating($car_id)
    {
        // This is a simple implementation
        // In a real application, you might want to store the average rating in the cars table
        // to avoid calculating it every time

        $rating_data = $this->getCarRating($car_id);

        // For now, we'll just return the success status
        return true;
    }

    /**
     * Get most recent reviews in the system (for admin dashboard)
     * 
     * @param int $limit Limit number of reviews (default 5)
     * @return array Recent reviews
     */
    public function getRecentReviews($limit = 5)
    {
        $query = "SELECT r.*, u.fullname as user_name, c.brand as car_brand, c.model as car_model
                FROM reviews r
                JOIN users u ON r.user_id = u.id
                JOIN cars c ON r.car_id = c.id
                ORDER BY r.created_at DESC
                LIMIT :limit";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        $reviews = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $reviews[] = $row;
        }

        return $reviews;
    }

    /**
     * Get total number of reviews in the system
     * 
     * @return int Total number of reviews
     */
    public function getTotalReviews()
    {
        $query = "SELECT COUNT(*) as total FROM reviews";
        $stmt = $this->db->prepare($query);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    /**
     * Get average rating for all cars
     * 
     * @return float Average rating
     */
    public function getSystemAverageRating()
    {
        $query = "SELECT AVG(rating) as avg_rating FROM reviews";
        $stmt = $this->db->prepare($query);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['avg_rating'] ? round($row['avg_rating'], 1) : 0;
    }

    /**
     * Get distribution of ratings (for statistics)
     * 
     * @return array Distribution of ratings (1-5 stars)
     */
    public function getRatingDistribution()
    {
        $query = "SELECT rating, COUNT(*) as count FROM reviews GROUP BY rating ORDER BY rating";
        $stmt = $this->db->prepare($query);
        $stmt->execute();

        $distribution = [
            1 => 0,
            2 => 0,
            3 => 0,
            4 => 0,
            5 => 0
        ];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $distribution[$row['rating']] = (int)$row['count'];
        }

        return $distribution;
    }
}
