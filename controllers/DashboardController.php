<?php
require_once __DIR__ . '/../models/DashboardModel.php';

class DashboardController
{
    private $model;

    public function __construct($pdo)
    {
        $this->model = new DashboardModel($pdo);
    }

    public function getStats()
    {
        $stats = $this->model->getCounts();
        return $stats;
    }
}
?>