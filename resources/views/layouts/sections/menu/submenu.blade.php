@php
  use Illuminate\Support\Facades\Route;
@endphp

<ul class="menu-sub">
  @if (isset($menu))
    @foreach ($menu as $submenu)
      {{-- active menu method --}}
      @php
        $activeClass = null;
        $active = $configData['layout'] === 'vertical' ? 'active open' : 'active';

        $currentRouteName = Route::currentRouteName();
        $currentSlugParam = Route::current()->parameter('unitSlug');

        // Cek active state
        if (is_array($submenu->slug)) {
            foreach ($submenu->slug as $slug) {
                if ($currentRouteName === $slug) {
                    if (isset($submenu->slug_param) && $currentSlugParam === $submenu->slug_param) {
                        $activeClass = 'active';
                        break;
                    } elseif (!isset($submenu->slug_param)) {
                        $activeClass = 'active';
                        break;
                    }
                }
            }
        } else {
            if ($currentRouteName === $submenu->slug) {
                if (isset($submenu->slug_param) && $currentSlugParam === $submenu->slug_param) {
                    $activeClass = 'active';
                } elseif (!isset($submenu->slug_param)) {
                    $activeClass = 'active';
                }
            }
        }

        // Cek active untuk submenu nested
        if (!$activeClass && isset($submenu->submenu)) {
            $slugsToCheck = is_array($submenu->slug) ? $submenu->slug : [$submenu->slug];

            foreach ($slugsToCheck as $slug) {
                if (str_starts_with($currentRouteName, $slug)) {
                    if (isset($submenu->slug_param)) {
                        if ($currentSlugParam === $submenu->slug_param) {
                            $activeClass = $active;
                            break;
                        }
                    } else {
                        $activeClass = $active;
                        break;
                    }
                }
            }
        }

        // Generate url menu
        if (isset($submenu->slug_param)) {
            // Jika menggunakan route()
            $routeName = is_array($submenu->slug) ? $submenu->slug[0] : $submenu->slug;

            $menuUrl = route($routeName, [
                'unitSlug' => $submenu->slug_param,
            ]);
        } else {
            // Jika URL manual
            $menuUrl = isset($submenu->url) ? url($submenu->url) : 'javascript:void(0)';
        }
      @endphp
      {{-- main menu --}}
      <li class="menu-item {{ $activeClass }}">
        <a href="{{ $menuUrl }}" class="{{ isset($submenu->submenu) ? 'menu-link menu-toggle' : 'menu-link' }}"
          @if (!empty($submenu->target)) target="_blank" @endif>

          @if (isset($submenu->icon))
            <i class="{{ $submenu->icon }}"></i>
          @endif

          <div>{{ $submenu->name ?? '' }}</div>

          @isset($submenu->badge)
            <div class="badge bg-{{ $submenu->badge[0] }} rounded-pill ms-auto">
              {{ $submenu->badge[1] }}
            </div>
          @endisset
        </a>

        {{-- SUBMENU --}}
        @if (isset($submenu->submenu))
          @include('layouts.sections.menu.submenu', ['menu' => $submenu->submenu])
        @endif
      </li>
    @endforeach
  @endif
</ul>
