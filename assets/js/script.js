
    const formContainer = document.getElementById('newsFormContainer');
    const btnShow = document.getElementById('btnShowForm');
    const btnCancel = document.getElementById('btnCancelForm');
    const formTitle = document.getElementById('formTitle');
    const form = formContainer.querySelector('form');
    
    // Sesuaikan ID input
    const inputId = document.getElementById('berita_id');
    const inputJudul = document.getElementById('judul');
    const inputIsi = document.getElementById('isi');
    const inputTipe = document.getElementById('tipe'); // Input Tipe BARU
    const inputImage = document.getElementById('image_input');
    const inputExistingImage = document.getElementById('existing_gambar_header');
    const previewContainer = document.getElementById('image_preview_container');
    const previewImg = document.getElementById('image_preview');

    // Tampilkan form untuk TAMBAH
    btnShow.addEventListener('click', () => {
        form.reset();
        inputId.value = '';
        inputExistingImage.value = '';
        previewContainer.style.display = 'none';
        formContainer.style.display = 'block';
        btnCancel.style.display = 'inline-block';
        btnShow.style.display = 'none';
        window.scrollTo(0, 0); // Gulung ke atas
    });

    // Tampilkan form untuk EDIT (di-update)
    function editNews(id, judul, isi, gambar, tipe) {
        form.reset();
        inputId.value = id;
        inputJudul.value = judul;
        inputIsi.value = isi;
        inputTipe.value = tipe; // Set Tipe BARU
        inputExistingImage.value = gambar;
        formTitle.innerText = 'Edit Berita';
        btnShow.innerText = 'Tutup Form';
        
        if (gambar) {
            previewImg.src = '../uploads/news_images/' + gambar;
            previewContainer.style.display = 'block';
        } else {
            previewContainer.style.display = 'none';
        }
        
        formContainer.style.display = 'block';
        window.scrollTo(0, 0); // Gulung ke atas
    }

    // Sembunyikan form
    btnCancel.addEventListener('click', () => {
        formContainer.style.display = 'none';
        btnShow.innerText = 'Tambah Berita';
        btnCancel.style.display = 'none';
        btnShow.style.display = 'inline-block';
        form.reset();
    });
