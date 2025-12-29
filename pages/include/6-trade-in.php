<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trade-in Flex Slider</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        * {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: #f7f7f7;
        }

        .tradein-container {
            padding: 40px 20px;
            max-width: 1400px;
            margin: 0 auto;
        }

        /* HEADING STYLES */
        .tradein-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .tradein-header h1 {
            font-size: 28px;
            font-weight: 600;
            color: #333;
            margin-bottom: 15px;
            text-align: center;
            position: relative;
            padding-bottom: 15px;
        }

        .tradein-header h1::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background-color: #007aff;
        }

        /* SLIDER WRAPPER */
        .tradein-slider-wrapper {
            position: relative;
            overflow: hidden;
            border-radius: 12px;
            background: white;
            padding: 30px 20px 60px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 40px;
        }

        .tradein-slider-wrapper.single-slide {
            padding-bottom: 30px;
        }

        /* SLIDER */
        .tradein-slider {
            display: flex;
            transition: transform 0.5s ease-in-out;
            gap: 0;
        }

        /* SLIDE */
        .tradein-slide {
            min-width: 100%;
            padding: 10px;
            box-sizing: border-box;
            display: flex;
            justify-content: center;
            flex-shrink: 0;
        }

        /* SLIDE INNER - Flex layout untuk produk */
        .tradein-slide-inner {
            display: flex;
            gap: 20px;
            width: 100%;
            justify-content: center;
            flex-wrap: wrap;
        }

        /* PRODUCT CARD */
        .tradein-product {
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

        .tradein-product:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 30px rgba(0, 122, 255, 0.1);
            border-color: #007aff;
        }

        .tradein-image {
            width: 100%;
            max-width: 200px;
            height: 180px;
            object-fit: contain;
            margin-bottom: 15px;
            border-radius: 8px;
            background-color: #f8f9fa;
            padding: 15px;
        }

        /* NAMA PRODUK */
        .tradein-name {
            font-size: 16px;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            line-height: 1.3;
        }

        /* HARGA - Layout sejajar */
        .tradein-prices {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 25px;
            width: 100%;
            text-align: left;
        }

        .tradein-price-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 5px 0;
        }

        .tradein-price-label {
            font-size: 14px;
            color: #666;
            font-weight: 500;
        }

        .tradein-price-value {
            font-size: 15px;
            font-weight: 600;
        }

        .tradein-old-price {
            color: #999;
            text-decoration: line-through;
        }

        .tradein-new-price {
            color: #007aff;
        }

        .tradein-discount {
            color: #34c759;
        }

        /* BUTTON */
        .tradein-btn {
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
            justify-content: center;
            gap: 8px;
            font-size: 14px;
            margin-top: auto;
        }

        .tradein-btn:hover {
            background-color: #0056cc;
            transform: scale(1.05);
        }

        /* NAVIGATION BUTTONS */
        .tradein-controls {
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

        .tradein-controls.hidden {
            display: none;
        }

        .tradein-nav-btn {
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

        .tradein-nav-btn.hidden {
            display: none;
        }

        .tradein-nav-btn:hover:not(:disabled) {
            background-color: #007aff;
            color: white;
            transform: scale(1.1);
            box-shadow: 0 5px 15px rgba(0, 122, 255, 0.3);
        }

        .tradein-nav-btn:disabled {
            opacity: 0.3;
            cursor: not-allowed;
            background-color: #f0f0f0;
        }

        .tradein-nav-btn:disabled:hover {
            transform: none;
            background-color: #f0f0f0;
            color: #333;
        }

        /* DOTS NAVIGATION */
        .tradein-dots {
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

        .tradein-dots.hidden {
            display: none;
        }

        .tradein-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background-color: rgba(0, 122, 255, 0.2);
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .tradein-dot.active {
            background-color: #007aff !important;
            transform: scale(1.2);
            border-color: white !important;
        }

        .tradein-dot:hover {
            background-color: rgba(0, 122, 255, 0.4);
        }

        /* RESPONSIVE STYLES - MODIFIKASI UTAMA */
        /* Desktop besar: 4 produk per slide */
        @media (min-width: 1200px) {
            .tradein-slide-inner .tradein-product {
                width: calc(25% - 20px);
                max-width: calc(25% - 20px);
                min-width: 0;
            }
        }

        /* Tablet landscape: 3 produk per slide */
        @media (min-width: 900px) and (max-width: 1199px) {
            .tradein-slide-inner .tradein-product {
                width: calc(33.333% - 20px);
                max-width: calc(33.333% - 20px);
                min-width: 0;
            }
            
            .tradein-image {
                max-width: 180px;
                height: 160px;
            }
            
            .tradein-name {
                font-size: 15px;
                height: 36px;
            }
        }

        /* Tablet portrait: 2 produk per slide */
        @media (min-width: 576px) and (max-width: 899px) {
            .tradein-slide-inner .tradein-product {
                width: calc(50% - 20px);
                max-width: calc(50% - 20px);
                min-width: 0;
            }
            
            .tradein-image {
                max-width: 160px;
                height: 140px;
            }
            
            .tradein-name {
                font-size: 15px;
                height: 36px;
            }
            
            .tradein-slider-wrapper {
                padding: 25px 15px 50px;
            }
            
            .tradein-controls {
                width: calc(100% - 30px);
                left: 15px;
            }
        }

        /* Mobile kecil: 1 produk per slide */
        @media (max-width: 575px) {
            .tradein-slide-inner .tradein-product {
                width: calc(100% - 20px);
                max-width: 300px;
                min-width: 0;
            }
            
            .tradein-image {
                max-width: 140px;
                height: 120px;
            }
            
            .tradein-name {
                font-size: 15px;
                height: 36px;
            }
            
            .tradein-price-label {
                font-size: 14px;
            }
            
            .tradein-price-value {
                font-size: 14px;
            }
            
            .tradein-slider-wrapper {
                padding: 20px 10px 50px;
            }
            
            .tradein-controls {
                width: calc(100% - 20px);
                left: 10px;
            }
            
            .tradein-nav-btn {
                width: 40px;
                height: 40px;
                font-size: 18px;
            }
        }

        /* Mobile sangat kecil */
        @media (max-width: 300px) {
            .tradein-slide-inner .tradein-product {
                width: 100%;
                max-width: 100%;
                padding: 15px;
            }
            
            .tradein-image {
                max-width: 120px;
                height: 100px;
                padding: 10px;
            }
            
            .tradein-name {
                font-size: 14px;
                height: 32px;
            }
            
            .tradein-price-label {
                font-size: 12px;
            }
            
            .tradein-price-value {
                font-size: 13px;
            }
            
            .tradein-btn {
                padding: 8px 16px;
                font-size: 13px;
            }
            
            .tradein-slider-wrapper {
                padding: 15px 5px 40px;
            }
            
            .tradein-controls {
                width: calc(100% - 10px);
                left: 5px;
            }
            
            .tradein-nav-btn {
                width: 35px;
                height: 35px;
                font-size: 16px;
            }

            .tradein-dots {
                bottom: 20px;
            }
            
            .tradein-dot {
                width: 10px;
                height: 10px;
            }
        }

        /* Responsif heading */
        @media (max-width: 768px) {
            .tradein-header h1 {
                font-size: 24px;
            }
        }
        
        @media (max-width: 480px) {
            .tradein-header h1 {
                font-size: 22px;
            }
            
            .tradein-container {
                padding: 30px 15px;
            }
        }
    </style>
</head>
<body>
    <div class="tradein-container">
        <!-- HEADING -->
        <div class="tradein-header">
            <h1>Trade-in produkmu dengan produk terbaru.</h1>
        </div>
        
        <!-- SLIDER -->
        <div class="tradein-slider-wrapper" id="tradeinSliderWrapper">
            <div class="tradein-controls" id="tradeinControls">
                <button class="tradein-nav-btn tradein-prev" id="tradeinPrev">
                    <i class="bi bi-chevron-left"></i>
                </button>
                <button class="tradein-nav-btn tradein-next" id="tradeinNext">
                    <i class="bi bi-chevron-right"></i>
                </button>
            </div>
            
            <div class="tradein-slider" id="tradeinSlider">
                <!-- Slides akan di-generate oleh JavaScript -->
            </div>
            
            <div class="tradein-dots" id="tradeinDots"></div>
        </div>
    </div>

    <script>
        // Data produk dengan harga trade-in
        const tradeinProducts = [
            {
                id: 1,
                name: '14-inch MacBook Pro M4 Pro',
                oldPrice: 'Rp 34.999.000',
                newPrice: 'Rp 33.999.000',
                discount: 'Rp 1.000.000',
                image: 'https://store.storeimages.cdn-apple.com/8756/as-images.apple.com/is/mbp14-spacegray-select-202310?wid=904&hei=840&fmt=jpeg&qlt=90&.v=1697230830030'
            },
            {
                id: 2,
                name: 'iPhone 16 Pro',
                oldPrice: 'Rp 21.999.000',
                newPrice: 'Rp 17.499.000',
                discount: 'Rp 4.500.000',
                image: 'https://store.storeimages.cdn-apple.com/8756/as-images.apple.com/is/iphone-15-pro-finish-select-202309-6-7inch?wid=5120&hei=2880&fmt=webp&qlt=70&.v=1693009279096'
            },
            {
                id: 3,
                name: '13-inch iPad Air M3',
                oldPrice: 'Rp 15.499.000',
                newPrice: 'Rp 13.699.000',
                discount: 'Rp 1.800.000',
                image: 'https://store.storeimages.cdn-apple.com/8756/as-images.apple.com/is/ipad-pro-11-select-wifi-spacegray-202210_FMT_WHH?wid=940&hei=1112&fmt=png-alpha&.v=1664411200794'
            },
            {
                id: 4,
                name: 'Apple Watch Series 10',
                oldPrice: 'Rp 7.299.000',
                newPrice: 'Rp 6.239.000',
                discount: 'Rp 1.060.000',
                image: 'https://store.storeimages.cdn-apple.com/8756/as-images.apple.com/is/s9-watch-ultra2-titanium-202309?wid=940&hei=1112&fmt=png-alpha&.v=1693501423925'
            },
            {
                id: 5,
                name: 'AirPods Pro 2',
                oldPrice: 'Rp 4.499.000',
                newPrice: 'Rp 3.799.000',
                discount: 'Rp 700.000',
                image: 'https://store.storeimages.cdn-apple.com/8756/as-images.apple.com/is/airpods-pro-2-hero-select-202409?wid=940&hei=1112&fmt=jpeg&qlt=90&.v=1723507647577'
            },
            {
                id: 6,
                name: 'MacBook Air 15" M3',
                oldPrice: 'Rp 23.999.000',
                newPrice: 'Rp 21.999.000',
                discount: 'Rp 2.000.000',
                image: 'https://store.storeimages.cdn-apple.com/8756/as-images.apple.com/is/macbook-air-15-midnight-select-202306?wid=904&hei=840&fmt=jpeg&qlt=90&.v=1683844876259'
            },
            {
                id: 7,
                name: 'iMac 24" M3',
                oldPrice: 'Rp 19.999.000',
                newPrice: 'Rp 17.999.000',
                discount: 'Rp 2.000.000',
                image: 'https://store.storeimages.cdn-apple.com/8756/as-images.apple.com/is/imac-24-blue-selection-hero-202104?wid=904&hei=840&fmt=jpeg&qlt=90&.v=1617492405000'
            },
            {
                id: 8,
                name: 'iPhone 15',
                oldPrice: 'Rp 14.999.000',
                newPrice: 'Rp 12.999.000',
                discount: 'Rp 2.000.000',
                image: 'https://store.storeimages.cdn-apple.com/8756/as-images.apple.com/is/iphone-15-finish-select-202309-6-1inch?wid=5120&hei=2880&fmt=webp&qlt=70&.v=1692923777977'
            },
            {
                id: 9,
                name: 'iPad Pro 12.9" M2',
                oldPrice: 'Rp 28.999.000',
                newPrice: 'Rp 25.999.000',
                discount: 'Rp 3.000.000',
                image: 'https://store.storeimages.cdn-apple.com/8756/as-images.apple.com/is/ipad-pro-12-11-select-wifi-spacegray-202210_FMT_WHH?wid=940&hei=1112&fmt=png-alpha&.v=1664411200794'
            },
            {
                id: 10,
                name: 'Mac Studio M2 Ultra',
                oldPrice: 'Rp 49.999.000',
                newPrice: 'Rp 44.999.000',
                discount: 'Rp 5.000.000',
                image: 'https://store.storeimages.cdn-apple.com/8756/as-images.apple.com/is/mac-studio-select-202206?wid=904&hei=840&fmt=jpeg&qlt=90&.v=1653053033061'
            },
            {
                id: 11,
                name: 'Apple Watch Ultra 2',
                oldPrice: 'Rp 14.999.000',
                newPrice: 'Rp 12.499.000',
                discount: 'Rp 2.500.000',
                image: 'https://store.storeimages.cdn-apple.com/8756/as-images.apple.com/is/watch-ultra2-202309_GEO_ID?wid=940&hei=1112&fmt=png-alpha&.v=1693501423925'
            },
            {
                id: 12,
                name: 'Mac Mini M2 Pro',
                oldPrice: 'Rp 18.999.000',
                newPrice: 'Rp 16.999.000',
                discount: 'Rp 2.000.000',
                image: 'https://store.storeimages.cdn-apple.com/8756/as-images.apple.com/is/mac-mini-select-202301?wid=904&hei=840&fmt=jpeg&qlt=90&.v=1671588045725'
            }
        ];

        // ===== FUNGSI UTAMA =====
        function renderTradeinSlider() {
            const sliderWrapper = document.getElementById('tradeinSliderWrapper');
            
            // Periksa jika hanya ada 1 slide
            const productsPerSlide = getProductsPerSlide();
            const slideCount = Math.ceil(tradeinProducts.length / productsPerSlide);
            const isSingleSlide = slideCount <= 1;

            let html = `
                <div class="tradein-controls ${isSingleSlide ? 'hidden' : ''}" id="tradeinControls">
                    <button class="tradein-nav-btn tradein-prev ${isSingleSlide ? 'hidden' : ''}" id="tradeinPrev">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                    <button class="tradein-nav-btn tradein-next ${isSingleSlide ? 'hidden' : ''}" id="tradeinNext">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </div>
                <div class="tradein-slider" id="tradeinSlider">
            `;

            for (let i = 0; i < slideCount; i++) {
                html += `<div class="tradein-slide">`;
                html += `<div class="tradein-slide-inner" id="tradein-slide-inner-${i}">`;

                for (let j = 0; j < productsPerSlide; j++) {
                    const index = i * productsPerSlide + j;
                    if (index < tradeinProducts.length) {
                        html += createTradeinCard(tradeinProducts[index]);
                    }
                }

                html += `</div></div>`;
            }

            html += `
                </div>
                ${!isSingleSlide ? `<div class="tradein-dots" id="tradeinDots"></div>` : ''}
            `;

            sliderWrapper.innerHTML = html;
            initTradeinSlider(productsPerSlide, slideCount);
        }

        // Fungsi untuk menentukan jumlah produk per slide berdasarkan lebar layar
        function getProductsPerSlide() {
            const screenWidth = window.innerWidth;
            
            if (screenWidth >= 1200) return 4;    // 4 produk per slide
            if (screenWidth >= 900) return 3;     // 3 produk per slide
            if (screenWidth >= 576) return 2;     // 2 produk per slide
            return 1;                             // 1 produk per slide
        }

        function createTradeinCard(product) {
            return `
                <div class="tradein-product">
                    <img src="${product.image}" alt="${product.name}" class="tradein-image">
                    <div class="tradein-name">${product.name}</div>
                    
                    <div class="tradein-prices">
                        <div class="tradein-price-row">
                            <span class="tradein-price-label">Harga Normal:</span>
                            <span class="tradein-price-value tradein-old-price">${product.oldPrice}</span>
                        </div>
                        <div class="tradein-price-row">
                            <span class="tradein-price-label">Setelah Trade-in:</span>
                            <span class="tradein-price-value tradein-new-price">${product.newPrice}</span>
                        </div>
                        <div class="tradein-price-row">
                            <span class="tradein-price-label">Anda Hemat:</span>
                            <span class="tradein-price-value tradein-discount">${product.discount}</span>
                        </div>
                    </div>
                    
                    <button class="tradein-btn" onclick="tradeinBuy(${product.id})">
                        <i class="bi bi-bag"></i> Beli Sekarang
                    </button>
                </div>
            `;
        }

        function initTradeinSlider(productsPerSlide, slideCount) {
            const slider = document.getElementById('tradeinSlider');
            const prevBtn = document.getElementById('tradeinPrev');
            const nextBtn = document.getElementById('tradeinNext');
            const dotsContainer = document.getElementById('tradeinDots');
            const sliderWrapper = document.querySelector('.tradein-slider-wrapper');

            // Jika hanya ada 1 slide, jangan inisialisasi navigasi
            if (slideCount <= 1) {
                if (sliderWrapper) {
                    sliderWrapper.classList.add('single-slide');
                }
                return;
            } else {
                if (sliderWrapper) {
                    sliderWrapper.classList.remove('single-slide');
                }
            }

            let currentSlide = 0;

            // Buat dots navigation hanya jika ada lebih dari 1 slide
            if (slideCount > 1 && dotsContainer) {
                dotsContainer.innerHTML = '';
                for (let i = 0; i < slideCount; i++) {
                    const dot = document.createElement('div');
                    dot.classList.add('tradein-dot');
                    if (i === 0) dot.classList.add('active');
                    dot.addEventListener('click', () => goToSlide(i));
                    dotsContainer.appendChild(dot);
                }
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
                
                const dots = document.querySelectorAll('.tradein-dots .tradein-dot');
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

        // Fungsi beli/trade-in
        function tradeinBuy(id) {
            const product = tradeinProducts.find(p => p.id === id);
            if (product) {
                alert(`Trade-in untuk:\n${product.name}\nHarga Normal: ${product.oldPrice}\nHarga Trade-in: ${product.newPrice}\nAnda Hemat: ${product.discount}`);
            }
        }

        // Fungsi debounce untuk optimasi event resize
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
        document.addEventListener('DOMContentLoaded', function() {
            renderTradeinSlider();
            
            // Handle resize dengan debounce
            window.addEventListener('resize', debounce(function() {
                renderTradeinSlider();
            }, 250));
        });
    </script>
</body>
</html>