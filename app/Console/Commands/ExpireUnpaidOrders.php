<?php

namespace App\Console\Commands;

use App\Services\UnpaidOrderExpirer;
use Illuminate\Console\Command;

class ExpireUnpaidOrders extends Command
{
    protected $signature = 'orders:expire';

    protected $description = 'Batalkan otomatis pesanan yang tidak dibayar, atau buktinya ditolak dan tidak diunggah ulang, melewati batas waktu';

    public function handle(UnpaidOrderExpirer $expirer): int
    {
        $count = $expirer->expire();

        $this->info("{$count} pesanan kedaluwarsa dibatalkan.");

        return self::SUCCESS;
    }
}
