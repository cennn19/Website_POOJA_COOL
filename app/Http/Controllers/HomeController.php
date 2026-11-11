<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Tampilkan halaman utama (Home).
     */
    public function index(): View
    {
        $featuredServices = Service::active()
            ->orderBy('sort_order')
            ->take(6)
            ->get();

        return view('pages.home', compact('featuredServices'));
    }

    /**
     * Tampilkan halaman Tentang Kami (About).
     */
    public function about(): View
    {
        return view('pages.about');
    }

    /**
     * Tampilkan halaman Kontak (Contact).
     */
    public function contact(): View
    {
        return view('pages.contact');
    }
}
