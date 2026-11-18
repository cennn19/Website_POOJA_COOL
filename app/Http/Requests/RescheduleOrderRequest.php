<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RescheduleOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'jadwal_tanggal' => ['required', 'date', 'after_or_equal:today'],
            'jadwal_sesi' => ['required', 'string', Rule::in(array_keys(config('booking.sesi', [])))],
            'paksa' => ['nullable', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'jadwal_tanggal.required' => 'Tanggal kunjungan baru wajib diisi.',
            'jadwal_tanggal.after_or_equal' => 'Tanggal kunjungan baru tidak boleh sebelum hari ini.',
            'jadwal_sesi.required' => 'Pilih sesi kunjungan baru.',
            'jadwal_sesi.in' => 'Sesi kunjungan tidak valid.',
        ];
    }
}
