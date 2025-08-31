<?php
// ===================== Report Strategy Interface =====================
interface ReportStrategy
{
    public function generateReport(mysqli $conn): array;
}
