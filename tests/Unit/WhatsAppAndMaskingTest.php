<?php

namespace Tests\Unit;

use App\Support\WhatsApp;
use PHPUnit\Framework\TestCase;

class WhatsAppAndMaskingTest extends TestCase
{
    public function test_normalizes_indonesian_numbers(): void
    {
        $this->assertSame('6281234567890', WhatsApp::normalize('0812-3456-7890'));
        $this->assertSame('6281234567890', WhatsApp::normalize('+62 812 3456 7890'));
        $this->assertSame('6281234567890', WhatsApp::normalize('81234567890'));
        $this->assertSame('6281234567890', WhatsApp::normalize('6281234567890'));
    }

    public function test_displays_local_format(): void
    {
        $this->assertSame('0812-3456-7890', WhatsApp::display('6281234567890'));
    }

    public function test_builds_link_with_encoded_message(): void
    {
        $this->assertSame('https://wa.me/6281234567890', WhatsApp::link('0812 3456 7890'));
        $this->assertSame(
            'https://wa.me/6281234567890?text=Halo%20admin%0AKode%3A%20HVC-1',
            WhatsApp::link('081234567890', "Halo admin\nKode: HVC-1")
        );
    }

    public function test_masks_phone(): void
    {
        $this->assertSame('0812****7890', mask_phone('081234567890'));
        $this->assertSame('0812****7890', mask_phone('+62 812-3456-7890'));
        $this->assertSame('***', mask_phone('123'));
    }

    public function test_masks_address(): void
    {
        $this->assertSame('•••, Malang', mask_address('Jl. Melati No. 5, Lowokwaru, Malang'));
        $this->assertSame('Jl. •••', mask_address('Jl. Melati No. 5'));
    }

    public function test_masks_name(): void
    {
        $this->assertSame('Hendra W.', mask_name('Hendra Wijaya'));
        $this->assertSame('Siti N. A.', mask_name('Siti nur Aisyah'));
        $this->assertSame('Budi', mask_name('Budi'));
    }
}
