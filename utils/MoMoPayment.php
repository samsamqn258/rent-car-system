<?php
class MoMoPayment
{
    private $endpoint;
    private $partnerCode;
    private $accessKey;
    private $secretKey;
    private $redirectUrl;
    private $ipnUrl;

    public function __construct()
    {
        $this->endpoint = MOMO_ENDPOINT;
        $this->partnerCode = MOMO_PARTNER_CODE;
        $this->accessKey = MOMO_ACCESS_KEY;
        $this->secretKey = MOMO_SECRET_KEY;
    }

    /**
     * Create a MoMo payment request
     * 
     * @param string $orderId Order ID
     * @param int $amount Payment amount
     * @param string $orderInfo Order information
     * @param string $redirectUrl URL to redirect after payment
     * @param string $ipnUrl URL for IPN (Instant Payment Notification)
     * @param string $extraData Additional data (optional)
     * @return array|false Payment URL or false if failed
     */
    public function createPaymentRequest($orderId, $amount, $orderInfo, $redirectUrl, $ipnUrl, $extraData = '')
    {
        $this->redirectUrl = $redirectUrl;
        $this->ipnUrl = $ipnUrl;

        // Create request ID
        $requestId = time() . "_" . rand(1000, 9999);

        // Prepare request data
        $rawData = [
            'partnerCode' => $this->partnerCode,
            'accessKey' => $this->accessKey,
            'requestId' => $requestId,
            'amount' => $amount,
            'orderId' => $orderId,
            'orderInfo' => $orderInfo,
            'returnUrl' => $this->redirectUrl,
            'notifyUrl' => $this->ipnUrl,
            'extraData' => $extraData
        ];

        // Create signature
        $signature = $this->createSignature($rawData);
        $rawData['signature'] = $signature;

        // Send request to MoMo
        $response = $this->execPostRequest($rawData);

        if ($response) {
            $jsonResult = json_decode($response, true);
            return $jsonResult;
        }

        return false;
    }

    /**
     * Verify MoMo payment response
     * 
     * @param array $responseData MoMo response data
     * @return bool True if signature is valid
     */
    public function verifyPaymentResponse($responseData)
    {
        if (!isset($responseData['signature'])) {
            return false;
        }

        // Get received signature
        $receivedSignature = $responseData['signature'];

        // Build raw data for signature verification
        $rawData = [
            'partnerCode' => $responseData['partnerCode'],
            'accessKey' => $responseData['accessKey'],
            'requestId' => $responseData['requestId'],
            'amount' => $responseData['amount'],
            'orderId' => $responseData['orderId'],
            'orderInfo' => $responseData['orderInfo'],
            'returnUrl' => $responseData['returnUrl'],
            'notifyUrl' => $responseData['notifyUrl'],
            'extraData' => isset($responseData['extraData']) ? $responseData['extraData'] : ''
        ];

        // Create expected signature
        $expectedSignature = $this->createSignature($rawData);

        // Verify signatures match
        return $receivedSignature === $expectedSignature;
    }

    /**
     * Create signature for MoMo request
     * 
     * @param array $data Request data
     * @return string HMAC SHA256 signature
     */
    private function createSignature($data)
    {
        // Build raw hash string
        $rawHash = "partnerCode=" . $data['partnerCode'] .
            "&accessKey=" . $data['accessKey'] .
            "&requestId=" . $data['requestId'] .
            "&amount=" . $data['amount'] .
            "&orderId=" . $data['orderId'] .
            "&orderInfo=" . $data['orderInfo'] .
            "&returnUrl=" . $data['returnUrl'] .
            "&notifyUrl=" . $data['notifyUrl'] .
            "&extraData=" . $data['extraData'];

        // Create signature
        return hash_hmac('sha256', $rawHash, $this->secretKey);
    }

    /**
     * Execute HTTP POST request to MoMo API
     * 
     * @param array $data Request data
     * @return string|false Response or false if failed
     */
    private function execPostRequest($data)
    {
        $ch = curl_init($this->endpoint);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen(json_encode($data))
            )
        );

        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            error_log('MoMo API Error: ' . curl_error($ch));
            return false;
        }

        curl_close($ch);

        return $result;
    }
}
