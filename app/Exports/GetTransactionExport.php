<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Spatie\QueryBuilder\QueryBuilder;

class GetTransactionExport implements FromQuery, WithMapping, WithHeadings, WithColumnWidths
{
    use Exportable;

    public function __construct(private QueryBuilder $query)
    {
    }

    public function query(): QueryBuilder
    {
        return $this->query;
    }

    public function map($transaction): array
    {
        $date = Carbon::parse($transaction->created_at)->timezone("Asia/Jakarta")->format("d-M-Y H:i:s");
        return [
            $transaction->code,
            'Rp. ' . number_format((float)$transaction->total_price, '0', '.', ','),
            $date,
        ];
    }

    public function headings(): array
    {
        return [
            'Transaction Code',
            'Total Price',
            'Created At',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 30,
            'C' => 30,
        ];
    }
}
