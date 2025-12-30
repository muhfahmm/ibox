<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AppleCare Protection</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

</head>

<body>
    <div class="container-about-apple-care">
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
                font-family: 'Poppins', system-ui, sans-serif;
            }

            body {
                background: #f8f9fa;
                color: #1d1d1f;
            }

            /* CONTAINER UTAMA */
            .container-about-apple-care {
                max-width: 1400px;
                margin: 80px auto 60px;
                padding: 0 20px;
            }

            /* JUDUL YANG LEBIH RAPI */
            .apple-care-header {
                text-align: center;
                padding-bottom: 25px;
                position: relative;
            }

            .apple-care-header::after {
                content: '';
                position: absolute;
                bottom: 0;
                left: 50%;
                transform: translateX(-50%);
                width: 120px;
                height: 4px;
                background: linear-gradient(90deg, #007aff, #0056d6);
                border-radius: 2px;
            }

            .apple-care-main-title {
                font-size: 2.8rem;
                font-weight: 700;
                color: #1d1d1f;
                margin-bottom: 15px;
                letter-spacing: -0.5px;
                line-height: 1.2;
            }

            .apple-care-subtitle {
                font-size: 1.2rem;
                color: #86868b;
                max-width: 600px;
                margin: 0 auto;
                font-weight: 400;
                line-height: 1.6;
            }

            /* GRID CARDS */
            .cards-apple-care {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 30px;
                padding: 20px 0;
            }

            /* CARD STYLE */
            .card-apple-care {
                background: white;
                border-radius: 16px;
                padding: 25px;
                box-shadow:
                    0 8px 30px rgba(0, 0, 0, 0.08),
                    0 2px 8px rgba(0, 0, 0, 0.03);
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                border: 1px solid rgba(0, 0, 0, 0.05);
                position: relative;
                overflow: hidden;
            }

            .card-apple-care:hover {
                transform: translateY(-8px);
                box-shadow:
                    0 15px 35px rgba(0, 122, 255, 0.1),
                    0 5px 15px rgba(0, 0, 0, 0.05);
                border-color: rgba(0, 122, 255, 0.15);
            }

            /* EFEK TOP BORDER PADA HOVER */
            .card-apple-care::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 4px;
                background: linear-gradient(90deg, #007aff, #0056d6);
                transform: scaleX(0);
                transition: transform 0.3s ease;
                transform-origin: left;
            }

            .card-apple-care:hover::before {
                transform: scaleX(1);
            }

            /* GAMBAR CARD */
            .card-apple-care img {
                width: 100%;
                height: 200px;
                object-fit: cover;
                border-radius: 12px;
                margin-bottom: 20px;
                transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .card-apple-care:hover img {
                transform: scale(1.03);
            }

            /* JUDUL CARD */
            .card-apple-care h3 {
                color: #1d1d1f;
                font-size: 1.5rem;
                font-weight: 700;
                margin-bottom: 15px;
                line-height: 1.3;
            }

            /* FITUR CARD */
            .applecare-feature {
                color: #515154;
                font-size: 0.95rem;
                margin: 12px 0;
                padding-left: 28px;
                position: relative;
                line-height: 1.5;
            }

            .applecare-feature::before {
                content: "✓";
                position: absolute;
                left: 0;
                top: 0;
                color: #007aff;
                font-weight: bold;
                background: rgba(0, 122, 255, 0.1);
                width: 22px;
                height: 22px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 0.85rem;
            }

            /* BADGE HARGA */
            .applecare-price {
                display: inline-block;
                background: rgba(0, 122, 255, 0.1);
                color: #007aff;
                padding: 8px 16px;
                border-radius: 20px;
                font-size: 0.9rem;
                font-weight: 600;
                margin-top: 20px;
                letter-spacing: 0.5px;
                border: 1px solid rgba(0, 122, 255, 0.2);
            }

            /* RESPONSIVE DESIGN */
            /* Desktop Lebar */
            @media (min-width: 1400px) {
                .apple-care-main-title {
                    font-size: 3rem;
                }

                .apple-care-subtitle {
                    font-size: 1.25rem;
                }

                .card-apple-care {
                    padding: 28px;
                }

                .card-apple-care img {
                    height: 220px;
                }

                .card-apple-care h3 {
                    font-size: 1.6rem;
                }
            }

            /* Tablet */
            @media (max-width: 1024px) {
                .cards-apple-care {
                    grid-template-columns: repeat(2, 1fr);
                    gap: 25px;
                }

                .container-about-apple-care {
                    margin: 60px auto 40px;
                }

                .apple-care-header {
                    padding-bottom: 22px;
                }

                .apple-care-main-title {
                    font-size: 2.3rem;
                }

                .apple-care-subtitle {
                    font-size: 1.1rem;
                    padding: 0 20px;
                }

                .card-apple-care {
                    padding: 22px;
                }

                .card-apple-care img {
                    height: 180px;
                }

                .card-apple-care h3 {
                    font-size: 1.4rem;
                }
            }

            /* Mobile */
            @media (max-width: 768px) {
                .cards-apple-care {
                    grid-template-columns: 1fr;
                    gap: 25px;
                    max-width: 500px;
                    margin: 0 auto;
                }

                .container-about-apple-care {
                    margin: 50px auto 30px;
                    padding: 0 15px;
                }

                .apple-care-header {
                    padding-bottom: 20px;
                }

                .apple-care-main-title {
                    font-size: 2rem;
                    margin-bottom: 12px;
                }

                .apple-care-subtitle {
                    font-size: 1rem;
                    padding: 0 10px;
                }

                .apple-care-header::after {
                    width: 100px;
                    height: 3px;
                }

                .card-apple-care {
                    padding: 20px;
                    border-radius: 14px;
                }

                .card-apple-care img {
                    height: 160px;
                }

                .card-apple-care h3 {
                    font-size: 1.3rem;
                }

                .applecare-feature {
                    font-size: 0.9rem;
                }
            }

            /* Small Mobile */
            @media (max-width: 480px) {
                .container-about-apple-care {
                    margin: 40px auto 20px;
                    padding: 0 10px;
                }

                .apple-care-main-title {
                    font-size: 1.7rem;
                }

                .apple-care-subtitle {
                    font-size: 0.95rem;
                }

                .card-apple-care {
                    padding: 18px;
                }

                .card-apple-care img {
                    height: 150px;
                }

                .card-apple-care h3 {
                    font-size: 1.2rem;
                }

                .applecare-feature {
                    font-size: 0.85rem;
                }
            }

            /* Animasi untuk cards */
            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .card-apple-care {
                animation: fadeInUp 0.6s ease forwards;
                opacity: 0;
            }

            .card-apple-care:nth-child(1) {
                animation-delay: 0.1s;
            }

            .card-apple-care:nth-child(2) {
                animation-delay: 0.2s;
            }

            .card-apple-care:nth-child(3) {
                animation-delay: 0.3s;
            }
        </style>
        <!-- HEADER SECTION YANG RAPI -->
        <div class="apple-care-header">
            <h1 class="apple-care-main-title">AppleCare+</h1>
        </div>

        <!-- GRID CARDS -->
        <div class="cards-apple-care">
            <!-- Card 1 - MacBook Air -->
            <div class="card-apple-care">
                <img src="https://esmeralda.cygnuss-district8.com/media/wysiwyg/ibox-v4/images/applecare/applecare-macbook-air.png" alt="MacBook Air">
                <h3>MacBook Air</h3>
                <p class="applecare-feature">Perlindungan hingga 500,000 kerusakan</p>
                <p class="applecare-feature">Garansi 3 tahun untuk hardware</p>
                <p class="applecare-feature">Coverage untuk accidental damage</p>
                <p class="applecare-feature">Dukungan teknis prioritas</p>
                <span class="applecare-price">Mulai Rp 1.499.000/tahun</span>
            </div>

            <!-- Card 2 - MacBook Pro -->
            <div class="card-apple-care">
                <img src="https://images.unsplash.com/photo-1517336714731-489689fd1ca8?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="MacBook Pro">
                <h3>MacBook Pro</h3>
                <p class="applecare-feature">Perlindungan hingga 600,000 kerusakan</p>
                <p class="applecare-feature">Garansi 4 tahun untuk hardware</p>
                <p class="applecare-feature">Coverage untuk accidental damage</p>
                <p class="applecare-feature">Battery service coverage</p>
                <span class="applecare-price">Mulai Rp 1.999.000/tahun</span>
            </div>

            <!-- Card 3 - iMac -->
            <div class="card-apple-care">
                <img src="https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="iMac">
                <h3>iMac</h3>
                <p class="applecare-feature">Perlindungan hingga 2,000,000 kerusakan</p>
                <p class="applecare-feature">Garansi 2 tahun untuk hardware</p>
                <p class="applecare-feature">Coverage untuk accidental damage</p>
                <p class="applecare-feature">On-site service available</p>
                <span class="applecare-price">Mulai Rp 2.499.000/tahun</span>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Animasi untuk header saat scroll
                const header = document.querySelector('.apple-care-header');

                // Intersection Observer untuk animasi header
                const observerOptions = {
                    threshold: 0.2,
                    rootMargin: '0px 0px -50px 0px'
                };

                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            header.style.opacity = '1';
                            header.style.transform = 'translateY(0)';
                        }
                    });
                }, observerOptions);

                // Set initial state
                header.style.opacity = '0';
                header.style.transform = 'translateY(-20px)';
                header.style.transition = 'opacity 0.8s ease, transform 0.8s ease';

                observer.observe(header);

                // Efek hover dan klik pada cards
                const cards = document.querySelectorAll('.card-apple-care');

                cards.forEach(card => {
                    // Efek hover
                    card.addEventListener('mouseenter', function() {
                        this.style.transform = 'translateY(-8px)';
                    });

                    card.addEventListener('mouseleave', function() {
                        if (!this.classList.contains('clicked')) {
                            this.style.transform = 'translateY(0)';
                        }
                    });

                    // Efek klik
                    card.addEventListener('click', function() {
                        const productName = this.querySelector('h3').textContent;
                        const productPrice = this.querySelector('.applecare-price').textContent;

                        // Animasi klik
                        this.style.transform = 'scale(0.98)';
                        this.style.transition = 'transform 0.2s ease';

                        setTimeout(() => {
                            this.style.transform = 'translateY(-8px)';

                            // Simulasi modal atau alert
                            showAppleCareModal(productName, productPrice);
                        }, 200);
                    });
                });

                // Fungsi untuk menampilkan modal AppleCare
                function showAppleCareModal(productName, price) {
                    // Buat modal
                    const modal = document.createElement('div');
                    modal.style.cssText = `
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0, 0, 0, 0.7);
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    z-index: 1000;
                    opacity: 0;
                    transition: opacity 0.3s ease;
                `;

                    const modalContent = document.createElement('div');
                    modalContent.style.cssText = `
                    background: white;
                    padding: 30px;
                    border-radius: 16px;
                    max-width: 400px;
                    width: 90%;
                    text-align: center;
                    transform: translateY(-20px);
                    transition: transform 0.3s ease;
                `;

                    modalContent.innerHTML = `
                    <h3 style="color: #1d1d1f; margin-bottom: 15px;">AppleCare+ untuk ${productName}</h3>
                    <p style="color: #515154; margin-bottom: 20px; line-height: 1.6;">
                        ${price}<br>
                        Perlindungan komprehensif termasuk accidental damage, dukungan teknis 24/7, dan garansi hardware.
                    </p>
                    <button class="modal-close-btn" style="
                        background: #007aff;
                        color: white;
                        border: none;
                        padding: 10px 20px;
                        border-radius: 8px;
                        font-weight: 600;
                        cursor: pointer;
                    ">Tutup</button>
                `;

                    modal.appendChild(modalContent);
                    document.body.appendChild(modal);

                    // Animasi masuk
                    setTimeout(() => {
                        modal.style.opacity = '1';
                        modalContent.style.transform = 'translateY(0)';
                    }, 10);

                    // Fungsi tutup modal
                    const closeModal = () => {
                        modal.style.opacity = '0';
                        modalContent.style.transform = 'translateY(-20px)';
                        setTimeout(() => {
                            document.body.removeChild(modal);
                        }, 300);
                    };

                    // Event listener untuk tombol tutup
                    modalContent.querySelector('.modal-close-btn').addEventListener('click', closeModal);

                    // Tutup modal saat klik di luar
                    modal.addEventListener('click', function(e) {
                        if (e.target === modal) {
                            closeModal();
                        }
                    });

                    // Tutup modal dengan ESC
                    document.addEventListener('keydown', function(e) {
                        if (e.key === 'Escape') {
                            closeModal();
                        }
                    });
                }

                // Animasi cards saat scroll
                const cardObserver = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.style.opacity = '1';
                            entry.target.style.transform = 'translateY(0)';
                        }
                    });
                }, {
                    threshold: 0.1,
                    rootMargin: '0px 0px -50px 0px'
                });

                cards.forEach(card => {
                    cardObserver.observe(card);
                });
            });
        </script>
    </div>


</body>

</html>