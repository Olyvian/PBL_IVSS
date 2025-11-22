document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.querySelector('.menu-toggle');
    const navbar = document.getElementById('main-navbar');
    const dropdownLinks = document.querySelectorAll('.has-dropdown > a');

    // ===================================
    // 1. Fungsi Toggle Menu Hamburger (Responsif)
    // ===================================
    
    if (menuToggle && navbar) {
        menuToggle.addEventListener('click', function() {
            // Menambah/menghapus kelas 'active' pada navbar untuk menampilkan/menyembunyikan
            navbar.classList.toggle('active');
            
            // Mengubah ikon hamburger menjadi X dan sebaliknya
            if (navbar.classList.contains('active')) {
                this.innerHTML = '&#10005;'; // Ikon X
            } else {
                this.innerHTML = '&#9776;'; // Ikon Hamburger
            }
        });
    }

    // ===================================
    // 2. Fungsi Dropdown (Untuk perangkat mobile/sentuh)
    // ===================================
    
    dropdownLinks.forEach(link => {
        link.addEventListener('click', function(event) {
            // Cek jika lebar layar kecil (seperti di CSS media query)
            if (window.innerWidth <= 768) {
                
                // Mencegah navigasi link (agar klik membuka menu)
                event.preventDefault(); 

                // Temukan elemen <ul> (sub-menu) yang berada di sebelah link
                const submenu = this.nextElementSibling;
                
                if (submenu && submenu.tagName === 'UL') {
                    
                    // Tutup semua sub-menu lain yang sedang terbuka
                    document.querySelectorAll('.has-dropdown ul').forEach(otherSub => {
                        // Pastikan hanya menutup sub-menu lain di level yang sama
                        if (otherSub !== submenu && otherSub.classList.contains('open')) {
                            otherSub.classList.remove('open');
                        }
                    });

                    // Buka/Tutup sub-menu yang sedang di-klik (menggunakan kelas 'open' dari CSS)
                    submenu.classList.toggle('open');
                }
            }
            // Jika layar besar, link akan berjalan normal (pindah halaman) karena tidak dicegah (event.preventDefault)
        });
    });
});