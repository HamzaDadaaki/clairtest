<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\AnalyticsStore;
use Illuminate\View\View;

class AnalyticsAdminController extends Controller
{
    public function index(): View
    {
        return view('admin.analytics.index', [
            'analytics' => AnalyticsStore::summary(),
        ]);
    }
}
