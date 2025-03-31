<?php
require_once 'models/Payment.php';
require_once 'models/Booking.php';
require_once 'utils/MoMoPayment.php';

class PaymentController
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

    // Process MoMo payment callback
    public function callback()
    {
        // Get MoMo response parameters
        $response_data = $_GET;

        if (!isset($response_data['orderId'])) {
            $_SESSION['error'] = "Yêu cầu thanh toán không hợp lệ.";
            header('Location: ' . BASE_URL);
            exit;
        }
        // Log the response data for debugging
        error_log('MoMo Response: ' . print_r($response_data, true));
        // Extract booking ID from order ID
        $orderId = $response_data['orderId'];
        $parts = explode('_', $orderId);
        $booking_id = isset($parts[1]) ? $parts[1] : 0;

        // Get payment details
        $this->payment->transaction_id = $orderId;

        if (!$this->payment->readByTransactionId()) {
            $_SESSION['error'] = "Không tìm thấy thông tin thanh toán.";
            header('Location: ' . BASE_URL);
            exit;
        }

        // Process result code
        $resultCode = $response_data['resultCode'];

        if ($resultCode == '0') {
            // Payment successful
            $this->payment->payment_status = 'paid';
            $this->payment->updateByTransactionId();

            // Update booking payment status
            $this->booking->id = $this->payment->booking_id;

            if ($this->booking->readOne()) {
                $this->booking->payment_status = 'paid';
                $this->booking->updatePaymentStatus();

                // Update booking status to confirmed
                $this->booking->booking_status = 'confirmed';
                $this->booking->updateStatus();

                $_SESSION['success'] = "Thanh toán thành công! Đơn đặt xe của bạn đã được xác nhận.";
            } else {
                $_SESSION['success'] = "Thanh toán thành công! Nhưng không thể cập nhật trạng thái đơn.";
            }
        } else {
            // Payment failed
            $this->payment->payment_status = 'failed';
            $this->payment->updateByTransactionId();

            $_SESSION['error'] = "Thanh toán thất bại. Mã lỗi: " . $resultCode;
        }

        // Redirect to booking details
        header('Location: ' . BASE_URL . '/booking/details/' . $booking_id);
        exit;
    }

    // Process MoMo IPN (Instant Payment Notification)
    public function ipn()
    {
        // Get data from the request
        $data = file_get_contents('php://input');
        $response_data = json_decode($data, true);

        // Verify MoMo signature and process payment
        if ($response_data && isset($response_data['signature'])) {
            // In a real implementation, verify the signature with MoMo

            // Extract booking ID from order ID
            $orderId = $response_data['orderId'];
            $parts = explode('_', $orderId);
            $booking_id = isset($parts[1]) ? $parts[1] : 0;

            // Process payment based on resultCode
            $resultCode = $response_data['resultCode'];

            $this->payment->transaction_id = $orderId;

            if ($this->payment->readByTransactionId()) {
                if ($resultCode == '0') {
                    // Payment successful
                    $this->payment->payment_status = 'paid';
                    $this->payment->updateByTransactionId();

                    // Update booking payment status
                    $this->booking->id = $this->payment->booking_id;

                    if ($this->booking->readOne()) {
                        $this->booking->payment_status = 'paid';
                        $this->booking->updatePaymentStatus();

                        // Update booking status to confirmed
                        $this->booking->booking_status = 'confirmed';
                        $this->booking->updateStatus();
                    }
                } else {
                    // Payment failed
                    $this->payment->payment_status = 'failed';
                    $this->payment->updateByTransactionId();
                }
            }
        }

        // Return success response to MoMo
        header('Content-Type: application/json');
        echo json_encode(['result' => true]);
        exit;
    }
}
