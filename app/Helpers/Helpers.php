<?php

namespace App\Helpers;
use App\Models\Unit;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class Helpers
{
  /**
   * Generate menu attributes for semi-dark mode
   *
   * @param bool $semiDarkEnabled Whether semi-dark mode is enabled
   * @return array HTML attributes for the menu element
   */

  public static function getLayoutByRole($role)
  {
    $layout = match ($role) {
      'mahasiswa' => 'horizontal',
      'kepala_unit' => 'vertical',
      'admin_unit' => 'vertical',
      'super_admin' => 'vertical',
      default => 'vertical'
    };

    return ['myLayout' => $layout];
  }
  public static function getMenuAttributes($semiDarkEnabled)
  {
    $attributes = [];

    if ($semiDarkEnabled) {
      $attributes['data-bs-theme'] = 'dark';
    }

    return $attributes;
  }

  /**
   * Filter menu items based on user role
   *
   * @param array $menuData Array of menu items
   * @param string $userRole Current user's role
   * @return array Filtered menu items
   */

  /**
   * Get vertical menu with dynamic service submenu
   *
   * @return array Complete menu data (static + dynamic)
   */
  public static function getVerticalMenuData()
  {
    // Ambil menu statis dari JSON
    $menuJson = file_get_contents(base_path('resources/menu/verticalMenu.json'));
    $menuData = json_decode($menuJson);

    // Cari menu "Layanan" dan inject submenu dinamis
    foreach ($menuData->menu as $key => $menu) {
      if (isset($menu->slug) && $menu->slug === 'service') {
        // Generate submenu dinamis dari database dengan cache
        $menuData->menu[$key]->submenu = Cache::remember('service_submenu', 3600, function () {
          return self::generateServiceSubmenu();
        });
        break;
      }
    }

    return $menuData;
  }

  /**
   * Generate dynamic service submenu from units
   *
   * @return array Service submenu items
   */
  private static function generateServiceSubmenu()
  {
    $submenu = [];
    $units = Unit::orderBy('nama_unit')->get();

    foreach ($units as $unit) {
      $submenu[] = (object) [
        'url' => '/service/' . $unit->slug,
        'name' => $unit->nama_unit,
        'slug' => [
          'service.unit',
          'service.show',
          'service.edit'
        ],
        'slug_param' => $unit->slug,
        'roles' => ['super_admin', 'kepala_unit']
      ];
    }

    return $submenu;
  }

  /**
   * Clear service menu cache
   * Call this method when units are created/updated/deleted
   */
  public static function clearServiceMenuCache()
  {
    Cache::forget('service_submenu');
  }

  public static function filterMenuByRole($menuData, $userRole)
  {
    $filteredMenu = [];

    foreach ($menuData as $menuItem) {
      // Convert object to array for easier manipulation
      $item = is_object($menuItem) ? clone $menuItem : (object) $menuItem;

      // Skip if menu has roles restriction and user doesn't have access
      if (isset($item->roles) && is_array($item->roles)) {
        if (!in_array($userRole, $item->roles)) {
          continue;
        }
      }

      // Filter submenu if exists
      if (isset($item->submenu) && is_array($item->submenu)) {
        $filteredSubmenu = [];

        foreach ($item->submenu as $subItem) {
          $subItemObj = is_object($subItem) ? clone $subItem : (object) $subItem;

          // Check if submenu item has role restriction
          if (isset($subItemObj->roles) && is_array($subItemObj->roles)) {
            if (in_array($userRole, $subItemObj->roles)) {
              $filteredSubmenu[] = $subItemObj;
            }
          } else {
            // If no role restriction, include the submenu item
            $filteredSubmenu[] = $subItemObj;
          }
        }

        // Only add parent menu if it has accessible submenu items
        if (!empty($filteredSubmenu)) {
          $item->submenu = $filteredSubmenu;
          $filteredMenu[] = $item;
        } elseif (!isset($item->submenu)) {
          // If it's not a parent menu (no submenu), add it directly
          $filteredMenu[] = $item;
        }
      } else {
        // Menu item without submenu
        $filteredMenu[] = $item;
      }
    }

    return $filteredMenu;
  }

  /**
   * Check if user has permission to access a specific menu/route
   *
   * @param string $slug Menu slug or route name
   * @param string $userRole Current user's role
   * @param array $menuData Complete menu data
   * @return bool
   */
  public static function hasMenuAccess($slug, $userRole, $menuData)
  {
    foreach ($menuData as $menuItem) {
      $item = is_object($menuItem) ? $menuItem : (object) $menuItem;

      // Check main menu item
      if (isset($item->slug) && $item->slug === $slug) {
        if (isset($item->roles) && is_array($item->roles)) {
          return in_array($userRole, $item->roles);
        }
        return true; // No role restriction means accessible
      }

      // Check submenu items
      if (isset($item->submenu) && is_array($item->submenu)) {
        foreach ($item->submenu as $subItem) {
          $subItemObj = is_object($subItem) ? $subItem : (object) $subItem;

          if (isset($subItemObj->slug) && $subItemObj->slug === $slug) {
            if (isset($subItemObj->roles) && is_array($subItemObj->roles)) {
              return in_array($userRole, $subItemObj->roles);
            }
            return true;
          }
        }
      }
    }

    return false;
  }

  public static function appClasses()
  {

    $data = config('custom.custom');


    // default data array
    $DefaultData = [
      'myLayout' => 'vertical',
      'myTheme' => 'light',
      'mySkins' => 'default',
      'hasSemiDark' => false,
      'myRTLMode' => true,
      'hasCustomizer' => true,
      'showDropdownOnHover' => true,
      'displayCustomizer' => true,
      'contentLayout' => 'compact',
      'headerType' => 'fixed',
      'navbarType' => 'sticky',
      'menuFixed' => true,
      'menuCollapsed' => false,
      'footerFixed' => false,
      'customizerControls' => [
        'color',
        'theme',
        'skins',
        'semiDark',
        'layoutCollapsed',
        'layoutNavbarOptions',
        'headerType',
        'contentLayout',
        'rtl'
      ],
      //   'defaultLanguage'=>'en',
    ];

    // if any key missing of array from custom.php file it will be merge and set a default value from dataDefault array and store in data variable
    $data = array_merge($DefaultData, $data);

    // All options available in the template
    $allOptions = [
      'myLayout' => ['vertical', 'horizontal', 'blank', 'front'],
      'menuCollapsed' => [true, false],
      'hasCustomizer' => [true, false],
      'showDropdownOnHover' => [true, false],
      'displayCustomizer' => [true, false],
      'contentLayout' => ['compact', 'wide'],
      'headerType' => ['fixed', 'static'],
      'navbarType' => ['sticky', 'static', 'hidden'],
      'myTheme' => ['light', 'dark', 'system'],
      'mySkins' => ['default', 'bordered', 'raspberry'],
      'hasSemiDark' => [true, false],
      'myRTLMode' => [true, false],
      'menuFixed' => [true, false],
      'footerFixed' => [true, false],
      'customizerControls' => [],
      // 'defaultLanguage'=>array('en'=>'en','fr'=>'fr','de'=>'de','ar'=>'ar'),
    ];

    //if myLayout value empty or not match with default options in custom.php config file then set a default value
    foreach ($allOptions as $key => $value) {
      if (array_key_exists($key, $DefaultData)) {
        if (gettype($DefaultData[$key]) === gettype($data[$key])) {
          // data key should be string
          if (is_string($data[$key])) {
            // data key should not be empty
            if (isset($data[$key]) && $data[$key] !== null) {
              // data key should not be exist inside allOptions array's sub array
              if (!array_key_exists($data[$key], $value)) {
                // ensure that passed value should be match with any of allOptions array value
                $result = array_search($data[$key], $value, 'strict');
                if (empty($result) && $result !== 0) {
                  $data[$key] = $DefaultData[$key];
                }
              }
            } else {
              // if data key not set or
              $data[$key] = $DefaultData[$key];
            }
          }
        } else {
          $data[$key] = $DefaultData[$key];
        }
      }
    }
    $themeVal = $data['myTheme'] == "dark" ? "dark" : "light";
    $themeUpdatedVal = $data['myTheme'] == "dark" ? "dark" : $data['myTheme'];

    // Determine if the layout is admin or front based on template name
    $layoutName = $data['myLayout'];
    $isAdmin = !Str::contains($layoutName, 'front');

    $modeCookieName = $isAdmin ? 'admin-mode' : 'front-mode';
    $colorPrefCookieName = $isAdmin ? 'admin-colorPref' : 'front-colorPref';
    $primaryColorCookieName = $isAdmin ? 'admin-primaryColor' : 'front-primaryColor';

    // Get primary color from custom.php if explicitly set
    $primaryColor = null;
    if (array_key_exists('primaryColor', $data)) {
      $primaryColor = $data['primaryColor'];
    }

    // Check for primary color in cookie
    if (isset($_COOKIE[$primaryColorCookieName])) {
      $primaryColor = $_COOKIE[$primaryColorCookieName];
    }

    // Determine style based on cookies, only if not 'blank-layout'
    if ($layoutName !== 'blank') {
      if (isset($_COOKIE[$modeCookieName])) {
        $themeVal = $_COOKIE[$modeCookieName];
        if ($themeVal === 'system') {
          $themeVal = isset($_COOKIE[$colorPrefCookieName]) ? $_COOKIE[$colorPrefCookieName] : 'light';
        }
        $themeUpdatedVal = $_COOKIE[$modeCookieName];
      }
    }

    // Define standardized cookie names
    $skinCookieName = 'customize_skin';
    $semiDarkCookieName = 'customize_semi_dark';

    // Process skin and semi-dark settings only for admin layouts
    if ($isAdmin) {
      // Get skin from cookie or fall back to config
      $skinFromCookie = isset($_COOKIE[$skinCookieName]) ? $_COOKIE[$skinCookieName] : null;
      $configSkin = isset($data['mySkins']) ? $data['mySkins'] : 'default';
      $skinName = $skinFromCookie ?: $configSkin;

      // Get semi-dark setting from cookie or fall back to config
      $semiDarkFromCookie = isset($_COOKIE[$semiDarkCookieName]) ? $_COOKIE[$semiDarkCookieName] : null;
      // Ensure we have a proper boolean conversion
      $semiDarkEnabled = $semiDarkFromCookie !== null ?
        filter_var($semiDarkFromCookie, FILTER_VALIDATE_BOOLEAN) :
        (bool) $data['hasSemiDark'];
    } else {
      // For front-end layouts, use defaults
      $skinName = 'default';
      $semiDarkEnabled = false;
    }

    // Get menu Collapsed state from cookie or fall back to config
    $menuCollapsedFromCookie = isset($_COOKIE['LayoutCollapsed']) ? $_COOKIE['LayoutCollapsed'] : $data['menuCollapsed'];

    // Get content layout from cookie or fall back to config
    $contentLayoutFromCookie = isset($_COOKIE['contentLayout']) ? $_COOKIE['contentLayout'] : $data['contentLayout'];

    // Get header type from cookie or fall back to config
    $navbarTypeFromCookie = isset($_COOKIE['navbarType']) ? $_COOKIE['navbarType'] : $data['navbarType'];

    // Get Header type from cookie or fall back to config
    $headerTypeFromCookie = isset($_COOKIE['headerType']) ? $_COOKIE['headerType'] : $data['headerType'];

    $directionVal = isset($_COOKIE['direction']) ? ($_COOKIE['direction'] === 'true' ? 'rtl' : 'ltr') : $data['myRTLMode'];

    //layout classes
    $layoutClasses = [
      'layout' => $data['myLayout'],
      'skins' => $data['mySkins'],
      'skinName' => $skinName,
      'semiDark' => $semiDarkEnabled,
      'color' => $primaryColor,
      'theme' => $themeVal,
      'themeOpt' => $data['myTheme'],
      'themeOptVal' => $themeUpdatedVal,
      'rtlMode' => $data['myRTLMode'],
      'textDirection' => $directionVal,
      'menuCollapsed' => $menuCollapsedFromCookie,
      'hasCustomizer' => $data['hasCustomizer'],
      'showDropdownOnHover' => $data['showDropdownOnHover'],
      'displayCustomizer' => $data['displayCustomizer'],
      'contentLayout' => $contentLayoutFromCookie,
      'headerType' => $headerTypeFromCookie,
      'navbarType' => $navbarTypeFromCookie,
      'menuFixed' => $data['menuFixed'],
      'footerFixed' => $data['footerFixed'],
      'customizerControls' => $data['customizerControls'],
      'menuAttributes' => self::getMenuAttributes($semiDarkEnabled),
    ];

    // sidebar Collapsed
    if ($layoutClasses['menuCollapsed'] === 'true' || $layoutClasses['menuCollapsed'] === true) {
      $layoutClasses['menuCollapsed'] = 'layout-menu-collapsed';
    } else {
      $layoutClasses['menuCollapsed'] = '';
    }

    // Header Type
    if ($layoutClasses['headerType'] == 'fixed') {
      $layoutClasses['headerType'] = 'layout-menu-fixed';
    }
    // Navbar Type
    if ($layoutClasses['navbarType'] == 'sticky') {
      $layoutClasses['navbarType'] = 'layout-navbar-fixed';
    } elseif ($layoutClasses['navbarType'] == 'static') {
      $layoutClasses['navbarType'] = '';
    } else {
      $layoutClasses['navbarType'] = 'layout-navbar-hidden';
    }

    // Menu Fixed
    if ($layoutClasses['menuFixed'] == true) {
      $layoutClasses['menuFixed'] = 'layout-menu-fixed';
    }


    // Footer Fixed
    if ($layoutClasses['footerFixed'] == true) {
      $layoutClasses['footerFixed'] = 'layout-footer-fixed';
    }

    // RTL Layout/Mode
    if ($layoutClasses['rtlMode'] == true) {
      $layoutClasses['rtlMode'] = 'rtl';
      $layoutClasses['textDirection'] = isset($_COOKIE['direction']) ? ($_COOKIE['direction'] === 'true' ? 'rtl' : 'ltr') : 'rtl';
    } else {
      $layoutClasses['rtlMode'] = 'ltr';
      $layoutClasses['textDirection'] = isset($_COOKIE['direction']) && $_COOKIE['direction'] === 'true' ? 'rtl' : 'ltr';
    }

    // Show DropdownOnHover for Horizontal Menu
    if ($layoutClasses['showDropdownOnHover'] == true) {
      $layoutClasses['showDropdownOnHover'] = true;
    } else {
      $layoutClasses['showDropdownOnHover'] = false;
    }

    // To hide/show display customizer UI, not js
    if ($layoutClasses['displayCustomizer'] == true) {
      $layoutClasses['displayCustomizer'] = true;
    } else {
      $layoutClasses['displayCustomizer'] = false;
    }

    return $layoutClasses;
  }

  public static function updatePageConfig($pageConfigs)
  {
    $demo = 'custom';
    if (isset($pageConfigs)) {
      if (count($pageConfigs) > 0) {
        foreach ($pageConfigs as $config => $val) {
          Config::set('custom.' . $demo . '.' . $config, $val);
        }
      }
    }
  }

  /**
   * Generate CSS for primary color
   *
   * @param string $color Hex color code for primary color
   * @return string CSS for primary color
   */
  public static function generatePrimaryColorCSS($color)
  {
    if (!$color)
      return '';

    // Check if the color actually came from a cookie or explicit configuration
    // Don't generate CSS if there's no specific need for a custom color
    $configColor = config('custom.custom.primaryColor', null);
    $isFromCookie = isset($_COOKIE['admin-primaryColor']) || isset($_COOKIE['front-primaryColor']);

    if (!$configColor && !$isFromCookie)
      return '';

    $r = hexdec(substr($color, 1, 2));
    $g = hexdec(substr($color, 3, 2));
    $b = hexdec(substr($color, 5, 2));

    // Calculate contrast color based on YIQ formula
    $yiq = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;
    $contrastColor = ($yiq >= 150) ? '#000' : '#fff';

    return <<<CSS
:root, [data-bs-theme=light], [data-bs-theme=dark] {
  --bs-primary: {$color};
  --bs-primary-rgb: {$r}, {$g}, {$b};
  --bs-primary-bg-subtle: rgba({$r}, {$g}, {$b}, 0.1);
  --bs-primary-border-subtle: rgba({$r}, {$g}, {$b}, 0.3);
  --bs-primary-contrast: {$contrastColor};
}
CSS;
  }
}