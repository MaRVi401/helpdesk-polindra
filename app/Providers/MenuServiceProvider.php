<?php

namespace App\Providers;


use Illuminate\Support\Facades\View;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Staff;
use App\Models\Unit;

use Illuminate\Support\ServiceProvider;

class MenuServiceProvider extends ServiceProvider
{
  /**
   * Register services.
   */
  public function register(): void
  {
    //
  }

  /**
   * Bootstrap services.
   */
  public function boot(): void
  {

    View::composer('*', function ($view) {

    $verticalMenuJson = file_get_contents(base_path('resources/menu/verticalMenu.json'));
    $verticalMenuData = json_decode($verticalMenuJson);
    $horizontalMenuJson = file_get_contents(base_path('resources/menu/horizontalMenu.json'));
    $horizontalMenuData = json_decode($horizontalMenuJson);

    if (Auth::check()) {
                $user = Auth::user();
                
                // Asumsi: Role kepala unit diidentifikasi dengan string 'kepala_unit' atau 'kepala-unit'
                // Sesuaikan jika Anda menggunakan ID role (misal: role_id == 3)
                if ($user->role === 'kepala_unit') { 
                    $this->filterMenuForKepalaUnit($verticalMenuData);
                    // $this->filterMenuForKepalaUnit($horizontalMenuData); // Aktifkan jika perlu
                }
            }

    $this->app->make('view')->share('menuData', [$verticalMenuData, $horizontalMenuData]);
    });
  }

/**
     * Fungsi logika filtering menu Layanan
     */
    private function filterMenuForKepalaUnit($menuData)
    {
        $user = Auth::user();

        // 1. Ambil data Staff
        $staff = Staff::where('user_id', $user->id)->first();
        if (!$staff) return;

        // 2. Ambil SEMUA unit dimana staff ini menjadi kepala
        $units = Unit::where('kepala_id', $staff->id)->get();

        // Mapping Manual (Opsional)
        $slugMap = [
            'UPA TIK'       => ['layanan-upa-tik', 'service-upa-tik', 'unit-upa-tik', 'tik'],
            'Akademik'      => ['layanan-akademik', 'service-akademik', 'unit-akademik'],
            'Kemahasiswaan' => ['layanan-kemahasiswaan', 'service-kemahasiswaan'],
            'UPT Bahasa'    => ['layanan-upt-bahasa', 'service-upt-bahasa'],
        ];

        // 3. Loop Menu Utama
        foreach ($menuData->menu as $key => $menuItem) {
            // Deteksi apakah ini menu "Layanan" / "Service"
            $isServiceMenu = (
                (isset($menuItem->slug) && (is_array($menuItem->slug) ? in_array('service', $menuItem->slug) : $menuItem->slug === 'service')) ||
                (isset($menuItem->name) && stripos($menuItem->name, 'Layanan') !== false)
            );

            if ($isServiceMenu && isset($menuItem->submenu)) {
                $filteredSubmenu = [];

                // Hanya jalankan filter jika user memiliki unit. 
                // Jika $units kosong, $filteredSubmenu tetap [], nanti menu induk akan dihapus.
                if ($units->isNotEmpty()) {
                    foreach ($menuItem->submenu as $subItem) {
                        $keepItem = false;
                        
                        $normalizedMenuName = Str::slug($subItem->name); 
                        $menuSlug = isset($subItem->slug) ? $subItem->slug : '';

                        // Cek terhadap SETIAP unit yang dimiliki user
                        foreach ($units as $unit) {
                            $unitName = $unit->nama_unit;
                            $normalizedUnitName = Str::slug($unitName);

                            // A. Mapping Manual
                            if (isset($slugMap[$unitName]) && isset($subItem->slug)) {
                                $menuSlugsToCheck = is_array($subItem->slug) ? $subItem->slug : [$subItem->slug];
                                foreach ($menuSlugsToCheck as $s) {
                                    if (in_array($s, $slugMap[$unitName])) {
                                        $keepItem = true;
                                        break; 
                                    }
                                }
                            }

                            if ($keepItem) break;

                            // B. Pencocokan Nama
                            if (Str::contains($normalizedMenuName, $normalizedUnitName)) {
                                $keepItem = true;
                                break;
                            }

                            // C. Pencocokan Slug (Handle Array/String)
                            if (is_array($menuSlug)) {
                                foreach ($menuSlug as $s) {
                                    if (is_string($s) && Str::contains($s, $normalizedUnitName)) {
                                        $keepItem = true;
                                        break 2; 
                                    }
                                }
                            } elseif (is_string($menuSlug)) {
                                if (Str::contains($menuSlug, $normalizedUnitName)) {
                                    $keepItem = true;
                                    break; 
                                }
                            }
                        } // End Loop Units

                        if ($keepItem) {
                            $filteredSubmenu[] = $subItem;
                        }
                    }
                }

                // Update submenu dengan hasil filter
                $menuData->menu[$key]->submenu = $filteredSubmenu;

                // LOGIKA PENTING: Jika submenu kosong (karena tidak ada unit matched atau user bukan PIC),
                // HAPUS menu induk "Layanan" agar tidak muncul dropdown kosong.
                if (empty($filteredSubmenu)) {
                    unset($menuData->menu[$key]);
                }
            }
        }

        // Re-index array agar urutan JSON tetap benar (menghindari konversi jadi Object di JS)
        $menuData->menu = array_values($menuData->menu);
    }
}