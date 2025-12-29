<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Layanan iBox - Responsive Slider</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {
            background-color: #f5f7fa;
            color: #1d1d1f;
        }

        .simple-services-container {
            width: 100%;
            max-width: 1400px;
            margin: 0 auto;
            padding-top: 30px;
        }

        .page-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .page-header h1 {
            font-size: 2rem;
            font-weight: 700;
            color: #1d1d1f;
            margin-bottom: 10px;
        }

        /* SIMPLE SLIDER STYLES */
        .simple-services-wrapper {
            position: relative;
            overflow: hidden;
            border-radius: 12px;
            background: white;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
            margin: 0 auto;
            border: 1px solid #e0e0e0;
        }

        .simple-slider {
            display: flex;
            transition: transform 0.5s ease;
            width: 100%;
        }

        .simple-slide {
            min-width: 100%;
            display: flex;
            gap: 20px;
            padding: 25px;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .simple-slide.active {
            opacity: 1;
        }

        /* CARD STYLES - LEBIH KECIL DAN SEDERHANA */
        .simple-card {
            flex: 1;
            min-width: 0;
            background: white;
            border-radius: 10px;
            padding: 20px;
            border: 1px solid #e0e0e0;
            display: flex;
            flex-direction: column;
            position: relative;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .simple-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        /* Warna border top untuk setiap card */
        .simple-card[data-service-type="pickup"] {
            border-top: 4px solid #0071bc;
        }

        .simple-card[data-service-type="eraxpress"] {
            border-top: 4px solid #ff3b30;
        }

        .simple-card[data-service-type="financing"] {
            border-top: 4px solid #0071bc;
        }

        .simple-card[data-service-type="experience"] {
            border-top: 4px solid #5856d6;
        }

        .simple-card[data-service-type="sale"] {
            border-top: 4px solid #008900;
        }

        .simple-icon {
            font-size: 1.5rem;
            margin-bottom: 15px;
        }

        .simple-card[data-service-type="pickup"] .simple-icon {
            color: #0071bc;
        }

        .simple-card[data-service-type="eraxpress"] .simple-icon {
            color: #ff3b30;
        }

        .simple-card[data-service-type="financing"] .simple-icon {
            color: #0071bc;
        }

        .simple-card[data-service-type="experience"] .simple-icon {
            color: #5856d6;
        }

        .simple-card[data-service-type="sale"] .simple-icon {
            color: #008900;
        }

        .simple-category {
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 10px;
        }

        .simple-card[data-service-type="pickup"] .simple-category {
            color: #0071bc;
        }

        .simple-card[data-service-type="eraxpress"] .simple-category {
            color: #ff3b30;
        }

        .simple-card[data-service-type="financing"] .simple-category {
            color: #0071bc;
        }

        .simple-card[data-service-type="experience"] .simple-category {
            color: #5856d6;
        }

        .simple-card[data-service-type="sale"] .simple-category {
            color: #008900;
        }

        .simple-title {
            font-size: 1rem;
            font-weight: 600;
            line-height: 1.3;
            color: #1d1d1f;
            margin-bottom: 15px;
            min-height: 40px;
        }

        .simple-footer {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #f0f0f5;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: auto;
        }

        .simple-badge {
            background-color: #f5f5f7;
            color: #86868b;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .simple-cta {
            background-color: #0071bc;
            color: white;
            padding: 8px 15px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.8rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.3s ease;
        }

        .simple-cta:hover {
            background-color: #005a9c;
        }

        .simple-cta i {
            margin-left: 5px;
            font-size: 0.8rem;
        }

        /* Navigation Buttons */
        .simple-controls {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 100%;
            display: flex;
            justify-content: space-between;
            padding: 0 15px;
            z-index: 10;
        }

        .simple-nav-btn {
            background: white;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 18px;
            color: #333;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .simple-nav-btn:hover {
            background: #0071bc;
            color: white;
        }

        .simple-nav-btn:disabled {
            opacity: 0.3;
            cursor: not-allowed;
        }

        .simple-nav-btn:disabled:hover {
            background: white;
            color: #333;
        }

        /* Dots Navigation */
        .simple-dots {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 5px;
            margin-bottom: 20px;
        }

        .simple-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #ddd;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .simple-dot.active {
            background: #0071bc;
        }

        /* RESPONSIVE STYLES - MODIFIKASI UTAMA */
        /* Desktop: 4 cards per slide (default) */
        @media (min-width: 993px) {
            .simple-slide .simple-card {
                flex: 0 0 calc(25% - 15px); /* 4 cards per slide */
                min-width: calc(25% - 15px);
            }
        }

        /* Tablet: 1 card per slide */
        @media (max-width: 992px) and (min-width: 577px) {
            .simple-slide {
                padding: 20px;
                gap: 15px;
                justify-content: center;
            }
            
            .simple-slide .simple-card {
                flex: 0 0 100%; /* 1 card per slide */
                max-width: 400px; /* Maksimal lebar card */
                margin: 0 auto;
            }
            
            .simple-title {
                min-height: 35px;
            }
            
            .page-header h1 {
                font-size: 1.8rem;
            }
        }

        /* Mobile: 1 card per slide */
        @media (max-width: 576px) {
            .simple-slide {
                padding: 15px;
                gap: 10px;
                justify-content: center;
            }
            
            .simple-slide .simple-card {
                flex: 0 0 100%; /* 1 card per slide */
                padding: 15px;
            }
            
            .simple-title {
                min-height: 30px;
                font-size: 0.95rem;
            }
            
            .simple-cta {
                padding: 6px 12px;
                font-size: 0.75rem;
            }
            
            .page-header h1 {
                font-size: 1.6rem;
            }
            
            .simple-services-wrapper {
                border-radius: 10px;
            }
            
            .simple-controls {
                padding: 0 10px;
            }
            
            .simple-nav-btn {
                width: 35px;
                height: 35px;
                font-size: 16px;
            }
            
            .simple-dots {
                gap: 8px;
            }
        }

        /* Ukuran layar sangat kecil */
        @media (max-width: 360px) {
            .simple-slide {
                padding: 10px;
            }
            
            .simple-card {
                padding: 12px;
            }
            
            .simple-icon {
                font-size: 1.3rem;
                margin-bottom: 10px;
            }
            
            .simple-category {
                font-size: 0.75rem;
            }
            
            .simple-title {
                font-size: 0.9rem;
                min-height: 28px;
            }
            
            .simple-footer {
                flex-direction: column;
                gap: 10px;
                align-items: flex-start;
            }
            
            .simple-badge {
                align-self: flex-start;
            }
            
            .simple-cta {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="simple-services-container">
        <div class="page-header">
            <h1>Layanan & Penawaran Eksklusif</h1>
        </div>
        
        <!-- SIMPLE SLIDER -->
        <div class="simple-services-wrapper">
            <div class="simple-controls">
                <button class="simple-nav-btn simple-prev" id="simplePrev" disabled>
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button class="simple-nav-btn simple-next" id="simpleNext">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
            
            <div class="simple-slider" id="simpleSlider">
                <!-- Slides akan di-generate oleh JavaScript -->
            </div>
            
            <div class="simple-dots" id="simpleDots"></div>
        </div>
    </div>

    <script>
        // Data layanan
        const simpleServices = [
            {
                id: 1,
                type: "pickup",
                category: "PICKUP",
                title: "Kirim dan Ambil. Belanja online dan bebas biaya kirim.",
                icon: "fas fa-shopping-bag",
                badge: "Gratis Ongkir",
                ctaText: "Pelajari"
            },
            {
                id: 2,
                type: "eraxpress",
                category: "eraXpress",
                title: "Kirim cepat, tanpa takut telat.",
                icon: "fas fa-shipping-fast",
                badge: "Same Day Delivery",
                ctaText: "Cek Jadwal"
            },
            {
                id: 3,
                type: "financing",
                category: "FINANCING",
                title: "Dapatkan harga spesial dan cicilan 0% untuk produk Apple.",
                icon: "fas fa-credit-card",
                badge: "0% Bunga",
                ctaText: "Ajukan Sekarang"
            },
            {
                id: 4,
                type: "experience",
                category: "IBOX EXPERIENCE DAYS",
                title: "Memaksimalkan penggunaan produk Apple anda bersama Apple expert",
                icon: "fas fa-chalkboard-teacher",
                badge: "Expert Session",
                ctaText: "Daftar Kelas"
            },
            {
                id: 5,
                type: "sale",
                category: "SALE",
                title: "Penawaran terbaik hari ini untuk Belanja Online dan Click & PickUp",
                icon: "fas fa-tags",
                badge: "Limited Time",
                ctaText: "Lihat Promo"
            }
        ];

        // Variabel slider
        let currentSimpleSlide = 0;
        let totalSimpleSlides = 0;
        let cardsPerSlide = 4; // Default untuk desktop
        
        // Fungsi untuk menentukan jumlah card per slide berdasarkan lebar layar
        function getCardsPerSlide() {
            if (window.innerWidth <= 576) { // Mobile
                return 1;
            } else if (window.innerWidth <= 992) { // Tablet
                return 1;
            } else { // Desktop
                return 4;
            }
        }
        
        // Render slider
        function renderSimpleSlider() {
            const slider = document.getElementById('simpleSlider');
            const dotsContainer = document.getElementById('simpleDots');
            
            // Reset
            slider.innerHTML = '';
            dotsContainer.innerHTML = '';
            
            // Update cardsPerSlide berdasarkan lebar layar
            cardsPerSlide = getCardsPerSlide();
            
            // Hitung jumlah slide
            totalSimpleSlides = Math.ceil(simpleServices.length / cardsPerSlide);
            
            // Buat slides
            for (let i = 0; i < totalSimpleSlides; i++) {
                const slide = document.createElement('div');
                slide.className = i === 0 ? 'simple-slide active' : 'simple-slide';
                
                // Tambahkan layanan ke slide ini
                for (let j = 0; j < cardsPerSlide; j++) {
                    const index = i * cardsPerSlide + j;
                    if (index < simpleServices.length) {
                        slide.appendChild(createSimpleCard(simpleServices[index]));
                    }
                }
                
                slider.appendChild(slide);
            }
            
            // Buat dots navigation
            for (let i = 0; i < totalSimpleSlides; i++) {
                const dot = document.createElement('div');
                dot.className = i === 0 ? 'simple-dot active' : 'simple-dot';
                dot.addEventListener('click', () => goToSimpleSlide(i));
                dotsContainer.appendChild(dot);
            }
            
            // Update tombol navigasi
            updateSimpleNavButtons();
        }

        // Buat card sederhana
        function createSimpleCard(service) {
            const card = document.createElement('div');
            card.className = 'simple-card';
            card.setAttribute('data-service-type', service.type);
            
            card.innerHTML = `
                <div class="simple-icon">
                    <i class="${service.icon}"></i>
                </div>
                <div class="simple-category">${service.category}</div>
                <div class="simple-title">${service.title}</div>
                <div class="simple-footer">
                    <div class="simple-badge">${service.badge}</div>
                    <a href="#" class="simple-cta" onclick="simpleServiceAction(${service.id})">
                        ${service.ctaText} <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            `;
            return card;
        }

        // Fungsi pindah slide
        function goToSimpleSlide(index) {
            if (index < 0 || index >= totalSimpleSlides) return;
            
            // Update slide aktif
            const slides = document.querySelectorAll('.simple-slide');
            const dots = document.querySelectorAll('.simple-dot');
            
            // Sembunyikan slide lama
            slides[currentSimpleSlide].classList.remove('active');
            
            // Tampilkan slide baru
            slides[index].classList.add('active');
            dots[currentSimpleSlide].classList.remove('active');
            dots[index].classList.add('active');
            
            // Update slider position
            const slider = document.getElementById('simpleSlider');
            slider.style.transform = `translateX(-${index * 100}%)`;
            
            // Update current slide
            currentSimpleSlide = index;
            
            // Update tombol navigasi
            updateSimpleNavButtons();
        }

        // Next slide
        function nextSimpleSlide() {
            if (currentSimpleSlide < totalSimpleSlides - 1) {
                goToSimpleSlide(currentSimpleSlide + 1);
            }
        }

        // Prev slide
        function prevSimpleSlide() {
            if (currentSimpleSlide > 0) {
                goToSimpleSlide(currentSimpleSlide - 1);
            }
        }

        // Update tombol navigasi
        function updateSimpleNavButtons() {
            const prevBtn = document.getElementById('simplePrev');
            const nextBtn = document.getElementById('simpleNext');
            
            prevBtn.disabled = currentSimpleSlide === 0;
            nextBtn.disabled = currentSimpleSlide === totalSimpleSlides - 1;
        }

        // Fungsi aksi layanan
        function simpleServiceAction(id) {
            const service = simpleServices.find(s => s.id === id);
            if (service) {
                alert(`Anda memilih layanan: ${service.category}\n${service.title}`);
            }
        }
        
        // Fungsi untuk menangani perubahan ukuran layar
        function handleResize() {
            const newCardsPerSlide = getCardsPerSlide();
            
            // Jika jumlah card per slide berubah, render ulang slider
            if (newCardsPerSlide !== cardsPerSlide) {
                // Simpan slide aktif saat ini berdasarkan data yang ditampilkan
                const currentFirstIndex = currentSimpleSlide * cardsPerSlide;
                
                // Render ulang slider
                renderSimpleSlider();
                
                // Hitung slide baru yang harus aktif
                const newSlideIndex = Math.floor(currentFirstIndex / newCardsPerSlide);
                
                // Pindah ke slide yang sesuai
                goToSimpleSlide(Math.min(newSlideIndex, totalSimpleSlides - 1));
            }
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            renderSimpleSlider();
            
            // Event listeners untuk tombol navigasi
            document.getElementById('simplePrev').addEventListener('click', prevSimpleSlide);
            document.getElementById('simpleNext').addEventListener('click', nextSimpleSlide);
            
            // Keyboard navigation
            document.addEventListener('keydown', function(e) {
                if (e.key === 'ArrowLeft') prevSimpleSlide();
                if (e.key === 'ArrowRight') nextSimpleSlide();
            });
            
            // Event listener untuk resize window
            window.addEventListener('resize', handleResize);
        });
    </script>
</body>
</html>