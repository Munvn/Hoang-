<div class="container">
    <style>
      .container_slider {
        overflow: hidden;
        position: relative;
        border: 2px solid #ddd;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        background-color: white;
        max-height: 500px; /* Đặt chiều cao tối đa */
        margin: 0 auto; /* Căn giữa slider theo chiều ngang */
        width: 100%;
        border-radius: 10px;
    }

    .list-images_slider {
        display: flex;
        transition: transform 0.5s ease;
        width: 100%; /* Đảm bảo khớp với chiều rộng của container */
    }

    .list-images_slider img {
        object-fit: cover;
        max-height: 500px;
        width: 100%; /* Đảm bảo ảnh chiếm toàn bộ chiều rộng */
    }

    .index-images_slider {
        position: absolute;
        bottom: 10px;
        display: flex;
        left: 50%;
        transform: translateX(-50%);
    }

    .index-item_slider {
        border: 2px solid #999;
        padding: 5px;
        margin: 3px;
        border-radius: 50%;
        cursor: pointer;
    }

    .index-item_slider.active_slider {
        background-color: #999;
    }

    .container_slider,
    .list-images_slider {
        margin: 0;
        padding: 0;
    }

    /* Media Query cho các thiết bị nhỏ */
    @media (max-width: 768px) {
        .container_slider {
            max-height: 350px; /* Giảm chiều cao cho thiết bị nhỏ */
        }
    }
  </style>
    <div class="container_slider" style="margin-top: 5px;">
        <div class="list-images_slider">
            <img src="image/1.jpg" width="100%" alt="">
            <img src="image/2.jpg" width="100%" alt="">
            <img src="image/3.jpg" width="100%" alt="">
            <img src="image/4.jpg" width="100%" alt="">
        </div>
        <div class="index-images_slider">
            <div class="index-item_slider index-item-0 active_slider"></div>
            <div class="index-item_slider index-item-1"></div>
            <div class="index-item_slider index-item-2"></div>
            <div class="index-item_slider index-item-3"></div>
        </div>
    </div>
    <script>
        const listImage = document.querySelector('.list-images_slider');
        const imgs = document.querySelectorAll('.list-images_slider img'); // Chọn tất cả các ảnh
        const length = imgs.length;
        let current = 0;

        // Lấy chiều rộng của mỗi ảnh
        const getImageWidth = () => {
            return imgs[0].offsetWidth; // Lấy chiều rộng của ảnh đầu tiên
        };

        // Cập nhật chỉ mục chấm tròn đang hoạt động
        const updateActiveIndex = () => {
            document.querySelector('.active_slider').classList.remove('active_slider');
            document.querySelector(`.index-item_slider:nth-child(${current + 1})`).classList.add('active_slider');
        };

        // Xử lý khi nhấn vào một chấm tròn
        const indexItems = document.querySelectorAll('.index-item_slider');
        indexItems.forEach((item, index) => {
            item.addEventListener('click', () => {
                current = index; // Cập nhật chỉ mục khi nhấn vào chấm tròn
                listImage.style.transform = 'translateX(-' + (getImageWidth() * current) + 'px)'; // Dịch chuyển slider
                updateActiveIndex();
            });
        });

        // Hàm chuyển slide tự động
        const handleChangeSlide = () => {
            const width = getImageWidth(); // Lấy chiều rộng của ảnh
            if (current === length - 1) {
                current = 0;
                listImage.style.transition = 'none'; // Tắt chuyển tiếp khi quay lại ảnh đầu tiên
                listImage.style.transform = 'translateX(0px)'; // Quay về ảnh đầu tiên
                setTimeout(() => {
                    listImage.style.transition = 'transform 0.5s ease'; // Bật lại chuyển tiếp
                }, 50); // Đảm bảo việc chuyển ảnh diễn ra sau khi quay về ảnh đầu tiên
            } else {
                current++;
                listImage.style.transform = 'translateX(-' + (width * current) + 'px)'; // Dịch chuyển slider
            }

            updateActiveIndex();
        };

        // Tự động chuyển slide mỗi 3 giây
        let handleEventChangeSlide = setInterval(handleChangeSlide, 3000);
    </script>
</div>