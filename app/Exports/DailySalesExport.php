<?php

namespace App\Exports;

use App\Models\Sale;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\BeforeSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DailySalesExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithEvents
{
    use Exportable;

    public function __construct(
        protected string $startDate,
        protected string $endDate,
        protected string $branchId = '',
        protected string $productCategoryId = '',
    ) {}

    public function query()
    {
        $start = $this->startDate . ' 00:00:00';
        $end   = $this->endDate   . ' 23:59:59';

        return Sale::query()
            ->with('store.branch', 'product.category')
            ->whereBetween('transaction_date', [$start, $end])
            ->when($this->branchId, function ($q) {
                $q->whereHas('store', fn ($sq) => $sq->where('branch_id', $this->branchId));
            })
            ->when($this->productCategoryId, function ($q) {
                $q->whereHas('product', fn ($sq) => $sq->where('product_category_id', $this->productCategoryId));
            })
            ->latest('transaction_date');
    }

    public function headings(): array
    {
        return [
            'Date',
            'Time',
            'Store',
            'Branch',
            'Product',
            'Category',
            'Amount (IDR)',
        ];
    }

    public function map($sale): array
    {
        return [
            $sale->transaction_date->format('Y-m-d'),
            $sale->transaction_date->format('H:i'),
            $sale->store->name ?? '-',
            $sale->store->branch->name ?? '-',
            $sale->product->name ?? '-',
            $sale->product?->category?->name ?? '-',
            $sale->amount,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            // Header row bold with background
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F46E5'],
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ],
        ];
    }

    /**
     * Set PDF/print layout to landscape.
     */
    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function (BeforeSheet $event) {
                $event->sheet->getDelegate()
                    ->getPageSetup()
                    ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)
                    ->setPaperSize(PageSetup::PAPERSIZE_A4);
            },
        ];
    }
}
