<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>iBox Indonesia - Produk Apple</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

</head>

<body>
    <div class="all-products-container">
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

            .all-products-container {
                max-width: 1400px;
                margin: 0 auto;
                padding: 0 20px;
            }

            .all-products-wrapper {
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

            /* ========================= */
            /* CATEGORY TABS SLIDER STYLES */
            /* ========================= */
            .category-tabs-wrapper {
                position: relative;
                margin-bottom: 40px;
                overflow: hidden;
                padding: 10px 0;
            }

            .category-tabs-container {
                display: flex;
                justify-content: flex-start;
                flex-wrap: nowrap;
                gap: 12px;
                transition: transform 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
                cursor: grab;
                user-select: none;
                width: max-content;
                padding: 5px 20px;
            }

            .category-tabs-container.grabbing {
                cursor: grabbing;
                transition: none;
            }

            .category-tab {
                padding: 12px 25px;
                background-color: white;
                border: 2px solid #e0e0e0;
                border-radius: 30px;
                font-weight: 500;
                cursor: pointer;
                transition: all 0.3s ease;
                color: #666;
                flex-shrink: 0;
                white-space: nowrap;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            }

            .category-tab.active {
                background-color: #007aff;
                color: white;
                border-color: #007aff;
                box-shadow: 0 4px 12px rgba(0, 122, 255, 0.25);
            }

            .category-tab:hover:not(.active) {
                border-color: #007aff;
                color: #007aff;
                transform: translateY(-2px);
                box-shadow: 0 4px 8px rgba(0, 122, 255, 0.15);
            }

            /* Tabs swipe hint */
            .tabs-swipe-hint {
                display: block;
                text-align: center;
                margin-top: 10px;
                font-size: 12px;
                color: #86868b;
                animation: pulse 2s infinite;
            }

            .tabs-swipe-hint i {
                margin-right: 5px;
                font-size: 14px;
            }

            /* Responsif untuk tabs slider */
            @media (max-width: 992px) {
                .category-tabs-wrapper {
                    overflow: hidden;
                }

                .category-tabs-container {
                    gap: 12px;
                    padding: 5px 20px;
                }

                .category-tab {
                    padding: 10px 20px;
                    font-size: 14px;
                }
            }

            @media (max-width: 768px) {
                .category-tabs-container {
                    gap: 10px;
                    padding: 5px 15px;
                }

                .category-tab {
                    padding: 8px 18px;
                    font-size: 13px;
                }
            }

            @media (max-width: 480px) {
                .category-tabs-container {
                    gap: 8px;
                    padding: 5px 10px;
                }

                .category-tab {
                    padding: 8px 15px;
                    font-size: 12px;
                }
            }

            @media (min-width: 993px) {
                .category-tabs-wrapper {
                    overflow: visible;
                    padding: 0;
                }

                .category-tabs-container {
                    flex-wrap: wrap;
                    justify-content: center;
                    width: 100%;
                    gap: 15px;
                    padding: 0;
                    cursor: default;
                }

                .category-tab {
                    padding: 12px 25px;
                }

                .tabs-swipe-hint {
                    display: none;
                }
            }

            /* SLIDER CONTAINER UNTUK SEMUA PRODUK */
            .all-products-slider-container {
                position: relative;
                overflow: hidden;
                border-radius: 12px;
                background: white;
                padding: 30px 20px 60px;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
                margin-bottom: 40px;
            }

            .all-products-slider-container.single-slide {
                padding-bottom: 30px;
            }

            .all-products-slider {
                display: flex;
                transition: transform 0.5s ease-in-out;
                gap: 0;
            }

            .all-products-slide {
                min-width: 100%;
                padding: 10px;
                box-sizing: border-box;
                display: flex;
                justify-content: center;
                flex-shrink: 0;
            }

            .all-products-slide-inner {
                display: flex;
                gap: 20px;
                width: 100%;
                justify-content: center;
                flex-wrap: wrap;
            }

            /* PRODUCT CARD STYLES UNTUK SEMUA */
            .product-card {
                background: white;
                border-radius: 12px;
                padding: 20px;
                height: 100%;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
                transition: all 0.3s ease;
                border: 1px solid #f0f0f0;
                display: flex;
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            .product-card:hover {
                transform: translateY(-8px);
                box-shadow: 0 15px 30px rgba(0, 122, 255, 0.1);
                border-color: #007aff;
            }

            .product-image {
                width: 100%;
                max-width: 200px;
                height: 180px;
                object-fit: contain;
                margin-bottom: 15px;
                border-radius: 8px;
                background-color: #f8f9fa;
                padding: 15px;
            }

            .product-badge {
                background-color: #ff3b30;
                color: white;
                padding: 4px 12px;
                border-radius: 20px;
                font-size: 12px;
                font-weight: 500;
                margin-bottom: 10px;
                display: inline-block;
            }

            .product-badge.new {
                background-color: #007aff;
            }

            .product-badge.discount {
                background-color: #34c759;
            }

            .product-name {
                font-size: 16px;
                font-weight: 600;
                color: #333;
                margin-bottom: 8px;
                height: 40px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .product-rating {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 5px;
                margin-bottom: 10px;
                color: #ffc107;
            }

            .product-rating span {
                color: #666;
                font-size: 13px;
                margin-left: 5px;
            }

            .product-price {
                font-size: 16px;
                color: #007aff;
                font-weight: 500;
                margin-bottom: 15px;
            }

            .product-btn {
                background-color: #007aff;
                color: white;
                border: none;
                padding: 10px 20px;
                border-radius: 25px;
                font-weight: 500;
                cursor: pointer;
                transition: all 0.3s ease;
                display: inline-flex;
                align-items: center;
                gap: 8px;
                font-size: 14px;
                margin-top: auto;
            }

            .product-btn:hover {
                background-color: #0056cc;
                transform: scale(1.05);
            }

            /* Navigation Buttons untuk semua slider */
            .all-products-slider-nav {
                position: absolute;
                top: 50%;
                transform: translateY(-50%);
                width: calc(100% - 40px);
                left: 20px;
                display: flex;
                justify-content: space-between;
                pointer-events: none;
                z-index: 20;
            }

            .all-products-slider-nav.hidden {
                display: none;
            }

            .all-products-nav-btn {
                background-color: white;
                border: none;
                width: 45px;
                height: 45px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: all 0.3s ease;
                font-size: 20px;
                color: #333;
                box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
                pointer-events: auto;
                opacity: 1;
                visibility: visible;
            }

            .all-products-nav-btn.hidden {
                display: none;
            }

            .all-products-nav-btn:hover:not(:disabled) {
                background-color: #007aff;
                color: white;
                transform: scale(1.1);
                box-shadow: 0 5px 15px rgba(0, 122, 255, 0.3);
            }

            .all-products-nav-btn:disabled {
                opacity: 0.3;
                cursor: not-allowed;
                background-color: #f0f0f0;
            }

            .all-products-nav-btn:disabled:hover {
                transform: none;
                background-color: #f0f0f0;
                color: #333;
            }

            /* Dots Navigation untuk semua slider */
            .all-products-slider-dots {
                display: flex;
                justify-content: center;
                gap: 10px;
                margin-top: 15px;
                position: absolute;
                bottom: 25px;
                left: 0;
                right: 0;
                z-index: 10;
            }

            .all-products-slider-dots.hidden {
                display: none;
            }

            .all-products-dot {
                width: 12px;
                height: 12px;
                border-radius: 50%;
                background-color: rgba(0, 122, 255, 0.2);
                cursor: pointer;
                transition: all 0.3s ease;
                border: 2px solid transparent;
            }

            .all-products-dot.active {
                background-color: #007aff !important;
                transform: scale(1.2);
                border-color: white !important;
            }

            .all-products-dot:hover {
                background-color: rgba(0, 122, 255, 0.4);
            }

            /* Responsive untuk semua slider - MODIFIKASI UTAMA */
            /* Desktop besar: 4 produk per slide */
            @media (min-width: 1200px) {
                .all-products-slide-inner .product-card {
                    width: calc(25% - 20px);
                    max-width: calc(25% - 20px);
                    min-width: 0;
                }
            }

            /* Tablet landscape: 3 produk per slide */
            @media (min-width: 900px) and (max-width: 1199px) {
                .all-products-slide-inner .product-card {
                    width: calc(33.333% - 20px);
                    max-width: calc(33.333% - 20px);
                    min-width: 0;
                }

                .product-image {
                    max-width: 180px;
                    height: 160px;
                }

                .product-name {
                    font-size: 15px;
                    height: 36px;
                }
            }

            /* Tablet portrait: 2 produk per slide */
            @media (min-width: 576px) and (max-width: 899px) {
                .all-products-slide-inner .product-card {
                    width: calc(50% - 20px);
                    max-width: calc(50% - 20px);
                    min-width: 0;
                }

                .product-image {
                    max-width: 160px;
                    height: 140px;
                }

                .product-name {
                    font-size: 15px;
                    height: 36px;
                }

                .all-products-slider-container {
                    padding: 25px 15px 50px;
                }

                .all-products-slider-nav {
                    width: calc(100% - 30px);
                    left: 15px;
                }
            }

            /* Mobile kecil: 1 produk per slide */
            @media (max-width: 575px) {
                .all-products-slide-inner .product-card {
                    width: calc(100% - 20px);
                    max-width: 300px;
                    min-width: 0;
                }

                .product-image {
                    max-width: 140px;
                    height: 120px;
                }

                .product-name {
                    font-size: 15px;
                    height: 36px;
                }

                .product-price {
                    font-size: 15px;
                }

                .all-products-slider-container {
                    padding: 20px 10px 50px;
                }

                .all-products-slider-nav {
                    width: calc(100% - 20px);
                    left: 10px;
                }

                .all-products-nav-btn {
                    width: 40px;
                    height: 40px;
                    font-size: 18px;
                }
            }

            /* Mobile sangat kecil */
            @media (max-width: 300px) {
                .all-products-slide-inner .product-card {
                    width: 100%;
                    max-width: 100%;
                    padding: 15px;
                }

                .product-image {
                    max-width: 120px;
                    height: 100px;
                    padding: 10px;
                }

                .product-name {
                    font-size: 14px;
                    height: 32px;
                }

                .product-price {
                    font-size: 14px;
                }

                .product-btn {
                    padding: 8px 16px;
                    font-size: 13px;
                }

                .all-products-slider-container {
                    padding: 15px 5px 40px;
                }

                .all-products-slider-nav {
                    width: calc(100% - 10px);
                    left: 5px;
                }

                .all-products-nav-btn {
                    width: 35px;
                    height: 35px;
                    font-size: 16px;
                }

                .all-products-slider-dots {
                    bottom: 20px;
                }

                .all-products-dot {
                    width: 10px;
                    height: 10px;
                }
            }

            /* Header */
            .product-header {
                text-align: center;
                margin-bottom: 10px;
            }

            .product-header h1 {
                font-size: 36px;
                font-weight: 700;
                color: #333;
                margin-bottom: 15px;
            }

            .product-header p {
                font-size: 16px;
                color: #666;
                max-width: 600px;
                margin: 0 auto;
                line-height: 1.6;
            }

            /* Container untuk produk terbaru */
            #latest-products-container {
                margin-top: 40px;
            }

            h3 {
                font-size: 24px;
            }

            @media (max-width: 768px) {
                h3 {
                    font-size: 22px;
                }

                .product-header h1 {
                    font-size: 28px;
                }
            }

            @media (max-width: 480px) {
                h3 {
                    font-size: 20px;
                }

                .product-header h1 {
                    font-size: 24px;
                }
            }
        </style>
        <div class="all-products-wrapper">
            <div class="product-header">
                <h1>Berbagai produk Apple</h1>
                <p>Temukan produk Apple terbaru dengan harga terbaik di iBox Indonesia</p>
            </div>

            <h3>Produk Apple Terpopuler</h3>

            <!-- Category Tabs Slider untuk Produk Populer -->
            <div class="category-tabs-wrapper">
                <div class="category-tabs-container" id="categoryTabsSlider">
                    <button class="category-tab active" data-category="all">Semua Produk</button>
                    <button class="category-tab" data-category="mac">Mac</button>
                    <button class="category-tab" data-category="iphone">iPhone</button>
                    <button class="category-tab" data-category="ipad">iPad</button>
                    <button class="category-tab" data-category="watch">Apple Watch</button>
                    <button class="category-tab" data-category="accessories">Aksesori</button>
                </div>

                <!-- SWIPE HINT UNTUK MOBILE -->
                <div class="tabs-swipe-hint" id="tabsSwipeHint">
                    <i class="bi bi-arrow-left-right"></i> Geser untuk melihat lebih banyak
                </div>
            </div>

            <!-- Container untuk slider produk populer -->
            <div id="popular-products-container">
                <!-- Slider untuk produk populer akan dimuat di sini -->
            </div>

            <h3>Produk Terbaru</h3>

            <!-- Container untuk slider produk terbaru -->
            <div id="latest-products-container">
                <!-- Slider untuk produk terbaru akan dimuat di sini -->
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const popularProductsContainer = document.getElementById('popular-products-container');
                const latestProductsContainer = document.getElementById('latest-products-container');
                const categoryTabs = document.querySelectorAll('#categoryTabsSlider .category-tab');
                const categoryTabsSlider = document.getElementById('categoryTabsSlider');
                const tabsSwipeHint = document.getElementById('tabsSwipeHint');

                // ===== LOAD CATEGORY PRODUCTS DARI FILE TERPISAH =====
                function loadCategoryProducts() {
                    fetch('4-category-products.php')
                        .then(response => response.text())
                        .then(html => {
                            categoryProductsSection.innerHTML = html;

                            // Setelah konten dimuat, inisialisasi event listener
                            setTimeout(() => {
                                // Mendengarkan event custom dari kategori produk
                                document.addEventListener('categoryProductSelected', function(e) {
                                    const category = e.detail.category;
                                    console.log(`Kategori dipilih dari komponen terpisah: ${category}`);

                                    // Filter produk populer berdasarkan kategori
                                    filterPopularProducts(category);

                                    // Update tab kategori yang aktif
                                    updateCategoryTab(category);
                                });
                            }, 100);
                        })
                        .catch(error => {
                            console.error('Error loading category products:', error);
                            categoryProductsSection.innerHTML = '<p>Gagal memuat kategori produk.</p>';
                        });
                }

                // Data produk populer
                const popularProducts = [{
                        id: 1,
                        name: 'MacBook Pro 14"',
                        category: 'mac',
                        price: 'Rp 24.999.000',
                        image: 'https://store.storeimages.cdn-apple.com/8756/as-images.apple.com/is/mbp14-spacegray-select-202310?wid=904&hei=840&fmt=jpeg&qlt=90&.v=1697230830030',
                        badge: {
                            text: 'Terlaris',
                            type: 'hot'
                        },
                        rating: 4.7
                    },
                    // ... (data produk populer lainnya, sama seperti sebelumnya)
                ];

                // Data produk terbaru
                const latestProducts = [{
                        id: 9,
                        name: 'MacBook Air 13" M3',
                        category: 'mac',
                        price: 'Rp 15.999.000',
                        image: 'https://store.storeimages.cdn-apple.com/8756/as-images.apple.com/is/macbook-air-spacegray-select-20220606?wid=904&hei=840&fmt=jpeg&qlt=90&.v=1653084303665',
                        badge: {
                            text: 'Terbaru',
                            type: 'new'
                        },
                        rating: 4.8
                    },
                    // ... (data produk terbaru lainnya, sama seperti sebelumnya)
                ];

                // ===== FUNGSI UNTUK TABS KATEGORI SLIDER =====
                let isTabsDragging = false;
                let tabsStartX = 0;
                let tabsCurrentTranslate = 0;
                let tabsPrevTranslate = 0;

                // Inisialisasi Category Tabs Slider
                function initTabsSlider() {
                    // Hitung total lebar konten
                    const containerWidth = categoryTabsSlider.parentElement.clientWidth;
                    const tabs = categoryTabsSlider.children;
                    let totalWidth = 0;

                    // Hitung total lebar semua tab termasuk gap
                    const style = window.getComputedStyle(categoryTabsSlider);
                    const gap = parseFloat(style.gap) || 12;

                    for (let i = 0; i < tabs.length; i++) {
                        totalWidth += tabs[i].offsetWidth;
                        if (i < tabs.length - 1) {
                            totalWidth += gap;
                        }
                    }

                    // Reset posisi slider jika konten lebih kecil dari container
                    if (totalWidth <= containerWidth) {
                        categoryTabsSlider.style.transform = 'translateX(0)';
                        tabsCurrentTranslate = 0;
                        tabsPrevTranslate = 0;
                    }

                    setupTabsDragEvents();
                }

                function setupTabsDragEvents() {
                    categoryTabsSlider.addEventListener('touchstart', tabsTouchStart);
                    categoryTabsSlider.addEventListener('touchmove', tabsTouchMove);
                    categoryTabsSlider.addEventListener('touchend', tabsTouchEnd);

                    categoryTabsSlider.addEventListener('mousedown', tabsMouseDown);
                    categoryTabsSlider.addEventListener('mousemove', tabsMouseMove);
                    categoryTabsSlider.addEventListener('mouseup', tabsMouseUp);
                    categoryTabsSlider.addEventListener('mouseleave', tabsMouseLeave);

                    categoryTabsSlider.style.cursor = 'grab';
                }

                // Event handlers untuk Category Tabs Slider
                function tabsTouchStart(e) {
                    isTabsDragging = true;
                    tabsStartX = e.touches[0].clientX;
                    tabsPrevTranslate = tabsCurrentTranslate;
                    categoryTabsSlider.classList.add('grabbing');
                }

                function tabsTouchMove(e) {
                    if (!isTabsDragging) return;

                    const currentX = e.touches[0].clientX;
                    const diffX = currentX - tabsStartX;
                    const newTranslate = tabsPrevTranslate + diffX;

                    setTabsSliderPosition(newTranslate);
                }

                function tabsTouchEnd() {
                    isTabsDragging = false;
                    categoryTabsSlider.classList.remove('grabbing');
                    snapTabsSlider();
                }

                function tabsMouseDown(e) {
                    isTabsDragging = true;
                    tabsStartX = e.clientX;
                    tabsPrevTranslate = tabsCurrentTranslate;
                    categoryTabsSlider.classList.add('grabbing');
                    e.preventDefault();
                }

                function tabsMouseMove(e) {
                    if (!isTabsDragging) return;

                    const currentX = e.clientX;
                    const diffX = currentX - tabsStartX;
                    const newTranslate = tabsPrevTranslate + diffX;

                    setTabsSliderPosition(newTranslate);
                }

                function tabsMouseUp() {
                    isTabsDragging = false;
                    categoryTabsSlider.classList.remove('grabbing');
                    snapTabsSlider();
                }

                function tabsMouseLeave() {
                    if (isTabsDragging) {
                        isTabsDragging = false;
                        categoryTabsSlider.classList.remove('grabbing');
                        snapTabsSlider();
                    }
                }

                // Fungsi untuk mengatur posisi tabs slider dengan batasan
                function setTabsSliderPosition(position) {
                    const container = categoryTabsSlider.parentElement;
                    const containerWidth = container.clientWidth;
                    const tabs = categoryTabsSlider.children;

                    // Hitung total lebar semua tab termasuk gap
                    const style = window.getComputedStyle(categoryTabsSlider);
                    const gap = parseFloat(style.gap) || 12;
                    let totalWidth = 0;

                    for (let i = 0; i < tabs.length; i++) {
                        totalWidth += tabs[i].offsetWidth;
                        if (i < tabs.length - 1) {
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

                    categoryTabsSlider.style.transform = `translateX(${position}px)`;
                    tabsCurrentTranslate = position;
                }

                // Fungsi untuk snap tabs slider ke posisi yang tepat
                function snapTabsSlider() {
                    const container = categoryTabsSlider.parentElement;
                    const containerWidth = container.clientWidth;
                    const tabs = categoryTabsSlider.children;

                    // Hitung total lebar semua tab termasuk gap
                    const style = window.getComputedStyle(categoryTabsSlider);
                    const gap = parseFloat(style.gap) || 12;
                    let totalWidth = 0;

                    for (let i = 0; i < tabs.length; i++) {
                        totalWidth += tabs[i].offsetWidth;
                        if (i < tabs.length - 1) {
                            totalWidth += gap;
                        }
                    }

                    // Jika totalWidth <= containerWidth, snap ke 0
                    if (totalWidth <= containerWidth) {
                        animateTabsSlider(0);
                        return;
                    }

                    // Snap ke posisi terdekat yang valid
                    const minTranslate = Math.min(0, containerWidth - totalWidth - 20);
                    const maxTranslate = 0;

                    let snapPosition = tabsCurrentTranslate;

                    // Jika posisi saat ini dekat dengan batas kiri (0), snap ke 0
                    if (snapPosition > -50) {
                        snapPosition = 0;
                    }
                    // Jika posisi saat ini dekat dengan batas kanan, snap ke batas kanan
                    else if (snapPosition < minTranslate + 50) {
                        snapPosition = minTranslate;
                    }

                    animateTabsSlider(snapPosition);
                }

                // Fungsi animasi untuk tabs slider
                function animateTabsSlider(targetPosition) {
                    const startPosition = tabsCurrentTranslate;
                    const duration = 300;
                    const startTime = performance.now();

                    function animate(currentTime) {
                        const elapsed = currentTime - startTime;
                        const progress = Math.min(elapsed / duration, 1);

                        // Easing function (easeOutCubic)
                        const ease = 1 - Math.pow(1 - progress, 3);

                        const currentPosition = startPosition + (targetPosition - startPosition) * ease;

                        categoryTabsSlider.style.transform = `translateX(${currentPosition}px)`;
                        tabsCurrentTranslate = currentPosition;

                        if (progress < 1) {
                            requestAnimationFrame(animate);
                        }
                    }

                    requestAnimationFrame(animate);
                }

                // ===== FUNGSI UNTUK PRODUK SLIDER =====
                function renderProductsSlider(products, containerId, title = '') {
                    // Hitung jumlah produk per slide berdasarkan lebar layar
                    const productsPerSlide = getProductsPerSlide();
                    const slideCount = Math.ceil(products.length / productsPerSlide);
                    const isSingleSlide = slideCount <= 1;

                    let html = `
                    <div class="all-products-slider-container ${isSingleSlide ? 'single-slide' : ''}">
                        <div class="all-products-slider-nav ${isSingleSlide ? 'hidden' : ''}" id="${containerId}-nav">
                            <button class="all-products-nav-btn all-products-prev-btn ${isSingleSlide ? 'hidden' : ''}" id="${containerId}-prev-btn">
                                <i class="bi bi-chevron-left"></i>
                            </button>
                            <button class="all-products-nav-btn all-products-next-btn ${isSingleSlide ? 'hidden' : ''}" id="${containerId}-next-btn">
                                <i class="bi bi-chevron-right"></i>
                            </button>
                        </div>
                        <div class="all-products-slider" id="${containerId}-slider">
                `;

                    // Buat slide sesuai jumlah yang dibutuhkan
                    for (let slideIndex = 0; slideIndex < slideCount; slideIndex++) {
                        html += `<div class="all-products-slide">`;
                        html += `<div class="all-products-slide-inner" id="${containerId}-slide-inner-${slideIndex}">`;

                        // Tambahkan produk ke slide ini
                        const startIndex = slideIndex * productsPerSlide;
                        const endIndex = Math.min(startIndex + productsPerSlide, products.length);

                        for (let i = startIndex; i < endIndex; i++) {
                            html += createProductCard(products[i]);
                        }

                        html += `</div></div>`;
                    }

                    html += `
                        </div>
                        ${!isSingleSlide ? `<div class="all-products-slider-dots" id="${containerId}-dots"></div>` : ''}
                    </div>
                `;

                    const container = document.getElementById(containerId);
                    if (container) {
                        container.innerHTML = html;
                        initAllProductsSlider(productsPerSlide, slideCount, containerId);
                    }
                }

                // Fungsi untuk menentukan jumlah produk per slide berdasarkan lebar layar
                function getProductsPerSlide() {
                    const screenWidth = window.innerWidth;

                    if (screenWidth >= 1200) return 4; // 4 produk per slide
                    if (screenWidth >= 900) return 3; // 3 produk per slide
                    if (screenWidth >= 576) return 2; // 2 produk per slide
                    return 1; // 1 produk per slide (untuk layar kecil)
                }

                function createProductCard(product) {
                    const badgeClass = product.badge ? `product-badge ${product.badge.type}` : '';
                    const badgeHTML = product.badge ?
                        `<div class="${badgeClass}">${product.badge.text}</div>` : '';

                    const stars = getProductStarRating(product.rating);

                    return `
                    <div class="product-card" data-category="${product.category}">
                        <img src="${product.image}" alt="${product.name}" class="product-image">
                        ${badgeHTML}
                        <div class="product-name">${product.name}</div>
                        <div class="product-rating">
                            ${stars}
                            <span>(${product.rating})</span>
                        </div>
                        <div class="product-price">${product.price}</div>
                        <button class="product-btn" data-product-id="${product.id}">
                            <i class="bi bi-bag"></i> Beli Sekarang
                        </button>
                    </div>
                `;
                }

                function getProductStarRating(rating) {
                    let stars = '';
                    const fullStars = Math.floor(rating);
                    const hasHalfStar = rating % 1 >= 0.5;

                    for (let i = 0; i < fullStars; i++) {
                        stars += '<i class="bi bi-star-fill"></i>';
                    }

                    if (hasHalfStar) {
                        stars += '<i class="bi bi-star-half"></i>';
                    }

                    const emptyStars = 5 - fullStars - (hasHalfStar ? 1 : 0);
                    for (let i = 0; i < emptyStars; i++) {
                        stars += '<i class="bi bi-star"></i>';
                    }

                    return stars;
                }

                function initAllProductsSlider(productsPerSlide, slideCount, containerId) {
                    const slider = document.getElementById(`${containerId}-slider`);
                    const prevBtn = document.getElementById(`${containerId}-prev-btn`);
                    const nextBtn = document.getElementById(`${containerId}-next-btn`);
                    const dotsContainer = document.getElementById(`${containerId}-dots`);
                    const sliderContainer = document.querySelector(`#${containerId} .all-products-slider-container`);
                    const navContainer = document.getElementById(`${containerId}-nav`);

                    // Jika hanya ada 1 slide, jangan inisialisasi navigasi
                    if (slideCount <= 1) {
                        if (sliderContainer) {
                            sliderContainer.classList.add('single-slide');
                        }
                        if (navContainer) {
                            navContainer.classList.add('hidden');
                        }
                        if (dotsContainer) {
                            dotsContainer.classList.add('hidden');
                        }
                        return;
                    } else {
                        if (sliderContainer) {
                            sliderContainer.classList.remove('single-slide');
                        }
                        if (navContainer) {
                            navContainer.classList.remove('hidden');
                        }
                    }

                    let currentSlide = 0;

                    // Buat dots navigation hanya jika ada lebih dari 1 slide
                    if (slideCount > 1 && dotsContainer) {
                        dotsContainer.classList.remove('hidden');
                        dotsContainer.innerHTML = '';
                        for (let i = 0; i < slideCount; i++) {
                            const dot = document.createElement('div');
                            dot.classList.add('all-products-dot');
                            if (i === 0) dot.classList.add('active');
                            dot.addEventListener('click', () => goToSlide(i));
                            dotsContainer.appendChild(dot);
                        }
                    } else if (dotsContainer) {
                        dotsContainer.classList.add('hidden');
                    }

                    function updateNavButtons() {
                        if (prevBtn) {
                            prevBtn.disabled = currentSlide === 0;
                        }
                        if (nextBtn) {
                            nextBtn.disabled = currentSlide === slideCount - 1;
                        }
                    }

                    function goToSlide(slideIndex) {
                        if (slideIndex < 0 || slideIndex >= slideCount) return;

                        currentSlide = slideIndex;
                        slider.style.transform = `translateX(-${currentSlide * 100}%)`;
                        updateDots();
                        updateNavButtons();
                    }

                    function updateDots() {
                        if (slideCount <= 1 || !dotsContainer) return;

                        const dots = document.querySelectorAll(`#${containerId}-dots .all-products-dot`);
                        dots.forEach((dot, index) => {
                            dot.classList.toggle('active', index === currentSlide);
                        });
                    }

                    function nextSlide() {
                        if (currentSlide < slideCount - 1) {
                            goToSlide(currentSlide + 1);
                        }
                    }

                    function prevSlide() {
                        if (currentSlide > 0) {
                            goToSlide(currentSlide - 1);
                        }
                    }

                    if (prevBtn) prevBtn.addEventListener('click', prevSlide);
                    if (nextBtn) nextBtn.addEventListener('click', nextSlide);

                    // Keyboard navigation
                    document.addEventListener('keydown', (e) => {
                        if (slideCount <= 1) return;

                        if (e.ctrlKey && e.key === 'ArrowLeft') {
                            prevSlide();
                        } else if (e.ctrlKey && e.key === 'ArrowRight') {
                            nextSlide();
                        }
                    });

                    // Swipe untuk mobile
                    let touchStartX = 0;
                    let touchEndX = 0;

                    if (slider && slideCount > 1) {
                        slider.addEventListener('touchstart', (e) => {
                            touchStartX = e.changedTouches[0].screenX;
                        });

                        slider.addEventListener('touchend', (e) => {
                            touchEndX = e.changedTouches[0].screenX;
                            handleSwipe();
                        });
                    }

                    function handleSwipe() {
                        const swipeThreshold = 50;
                        const diff = touchStartX - touchEndX;

                        if (Math.abs(diff) > swipeThreshold) {
                            if (diff > 0) {
                                nextSlide();
                            } else {
                                prevSlide();
                            }
                        }
                    }

                    updateNavButtons();
                }

                // ===== FILTER PRODUK POPULER =====
                function filterPopularProducts(category) {
                    let filteredProducts;

                    if (category === 'all') {
                        filteredProducts = popularProducts;
                    } else {
                        filteredProducts = popularProducts.filter(product => product.category === category);
                    }

                    renderProductsSlider(filteredProducts, 'popular-products-container', 'Produk Populer');
                }

                // ===== UPDATE TAB KATEGORI =====
                function updateCategoryTab(category) {
                    categoryTabs.forEach(tab => {
                        if (tab.dataset.category === category) {
                            tab.classList.add('active');
                        } else {
                            tab.classList.remove('active');
                        }
                    });
                }

                // ===== FUNGSI UNTUK RESPONSIF =====
                function handleResize() {
                    initTabsSlider();

                    // Render ulang slider produk saat resize untuk update layout
                    const activeTab = document.querySelector('#categoryTabsSlider .category-tab.active');
                    if (activeTab) {
                        filterPopularProducts(activeTab.dataset.category);
                    }

                    // Render ulang slider produk terbaru
                    renderProductsSlider(latestProducts, 'latest-products-container', 'Produk Terbaru');
                }

                // ===== EVENT LISTENERS =====
                window.addEventListener('resize', debounce(handleResize, 250));

                categoryTabsSlider.addEventListener('touchstart', function() {
                    tabsSwipeHint.style.opacity = '0.5';
                    setTimeout(() => {
                        tabsSwipeHint.style.display = 'none';
                    }, 300);
                });

                categoryTabsSlider.addEventListener('mousedown', function() {
                    tabsSwipeHint.style.opacity = '0.5';
                    setTimeout(() => {
                        tabsSwipeHint.style.display = 'none';
                    }, 300);
                });

                categoryTabs.forEach(tab => {
                    tab.addEventListener('click', () => {
                        const category = tab.dataset.category;
                        filterPopularProducts(category);
                    });
                });

                // Add click event to all buy buttons
                document.addEventListener('click', function(e) {
                    if (e.target.classList.contains('product-btn') || e.target.closest('.product-btn')) {
                        const productCard = e.target.closest('.product-card');
                        const productName = productCard.querySelector('.product-name').textContent;
                        alert(`Terima kasih! Anda akan membeli: ${productName}`);
                    }
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
                loadCategoryProducts(); // Load kategori produk dari file terpisah
                initTabsSlider();
                filterPopularProducts('all'); // Render produk populer awal
                renderProductsSlider(latestProducts, 'latest-products-container', 'Produk Terbaru'); // Render produk terbaru
            });
        </script>
    </div>


</body>

</html>