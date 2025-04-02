<?php
require_once 'models/Car.php';
require_once 'models/CarImage.php';
require_once 'models/Review.php';

class CarService
{
    private $db;
    private $car;

    public function __construct($db)
    {
        $this->db = $db;
        $this->car = new Car($db);
    }

    // Add a new car with images
    public function addCar($car_data, $images)
    {
        // Set car properties
        $this->car->owner_id = $car_data['owner_id'];
        $this->car->brand = $car_data['brand'];
        $this->car->model = $car_data['model'];
        $this->car->year = $car_data['year'];
        $this->car->car_type = $car_data['car_type'];
        $this->car->seats = $car_data['seats'];
        $this->car->price_per_day = $car_data['price_per_day'];
        $this->car->address = $car_data['address'];
        $this->car->latitude = $car_data['latitude'];
        $this->car->longitude = $car_data['longitude'];
        $this->car->description = $car_data['description'];

        // Create car record
        if (!$this->car->create()) {
            return false;
        }

        // Handle image uploads
        $car_id = $this->car->id;
        $upload_dir = UPLOAD_DIR . 'cars/';

        // Create directory if not exists
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $car_image = new CarImage($this->db);
        $car_image->car_id = $car_id;

        // Process each image
        foreach ($images['name'] as $key => $name) {
            if ($images['error'][$key] === 0) {
                $tmp_name = $images['tmp_name'][$key];
                $ext = pathinfo($name, PATHINFO_EXTENSION);
                $new_name = uniqid() . '.' . $ext;
                $destination = $upload_dir . $new_name;

                if (move_uploaded_file($tmp_name, $destination)) {
                    $car_image->image_path = 'public/uploads/cars/' . $new_name;
                    $car_image->is_primary = ($key === 0) ? 1 : 0; // First image is primary

                    $car_image->create();
                }
            }
        }

        return $car_id;
    }

    // Update car details and images
    public function updateCar($car_id, $owner_id, $car_data, $images = null)
    {
        // Verify car ownership
        $this->car->id = $car_id;
        $this->car->owner_id = $owner_id;

        // Check if car exists and belongs to owner
        if (!$this->car->readOne() || $this->car->owner_id != $owner_id) {
            return false;
        }

        // Update car properties
        $this->car->brand = $car_data['brand'];
        $this->car->model = $car_data['model'];
        $this->car->year = $car_data['year'];
        $this->car->car_type = $car_data['car_type'];
        $this->car->seats = $car_data['seats'];
        $this->car->price_per_day = $car_data['price_per_day'];
        $this->car->address = $car_data['address'];
        $this->car->latitude = $car_data['latitude'];
        $this->car->longitude = $car_data['longitude'];
        $this->car->description = $car_data['description'];

        // Update car record
        if (!$this->car->update()) {
            return false;
        }

        // Handle new image uploads if any
        if ($images && !empty($images['name'][0])) {
            $upload_dir = UPLOAD_DIR . 'cars/';

            // Create directory if not exists
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $car_image = new CarImage($this->db);
            $car_image->car_id = $car_id;

            // Process each image
            foreach ($images['name'] as $key => $name) {
                if ($images['error'][$key] === 0) {
                    $tmp_name = $images['tmp_name'][$key];
                    $ext = pathinfo($name, PATHINFO_EXTENSION);
                    $new_name = uniqid() . '.' . $ext;
                    $destination = $upload_dir . $new_name;

                    if (move_uploaded_file($tmp_name, $destination)) {
                        $car_image->image_path = 'public/uploads/cars/' . $new_name;

                        // Set as primary if requested or if it's the first image
                        $is_primary = isset($car_data['primary_image']) && $car_data['primary_image'] == $key;
                        $car_image->is_primary = $is_primary ? 1 : 0;

                        if ($is_primary) {
                            // Reset all other images to non-primary
                            $car_image->resetPrimaryImages();
                        }

                        $car_image->create();
                    }
                }
            }
        }

        return true;
    }

    // Get car details with images and reviews
    public function getCarDetails($car_id)
    {
        $this->car->id = $car_id;

        if (!$this->car->readOne()) {
            return false;
        }

        // Get car images
        $images = $this->car->getImages();
        $car_images = [];

        while ($row = $images->fetch(PDO::FETCH_ASSOC)) {
            $car_images[] = $row;
        }

        // Get car reviews
        $review = new Review($this->db);
        $review->car_id = $car_id;
        $reviews_stmt = $review->readByCar();
        $reviews = [];

        while ($row = $reviews_stmt->fetch(PDO::FETCH_ASSOC)) {
            $reviews[] = $row;
        }

        // Prepare car details array
        $car_details = [
            'id' => $this->car->id,
            'owner_id' => $this->car->owner_id,
            'brand' => $this->car->brand,
            'model' => $this->car->model,
            'year' => $this->car->year,
            'car_type' => $this->car->car_type,
            'seats' => $this->car->seats,
            'price_per_day' => $this->car->price_per_day,
            'address' => $this->car->address,
            'latitude' => $this->car->latitude,
            'longitude' => $this->car->longitude,
            'description' => $this->car->description,
            'status' => $this->car->status,
            'owner_name' => $this->car->owner_name,
            'avg_rating' => $this->car->avg_rating,
            'review_count' => $this->car->review_count,
            'images' => $car_images,
            'reviews' => $reviews
        ];

        return $car_details;
    }

    // Search cars by criteria
    public function searchCars($search_params)
    {
        $cars_stmt = $this->car->readAvailable($search_params);
        $cars = [];

        while ($row = $cars_stmt->fetch(PDO::FETCH_ASSOC)) {
            $cars[] = $row;
        }

        return $cars;
    }

    public function searchCarByAddress($search_params)
{
    // Gọi hàm readAvailableByAddress để tìm kiếm các xe theo địa chỉ và ngày
    $cars_stmt = $this->car->readAvailableByAddress($search_params);
    $cars = [];

    // Duyệt qua kết quả trả về và đưa các xe vào mảng $cars
    while ($row = $cars_stmt->fetch(PDO::FETCH_ASSOC)) {
        $cars[] = $row;
    }

    // Trả về danh sách các xe nếu tìm thấy, nếu không có xe trả về mảng rỗng
    return $cars ?: [];
}


    // Get cars by owner
    public function getCarsByOwner($owner_id)
    {
        $this->car->owner_id = $owner_id;
        $cars_stmt = $this->car->readByOwner();
        $cars = [];

        while ($row = $cars_stmt->fetch(PDO::FETCH_ASSOC)) {
            $cars[] = $row;
        }

        return $cars;
    }

    // Approve car listing (admin)
    public function approveCar($car_id)
    {
        $this->car->id = $car_id;
        $this->car->status = 'approved';

        return $this->car->updateStatus();
    }

    // Reject car listing (admin)
    public function rejectCar($car_id)
    {
        $this->car->id = $car_id;
        $this->car->status = 'rejected';

        return $this->car->updateStatus();
    }

    // Check car availability for booking dates
    public function isCarAvailable($car_id, $start_date, $end_date)
    {
        $this->car->id = $car_id;

        return $this->car->isAvailable($start_date, $end_date);
    }

    // Calculate total price for a date range
    public function calculateTotalPrice($car_id, $start_date, $end_date)
    {
        $this->car->id = $car_id;

        if (!$this->car->readOne()) {
            return false;
        }

        $start = new DateTime($start_date);
        $end = new DateTime($end_date);
        $days = $start->diff($end)->days + 1; // Include both start and end day

        return $this->car->price_per_day * $days;
    }

    // Get all car brands for filter
    public function getAllBrands()
    {
        $brands_stmt = $this->car->getBrands();
        $brands = [];

        while ($row = $brands_stmt->fetch(PDO::FETCH_ASSOC)) {
            $brands[] = $row['brand'];
        }

        return $brands;
    }
}
