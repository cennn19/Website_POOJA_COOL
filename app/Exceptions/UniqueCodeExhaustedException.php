<?php

namespace App\Exceptions;

use DomainException;

class UniqueCodeExhaustedException extends DomainException
{
    public function __construct()
    {
        parent::__construct('Sistem sedang menerima terlalu banyak pesanan yang belum selesai. Silakan coba lagi beberapa saat lagi atau hubungi admin via WhatsApp.');
    }
}
