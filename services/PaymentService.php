<?php
require_once 'models/Payment.php';
require_once 'models/Booking.php';
require_once 'utils/MoMoPayment.php';

class PaymentService
{
    private $db;
    private $payment;
    private $booking;
    private $momoPayment;

    public function __construct($db)
    {
        $this->db = $db;
        $this->payment = new Payment($db);
        $this->booking = new Booking($db);
        $this->momoPayment = new MoMoPayment();
    }

    /**
     * Create a new payment request for a booking
     * 
     * @param int $booking_id Booking ID
     * @param float $amount Payment amount
     * @param string $order_info Order information
     * @return string|false Payment URL or false if failed
     */
    public function createPaymentRequest($booking_id, $amount, $order_info)
    {
        // Get booking details to verify
        $this->booking->id = $booking_id;

        if (!$this->booking->readOne()) {
            return false;
        }

        // Check if payment already exists
        $this->payment->booking_id = $booking_id;
        $payment_stmt = $this->payment->readByBooking();
        $existing_payments = [];

        while ($row = $payment_stmt->fetch(PDO::FETCH_ASSOC)) {
            // If a successful payment already exists, don't create a new one
            if ($row['payment_status'] == 'paid') {
                return false;
            }
            $existing_payments[] = $row;
        }

        // Generate redirect and IPN URLs
        $redirectUrl = BASE_URL . "/payment/callback";
        $ipnUrl = BASE_URL . "/payment/ipn";

        // Create a unique reference for this payment
        $orderId = time() . "_" . $booking_id;
        $extraData = "booking_id=" . $booking_id;

        // Request payment URL from MoMo
        $result = $this->momoPayment->createPaymentRequest($orderId, $amount, $order_info, $redirectUrl, $ipnUrl, $extraData);

        if ($result && isset($result['payUrl'])) {
            // Save payment record to database
            $this->payment->booking_id = $booking_id;
            $this->payment->amount = $amount;
            $this->payment->payment_method = 'MoMo';
            $this->payment->transaction_id = $orderId;
            $this->payment->payment_status = 'pending';

            if ($this->payment->create()) {
                return $result['payUrl'];
            }
        }

        return false;
    }

    /**
     * Process payment callback from payment gateway
     * 
     * @param array $response_data Response data from payment gateway
     * @return array|false Result with booking ID and payment status or false if failed
     */
    public function processPaymentCallback($response_data)
    {
        // Verify MoMo payment response
        if (!$this->momoPayment->verifyPaymentResponse($response_data)) {
            return false;
        }

        // Extract transaction ID and result code
        $transaction_id = $response_data['orderId'];
        $result_code = isset($response_data['resultCode']) ? $response_data['resultCode'] : null;

        // Extract booking ID from transaction ID
        $parts = explode('_', $transaction_id);
        $booking_id = isset($parts[1]) ? $parts[1] : 0;

        if (!$booking_id) {
            return false;
        }

        // Get payment details
        $this->payment->transaction_id = $transaction_id;

        if (!$this->payment->readByTransactionId()) {
            return false;
        }

        // Update payment status based on result code
        $payment_status = ($result_code == '0') ? 'paid' : 'failed';
        $this->payment->payment_status = $payment_status;

        if ($this->payment->updateByTransactionId()) {
            // Update booking payment status
            $this->booking->id = $this->payment->booking_id;

            if ($this->booking->readOne()) {
                $this->booking->payment_status = $payment_status;
                $this->booking->updatePaymentStatus();

                // If payment is successful, update booking status to confirmed
                if ($payment_status == 'paid') {
                    $this->booking->booking_status = 'confirmed';
                    $this->booking->updateStatus();
                }
            }

            return [
                'success' => true,
                'booking_id' => $this->payment->booking_id,
                'status' => $payment_status
            ];
        }

        return false;
    }

    /**
     * Process IPN (Instant Payment Notification)
     * 
     * @param array $response_data Response data from payment gateway
     * @return bool Success status
     */
    public function processPaymentIPN($response_data)
    {
        // This method is similar to processPaymentCallback but is called 
        // directly from the payment gateway rather than redirecting the user

        // Extract transaction ID and result code
        $transaction_id = isset($response_data['orderId']) ? $response_data['orderId'] : null;
        $result_code = isset($response_data['resultCode']) ? $response_data['resultCode'] : null;

        if (!$transaction_id || $result_code === null) {
            return false;
        }

        // Get payment details
        $this->payment->transaction_id = $transaction_id;

        if (!$this->payment->readByTransactionId()) {
            return false;
        }

        // Update payment status based on result code
        $payment_status = ($result_code == '0') ? 'paid' : 'failed';
        $this->payment->payment_status = $payment_status;

        if ($this->payment->updateByTransactionId()) {
            // Update booking payment status
            $this->booking->id = $this->payment->booking_id;

            if ($this->booking->readOne()) {
                $this->booking->payment_status = $payment_status;
                $this->booking->updatePaymentStatus();

                // If payment is successful, update booking status to confirmed
                if ($payment_status == 'paid') {
                    $this->booking->booking_status = 'confirmed';
                    $this->booking->updateStatus();
                }
            }

            return true;
        }

        return false;
    }

    /**
     * Get payment details by booking ID
     * 
     * @param int $booking_id Booking ID
     * @return array|false Payment details or false if not found
     */
    public function getPaymentByBooking($booking_id)
    {
        $this->payment->booking_id = $booking_id;
        $payment_stmt = $this->payment->readByBooking();
        $payments = [];

        while ($row = $payment_stmt->fetch(PDO::FETCH_ASSOC)) {
            $payments[] = $row;
        }

        return !empty($payments) ? $payments : false;
    }

    /**
     * Get payment details by transaction ID
     * 
     * @param string $transaction_id Transaction ID
     * @return array|false Payment details or false if not found
     */
    public function getPaymentByTransaction($transaction_id)
    {
        $this->payment->transaction_id = $transaction_id;

        if ($this->payment->readByTransactionId()) {
            return [
                'id' => $this->payment->id,
                'booking_id' => $this->payment->booking_id,
                'amount' => $this->payment->amount,
                'payment_method' => $this->payment->payment_method,
                'transaction_id' => $this->payment->transaction_id,
                'payment_status' => $this->payment->payment_status,
                'created_at' => $this->payment->created_at,
                'updated_at' => $this->payment->updated_at
            ];
        }

        return false;
    }

    /**
     * Refund a payment
     * 
     * @param int $payment_id Payment ID
     * @return bool Success status
     */
    public function refundPayment($payment_id)
    {
        // In a real application, this would call the MoMo refund API
        // For now, we'll just update the payment status

        // Get payment details
        $query = "SELECT * FROM payments WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $payment_id);
        $stmt->execute();

        $payment = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$payment) {
            return false;
        }

        // Update payment status to refunded
        $query = "UPDATE payments SET payment_status = 'refunded', updated_at = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $payment_id);

        if ($stmt->execute()) {
            // Update booking payment status
            $query = "UPDATE bookings SET payment_status = 'refunded', updated_at = NOW() WHERE id = :booking_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':booking_id', $payment['booking_id']);

            return $stmt->execute();
        }

        return false;
    }

    /**
     * Generate payment statistics for admin dashboard
     * 
     * @param string $period Time period (day, week, month, year)
     * @return array Payment statistics
     */
    public function getPaymentStatistics($period = 'month')
    {
        // Define time period filter
        $where_clause = "";

        switch ($period) {
            case 'day':
                $where_clause = "WHERE DATE(created_at) = CURRENT_DATE()";
                break;
            case 'week':
                $where_clause = "WHERE YEARWEEK(created_at, 1) = YEARWEEK(CURRENT_DATE(), 1)";
                break;
            case 'month':
                $where_clause = "WHERE MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE())";
                break;
            case 'year':
                $where_clause = "WHERE YEAR(created_at) = YEAR(CURRENT_DATE())";
                break;
            default:
                $where_clause = "WHERE MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE())";
        }

        // Get payment statistics
        $query = "SELECT 
                    COUNT(*) as total_payments,
                    SUM(amount) as total_amount,
                    COUNT(CASE WHEN payment_status = 'paid' THEN 1 END) as successful_payments,
                    COUNT(CASE WHEN payment_status = 'failed' THEN 1 END) as failed_payments,
                    COUNT(CASE WHEN payment_status = 'refunded' THEN 1 END) as refunded_payments
                FROM payments
                $where_clause";

        $stmt = $this->db->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get payment methods distribution
     * 
     * @return array Payment methods with count and percentage
     */
    public function getPaymentMethodsDistribution()
    {
        $query = "SELECT 
                    payment_method,
                    COUNT(*) as count,
                    COUNT(*) * 100.0 / (SELECT COUNT(*) FROM payments) as percentage
                FROM payments
                WHERE payment_status = 'paid'
                GROUP BY payment_method";

        $stmt = $this->db->prepare($query);
        $stmt->execute();

        $methods = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $methods[] = $row;
        }

        return $methods;
    }
}