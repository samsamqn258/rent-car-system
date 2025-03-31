<?php
class CarImage {
    private $conn;
    private $table_name = "car_images";

    // Car image properties
    public $id;
    public $car_id;
    public $image_path;
    public $is_primary;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create new car image
    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    car_id = :car_id,
                    image_path = :image_path,
                    is_primary = :is_primary,
                    created_at = NOW()";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->car_id = htmlspecialchars(strip_tags($this->car_id));
        $this->image_path = htmlspecialchars(strip_tags($this->image_path));
        $this->is_primary = htmlspecialchars(strip_tags($this->is_primary));

        // Bind values
        $stmt->bindParam(":car_id", $this->car_id);
        $stmt->bindParam(":image_path", $this->image_path);
        $stmt->bindParam(":is_primary", $this->is_primary);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }

        return false;
    }

    // Read car images by car ID
    public function readByCarId() {
        $query = "SELECT * FROM " . $this->table_name . "
                WHERE car_id = :car_id
                ORDER BY is_primary DESC, created_at ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':car_id', $this->car_id);
        $stmt->execute();

        return $stmt;
    }

    // Delete car image
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    // Update is_primary status
    public function updatePrimary() {
        $query = "UPDATE " . $this->table_name . "
                SET is_primary = :is_primary
                WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':is_primary', $this->is_primary);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    // Reset all primary images for a car (set all to 0)
    public function resetPrimaryImages() {
        $query = "UPDATE " . $this->table_name . "
                SET is_primary = 0
                WHERE car_id = :car_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':car_id', $this->car_id);

        return $stmt->execute();
    }

    // Delete all images for a car
    public function deleteAllCarImages() {
        $query = "DELETE FROM " . $this->table_name . " WHERE car_id = :car_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':car_id', $this->car_id);

        return $stmt->execute();
    }

    // Get primary image for a car
    public function getPrimaryImage() {
        $query = "SELECT * FROM " . $this->table_name . "
                WHERE car_id = :car_id AND is_primary = 1
                LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':car_id', $this->car_id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return $row['image_path'];
        }

        // If no primary image, get the first image
        $query = "SELECT * FROM " . $this->table_name . "
                WHERE car_id = :car_id
                ORDER BY created_at ASC
                LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':car_id', $this->car_id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $row['image_path'] : null;
    }
}