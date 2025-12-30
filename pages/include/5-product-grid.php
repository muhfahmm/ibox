<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Grid Produk Apple</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

</head>

<body>
    <div class="container-product">
        <style>
            /* Variabel Warna Apple */
            :root {
                --apple-blue: #0071e3;
                --apple-blue-hover: #0077ed;
                --apple-gray-bg: #f5f5f7;
                --apple-gray-light: #f5f5f7;
                --apple-gray-text: #86868b;
                --apple-dark-text: #1d1d1f;
                --apple-new-badge: #bf4800;
                --apple-card-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
                --apple-card-shadow-hover: 0 8px 30px rgba(0, 0, 0, 0.12);
                --apple-border-color: rgba(0, 0, 0, 0.1);
            }

            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                background-color: var(--apple-gray-bg);
                color: var(--apple-dark-text);
                line-height: 1.5;
            }

            .container-product {
                width: 100%;
                max-width: 1400px;
                margin: 0 auto;
            }

            .grid-main {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 24px;
            }

            .grid-side {
                display: grid;
                grid-template-rows: 1fr 1fr;
                gap: 24px;
            }

            /* Styling Kartu */
            .card {
                background-color: #ffffff;
                border-radius: 20px;
                padding: 32px;
                transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.1);
                position: relative;
                overflow: hidden;
                border: 1px solid var(--apple-border-color);
                box-shadow: var(--apple-card-shadow);
            }

            /* Efek hover default pada kartu */
            .card:hover {
                transform: translateY(-6px);
                box-shadow: var(--apple-card-shadow-hover);
            }

            /* Efek khusus saat hover button */
            .button-hover-active {
                transform: translateY(-8px) scale(1.02);
                box-shadow: 0 12px 40px rgba(0, 113, 227, 0.18);
                border-color: var(--apple-blue);
            }

            .card-large {
                text-align: center;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
            }

            .card-horizontal {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 24px;
            }

            /* Badge */
            .badge {
                color: var(--apple-new-badge);
                font-size: 12px;
                font-weight: 600;
                letter-spacing: 0.3px;
                display: block;
                margin-bottom: 2px;
                text-transform: uppercase;
            }

            /* Judul */
            .title {
                font-weight: 700;
                line-height: 1.1;
                margin: 2px 0;
            }

            .card-large .title {
                font-size: 28px;
            }

            .card-horizontal .title {
                font-size: 24px;
            }

            /* Teks */
            .text {
                color: var(--apple-dark-text);
                margin: 2px 0 4px 0;
            }

            .card-large .text {
                font-size: 19px;
                font-weight: 500;
                line-height: 1.3;
            }

            .card-horizontal .text {
                font-size: 16px;
            }

            /* Harga dalam teks */
            .price-text {
                font-size: 17px;
                color: var(--apple-dark-text);
                margin: 4px 0 8px 0;
            }

            .price-start {
                color: var(--apple-gray-text);
                font-weight: 400;
            }

            /* BUTTON KECIL SAMA UNTUK SEMUA */
            .button {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 6px;
                /* DIPERKECIL */
                background-color: var(--apple-blue);
                color: #ffffff;
                padding: 8px 16px;
                /* DIPERKECIL: dari 10px 22px */
                border-radius: 980px;
                text-decoration: none;
                font-size: 14px;
                /* DIPERKECIL: dari 15px */
                font-weight: 500;
                border: none;
                cursor: pointer;
                transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
                position: relative;
                overflow: hidden;
                z-index: 2;
                margin-top: 4px;
                /* Tambahkan ini agar button tidak terlalu panjang */
                width: auto;
                min-width: 120px;
                /* Lebar minimum yang sama untuk semua */
            }

            .button:hover {
                background-color: var(--apple-blue-hover);
                transform: scale(1.05);
                box-shadow: 0 4px 15px rgba(0, 113, 227, 0.25);
                /* DIPERKECIL */
            }

            /* Efek ripple pada button */
            .button::after {
                content: '';
                position: absolute;
                top: 50%;
                left: 50%;
                width: 0;
                height: 0;
                border-radius: 50%;
                background-color: rgba(255, 255, 255, 0.3);
                transform: translate(-50%, -50%);
                transition: width 0.6s, height 0.6s;
                z-index: -1;
            }

            .button:hover::after {
                width: 200px;
                /* DIPERKECIL: dari 300px */
                height: 200px;
                /* DIPERKECIL: dari 300px */
            }

            /* Gambar */
            .image-large {
                margin-top: 16px;
                height: 320px;
                width: auto;
                max-width: 100%;
                border-radius: 12px;
                transition: transform 0.5s ease;
                object-fit: contain;
            }

            .card:hover .image-large {
                transform: scale(1.05);
            }

            .image-small {
                width: 160px;
                height: 160px;
                object-fit: cover;
                border-radius: 10px;
                transition: transform 0.5s ease;
                flex-shrink: 0;
            }

            .card-horizontal:hover .image-small {
                transform: scale(1.08);
            }

            .content-right {
                text-align: right;
                max-width: 260px;
                flex-grow: 1;
                display: flex;
                flex-direction: column;
                align-items: flex-end;
                /* Agar button sejajar kanan */
            }

            /* Untuk button di card-large (iPhone) */
            .card-large .button-container {
                display: flex;
                justify-content: center;
                width: 100%;
            }

            /* Efek ikon pada button saat hover */
            .button i {
                transition: transform 0.3s ease;
                opacity: 0;
                transform: translateX(-8px);
                /* DIPERKECIL */
                font-size: 12px;
                /* DIPERKECIL */
            }

            .button:hover i {
                opacity: 1;
                transform: translateX(0);
            }

            /* Responsif */
            @media (max-width: 900px) {
                .grid-main {
                    grid-template-columns: 1fr;
                }

                .grid-side {
                    grid-template-rows: auto;
                }

                .card-horizontal {
                    flex-direction: column;
                    text-align: center;
                }

                .content-right {
                    text-align: center;
                    max-width: 100%;
                    align-items: center;
                    /* Di mobile, button di tengah */
                }

                .card-large .title {
                    font-size: 24px;
                }

                .card-horizontal .title {
                    font-size: 20px;
                }

                .image-large {
                    height: 250px;
                }

                .button {
                    min-width: 110px;
                    /* Sedikit lebih kecil di mobile */
                }
            }

            /* Animasi saat halaman dimuat */
            @keyframes cardAppear {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .card {
                animation: cardAppear 0.6s ease-out forwards;
                opacity: 0;
            }

            .card-large {
                animation-delay: 0.1s;
            }

            .grid-side .card:first-child {
                animation-delay: 0.2s;
            }

            .grid-side .card:last-child {
                animation-delay: 0.3s;
            }
        </style>
        <div class="grid-main">
            <!-- iPhone 17 Pro -->
            <div class="card card-large">
                <span class="badge">NEW</span>
                <h2 class="title">iPhone 17 Pro</h2>
                <p class="text">Pro luar dalam.</p>
                <p class="price-text">
                    <span class="price-start">Mulai </span>Rp23.749.000
                </p>
                <div class="button-container">
                    <a href="#" class="button">
                        <i class="fas fa-shopping-bag"></i>
                        Beli sekarang
                    </a>
                </div>
                <img src="https://images.unsplash.com/photo-1592899677977-9c10ca588bbd?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
                    class="image-large" alt="iPhone">
            </div>

            <!-- iPad & AirPods -->
            <div class="grid-side">
                <!-- iPad Pro M5 -->
                <div class="card card-horizontal">
                    <img src="https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80"
                        class="image-small" alt="iPad">
                    <div class="content-right">
                        <span class="badge">NEW</span>
                        <h3 class="title">iPad Pro M5</h3>
                        <p class="text">Bertenagaaaaa.</p>
                        <p class="price-text">
                            <span class="price-start">Mulai </span>Rp20.499.000
                        </p>
                        <a href="#" class="button">
                            <i class="fas fa-shopping-bag"></i>
                            Beli sekarang
                        </a>
                    </div>
                </div>

                <!-- AirPods Pro 3 -->
                <div class="card card-horizontal">
                    <img src="https://images.unsplash.com/photo-1589003077984-894e133dabab?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80"
                        class="image-small" alt="AirPods">
                    <div class="content-right">
                        <span class="badge">NEW</span>
                        <h3 class="title">AirPods Pro 3</h3>
                        <p class="text">Peredam kebisingan aktif.</p>
                        <p class="price-text">
                            <span class="price-start">Mulai </span>Rp4.499.000
                        </p>
                        <a href="#" class="button">
                            <i class="fas fa-shopping-bag"></i>
                            Beli sekarang
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <script>
            // Menambahkan efek hover yang mempengaruhi seluruh grid
            document.querySelectorAll('.button').forEach(button => {
                button.addEventListener('mouseenter', function() {
                    // Tambah kelas aktif ke semua kartu
                    document.querySelectorAll('.card').forEach(card => {
                        card.classList.add('button-hover-active');

                        // Tambah efek glow khusus
                        card.style.boxShadow = '0 12px 40px rgba(0, 113, 227, 0.18)';
                        card.style.borderColor = 'rgba(0, 113, 227, 0.3)';
                    });
                });

                button.addEventListener('mouseleave', function() {
                    // Hapus kelas aktif dari semua kartu
                    document.querySelectorAll('.card').forEach(card => {
                        card.classList.remove('button-hover-active');

                        // Kembalikan ke semula
                        card.style.boxShadow = '';
                        card.style.borderColor = '';
                    });
                });

                // Efek klik pada tombol
                button.addEventListener('click', function(e) {
                    e.preventDefault();

                    // Ambil nama produk
                    const card = this.closest('.card');
                    const productName = card.querySelector('.title').textContent;

                    // Animasi klik
                    this.style.transform = 'scale(0.95)';
                    this.style.backgroundColor = '#0056b3';

                    // Efek ripple
                    const ripple = document.createElement('span');
                    ripple.style.position = 'absolute';
                    ripple.style.borderRadius = '50%';
                    ripple.style.backgroundColor = 'rgba(255, 255, 255, 0.7)';
                    ripple.style.transform = 'scale(0)';
                    ripple.style.animation = 'ripple 0.6s linear';
                    ripple.style.width = '80px'; /* DIPERKECIL: dari 100px */
                    ripple.style.height = '80px'; /* DIPERKECIL: dari 100px */
                    ripple.style.top = '50%';
                    ripple.style.left = '50%';
                    ripple.style.marginTop = '-40px'; /* DIPERKECIL: dari -50px */
                    ripple.style.marginLeft = '-40px'; /* DIPERKECIL: dari -50px */

                    this.appendChild(ripple);

                    // Hapus ripple setelah animasi selesai
                    setTimeout(() => {
                        ripple.remove();
                    }, 600);

                    // Simulasi pembelian
                    setTimeout(() => {
                        alert(`Terima kasih! Anda akan membeli ${productName}.`);

                        // Reset button
                        this.style.transform = '';
                        this.style.backgroundColor = '';
                    }, 800);
                });
            });

            // Tambah style untuk animasi ripple
            const style = document.createElement('style');
            style.textContent = `
            @keyframes ripple {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }
        `;
            document.head.appendChild(style);
        </script>
    </div>


</body>

</html>