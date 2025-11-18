// =======================================================
// 1. Toggle Menu Navbar untuk Mobile
// =======================================================
document.addEventListener('DOMContentLoaded', function () {
    const menuToggle = document.querySelector('.menu-toggle');
    const navbar = document.querySelector('.navbar');

    menuToggle.addEventListener('click', function () {
        navbar.classList.toggle('active');
        // Tutup semua dropdown (jika ada) saat menu utama ditutup
        document.querySelectorAll('.navbar .has-dropdown > ul').forEach(dropdown => {
            dropdown.classList.remove('active');
        });
    });

    // =======================================================
    // 2. Toggle Dropdown pada Navbar
    // Tidak ada menu dropdown di navbar, namun logika ini dipertahankan 
    // jika ada elemen has-dropdown di masa depan.
    // =======================================================
    document.querySelectorAll('.navbar .has-dropdown > a').forEach(item => {
        item.addEventListener('click', function (e) {
            // Hanya aktifkan toggle di perangkat mobile
            if (window.innerWidth <= 900 || navbar.classList.contains('active')) {
                e.preventDefault();
                const dropdown = this.nextElementSibling;
                // Tutup dropdown lain yang terbuka di level yang sama
                document.querySelectorAll('.navbar .has-dropdown > ul').forEach(otherDropdown => {
                    if (otherDropdown !== dropdown && otherDropdown.classList.contains('active')) {
                        otherDropdown.classList.remove('active');
                    }
                });
                dropdown.classList.toggle('active');
            }
        });
    });

    // Menutup dropdown saat klik di luar navbar
    document.addEventListener('click', function (e) {
        if (!navbar.contains(e.target) && !menuToggle.contains(e.target) && navbar.classList.contains('active')) {
            navbar.classList.remove('active');
            document.querySelectorAll('.navbar .has-dropdown > ul').forEach(dropdown => {
                dropdown.classList.remove('active');
            });
        }
    });
    
    // Panggil fungsi load data saat DOM selesai dimuat
    if (document.querySelector('.vision-mission-section')) {
        loadDatabaseContent(); 
    }
});


// =======================================================
// 3. Fungsi Load Data dari Server (Database)
// =======================================================

function loadDatabaseContent() {
    // Memanggil skrip sisi server untuk mengambil data
    fetch('get_data.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Gagal mengambil data dari get_data.php. Status: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            console.log('Data berhasil dimuat:', data);

            // Visi dan Misi: Dipertahankan
            const visionText = document.querySelector('.vision-card p');
            if (visionText && data.vision) {
                 // Dipertahankan untuk kompatibilitas AJAX di masa depan
            }

            const missionList = document.querySelector('.mission-card ul');
            if (missionList && data.mission && Array.isArray(data.mission)) {
                 // Dipertahankan untuk kompatibilitas AJAX di masa depan
            }

            // Catatan: Logika untuk Activities, Lecturers, dan Facilities telah DIHAPUS.
            
        })
        .catch(error => {
            console.error('Ada masalah saat memuat konten database:', error);
        });
}