<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Layanan Lengkap untuk Anda | iBox</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: #f8f9fa;
            color: #333;
            line-height: 1.6;
        }

        /* CONTAINER UTAMA */
        .layanan-lengkap-untuk-anda-container {
            width: 100%;
            max-width: 1400px;
            margin: 0 auto;
        }

        /* SERVICES SECTION - GRID untuk desktop, FLEX untuk mobile */
        .layanan-lengkap-untuk-anda-services {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            align-items: stretch;
            /* Pastikan semua card sama tinggi */
        }

        /* SERVICE CARD */
        .layanan-lengkap-untuk-anda-card {
            background-color: white;
            border-radius: 16px;
            padding: 40px 35px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border: 1px solid #eaeaea;
            display: flex;
            flex-direction: column;
            height: 100%;
            /* Pastikan semua card sama tinggi */
        }

        .layanan-lengkap-untuk-anda-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0, 122, 255, 0.15);
            border-color: #007aff;
        }

        /* Title Styles - SAMA untuk semua card */
        .layanan-lengkap-untuk-anda-card h2 {
            font-size: 28px;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 20px;
            line-height: 1.3;
            min-height: 68px;
            /* Pastikan semua judul sama tinggi */
            display: flex;
            align-items: center;
        }

        /* Content Styles */
        .layanan-lengkap-untuk-anda-card p {
            font-size: 16.5px;
            color: #555;
            margin-bottom: 30px;
            line-height: 1.7;
            flex-grow: 1;
            /* Konten tumbuh sama rata */
        }

        /* Link Styles */
        .layanan-lengkap-untuk-anda-link {
            display: inline-flex;
            align-items: center;
            color: #007aff;
            text-decoration: none;
            font-weight: 600;
            font-size: 16.5px;
            transition: all 0.2s ease;
            padding: 10px 0;
            border-top: 1px solid #eee;
            margin-top: auto;
            /* Dorong link ke bawah */
            padding-top: 20px;
        }

        .layanan-lengkap-untuk-anda-link:hover {
            color: #0056cc;
        }

        .layanan-lengkap-untuk-anda-link::after {
            content: "→";
            margin-left: 8px;
            font-weight: 700;
            font-size: 18px;
            transition: transform 0.2s ease;
        }

        .layanan-lengkap-untuk-anda-link:hover::after {
            transform: translateX(5px);
        }

        /* HR Styles */
        .layanan-lengkap-untuk-anda-card hr {
            border: none;
            height: 2px;
            background: linear-gradient(90deg, #007aff, transparent);
            margin: 25px 0;
            opacity: 0.3;
        }

        /* RESPONSIVE DESIGN */
        /* Desktop Sedang */
        @media (max-width: 1200px) {
            .layanan-lengkap-untuk-anda-services {
                gap: 25px;
            }

            .layanan-lengkap-untuk-anda-card {
                padding: 35px 30px;
            }

            .layanan-lengkap-untuk-anda-card h2 {
                font-size: 26px;
                min-height: 62px;
            }
        }

        /* Tablet - Tetap 3 kolom sejajar */
        @media (max-width: 992px) {

            .layanan-lengkap-untuk-anda-container {
                max-width: 100%;
            }

            .layanan-lengkap-untuk-anda-services {
                gap: 20px;
            }

            .layanan-lengkap-untuk-anda-card {
                padding: 30px 25px;
            }

            .layanan-lengkap-untuk-anda-card h2 {
                font-size: 24px;
                min-height: 58px;
            }

            .layanan-lengkap-untuk-anda-card p {
                font-size: 16px;
            }
        }

        /* Tablet Kecil - MASIH 3 KOLOM */
        @media (max-width: 768px) {

            .layanan-lengkap-untuk-anda-services {
                grid-template-columns: repeat(3, 1fr);
                gap: 15px;
            }

            .layanan-lengkap-untuk-anda-card {
                padding: 25px 20px;
            }

            .layanan-lengkap-untuk-anda-card h2 {
                font-size: 22px;
                min-height: 52px;
                margin-bottom: 15px;
            }

            .layanan-lengkap-untuk-anda-card p {
                font-size: 15.5px;
                margin-bottom: 20px;
            }

            .layanan-lengkap-untuk-anda-link {
                font-size: 16px;
                padding-top: 15px;
            }
        }

        /* MOBILE - BERUBAH MENJADI COLUMN (768px ke bawah) */
        @media (max-width: 767px) {
            .layanan-lengkap-untuk-anda-services {
                display: flex !important;
                flex-direction: column;
                gap: 20px;
                grid-template-columns: 1fr !important;
            }

            .layanan-lengkap-untuk-anda-card {
                width: 100% !important;
                flex: 1 1 auto !important;
                min-height: auto !important;
                padding: 30px 25px !important;
                margin-bottom: 0 !important;
            }

            .layanan-lengkap-untuk-anda-card h2 {
                font-size: 24px !important;
                min-height: auto !important;
                margin-bottom: 15px !important;
                align-items: flex-start !important;
            }

            .layanan-lengkap-untuk-anda-card p {
                font-size: 16px !important;
                margin-bottom: 20px !important;
            }

            .layanan-lengkap-untuk-anda-link {
                font-size: 16.5px !important;
                padding-top: 20px !important;
            }

            .layanan-lengkap-untuk-anda-card hr {
                margin: 20px 0 !important;
            }
        }

        /* Mobile Sedang */
        @media (max-width: 576px) {
            .layanan-lengkap-untuk-anda-services {
                gap: 18px;
            }

            .layanan-lengkap-untuk-anda-card {
                padding: 25px 20px !important;
            }

            .layanan-lengkap-untuk-anda-card h2 {
                font-size: 22px !important;
                margin-bottom: 12px !important;
            }

            .layanan-lengkap-untuk-anda-card p {
                font-size: 15.5px !important;
                margin-bottom: 18px !important;
            }

            .layanan-lengkap-untuk-anda-link {
                font-size: 16px !important;
                padding-top: 18px !important;
            }

            .layanan-lengkap-untuk-anda-card hr {
                margin: 18px 0 !important;
            }
        }

        /* Mobile Kecil */
        @media (max-width: 480px) {
            .layanan-lengkap-untuk-anda-services {
                gap: 15px;
            }

            .layanan-lengkap-untuk-anda-card {
                padding: 22px 18px !important;
            }

            .layanan-lengkap-untuk-anda-card h2 {
                font-size: 20px !important;
                margin-bottom: 10px !important;
            }

            .layanan-lengkap-untuk-anda-card p {
                font-size: 15px !important;
                margin-bottom: 15px !important;
            }

            .layanan-lengkap-untuk-anda-link {
                font-size: 15.5px !important;
                padding-top: 15px !important;
            }

            .layanan-lengkap-untuk-anda-card hr {
                margin: 15px 0 !important;
            }
        }

        /* Mobile Sangat Kecil */
        @media (max-width: 360px) {

            .layanan-lengkap-untuk-anda-services {
                gap: 12px;
            }

            .layanan-lengkap-untuk-anda-card {
                padding: 20px 16px !important;
            }

            .layanan-lengkap-untuk-anda-card h2 {
                font-size: 19px !important;
            }

            .layanan-lengkap-untuk-anda-card p {
                font-size: 14.5px !important;
            }

            .layanan-lengkap-untuk-anda-link {
                font-size: 15px !important;
            }
        }

        /* Desktop Lebar */
        @media (min-width: 1400px) {
            .layanan-lengkap-untuk-anda-services {
                gap: 40px;
            }

            .layanan-lengkap-untuk-anda-card {
                padding: 45px 40px;
            }

            .layanan-lengkap-untuk-anda-card h2 {
                font-size: 30px;
                min-height: 72px;
            }

            .layanan-lengkap-untuk-anda-card p {
                font-size: 17px;
            }
        }
    </style>
</head>

<body>
    <div class="layanan-lengkap-untuk-anda-container">
        <!-- SERVICES - DESKTOP: 3 CARDS SEJAJAR, MOBILE: COLUMN -->
        <div class="layanan-lengkap-untuk-anda-services">
            <!-- Service 1 -->
            <article class="layanan-lengkap-untuk-anda-card">
                <h2>Dapatkan servis dan bantuan profesional</h2>
                <p>Mulai dari pengaturan device terbaru-mu hingga servis, dapatkan pengalaman terbaiknya dengan tim ahli kami yang siap membantu.</p>
                <hr>
                <a href="#" class="layanan-lengkap-untuk-anda-link">Lebih lanjut</a>
            </article>

            <!-- Service 2 -->
            <article class="layanan-lengkap-untuk-anda-card">
                <h2>Simulasi kredit dan cicilan</h2>
                <p>Berbagai pilihan pembayaran kredit dan cicilan yang fleksibel. Temukan semua pilihanmu dengan kemudahan dan transparansi.</p>
                <hr>
                <a href="#" class="layanan-lengkap-untuk-anda-link">Lebih lanjut</a>
            </article>

            <!-- Service 3 -->
            <article class="layanan-lengkap-untuk-anda-card">
                <h2>Beli online, ambil di toko</h2>
                <p>Belanja online dengan mudah dan bebas biaya kirim. Pesan sekarang dan ambil produkmu di toko iBox terdekat kapan saja.</p>
                <hr>
                <a href="#" class="layanan-lengkap-untuk-anda-link">Lebih lanjut</a>
            </article>
        </div>
    </div>

    <script>
        // Efek hover untuk kartu layanan
        document.addEventListener('DOMContentLoaded', function() {
            const serviceCards = document.querySelectorAll('.layanan-lengkap-untuk-anda-card');
            const serviceLinks = document.querySelectorAll('.layanan-lengkap-untuk-anda-link');

            // Efek hover card (hanya di desktop)
            serviceCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    if (window.innerWidth > 767) {
                        this.style.transform = 'translateY(-10px)';
                    }
                });

                card.addEventListener('mouseleave', function() {
                    if (window.innerWidth > 767) {
                        this.style.transform = 'translateY(0)';
                    }
                });
            });

            // Efek klik pada link "Lebih lanjut"
            serviceLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const cardTitle = this.closest('.layanan-lengkap-untuk-anda-card').querySelector('h2').textContent;
                    alert(`Anda mengklik "${cardTitle}"\nFitur ini akan membawa Anda ke halaman detail layanan.`);
                });
            });

            // Fungsi untuk mereset height card (hanya di desktop)
            function resetCardHeights() {
                const cards = document.querySelectorAll('.layanan-lengkap-untuk-anda-card');

                if (window.innerWidth > 767) {
                    // Desktop: semua card sama tinggi
                    let maxHeight = 0;

                    // Reset heights dulu
                    cards.forEach(card => {
                        card.style.height = 'auto';
                    });

                    // Cari height tertinggi
                    cards.forEach(card => {
                        const height = card.offsetHeight;
                        if (height > maxHeight) {
                            maxHeight = height;
                        }
                    });

                    // Terapkan height yang sama
                    cards.forEach(card => {
                        card.style.height = maxHeight + 'px';
                    });
                } else {
                    // Mobile: height auto (natural height)
                    cards.forEach(card => {
                        card.style.height = 'auto';
                    });
                }
            }

            // Jalankan saat load dan resize
            window.addEventListener('load', resetCardHeights);
            window.addEventListener('resize', resetCardHeights);
        });
    </script>
</body>

</html>