<?php

namespace App\Http\Controllers\Pages;
use App\Http\Controllers\Controller;

class LandingPage extends Controller
{
  public function landingPage()
  {
    $pageConfigs = ['myLayout' => 'front'];
    return view('content.pages.landing', ['pageConfigs' => $pageConfigs]);
  }
}