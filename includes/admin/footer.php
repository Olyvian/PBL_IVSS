</section> </main> </div> <script>
// Ambil elemen-elemen
const formContainer = document.getElementById('newsFormContainer');
const btnShowForm = document.getElementById('btnShowForm');
const btnCancelForm = document.getElementById('btnCancelForm');
const formTitle = document.getElementById('formTitle');
const newsIdInput = document.getElementById('news_id');
const imgPreviewContainer = document.getElementById('image_preview_container');
const imgPreview = document.getElementById('image_preview');

// Fungsi untuk menampilkan form
function showForm(isEditMode = false) {
    formContainer.style.display = 'block';
    if (isEditMode) {
        formTitle.textContent = 'Edit Berita';
        btnShowForm.style.display = 'none'; // Sembunyikan tombol "Tambah Berita"
    } else {
        formTitle.textContent = 'Tambah Berita Baru';
        // Reset form untuk mode "Tambah"
        document.getElementById('news_id').value = '';
        document.getElementById('title').value = '';
        document.getElementById('content').value = '';
        document.getElementById('existing_image').value = '';
        document.getElementById('image_input').value = null;
        imgPreviewContainer.style.display = 'none';
        imgPreview.src = '';
    }
    // Scroll ke form
    window.scrollTo({ top: formContainer.offsetTop - 20, behavior: 'smooth' });
}

// Fungsi untuk menyembunyikan form
function hideForm() {
    formContainer.style.display = 'none';
    btnShowForm.style.display = 'inline-block'; // Tampilkan lagi tombol "Tambah"
    // Reset semua field
    document.getElementById('news_id').value = '';
    document.getElementById('title').value = '';
    document.getElementById('content').value = '';
    document.getElementById('existing_image').value = '';
    document.getElementById('image_input').value = null;
    imgPreviewContainer.style.display = 'none';
    imgPreview.src = '';
}

// Event listener untuk tombol
btnShowForm.addEventListener('click', () => showForm(false));
btnCancelForm.addEventListener('click', hideForm);


// PREVIEW GAMBAR SAAT UPLOAD
document.getElementById('image_input').addEventListener('change', function() {
    const file = this.files[0];
    if (!file) {
        imgPreviewContainer.style.display = 'none';
        return;
    }
    imgPreview.src = URL.createObjectURL(file);
    imgPreviewContainer.style.display = 'block';
});

// MODE EDIT (dari kode Anda)
function editNews(id, title, content, image) {
    // Tampilkan form dalam mode edit
    showForm(true); 
    
    document.getElementById('news_id').value = id;
    document.getElementById('title').value = title;
    document.getElementById('content').value = content;
    document.getElementById('existing_image').value = image;

    // Preview gambar lama
    if (image) {
        // Path disesuaikan (dari 'pages' ke 'uploads')
        imgPreview.src = "../uploads/news_images/" + image;
        imgPreviewContainer.style.display = 'block';
    } else {
        imgPreviewContainer.style.display = 'none';
    }
}
</script>
</body>
</html>