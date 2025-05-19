<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProfitLossExport implements WithMultipleSheets
{
    protected $reportData;
    protected $period;
    protected $store;

    public function __construct($reportData, $period, $store = null)
    {
        $this->reportData = $reportData;
        $this->period = $period;
        $this->store = $store;
    }

    public function sheets(): array
    {
        return [
            new ProfitLossSummarySheet($this->reportData, $this->period, $this->store),
            new ProfitLossDetailSheet($this->reportData, $this->period, $this->store),
            new ProfitLossTrendSheet($this->reportData['chart_data'], $this->period, $this->store),
        ];
    }
}

class ProfitLossSummarySheet implements FromCollection, WithTitle, WithHeadings, WithStyles, WithColumnFormatting, ShouldAutoSize
{
    protected $reportData;
    protected $period;
    protected $store;

    public function __construct($reportData, $period, $store)
    {
        $this->reportData = $reportData;
        $this->period = $period;
        $this->store = $store;
    }

    public function collection()
    {
        return collect([
            // Summary Section
            ['Summary', '', ''],
            ['Period', $this->period['start'] . ' to ' . $this->period['end'], ''],
            ['Store', $this->store ? $this->store->name : 'All Stores', ''],
            ['', '', ''],

            // Key Metrics
            ['Key Metrics', 'Amount', 'Growth'],
            ['Gross Sales', $this->reportData['sales']['gross'], $this->reportData['sales']['growth'] . '%'],
            ['Net Sales', $this->reportData['sales']['net'], $this->reportData['sales']['growth'] . '%'],
            ['Total Costs', $this->reportData['costs']['total_costs'], $this->reportData['costs']['growth'] . '%'],
            ['Net Profit', $this->reportData['summary']['net_profit'], $this->reportData['summary']['growth'] . '%'],
        ]);
    }

    public function title(): string
    {
        return 'Summary';
    }

    public function headings(): array
    {
        return []; // We'll handle headers in the collection
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            5 => ['font' => ['bold' => true]],
            'B6:B9' => ['numberFormat' => ['formatCode' => '"$"#,##0.00_-']],
        ];
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
        ];
    }
}

class ProfitLossDetailSheet implements FromCollection, WithTitle, WithHeadings, WithStyles, WithColumnFormatting, ShouldAutoSize
{
    protected $reportData;
    protected $period;
    protected $store;

    public function __construct($reportData, $period, $store)
    {
        $this->reportData = $reportData;
        $this->period = $period;
        $this->store = $store;
    }

    public function collection()
    {
        return collect([
            // Sales Section
            ['Sales Breakdown', '', ''],
            ['Component', 'Amount', 'Percentage'],
            ['Gross Sales', $this->reportData['sales']['gross'], '100%'],
            ['Sales Tax', $this->reportData['sales']['tax'],
                $this->calculatePercentage($this->reportData['sales']['tax'], $this->reportData['sales']['gross'])],
            ['Discounts', $this->reportData['sales']['discount'],
                $this->calculatePercentage($this->reportData['sales']['discount'], $this->reportData['sales']['gross'])],
            ['Net Sales', $this->reportData['sales']['net'],
                $this->calculatePercentage($this->reportData['sales']['net'], $this->reportData['sales']['gross'])],
            ['', '', ''],

            // Costs Section
            ['Costs Breakdown', '', ''],
            ['Component', 'Amount', 'Percentage'],
            ['Gross Purchases', $this->reportData['costs']['purchases']['gross'], '100%'],
            ['Purchase Tax', $this->reportData['costs']['purchases']['tax'],
                $this->calculatePercentage($this->reportData['costs']['purchases']['tax'],
                $this->reportData['costs']['purchases']['gross'])],
            ['Purchase Discounts', $this->reportData['costs']['purchases']['discount'],
                $this->calculatePercentage($this->reportData['costs']['purchases']['discount'],
                $this->reportData['costs']['purchases']['gross'])],
            ['Net Purchases', $this->reportData['costs']['purchases']['net'],
                $this->calculatePercentage($this->reportData['costs']['purchases']['net'],
                $this->reportData['costs']['purchases']['gross'])],
            ['Operating Expenses', $this->reportData['costs']['expenses'],
                $this->calculatePercentage($this->reportData['costs']['expenses'],
                $this->reportData['costs']['purchases']['gross'])],
            ['Total Costs', $this->reportData['costs']['total_costs'],
                $this->calculatePercentage($this->reportData['costs']['total_costs'],
                $this->reportData['costs']['purchases']['gross'])],
        ]);
    }

    public function title(): string
    {
        return 'Detailed Analysis';
    }

    public function headings(): array
    {
        return []; // We'll handle headers in the collection
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
            2 => ['font' => ['bold' => true]],
            9 => ['font' => ['bold' => true, 'size' => 12]],
            10 => ['font' => ['bold' => true]],
        ];
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
        ];
    }

    private function calculatePercentage($value, $total)
    {
        if ($total == 0) return '0%';
        return round(($value / $total) * 100, 1) . '%';
    }
}

class ProfitLossTrendSheet implements FromCollection, WithTitle, WithHeadings, WithStyles, WithColumnFormatting, ShouldAutoSize
{
    protected $chartData;
    protected $period;
    protected $store;

    public function __construct($chartData, $period, $store)
    {
        $this->chartData = $chartData;
        $this->period = $period;
        $this->store = $store;
    }

    public function collection()
    {
        $data = collect([['Date', 'Net Sales', 'Total Costs', 'Net Profit']]);

        foreach ($this->chartData['labels'] as $index => $date) {
            $data->push([
                $date,
                $this->chartData['sales'][$index],
                $this->chartData['costs'][$index],
                $this->chartData['profit'][$index]
            ]);
        }

        return $data;
    }

    public function title(): string
    {
        return 'Daily Trend';
    }

    public function headings(): array
    {
        return []; // We'll handle headers in the collection
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
            'C' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
            'D' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
        ];
    }
}
