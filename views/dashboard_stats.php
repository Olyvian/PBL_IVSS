<div class="dashboard-grid">
    <div class="stat-card">
        <div class="stat-icon"><i class="fa-solid fa-newspaper"></i></div>
        <div class="stat-info">
            <span class="stat-title">Total Berita</span>
            <span class="stat-number"><?php echo $stats['totalBerita']; ?></span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="color: #198754; background-color: #e8f3ee;">
            <i class="fa-solid fa-users"></i>
        </div>
        <div class="stat-info">
            <span class="stat-title">Total Member</span>
            <span class="stat-number"><?php echo $stats['totalMember']; ?></span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="color: #ffc107; background-color: #fff8e6;">
            <i class="fa-solid fa-flask"></i>
        </div>
        <div class="stat-info">
            <span class="stat-title">Total Riset</span>
            <span class="stat-number"><?php echo $stats['totalRiset']; ?></span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="color: #6f42c1; background-color: #f1eef8;">
            <i class="fa-solid fa-file-signature"></i>
        </div>
        <div class="stat-info">
            <span class="stat-title">Pendaftaran Pending</span>
            <span class="stat-number"><?php echo $stats['totalPendaftaran']; ?></span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="color: #0dcaf0; background-color: #e0faff;">
            <i class="fa-solid fa-computer"></i>
        </div>
        <div class="stat-info">
            <span class="stat-title">Total Fasilitas</span>
            <span class="stat-number"><?php echo $stats['totalFasilitas']; ?></span>
        </div>
    </div>
</div>