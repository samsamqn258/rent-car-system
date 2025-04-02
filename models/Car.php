<?php
class Car
{
    private $conn;
    private $table_name = "cars";

    // Car properties
    public $id;
    public $owner_id;
    public $brand;
    public $model;
    public $year;
    public $car_type;
    public $seats;
    public $price_per_day;
    public $address;
    public $latitude;
    public $longitude;
    public $description;
    public $status;
    public $created_at;
    public $updated_at;

    // Extra properties for joins
    public $owner_name;
    public $avg_rating;
    public $review_count;
    public $primary_image;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Create new car
    public function create()
    {
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    owner_id = :owner_id,
                    brand = :brand,
                    model = :model,
                    year = :year,
                    car_type = :car_type,
                    seats = :seats,
                    price_per_day = :price_per_day,
                    address = :address,
                    latitude = :latitude,
                    longitude = :longitude,
                    description = :description,
                    status = 'unapproved',
                    created_at = NOW(),
                    updated_at = NOW()";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->owner_id = htmlspecialchars(strip_tags($this->owner_id));
        $this->brand = htmlspecialchars(strip_tags($this->brand));
        $this->model = htmlspecialchars(strip_tags($this->model));
        $this->year = htmlspecialchars(strip_tags($this->year));
        $this->car_type = htmlspecialchars(strip_tags($this->car_type));
        $this->seats = htmlspecialchars(strip_tags($this->seats));
        $this->price_per_day = htmlspecialchars(strip_tags($this->price_per_day));
        $this->address = htmlspecialchars(strip_tags($this->address));
        $this->latitude = htmlspecialchars(strip_tags($this->latitude));
        $this->longitude = htmlspecialchars(strip_tags($this->longitude));
        $this->description = htmlspecialchars(strip_tags($this->description));

        // Bind values
        $stmt->bindParam(":owner_id", $this->owner_id);
        $stmt->bindParam(":brand", $this->brand);
        $stmt->bindParam(":model", $this->model);
        $stmt->bindParam(":year", $this->year);
        $stmt->bindParam(":car_type", $this->car_type);
        $stmt->bindParam(":seats", $this->seats);
        $stmt->bindParam(":price_per_day", $this->price_per_day);
        $stmt->bindParam(":address", $this->address);
        $stmt->bindParam(":latitude", $this->latitude);
        $stmt->bindParam(":longitude", $this->longitude);
        $stmt->bindParam(":description", $this->description);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }

        return false;
    }

    // Read all available cars
    public function readAvailable($search_params = [])
    {
        $query = "SELECT c.*, u.fullname as owner_name, 
                    (SELECT image_path FROM car_images WHERE car_id = c.id AND is_primary = 1 LIMIT 1) as primary_image,
                    AVG(r.rating) as avg_rating, COUNT(r.id) as review_count
                FROM " . $this->table_name . " c
                LEFT JOIN users u ON c.owner_id = u.id
                LEFT JOIN reviews r ON c.id = r.car_id
                WHERE c.status = 'approved'";

        // Apply search filters
        if (!empty($search_params)) {
            if (isset($search_params['min_price']) && $search_params['min_price'] > 0) {
                $query .= " AND c.price_per_day >= " . intval($search_params['min_price']);
            }

            if (isset($search_params['max_price']) && $search_params['max_price'] > 0) {
                $query .= " AND c.price_per_day <= " . intval($search_params['max_price']);
            }

            if (isset($search_params['brand']) && !empty($search_params['brand'])) {
                $query .= " AND c.brand = '" . $search_params['brand'] . "'";
            }

            if (isset($search_params['car_type']) && !empty($search_params['car_type'])) {
                $query .= " AND c.car_type = '" . $search_params['car_type'] . "'";
            }

            if (isset($search_params['seats']) && $search_params['seats'] > 0) {
                $query .= " AND c.seats >= " . intval($search_params['seats']);
            }
        }

        // Group by car ID to handle multiple reviews
        $query .= " GROUP BY c.id";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    public function readAvailableByAddress($search_params = []) {
        // Khởi tạo câu truy vấn
        $query = "SELECT c.*, u.fullname as owner_name, 
                    (SELECT image_path FROM car_images WHERE car_id = c.id AND is_primary = 1 LIMIT 1) as primary_image,
                    AVG(r.rating) as avg_rating, COUNT(r.id) as review_count
                  FROM " . $this->table_name . " c
                  LEFT JOIN users u ON c.owner_id = u.id
                  LEFT JOIN reviews r ON c.id = r.car_id
                  LEFT JOIN bookings b ON c.id = b.car_id
                  WHERE c.status = 'approved'";
    
        // Kiểm tra nếu có tham số tìm kiếm (address, start_date, end_date)
        if (!empty($search_params)) {
            if (isset($search_params['address']) && !empty($search_params['address'])) {
                // Chia nhỏ địa chỉ thành từng phần (ví dụ: Hồ Chí Minh, Bình Thạnh, Võ Văn Kiệt)
                $address_parts = explode(',', $search_params['address']);
                $address_conditions = [];
    
                foreach ($address_parts as $index => $part) {
                    $address_conditions[] = "c.address LIKE :address$index";
                }
    
                // Kết hợp điều kiện tìm kiếm
                $query .= " AND (" . implode(" AND ", $address_conditions) . ")";
            }
    
            if (isset($search_params['start_date']) && isset($search_params['end_date'])) {
                $query .= " AND c.id NOT IN (
                    SELECT car_id FROM bookings 
                    WHERE start_date <= :end_date AND end_date >= :start_date AND payment_status = 'paid')";
            }
        }
    
        // Nhóm theo car ID để xử lý nhiều đánh giá
        $query .= " GROUP BY c.id";
    
        // Chuẩn bị và thực thi truy vấn
        $stmt = $this->conn->prepare($query);
    
        // Liên kết các tham số tìm kiếm vào truy vấn
        if (isset($search_params['address']) && !empty($search_params['address'])) {
            $address_parts = explode(',', $search_params['address']);
            foreach ($address_parts as $index => $part) {
                $param = "%" . trim($part) . "%"; // Thêm ký tự % để tìm kiếm một phần địa chỉ
                $stmt->bindParam(":address$index", $param);
            }
        }
    
        if (isset($search_params['start_date']) && isset($search_params['end_date'])) {
            $stmt->bindParam(':start_date', $search_params['start_date']);
            $stmt->bindParam(':end_date', $search_params['end_date']);
        }
    
        // Thực thi truy vấn
        $stmt->execute();
    
        return $stmt;
    }
    

    // Read car details by ID
    public function readOne()
    {
        $query = "SELECT c.*, u.fullname as owner_name, u.phone as owner_phone,
                    AVG(r.rating) as avg_rating, COUNT(r.id) as review_count
                FROM " . $this->table_name . " c
                LEFT JOIN users u ON c.owner_id = u.id
                LEFT JOIN reviews r ON c.id = r.car_id
                WHERE c.id = :id
                GROUP BY c.id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->id = $row['id'];
            $this->owner_id = $row['owner_id'];
            $this->brand = $row['brand'];
            $this->model = $row['model'];
            $this->year = $row['year'];
            $this->car_type = $row['car_type'];
            $this->seats = $row['seats'];
            $this->price_per_day = $row['price_per_day'];
            $this->address = $row['address'];
            $this->latitude = $row['latitude'];
            $this->longitude = $row['longitude'];
            $this->description = $row['description'];
            $this->status = $row['status'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            $this->owner_name = $row['owner_name'];
            $this->avg_rating = $row['avg_rating'];
            $this->review_count = $row['review_count'];

            return true;
        }

        return false;
    }

    // Read cars by owner ID
    public function readByOwner()
    {
        $query = "SELECT c.*, 
                    (SELECT image_path FROM car_images WHERE car_id = c.id AND is_primary = 1 LIMIT 1) as primary_image,
                    AVG(r.rating) as avg_rating, COUNT(r.id) as review_count
                FROM " . $this->table_name . " c
                LEFT JOIN reviews r ON c.id = r.car_id
                WHERE c.owner_id = :owner_id
                GROUP BY c.id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':owner_id', $this->owner_id);
        $stmt->execute();

        return $stmt;
    }

    // Update car
    public function update()
    {
        $query = "UPDATE " . $this->table_name . "
                SET
                    brand = :brand,
                    model = :model,
                    year = :year,
                    car_type = :car_type,
                    seats = :seats,
                    price_per_day = :price_per_day,
                    address = :address,
                    latitude = :latitude,
                    longitude = :longitude,
                    description = :description,
                    updated_at = NOW()
                WHERE
                    id = :id AND owner_id = :owner_id";

        $stmt = $this->conn->prepare($query);

        // Sanitize data
        $this->brand = htmlspecialchars(strip_tags($this->brand));
        $this->model = htmlspecialchars(strip_tags($this->model));
        $this->year = htmlspecialchars(strip_tags($this->year));
        $this->car_type = htmlspecialchars(strip_tags($this->car_type));
        $this->seats = htmlspecialchars(strip_tags($this->seats));
        $this->price_per_day = htmlspecialchars(strip_tags($this->price_per_day));
        $this->address = htmlspecialchars(strip_tags($this->address));
        $this->latitude = htmlspecialchars(strip_tags($this->latitude));
        $this->longitude = htmlspecialchars(strip_tags($this->longitude));
        $this->description = htmlspecialchars(strip_tags($this->description));

        // Bind data
        $stmt->bindParam(':brand', $this->brand);
        $stmt->bindParam(':model', $this->model);
        $stmt->bindParam(':year', $this->year);
        $stmt->bindParam(':car_type', $this->car_type);
        $stmt->bindParam(':seats', $this->seats);
        $stmt->bindParam(':price_per_day', $this->price_per_day);
        $stmt->bindParam(':address', $this->address);
        $stmt->bindParam(':latitude', $this->latitude);
        $stmt->bindParam(':longitude', $this->longitude);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':owner_id', $this->owner_id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Update car status
    public function updateStatus()
    {
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

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Check if car is available for a specific date range
    public function isAvailable($start_date, $end_date)
    {
        $query = "SELECT COUNT(*) as count FROM bookings 
          WHERE car_id = :car_id 
          AND booking_status IN ('pending', 'confirmed') 
          AND (
              (start_date <= :start_date1 AND end_date >= :start_date2) OR
              (start_date <= :end_date1 AND end_date >= :end_date2) OR
              (start_date >= :start_date3 AND end_date <= :end_date3)
          )";
        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(':car_id', $this->id, PDO::PARAM_INT);
        $stmt->bindValue(':start_date1', $start_date, PDO::PARAM_STR);
        $stmt->bindValue(':start_date2', $start_date, PDO::PARAM_STR);
        $stmt->bindValue(':end_date1', $end_date, PDO::PARAM_STR);
        $stmt->bindValue(':end_date2', $end_date, PDO::PARAM_STR);
        $stmt->bindValue(':start_date3', $start_date, PDO::PARAM_STR);
        $stmt->bindValue(':end_date3', $end_date, PDO::PARAM_STR);

        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['count'] == 0;
    }

    // Get car images
    public function getImages()
    {
        $query = "SELECT * FROM car_images 
                WHERE car_id = :car_id 
                ORDER BY is_primary DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':car_id', $this->id);
        $stmt->execute();

        return $stmt;
    }

    // Get all car brands for filtering
    public function getBrands()
    {
        $query = "SELECT DISTINCT brand FROM " . $this->table_name . " 
                WHERE status = 'approved'
                ORDER BY brand";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }
}
