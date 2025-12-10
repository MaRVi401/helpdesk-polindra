<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TestPage extends Controller
{
  public function index()
  { 
    $pageConfigs = ['myLayout' => 'horizontal'];
    return view('content.apps.mahasiswa.faq', ['pageConfigs' => $pageConfigs]);
  }
}