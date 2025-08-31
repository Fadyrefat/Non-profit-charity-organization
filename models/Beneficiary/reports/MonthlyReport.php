<?php
require_once "ReportStrategy.php";

class MonthlyReport implements ReportStrategy {
    public function generateReport(mysqli $conn): array {
        $sql = "
            SELECT request_type, COUNT(*) as total_requests
            FROM requests
            WHERE MONTH(created_at) = MONTH(CURRENT_DATE())
              AND YEAR(created_at) = YEAR(CURRENT_DATE())
            GROUP BY request_type
        ";
        $result = $conn->query($sql);

        $report = [];
        while ($row = $result->fetch_assoc()) {
            $report[] = $row;
        }
        return $report;
    }
}
