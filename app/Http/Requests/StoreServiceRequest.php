<?php

namespace App\Http\Requests;

use App\Models\Service;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreServiceRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:150'],
            'satuan' => ['required', Rule::in(Service::SATUAN)],
            'price' => ['required', 'integer', 'min:0', 'max:100000000'],
            'harga_mulai' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama jenis pekerjaan wajib diisi.',
            'satuan.in' => 'Satuan harus unit atau meter.',
            'price.required' => 'Harga wajib diisi (isi 0 jika belum ada; layanan tidak akan tampil).',
            'price.integer' => 'Harga harus berupa angka tanpa titik atau koma.',
        ];
    }
}
