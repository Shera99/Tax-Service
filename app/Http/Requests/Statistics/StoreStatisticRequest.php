<?php

namespace App\Http\Requests\Statistics;

use Illuminate\Foundation\Http\FormRequest;

class StoreStatisticRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'event_name' => ['required', 'string', 'max:255'],
            'organization_name' => ['required', 'string', 'max:255'],
            'venue_name' => ['nullable', 'string', 'max:255'],
            'date_time' => ['required', 'date_format:Y-m-d H:i:s'],
            'total_tickets_available' => ['required', 'integer', 'min:0'],
            'total_amount_sold' => ['required', 'numeric', 'min:0'],
            'total_tickets_sold' => ['required', 'integer', 'min:0'],
            'free_tickets_count' => ['required', 'integer', 'min:0'],
            'invitation_tickets_count' => ['required', 'integer', 'min:0'],
            'refunded_tickets_count' => ['required', 'integer', 'min:0'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'event_name.required' => 'Название события обязательно',
            'event_name.max' => 'Название события не должно превышать 255 символов',
            'organization_name.required' => 'Название организации обязательно',
            'organization_name.max' => 'Название организации не должно превышать 255 символов',
            'venue_name.max' => 'Название площадки не должно превышать 255 символов',
            'date_time.required' => 'Дата и время обязательны',
            'date_time.date_format' => 'Дата и время должны быть в формате Y-m-d H:i:s',
            'total_tickets_available.required' => 'Количество доступных билетов обязательно',
            'total_tickets_available.integer' => 'Количество доступных билетов должно быть целым числом',
            'total_tickets_available.min' => 'Количество доступных билетов не может быть отрицательным',
            'total_amount_sold.required' => 'Сумма продаж обязательна',
            'total_amount_sold.numeric' => 'Сумма продаж должна быть числом',
            'total_amount_sold.min' => 'Сумма продаж не может быть отрицательной',
            'total_tickets_sold.required' => 'Количество проданных билетов обязательно',
            'total_tickets_sold.integer' => 'Количество проданных билетов должно быть целым числом',
            'total_tickets_sold.min' => 'Количество проданных билетов не может быть отрицательным',
            'free_tickets_count.required' => 'Количество непроданных билетов обязательно',
            'free_tickets_count.integer' => 'Количество непроданных билетов должно быть целым числом',
            'free_tickets_count.min' => 'Количество непроданных билетов не может быть отрицательным',
            'invitation_tickets_count.required' => 'Количество пригласительных билетов обязательно',
            'invitation_tickets_count.integer' => 'Количество пригласительных билетов должно быть целым числом',
            'invitation_tickets_count.min' => 'Количество пригласительных билетов не может быть отрицательным',
            'refunded_tickets_count.required' => 'Количество возвращенных билетов обязательно',
            'refunded_tickets_count.integer' => 'Количество возвращенных билетов должно быть целым числом',
            'refunded_tickets_count.min' => 'Количество возвращенных билетов не может быть отрицательным',
        ];
    }
}
