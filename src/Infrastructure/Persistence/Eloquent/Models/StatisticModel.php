<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;

class StatisticModel extends Model
{
    protected $table = 'statistics';

    protected $fillable = [
        'event_name',
        'organization_name',
        'date_time',
        'total_tickets_available',
        'total_amount_sold',
        'total_tickets_sold',
        'free_tickets_count',
        'invitation_tickets_count',
        'refunded_tickets_count',
    ];

    protected $casts = [
        'date_time' => 'datetime',
        'total_tickets_available' => 'integer',
        'total_amount_sold' => 'decimal:2',
        'total_tickets_sold' => 'integer',
        'free_tickets_count' => 'integer',
        'invitation_tickets_count' => 'integer',
        'refunded_tickets_count' => 'integer',
    ];

    public function scopeSearch($query, ?string $search)
    {
        if ($search) {
            $search = mb_strtolower($search);
            return $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(event_name) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(organization_name) LIKE ?', ["%{$search}%"]);
            });
        }
        return $query;
    }

    public function scopeDateFrom($query, ?string $dateFrom)
    {
        if ($dateFrom) {
            return $query->where('date_time', '>=', $dateFrom);
        }
        return $query;
    }

    public function scopeDateTo($query, ?string $dateTo)
    {
        if ($dateTo) {
            return $query->where('date_time', '<=', $dateTo . ' 23:59:59');
        }
        return $query;
    }
}
