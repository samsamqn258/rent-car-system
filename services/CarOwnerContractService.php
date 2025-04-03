<?php
require_once 'models/CarOwnerContract.php';
class CarOwnerContractService {
    private $db;
    private $carOwnerContractModel;

    public function __construct($db) {
        $this->db = $db;
        $this->carOwnerContractModel = new CarOwnerContract($db);
    }

    public function createDefaultContract(int $ownerId): bool {
        // Thực hiện các nghiệp vụ kiểm tra dữ liệu trước khi tạo hợp đồng
        if (empty($ownerId) || !is_numeric($ownerId) || $ownerId <= 0) {
            return false; // Dữ liệu không hợp lệ
        }

        // Lấy ngày hiện tại
        $startDate = date('Y-m-d');

        // Tính ngày kết thúc là 1 năm sau
        $endDate = date('Y-m-d', strtotime('+1 year', strtotime($startDate)));

        // Phí hợp đồng cố định
        $contractFee = 20000;

        // Gán dữ liệu cho model
        $this->carOwnerContractModel->owner_id = $ownerId;
        $this->carOwnerContractModel->start_date = $startDate;
        $this->carOwnerContractModel->end_date = $endDate;
        $this->carOwnerContractModel->contract_fee = $contractFee;

        // Gọi phương thức tạo của model
        return $this->carOwnerContractModel->create();
    }

    /**
     * Đánh dấu hợp đồng là đã thanh toán.
     * Thực hiện các nghiệp vụ kiểm tra trước khi cập nhật trạng thái.
     *
     * @param int $contractId
     * @return bool Trả về true nếu cập nhật thành công, false nếu thất bại.
     */
    public function markContractAsPaid(int $contractId): bool {
        // Kiểm tra xem contractId có hợp lệ không
        if (empty($contractId) || !is_numeric($contractId) || $contractId <= 0) {
            return false;
        }

        // Gán ID cho model
        $this->carOwnerContractModel->id = $contractId;

        // Gọi phương thức đánh dấu đã thanh toán của model
        return $this->carOwnerContractModel->markAsPaid();
    }

    /**
     * Duyệt hợp đồng.
     * Chỉ duyệt những hợp đồng đã được thanh toán.
     *
     * @param int $contractId
     * @return bool Trả về true nếu duyệt thành công, false nếu thất bại hoặc chưa thanh toán.
     */
    public function approveContract(int $contractId): bool {
        // Kiểm tra xem contractId có hợp lệ không
        if (empty($contractId) || !is_numeric($contractId) || $contractId <= 0) {
            return false;
        }

        // Gán ID cho model
        $this->carOwnerContractModel->id = $contractId;

        // Gọi phương thức duyệt hợp đồng của model
        return $this->carOwnerContractModel->approveContract();
    }

    /**
     * Lấy danh sách tất cả hợp đồng chưa thanh toán.
     * Thực hiện các nghiệp vụ lọc hoặc xử lý dữ liệu sau khi lấy (nếu cần).
     *
     * @return PDOStatement|false
     */
    public function getUnpaidContracts() {
        return $this->carOwnerContractModel->getUnpaidContracts();
    }

    /**
     * Lấy danh sách tất cả hợp đồng đã thanh toán.
     *
     * @return PDOStatement|false
     */
    public function getPaidContracts() {
        return $this->carOwnerContractModel->getPaidContracts();
    }

    /**
     * Lấy danh sách các hợp đồng đã thanh toán nhưng chưa được duyệt.
     *
     * @return PDOStatement|false
     */
    public function getUnapprovedPaidContracts() {
        return $this->carOwnerContractModel->getUnapprovedPaidContracts();
    }

    /**
     * Lấy danh sách tất cả hợp đồng đã được duyệt.
     *
     * @return PDOStatement|false
     */
    public function getApprovedContracts() {
        return $this->carOwnerContractModel->getApprovedContracts();
    }

    /**
     * Lấy danh sách các hợp đồng sắp hết hạn trong vòng 2 tháng tới.
     *
     * @return PDOStatement|false
     */
    public function getExpiringContracts() {
        return $this->carOwnerContractModel->getExpiringContracts();
    }

    /**
     * Lấy danh sách các hợp đồng thuộc về một chủ xe cụ thể.
     *
     * @param int $ownerId
     * @return PDOStatement|false
     */
    public function getContractsByOwnerId(int $ownerId) {
        // Kiểm tra xem ownerId có hợp lệ không
        if (empty($ownerId) || !is_numeric($ownerId) || $ownerId <= 0) {
            return false;
        }
        return $this->carOwnerContractModel->getContractsByOwnerId($ownerId);
    }
     /**
     * Kiểm tra xem chủ xe có hợp đồng nào hay không.
     *
     * @param int $ownerId ID của chủ xe.
     * @return bool True nếu chủ xe có ít nhất một hợp đồng, false nếu không.
     */
    public function hasAnyContractForOwner(int $ownerId): bool {
        return $this->carOwnerContractModel->hasAnyContract($ownerId);
    }

    /**
     * Kiểm tra xem tất cả các hợp đồng của chủ xe đã hết hạn hay chưa.
     * Trước tiên kiểm tra xem chủ xe có hợp đồng nào không.
     *
     * @param int $ownerId ID của chủ xe.
     * @return bool True nếu chủ xe không có hợp đồng nào hoặc tất cả hợp đồng đã hết hạn, false nếu có ít nhất một hợp đồng còn hiệu lực.
     */
    public function allContractsOfOwnerExpired(int $ownerId): bool {
        return $this->carOwnerContractModel->allContractsExpired($ownerId);
    }

    /**
     * Kiểm tra xem chủ xe có hợp đồng nào còn hiệu lực hay không.
     *
     * @param int $ownerId ID của chủ xe.
     * @return bool True nếu chủ xe có ít nhất một hợp đồng còn hiệu lực, false nếu không có hợp đồng nào còn hiệu lực.
     */
    public function hasActiveContractForOwner(int $ownerId): bool {
        return $this->carOwnerContractModel->hasActiveContract($ownerId);
    }
    public function getContractsWithOwnerName(){
        return $this->carOwnerContractModel->getContractsWithOwnerName();
    }
}

?>