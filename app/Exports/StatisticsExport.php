<?php

namespace App\Exports;

use Illuminate\Http\Request;
use Infrastructure\Persistence\Eloquent\Models\StatisticModel;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StatisticsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    use Exportable;

    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function query()
    {
        $query = StatisticModel::query();

        // Поиск
        if ($search = $this->request->get('search')) {
            $query->search($search);
        }

        // Фильтр по дате от
        if ($dateFrom = $this->request->get('date_from')) {
            $query->dateFrom($dateFrom);
        }

        // Фильтр по дате до
        if ($dateTo = $this->request->get('date_to')) {
            $query->dateTo($dateTo);
        }

        // Сортировка
        $sortField = $this->request->get('sort', 'date_time');
        $sortDirection = $this->request->get('direction', 'desc');

        $allowedSortFields = [
            'date_time',
            'created_at',
            'event_name',
            'organization_name',
            'total_tickets_available',
            'total_amount_sold',
            'total_tickets_sold',
            'free_tickets_count',
            'invitation_tickets_count',
            'refunded_tickets_count',
        ];

        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortDirection === 'asc' ? 'asc' : 'desc');
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'ID',
            'ID события',
            'ID сессии',
            'Название мероприятия',
            'Организация',
            'Площадка',
            'Дата и время',
            'Всего билетов',
            'Продано билетов',
            'Свободных билетов',
            'Пригласительных',
            'Возвращено',
            'Сумма продаж',
            'Дата создания',
        ];
    }

    public function map($statistic): array
    {
        return [
            $statistic->id,
            $statistic->event_id,
            $statistic->session_id,
            $statistic->event_name,
            $statistic->organization_name,
            $statistic->venue_name ?? '',
            $statistic->date_time->format('d.m.Y H:i'),
            $statistic->total_tickets_available,
            $statistic->total_tickets_sold,
            $statistic->free_tickets_count,
            $statistic->invitation_tickets_count,
            $statistic->refunded_tickets_count,
            $statistic->total_amount_sold,
            $statistic->created_at->format('d.m.Y H:i'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            // Заголовки жирным
            1 => ['font' => ['bold' => true]],
        ];
    }
}
