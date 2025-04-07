<?php
require_once 'models/Car.php';
require_once 'models/Review.php';

class HomeController
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    // Display the homepage
    public function index()
    {
        // Get featured cars for homepage
        $featured_cars = $this->getFeaturedCars();

        // Sample testimonials (in a real app, this would come from the database)
        $testimonials = [
            [
                'name' => 'Nguyễn Văn A',
                'avatar' => 'avatar1.jpg',
                'rating' => 5,
                'comment' => 'Dịch vụ thuê xe rất tuyệt vời. Xe sạch sẽ, thủ tục đơn giản và dễ dàng. Chắc chắn sẽ sử dụng lại!'
            ],
            [
                'name' => 'Trần Thị B',
                'avatar' => 'avatar2.jpg',
                'rating' => 4,
                'comment' => 'Tôi rất hài lòng với trải nghiệm thuê xe. Chủ xe rất thân thiện và nhiệt tình hỗ trợ.'
            ],
            [
                'name' => 'Lê Văn C',
                'avatar' => 'avatar3.jpg',
                'rating' => 5,
                'comment' => 'Giá cả hợp lý, xe chất lượng tốt. Tôi sẽ giới thiệu dịch vụ này cho bạn bè và người thân.'
            ]
        ];

        include 'views/home.php';
    }

    // Get featured cars for homepage
    private function getFeaturedCars($limit = 12)
    {
        $query = "SELECT c.*, u.fullname as owner_name, 
                (SELECT image_path FROM car_images WHERE car_id = c.id AND is_primary = 1 LIMIT 1) as primary_image,
                AVG(r.rating) as avg_rating, COUNT(r.id) as review_count
            FROM cars c
            LEFT JOIN users u ON c.owner_id = u.id
            LEFT JOIN reviews r ON c.id = r.car_id
            WHERE c.status = 'approved'
            GROUP BY c.id
            ORDER BY avg_rating DESC, c.created_at DESC
            LIMIT :limit";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        $featured_cars = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $featured_cars[] = $row;
        }

        return $featured_cars;
    }

    // About us page
    public function about()
    {
        include 'views/about.php';
    }

    // Contact us page
    public function contact()
    {
        include 'views/contact.php';
    }

    // FAQ page
    public function faq()
    {
        include 'views/faq.php';
    }

    // Terms and conditions page
    public function terms()
    {
        include 'views/terms.php';
    }

    // Privacy policy page
    public function privacy()
    {
        include 'views/privacy.php';
    }
}
