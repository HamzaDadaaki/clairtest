<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\AnalyticsStore;
use App\Support\Catalog;
use App\Support\ContactInbox;
use App\Support\PartnerStore;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $analytics = AnalyticsStore::summary();
        $products = Catalog::all();
        $contacts = ContactInbox::all();
        $partners = PartnerStore::all();

        return view('admin.dashboard', [
            'products' => $products,
            'contacts' => $contacts,
            'partners' => $partners,
            'analytics' => $analytics,
        ]);
    }
}
