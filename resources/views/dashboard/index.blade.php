@extends('layouts.app')

@section('title', 'Статистика')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-chart-bar text-blue-500 mr-2"></i>Статистика мероприятий
            </h1>
            <span class="text-sm text-gray-500 mt-2 sm:mt-0">
                Всего записей: {{ $statistics->total() }}
            </span>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <form method="GET" action="{{ route('dashboard') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Search -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Поиск</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Мероприятие, организация, площадка..."
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                    <!-- Date from -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Дата от</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Date to -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Дата до</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Sort -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Сортировка</label>
                        <select name="sort"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="date_time" {{ request('sort') == 'date_time' ? 'selected' : '' }}>По дате
                                мероприятия</option>
                            <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>По дате
                                создания</option>
                            <option value="event_name" {{ request('sort') == 'event_name' ? 'selected' : '' }}>По названию
                            </option>
                            <option value="organization_name" {{ request('sort') == 'organization_name' ? 'selected' : '' }}>
                                По организации</option>
                            <option value="total_amount_sold" {{ request('sort') == 'total_amount_sold' ? 'selected' : '' }}>
                                По сумме продаж</option>
                            <option value="total_tickets_sold" {{ request('sort') == 'total_tickets_sold' ? 'selected' : '' }}>По проданным билетам</option>
                            <option value="total_tickets_available" {{ request('sort') == 'total_tickets_available' ? 'selected' : '' }}>По доступным билетам</option>
                        </select>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <!-- Direction toggle -->
                    <div class="flex items-center space-x-2">
                        <label class="text-sm text-gray-600">Направление:</label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="direction" value="desc" {{ request('direction', 'desc') == 'desc' ? 'checked' : '' }} class="form-radio text-blue-500">
                            <span class="ml-1 text-sm">По убыванию</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="direction" value="asc" {{ request('direction') == 'asc' ? 'checked' : '' }} class="form-radio text-blue-500">
                            <span class="ml-1 text-sm">По возрастанию</span>
                        </label>
                    </div>

                    <div class="flex-1"></div>

                    <a href="{{ route('dashboard') }}"
                        class="px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors">
                        <i class="fas fa-times mr-1"></i>Сбросить
                    </a>
                    <button type="submit"
                        class="px-6 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors">
                        <i class="fas fa-filter mr-1"></i>Применить
                    </button>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">ID
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Мероприятие</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Организация</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Площадка</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Дата/Время</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Количество заведенных билетов</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Продано билетов</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Свободных билетов</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Пригласительных билетов</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Возвращенных билетов</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Сумма продаж</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($statistics as $stat)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 text-sm text-gray-500">{{ $stat->id }}</td>
                                <td class="px-4 py-3">
                                    <div class="text-sm font-medium text-gray-900 max-w-xs truncate"
                                        title="{{ $stat->event_name }}">
                                        {{ $stat->event_name }}
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-sm text-gray-600 max-w-xs truncate" title="{{ $stat->organization_name }}">
                                        {{ $stat->organization_name }}
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-sm text-gray-600 max-w-xs truncate" title="{{ $stat->venue_name }}">
                                        {{ $stat->venue_name ?? '—' }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    {{ $stat->date_time->format('d.m.Y H:i') }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900 text-right font-medium">
                                    {{ number_format($stat->total_tickets_available, 0, '', ' ') }}
                                </td>
                                <td class="px-4 py-3 text-sm text-right">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        {{ number_format($stat->total_tickets_sold, 0, '', ' ') }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600 text-right">
                                    {{ number_format($stat->free_tickets_count, 0, '', ' ') }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600 text-right">
                                    {{ number_format($stat->invitation_tickets_count, 0, '', ' ') }}
                                </td>
                                <td class="px-4 py-3 text-sm text-right">
                                    @if($stat->refunded_tickets_count > 0)
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            {{ number_format($stat->refunded_tickets_count, 0, '', ' ') }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">0</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-right font-semibold text-gray-900 whitespace-nowrap">
                                    {{ number_format($stat->total_amount_sold, 2, ',', ' ') }} сом
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="px-4 py-12 text-center">
                                    <div class="text-gray-400">
                                        <i class="fas fa-inbox text-4xl mb-3"></i>
                                        <p>Данные не найдены</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($statistics->hasPages())
                <div class="px-4 py-3 border-t border-gray-200 bg-gray-50">
                    {{ $statistics->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection