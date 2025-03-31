<?php
class Validator {
    /**
     * Validate required fields
     * 
     * @param array $data Form data
     * @param array $fields Required field names
     * @return array Error messages
     */
    public function validateRequired($data, $fields) {
        $errors = [];
        
        foreach ($fields as $field) {
            if (!isset($data[$field]) || trim($data[$field]) === '') {
                $errors[] = $this->getFieldLabel($field) . ' không được để trống';
            }
        }
        
        return $errors;
    }
    
    /**
     * Validate email format
     * 
     * @param string $email Email to validate
     * @return bool True if valid
     */
    public function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Validate password strength
     * 
     * @param string $password Password to validate
     * @param int $minLength Minimum length (default: 6)
     * @return bool True if valid
     */
    public function validatePassword($password, $minLength = 6) {
        return strlen($password) >= $minLength;
    }
    
    /**
     * Validate numeric field
     * 
     * @param string $value Value to validate
     * @return bool True if numeric
     */
    public function validateNumeric($value) {
        return is_numeric($value);
    }
    
    /**
     * Validate date format
     * 
     * @param string $date Date to validate
     * @param string $format Date format (default: Y-m-d)
     * @return bool True if valid
     */
    public function validateDate($date, $format = 'Y-m-d') {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }
    
    /**
     * Validate date range
     * 
     * @param string $startDate Start date
     * @param string $endDate End date
     * @return bool True if valid
     */
    public function validateDateRange($startDate, $endDate) {
        if (!$this->validateDate($startDate) || !$this->validateDate($endDate)) {
            return false;
        }
        
        return strtotime($startDate) <= strtotime($endDate);
    }
    
    /**
     * Validate phone number format
     * 
     * @param string $phone Phone number to validate
     * @return bool True if valid
     */
    public function validatePhone($phone) {
        return preg_match('/^[0-9]{10,11}$/', $phone) === 1;
    }
    
    /**
     * Validate image file
     * 
     * @param array $file Uploaded file data
     * @param int $maxSize Maximum file size in bytes
     * @param array $allowedTypes Allowed MIME types
     * @return bool|string True if valid, error message if not
     */
    public function validateImage($file, $maxSize = 5242880, $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg']) {
        // Check if file was uploaded
        if ($file['error'] !== 0) {
            return 'Lỗi khi tải lên file';
        }
        
        // Check file size
        if ($file['size'] > $maxSize) {
            return 'Kích thước file quá lớn (tối đa ' . ($maxSize / 1048576) . 'MB)';
        }
        
        // Check file type
        if (!in_array($file['type'], $allowedTypes)) {
            return 'Loại file không được hỗ trợ (chỉ hỗ trợ JPEG, JPG, PNG)';
        }
        
        return true;
    }
    
    /**
     * Get human-readable field label from field name
     * 
     * @param string $fieldName Field name
     * @return string Field label
     */
    private function getFieldLabel($fieldName) {
        $labels = [
            'username' => 'Tên đăng nhập',
            'email' => 'Email',
            'password' => 'Mật khẩu',
            'password_confirm' => 'Xác nhận mật khẩu',
            'fullname' => 'Họ tên',
            'phone' => 'Số điện thoại',
            'address' => 'Địa chỉ',
            'brand' => 'Hãng xe',
            'model' => 'Mẫu xe',
            'year' => 'Năm sản xuất',
            'car_type' => 'Loại xe',
            'seats' => 'Số chỗ ngồi',
            'price_per_day' => 'Giá thuê mỗi ngày',
            'latitude' => 'Vĩ độ',
            'longitude' => 'Kinh độ',
            'description' => 'Mô tả',
            'start_date' => 'Ngày bắt đầu',
            'end_date' => 'Ngày kết thúc',
            'rating' => 'Đánh giá',
            'comment' => 'Nhận xét',
            'code' => 'Mã khuyến mãi',
            'discount_percentage' => 'Phần trăm giảm giá',
            'start_date_promo' => 'Ngày bắt đầu khuyến mãi',
            'end_date_promo' => 'Ngày kết thúc khuyến mãi'
        ];
        
        return isset($labels[$fieldName]) ? $labels[$fieldName] : ucfirst($fieldName);
    }
}