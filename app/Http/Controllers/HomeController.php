<?php

namespace App\Http\Controllers;

use App\Models\KategoriLayanan;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Tampilkan halaman utama (Home).
     */
    public function index(): View
    {
        // Daftar harga per kategori. Pekerjaan yang harganya belum diisi (0) tidak tampil.
        $kategoriLayanan = KategoriLayanan::katalog();

        return view('pages.home', compact('kategoriLayanan'));
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
