<?php
class CarOwnerContract
{
    private $conn;
    private $table_name = "car_owner_contracts";

    // Car Owner Contract properties
    public $id;
    public $owner_id;
    public $start_date;
    public $end_date;
    public $contract_fee;
    public $status;
    public $approved;
    public $created_at;
    public $updated_at;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Tạo hợp đồng với phí mặc định + 20.000 VND
    public function create()
    {
        // Kiểm tra xem chủ xe có hợp đồng nào chưa hết hạn hoặc sắp hết hạn trong vòng 1 tuần không
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " 
                  WHERE owner_id = :owner_id 
                  AND (end_date >= NOW() OR end_date BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 7 DAY))";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":owner_id", $this->owner_id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row['total'] > 0) {
            // Nếu có hợp đồng chưa hết hạn hoặc sắp hết hạn, không cho phép tạo mới
            return false;
        }

        // Nếu không có hợp đồng nào hợp lệ, tiếp tục tạo hợp đồng mới
        $this->contract_fee += 20000; // Thêm cứng 20.000 VND

        $query = "INSERT INTO " . $this->table_name . "
                SET
                    owner_id = :owner_id,
                    start_date = :start_date,
                    end_date = :end_date,
                    contract_fee = :contract_fee,
                    status = 'pending_payment', -- Mặc định chưa thanh toán
                    approved = 0, -- Mặc định chưa duyệt
                    created_at = NOW(),
                    updated_at = NOW()";

        $stmt = $this->conn->prepare($query);

        // Sanitize data
        $this->owner_id = htmlspecialchars(strip_tags($this->owner_id));
        $this->start_date = htmlspecialchars(strip_tags($this->start_date));
        $this->end_date = htmlspecialchars(strip_tags($this->end_date));
        $this->contract_fee = htmlspecialchars(strip_tags($this->contract_fee));

        // Bind data
        $stmt->bindParam(":owner_id", $this->owner_id);
        $stmt->bindParam(":start_date", $this->start_date);
        $stmt->bindParam(":end_date", $this->end_date);
        $stmt->bindParam(":contract_fee", $this->contract_fee);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }

        return false;
    }

    // Cập nhật trạng thái thanh toán của hợp đồng
    public function markAsPaid()
    {
        $query = "UPDATE " . $this->table_name . "
                SET status = 'paid', updated_at = NOW()
                WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    // Duyệt hợp đồng (chỉ duyệt khi đã thanh toán)
    public function approveContract()
    {
        $query = "UPDATE " . $this->table_name . "
                SET approved = 1, updated_at = NOW()
                WHERE id = :id AND status = 'paid'";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    // Lấy danh sách hợp đồng chưa thanh toán
    public function getUnpaidContracts()
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE status = 'pending_payment'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Lấy danh sách hợp đồng đã thanh toán
    public function getPaidContracts()
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE status = 'paid'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Lấy danh sách hợp đồng chưa duyệt nhưng đã thanh toán
    public function getUnapprovedPaidContracts()
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE status = 'paid' AND approved = 0";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Lấy danh sách hợp đồng đã duyệt
    public function getApprovedContracts()
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE status = 'paid' AND approved = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
    // Lấy danh sách hợp đồng sắp hết hạn trước 2 tháng
    public function getExpiringContracts()
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE end_date BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 2 MONTH)";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
    // Lấy danh sách hợp đồng theo owner_id
    public function getContractsByOwnerId($owner_id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE owner_id = :owner_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':owner_id', $owner_id);
        $stmt->execute();
        return $stmt;
    }
    /**
     * Kiểm tra xem chủ xe có hợp đồng nào hay không.
     *
     * @param int $owner_id ID của chủ xe.
     * @return bool True nếu chủ xe có ít nhất một hợp đồng, false nếu không.
     */
    public function hasAnyContract($owner_id)
    {
        $query = "SELECT COUNT(*) FROM " . $this->table_name . "
                  WHERE owner_id = :owner_id AND status = 'paid' AND approved = 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':owner_id', $owner_id);
        $stmt->execute();

        return (bool) $stmt->fetchColumn();
    }
    /**
     * Kiểm tra xem tất cả các hợp đồng của chủ xe đã hết hạn hay chưa.
     * Trước tiên kiểm tra xem chủ xe có hợp đồng nào không.
     *
     * @param int $owner_id ID của chủ xe.
     * @return bool True nếu chủ xe không có hợp đồng nào hoặc tất cả hợp đồng đã hết hạn, false nếu có ít nhất một hợp đồng còn hiệu lực.
     */
    public function allContractsExpired($owner_id)
    {
        // Kiểm tra xem chủ xe có hợp đồng nào không
        if (!$this->hasAnyContract($owner_id)) {
            return true; // Nếu không có hợp đồng nào thì coi như tất cả đã "hết hạn" (theo logic nghiệp vụ)
        }

        $query = "SELECT COUNT(*) FROM " . $this->table_name . "
                  WHERE owner_id = :owner_id AND end_date >= CURDATE() ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':owner_id', $owner_id);
        $stmt->execute();

        return ((int) $stmt->fetchColumn()) === 0;
    }

    /**
     * Kiểm tra xem chủ xe có hợp đồng nào còn hiệu lực hay không.
     *
     * @param int $owner_id ID của chủ xe.
     * @return bool True nếu chủ xe có ít nhất một hợp đồng còn hiệu lực, false nếu không có hợp đồng nào còn hiệu lực.
     */
    public function hasActiveContract($owner_id)
    {
        $query = "SELECT COUNT(*) FROM " . $this->table_name . "
                  WHERE owner_id = :owner_id AND end_date >= CURDATE() AND status = 'paid' AND approved = 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':owner_id', $owner_id);
        $stmt->execute();

        return (bool) $stmt->fetchColumn();
    }
    public function getContractsWithOwnerName()
    {
        $query = "SELECT c.*, u.username AS owner_name
                  FROM " . $this->table_name . " c
                  JOIN users u ON c.owner_id = u.id";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}