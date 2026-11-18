<?php

namespace App\Http\Requests;

use App\Models\Service;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Simpan sekaligus harga, satuan, "Mulai dari", status aktif, dan urutan banyak jenis pekerjaan.
 *
 * Bentuk input: services[{id}][price|satuan|harga_mulai|is_active|sort_order].
 */
class BulkUpdateServicesRequest extends FormRequest
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
            'services' => ['required', 'array'],
            'services.*.price' => ['required', 'integer', 'min:0', 'max:100000000'],
            'services.*.satuan' => ['required', Rule::in(Service::SATUAN)],
            'services.*.harga_mulai' => ['nullable', 'boolean'],
            'services.*.is_active' => ['nullable', 'boolean'],
            'services.*.sort_order' => ['required', 'integer', 'min:0', 'max:9999'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'services.required' => 'Tidak ada data layanan yang dikirim.',
            'services.*.price.required' => 'Harga wajib diisi.',
            'services.*.price.integer' => 'Harga harus berupa angka tanpa titik atau koma.',
            'services.*.price.min' => 'Harga tidak boleh negatif.',
            'services.*.price.max' => 'Harga terlalu besar.',
            'services.*.satuan.in' => 'Satuan harus unit atau meter.',
            'services.*.sort_order.required' => 'Urutan wajib diisi.',
            'services.*.sort_order.integer' => 'Urutan harus berupa angka.',
        ];
    }
}
