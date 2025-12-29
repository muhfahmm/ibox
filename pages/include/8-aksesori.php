<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aksesori Unggulan Apple</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: #f8f9fa;
            color: #1d1d1f;
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* CONTAINER UTAMA */
        .container-aksesori {
            width: 100%;
            max-width: 1400px;
            margin: 50px auto 30px;
            padding: 0 20px;
            position: relative;
        }

        /* HEADING STYLE */
        .section-heading {
            text-align: center;
            position: relative;
            margin-bottom: 30px;
        }

        .section-heading h1 {
            font-size: 2.5rem;
            font-weight: 700;
            color: #1d1d1f;
            display: inline-block;
            position: relative;
            margin-bottom: 15px;
            letter-spacing: -0.5px;
        }

        .section-heading h1::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background-color: #007aff;
        }

        .section-heading p {
            font-size: 1.1rem;
            color: #86868b;
            max-width: 600px;
            margin: 25px auto 0;
            font-weight: 400;
        }

        /* SLIDER WRAPPER */
        .aksesori-slider-wrapper {
            position: relative;
            overflow: hidden;
            border-radius: 24px;
            padding: 30px 0 50px;
            margin: 0;
            background: #ffffff;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }

        /* SLIDER TRACK */
        .aksesori-slider-track {
            display: flex;
            transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
            gap: 25px;
            padding: 0 60px;
        }

        /* CONTENT CARD - SEMUA CARD SAMA TINGGI */
        .content-aksesori {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
            height: 100%;
            position: relative;
            flex-shrink: 0;
        }

        .content-aksesori:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(0, 122, 255, 0.1);
            border-color: rgba(0, 122, 255, 0.15);
        }

        /* HEADER CARD - SEMUA GAMBAR SAMA TINGGI */
        .header-card-aksesori {
            width: 100%;
            height: 220px;
            overflow: hidden;
            background: linear-gradient(135deg, #f5f5f7 0%, #ffffff 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
        }

        .header-card-aksesori::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(0, 0, 0, 0.05), transparent);
        }

        .header-card-aksesori img {
            width: auto;
            height: 160px;
            max-width: 100%;
            object-fit: contain;
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.1));
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .content-aksesori:hover .header-card-aksesori img {
            transform: scale(1.08) translateY(-3px);
            filter: drop-shadow(0 6px 12px rgba(0, 0, 0, 0.15));
        }

        /* FOOTER CARD - KONTEN YANG FLEKSIBEL */
        .footer-card-aksesori {
            padding: 25px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .badge-category {
            display: inline-block;
            background: rgba(0, 122, 255, 0.1);
            color: #007aff;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            margin-bottom: 15px;
            align-self: flex-start;
        }

        .nama-aksesori {
            font-size: 1.4rem;
            font-weight: 700;
            color: #1d1d1f;
            margin-bottom: 12px;
            line-height: 1.3;
            letter-spacing: -0.2px;
            min-height: 60px;
        }

        .deskripsi-aksesori {
            font-size: 0.95rem;
            color: #515154;
            margin-bottom: 20px;
            line-height: 1.6;
            flex-grow: 1;
        }

        /* HARGA DAN TOMBEL - VERTIKAL LAYOUT */
        .harga-container {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-top: auto;
        }

        .harga-aksesori {
            font-size: 1.2rem;
            font-weight: 700;
            color: #007aff;
            background: rgba(0, 122, 255, 0.08);
            padding: 12px 20px;
            border-radius: 12px;
            text-align: center;
            width: 100%;
            display: block;
            box-sizing: border-box;
        }

        .btn-beli {
            background: #007aff;
            color: white;
            border: none;
            padding: 14px 24px;
            border-radius: 12px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            box-shadow: 0 4px 12px rgba(0, 122, 255, 0.2);
            box-sizing: border-box;
        }

        .btn-beli:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 122, 255, 0.3);
            background: #0066cc;
        }

        .btn-beli i {
            font-size: 0.95rem;
        }

        /* NAVIGATION BUTTONS */
        .aksesori-nav-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: white;
            border: none;
            width: 56px;
            height: 56px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-size: 1.5rem;
            color: #1d1d1f;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            z-index: 20;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.8);
        }

        .aksesori-nav-btn:hover:not(:disabled) {
            background: #007aff;
            color: white;
            transform: translateY(-50%) scale(1.1);
            box-shadow: 0 10px 25px rgba(0, 122, 255, 0.3);
        }

        .aksesori-nav-btn:disabled {
            opacity: 0.4;
            cursor: not-allowed;
            background: #f5f5f7;
            color: #86868b;
            transform: translateY(-50%);
        }

        .aksesori-nav-btn:disabled:hover {
            background: #f5f5f7;
            color: #86868b;
            transform: translateY(-50%);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        }

        .aksesori-prev {
            left: 20px;
        }

        .aksesori-next {
            right: 20px;
        }

        /* DOTS INDICATOR */
        .aksesori-dots {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 14px;
            margin-top: 30px;
        }

        .aksesori-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: rgba(0, 0, 0, 0.1);
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            border: none;
            padding: 0;
        }

        .aksesori-dot:hover {
            background: rgba(0, 122, 255, 0.6);
            transform: scale(1.2);
        }

        .aksesori-dot.active {
            background: #007aff;
            transform: scale(1.3);
            box-shadow: 0 3px 8px rgba(0, 122, 255, 0.3);
        }

        /* RESPONSIVE DESIGN - SISTEM GRID RESPONSIF */
        /* Desktop Besar: 3 produk per slide (3-3-3) */
        @media (min-width: 1200px) {
            .aksesori-slider-track {
                gap: 30px;
                padding: 0 70px;
            }
            
            .content-aksesori {
                width: calc((100% - 60px) / 3);
            }
        }

        /* Desktop Sedang: 3 produk per slide */
        @media (min-width: 992px) and (max-width: 1199px) {
            .aksesori-slider-track {
                gap: 25px;
                padding: 0 60px;
            }
            
            .content-aksesori {
                width: calc((100% - 50px) / 3);
            }
            
            .header-card-aksesori {
                height: 200px;
            }
            
            .header-card-aksesori img {
                height: 140px;
            }
            
            .nama-aksesori {
                font-size: 1.3rem;
                min-height: 55px;
            }
            
            .deskripsi-aksesori {
                font-size: 0.9rem;
            }
        }

        /* Tablet Landscape: 2 produk per slide (2-2-2-2) */
        @media (min-width: 768px) and (max-width: 991px) {
            .aksesori-slider-track {
                gap: 25px;
                padding: 0 60px;
            }
            
            .content-aksesori {
                width: calc((100% - 25px) / 2);
            }
            
            .section-heading h1 {
                font-size: 2.2rem;
            }
            
            .header-card-aksesori {
                height: 200px;
            }
            
            .header-card-aksesori img {
                height: 140px;
            }
            
            .footer-card-aksesori {
                padding: 22px;
            }
            
            .nama-aksesori {
                font-size: 1.3rem;
                min-height: 55px;
            }
            
            .deskripsi-aksesori {
                font-size: 0.9rem;
            }
            
            .harga-aksesori {
                font-size: 1.15rem;
                padding: 11px 18px;
            }
            
            .btn-beli {
                padding: 12px 22px;
                font-size: 0.9rem;
            }
        }

        /* Tablet Portrait: 2 produk per slide */
        @media (min-width: 576px) and (max-width: 767px) {
            .aksesori-slider-track {
                gap: 20px;
                padding: 0 60px;
            }
            
            .content-aksesori {
                width: calc((100% - 20px) / 2);
            }
            
            .section-heading h1 {
                font-size: 2rem;
            }
            
            .header-card-aksesori {
                height: 180px;
            }
            
            .header-card-aksesori img {
                height: 120px;
            }
            
            .footer-card-aksesori {
                padding: 20px;
            }
            
            .nama-aksesori {
                font-size: 1.2rem;
                min-height: 50px;
            }
            
            .deskripsi-aksesori {
                font-size: 0.88rem;
            }
            
            .harga-aksesori {
                font-size: 1.1rem;
                padding: 10px 16px;
            }
            
            .btn-beli {
                padding: 11px 20px;
                font-size: 0.88rem;
            }
            
            .aksesori-nav-btn {
                width: 48px;
                height: 48px;
                font-size: 1.3rem;
            }
            
            .aksesori-prev {
                left: 15px;
            }
            
            .aksesori-next {
                right: 15px;
            }
        }

        /* Mobile: 1 produk per slide (1-1-1-1) */
        @media (max-width: 575px) {
            .container-aksesori {
                margin: 30px auto;
                padding: 0 15px;
            }
            
            .section-heading h1 {
                font-size: 1.8rem;
            }
            
            .section-heading p {
                font-size: 1rem;
                padding: 0;
            }
            
            .aksesori-slider-wrapper {
                padding: 20px 0 40px;
                border-radius: 18px;
            }
            
            .aksesori-slider-track {
                gap: 0;
                padding: 0 50px;
            }
            
            .content-aksesori {
                width: 100%;
                margin: 0 5px;
                border-radius: 16px;
            }
            
            .header-card-aksesori {
                height: 180px;
                padding: 15px;
            }
            
            .header-card-aksesori img {
                height: 120px;
            }
            
            .footer-card-aksesori {
                padding: 18px;
            }
            
            .badge-category {
                font-size: 0.8rem;
                padding: 5px 14px;
            }
            
            .nama-aksesori {
                font-size: 1.2rem;
                min-height: 45px;
            }
            
            .deskripsi-aksesori {
                font-size: 0.88rem;
            }
            
            .harga-aksesori {
                font-size: 1.1rem;
                padding: 10px 16px;
            }
            
            .btn-beli {
                padding: 12px 20px;
                font-size: 0.9rem;
            }
            
            .aksesori-nav-btn {
                width: 44px;
                height: 44px;
                font-size: 1.2rem;
            }
            
            .aksesori-prev {
                left: 10px;
            }
            
            .aksesori-next {
                right: 10px;
            }
            
            .aksesori-dots {
                gap: 12px;
                margin-top: 25px;
            }
            
            .aksesori-dot {
                width: 8px;
                height: 8px;
            }
        }

        /* Mobile Sangat Kecil */
        @media (max-width: 400px) {
            .container-aksesori {
                padding: 0 10px;
            }
            
            .section-heading h1 {
                font-size: 1.6rem;
            }
            
            .aksesori-slider-track {
                padding: 0 40px;
            }
            
            .header-card-aksesori {
                height: 160px;
            }
            
            .header-card-aksesori img {
                height: 110px;
            }
            
            .footer-card-aksesori {
                padding: 16px;
            }
            
            .nama-aksesori {
                font-size: 1.1rem;
                min-height: 40px;
            }
            
            .deskripsi-aksesori {
                font-size: 0.85rem;
            }
            
            .harga-aksesori {
                font-size: 1rem;
                padding: 9px 14px;
            }
            
            .btn-beli {
                padding: 10px 18px;
                font-size: 0.85rem;
            }
            
            .aksesori-nav-btn {
                width: 40px;
                height: 40px;
                font-size: 1.1rem;
            }
            
            .aksesori-prev {
                left: 5px;
            }
            
            .aksesori-next {
                right: 5px;
            }
        }

        /* Auto-hide dots jika hanya 1 slide */
        .aksesori-dots.hidden {
            display: none;
        }

        /* Hide navigation jika hanya 1 slide */
        .aksesori-nav-btn.hidden {
            display: none;
        }

        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .content-aksesori {
            animation: fadeIn 0.6s ease forwards;
        }

        /* Equal Height for Cards */
        .equal-height {
            display: flex;
            flex-wrap: wrap;
        }
    </style>
</head>

<body>
    <div class="container-aksesori">
        <!-- HEADING SECTION -->
        <div class="section-heading">
            <h1>Aksesori Unggulan Apple</h1>
            <p>Temukan aksesori premium untuk melengkapi perangkat Apple Anda dengan pengalaman terbaik</p>
        </div>

        <!-- SLIDER WRAPPER -->
        <div class="aksesori-slider-wrapper">
            <!-- Navigation Buttons -->
            <button class="aksesori-nav-btn aksesori-prev" id="aksesoriPrevBtn">
                <i class="bi bi-chevron-left"></i>
            </button>
            
            <button class="aksesori-nav-btn aksesori-next" id="aksesoriNextBtn">
                <i class="bi bi-chevron-right"></i>
            </button>

            <!-- SLIDER TRACK -->
            <div class="aksesori-slider-track" id="aksesoriSliderTrack">
                <!-- Card 1 -->
                <div class="content-aksesori">
                    <div class="header-card-aksesori">
                        <img src="https://esmeralda.cygnuss-district8.com/media/wysiwyg/ibox-v4/images/aksesori-unggulan/aksesori-mac.png" alt="Aksesori Mac">
                    </div>
                    <div class="footer-card-aksesori">
                        <span class="badge-category">Mac</span>
                        <h3 class="nama-aksesori">Aksesori Mac</h3>
                        <p class="deskripsi-aksesori">Tingkatkan produktivitas dengan aksesori premium untuk MacBook dan iMac Anda. Dari keyboard hingga docking station.</p>
                        <div class="harga-container">
                            <span class="harga-aksesori">Mulai Rp459.000</span>
                            <button class="btn-beli">
                                <i class="bi bi-cart-plus"></i> Beli Sekarang
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Card 2 -->
                <div class="content-aksesori">
                    <div class="header-card-aksesori">
                        <img src="https://esmeralda.cygnuss-district8.com/media/wysiwyg/ibox-v4/images/aksesori-unggulan/aksesori-ipad.png" alt="Aksesori iPad">
                    </div>
                    <div class="footer-card-aksesori">
                        <span class="badge-category">iPad</span>
                        <h3 class="nama-aksesori">Aksesori iPad</h3>
                        <p class="deskripsi-aksesori">Transformasikan iPad Anda menjadi workstation kreatif dengan keyboard, Apple Pencil, dan case premium.</p>
                        <div class="harga-container">
                            <span class="harga-aksesori">Mulai Rp1.599.000</span>
                            <button class="btn-beli">
                                <i class="bi bi-cart-plus"></i> Beli Sekarang
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Card 3 -->
                <div class="content-aksesori">
                    <div class="header-card-aksesori">
                        <img src="https://esmeralda.cygnuss-district8.com/media/wysiwyg/ibox-v4/images/aksesori-unggulan/aksesori-watch.png" alt="Aksesori Watch">
                    </div>
                    <div class="footer-card-aksesori">
                        <span class="badge-category">Watch</span>
                        <h3 class="nama-aksesori">Aksesori Watch</h3>
                        <p class="deskripsi-aksesori">Personalisasikan Apple Watch dengan koleksi strap eksklusif dan charger portable yang stylish.</p>
                        <div class="harga-container">
                            <span class="harga-aksesori">Mulai Rp399.000</span>
                            <button class="btn-beli">
                                <i class="bi bi-cart-plus"></i> Beli Sekarang
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Card 4 -->
                <div class="content-aksesori">
                    <div class="header-card-aksesori">
                        <img src="https://cdnpro.eraspace.com/media/wysiwyg/IMG-17900036_m_jpeg_1.webp" alt="Aksesori iPhone">
                    </div>
                    <div class="footer-card-aksesori">
                        <span class="badge-category">iPhone</span>
                        <h3 class="nama-aksesori">Aksesori iPhone</h3>
                        <p class="deskripsi-aksesori">Lindungi dan tingkatkan pengalaman iPhone dengan case premium, charger MagSafe, dan earphone terbaru.</p>
                        <div class="harga-container">
                            <span class="harga-aksesori">Mulai Rp200.000</span>
                            <button class="btn-beli">
                                <i class="bi bi-cart-plus"></i> Beli Sekarang
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Card 5 -->
                <div class="content-aksesori">
                    <div class="header-card-aksesori">
                        <img src="https://store.storeimages.cdn-apple.com/8756/as-images.apple.com/is/MU8F2?wid=572&hei=572&fmt=jpeg&qlt=95&.v=1542406417329" alt="Apple Pencil">
                    </div>
                    <div class="footer-card-aksesori">
                        <span class="badge-category">iPad</span>
                        <h3 class="nama-aksesori">Apple Pencil (2nd Gen)</h3>
                        <p class="deskripsi-aksesori">Alat tulis presisi untuk iPad dengan latensi rendah dan pengisian nirkabel yang mudah.</p>
                        <div class="harga-container">
                            <span class="harga-aksesori">Rp2.199.000</span>
                            <button class="btn-beli">
                                <i class="bi bi-cart-plus"></i> Beli Sekarang
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Card 6 -->
                <div class="content-aksesori">
                    <div class="header-card-aksesori">
                        <img src="https://store.storeimages.cdn-apple.com/8756/as-images.apple.com/is/airpods-pro-2-hero-select-202409?wid=940&hei=1112&fmt=jpeg&qlt=90&.v=1723507647577" alt="AirPods Pro">
                    </div>
                    <div class="footer-card-aksesori">
                        <span class="badge-category">Audio</span>
                        <h3 class="nama-aksesori">AirPods Pro (2nd Gen)</h3>
                        <p class="deskripsi-aksesori">Pengalaman audio terbaik dengan Active Noise Cancellation dan Adaptive Audio.</p>
                        <div class="harga-container">
                            <span class="harga-aksesori">Rp3.999.000</span>
                            <button class="btn-beli">
                                <i class="bi bi-cart-plus"></i> Beli Sekarang
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- DOTS INDICATOR -->
        <div class="aksesori-dots" id="aksesoriDots"></div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Elemen DOM
            const sliderTrack = document.getElementById('aksesoriSliderTrack');
            const prevBtn = document.getElementById('aksesoriPrevBtn');
            const nextBtn = document.getElementById('aksesoriNextBtn');
            const dotsContainer = document.getElementById('aksesoriDots');
            
            // Variabel slider
            const cards = document.querySelectorAll('.content-aksesori');
            const totalCards = cards.length;
            let currentSlide = 0;
            let cardsPerView = 3; // Default 3 cards per slide untuk desktop
            
            // Fungsi untuk menghitung cards per view berdasarkan lebar layar
            function calculateCardsPerView() {
                const screenWidth = window.innerWidth;
                
                if (screenWidth >= 1200) return 3;     // Desktop besar: 3-3-3
                if (screenWidth >= 768) return 2;      // Tablet: 2-2-2-2
                return 1;                               // Mobile: 1-1-1-1
            }
            
            // Fungsi untuk menghitung total slides
            function calculateTotalSlides() {
                cardsPerView = calculateCardsPerView();
                return Math.ceil(totalCards / cardsPerView);
            }
            
            let totalSlides = calculateTotalSlides();
            
            // Fungsi untuk membuat dots indicator
            function createDots() {
                dotsContainer.innerHTML = '';
                totalSlides = calculateTotalSlides();
                
                if (totalSlides <= 1) {
                    dotsContainer.classList.add('hidden');
                } else {
                    dotsContainer.classList.remove('hidden');
                    
                    for (let i = 0; i < totalSlides; i++) {
                        const dot = document.createElement('div');
                        dot.classList.add('aksesori-dot');
                        if (i === currentSlide) dot.classList.add('active');
                        dot.setAttribute('data-slide', i);
                        dot.addEventListener('click', () => goToSlide(i));
                        dotsContainer.appendChild(dot);
                    }
                }
            }
            
            // Fungsi untuk update slider position
            function updateSliderPosition() {
                // Hitung lebar satu card termasuk gap
                const cardStyle = window.getComputedStyle(cards[0]);
                const cardWidth = cards[0].offsetWidth;
                const gap = parseFloat(window.getComputedStyle(sliderTrack).gap) || 25;
                
                const translateX = currentSlide * cardsPerView * (cardWidth + gap);
                sliderTrack.style.transform = `translateX(-${translateX}px)`;
                
                // Update dots
                const dots = document.querySelectorAll('.aksesori-dot');
                dots.forEach((dot, index) => {
                    dot.classList.toggle('active', index === currentSlide);
                });
                
                // Update navigation buttons
                updateNavigationButtons();
            }
            
            // Fungsi untuk update navigation buttons
            function updateNavigationButtons() {
                if (totalSlides <= 1) {
                    prevBtn.classList.add('hidden');
                    nextBtn.classList.add('hidden');
                    return;
                }
                
                prevBtn.classList.remove('hidden');
                nextBtn.classList.remove('hidden');
                
                prevBtn.disabled = currentSlide === 0;
                nextBtn.disabled = currentSlide === totalSlides - 1;
            }
            
            // Fungsi untuk pindah slide
            function goToSlide(slideIndex) {
                if (slideIndex < 0 || slideIndex >= totalSlides) return;
                
                // Animasi untuk transisi slide
                sliderTrack.style.transition = 'transform 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
                
                currentSlide = slideIndex;
                updateSliderPosition();
            }
            
            // Fungsi untuk next slide
            function nextSlide() {
                if (currentSlide < totalSlides - 1) {
                    currentSlide++;
                    updateSliderPosition();
                }
            }
            
            // Fungsi untuk prev slide
            function prevSlide() {
                if (currentSlide > 0) {
                    currentSlide--;
                    updateSliderPosition();
                }
            }
            
            // Event listeners untuk navigation buttons
            prevBtn.addEventListener('click', prevSlide);
            nextBtn.addEventListener('click', nextSlide);
            
            // Keyboard navigation
            document.addEventListener('keydown', (e) => {
                if (e.key === 'ArrowLeft') {
                    prevSlide();
                } else if (e.key === 'ArrowRight') {
                    nextSlide();
                }
            });
            
            // Touch/swipe untuk mobile
            let touchStartX = 0;
            let touchEndX = 0;
            
            sliderTrack.addEventListener('touchstart', (e) => {
                touchStartX = e.changedTouches[0].clientX;
            });
            
            sliderTrack.addEventListener('touchend', (e) => {
                touchEndX = e.changedTouches[0].clientX;
                const swipeThreshold = 50;
                const diff = touchStartX - touchEndX;
                
                if (Math.abs(diff) > swipeThreshold) {
                    if (diff > 0) {
                        nextSlide();
                    } else {
                        prevSlide();
                    }
                }
            });
            
            // Handle window resize dengan debounce
            let resizeTimeout;
            window.addEventListener('resize', () => {
                clearTimeout(resizeTimeout);
                resizeTimeout = setTimeout(() => {
                    cardsPerView = calculateCardsPerView();
                    totalSlides = calculateTotalSlides();
                    
                    // Reset to first slide jika current slide tidak valid setelah resize
                    if (currentSlide >= totalSlides) {
                        currentSlide = totalSlides - 1;
                    }
                    
                    createDots();
                    updateSliderPosition();
                    updateNavigationButtons();
                }, 250);
            });
            
            // Fungsi untuk menyamakan tinggi semua card dalam satu slide
            function equalizeCardHeights() {
                if (cards.length === 0 || cardsPerView === 1) return;
                
                // Reset semua tinggi ke auto terlebih dahulu
                cards.forEach(card => {
                    card.style.height = 'auto';
                });
                
                // Sinkronisasi tinggi card per slide
                for (let slideIndex = 0; slideIndex < totalSlides; slideIndex++) {
                    const startIdx = slideIndex * cardsPerView;
                    const endIdx = Math.min(startIdx + cardsPerView, totalCards);
                    const slideCards = Array.from(cards).slice(startIdx, endIdx);
                    
                    if (slideCards.length === 0) continue;
                    
                    // Cari tinggi maksimum dalam slide
                    let maxHeight = 0;
                    slideCards.forEach(card => {
                        const cardHeight = card.offsetHeight;
                        if (cardHeight > maxHeight) maxHeight = cardHeight;
                    });
                    
                    // Terapkan tinggi maksimum ke semua card dalam slide
                    slideCards.forEach(card => {
                        card.style.height = `${maxHeight}px`;
                    });
                }
            }
            
            // Inisialisasi
            cardsPerView = calculateCardsPerView();
            totalSlides = calculateTotalSlides();
            createDots();
            updateNavigationButtons();
            updateSliderPosition();
            
            // Samakan tinggi card setelah semua gambar dimuat
            const images = document.querySelectorAll('.header-card-aksesori img');
            let loadedImages = 0;
            
            function checkAllImagesLoaded() {
                loadedImages++;
                if (loadedImages === images.length) {
                    // Tunggu sebentar agar layout stabil
                    setTimeout(() => {
                        equalizeCardHeights();
                    }, 100);
                }
            }
            
            images.forEach(img => {
                if (img.complete) {
                    checkAllImagesLoaded();
                } else {
                    img.addEventListener('load', checkAllImagesLoaded);
                    img.addEventListener('error', checkAllImagesLoaded);
                }
            });
            
            // Jika tidak ada gambar, tetap jalankan equalize
            if (images.length === 0) {
                setTimeout(equalizeCardHeights, 100);
            }
            
            // Event listener untuk tombol beli
            document.querySelectorAll('.btn-beli').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    // Animasi klik
                    this.style.transform = 'scale(0.95)';
                    this.style.transition = 'transform 0.2s ease';
                    
                    setTimeout(() => {
                        this.style.transform = '';
                        
                        // Simulasi aksi beli
                        const productName = this.closest('.content-aksesori').querySelector('.nama-aksesori').textContent;
                        const productPrice = this.closest('.content-aksesori').querySelector('.harga-aksesori').textContent;
                        alert(`Terima kasih! Anda akan membeli:\n\n${productName}\n${productPrice}`);
                    }, 200);
                });
            });
        });
    </script>
</body>
</html>