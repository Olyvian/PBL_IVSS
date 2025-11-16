<?php
require_once __DIR__ . '/../config/database.php';
$stmt = $pdo->query("SELECT * FROM news ORDER BY published_at DESC");
$allNews = $stmt->fetchAll();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Berita & Pengumuman</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    .news-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:18px;max-width:1100px;margin:18px auto}
    .news-card .card{padding:0;overflow:hidden}
    .news-thumb{width:100%;height:160px;object-fit:cover;display:block}
    .news-body{padding:12px}
    .news-title{font-weight:700;margin-bottom:8px;font-size:16px}
    .news-meta{font-size:13px;color:var(--muted);margin-top:8px}
  </style>
</head>
<body>
<div style="max-width:1200px;margin:28px auto;padding:0 18px">
  <h2 style="margin-bottom:12px">Berita & Pengumuman</h2>

  <div class="news-grid">
    <?php foreach ($allNews as $item): ?>
    <div class="news-card card">
      <?php if (!empty($item['image'])): ?>
        <img class="news-thumb" src="../uploads/news_images/<?= htmlspecialchars($item['image']) ?>" alt="">
      <?php endif; ?>
      <div class="news-body">
        <div class="news-title"><?= htmlspecialchars($item['title']) ?></div>
        <div class="news-excerpt"><?= nl2br(htmlspecialchars(substr(strip_tags($item['content']),0,180))) ?>...</div>
        <div class="news-meta"><?= date('d M Y', strtotime($item['published_at'])) ?></div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</div>
</body>
</html>
