<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Footer iBox</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

</head>

<body>
    <footer class="ibox-footer">
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                background-color: #f9f9f9;
                color: #333;
                overflow-x: hidden;
            }

            .main-content {
                flex: 1;
                padding: 40px 20px;
                max-width: 1200px;
                margin: 0 auto;
                width: 100%;
            }

            h1 {
                color: #1d1d1f;
                margin-bottom: 20px;
                font-weight: 600;
            }

            p {
                color: #515154;
                line-height: 1.5;
                margin-bottom: 20px;
            }

            /* Footer Styles */
            .ibox-footer {
                background-color: #f5f5f7;
                color: #1d1d1f;
                border-top: 1px solid #d2d2d7;
                padding: 40px 0 20px;
                font-size: 12px;
                line-height: 1.33337;
                font-weight: 400;
            }

            .footer-container {
                max-width: 980px;
                margin: 0 auto;
                padding: 0 22px;
            }

            .footer-columns {
                display: flex;
                flex-wrap: wrap;
                margin-bottom: 30px;
            }

            .footer-column {
                flex: 1;
                min-width: 200px;
                margin-bottom: 20px;
                padding-right: 20px;
            }

            .footer-column-title {
                font-weight: 600;
                margin-bottom: 10px;
                color: #1d1d1f;
                font-size: 12px;
            }

            .footer-column ul {
                list-style: none;
            }

            .footer-column li {
                margin-bottom: 8px;
            }

            .footer-column a {
                color: #515154;
                text-decoration: none;
                transition: color 0.2s;
            }

            .footer-column a:hover {
                color: #1d1d1f;
                text-decoration: underline;
            }

            .footer-divider {
                border-top: 1px solid #d2d2d7;
                margin: 20px 0;
            }

            .footer-account {
                margin-bottom: 20px;
            }

            .account-link {
                color: #0066cc;
                text-decoration: none;
                font-weight: 600;
            }

            .account-link:hover {
                text-decoration: underline;
            }

            .footer-partners {
                margin-top: 30px;
            }

            .partners-title {
                font-weight: 600;
                margin-bottom: 15px;
                color: #1d1d1f;
            }

            .partners-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
                gap: 15px;
            }

            .partner-item {
                background-color: #fff;
                border: 1px solid #d2d2d7;
                border-radius: 8px;
                padding: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                height: 60px;
                font-weight: 500;
                color: #1d1d1f;
                text-align: center;
                transition: all 0.2s;
            }

            .partner-item:hover {
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
                transform: translateY(-2px);
            }

            .partner-premium {
                border-left: 4px solid #ff9500;
            }

            .partner-service {
                border-left: 4px solid #007aff;
            }

            .partner-business {
                border-left: 4px solid #34c759;
            }

            .partner-education {
                border-left: 4px solid #af52de;
            }

            .footer-bottom {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-top: 20px;
                padding-top: 20px;
                border-top: 1px solid #d2d2d7;
                color: #86868b;
                font-size: 11px;
            }

            .copyright {
                flex: 1;
            }

            .footer-links {
                display: flex;
                gap: 20px;
            }

            .footer-links a {
                color: #515154;
                text-decoration: none;
            }

            .footer-links a:hover {
                text-decoration: underline;
                color: #1d1d1f;
            }

            .back-to-top {
                background-color: #0071e3;
                color: white;
                border: none;
                border-radius: 20px;
                padding: 8px 16px;
                font-size: 12px;
                cursor: pointer;
                transition: background-color 0.2s;
            }

            .back-to-top:hover {
                background-color: #0056b3;
            }

            /* Responsive */
            @media (max-width: 768px) {
                .footer-columns {
                    flex-direction: column;
                }

                .footer-column {
                    min-width: 100%;
                    padding-right: 0;
                    margin-bottom: 25px;
                }

                .partners-grid {
                    grid-template-columns: repeat(2, 1fr);
                }

                .footer-bottom {
                    flex-direction: column;
                    align-items: flex-start;
                    gap: 15px;
                }

                .footer-links {
                    flex-wrap: wrap;
                    gap: 10px 20px;
                }

                .back-to-top {
                    align-self: flex-end;
                    margin-top: 10px;
                }
            }

            @media (max-width: 480px) {
                .partners-grid {
                    grid-template-columns: 1fr;
                }
            }
        </style>
        <div class="footer-container">
            <div class="footer-columns">
                <div class="footer-column">
                    <div class="footer-column-title">Belanja</div>
                    <ul>
                        <li><a href="#">Mac</a></li>
                        <li><a href="#">iPad</a></li>
                        <li><a href="#">iPhone</a></li>
                        <li><a href="#">Watch</a></li>
                        <li><a href="#">Music</a></li>
                    </ul>
                </div>

                <div class="footer-column">
                    <div class="footer-column-title">Aksesori</div>
                    <ul>
                        <li><a href="#">Layanan</a></li>
                        <li><a href="#">Layanan pelanggan</a></li>
                        <li><a href="#">Bisnis</a></li>
                    </ul>
                </div>

                <div class="footer-column">
                    <div class="footer-column-title">Financing</div>
                    <ul>
                        <li><a href="#">Trade-In</a></li>
                        <li><a href="#">In-Store Classes</a></li>
                        <li><a href="#">AppleCare</a></li>
                    </ul>
                </div>

                <div class="footer-column">
                    <div class="footer-column-title">Tentang iBox</div>
                    <ul>
                        <li><a href="#">Tentang iBox</a></li>
                        <li><a href="#">Hubungi kami</a></li>
                        <li><a href="#">Yang sering ditanyakan</a></li>
                        <li><a href="#">Cari toko</a></li>
                        <li><a href="#">Informasi Keamanan</a></li>
                    </ul>
                </div>

                <div class="footer-column">
                    <div class="footer-column-title">Kebijakan</div>
                    <ul>
                        <li><a href="#">Kebijakan pengiriman</a></li>
                        <li><a href="#">Kebijakan sistem pembayaran</a></li>
                        <li><a href="#">Kebijakan privasi</a></li>
                    </ul>
                </div>
            </div>

            <div class="footer-divider"></div>

            <div class="footer-account">
                <a href="#" class="account-link">Akun saya</a>
            </div>

            <div class="footer-partners">
                <div class="partners-title">Apple Premium Partner</div>
                <div class="partners-grid">
                    <div class="partner-item partner-premium">Premium Partner</div>
                    <div class="partner-item partner-service">Authorized Service Provider</div>
                    <div class="partner-item partner-business">Premium Business Partner</div>
                    <div class="partner-item partner-education">Premium Education Partner</div>
                </div>
            </div>

            <div class="footer-bottom">
                <div class="copyright">
                    © 2025 iBox. Hak cipta dilindungi undang-undang.
                </div>
                <div class="footer-links">
                    <a href="#">Syarat Penggunaan</a>
                    <a href="#">Peta Situs</a>
                    <a href="#">Cookies</a>
                </div>
                <button class="back-to-top" id="backToTop">
                    <i class="fas fa-arrow-up"></i> Kembali ke Atas
                </button>
            </div>
        </div>
        <script>
            // Back to top functionality
            document.getElementById('backToTop').addEventListener('click', function() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });

            // Show/hide back to top button based on scroll position
            window.addEventListener('scroll', function() {
                const backToTopBtn = document.getElementById('backToTop');
                if (window.scrollY > 300) {
                    backToTopBtn.style.display = 'block';
                } else {
                    backToTopBtn.style.display = 'block'; // Always visible in this design
                }
            });

            // Highlight current section in footer on hover
            document.querySelectorAll('.footer-column a').forEach(link => {
                link.addEventListener('mouseenter', function() {
                    this.style.color = '#0071e3';
                });

                link.addEventListener('mouseleave', function() {
                    this.style.color = '#515154';
                });
            });

            // Add animation to partner items on page load
            document.addEventListener('DOMContentLoaded', function() {
                const partnerItems = document.querySelectorAll('.partner-item');
                partnerItems.forEach((item, index) => {
                    setTimeout(() => {
                        item.style.opacity = '0';
                        item.style.transform = 'translateY(10px)';

                        setTimeout(() => {
                            item.style.transition = 'opacity 0.5s, transform 0.5s';
                            item.style.opacity = '1';
                            item.style.transform = 'translateY(0)';
                        }, 100);
                    }, index * 100);
                });
            });
        </script>
    </footer>


</body>

</html>