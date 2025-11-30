<style>
    /* Catatan: CSS ini menggunakan variabel dari :root yang Anda kirim.
       Pastikan style_profil.css sudah termuat di halaman utama.
    */

    .footer-polinema {
        background-color: var(--polinema-blue); /* Biru Utama #0D4C7C */
        color: var(--text-light);               /* Putih #ffffff */
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        padding-top: 60px;
        margin-top: auto;
        font-size: 15px;
        position: relative;
        /* Garis atas menggunakan Merah Aksen */
        border-top: 5px solid var(--polinema-dark-gradient); 
    }

    /* Container Utama: Grid */
    .footer-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 40px 50px 40px;
        display: grid;
        grid-template-columns: 4fr 2.5fr 3.5fr; 
        gap: 60px;
    }

    /* --- KOLOM 1: IDENTITAS --- */
    .footer-brand-box {
        display: flex;
        align-items: center;
        margin-bottom: 25px;
    }

    .footer-logo {
        width: 65px;
        height: 65px;
        margin-right: 15px;
        background: var(--text-light); /* Putih */
        padding: 5px;
        border-radius: 50%;
        object-fit: contain;
    }

    .footer-brand-text h3 {
        margin: 0;
        font-size: 13px;
        font-weight: 400;
        opacity: 0.9;
        letter-spacing: 0.5px;
        color: var(--text-light);
    }

    .footer-brand-text h2 {
        margin: 0;
        font-size: 18px;
        font-weight: 800;
        line-height: 1.2;
        color: var(--text-light);
    }

    .footer-contact p {
        margin: 10px 0;
        display: flex;
        align-items: center;
        color: var(--footer-link); /* Abu-abu terang #CCCCCC */
    }

    .footer-contact i {
        width: 25px;
        /* Menggunakan Putih agar kontras di atas biru tua */
        color: var(--text-light); 
        text-align: center;
        margin-right: 10px;
        font-size: 1.1em;
    }

    /* --- KOLOM 2 & 3: JUDUL & LINK --- */
    .footer-heading {
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 25px;
        color: var(--text-light);
        position: relative;
        padding-bottom: 10px;
    }

    /* Garis bawah judul menggunakan Merah Aksen */
    .footer-heading::after {
        content: '';
        position: absolute;
        left: 0;
        bottom: 0;
        width: 30px;
        height: 3px;
        background-color: var(--polinema-dark-gradient); /* Merah #CC0000 */
        border-radius: 2px;
    }

    .footer-links {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .footer-links li {
        margin-bottom: 12px;
        /* Garis pemisah tipis menggunakan warna border yang ada */
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        padding-bottom: 12px;
    }

    .footer-links li:last-child {
        border-bottom: none;
    }

    .footer-links a {
        color: var(--footer-link); /* Abu-abu #CCCCCC */
        text-decoration: none;
        transition: 0.3s;
        display: block;
    }

    .footer-links a:hover {
        color: var(--text-light); /* Putih saat hover */
        padding-left: 5px;
    }

    /* --- KOLOM 3: TOMBOL & EKSTERNAL --- */
    .footer-desc {
        font-size: 14px;
        color: var(--footer-link);
        line-height: 1.5;
        margin-bottom: 20px;
    }

    /* === TOMBOL DAFTAR (MERAH) === */
    .btn-footer-cta {
        display: inline-block;
        background-color: var(--polinema-red); /* Merah #CC0000 */
        color: var(--text-light);              /* Putih */
        padding: 10px 25px;
        border-radius: 4px; /* Radius kecil agar formal */
        font-weight: 600;
        text-decoration: none;
        font-size: 14px;
        border: 1px solid var(--polinema-red);
        transition: all 0.3s ease;
    }

    .btn-footer-cta:hover {
        /* Saat hover: Background Putih, Teks Merah */
        background-color: var(--text-light); 
        color: var(--polinema-red);
    }
    /* ============================= */

    .external-links {
        margin-top: 30px;
    }

    .footer-heading-small {
        font-size: 14px;
        font-weight: 700;
        margin-bottom: 15px;
        color: var(--footer-link);
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .external-links ul {
        list-style: none;
        padding: 0;
    }

    .external-links li {
        margin-bottom: 8px;
    }

    .external-links a {
        color: var(--footer-link);
        text-decoration: none;
        font-size: 14px;
    }

    .external-links a:hover {
        color: var(--text-light);
        text-decoration: underline;
    }

    .external-links i {
        font-size: 12px;
        margin-left: 5px;
        color: var(--text-light);
    }

    /* --- COPYRIGHT BAR --- */
    .footer-bottom {
        background-color: rgba(0,0,0,0.2); /* Gelapkan sedikit dari background utama */
        text-align: center;
        padding: 20px;
        font-size: 14px;
        color: var(--footer-link);
        border-top: 1px solid rgba(255,255,255,0.05);
    }

    /* --- RESPONSIVE --- */
    @media (max-width: 900px) {
        .footer-container {
            grid-template-columns: 1fr;
            gap: 40px;
            text-align: center;
        }

        .footer-brand-box {
            justify-content: center;
            text-align: left;
        }

        .footer-contact p {
            justify-content: center;
        }

        .footer-heading::after {
            left: 50%;
            transform: translateX(-50%);
        }
    }
</style>

<footer class="footer-polinema">
    <div class="footer-container">
        
        <div class="footer-col identity-col">
            <div class="footer-brand-box">
                <img src="assets/img/logo_polinema.png" alt="Logo POLINEMA" class="footer-logo">
                <div class="footer-brand-text">
                    <h3>JURUSAN TEKNOLOGI INFORMASI</h3>
                    <h2>POLITEKNIK NEGERI MALANG</h2>
                </div>
            </div>
            
            <div class="footer-contact">
                <p><i class="fa-solid fa-map-location-dot"></i> Jl. Soekarno Hatta No.9, Malang 65141</p>
                <p><i class="fa-solid fa-envelope"></i> jti@polinema.ac.id</p>
                <p><i class="fa-solid fa-phone"></i> (0341) 404424</p>
            </div>
        </div>

        <div class="footer-col links-col">
            <h4 class="footer-heading">Jelajahi Lab</h4>
            <ul class="footer-links">
                <li><a href="index.php">Beranda</a></li>
                <li><a href="riset.php">Riset & Penelitian</a></li>
                <li><a href="member.php">Anggota Lab (Member)</a></li>
                <li><a href="berita-pengumuman.php">Berita & Pengumuman</a></li>
            </ul>
        </div>

        <div class="footer-col action-col">
            <h4 class="footer-heading">Bergabunglah</h4>
            <p class="footer-desc">Tertarik menjadi bagian dari riset kami? Daftarkan diri Anda sekarang.</p>
            
            <a href="pendaftaran.php" class="btn-footer-cta">
                Daftar Magang
            </a>
        </div>

    </div>

    <div class="footer-bottom">
        <p>&copy; <?= date('Y') ?> Laboratorium IVSS - Politeknik Negeri Malang. All Rights Reserved.</p>
    </div>
</footer>

<script src="assets/js/script_main.js"></script> 
</body>
</html>