<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UploadPaymentProofRequest extends FormRequest
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
            'proof' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    /**
     * Custom messages in Indonesian.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'proof.required' => 'File bukti transfer wajib diunggah.',
            'proof.image' => 'File yang diunggah harus berupa gambar.',
            'proof.mimes' => 'Format file yang didukung hanya JPG, JPEG, PNG, dan WEBP.',
            'proof.max' => 'Ukuran file gambar maksimal 2 MB (2048 KB).',
        ];
    }
}
