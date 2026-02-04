@extends('layouts.app')

@section('title', 'Создание API ключа')

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('api-keys.index') }}" class="text-gray-600 hover:text-gray-800 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Назад к списку
        </a>
        <h1 class="text-2xl font-bold text-gray-800 mt-4">
            <i class="fas fa-plus-circle text-blue-500 mr-2"></i>Создание API ключа
        </h1>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <form method="POST" action="{{ route('api-keys.store') }}" class="space-y-6">
            @csrf

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Название сервиса</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name') }}"
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
                    placeholder="Например: Kassir Main Service"
                >
                @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500">Укажите понятное название для идентификации сервиса</p>
            </div>

            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-yellow-500"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">Важно!</h3>
                        <p class="mt-1 text-sm text-yellow-700">
                            После создания вам будет показан секретный ключ. Сохраните его в надежном месте - он больше не будет показан!
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-4">
                <a href="{{ route('api-keys.index') }}" class="px-6 py-2 text-gray-600 hover:text-gray-800 transition-colors">
                    Отмена
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors">
                    <i class="fas fa-key mr-2"></i>Создать ключ
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
