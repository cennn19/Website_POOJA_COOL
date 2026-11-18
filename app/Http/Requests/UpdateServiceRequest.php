<?php

namespace App\Http\Requests;

use App\Models\Service;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateServiceRequest extends FormRequest
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
            'price' => ['required', 'integer', 'min:0', 'max:100000000'],
            'satuan' => ['sometimes', 'required', Rule::in(Service::SATUAN)],
            'harga_mulai' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['sometimes', 'required', 'integer', 'min:0', 'max:9999'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'price.required' => 'Harga wajib diisi.',
            'price.integer' => 'Harga harus berupa angka tanpa titik atau koma.',
            'price.min' => 'Harga tidak boleh negatif.',
            'satuan.in' => 'Satuan harus unit atau meter.',
            'sort_order.integer' => 'Urutan harus berupa angka.',
        ];
    }
}
