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
        $currentSlugParam = Route::current()->parameter('slug'); // Ambil slug dari URL

        // Cek apakah slug adalah array atau string
        if (gettype($submenu->slug) === 'array') {
            // Jika slug adalah array, cek semua route name di dalamnya
            foreach ($submenu->slug as $slug) {
                if ($currentRouteName === $slug) {
                    // Cek juga slug_param jika ada
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
            // Jika slug adalah string biasa
            if ($currentRouteName === $submenu->slug) {
                if (isset($submenu->slug_param) && $currentSlugParam === $submenu->slug_param) {
                    $activeClass = 'active';
                } elseif (!isset($submenu->slug_param)) {
                    $activeClass = 'active';
                }
            }
        }

        // Cek untuk submenu nested
        if (!$activeClass && isset($submenu->submenu)) {
            $slugsToCheck = gettype($submenu->slug) === 'array' ? $submenu->slug : [$submenu->slug];

            foreach ($slugsToCheck as $slug) {
                if (str_contains($currentRouteName, $slug) && strpos($currentRouteName, $slug) === 0) {
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

        // Generate URL dinamis jika ada slug_param
        $menuUrl = isset($submenu->slug_param)
            ? route(gettype($submenu->slug) === 'array' ? $submenu->slug[0] : $submenu->slug, [
                'slug' => $submenu->slug_param,
            ])
            : (isset($submenu->url)
                ? url($submenu->url)
                : 'javascript:void(0)');
      @endphp

      <li class="menu-item {{ $activeClass }}">
        <a href="{{ $menuUrl }}" class="{{ isset($submenu->submenu) ? 'menu-link menu-toggle' : 'menu-link' }}"
          @if (isset($submenu->target) and !empty($submenu->target)) target="_blank" @endif>
          @if (isset($submenu->icon))
            <i class="{{ $submenu->icon }}"></i>
          @endif
          <div>{{ isset($submenu->name) ? __($submenu->name) : '' }}</div>
          @isset($submenu->badge)
            <div class="badge bg-{{ $submenu->badge[0] }} rounded-pill ms-auto">{{ $submenu->badge[1] }}</div>
          @endisset
        </a>

        {{-- submenu --}}
        @if (isset($submenu->submenu))
          @include('layouts.sections.menu.submenu', ['menu' => $submenu->submenu])
        @endif
      </li>
    @endforeach
  @endif
</ul>
