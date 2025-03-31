<?php
/**
 * Database Connection Manager
 * 
 * Manages database connections using PDO and follows singleton pattern
 * to ensure only one connection is established throughout the application.
 */
class Database {
    // Database connection parameters
    private $host;
    private $db_name;
    private $username;
    private $password;
    // Connection object
    private $conn;
    // Singleton instance
    private static $instance;

    /**
     * Constructor - Initialize connection parameters from configuration file
     */
    private function __construct() {
        // Load database configuration
        if (!defined('DB_HOST')) {
            require_once dirname(__DIR__) . '/configs/config.php';
        }
        
        $this->host = DB_HOST;
        $this->db_name = DB_NAME;
        $this->username = DB_USERNAME;
        $this->password = DB_PASSWORD;
    }

    /**
     * Get singleton instance of Database class
     * 
     * @return Database The Database instance
     */
    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    /**
     * Get database connection
     * 
     * @return PDO The database connection
     */
    public function getConnection() {
        $this->conn = null;

        try {
            // Create PDO connection with appropriate options
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (PDOException $exception) {
            // Log error and display user-friendly message
            error_log("Database Connection Error: " . $exception->getMessage());
            
            // In development environment, you might want to show detailed error
            if (defined('ENVIRONMENT') && INFO_ENVIRONMENT === 'development') {
                echo "Connection Error: " . $exception->getMessage();
            } else {
                echo "Không thể kết nối đến cơ sở dữ liệu. Vui lòng thử lại sau.";
            }
            
            exit;
        }

        return $this->conn;
    }
    
    /**
     * Execute SQL query and return result
     * 
     * @param string $sql SQL query
     * @param array $params Parameters for prepared statement
     * @return PDOStatement Result set
     */
    public function query($sql, $params = []) {
        try {
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $exception) {
            error_log("Database Query Error: " . $exception->getMessage());
            
            if (defined('ENVIRONMENT') && INFO_ENVIRONMENT === 'development') {
                echo "Query Error: " . $exception->getMessage();
            } else {
                echo "Đã xảy ra lỗi khi thực hiện truy vấn. Vui lòng thử lại sau.";
            }
            
            exit;
        }
    }
    
    /**
     * Begin a transaction
     */
    public function beginTransaction() {
        return $this->getConnection()->beginTransaction();
    }
    
    /**
     * Commit a transaction
     */
    public function commit() {
        return $this->getConnection()->commit();
    }
    
    /**
     * Rollback a transaction
     */
    public function rollback() {
        return $this->getConnection()->rollBack();
    }
    
    /**
     * Get the last inserted ID
     * 
     * @return string The last inserted ID
     */
    public function lastInsertId() {
        return $this->getConnection()->lastInsertId();
    }
    
    /**
     * Close database connection
     */
    public function closeConnection() {
        $this->conn = null;
    }
    
    /**
     * Prevent cloning of the instance
     */
    private function __clone() {}
    
    /**
     * Prevent unserializing of the instance
     */
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}