<?php

namespace App\Http\Requests;

use App\Models\Service;
use App\Services\BookingSchedule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreOrderRequest extends FormRequest
{
    /** Maksimal jenis pekerjaan berbeda dalam satu pesanan. */
    public const MAX_ITEMS = 20;

    public const PHONE_MIN_DIGITS = 9;

    public const PHONE_MAX_DIGITS = 15;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Potong daftar item yang terlalu panjang sebelum validasi, agar request berisi ribuan item
     * tidak memicu ribuan query. Satu item lebih disisakan supaya aturan "max" tetap gagal.
     */
    protected function prepareForValidation(): void
    {
        $items = $this->input('items');

        if (is_array($items) && count($items) > self::MAX_ITEMS) {
            $this->merge(['items' => array_slice($items, 0, self::MAX_ITEMS + 1)]);
        }
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
            'phone' => ['required', 'string', 'max:25', 'regex:/^[0-9+\-\s()]+$/', function (string $attribute, mixed $value, \Closure $fail) {
                // Harus berisi nomor sungguhan, bukan hanya simbol (misal "---").
                $digits = strlen((string) preg_replace('/\D+/', '', (string) $value));

                if ($digits < self::PHONE_MIN_DIGITS || $digits > self::PHONE_MAX_DIGITS) {
                    $fail('Nomor telepon harus berisi '.self::PHONE_MIN_DIGITS.'–'.self::PHONE_MAX_DIGITS.' digit angka.');
                }
            }],
            'address' => ['required', 'string', 'max:500'],
            'schedule_date' => ['required', 'date', 'after_or_equal:today', 'before_or_equal:'.now()->addDays((int) config('booking.maks_hari_ke_depan', 30))->toDateString()],
            'sesi' => ['required', 'string', Rule::in(array_keys(config('booking.sesi', [])))],
            'notes' => ['nullable', 'string', 'max:1000'],
            // Batas jumlah baris mencegah pesanan raksasa dan beban query berlebihan dari satu request.
            'items' => ['required', 'array', 'min:1', 'max:'.self::MAX_ITEMS],
            'items.*' => ['array'],
            'items.*.kategori_id' => ['required', 'integer', Rule::exists('kategori_layanan', 'id')->where('aktif', true)],
            // Layanan berharga 0 (harga belum diisi admin) dianggap belum bisa dipesan.
            // distinct: layanan yang sama tidak boleh dikirim berulang untuk mengakali batas 20 unit.
            'items.*.service_id' => ['required', 'integer', 'distinct', Rule::exists('services', 'id')->where('is_active', true)->where(fn ($q) => $q->where('price', '>', 0))],
            'items.*.qty' => ['required', 'integer', 'min:1', 'max:20'],
        ];
    }

    /**
     * Aturan jadwal yang bergantung pada waktu sekarang & kuota (dicek setelah aturan dasar lolos).
     *
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                // Jenis pekerjaan harus berasal dari kategori yang dipilih.
                if ($validator->errors()->has('items')) {
                    return;
                }

                foreach ((array) $this->input('items', []) as $index => $item) {
                    if ($validator->errors()->hasAny(["items.$index.kategori_id", "items.$index.service_id"])) {
                        continue;
                    }

                    $cocok = Service::query()
                        ->whereKey($item['service_id'] ?? 0)
                        ->where('kategori_layanan_id', $item['kategori_id'] ?? 0)
                        ->exists();

                    if (! $cocok) {
                        $validator->errors()->add("items.$index.service_id", 'Jenis pekerjaan tidak sesuai dengan kategori yang dipilih.');
                    }
                }
            },
            function (Validator $validator) {
                if ($validator->errors()->hasAny(['schedule_date', 'sesi'])) {
                    return;
                }

                $schedule = app(BookingSchedule::class);
                $date = $this->input('schedule_date');
                $sesi = $this->input('sesi');

                if ($error = $schedule->timeRuleError($date, $sesi)) {
                    $validator->errors()->add('sesi', $error);

                    return;
                }

                if ($schedule->isFull($date, $sesi)) {
                    $validator->errors()->add('sesi', $schedule->fullMessage($date, $sesi));
                }
            },
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
            'schedule_date.before_or_equal' => 'Tanggal kunjungan maksimal '.config('booking.maks_hari_ke_depan', 30).' hari ke depan.',
            'sesi.required' => 'Pilih sesi kunjungan (Pagi atau Siang).',
            'sesi.in' => 'Sesi kunjungan tidak valid.',
            'items.required' => 'Silakan pilih minimal 1 layanan sebelum melanjutkan.',
            'items.min' => 'Silakan pilih minimal 1 layanan.',
            'items.max' => 'Maksimal '.self::MAX_ITEMS.' jenis pekerjaan dalam satu pesanan.',
            'items.*.service_id.distinct' => 'Jenis pekerjaan yang sama dipilih lebih dari sekali. Ubah jumlahnya saja.',
            'items.*.kategori_id.required' => 'Pilih kategori layanan terlebih dahulu.',
            'items.*.kategori_id.exists' => 'Kategori layanan yang dipilih tidak tersedia.',
            'items.*.service_id.required' => 'Layanan yang dipilih tidak valid.',
            'items.*.service_id.exists' => 'Salah satu layanan yang dipilih sudah tidak tersedia. Silakan pilih ulang layanan Anda.',
            'items.*.qty.required' => 'Jumlah layanan wajib diisi.',
            'items.*.qty.min' => 'Jumlah minimal adalah 1.',
            'items.*.qty.max' => 'Jumlah maksimal per layanan adalah 20.',
        ];
    }
}
