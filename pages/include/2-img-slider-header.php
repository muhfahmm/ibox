<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>iBox Slider</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>

<body>


    <div class="slider-container">
        <style>
            /* CSS untuk image slider */
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                background-color: #f5f5f7;
            }

            .slider-container {
                position: relative;
                width: 100%;
                max-width: 1400px;
                margin: 0 auto;
                overflow: hidden;
                border-radius: 12px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
                height: 500px;
            }

            .slider {
                display: flex;
                transition: transform 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
                height: 100%;
                width: 100%;
            }

            .slide {
                min-width: 100%;
                height: 100%;
                position: relative;
                flex-shrink: 0;
            }

            .slide img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                display: block;
            }

            .slide-content {
                position: absolute;
                bottom: 60px;
                left: 60px;
                background: rgba(255, 255, 255, 0.95);
                padding: 30px;
                border-radius: 12px;
                max-width: 500px;
                box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
                backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.2);
            }

            .slide-content h3 {
                color: #1d1d1f;
                font-size: 32px;
                margin-bottom: 12px;
                font-weight: 700;
                line-height: 1.2;
            }

            .slide-content p {
                color: #515154;
                font-size: 17px;
                margin-bottom: 25px;
                line-height: 1.5;
            }

            .slide-btn {
                background-color: #007aff;
                color: white;
                border: none;
                padding: 14px 30px;
                border-radius: 28px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                display: inline-flex;
                align-items: center;
                gap: 10px;
                font-size: 15px;
                letter-spacing: 0.3px;
                box-shadow: 0 4px 20px rgba(0, 122, 255, 0.3);
            }

            .slide-btn:hover {
                background-color: #0056cc;
                transform: translateY(-3px);
                box-shadow: 0 8px 25px rgba(0, 122, 255, 0.4);
            }

            .slide-btn:active {
                transform: translateY(-1px);
            }

            .slider-nav {
                position: absolute;
                top: 50%;
                transform: translateY(-50%);
                width: 100%;
                display: flex;
                justify-content: space-between;
                padding: 0 25px;
                z-index: 10;
                pointer-events: none;
            }

            .nav-btn {
                background-color: rgba(255, 255, 255, 0.95);
                border: none;
                width: 56px;
                height: 56px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                font-size: 22px;
                color: #1d1d1f;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
                opacity: 1;
                pointer-events: auto;
            }

            .nav-btn:hover {
                background-color: #ffffff;
                transform: scale(1.15);
                color: #007aff;
                box-shadow: 0 6px 25px rgba(0, 0, 0, 0.2);
            }

            .nav-btn:active {
                transform: scale(1.05);
            }

            /* Status indicator (simple dots) */
            .slider-status {
                position: absolute;
                bottom: 25px;
                left: 50%;
                transform: translateX(-50%);
                display: flex;
                gap: 8px;
                z-index: 10;
            }

            .status-dot {
                width: 8px;
                height: 8px;
                border-radius: 50%;
                background-color: rgba(255, 255, 255, 0.4);
                cursor: pointer;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .status-dot.active {
                background-color: #ffffff;
                transform: scale(1.3);
            }

            .status-dot:hover {
                background-color: rgba(255, 255, 255, 0.8);
            }

            /* Mobile-first responsive styles */
            @media (max-width: 1400px) {
                .slider-container {
                    max-width: 1200px;
                    height: 450px;
                }
            }

            @media (max-width: 1200px) {
                .slider-container {
                    max-width: 1000px;
                    height: 400px;
                }

                .slide-content {
                    bottom: 50px;
                    left: 50px;
                    max-width: 420px;
                    padding: 25px;
                }

                .slide-content h3 {
                    font-size: 28px;
                }

                .slide-content p {
                    font-size: 16px;
                }
            }

            @media (max-width: 992px) {
                .slider-container {
                    height: 350px;
                    border-radius: 10px;
                }

                .slide-content {
                    bottom: 40px;
                    left: 40px;
                    max-width: 380px;
                    padding: 22px;
                }

                .slide-content h3 {
                    font-size: 24px;
                }

                .slide-content p {
                    font-size: 15px;
                    margin-bottom: 20px;
                }

                .slide-btn {
                    padding: 12px 25px;
                    font-size: 14px;
                }

                .nav-btn {
                    width: 48px;
                    height: 48px;
                    font-size: 20px;
                }
            }

            @media (max-width: 768px) {

                .slider-container {
                    height: 320px;
                    border-radius: 8px;
                }

                .slide-content {
                    bottom: 30px;
                    left: 30px;
                    max-width: 320px;
                    padding: 20px;
                }

                .slide-content h3 {
                    font-size: 22px;
                }

                .slide-content p {
                    font-size: 14px;
                    margin-bottom: 18px;
                }

                .slide-btn {
                    padding: 11px 22px;
                    font-size: 13.5px;
                }

                .nav-btn {
                    width: 44px;
                    height: 44px;
                    font-size: 18px;
                    padding: 0 15px;
                }

                .slider-nav {
                    padding: 0 15px;
                }

                .slider-status {
                    bottom: 15px;
                }
            }

            @media (max-width: 576px) {
                body {
                    padding: 10px;
                }

                .slider-container {
                    height: 280px;
                    border-radius: 6px;
                }

                .slide-content {
                    position: relative;
                    background: white;
                    max-width: 100%;
                    left: 0;
                    bottom: 0;
                    border-radius: 0;
                    padding: 20px 25px;
                    margin-top: -5px;
                    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
                }

                .slide-content h3 {
                    font-size: 20px;
                }

                .slide-content p {
                    font-size: 13.5px;
                }

                .slider-status {
                    bottom: 10px;
                }

                .nav-btn {
                    opacity: 1;
                    background-color: rgba(255, 255, 255, 0.9);
                    width: 40px;
                    height: 40px;
                    font-size: 16px;
                }
            }

            @media (max-width: 480px) {
                .slider-container {
                    height: 250px;
                }

                .slide-content {
                    padding: 18px 20px;
                }

                .slide-content h3 {
                    font-size: 18px;
                }

                .slide-content p {
                    font-size: 13px;
                }

                .slide-btn {
                    padding: 10px 20px;
                    font-size: 13px;
                    border-radius: 24px;
                }

                .nav-btn {
                    width: 36px;
                    height: 36px;
                    font-size: 15px;
                    padding: 0 12px;
                }

                .slider-status {
                    bottom: 8px;
                }
            }

            @media (max-width: 400px) {
                .slider-container {
                    height: 220px;
                }

                .slide-content {
                    padding: 15px 18px;
                }

                .slide-content h3 {
                    font-size: 17px;
                }

                .slide-content p {
                    font-size: 12.5px;
                    margin-bottom: 15px;
                }

                .slide-btn {
                    padding: 9px 18px;
                    font-size: 12.5px;
                }

                .slider-status {
                    bottom: 6px;
                }
            }

            /* Animation for slide transitions */
            @keyframes fadeIn {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .slide-content {
                animation: fadeIn 0.6s cubic-bezier(0.4, 0, 0.2, 1) 0.3s both;
            }
        </style>
        <div class="slider" id="ibox-slider">
            <!-- Slide 1: iPhone -->
            <div class="slide">
                <img src="https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?ixlib=rb-1.2.1&auto=format&fit=crop&w=1200&q=80" alt="iPhone 15 Pro">
                <div class="slide-content">
                    <h3>iPhone 15 Pro</h3>
                    <p>Titanium. So strong. So light. So Pro. Rasakan kekuatan chip A17 Pro yang revolusioner.</p>
                    <button class="slide-btn" onclick="window.location.href='#'">
                        <i class="bi bi-bag"></i> Beli Sekarang
                    </button>
                </div>
            </div>

            <!-- Slide 2: MacBook -->
            <div class="slide">
                <img src="https://images.unsplash.com/photo-1496181133206-80ce9b88a853?ixlib=rb-1.2.1&auto=format&fit=crop&w=1200&q=80" alt="MacBook Pro">
                <div class="slide-content">
                    <h3>MacBook Pro</h3>
                    <p>Ditenagai chip M3 yang luar biasa. Untuk pengembang, desainer, dan profesional kreatif.</p>
                    <button class="slide-btn" onclick="window.location.href='#'">
                        <i class="bi bi-laptop"></i> Jelajahi Mac
                    </button>
                </div>
            </div>

            <!-- Slide 3: Apple Watch -->
            <div class="slide">
                <img src="https://images.unsplash.com/photo-1523275335684-37898b6baf30?ixlib=rb-1.2.1&auto=format&fit=crop&w=1200&q=80" alt="Apple Watch">
                <div class="slide-content">
                    <h3>Apple Watch Series 9</h3>
                    <p>Lebih cerdas, lebih cerah, lebih kuat. Pantau kesehatan dan tingkatkan produktivitas Anda.</p>
                    <button class="slide-btn" onclick="window.location.href='#'">
                        <i class="bi bi-watch"></i> Lihat Watch
                    </button>
                </div>
            </div>

            <!-- Slide 4: iPad -->
            <div class="slide">
                <img src="https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?ixlib=rb-1.2.1&auto=format&fit=crop&w=1200&q=80" alt="iPad Pro">
                <div class="slide-content">
                    <h3>iPad Pro</h3>
                    <p>Chip M2 yang super cepat. Layar Liquid Retina XDR yang menakjubkan. Sangat Pro.</p>
                    <button class="slide-btn" onclick="window.location.href='#'">
                        <i class="bi bi-tablet"></i> Beli iPad
                    </button>
                </div>
            </div>

            <!-- Slide 5: Aksesori -->
            <div class="slide">
                <img src="https://images.unsplash.com/photo-1581094794329-c8112a89af12?ixlib=rb-1.2.1&auto=format&fit=crop&w=1200&q=80" alt="Aksesori Apple">
                <div class="slide-content">
                    <h3>Aksesori Apple</h3>
                    <p>Temukan koleksi lengkap aksesori untuk melengkapi perangkat Apple Anda.</p>
                    <button class="slide-btn" onclick="window.location.href='#'">
                        <i class="bi bi-headphones"></i> Lihat Aksesori
                    </button>
                </div>
            </div>
        </div>

        <!-- Navigation buttons -->
        <div class="slider-nav">
            <button class="nav-btn prev-btn">
                <i class="bi bi-chevron-left"></i>
            </button>
            <button class="nav-btn next-btn">
                <i class="bi bi-chevron-right"></i>
            </button>
        </div>

        <!-- Simple status dots -->
        <div class="slider-status" id="sliderStatus">
            <!-- Dots will be generated by JavaScript -->
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const slider = document.getElementById('ibox-slider');
                const slides = document.querySelectorAll('.slide');
                const prevBtn = document.querySelector('.prev-btn');
                const nextBtn = document.querySelector('.next-btn');
                const sliderStatus = document.getElementById('sliderStatus');

                let currentSlide = 0;
                let slideInterval;
                let isForwardDirection = true;
                let autoPlaySpeed = 3000; // 3 detik per slide (sesuai delay auto swipe)

                // Create status dots
                function createStatusDots() {
                    sliderStatus.innerHTML = '';
                    for (let i = 0; i < slides.length; i++) {
                        const dot = document.createElement('div');
                        dot.classList.add('status-dot');
                        if (i === 0) dot.classList.add('active');
                        dot.addEventListener('click', () => {
                            clearInterval(slideInterval);
                            goToSlide(i);
                            startAutoPlay();
                        });
                        sliderStatus.appendChild(dot);
                    }
                }

                // Go to specific slide
                function goToSlide(slideIndex) {
                    currentSlide = slideIndex;
                    slider.style.transform = `translateX(-${currentSlide * 100}%)`;
                    updateStatusDots();
                }

                // Next slide - always forward direction for manual navigation
                function nextSlide() {
                    let newSlide = currentSlide + 1;
                    if (newSlide >= slides.length) newSlide = 0;
                    goToSlide(newSlide);
                }

                // Previous slide - always forward direction for manual navigation
                function prevSlide() {
                    let newSlide = currentSlide - 1;
                    if (newSlide < 0) newSlide = slides.length - 1;
                    goToSlide(newSlide);
                }

                // Auto slide function with forward/backward pattern (1,2,3,4,5,4,3,2,1)
                function autoSlide() {
                    let newSlide;

                    if (isForwardDirection) {
                        // Moving forward: 1,2,3,4,5
                        newSlide = currentSlide + 1;
                        if (newSlide >= slides.length) {
                            // Reached the end, switch to backward direction
                            isForwardDirection = false;
                            newSlide = currentSlide - 1;
                        }
                    } else {
                        // Moving backward: 5,4,3,2,1
                        newSlide = currentSlide - 1;
                        if (newSlide < 0) {
                            // Reached the beginning, switch to forward direction
                            isForwardDirection = true;
                            newSlide = currentSlide + 1;
                        }
                    }

                    goToSlide(newSlide);
                }

                // Update status dots
                function updateStatusDots() {
                    const dots = document.querySelectorAll('.status-dot');
                    dots.forEach((dot, index) => {
                        dot.classList.toggle('active', index === currentSlide);
                    });
                }

                // Start autoplay
                function startAutoPlay() {
                    clearInterval(slideInterval);
                    slideInterval = setInterval(autoSlide, autoPlaySpeed);
                }

                // Stop autoplay
                function stopAutoPlay() {
                    clearInterval(slideInterval);
                }

                // Initialize slider
                function initSlider() {
                    createStatusDots();
                    startAutoPlay();

                    // Event listeners for navigation buttons
                    prevBtn.addEventListener('click', () => {
                        prevSlide();
                        resetAutoPlay();
                    });

                    nextBtn.addEventListener('click', () => {
                        nextSlide();
                        resetAutoPlay();
                    });

                    // Reset autoplay after manual navigation
                    function resetAutoPlay() {
                        stopAutoPlay();
                        startAutoPlay();
                    }

                    // Keyboard navigation
                    document.addEventListener('keydown', (e) => {
                        if (e.key === 'ArrowLeft') {
                            prevSlide();
                            resetAutoPlay();
                        } else if (e.key === 'ArrowRight') {
                            nextSlide();
                            resetAutoPlay();
                        }
                    });

                    // Pause on hover
                    slider.addEventListener('mouseenter', stopAutoPlay);
                    slider.addEventListener('mouseleave', startAutoPlay);

                    // Touch/swipe support for mobile
                    let touchStartX = 0;
                    let touchEndX = 0;
                    let touchStartY = 0;
                    let touchEndY = 0;

                    slider.addEventListener('touchstart', (e) => {
                        touchStartX = e.changedTouches[0].screenX;
                        touchStartY = e.changedTouches[0].screenY;
                        stopAutoPlay();
                    });

                    slider.addEventListener('touchend', (e) => {
                        touchEndX = e.changedTouches[0].screenX;
                        touchEndY = e.changedTouches[0].screenY;

                        // Calculate swipe distance
                        const diffX = touchStartX - touchEndX;
                        const diffY = touchStartY - touchEndY;

                        // Only trigger swipe if horizontal movement is greater than vertical
                        if (Math.abs(diffX) > Math.abs(diffY)) {
                            handleSwipe(diffX);
                        }

                        // Restart autoplay after swipe
                        setTimeout(startAutoPlay, 1000);
                    });

                    function handleSwipe(diff) {
                        const swipeThreshold = 50;

                        if (Math.abs(diff) > swipeThreshold) {
                            if (diff > 0) {
                                // Swipe left - next slide
                                nextSlide();
                            } else {
                                // Swipe right - previous slide
                                prevSlide();
                            }
                        }
                    }

                    // Handle window resize
                    let resizeTimeout;
                    window.addEventListener('resize', () => {
                        clearTimeout(resizeTimeout);
                        resizeTimeout = setTimeout(() => {
                            // Reset slider position on resize
                            slider.style.transform = `translateX(-${currentSlide * 100}%)`;
                        }, 250);
                    });
                }

                // Initialize the slider
                initSlider();
            });
        </script>
    </div>


</body>

</html>