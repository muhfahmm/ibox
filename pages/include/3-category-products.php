<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>iBox Indonesia - Kategori Produk</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

</head>

<body>
    <div class="category-products-container">
        <style>
            * {
                padding: 0;
                margin: 0;
                box-sizing: border-box;
                font-family: 'Poppins', sans-serif;
            }

            body {
                background-color: #f7f7f7;
                color: #333;
            }

            .category-products-container {
                max-width: 1400px;
                margin: 0 auto;
                padding: 0 20px;
            }

            .category-products-wrapper {
                padding: 40px 0;
            }

            h3 {
                font-size: 28px;
                font-weight: 600;
                color: #333;
                margin-bottom: 30px;
                text-align: center;
                position: relative;
                padding-bottom: 15px;
            }

            h3::after {
                content: '';
                position: absolute;
                bottom: 0;
                left: 50%;
                transform: translateX(-50%);
                width: 80px;
                height: 3px;
                background-color: #007aff;
            }

            /* CATEGORY PRODUCTS SLIDER STYLES */
            .category-products-wrapper {
                position: relative;
                margin-bottom: 30px;
                overflow: hidden;
                padding: 10px 0;
            }

            .category-products {
                display: flex;
                justify-content: flex-start;
                flex-wrap: nowrap;
                gap: 15px;
                transition: transform 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
                width: max-content;
                cursor: grab;
                user-select: none;
                padding: 5px 20px;
            }

            .category-products.grabbing {
                cursor: grabbing;
                transition: none;
            }

            .product-items {
                display: flex;
                flex-direction: column;
                align-items: center;
                text-align: center;
                width: 110px;
                padding: 15px;
                border-radius: 12px;
                transition: all 0.3s ease;
                cursor: pointer;
                flex-shrink: 0;
                background-color: white;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            }

            .product-items:hover {
                background-color: #f5f5f7;
                transform: translateY(-5px);
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            }

            .product-items.active {
                background-color: #f0f7ff;
                border: 2px solid #007aff;
                box-shadow: 0 5px 15px rgba(0, 122, 255, 0.15);
            }

            .product-items img {
                height: 50px;
                margin-bottom: 15px;
                object-fit: contain;
            }

            .product-items-title {
                width: 100%;
            }

            .product-items-name {
                font-weight: 700;
                font-size: 16px;
                margin-bottom: 5px;
                color: #1d1d1f;
            }

            .product-items-price {
                font-weight: 600;
                font-size: 13px;
                color: #86868b;
            }

            /* SWIPE INDICATOR FOR MOBILE */
            .swipe-hint {
                display: block;
                text-align: center;
                margin-top: 10px;
                font-size: 12px;
                color: #86868b;
                animation: pulse 2s infinite;
            }

            .swipe-hint i {
                margin-right: 5px;
                font-size: 14px;
            }

            @keyframes pulse {

                0%,
                100% {
                    opacity: 0.5;
                }

                50% {
                    opacity: 1;
                }
            }

            /* Responsif untuk layar lebih kecil */
            @media (max-width: 768px) {
                .category-products {
                    gap: 10px;
                    padding: 5px 15px;
                }

                .product-items {
                    width: 100px;
                    padding: 10px;
                }

                .product-items img {
                    height: 40px;
                    margin-bottom: 10px;
                }

                .product-items-name {
                    font-size: 14px;
                }

                .product-items-price {
                    font-size: 11px;
                }
            }

            @media (max-width: 480px) {
                .category-products {
                    gap: 8px;
                    padding: 5px 10px;
                }

                .product-items {
                    width: 90px;
                    padding: 8px;
                }

                .product-items img {
                    height: 35px;
                }
            }

            @media (min-width: 1040px) {
                .category-products-wrapper {
                    overflow: visible;
                    padding: 0;
                }

                .category-products {
                    flex-wrap: wrap;
                    justify-content: center;
                    width: 100%;
                    gap: 20px;
                    padding: 0;
                    cursor: default;
                }

                .product-items {
                    width: 120px;
                }

                .swipe-hint {
                    display: none;
                }
            }

            /* Header */
            .category-header {
                text-align: center;
                margin-bottom: 40px;
            }

            .category-header h1 {
                font-size: 36px;
                font-weight: 700;
                color: #333;
                margin-bottom: 15px;
            }

            .category-header p {
                font-size: 16px;
                color: #666;
                max-width: 600px;
                margin: 0 auto;
                line-height: 1.6;
            }

            @media (max-width: 768px) {
                .category-header h1 {
                    font-size: 28px;
                }
            }

            @media (max-width: 480px) {
                .category-header h1 {
                    font-size: 24px;
                }
            }
        </style>
        <div class="category-products-wrapper">
            <div class="category-header">
                <h1>Kategori Produk Apple</h1>
                <p>Pilih kategori produk Apple yang Anda minati</p>
            </div>

            <!-- Category Products Slider -->
            <div class="category-products-wrapper">
                <div class="category-products" id="categoryProducts">
                    <div class="product-items" data-category="mac" data-category-name="Mac">
                        <img src="https://cdnpro.eraspace.com/media/wysiwyg/banner/IMG_3966.png">
                        <div class="product-items-title">
                            <p class="product-items-name">Mac</p>
                            <p class="product-items-price">Mulai dari Rp11 juta</p>
                        </div>
                    </div>
                    <div class="product-items" data-category="iphone" data-category-name="iPhone">
                        <img src="https://cdnpro.eraspace.com/media/wysiwyg/store_card_13_iphone_nav_202509_f4cfa37f_36ba_4094_9b94_e110f8a4e707.png">
                        <div class="product-items-title">
                            <p class="product-items-name">iPhone</p>
                            <p class="product-items-price">Mulai dari Rp8 juta</p>
                        </div>
                    </div>
                    <div class="product-items" data-category="ipad" data-category-name="iPad">
                        <img src="https://esmeralda.cygnuss-district8.com/media/wysiwyg/iPad_-_Cek_yang_terbaru.webp?rand=1720254832">
                        <div class="product-items-title">
                            <p class="product-items-name">iPad</p>
                            <p class="product-items-price">Mulai dari Rp4 juta</p>
                        </div>
                    </div>
                    <div class="product-items" data-category="watch" data-category-name="Apple Watch">
                        <img src="https://cdnpro.eraspace.com/media/wysiwyg/image-watch.png">
                        <div class="product-items-title">
                            <p class="product-items-name">WATCH</p>
                            <p class="product-items-price">Mulai dari Rp3juta</p>
                        </div>
                    </div>
                    <div class="product-items" data-category="music" data-category-name="Music">
                        <img src="https://cdnpro.eraspace.com/media/wysiwyg/url_upload_691a6fad14870.tmp-691a6fad3c537.png">
                        <div class="product-items-title">
                            <p class="product-items-name">Music</p>
                            <p class="product-items-price">Mulai dari Rp2 juta</p>
                        </div>
                    </div>
                    <div class="product-items" data-category="accessories" data-category-name="Aksesori">
                        <img src="https://bim4s4kti.eraspace.com/media/wysiwyg/store-card-13-accessories-nav-202509.png">
                        <div class="product-items-title">
                            <p class="product-items-name">Aksesori</p>
                            <p class="product-items-price">Mulai dari Rp200 ribu</p>
                        </div>
                    </div>
                    <div class="product-items" data-category="airtag" data-category-name="AirTag">
                        <img src="https://esmeralda.cygnuss-district8.com/media/wysiwyg/ibox-v4/images/berbagai-produk/airtag.png">
                        <div class="product-items-title">
                            <p class="product-items-name">AirTag</p>
                            <p class="product-items-price">Mulai dari Rp400 ribu</p>
                        </div>
                    </div>
                </div>

                <!-- SWIPE HINT UNTUK MOBILE -->
                <div class="swipe-hint" id="swipeHint">
                    <i class="bi bi-arrow-left-right"></i> Geser untuk melihat lebih banyak
                </div>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const categoryProducts = document.getElementById('categoryProducts');
                const swipeHint = document.getElementById('swipeHint');
                const categoryItems = document.querySelectorAll('.product-items');

                // ===== FUNGSI UNTUK KATEGORI PRODUK SLIDER =====
                let isCategoryDragging = false;
                let categoryStartX = 0;
                let categoryCurrentTranslate = 0;
                let categoryPrevTranslate = 0;

                // Inisialisasi Category Products Slider
                function initCategorySlider() {
                    // Hitung total lebar konten
                    const containerWidth = categoryProducts.parentElement.clientWidth;
                    const items = categoryProducts.children;
                    let totalWidth = 0;

                    // Hitung total lebar semua item termasuk gap
                    const style = window.getComputedStyle(categoryProducts);
                    const gap = parseFloat(style.gap) || 15;

                    for (let i = 0; i < items.length; i++) {
                        totalWidth += items[i].offsetWidth;
                        if (i < items.length - 1) {
                            totalWidth += gap;
                        }
                    }

                    // Reset posisi slider jika konten lebih kecil dari container
                    if (totalWidth <= containerWidth) {
                        categoryProducts.style.transform = 'translateX(0)';
                        categoryCurrentTranslate = 0;
                        categoryPrevTranslate = 0;
                    }

                    setupCategoryDragEvents();
                }

                function setupCategoryDragEvents() {
                    categoryProducts.addEventListener('touchstart', categoryTouchStart);
                    categoryProducts.addEventListener('touchmove', categoryTouchMove);
                    categoryProducts.addEventListener('touchend', categoryTouchEnd);

                    categoryProducts.addEventListener('mousedown', categoryMouseDown);
                    categoryProducts.addEventListener('mousemove', categoryMouseMove);
                    categoryProducts.addEventListener('mouseup', categoryMouseUp);
                    categoryProducts.addEventListener('mouseleave', categoryMouseLeave);

                    categoryProducts.style.cursor = 'grab';
                }

                // Event handlers untuk Category Products Slider
                function categoryTouchStart(e) {
                    isCategoryDragging = true;
                    categoryStartX = e.touches[0].clientX;
                    categoryPrevTranslate = categoryCurrentTranslate;
                    categoryProducts.classList.add('grabbing');
                }

                function categoryTouchMove(e) {
                    if (!isCategoryDragging) return;

                    const currentX = e.touches[0].clientX;
                    const diffX = currentX - categoryStartX;
                    const newTranslate = categoryPrevTranslate + diffX;

                    // Batasi pergerakan slider
                    setCategorySliderPosition(newTranslate);
                }

                function categoryTouchEnd() {
                    isCategoryDragging = false;
                    categoryProducts.classList.remove('grabbing');

                    // Snap ke posisi terdekat
                    snapCategorySlider();
                }

                function categoryMouseDown(e) {
                    isCategoryDragging = true;
                    categoryStartX = e.clientX;
                    categoryPrevTranslate = categoryCurrentTranslate;
                    categoryProducts.classList.add('grabbing');
                    e.preventDefault();
                }

                function categoryMouseMove(e) {
                    if (!isCategoryDragging) return;

                    const currentX = e.clientX;
                    const diffX = currentX - categoryStartX;
                    const newTranslate = categoryPrevTranslate + diffX;

                    setCategorySliderPosition(newTranslate);
                }

                function categoryMouseUp() {
                    isCategoryDragging = false;
                    categoryProducts.classList.remove('grabbing');
                    snapCategorySlider();
                }

                function categoryMouseLeave() {
                    if (isCategoryDragging) {
                        isCategoryDragging = false;
                        categoryProducts.classList.remove('grabbing');
                        snapCategorySlider();
                    }
                }

                // Fungsi untuk mengatur posisi slider dengan batasan
                function setCategorySliderPosition(position) {
                    const container = categoryProducts.parentElement;
                    const containerWidth = container.clientWidth;
                    const items = categoryProducts.children;

                    // Hitung total lebar semua item termasuk gap
                    const style = window.getComputedStyle(categoryProducts);
                    const gap = parseFloat(style.gap) || 15;
                    let totalWidth = 0;

                    for (let i = 0; i < items.length; i++) {
                        totalWidth += items[i].offsetWidth;
                        if (i < items.length - 1) {
                            totalWidth += gap;
                        }
                    }

                    // Batasi pergerakan slider
                    // Posisi maksimal (ke kiri): 0 (tidak bergerak dari posisi awal)
                    // Posisi minimal (ke kanan): containerWidth - totalWidth
                    // Jika totalWidth <= containerWidth, maka posisi harus 0
                    if (totalWidth <= containerWidth) {
                        position = 0;
                    } else {
                        const minTranslate = Math.min(0, containerWidth - totalWidth - 20); // 20px padding untuk estetika
                        const maxTranslate = 0;
                        position = Math.max(minTranslate, Math.min(maxTranslate, position));
                    }

                    categoryProducts.style.transform = `translateX(${position}px)`;
                    categoryCurrentTranslate = position;
                }

                // Fungsi untuk snap slider ke posisi yang tepat
                function snapCategorySlider() {
                    const container = categoryProducts.parentElement;
                    const containerWidth = container.clientWidth;
                    const items = categoryProducts.children;

                    // Hitung total lebar semua item termasuk gap
                    const style = window.getComputedStyle(categoryProducts);
                    const gap = parseFloat(style.gap) || 15;
                    let totalWidth = 0;

                    for (let i = 0; i < items.length; i++) {
                        totalWidth += items[i].offsetWidth;
                        if (i < items.length - 1) {
                            totalWidth += gap;
                        }
                    }

                    // Jika totalWidth <= containerWidth, snap ke 0
                    if (totalWidth <= containerWidth) {
                        animateCategorySlider(0);
                        return;
                    }

                    // Snap ke posisi terdekat yang valid
                    const minTranslate = Math.min(0, containerWidth - totalWidth - 20);
                    const maxTranslate = 0;

                    let snapPosition = categoryCurrentTranslate;

                    // Jika posisi saat ini dekat dengan batas kiri (0), snap ke 0
                    if (snapPosition > -50) {
                        snapPosition = 0;
                    }
                    // Jika posisi saat ini dekat dengan batas kanan, snap ke batas kanan
                    else if (snapPosition < minTranslate + 50) {
                        snapPosition = minTranslate;
                    }

                    animateCategorySlider(snapPosition);
                }

                // Fungsi animasi untuk slider kategori
                function animateCategorySlider(targetPosition) {
                    const startPosition = categoryCurrentTranslate;
                    const duration = 300;
                    const startTime = performance.now();

                    function animate(currentTime) {
                        const elapsed = currentTime - startTime;
                        const progress = Math.min(elapsed / duration, 1);

                        // Easing function (easeOutCubic)
                        const ease = 1 - Math.pow(1 - progress, 3);

                        const currentPosition = startPosition + (targetPosition - startPosition) * ease;

                        categoryProducts.style.transform = `translateX(${currentPosition}px)`;
                        categoryCurrentTranslate = currentPosition;

                        if (progress < 1) {
                            requestAnimationFrame(animate);
                        }
                    }

                    requestAnimationFrame(animate);
                }

                // ===== EVENT LISTENERS =====
                window.addEventListener('resize', debounce(initCategorySlider, 250));

                categoryItems.forEach(item => {
                    item.addEventListener('click', function() {
                        const category = this.dataset.category;
                        const categoryName = this.dataset.categoryName;

                        // Hapus class active dari semua item
                        categoryItems.forEach(i => i.classList.remove('active'));

                        // Tambah class active ke item yang diklik
                        this.classList.add('active');

                        // Kirim event custom untuk komunikasi dengan komponen lain
                        const event = new CustomEvent('categoryProductSelected', {
                            detail: {
                                category: category,
                                categoryName: categoryName
                            }
                        });
                        document.dispatchEvent(event);

                        console.log(`Kategori produk dipilih: ${category} (${categoryName})`);
                    });
                });

                categoryProducts.addEventListener('touchstart', function() {
                    swipeHint.style.opacity = '0.5';
                    setTimeout(() => {
                        swipeHint.style.display = 'none';
                    }, 300);
                });

                categoryProducts.addEventListener('mousedown', function() {
                    swipeHint.style.opacity = '0.5';
                    setTimeout(() => {
                        swipeHint.style.display = 'none';
                    }, 300);
                });

                // ===== UTILITY FUNCTIONS =====
                function debounce(func, wait) {
                    let timeout;
                    return function executedFunction(...args) {
                        const later = () => {
                            clearTimeout(timeout);
                            func(...args);
                        };
                        clearTimeout(timeout);
                        timeout = setTimeout(later, wait);
                    };
                }

                // ===== INISIALISASI =====
                initCategorySlider();
            });
        </script>
    </div>


</body>

</html>