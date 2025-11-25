<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;

class AuthPage extends Controller
{
  public function authPage()
  {
    $pageConfigs = ['myLayout' => 'blank'];
    return view('content.auth.login', ['pageConfigs' => $pageConfigs]);
  }
}