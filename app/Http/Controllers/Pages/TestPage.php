<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TestPage extends Controller
{
  public function home()
  { 
    // $pageConfigs = ['myLayout' => 'blank'];
    return view('content.pages.dashboard');
  }
}