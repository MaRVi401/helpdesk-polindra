<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\Jabatan;
use App\Models\Tiket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TestPage extends Controller
{
  public function index()
  {
    $user = auth()->user();
    return view('content.pages.test', compact('user'), ['pageConfigs' => $this->pageConfigs]);
  }
}