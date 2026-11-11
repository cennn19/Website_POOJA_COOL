<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:25', 'regex:/^[0-9+\-\s()]+$/'],
            'address' => ['required', 'string', 'max:500'],
            'schedule_date' => ['required', 'date', 'after_or_equal:today'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.service_id' => ['required', 'integer', 'exists:services,id'],
            'items.*.qty' => ['required', 'integer', 'min:1', 'max:20'],
        ];
    }

    /**
     * Get custom error messages in Indonesian.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.max' => 'Nama maksimal 100 karakter.',
            'phone.required' => 'Nomor telepon / WhatsApp wajib diisi.',
            'phone.regex' => 'Format nomor telepon tidak valid.',
            'address.required' => 'Alamat lengkap wajib diisi.',
            'address.max' => 'Alamat maksimal 500 karakter.',
            'schedule_date.required' => 'Tanggal kunjungan wajib ditentukan.',
            'schedule_date.after_or_equal' => 'Tanggal kunjungan tidak boleh sebelum hari ini.',
            'items.required' => 'Silakan pilih minimal 1 layanan sebelum melanjutkan.',
            'items.min' => 'Silakan pilih minimal 1 layanan.',
            'items.*.service_id.required' => 'Layanan yang dipilih tidak valid.',
            'items.*.service_id.exists' => 'Layanan yang dipilih tidak terdaftar di sistem.',
            'items.*.qty.required' => 'Jumlah layanan wajib diisi.',
            'items.*.qty.min' => 'Jumlah minimal adalah 1.',
            'items.*.qty.max' => 'Jumlah maksimal per layanan adalah 20 unit.',
        ];
    }
}
