<?php
class Promotion {
    private $conn;
    private $table_name = "promotions";

    // Promotion properties
    public $id;
    public $code;
    public $discount_percentage;
    public $start_date;
    public $end_date;
    public $status;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create new promotion
    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    code = :code,
                    discount_percentage = :discount_percentage,
                    start_date = :start_date,
                    end_date = :end_date,
                    status = :status,
                    created_at = NOW(),
                    updated_at = NOW()";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->code = htmlspecialchars(strip_tags($this->code));
        $this->discount_percentage = htmlspecialchars(strip_tags($this->discount_percentage));
        $this->start_date = htmlspecialchars(strip_tags($this->start_date));
        $this->end_date = htmlspecialchars(strip_tags($this->end_date));
        $this->status = htmlspecialchars(strip_tags($this->status));

        // Bind values
        $stmt->bindParam(":code", $this->code);
        $stmt->bindParam(":discount_percentage", $this->discount_percentage);
        $stmt->bindParam(":start_date", $this->start_date);
        $stmt->bindParam(":end_date", $this->end_date);
        $stmt->bindParam(":status", $this->status);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }

        return false;
    }

    // Read all promotions
    public function readAll() {
        $query = "SELECT * FROM " . $this->table_name . " 
                ORDER BY created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    // Read active promotions
    public function readActive() {
        $query = "SELECT * FROM " . $this->table_name . " 
                WHERE status = 'active' 
                    AND start_date <= NOW() 
                    AND end_date >= NOW() 
                ORDER BY created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    // Read one promotion by ID
    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " 
                WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->id = $row['id'];
            $this->code = $row['code'];
            $this->discount_percentage = $row['discount_percentage'];
            $this->start_date = $row['start_date'];
            $this->end_date = $row['end_date'];
            $this->status = $row['status'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            
            return true;
        }
        
        return false;
    }

    // Update promotion
    public function update() {
        $query = "UPDATE " . $this->table_name . "
                SET
                    code = :code,
                    discount_percentage = :discount_percentage,
                    start_date = :start_date,
                    end_date = :end_date,
                    status = :status,
                    updated_at = NOW()
                WHERE
                    id = :id";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->code = htmlspecialchars(strip_tags($this->code));
        $this->discount_percentage = htmlspecialchars(strip_tags($this->discount_percentage));
        $this->start_date = htmlspecialchars(strip_tags($this->start_date));
        $this->end_date = htmlspecialchars(strip_tags($this->end_date));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->id = htmlspecialchars(strip_tags($this->id));

        // Bind values
        $stmt->bindParam(":code", $this->code);
        $stmt->bindParam(":discount_percentage", $this->discount_percentage);
        $stmt->bindParam(":start_date", $this->start_date);
        $stmt->bindParam(":end_date", $this->end_date);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":id", $this->id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Delete promotion
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(':id', $this->id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Validate promotion code
    public function validateCode($code) {
        $query = "SELECT * FROM " . $this->table_name . " 
                WHERE code = :code 
                    AND status = 'active' 
                    AND start_date <= NOW() 
                    AND end_date >= NOW()";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':code', $code);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->id = $row['id'];
            $this->code = $row['code'];
            $this->discount_percentage = $row['discount_percentage'];
            $this->start_date = $row['start_date'];
            $this->end_date = $row['end_date'];
            $this->status = $row['status'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            
            return true;
        }
        
        return false;
    }

    // Check if promotion code exists
    public function codeExists($code) {
        $query = "SELECT id FROM " . $this->table_name . " 
                WHERE code = :code 
                    AND id != :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':code', $code);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
}