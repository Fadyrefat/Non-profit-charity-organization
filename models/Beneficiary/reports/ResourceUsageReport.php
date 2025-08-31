<?php
require_once "ReportStrategy.php";

// ===================== Resource Usage Report =====================
class ResourceUsageReport implements ReportStrategy
{
    public function generateReport(mysqli $conn): array
    {
        $sql = "
            SELECT resource_type, SUM(quantity) as total_distributed
            FROM distributions
            GROUP BY resource_type
        ";

        $result = $conn->query($sql);

        $report = [];
        while ($row = $result->fetch_assoc()) {
            $report[] = $row;
        }

        return $report;
    }
}
