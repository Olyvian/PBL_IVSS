-- 1. Tabel Users
CREATE TABLE users (
  id SERIAL PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  email VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role VARCHAR(20) NOT NULL DEFAULT 'viewer' CHECK (role IN ('viewer', 'admin_lab', 'admin_berita')),
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
  status VARCHAR(20) NOT NULL DEFAULT 'aktif' CHECK (status IN ('aktif', 'alumni')),
  CONSTRAINT fk_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- 3. Tabel Berita
CREATE TABLE berita (
  id SERIAL PRIMARY KEY,
  judul VARCHAR(255) NOT NULL,
  isi TEXT NOT NULL,
  gambar_header VARCHAR(255) NULL,
  tipe VARCHAR(20) NOT NULL DEFAULT 'berita' CHECK (tipe IN ('berita', 'pengumuman')),
  author_id INT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_author FOREIGN KEY (author_id) REFERENCES users(id)
);

-- 4. Tabel Riset
CREATE TABLE riset (
  id SERIAL PRIMARY KEY,
  judul_riset VARCHAR(255) NOT NULL,
  deskripsi TEXT NOT NULL,
  link_riset TYPE text,
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
  universitas VARCHAR(100) NOT NULL,
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
    ikon_fa VARCHAR(255)                   
);

-- 8. Tabel Visi dan Misi
CREATE TABLE visi_misi (
    id SERIAL PRIMARY KEY,
    tipe VARCHAR(10) NOT NULL,
    konten_id INT NOT NULL,
    deskripsi TEXT
);
