<?php
require_once 'models/Payment.php';
require_once 'models/Booking.php';

class MoMoPaymentService
{
    private $db;
    private $endpoint;
    private $partnerCode;
    private $accessKey;
    private $secretKey;
    private $redirectUrl;
    private $ipnUrl;

    public function __construct($db)
    {
        $this->db = $db;
        $this->endpoint = MOMO_ENDPOINT;
        $this->partnerCode = MOMO_PARTNER_CODE;
        $this->accessKey = MOMO_ACCESS_KEY;
        $this->secretKey = MOMO_SECRET_KEY;
        $this->redirectUrl = BASE_URL . "/payment/callback";
        $this->ipnUrl = BASE_URL . "/payment/ipn"; // IPN = Instant Payment Notification
    }

    // ✅ Tạo yêu cầu thanh toán MoMo
    public function createPaymentRequest($booking_id, $amount, $order_info)
    {
        $requestId = time() . rand(1000, 9999);
        $orderId =  time() . "_" . $booking_id;
        error_log("IPN URL: " . $this->ipnUrl);


        $amount = (int) round($amount);

        $rawHash = "accessKey=" . $this->accessKey .
            "&amount=" . (string)$amount .
            "&extraData=" . "extraMomo=$booking_id" .
            "&ipnUrl=" . $this->ipnUrl .
            "&orderId=" . $orderId .
            "&orderInfo=" . $order_info .
            "&partnerCode=" . $this->partnerCode .
            "&redirectUrl=" . $this->redirectUrl .
            "&requestId=" . $requestId .
            "&requestType=" . "captureWallet";

        error_log('rawhash' . $rawHash);
        $signature = hash_hmac('sha256', $rawHash, $this->secretKey);

        // Dữ liệu gửi đến MoMo
        $data = [
            'partnerCode' => $this->partnerCode,
            'accessKey' => $this->accessKey,
            'requestId' => $requestId,
            'amount' => (string)$amount, // Đảm bảo amount là chuỗi số nguyên
            'orderId' => $orderId,
            'orderInfo' => $order_info,
            'redirectUrl' => $this->redirectUrl,
            'ipnUrl' => $this->ipnUrl,
            'requestType' => 'captureWallet',
            'extraData' => "extraMomo=$booking_id",
            'signature' => $signature
        ];
        error_log("Data sent to MoMo API: " . json_encode($data));

        // Gọi API MoMo
        $response = $this->execPostRequest($this->endpoint, $data);
        $jsonResult = json_decode($response, true);
        // if ($jsonResult && isset($jsonResult['payUrl'])) {
        //     error_log("Pay URL: " . $jsonResult['payUrl']);
        //     return $jsonResult['payUrl'];
        // } else {
        //     error_log("MoMo API Response Error: " . json_encode($jsonResult));
        //     return false;
        // }
        if ($jsonResult && isset($jsonResult['payUrl'])) {
            // Save payment to database
            $payment = new Payment($this->db);
            $payment->booking_id = $booking_id;
            $payment->amount = $amount;
            $payment->payment_method = 'MoMo';
            $payment->transaction_id = $orderId;
            $payment->payment_status = 'pending';
            if (!$payment->create()) {
                error_log("Failed to save payment to database. Error: " . json_encode($this->db->errorInfo()));
                return false;
            }

            return $jsonResult['payUrl'];
        } else {
            error_log("MoMo API Response Error: " . json_encode($jsonResult));
            return false;
        }
    }

    // ✅ Xử lý phản hồi từ MoMo
    public function processPaymentCallback($response_data)
    {
        if (!$this->verifyPaymentResponse($response_data)) {
            return false;
        }

        $transaction_id = $response_data['orderId'];
        $status = ($response_data['resultCode'] == 0) ? 'paid' : 'failed';

        // Cập nhật trạng thái thanh toán
        $payment = new Payment($this->db);
        $payment->transaction_id = $transaction_id;

        if ($payment->readByTransactionId()) {
            $payment->payment_status = $status;
            $payment->updateByTransactionId();

            // Cập nhật trạng thái đặt xe
            $booking = new Booking($this->db);
            $booking->id = $payment->booking_id;

            if ($booking->readOne()) {
                $booking->payment_status = $status;
                $booking->updatePaymentStatus();

                if ($status == 'paid') {
                    $booking->booking_status = 'confirmed';
                    $booking->updateStatus();
                }
            }

            return [
                'success' => true,
                'booking_id' => $payment->booking_id,
                'status' => $status
            ];
        }

        return false;
    }

    // ✅ Kiểm tra chữ ký từ MoMo
    private function verifyPaymentResponse($response_data)
    {
        if (!isset($response_data['signature'])) return false;

        // Tạo chữ ký để kiểm tra
        $rawHash = "partnerCode=" . $response_data['partnerCode'] .
            "&accessKey=" . $response_data['accessKey'] .
            "&requestId=" . $response_data['requestId'] .
            "&amount=" . $response_data['amount'] .
            "&orderId=" . $response_data['orderId'] .
            "&orderInfo=" . $response_data['orderInfo'] .
            "&transId=" . $response_data['transId'] .
            "&message=" . $response_data['message'] .
            "&responseTime=" . $response_data['responseTime'] .
            "&errorCode=" . $response_data['errorCode'] .
            "&payType=" . $response_data['payType'] .
            "&extraData=" . $response_data['extraData'];

        $signature = hash_hmac('sha256', $rawHash, $this->secretKey);

        return $signature === $response_data['signature'];
    }

    // ✅ Gọi API MoMo
    function execPostRequest($url, $data)
    {
        // Mã hóa dữ liệu thành JSON
        $jsonData = json_encode($data);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData); // Sử dụng JSON đã mã hóa
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($jsonData) // Tính độ dài của chuỗi JSON
            )
        );
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

        // Execute POST
        $result = curl_exec($ch);

        // Check for cURL errors
        if (curl_errno($ch)) {
            error_log('cURL Error: ' . curl_error($ch));
            return false;
        }

        // Close connection
        curl_close($ch);

        return $result;
    }
}