<?php include 'views/shared/header.php'; ?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">Đánh giá xe</h3>
                </div>
                <div class="card-body">
                    <div class="car-info mb-4">
                        <div class="d-flex">
                            <div class="me-3">
                                <?php if (!empty($car_image)): ?>
                                    <img src="<?php echo BASE_URL . '/' . $car_image; ?>" alt="<?php echo $car_brand . ' ' . $car_model; ?>" style="width: 120px; height: 80px; object-fit: cover;" class="rounded">
                                <?php else: ?>
                                    <img src="<?php echo BASE_URL; ?>/public/images/no-image.jpg" alt="No Image" style="width: 120px; height: 80px; object-fit: cover;" class="rounded">
                                <?php endif; ?>
                            </div>
                            <div>
                                <h4><?php echo $car_brand . ' ' . $car_model; ?></h4>
                                <p class="text-muted">Đơn thuê xe #<?php echo $booking_id; ?></p>
                                <a href="<?php echo BASE_URL; ?>/cars/details/<?php echo $car_id; ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-external-link-alt me-1"></i> Xem chi tiết xe
                                </a>
                            </div>
                        </div>
                    </div>

                    <form action="<?php echo BASE_URL; ?>/review/create/<?php echo $booking_id; ?>" method="post">
                        <div class="mb-4">
                            <label class="form-label">Đánh giá của bạn:</label>
                            <div class="rating-stars mb-2">
                                <div class="d-flex justify-content-center">
                                    <?php for ($i = 5; $i >= 1; $i--): ?>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input d-none" type="radio" name="rating" id="rating<?php echo $i; ?>" value="<?php echo $i; ?>" <?php echo (isset($_SESSION['form_data']['rating']) && $_SESSION['form_data']['rating'] == $i) ? 'checked' : ($i == 5 ? 'checked' : ''); ?>>
                                            <label class="form-check-label star-label" for="rating<?php echo $i; ?>">
                                                <i class="far fa-star fa-2x"></i>
                                            </label>
                                        </div>
                                    <?php endfor; ?>
                                </div>
                                <div class="rating-text text-center mt-2">Tuyệt vời</div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="comment" class="form-label">Nhận xét:</label>
                            <textarea class="form-control" id="comment" name="comment" rows="5" placeholder="Chia sẻ trải nghiệm của bạn với xe này..." required><?php echo isset($_SESSION['form_data']['comment']) ? $_SESSION['form_data']['comment'] : ''; ?></textarea>
                            <div class="form-text">Nhận xét của bạn sẽ giúp người dùng khác có quyết định tốt hơn khi thuê xe.</div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">Gửi đánh giá</button>
                            <a href="<?php echo BASE_URL; ?>/booking/details/<?php echo $booking_id; ?>" class="btn btn-outline-secondary">Quay lại</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header bg-info text-white">
                    <h4 class="mb-0">Hướng dẫn đánh giá</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Nên đánh giá</h5>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="fas fa-check-circle text-success me-3"></i>
                                    <span>Chất lượng xe</span>
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="fas fa-check-circle text-success me-3"></i>
                                    <span>Sự hỗ trợ của chủ xe</span>
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="fas fa-check-circle text-success me-3"></i>
                                    <span>Trải nghiệm lái xe</span>
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="fas fa-check-circle text-success me-3"></i>
                                    <span>Tình trạng vệ sinh của xe</span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5>Không nên đánh giá</h5>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="fas fa-times-circle text-danger me-3"></i>
                                    <span>Thông tin cá nhân</span>
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="fas fa-times-circle text-danger me-3"></i>
                                    <span>Ngôn ngữ thiếu tôn trọng</span>
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="fas fa-times-circle text-danger me-3"></i>
                                    <span>Vấn đề không liên quan đến xe</span>
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="fas fa-times-circle text-danger me-3"></i>
                                    <span>Quảng cáo hoặc spam</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.star-label {
    cursor: pointer;
    color: #ccc;
    transition: color 0.2s;
    padding: 0 5px;
}

.star-label:hover,
.star-label:hover ~ .star-label,
input[name="rating"]:checked ~ label .fa-star {
    color: #ffc107;
}

input[name="rating"]:checked + label .fa-star,
input[name="rating"]:checked + label .fa-star ~ .star-label .fa-star {
    color: #ffc107;
}

input[name="rating"]:checked + label .far.fa-star {
    font-weight: 900;
    content: "\f005";
    color: #ffc107;
}

.rating-stars {
    direction: rtl;
    text-align: center;
}

.rating-stars .form-check-inline {
    margin-right: 0;
}

.rating-stars input[type="radio"]:checked ~ label i,
.rating-stars input[type="radio"]:checked ~ label ~ label i,
.rating-stars input[type="radio"]:checked + label i,
.rating-stars label:hover i,
.rating-stars label:hover ~ label i {
    font-weight: 900;
    /* Make it a solid star */
    content: "\f005";
    font-family: "Font Awesome 5 Free";
    color: #ffc107;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ratingInputs = document.querySelectorAll('input[name="rating"]');
    const ratingText = document.querySelector('.rating-text');
    const ratingLabels = document.querySelectorAll('.star-label i');
    
    // Set initial rating text based on selected rating
    updateRatingText();
    
    // Replace all far fa-star with fas fa-star when checked or hovered
    ratingInputs.forEach(input => {
        input.addEventListener('change', function() {
            updateRatingText();
            updateStarVisuals();
        });
    });
    
    // Update rating text based on selected rating
    function updateRatingText() {
        const selectedRating = document.querySelector('input[name="rating"]:checked').value;
        let ratingMessage = '';
        
        switch (parseInt(selectedRating)) {
            case 1:
                ratingMessage = 'Rất tệ';
                break;
            case 2:
                ratingMessage = 'Không hài lòng';
                break;
            case 3:
                ratingMessage = 'Bình thường';
                break;
            case 4:
                ratingMessage = 'Hài lòng';
                break;
            case 5:
                ratingMessage = 'Tuyệt vời';
                break;
            default:
                ratingMessage = 'Hãy chọn đánh giá';
        }
        
        ratingText.textContent = ratingMessage;
    }
    
    // Update star visuals (solid vs outline)
    function updateStarVisuals() {
        const checkedRating = document.querySelector('input[name="rating"]:checked').value;
        
        ratingLabels.forEach((star, index) => {
            // Convert RTL index to actual rating value (5 to 1)
            const starValue = 5 - index;
            
            if (starValue <= checkedRating) {
                star.classList.remove('far');
                star.classList.add('fas');
            } else {
                star.classList.remove('fas');
                star.classList.add('far');
            }
        });
    }
    
    // Initial star visuals update
    updateStarVisuals();
});
</script>

<?php
// Clear form data from session
if (isset($_SESSION['form_data'])) {
    unset($_SESSION['form_data']);
}
?>

<?php include 'views/shared/footer.php'; ?>