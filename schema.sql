
-- 1. Tabel Users\
DROP IF EXISTS TABLE users CASCADE;
CREATE TABLE users (
  id SERIAL PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  email VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role VARCHAR(20) NOT NULL DEFAULT 'viewer' CHECK (role IN ('viewer', 'admin_lab', 'admin_berita','member_lab')),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Tabel Anggota Lab
CREATE TABLE anggota_lab (
  id SERIAL PRIMARY KEY,
  user_id INT NULL,
  nama_lengkap VARCHAR(100) NOT NULL,
  posisi VARCHAR(50) NOT NULL,
  bio TEXT NULL,
  foto_profil VARCHAR(255) NULL,
  nomor_telepon VARCHAR(20) NULL, -- Kolom baru
  status VARCHAR(20) NOT NULL DEFAULT 'aktif' CHECK (status IN ('aktif', 'alumni')),
  CONSTRAINT fk_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);
-- 3. Tabel Berita
CREATE TABLE berita (
  id SERIAL PRIMARY KEY,
  judul VARCHAR(255) NOT NULL,
  isi TEXT NOT NULL,
  gambar_header VARCHAR(255) NULL,
  tipe VARCHAR(20) NOT NULL DEFAULT 'berita' CHECK (tipe IN ('berita', 'pengumuman', 'pelatihan')),
  author_id INT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_author FOREIGN KEY (author_id) REFERENCES users(id)
);

-- 4. Tabel Riset
CREATE TABLE riset (
  id SERIAL PRIMARY KEY,
  judul_riset VARCHAR(255) NOT NULL,
  deskripsi TEXT NOT NULL,
  link_riset text,
  tanggal_mulai DATE NULL,
  tanggal_selesai DATE NULL
);

-- 5. Tabel Pivot (Riset - Anggota)
CREATE TABLE riset_anggota (
  riset_id INT NOT NULL,
  anggota_id INT NOT NULL,
  PRIMARY KEY (riset_id, anggota_id),
  CONSTRAINT fk_riset FOREIGN KEY (riset_id) REFERENCES riset(id) ON DELETE CASCADE,
  CONSTRAINT fk_anggota FOREIGN KEY (anggota_id) REFERENCES anggota_lab(id) ON DELETE CASCADE
);

-- 6. Tabel Pendaftaran Magang
CREATE TABLE pendaftaran_magang (
  id SERIAL PRIMARY KEY,
  user_id INT NOT NULL,
  nama_lengkap VARCHAR(100) NOT NULL,
  cv_file VARCHAR(255) NOT NULL,
  motivasi TEXT NULL,
  status VARCHAR(20) NOT NULL DEFAULT 'pending' CHECK (status IN ('pending', 'diterima', 'ditolak')),
  tanggal_daftar TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_pendaftar FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 7. Tabel Fasilitas dan Peralatan
CREATE TABLE fasilitas_peralatan (
    id SERIAL PRIMARY KEY,
    jenis VARCHAR(50) NOT NULL,            
    judul VARCHAR(255) NOT NULL,           
    deskripsi TEXT,
    gambar VARCHAR(255) NULL                   
);

-- 8. Tabel Visi dan Misi
CREATE TABLE visi_misi (
    id SERIAL PRIMARY KEY,
    tipe VARCHAR(10) NOT NULL,
    konten_id INT NOT NULL,
    deskripsi TEXT
);
CREATE TABLE dataset(
    id SERIAL PRIMARY KEY,
    nama_dataset VARCHAR(255) NOT NULL,
    deskripsi TEXT,
    url TEXT NOT NULL,
    tanggal_ditambahkan TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)
-- 9. Tabel Produk (Inovasi Lab)
CREATE TABLE produk (
    id SERIAL PRIMARY KEY,
    nama_produk VARCHAR(255) NOT NULL,
    deskripsi TEXT,
    gambar VARCHAR(255) NULL,
    tanggal_dibuat TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


DROP PROCEDURE IF EXISTS tambah_anggota_lab(
    VARCHAR, VARCHAR, VARCHAR, VARCHAR, VARCHAR, VARCHAR
);

CREATE OR REPLACE PROCEDURE tambah_anggota_lab(
    username VARCHAR(50),
    email VARCHAR(100),
    password VARCHAR(255),
    nama_lengkap VARCHAR(100),
    posisi VARCHAR(50),
    nomor_telepon VARCHAR(20) DEFAULT NULL
)
LANGUAGE plpgsql
AS $$
DECLARE
    user_id INT;
BEGIN

    INSERT INTO users (username, email, password, role)
    VALUES (username, email, password, 'member_lab')
    RETURNING id INTO user_id;

    INSERT INTO anggota_lab (
        user_id,
        nama_lengkap,
        posisi,
        nomor_telepon,
        status
    )
    VALUES (
        user_id,
        nama_lengkap,
        posisi,
        nomor_telepon,
        'aktif'
    );


EXCEPTION
    WHEN unique_violation THEN
        RAISE EXCEPTION 'Username atau email sudah terdaftar: %', SQLERRM;
    WHEN OTHERS THEN
        RAISE EXCEPTION 'Gagal menambahkan anggota lab: %', SQLERRM;
END;
$$;
