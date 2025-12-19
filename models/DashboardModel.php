<?php
class DashboardModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getCounts()
    {
        try {
            return [
                'totalBerita' => $this->pdo->query("SELECT count(id) FROM berita")->fetchColumn(),
                'totalMember' => $this->pdo->query("SELECT count(id) FROM anggota_lab")->fetchColumn(),
                'totalRiset' => $this->pdo->query("SELECT count(id) FROM riset")->fetchColumn(),
                'totalPendaftaran' => $this->pdo->query("SELECT count(id) FROM pendaftaran_magang WHERE status = 'pending'")->fetchColumn(),
                'totalFasilitas' => $this->pdo->query("SELECT count(id) FROM fasilitas_peralatan")->fetchColumn()
            ];
        } catch (PDOException $e) {
            return [
                'totalBerita' => 0,
                'totalMember' => 0,
                'totalRiset' => 0,
                'totalPendaftaran' => 0,
                'totalFasilitas' => 0,
                'error' => $e->getMessage()
            ];
        }
    }
}
?>