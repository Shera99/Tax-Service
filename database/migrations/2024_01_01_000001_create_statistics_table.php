<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('statistics', function (Blueprint $table) {
            $table->id();
            $table->string('event_name', 255);
            $table->dateTime('date_time')->comment('Дата и время сессии');

            // Вместимость
            $table->integer('total_tickets_available')->default(0)->comment('Всего билетов под продажу');
            $table->decimal('total_amount_sold', 12, 2)->default(0)->comment('Сумма продаж');

            // Детализация
            $table->integer('total_tickets_sold')->default(0)->comment('Кол-во проданных билетов');
            $table->integer('free_tickets_count')->default(0)->comment('Кол-во не проданных билетов');
            $table->integer('invitation_tickets_count')->default(0)->comment('Кол-во пригласительных билетов');
            $table->integer('refunded_tickets_count')->default(0)->comment('Кол-во возвращенных билетов');

            $table->timestamps();

            // Индексы для часто используемых запросов
            $table->index('event_name');
            $table->index('date_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('statistics');
    }
};
