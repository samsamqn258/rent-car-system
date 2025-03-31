<?php
class Review {
    private $conn;
    private $table_name = "reviews";

    // Review properties
    public $id;
    public $booking_id;
    public $user_id;
    public $car_id;
    public $rating;
    public $comment;
    public $created_at;
    public $updated_at;

    // Additional properties for joins
    public $user_name;
    public $car_brand;
    public $car_model;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create new review
    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    booking_id = :booking_id,
                    user_id = :user_id,
                    car_id = :car_id,
                    rating = :rating,
                    comment = :comment,
                    created_at = NOW(),
                    updated_at = NOW()";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->booking_id = htmlspecialchars(strip_tags($this->booking_id));
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->car_id = htmlspecialchars(strip_tags($this->car_id));
        $this->rating = htmlspecialchars(strip_tags($this->rating));
        $this->comment = htmlspecialchars(strip_tags($this->comment));

        // Bind values
        $stmt->bindParam(":booking_id", $this->booking_id);
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":car_id", $this->car_id);
        $stmt->bindParam(":rating", $this->rating);
        $stmt->bindParam(":comment", $this->comment);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }

        return false;
    }

    // Read reviews by car ID
    public function readByCar() {
        $query = "SELECT r.*, u.fullname as user_name
                FROM " . $this->table_name . " r
                JOIN users u ON r.user_id = u.id
                WHERE r.car_id = :car_id
                ORDER BY r.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':car_id', $this->car_id);
        $stmt->execute();

        return $stmt;
    }

    // Read reviews by user ID
    public function readByUser() {
        $query = "SELECT r.*, c.brand as car_brand, c.model as car_model
                FROM " . $this->table_name . " r
                JOIN cars c ON r.car_id = c.id
                WHERE r.user_id = :user_id
                ORDER BY r.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->execute();

        return $stmt;
    }

    // Check if a booking has been reviewed
    public function hasReviewed() {
        $query = "SELECT COUNT(*) as count
                FROM " . $this->table_name . "
                WHERE booking_id = :booking_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':booking_id', $this->booking_id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['count'] > 0;
    }

    // Get average rating for a car
    public function getAverageRating() {
        $query = "SELECT AVG(rating) as avg_rating, COUNT(*) as review_count
                FROM " . $this->table_name . "
                WHERE car_id = :car_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':car_id', $this->car_id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}