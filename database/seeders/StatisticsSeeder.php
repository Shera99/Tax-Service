<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Infrastructure\Persistence\Eloquent\Models\StatisticModel;

class StatisticsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $events = [
            'Концерт "Звезды эстрады"',
            'Театральная постановка "Гамлет"',
            'Балет "Лебединое озеро"',
            'Опера "Кармен"',
            'Рок-фестиваль "Summer Fest"',
            'Стендап-шоу "Юмор FM"',
            'Детский спектакль "Золушка"',
            'Симфонический оркестр',
            'Джазовый вечер',
            'Цирковое представление',
            'Мюзикл "Ромео и Джульетта"',
            'Кинопоказ "Премьера года"',
            'Выставка современного искусства',
            'Спортивное мероприятие',
            'Корпоративное мероприятие',
        ];

        $organizations = [
            'ТОО "КазахКонцерт"',
            'АО "Национальный театр"',
            'ИП "Иванов И.И."',
            'ТОО "Event Pro"',
            'АО "Культурный центр"',
            'ТОО "Show Time"',
            'ИП "Сидоров С.С."',
            'ТОО "Mega Events"',
        ];

        $venues = [
            'Дворец Республики',
            'Национальный театр оперы и балета',
            'Концертный зал "Астана"',
            'Спорткомплекс "Арена"',
            'Театр драмы им. М. Ауэзова',
            'Дворец спорта',
            'Конгресс-холл',
            'Центральный стадион',
            null, // Некоторые записи без площадки
        ];

        for ($i = 0; $i < 50; $i++) {
            $totalAvailable = rand(100, 2000);
            $sold = rand(0, $totalAvailable);
            $invitations = rand(0, min(50, $totalAvailable - $sold));
            $free = $totalAvailable - $sold - $invitations;
            $refunded = rand(0, min(20, $sold));
            $pricePerTicket = rand(1000, 15000);

            StatisticModel::create([
                'event_name' => $events[array_rand($events)] . ' #' . ($i + 1),
                'organization_name' => $organizations[array_rand($organizations)],
                'venue_name' => $venues[array_rand($venues)],
                'date_time' => now()->subDays(rand(0, 365))->setTime(rand(10, 21), 0),
                'total_tickets_available' => $totalAvailable,
                'total_amount_sold' => ($sold - $refunded) * $pricePerTicket,
                'total_tickets_sold' => $sold,
                'free_tickets_count' => $free,
                'invitation_tickets_count' => $invitations,
                'refunded_tickets_count' => $refunded,
            ]);
        }
    }
}
