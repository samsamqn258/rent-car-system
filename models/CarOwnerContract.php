<?php
class CarOwnerContract {
    private $conn;
    private $table_name = "car_owner_contracts";

    // Car Owner Contract properties
    public $id;
    public $owner_id;
    public $start_date;
    public $end_date;
    public $contract_fee;
    public $status;
    public $created_at;
    public $updated_at;

    // Additional properties for joins
    public $owner_name;
    public $owner_email;
    public $owner_phone;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create new car owner contract
    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    owner_id = :owner_id,
                    start_date = :start_date,
                    end_date = :end_date,
                    contract_fee = :contract_fee,
                    status = :status,
                    created_at = NOW(),
                    updated_at = NOW()";

        $stmt = $this->conn->prepare($query);

        // Sanitize data
        $this->owner_id = htmlspecialchars(strip_tags($this->owner_id));
        $this->start_date = htmlspecialchars(strip_tags($this->start_date));
        $this->end_date = htmlspecialchars(strip_tags($this->end_date));
        $this->contract_fee = htmlspecialchars(strip_tags($this->contract_fee));
        $this->status = htmlspecialchars(strip_tags($this->status));

        // Bind data
        $stmt->bindParam(":owner_id", $this->owner_id);
        $stmt->bindParam(":start_date", $this->start_date);
        $stmt->bindParam(":end_date", $this->end_date);
        $stmt->bindParam(":contract_fee", $this->contract_fee);
        $stmt->bindParam(":status", $this->status);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }

        return false;
    }

    // Read all contracts
    public function readAll() {
        $query = "SELECT c.*, u.fullname as owner_name, u.email as owner_email, u.phone as owner_phone
                FROM " . $this->table_name . " c
                LEFT JOIN users u ON c.owner_id = u.id
                ORDER BY c.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    // Read contracts by owner ID
    public function readByOwner() {
        $query = "SELECT * FROM " . $this->table_name . " 
                WHERE owner_id = :owner_id
                ORDER BY created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':owner_id', $this->owner_id);
        $stmt->execute();

        return $stmt;
    }

    // Read one contract by ID
    public function readOne() {
        $query = "SELECT c.*, u.fullname as owner_name, u.email as owner_email, u.phone as owner_phone
                FROM " . $this->table_name . " c
                LEFT JOIN users u ON c.owner_id = u.id
                WHERE c.id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->id = $row['id'];
            $this->owner_id = $row['owner_id'];
            $this->start_date = $row['start_date'];
            $this->end_date = $row['end_date'];
            $this->contract_fee = $row['contract_fee'];
            $this->status = $row['status'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            $this->owner_name = $row['owner_name'];
            $this->owner_email = $row['owner_email'];
            $this->owner_phone = $row['owner_phone'];
            
            return true;
        }
        
        return false;
    }

    // Read active contract by owner ID
    public function readActiveByOwner() {
        $current_date = date('Y-m-d');
        
        $query = "SELECT * FROM " . $this->table_name . " 
                WHERE owner_id = :owner_id 
                    AND status = 'active' 
                    AND start_date <= :current_date 
                    AND end_date >= :current_date
                LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':owner_id', $this->owner_id);
        $stmt->bindParam(':current_date', $current_date);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->id = $row['id'];
            $this->owner_id = $row['owner_id'];
            $this->start_date = $row['start_date'];
            $this->end_date = $row['end_date'];
            $this->contract_fee = $row['contract_fee'];
            $this->status = $row['status'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            
            return true;
        }
        
        return false;
    }

    // Update contract
    public function update() {
        $query = "UPDATE " . $this->table_name . "
                SET
                    start_date = :start_date,
                    end_date = :end_date,
                    contract_fee = :contract_fee,
                    status = :status,
                    updated_at = NOW()
                WHERE
                    id = :id";

        $stmt = $this->conn->prepare($query);

        // Sanitize data
        $this->start_date = htmlspecialchars(strip_tags($this->start_date));
        $this->end_date = htmlspecialchars(strip_tags($this->end_date));
        $this->contract_fee = htmlspecialchars(strip_tags($this->contract_fee));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->id = htmlspecialchars(strip_tags($this->id));

        // Bind data
        $stmt->bindParam(':start_date', $this->start_date);
        $stmt->bindParam(':end_date', $this->end_date);
        $stmt->bindParam(':contract_fee', $this->contract_fee);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':id', $this->id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Check if owner has active contract
    public function hasActiveContract() {
        $current_date = date('Y-m-d');
        
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " 
                WHERE owner_id = :owner_id 
                    AND status = 'active' 
                    AND start_date <= :current_date 
                    AND end_date >= :current_date";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':owner_id', $this->owner_id);
        $stmt->bindParam(':current_date', $current_date);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $row['count'] > 0;
    }

    // Create a default one-year contract for a new owner
    public function createDefaultContract() {
        // Set default values for a one-year contract
        $this->start_date = date('Y-m-d');
        $this->end_date = date('Y-m-d', strtotime('+1 year'));
        $this->contract_fee = 1000000; // 1,000,000 VND default annual fee
        $this->status = 'active';
        
        return $this->create();
    }

    // Renew contract
    public function renewContract($duration_months = 12) {
        // Get the latest contract for this owner
        $query = "SELECT * FROM " . $this->table_name . " 
                WHERE owner_id = :owner_id 
                ORDER BY end_date DESC 
                LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':owner_id', $this->owner_id);
        $stmt->execute();
        
        $latest_contract = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($latest_contract) {
            // Calculate new start and end dates
            $current_date = date('Y-m-d');
            
            // If the latest contract is still active, extend from its end date
            if ($latest_contract['status'] == 'active' && $latest_contract['end_date'] >= $current_date) {
                $this->start_date = date('Y-m-d', strtotime($latest_contract['end_date'] . ' +1 day'));
            } else {
                // Otherwise, start from today
                $this->start_date = $current_date;
            }
            
            $this->end_date = date('Y-m-d', strtotime($this->start_date . ' +' . $duration_months . ' months'));
            $this->contract_fee = $latest_contract['contract_fee'] * ($duration_months / 12); // Proportional fee
            $this->status = 'active';
            
            return $this->create();
        } else {
            // If no previous contract, create a new default one
            return $this->createDefaultContract();
        }
    }

    // Get contract statistics (total contracts, active contracts, total fees)
    public function getContractStatistics() {
        $query = "SELECT 
                    COUNT(*) as total_contracts,
                    SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_contracts,
                    SUM(contract_fee) as total_fees
                FROM " . $this->table_name;

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Get all contracts expiring in the next 30 days
    public function getExpiringContracts() {
        $current_date = date('Y-m-d');
        $expiry_date = date('Y-m-d', strtotime('+30 days'));
        
        $query = "SELECT c.*, u.fullname as owner_name, u.email as owner_email, u.phone as owner_phone
                FROM " . $this->table_name . " c
                LEFT JOIN users u ON c.owner_id = u.id
                WHERE c.status = 'active' 
                    AND c.end_date BETWEEN :current_date AND :expiry_date
                ORDER BY c.end_date ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':current_date', $current_date);
        $stmt->bindParam(':expiry_date', $expiry_date);
        $stmt->execute();
        
        return $stmt;
    }
}