<?php
interface ReportStrategy {
    public function generateReport(mysqli $conn): array;
}
