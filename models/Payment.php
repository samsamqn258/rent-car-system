<?php
class Payment
{
    private $conn;
    private $table_name = "payments";

    // Payment properties
    public $id;
    public $booking_id;
    public $amount;
    public $payment_method;
    public $transaction_id;
    public $payment_status;
    public $created_at;
    public $updated_at;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Create new payment record
    public function create()
    {
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    booking_id = :booking_id,
                    amount = :amount,
                    payment_method = :payment_method,
                    transaction_id = :transaction_id,
                    payment_status = :payment_status,
                    created_at = NOW(),
                    updated_at = NOW()";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->booking_id = htmlspecialchars(strip_tags($this->booking_id));
        $this->amount = htmlspecialchars(strip_tags($this->amount));
        $this->payment_method = htmlspecialchars(strip_tags($this->payment_method));
        $this->transaction_id = htmlspecialchars(strip_tags($this->transaction_id));
        $this->payment_status = htmlspecialchars(strip_tags($this->payment_status));

        // Bind values
        $stmt->bindParam(":booking_id", $this->booking_id);
        $stmt->bindParam(":amount", $this->amount);
        $stmt->bindParam(":payment_method", $this->payment_method);
        $stmt->bindParam(":transaction_id", $this->transaction_id);
        $stmt->bindParam(":payment_status", $this->payment_status);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }

        // Log error if execution fails
        $errorInfo = $stmt->errorInfo();
        error_log('Lỗi khi lưu thanh toán: ' . print_r($errorInfo, true));

        return false;
    }

    // Update payment status by transaction ID
    public function updateByTransactionId()
    {
        $query = "UPDATE " . $this->table_name . "
                SET
                    payment_status = :payment_status,
                    updated_at = NOW()
                WHERE
                    transaction_id = :transaction_id";

        $stmt = $this->conn->prepare($query);

        $this->payment_status = htmlspecialchars(strip_tags($this->payment_status));
        $this->transaction_id = htmlspecialchars(strip_tags($this->transaction_id));

        $stmt->bindParam(':payment_status', $this->payment_status);
        $stmt->bindParam(':transaction_id', $this->transaction_id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Read payment by booking ID
    public function readByBooking()
    {
        $query = "SELECT * FROM " . $this->table_name . "
                WHERE booking_id = :booking_id
                ORDER BY created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':booking_id', $this->booking_id);
        $stmt->execute();

        return $stmt;
    }

    // Read payment by transaction ID
    public function readByTransactionId()
    {
        $query = "SELECT * FROM " . $this->table_name . "
                WHERE transaction_id = :transaction_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':transaction_id', $this->transaction_id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->id = $row['id'];
            $this->booking_id = $row['booking_id'];
            $this->amount = $row['amount'];
            $this->payment_method = $row['payment_method'];
            $this->transaction_id = $row['transaction_id'];
            $this->payment_status = $row['payment_status'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];

            return true;
        }

        return false;
    }
}
