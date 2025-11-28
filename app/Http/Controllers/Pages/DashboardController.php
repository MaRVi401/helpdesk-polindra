<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\Jabatan;
use App\Models\Tiket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        return view('content.pages.dashboard', compact('user'),['pageConfigs' => $this->pageConfigs]);
    }
}