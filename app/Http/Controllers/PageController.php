<?php

namespace App\Http\Controllers;

use App\Support\Catalog;
use App\Support\PartnerStore;
use Illuminate\View\View;

class PageController extends Controller
{
    public function home(): View
    {
        return view('home', [
            'featuredProducts' => Catalog::featured(),
            'stats' => Catalog::stats(),
            'partners' => PartnerStore::active(),
        ]);
    }

    public function about(): View
    {
        return view('pages.about', [
            'stats' => Catalog::stats(),
        ]);
    }

    public function collaboration(): View
    {
        return view('pages.collaboration', [
            'partners' => PartnerStore::active(),
        ]);
    }

    public function contact(): View
    {
        return view('pages.contact');
    }
}
