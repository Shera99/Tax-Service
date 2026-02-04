@extends('layouts.app')

@section('title', 'API Ключи')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-key text-blue-500 mr-2"></i>API Ключи
        </h1>
        <a href="{{ route('api-keys.create') }}" class="mt-4 sm:mt-0 inline-flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors">
            <i class="fas fa-plus mr-2"></i>Создать ключ
        </a>
    </div>

    <!-- Info -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-500"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-blue-700">
                    API ключи используются внешними сервисами для отправки статистики. Каждый сервис должен иметь свой уникальный ключ.
                </p>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Название</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Публичный ключ</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Статус</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Последнее использование</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Создан</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Действия</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($apiKeys as $apiKey)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-key text-white text-xs"></i>
                                </div>
                                <span class="text-sm font-medium text-gray-900">{{ $apiKey->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <code class="text-xs bg-gray-100 px-2 py-1 rounded">{{ $apiKey->public_key }}</code>
                        </td>
                        <td class="px-6 py-4">
                            @if($apiKey->is_active)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i>Активен
                            </span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <i class="fas fa-times-circle mr-1"></i>Неактивен
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $apiKey->last_used_at ? $apiKey->last_used_at->format('d.m.Y H:i') : 'Никогда' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $apiKey->created_at->format('d.m.Y H:i') }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('api-keys.show', $apiKey) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Подробнее">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <form action="{{ route('api-keys.toggle', $apiKey) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="p-2 {{ $apiKey->is_active ? 'text-yellow-600 hover:bg-yellow-50' : 'text-green-600 hover:bg-green-50' }} rounded-lg transition-colors" title="{{ $apiKey->is_active ? 'Деактивировать' : 'Активировать' }}">
                                        <i class="fas {{ $apiKey->is_active ? 'fa-pause' : 'fa-play' }}"></i>
                                    </button>
                                </form>
                                <form action="{{ route('api-keys.destroy', $apiKey) }}" method="POST" class="inline" onsubmit="return confirm('Вы уверены, что хотите удалить этот API ключ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Удалить">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="text-gray-400">
                                <i class="fas fa-key text-4xl mb-3"></i>
                                <p>API ключи не найдены</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($apiKeys->hasPages())
        <div class="px-6 py-3 border-t border-gray-200 bg-gray-50">
            {{ $apiKeys->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
