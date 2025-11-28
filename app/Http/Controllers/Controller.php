<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;

abstract class Controller
{
    // Set Layout berdasarkan Role
    protected $pageConfigs = [];

    public function __construct()
    {
        $this->setPageConfigs();
    }

    protected function setPageConfigs()
    {
        $user = Auth::user();

        $layout = 'vertical'; // default
        if ($user && $user->role === 'mahasiswa') {
            $layout = 'horizontal';
        }

        $this->pageConfigs = [
            'myLayout' => $layout,
            'navbarFixed' => true,
            'footerFixed' => false,
        ];
    }
}