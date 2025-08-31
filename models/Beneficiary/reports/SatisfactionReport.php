<?php
require_once "ReportStrategy.php";

// ===================== Satisfaction Report =====================
class SatisfactionReport implements ReportStrategy
{
    public function generateReport(mysqli $conn): array
    {
        $sql = "
            SELECT AVG(satisfaction_rating) as avg_rating
            FROM beneficiaryFeedback
            WHERE reported_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)
        ";

        $result = $conn->query($sql);

        $report = [];
        while ($row = $result->fetch_assoc()) {
            $report[] = $row;
        }

        return $report;
    }
}
