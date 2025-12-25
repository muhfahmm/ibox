<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout Sekarang - iBox</title>
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

        /* CONTAINER UTAMA CHECKOUT */
        .container-checkout {
            width: 100%;
            max-width: 1400px;
            margin: 50px auto 30px;
            padding: 0 20px;
            position: relative;
        }

        /* HEADING STYLE CHECKOUT */
        .section-heading-checkout {
            text-align: center;
            margin-bottom: 25px;
            position: relative;
        }

        .section-heading-checkout h1 {
            font-size: 2.5rem;
            font-weight: 700;
            color: #1d1d1f;
            display: inline-block;
            position: relative;
            margin-bottom: 15px;
            letter-spacing: -0.5px;
        }

        .section-heading-checkout h1::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: linear-gradient(90deg, #007aff, #0056d6);
            border-radius: 2px;
        }

        .section-heading-checkout p {
            font-size: 1.1rem;
            color: #86868b;
            max-width: 600px;
            margin: 0 auto;
            font-weight: 400;
        }

        /* SLIDER WRAPPER CHECKOUT */
        .checkout-slider-wrapper {
            position: relative;
            overflow: hidden;
            border-radius: 24px;
            padding: 30px 0;
            margin: 0 20px;
            background: #ffffff;
            box-shadow: 
                0 10px 30px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(0, 122, 255, 0.08);
        }

        /* SLIDER TRACK CHECKOUT */
        .checkout-slider-track {
            display: flex;
            transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
            gap: 30px;
            padding: 0 60px;
            align-items: stretch;
        }

        /* CONTENT CARD CHECKOUT */
        .content-checkout {
            flex: 0 0 calc(50% - 15px);
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 
                0 8px 30px rgba(0, 0, 0, 0.08),
                0 2px 8px rgba(0, 0, 0, 0.03);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
            min-height: 100%;
            height: auto;
            position: relative;
        }

        .content-checkout:hover {
            transform: translateY(-8px);
            box-shadow: 
                0 15px 35px rgba(0, 122, 255, 0.1),
                0 5px 15px rgba(0, 0, 0, 0.05);
            border-color: rgba(0, 122, 255, 0.15);
        }

        /* HEADER CARD CHECKOUT */
        .header-card-checkout {
            width: 100%;
            height: 200px;
            overflow: hidden;
            background: linear-gradient(135deg, #f5f5f7 0%, #ffffff 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            flex-shrink: 0;
        }

        .header-card-checkout::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(0, 0, 0, 0.05), transparent);
        }

        .header-card-checkout img {
            width: auto;
            height: 140px;
            max-width: 100%;
            object-fit: contain;
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.1));
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .content-checkout:hover .header-card-checkout img {
            transform: scale(1.08) translateY(-3px);
            filter: drop-shadow(0 6px 12px rgba(0, 0, 0, 0.15));
        }

        /* FOOTER CARD CHECKOUT */
        .footer-card-checkout {
            padding: 25px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
            min-height: 220px;
        }

        /* BADGE CHECKOUT - WARNA iBOX */
        .badge-checkout {
            display: inline-block;
            background: linear-gradient(135deg, rgba(0, 122, 255, 0.12), rgba(0, 122, 255, 0.08));
            color: #007aff;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            margin-bottom: 15px;
            align-self: flex-start;
            border: 1px solid rgba(0, 122, 255, 0.15);
            box-shadow: 0 2px 4px rgba(0, 122, 255, 0.05);
        }

        /* Variasi warna badge untuk tipe berbeda */
        .badge-checkout.stock {
            background: linear-gradient(135deg, rgba(52, 199, 89, 0.12), rgba(52, 199, 89, 0.08));
            color: #34c759;
            border-color: rgba(52, 199, 89, 0.15);
        }
        
        .badge-checkout.bestseller {
            background: linear-gradient(135deg, rgba(255, 149, 0, 0.12), rgba(255, 149, 0, 0.08));
            color: #ff9500;
            border-color: rgba(255, 149, 0, 0.15);
        }
        
        .badge-checkout.new {
            background: linear-gradient(135deg, rgba(255, 45, 85, 0.12), rgba(255, 45, 85, 0.08));
            color: #ff2d55;
            border-color: rgba(255, 45, 85, 0.15);
        }

        .nama-checkout {
            font-size: 1.4rem;
            font-weight: 700;
            color: #1d1d1f;
            margin-bottom: 12px;
            line-height: 1.3;
            letter-spacing: -0.2px;
            min-height: 40px;
            display: flex;
            align-items: center;
        }

        .deskripsi-checkout {
            font-size: 0.95rem;
            color: #515154;
            margin-bottom: 20px;
            line-height: 1.6;
            flex-grow: 1;
            min-height: 70px;
        }

        .harga-container-checkout {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: auto;
            padding-top: 20px;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
            flex-shrink: 0;
        }

        .harga-checkout {
            font-size: 1.2rem;
            font-weight: 700;
            color: #007aff;
            background: linear-gradient(135deg, rgba(0, 122, 255, 0.08), rgba(0, 122, 255, 0.04));
            padding: 10px 18px;
            border-radius: 12px;
            display: inline-block;
            white-space: nowrap;
            border: 1px solid rgba(0, 122, 255, 0.1);
        }

        /* TOMBOL CHECKOUT - WARNA iBOX */
        .btn-checkout {
            background: linear-gradient(135deg, #007aff 0%, #0056d6 100%);
            color: white;
            border: none;
            padding: 10px 22px;
            border-radius: 12px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 12px rgba(0, 122, 255, 0.2);
            white-space: nowrap;
            position: relative;
            overflow: hidden;
        }

        .btn-checkout::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: 0.5s;
        }

        .btn-checkout:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 122, 255, 0.3);
            background: linear-gradient(135deg, #0056d6 0%, #0041a8 100%);
        }

        .btn-checkout:hover::before {
            left: 100%;
        }

        .btn-checkout i {
            font-size: 0.95rem;
        }

        /* NAVIGATION BUTTONS CHECKOUT - WARNA iBOX */
        .checkout-nav-btn {
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
            box-shadow: 
                0 6px 20px rgba(0, 0, 0, 0.1),
                0 2px 4px rgba(0, 0, 0, 0.06);
            z-index: 20;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.8);
        }

        .checkout-nav-btn:hover {
            background: #007aff;
            color: white;
            transform: translateY(-50%) scale(1.1);
            box-shadow: 
                0 10px 25px rgba(0, 122, 255, 0.3),
                0 4px 8px rgba(0, 122, 255, 0.1);
        }

        .checkout-nav-btn:disabled {
            opacity: 0.4;
            cursor: not-allowed;
            background: #f5f5f7;
            color: #86868b;
            transform: translateY(-50%);
        }

        .checkout-nav-btn:disabled:hover {
            background: #f5f5f7;
            color: #86868b;
            transform: translateY(-50%);
            box-shadow: 
                0 6px 20px rgba(0, 0, 0, 0.1),
                0 2px 4px rgba(0, 0, 0, 0.06);
        }

        .checkout-prev {
            left: 20px;
        }

        .checkout-next {
            right: 20px;
        }

        /* DOTS INDICATOR CHECKOUT - WARNA iBOX */
        .checkout-dots {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 14px;
            margin-top: 40px;
        }

        .checkout-dot {
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

        .checkout-dot:hover {
            background: rgba(0, 122, 255, 0.6);
            transform: scale(1.2);
        }

        .checkout-dot.active {
            background: #007aff;
            transform: scale(1.3);
            box-shadow: 0 3px 8px rgba(0, 122, 255, 0.3);
        }

        /* RESPONSIVE DESIGN CHECKOUT */
        /* Desktop Lebar */
        @media (min-width: 1400px) {
            .section-heading-checkout h1 {
                font-size: 1.5rem;
            }
            
            .checkout-slider-track {
                padding: 0 80px;
            }
            
            .header-card-checkout {
                height: 220px;
            }
            
            .header-card-checkout img {
                height: 160px;
            }
            
            .footer-card-checkout {
                min-height: 240px;
                padding: 28px;
            }
            
            .nama-checkout {
                font-size: 1.5rem;
                min-height: 45px;
            }
            
            .deskripsi-checkout {
                font-size: 1rem;
                min-height: 75px;
            }
            
            .harga-checkout {
                font-size: 1.25rem;
                padding: 12px 20px;
            }
            
            .btn-checkout {
                padding: 12px 24px;
                font-size: 0.95rem;
            }
        }

        /* Desktop */
        @media (min-width: 1024px) {
            .content-checkout {
                flex: 0 0 calc(50% - 15px);
            }
        }

        /* Tablet */
        @media (max-width: 1024px) and (min-width: 769px) {
            .container-checkout {
                padding: 0 15px;
            }
            
            .section-heading-checkout h1 {
                font-size: 2.2rem;
            }
            
            .checkout-slider-wrapper {
                margin: 0 10px;
            }
            
            .checkout-slider-track {
                padding: 0 50px;
                gap: 25px;
            }
            
            .header-card-checkout {
                height: 190px;
            }
            
            .header-card-checkout img {
                height: 130px;
            }
            
            .footer-card-checkout {
                padding: 22px;
                min-height: 210px;
            }
            
            .nama-checkout {
                font-size: 1.3rem;
                min-height: 38px;
            }
            
            .deskripsi-checkout {
                font-size: 0.92rem;
                min-height: 65px;
            }
            
            .harga-checkout {
                font-size: 1.15rem;
                padding: 9px 16px;
            }
            
            .btn-checkout {
                padding: 9px 20px;
                font-size: 0.88rem;
            }
        }

        /* MOBILE - 1 card per slide */
        @media (max-width: 768px) {
            .container-checkout {
                margin: 30px auto;
                padding: 0 10px;
            }
            
            .section-heading-checkout h1 {
                font-size: 1.8rem;
            }
            
            .section-heading-checkout p {
                font-size: 1rem;
                padding: 0 15px;
            }
            
            .checkout-slider-wrapper {
                margin: 0 5px;
                padding: 20px 0;
                border-radius: 18px;
            }
            
            .checkout-slider-track {
                padding: 0 60px;
                gap: 0;
            }
            
            .content-checkout {
                flex: 0 0 100%;
                margin: 0 10px;
                border-radius: 16px;
                min-height: auto;
            }
            
            .checkout-nav-btn {
                width: 48px;
                height: 48px;
                font-size: 1.3rem;
            }
            
            .checkout-prev {
                left: 10px;
            }
            
            .checkout-next {
                right: 10px;
            }
            
            .header-card-checkout {
                height: 180px;
                padding: 15px;
            }
            
            .header-card-checkout img {
                height: 120px;
            }
            
            .footer-card-checkout {
                padding: 20px;
                min-height: 200px;
            }
            
            .badge-checkout {
                font-size: 0.8rem;
                padding: 5px 14px;
            }
            
            .nama-checkout {
                font-size: 1.2rem;
                min-height: 35px;
            }
            
            .deskripsi-checkout {
                font-size: 0.9rem;
                min-height: 60px;
            }
            
            .harga-container-checkout {
                flex-direction: column;
                gap: 15px;
                align-items: stretch;
            }
            
            .harga-checkout,
            .btn-checkout {
                width: 100%;
                text-align: center;
                justify-content: center;
            }
            
            .checkout-dots {
                margin-top: 30px;
            }
        }

        /* Small Mobile */
        @media (max-width: 480px) {
            .container-checkout {
                margin: 20px auto;
            }
            
            .section-heading-checkout h1 {
                font-size: 1.5rem;
            }
            
            .checkout-slider-track {
                padding: 0 50px;
            }
            
            .checkout-nav-btn {
                width: 44px;
                height: 44px;
                font-size: 1.2rem;
            }
            
            .header-card-checkout {
                height: 160px;
            }
            
            .header-card-checkout img {
                height: 110px;
            }
            
            .footer-card-checkout {
                padding: 18px;
                min-height: 190px;
            }
            
            .nama-checkout {
                font-size: 1.1rem;
                min-height: 32px;
            }
            
            .deskripsi-checkout {
                font-size: 0.85rem;
                min-height: 55px;
            }
            
            .harga-checkout {
                font-size: 1.05rem;
                padding: 8px 14px;
            }
            
            .btn-checkout {
                padding: 8px 18px;
                font-size: 0.85rem;
            }
            
            .checkout-dots {
                gap: 12px;
                margin-top: 25px;
            }
            
            .checkout-dot {
                width: 8px;
                height: 8px;
            }
        }

        /* Auto-hide dots jika hanya 1 slide */
        .checkout-dots.hidden {
            display: none;
        }

        /* Hide navigation jika hanya 1 slide */
        .checkout-nav-btn.hidden {
            display: none;
        }

        /* Loading animation */
        @keyframes fadeInCheckout {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .content-checkout {
            animation: fadeInCheckout 0.6s ease forwards;
        }

        .content-checkout:nth-child(1) { animation-delay: 0.1s; }
        .content-checkout:nth-child(2) { animation-delay: 0.2s; }
        .content-checkout:nth-child(3) { animation-delay: 0.3s; }
        .content-checkout:nth-child(4) { animation-delay: 0.4s; }
    </style>
</head>

<body>
    <div class="container-checkout">
        <!-- HEADING SECTION -->
        <div class="section-heading-checkout">
            <h1>Checkout Sekarang</h1>
        </div>

        <!-- SLIDER WRAPPER -->
        <div class="checkout-slider-wrapper">
            <!-- Navigation Buttons -->
            <button class="checkout-nav-btn checkout-prev" id="checkoutPrevBtn">
                <i class="bi bi-chevron-left"></i>
            </button>
            
            <button class="checkout-nav-btn checkout-next" id="checkoutNextBtn">
                <i class="bi bi-chevron-right"></i>
            </button>

            <!-- SLIDER TRACK -->
            <div class="checkout-slider-track" id="checkoutSliderTrack">
                <!-- Card 1 -->
                <div class="content-checkout">
                    <div class="header-card-checkout">
                        <img src="https://esmeralda.cygnuss-district8.com/media/wysiwyg/ibox-v4/images/aksesori-unggulan/aksesori-mac.png" alt="MacBook Pro M3">
                    </div>
                    <div class="footer-card-checkout">
                        <span class="badge-checkout stock">Ready Stock</span>
                        <h3 class="nama-checkout">MacBook Pro 14" M3</h3>
                        <p class="deskripsi-checkout">Laptop premium dengan chip M3, layar Liquid Retina XDR, dan baterai tahan lama hingga 18 jam. Cocok untuk profesional kreatif.</p>
                        <div class="harga-container-checkout">
                            <span class="harga-checkout">Rp 24.999.000</span>
                            <button class="btn-checkout">
                                <i class="bi bi-bag-check"></i> Checkout
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Card 2 -->
                <div class="content-checkout">
                    <div class="header-card-checkout">
                        <img src="https://esmeralda.cygnuss-district8.com/media/wysiwyg/ibox-v4/images/aksesori-unggulan/aksesori-ipad.png" alt="iPad Pro M2">
                    </div>
                    <div class="footer-card-checkout">
                        <span class="badge-checkout bestseller">Best Seller</span>
                        <h3 class="nama-checkout">iPad Pro 12.9" M2</h3>
                        <p class="deskripsi-checkout">Tablet paling canggih dengan chip M2, layar Liquid Retina XDR, dan dukungan Apple Pencil (2nd gen).</p>
                        <div class="harga-container-checkout">
                            <span class="harga-checkout">Rp 19.499.000</span>
                            <button class="btn-checkout">
                                <i class="bi bi-bag-check"></i> Checkout
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Card 3 -->
                <div class="content-checkout">
                    <div class="header-card-checkout">
                        <img src="https://esmeralda.cygnuss-district8.com/media/wysiwyg/ibox-v4/images/aksesori-unggulan/aksesori-watch.png" alt="Apple Watch Ultra 2">
                    </div>
                    <div class="footer-card-checkout">
                        <span class="badge-checkout">Limited Edition</span>
                        <h3 class="nama-checkout">Apple Watch Ultra 2</h3>
                        <p class="deskripsi-checkout">Smartwatch tahan banting dengan fitur diving, GPS presisi tinggi, dan baterai hingga 36 jam. Untuk petualang sejati.</p>
                        <div class="harga-container-checkout">
                            <span class="harga-checkout">Rp 12.999.000</span>
                            <button class="btn-checkout">
                                <i class="bi bi-bag-check"></i> Checkout
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Card 4 -->
                <div class="content-checkout">
                    <div class="header-card-checkout">
                        <img src="https://cdnpro.eraspace.com/media/wysiwyg/IMG-17900036_m_jpeg_1.webp" alt="iPhone 15 Pro">
                    </div>
                    <div class="footer-card-checkout">
                        <span class="badge-checkout new">New Arrival</span>
                        <h3 class="nama-checkout">iPhone 15 Pro Max</h3>
                        <p class="deskripsi-checkout">Smartphone flagship dengan titanium aerospace, chip A17 Pro, dan kamera 48MP. Warna Natural Titanium.</p>
                        <div class="harga-container-checkout">
                            <span class="harga-checkout">Rp 21.999.000</span>
                            <button class="btn-checkout">
                                <i class="bi bi-bag-check"></i> Checkout
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- DOTS INDICATOR -->
        <div class="checkout-dots" id="checkoutDots"></div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Elemen DOM dengan prefix checkout
            const sliderTrack = document.getElementById('checkoutSliderTrack');
            const prevBtn = document.getElementById('checkoutPrevBtn');
            const nextBtn = document.getElementById('checkoutNextBtn');
            const dotsContainer = document.getElementById('checkoutDots');
            
            // Variabel slider checkout
            const cards = document.querySelectorAll('.content-checkout');
            const totalCards = cards.length;
            let currentSlideCheckout = 0;
            let cardsPerViewCheckout = 2;
            
            // Fungsi untuk membuat semua card sejajar
            function equalizeCheckoutCardHeights() {
                if (cards.length === 0) return;
                
                cards.forEach(card => {
                    card.style.height = 'auto';
                });
                
                if (cardsPerViewCheckout > 1) {
                    const totalSlides = Math.ceil(totalCards / cardsPerViewCheckout);
                    
                    for (let slideIndex = 0; slideIndex < totalSlides; slideIndex++) {
                        const startIdx = slideIndex * cardsPerViewCheckout;
                        const endIdx = startIdx + cardsPerViewCheckout;
                        const slideCards = Array.from(cards).slice(startIdx, endIdx);
                        
                        if (slideCards.length === 0) continue;
                        
                        let maxHeight = 0;
                        slideCards.forEach(card => {
                            const cardHeight = card.offsetHeight;
                            if (cardHeight > maxHeight) maxHeight = cardHeight;
                        });
                        
                        slideCards.forEach(card => {
                            card.style.height = `${maxHeight}px`;
                        });
                    }
                }
            }
            
            // Fungsi untuk menghitung cards per view
            function calculateCheckoutCardsPerView() {
                return window.innerWidth <= 768 ? 1 : 2;
            }
            
            // Fungsi untuk menghitung total slides
            function calculateCheckoutTotalSlides() {
                cardsPerViewCheckout = calculateCheckoutCardsPerView();
                return Math.ceil(totalCards / cardsPerViewCheckout);
            }
            
            let totalSlidesCheckout = calculateCheckoutTotalSlides();
            
            // Fungsi untuk membuat dots indicator
            function createCheckoutDots() {
                dotsContainer.innerHTML = '';
                totalSlidesCheckout = calculateCheckoutTotalSlides();
                
                if (totalSlidesCheckout <= 1) {
                    dotsContainer.classList.add('hidden');
                } else {
                    dotsContainer.classList.remove('hidden');
                    
                    for (let i = 0; i < totalSlidesCheckout; i++) {
                        const dot = document.createElement('div');
                        dot.classList.add('checkout-dot');
                        if (i === currentSlideCheckout) dot.classList.add('active');
                        dot.setAttribute('data-slide', i);
                        dot.addEventListener('click', () => goToCheckoutSlide(i));
                        dotsContainer.appendChild(dot);
                    }
                }
            }
            
            // Fungsi untuk update slider position
            function updateCheckoutSliderPosition() {
                const cardWidth = cards[0].offsetWidth + 30;
                const translateX = currentSlideCheckout * cardsPerViewCheckout * cardWidth;
                sliderTrack.style.transform = `translateX(-${translateX}px)`;
                
                const dots = document.querySelectorAll('.checkout-dot');
                dots.forEach((dot, index) => {
                    dot.classList.toggle('active', index === currentSlideCheckout);
                });
                
                updateCheckoutNavigationButtons();
            }
            
            // Fungsi untuk update navigation buttons
            function updateCheckoutNavigationButtons() {
                if (totalSlidesCheckout <= 1) {
                    prevBtn.classList.add('hidden');
                    nextBtn.classList.add('hidden');
                    return;
                }
                
                prevBtn.classList.remove('hidden');
                nextBtn.classList.remove('hidden');
                
                prevBtn.disabled = currentSlideCheckout === 0;
                nextBtn.disabled = currentSlideCheckout === totalSlidesCheckout - 1;
            }
            
            // Fungsi untuk pindah slide
            function goToCheckoutSlide(slideIndex) {
                if (slideIndex < 0 || slideIndex >= totalSlidesCheckout) return;
                
                sliderTrack.style.transition = 'transform 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
                
                currentSlideCheckout = slideIndex;
                updateCheckoutSliderPosition();
            }
            
            // Fungsi untuk next slide
            function nextCheckoutSlide() {
                if (currentSlideCheckout < totalSlidesCheckout - 1) {
                    currentSlideCheckout++;
                    updateCheckoutSliderPosition();
                }
            }
            
            // Fungsi untuk prev slide
            function prevCheckoutSlide() {
                if (currentSlideCheckout > 0) {
                    currentSlideCheckout--;
                    updateCheckoutSliderPosition();
                }
            }
            
            // Event listeners untuk navigation buttons
            prevBtn.addEventListener('click', prevCheckoutSlide);
            nextBtn.addEventListener('click', nextCheckoutSlide);
            
            // Keyboard navigation
            document.addEventListener('keydown', (e) => {
                if (e.key === 'ArrowLeft' && e.target === document.body) {
                    prevCheckoutSlide();
                } else if (e.key === 'ArrowRight' && e.target === document.body) {
                    nextCheckoutSlide();
                }
            });
            
            // Touch/swipe untuk mobile
            let touchStartXCheckout = 0;
            let touchEndXCheckout = 0;
            
            sliderTrack.addEventListener('touchstart', (e) => {
                touchStartXCheckout = e.changedTouches[0].clientX;
            });
            
            sliderTrack.addEventListener('touchend', (e) => {
                touchEndXCheckout = e.changedTouches[0].clientX;
                const swipeThreshold = 50;
                const diff = touchStartXCheckout - touchEndXCheckout;
                
                if (Math.abs(diff) > swipeThreshold) {
                    if (diff > 0) {
                        nextCheckoutSlide();
                    } else {
                        prevCheckoutSlide();
                    }
                }
            });
            
            // Handle window resize
            let resizeTimeoutCheckout;
            window.addEventListener('resize', () => {
                clearTimeout(resizeTimeoutCheckout);
                resizeTimeoutCheckout = setTimeout(() => {
                    cardsPerViewCheckout = calculateCheckoutCardsPerView();
                    totalSlidesCheckout = calculateCheckoutTotalSlides();
                    
                    if (currentSlideCheckout >= totalSlidesCheckout) {
                        currentSlideCheckout = totalSlidesCheckout - 1;
                    }
                    
                    createCheckoutDots();
                    updateCheckoutSliderPosition();
                    updateCheckoutNavigationButtons();
                    
                    setTimeout(equalizeCheckoutCardHeights, 100);
                }, 250);
            });
            
            // Inisialisasi
            cardsPerViewCheckout = calculateCheckoutCardsPerView();
            totalSlidesCheckout = calculateCheckoutTotalSlides();
            createCheckoutDots();
            updateCheckoutNavigationButtons();
            updateCheckoutSliderPosition();
            
            // Samakan tinggi card setelah semua gambar dimuat
            const images = document.querySelectorAll('.header-card-checkout img');
            let loadedCheckoutImages = 0;
            
            function checkAllCheckoutImagesLoaded() {
                loadedCheckoutImages++;
                if (loadedCheckoutImages === images.length) {
                    setTimeout(() => {
                        equalizeCheckoutCardHeights();
                    }, 100);
                }
            }
            
            images.forEach(img => {
                if (img.complete) {
                    checkAllCheckoutImagesLoaded();
                } else {
                    img.addEventListener('load', checkAllCheckoutImagesLoaded);
                    img.addEventListener('error', checkAllCheckoutImagesLoaded);
                }
            });
            
            if (images.length === 0) {
                setTimeout(equalizeCheckoutCardHeights, 100);
            }
            
            // Tambahkan efek klik untuk tombol checkout
            document.querySelectorAll('.btn-checkout').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    this.style.transform = 'scale(0.95)';
                    this.style.transition = 'transform 0.2s ease';
                    
                    setTimeout(() => {
                        this.style.transform = '';
                        
                        const productName = this.closest('.content-checkout').querySelector('.nama-checkout').textContent;
                        const harga = this.closest('.content-checkout').querySelector('.harga-checkout').textContent;
                        const badge = this.closest('.content-checkout').querySelector('.badge-checkout').textContent;
                        
                        // Simulasi proses checkout dengan tema iBox
                        const modalHTML = `
                            <div style="position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);display:flex;align-items:center;justify-content:center;z-index:1000;">
                                <div style="background:white;border-radius:16px;padding:30px;max-width:400px;width:90%;box-shadow:0 10px 30px rgba(0,0,0,0.15);">
                                    <div style="text-align:center;margin-bottom:20px;">
                                        <div style="width:60px;height:60px;background:linear-gradient(135deg, #007aff, #0056d6);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 15px;">
                                            <i class="bi bi-bag-check" style="color:white;font-size:24px;"></i>
                                        </div>
                                        <h3 style="color:#1d1d1f;margin-bottom:10px;">Checkout Berhasil!</h3>
                                        <p style="color:#86868b;font-size:14px;">Produk telah ditambahkan ke keranjang belanja</p>
                                    </div>
                                    <div style="background:#f8f9fa;border-radius:12px;padding:15px;margin-bottom:20px;">
                                        <p style="font-weight:600;color:#1d1d1f;margin-bottom:5px;">${productName}</p>
                                        <p style="color:#007aff;font-weight:700;margin-bottom:5px;">${harga}</p>
                                        <span style="display:inline-block;background:rgba(0,122,255,0.1);color:#007aff;padding:4px 10px;border-radius:12px;font-size:12px;">${badge}</span>
                                    </div>
                                    <div style="display:flex;gap:10px;">
                                        <button onclick="this.closest('div[style*=\"position:fixed\"]').remove()" style="flex:1;padding:12px;background:#f5f5f7;border:none;border-radius:12px;color:#1d1d1f;font-weight:600;cursor:pointer;">Lanjut Belanja</button>
                                        <button onclick="alert('Proses pembayaran akan dilakukan...');this.closest('div[style*=\"position:fixed\"]').remove()" style="flex:1;padding:12px;background:linear-gradient(135deg, #007aff, #0056d6);border:none;border-radius:12px;color:white;font-weight:600;cursor:pointer;">Bayar Sekarang</button>
                                    </div>
                                </div>
                            </div>
                        `;
                        
                        const modal = document.createElement('div');
                        modal.innerHTML = modalHTML;
                        document.body.appendChild(modal.firstElementChild);
                    }, 200);
                });
            });
        });
    </script>
</body>
</html>