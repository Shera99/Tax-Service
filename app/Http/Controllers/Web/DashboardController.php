<?php

namespace App\Http\Controllers\Web;

use App\Exports\StatisticsExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Infrastructure\Persistence\Eloquent\Models\StatisticModel;
use Maatwebsite\Excel\Facades\Excel;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $query = StatisticModel::query();

        // Поиск
        if ($search = $request->get('search')) {
            $query->search($search);
        }

        // Фильтр по дате от
        if ($dateFrom = $request->get('date_from')) {
            $query->dateFrom($dateFrom);
        }

        // Фильтр по дате до
        if ($dateTo = $request->get('date_to')) {
            $query->dateTo($dateTo);
        }

        // Сортировка
        $sortField = $request->get('sort', 'date_time');
        $sortDirection = $request->get('direction', 'desc');

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

        $statistics = $query->paginate(20)->withQueryString();

        return view('dashboard.index', compact('statistics'));
    }

    public function export(Request $request)
    {
        $filename = 'statistics_' . date('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(new StatisticsExport($request), $filename);
    }
}
