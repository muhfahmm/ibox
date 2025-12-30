<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>iBox Layanan</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

</head>

<body>
    <div class="kelas-layanan-container">
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

            .kelas-layanan-container {
                padding: 40px 20px;
                max-width: 1400px;
                margin: 0 auto;
            }

            /* HEADING STYLES */
            .kelas-layanan-header {
                text-align: center;
                margin-bottom: 30px;
            }

            .kelas-layanan-header h1 {
                font-size: 28px;
                font-weight: 600;
                color: #333;
                margin-bottom: 15px;
                text-align: center;
                position: relative;
                padding-bottom: 15px;
            }

            .kelas-layanan-header h1::after {
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
            .kelas-layanan-wrapper {
                position: relative;
                overflow: hidden;
                border-radius: 12px;
                background: white;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
                margin-bottom: 40px;
            }

            .kelas-layanan-wrapper.single-slide {
                padding-bottom: 30px;
            }

            /* SLIDER */
            .kelas-layanan-slider {
                display: flex;
                transition: transform 0.5s ease-in-out;
                gap: 0;
            }

            /* SLIDE */
            .kelas-layanan-slide {
                min-width: 100%;
                box-sizing: border-box;
                display: flex;
                justify-content: center;
                flex-shrink: 0;
            }

            /* SLIDE INNER - Flex layout untuk konten */
            .kelas-layanan-slide-inner {
                display: flex;
                gap: 20px;
                width: 100%;
                padding: 10px 5px;
                justify-content: center;
            }

            /* CONTENT CARD */
            .kelas-layanan-content-card {
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
                flex: 1;
                min-width: 0;
            }

            .kelas-layanan-content-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 8px 20px rgba(0, 122, 255, 0.1);
                border-color: #007aff;
            }

            /* IMAGE STYLES */
            .kelas-layanan-content-img {
                width: 100%;
                margin-bottom: 15px;
                display: flex;
                justify-content: center;
            }

            .kelas-layanan-content-img img {
                width: 160px;
                height: 160px;
                object-fit: contain;
                border-radius: 8px;
                background-color: #f8f9fa;
                padding: 12px;
            }

            /* TITLE STYLES */
            .kelas-layanan-title {
                font-size: 11px;
                font-weight: 600;
                color: #707070;
                text-transform: uppercase;
                letter-spacing: 1px;
                margin-bottom: 8px;
            }

            /* TEXT STYLES */
            .kelas-layanan-text {
                width: 100%;
                text-align: center;
            }

            .kelas-layanan-text-heading {
                font-size: 18px;
                font-weight: 800;
                color: #333;
                margin-bottom: 12px;
                line-height: 1.3;
                display: flex;
                align-items: center;
                justify-content: center;
                min-height: 50px;
            }

            .kelas-layanan-first-paragraph,
            .kelas-layanan-second-paragraph {
                font-size: 13px;
                font-weight: 600;
                color: #666;
                margin-bottom: 8px;
                line-height: 1.4;
            }

            .kelas-layanan-second-paragraph {
                font-weight: 400;
            }

            /* Line break styling for long text */
            .line-break {
                display: block;
                margin-top: 3px;
            }

            /* NAVIGATION BUTTONS */
            .kelas-layanan-controls {
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

            .kelas-layanan-controls.hidden {
                display: none;
            }

            .kelas-layanan-nav-btn {
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

            .kelas-layanan-nav-btn.hidden {
                display: none;
            }

            .kelas-layanan-nav-btn:hover {
                background-color: #007aff;
                color: white;
                transform: scale(1.1);
                box-shadow: 0 5px 15px rgba(0, 122, 255, 0.3);
            }

            .kelas-layanan-nav-btn:disabled {
                opacity: 0.3;
                cursor: not-allowed;
                background-color: #f0f0f0;
            }

            .kelas-layanan-nav-btn:disabled:hover {
                transform: none;
                background-color: #f0f0f0;
                color: #333;
            }

            /* DOTS INDICATOR */
            .kelas-layanan-dots {
                display: flex;
                justify-content: center;
                align-items: center;
                padding: 15px 0 25px;
                gap: 8px;
            }

            .kelas-layanan-dot {
                width: 8px;
                height: 8px;
                border-radius: 50%;
                background-color: #ddd;
                border: none;
                cursor: pointer;
                transition: all 0.3s ease;
                padding: 0;
            }

            .kelas-layanan-dot:hover {
                background-color: #aaa;
            }

            .kelas-layanan-dot.active {
                border-radius: 10px;
                background-color: #007aff;
            }

            .kelas-layanan-dots.hidden {
                display: none;
            }

            /* RESPONSIVE STYLES */
            /* Desktop & Tablet: 2 card per slide (≥769px) */
            @media (min-width: 769px) {
                .kelas-layanan-content-card {
                    flex: 0 0 calc(50% - 15px);
                    min-width: calc(50% - 15px);
                    max-width: none;
                }

                .kelas-layanan-slide-inner {
                    gap: 30px;
                    padding: 10px 15px;
                    justify-content: center;
                }

                .kelas-layanan-content-img img {
                    width: 180px;
                    height: 180px;
                    padding: 15px;
                }

                .kelas-layanan-text-heading {
                    font-size: 20px;
                    min-height: 55px;
                }

                .kelas-layanan-first-paragraph,
                .kelas-layanan-second-paragraph {
                    font-size: 14px;
                }

                .kelas-layanan-wrapper {
                    padding: 25px 0px 20px;
                }

                .kelas-layanan-controls {
                    width: calc(100% - 30px);
                    left: 15px;
                }

                .kelas-layanan-dots {
                    padding: 20px 0 15px;
                }
            }

            /* Mobile: 1 card per slide (≤768px) */
            @media (max-width: 768px) {
                .kelas-layanan-content-card {
                    flex: 0 0 100%;
                    min-width: 100%;
                    max-width: 100%;
                    padding: 25px;
                }

                .kelas-layanan-slide-inner {
                    gap: 0;
                    padding: 10px 20px;
                    justify-content: center;
                }

                .kelas-layanan-content-img img {
                    width: 180px;
                    height: 180px;
                    padding: 15px;
                }

                .kelas-layanan-text-heading {
                    font-size: 20px;
                    min-height: 55px;
                    margin-bottom: 15px;
                }

                .kelas-layanan-first-paragraph,
                .kelas-layanan-second-paragraph {
                    font-size: 15px;
                    margin-bottom: 10px;
                }

                .kelas-layanan-second-paragraph {
                    margin-bottom: 0;
                }

                .kelas-layanan-wrapper {
                    padding: 20px 0px 15px;
                }

                .kelas-layanan-controls {
                    width: calc(100% - 30px);
                    left: 15px;
                }

                .kelas-layanan-nav-btn {
                    width: 45px;
                    height: 45px;
                    font-size: 20px;
                }

                .kelas-layanan-header h1 {
                    font-size: 24px;
                }

                .kelas-layanan-dots {
                    padding: 15px 0 10px;
                }
            }

            /* Mobile Kecil: 1 card per slide dengan penyesuaian */
            @media (max-width: 576px) {
                .kelas-layanan-content-card {
                    padding: 20px;
                }

                .kelas-layanan-content-img img {
                    width: 150px;
                    height: 150px;
                    padding: 12px;
                }

                .kelas-layanan-text-heading {
                    font-size: 18px;
                    min-height: 50px;
                }

                .kelas-layanan-first-paragraph,
                .kelas-layanan-second-paragraph {
                    font-size: 14px;
                }

                .kelas-layanan-wrapper {
                    padding: 15px 0px 10px;
                }

                .kelas-layanan-controls {
                    width: calc(100% - 20px);
                    left: 10px;
                }

                .kelas-layanan-nav-btn {
                    width: 40px;
                    height: 40px;
                    font-size: 18px;
                }

                .kelas-layanan-dots {
                    padding: 12px 0 8px;
                }
            }

            @media (max-width: 480px) {
                .kelas-layanan-container {
                    padding: 30px 15px;
                }

                .kelas-layanan-header h1 {
                    font-size: 22px;
                }

                .kelas-layanan-content-card {
                    padding: 18px;
                }

                .kelas-layanan-content-img img {
                    width: 130px;
                    height: 130px;
                    padding: 10px;
                }

                .kelas-layanan-text-heading {
                    font-size: 17px;
                    min-height: 48px;
                }

                .kelas-layanan-first-paragraph,
                .kelas-layanan-second-paragraph {
                    font-size: 13px;
                }
            }

            @media (max-width: 360px) {
                .kelas-layanan-content-card {
                    padding: 15px;
                }

                .kelas-layanan-content-img img {
                    width: 110px;
                    height: 110px;
                    padding: 8px;
                }

                .kelas-layanan-text-heading {
                    font-size: 16px;
                    min-height: 45px;
                }

                .kelas-layanan-first-paragraph,
                .kelas-layanan-second-paragraph {
                    font-size: 12px;
                }
            }
        </style>
        <!-- HEADING -->
        <div class="kelas-layanan-header">
            <h1>Layanan iBox untuk Anda</h1>
        </div>

        <!-- SLIDER -->
        <div class="kelas-layanan-wrapper" id="kelasLayananWrapper">
            <!-- Konten akan di-generate oleh JavaScript -->
        </div>
        <script>
            // Data layanan dengan teks yang sudah diformat
            const layananData = [{
                    id: 1,
                    title: "IN STORE",
                    heading: "Kirim atau ambil",
                    firstParagraph: "Bebas biaya kirim dan ambil sendiri",
                    secondParagraph: "Ambil pesananmu di toko iBox terdekat",
                    image: "https://cdnpro.eraspace.com/media/wysiwyg/mobileapps/foto_kirim_atau_ambil_-_desktop.png"
                },
                {
                    id: 2,
                    title: "IN STORE",
                    heading: "Cari toko terdekat",
                    firstParagraph: "Belanja produk terbaru Apple",
                    secondParagraph: "Temukan cabang iBox di terdekatmu",
                    image: "https://cdnpro.eraspace.com/media/wysiwyg/mobileapps/foto_toko_-_desktop.png"
                },
                {
                    id: 3,
                    title: "IN STORE",
                    heading: "Ikuti kelas",
                    firstParagraph: "Gratis setiap sesi kelasnya",
                    secondParagraph: "Sesi kreativitas gratis untukmu dalam bidang seni",
                    image: "https://cdnpro.eraspace.com/media/wysiwyg/mobileapps/foto_ikuti_kelas_-_desktop.png"
                },
                {
                    id: 4,
                    title: "IN STORE",
                    heading: "Bantuan untukmu",
                    firstParagraph: "iBox selalu ada solusi untukmu",
                    secondParagraph: "Mulai dari mengatur perangkat hingga memulihkan ID Apple",
                    image: "https://cdnpro.eraspace.com/media/wysiwyg/mobileapps/foto_bantuan_untukmu_-_desktop.png"
                }
            ];

            // Fungsi untuk memformat teks dengan pemisahan kalimat
            function formatText(text, maxWords = 5) {
                // Pisahkan teks menjadi kata-kata
                const words = text.split(' ');

                // Jika kata kurang dari atau sama dengan maxWords, kembalikan teks asli
                if (words.length <= maxWords) {
                    return text;
                }

                // Hitung titik tengah
                const midPoint = Math.floor(words.length / 2);

                // Gabungkan kata-kata menjadi dua baris
                const firstLine = words.slice(0, midPoint).join(' ');
                const secondLine = words.slice(midPoint).join(' ');

                // Kembalikan teks dengan line break
                return `${firstLine}<span class="line-break">${secondLine}</span>`;
            }

            // ===== FUNGSI UNTUK KELAS LAYANAN SLIDER =====
            function renderKelasLayananSlider() {
                const wrapper = document.getElementById('kelasLayananWrapper');

                // Tentukan jumlah item per slide berdasarkan lebar layar
                const itemsPerSlide = getItemsPerSlide();
                const slideCount = Math.ceil(layananData.length / itemsPerSlide);
                const isSingleSlide = slideCount <= 1;

                console.log(`Items per slide: ${itemsPerSlide}, Slide count: ${slideCount}, Single slide: ${isSingleSlide}`);

                let html = `
                <div class="kelas-layanan-controls ${isSingleSlide ? 'hidden' : ''}">
                    <button class="kelas-layanan-nav-btn kelas-layanan-prev ${isSingleSlide ? 'hidden' : ''}" data-action="prev">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                    <button class="kelas-layanan-nav-btn kelas-layanan-next ${isSingleSlide ? 'hidden' : ''}" data-action="next">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </div>
                <div class="kelas-layanan-slider">
            `;

                // Buat slide berdasarkan jumlah item per slide
                for (let slideIndex = 0; slideIndex < slideCount; slideIndex++) {
                    html += `<div class="kelas-layanan-slide">`;
                    html += `<div class="kelas-layanan-slide-inner" data-slide-index="${slideIndex}">`;

                    // Tambahkan item ke slide ini
                    for (let itemIndex = 0; itemIndex < itemsPerSlide; itemIndex++) {
                        const dataIndex = slideIndex * itemsPerSlide + itemIndex;
                        if (dataIndex < layananData.length) {
                            html += createLayananCard(layananData[dataIndex]);
                        }
                    }

                    html += `</div></div>`;
                }

                html += `</div>`;

                // Tambahkan dots indikator
                html += `<div class="kelas-layanan-dots ${isSingleSlide ? 'hidden' : ''}" id="kelasLayananDots">`;

                for (let i = 0; i < slideCount; i++) {
                    html += `<button class="kelas-layanan-dot ${i === 0 ? 'active' : ''}" data-slide="${i}"></button>`;
                }

                html += `</div>`;

                wrapper.innerHTML = html;
                initKelasLayananSlider(itemsPerSlide, slideCount);
            }

            function getItemsPerSlide() {
                const screenWidth = window.innerWidth;

                // Mobile (≤ 768px): 1 card per slide
                if (screenWidth <= 768) {
                    return 1;
                }
                // Tablet & Desktop (> 768px): 2 cards per slide
                else {
                    return 2;
                }
            }

            function createLayananCard(item) {
                // Format teks dengan pemisahan kalimat
                const formattedHeading = formatText(item.heading, 5);
                const formattedFirstParagraph = formatText(item.firstParagraph, 5);
                const formattedSecondParagraph = formatText(item.secondParagraph, 5);

                return `
                <div class="kelas-layanan-content-card">
                    <div class="kelas-layanan-content-img">
                        <img src="${item.image}" alt="${item.heading}">
                    </div>
                    <p class="kelas-layanan-title">${item.title}</p>
                    <div class="kelas-layanan-text">
                        <h4 class="kelas-layanan-text-heading">${formattedHeading}</h4>
                        <p class="kelas-layanan-first-paragraph">${formattedFirstParagraph}</p>
                        <p class="kelas-layanan-second-paragraph">${formattedSecondParagraph}</p>
                    </div>
                </div>
            `;
            }

            function initKelasLayananSlider(itemsPerSlide, slideCount) {
                const slider = document.querySelector('.kelas-layanan-slider');
                const prevBtn = document.querySelector('.kelas-layanan-prev');
                const nextBtn = document.querySelector('.kelas-layanan-next');
                const wrapper = document.querySelector('.kelas-layanan-wrapper');
                const controls = document.querySelector('.kelas-layanan-controls');
                const dotsContainer = document.getElementById('kelasLayananDots');
                const dots = document.querySelectorAll('.kelas-layanan-dot');

                console.log(`Init slider - Slide count: ${slideCount}, Items per slide: ${itemsPerSlide}`);

                // Jika hanya ada 1 slide, sembunyikan controls dan dots
                if (slideCount <= 1) {
                    if (wrapper) {
                        wrapper.classList.add('single-slide');
                    }
                    if (controls) {
                        controls.classList.add('hidden');
                    }
                    if (dotsContainer) {
                        dotsContainer.classList.add('hidden');
                    }
                    console.log('Single slide - hiding controls and dots');
                    return;
                } else {
                    if (wrapper) {
                        wrapper.classList.remove('single-slide');
                    }
                    if (controls) {
                        controls.classList.remove('hidden');
                    }
                    if (dotsContainer) {
                        dotsContainer.classList.remove('hidden');
                    }
                    console.log('Multiple slides - showing controls and dots');
                }

                let currentSlide = 0;

                // Fungsi untuk update dots indikator
                function updateDots() {
                    dots.forEach((dot, index) => {
                        if (index === currentSlide) {
                            dot.classList.add('active');
                        } else {
                            dot.classList.remove('active');
                        }
                    });
                }

                function updateNavButtons() {
                    if (prevBtn) {
                        prevBtn.disabled = currentSlide === 0;
                        console.log(`Prev button disabled: ${prevBtn.disabled}`);
                    }
                    if (nextBtn) {
                        nextBtn.disabled = currentSlide === slideCount - 1;
                        console.log(`Next button disabled: ${nextBtn.disabled}`);
                    }
                }

                function goToSlide(slideIndex) {
                    if (slideIndex < 0 || slideIndex >= slideCount) return;

                    currentSlide = slideIndex;
                    if (slider) {
                        slider.style.transform = `translateX(-${currentSlide * 100}%)`;
                        console.log(`Moving to slide: ${slideIndex}`);
                    }
                    updateNavButtons();
                    updateDots();
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

                // Event listeners untuk tombol navigasi
                if (prevBtn) {
                    prevBtn.addEventListener('click', prevSlide);
                    console.log('Prev button event listener added');
                }
                if (nextBtn) {
                    nextBtn.addEventListener('click', nextSlide);
                    console.log('Next button event listener added');
                }

                // Event listeners untuk dots
                dots.forEach(dot => {
                    dot.addEventListener('click', function() {
                        const slideIndex = parseInt(this.getAttribute('data-slide'));
                        goToSlide(slideIndex);
                    });
                });

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
                }

                // Update tombol navigasi dan dots pertama kali
                updateNavButtons();
                updateDots();

                // Set posisi awal ke slide 0
                if (slider) {
                    slider.style.transform = `translateX(0)`;
                }
            }

            // ===== INISIALISASI =====
            document.addEventListener('DOMContentLoaded', function() {
                renderKelasLayananSlider();

                // Handle resize - re-render slider saat ukuran layar berubah
                let resizeTimeout;
                window.addEventListener('resize', function() {
                    clearTimeout(resizeTimeout);
                    resizeTimeout = setTimeout(() => {
                        renderKelasLayananSlider();
                    }, 250);
                });
            });
        </script>
    </div>


</body>

</html>