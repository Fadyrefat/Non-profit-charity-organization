<?php

require_once __DIR__ . "/reports/ReportStrategy.php";
require_once __DIR__ . "/reports/MonthlyReport.php";
require_once __DIR__ . "/reports/SatisfactionReport.php";
require_once __DIR__ . "/reports/ResourceUsageReport.php";

class ReportContext
{
    private ReportStrategy $strategy;

    public function __construct(string $type)
    {
        $this->setStrategyByType($type);
    }

    // ===================== Set Strategy Based on Report Type =====================
    public function setStrategyByType(string $type)
    {
        switch (strtolower($type)) {
            case 'satisfaction':
                $this->strategy = new SatisfactionReport();
                break;
            case 'resource':
                $this->strategy = new ResourceUsageReport();
                break;
            case 'monthly':
            default:
                $this->strategy = new MonthlyReport();
                break;
        }
    }

    // ===================== Generate Report =====================
    public function generate(mysqli $conn): array
    {
        return $this->strategy->generateReport($conn);
    }
}
?>
