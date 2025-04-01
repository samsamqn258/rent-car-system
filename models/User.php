<?php
class User {
    private $conn;
    private $table_name = "users";

    // User properties
    public $id;
    public $username;
    public $email;
    public $password;
    public $fullname;
    public $phone;
    public $address;
    public $license;
    public $role;
    public $status;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create new user
    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    username = :username,
                    email = :email,
                    password = :password,
                    fullname = :fullname,
                    phone = :phone,
                    address = :address,
                    role = :role,
                    status = 'active',
                    created_at = NOW(),
                    updated_at = NOW()";

        $stmt = $this->conn->prepare($query);

        // Sanitize and bind
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
        $this->fullname = htmlspecialchars(strip_tags($this->fullname));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->address = htmlspecialchars(strip_tags($this->address));
        $this->role = htmlspecialchars(strip_tags($this->role));

        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password', $this->password);
        $stmt->bindParam(':fullname', $this->fullname);
        $stmt->bindParam(':phone', $this->phone);
        $stmt->bindParam(':address', $this->address);
        $stmt->bindParam(':role', $this->role);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }

        return false;
    }

    // Login check
    public function login() {
        $query = "SELECT * FROM " . $this->table_name . " 
                WHERE 
                    (username = :username OR email = :email) 
                    AND status = 'active'";

        $stmt = $this->conn->prepare($query);
        
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->email = htmlspecialchars(strip_tags($this->email));
        
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':email', $this->email);
        
        $stmt->execute();
        $row = $stmt->fetch();
        
        if($row) {
            $this->id = $row['id'];
            $this->username = $row['username'];
            $this->email = $row['email'];
            $this->fullname = $row['fullname'];
            $this->phone = $row['phone'];
            $this->address = $row['address'];
            $this->role = $row['role'];
            $this->status = $row['status'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            
            if(password_verify($this->password, $row['password'])) {
                return true;
            }
        }
        
        return false;
    }
    
    // Read users with optional filter by ID
    public function read($id = null) {
        $query = "SELECT * FROM " . $this->table_name;
        
        if($id) {
            $query .= " WHERE id = :id";
        }
        
        $stmt = $this->conn->prepare($query);
        
        if($id) {
            $stmt->bindParam(':id', $id);
        }
        
        $stmt->execute();
        
        return $stmt;
    }
    
    // Update user profile
    public function update() {
        $query = "UPDATE " . $this->table_name . "
                SET
                    fullname = :fullname,
                    phone = :phone,
                    address = :address,
                    license = :license,
                    updated_at = NOW()
                WHERE
                    id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $this->fullname = htmlspecialchars(strip_tags($this->fullname));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->address = htmlspecialchars(strip_tags($this->address));
        
        $stmt->bindParam(':fullname', $this->fullname);
        $stmt->bindParam(':phone', $this->phone);
        $stmt->bindParam(':address', $this->address);
        $stmt->bindParam(':license', $this->license);
        $stmt->bindParam(':id', $this->id);
        
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    // Update user status (active/blocked)
    public function updateStatus() {
        $query = "UPDATE " . $this->table_name . "
                SET
                    status = :status,
                    updated_at = NOW()
                WHERE
                    id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $this->status = htmlspecialchars(strip_tags($this->status));
        
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':id', $this->id);
        
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    // Update user password
    public function updatePassword() {
        $query = "UPDATE " . $this->table_name . "
                SET
                    password = :password,
                    updated_at = NOW()
                WHERE
                    id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
        
        $stmt->bindParam(':password', $this->password);
        $stmt->bindParam(':id', $this->id);
        
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    // Check if username or email exists
    public function usernameOrEmailExists() {
        $query = "SELECT id FROM " . $this->table_name . " 
                WHERE username = :username OR email = :email";
        
        $stmt = $this->conn->prepare($query);
        
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->email = htmlspecialchars(strip_tags($this->email));
        
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':email', $this->email);
        
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }
}