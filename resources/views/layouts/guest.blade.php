<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
  class="layout-wide customizer-hide"
  dir="ltr"
  data-skin="default"
  data-bs-theme="light"
  data-assets-path="{{ url('public/vendor/vuexy/') }}/"
  data-template="vertical-menu-template">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Laravel'))</title>

    <link rel="icon" type="image/x-icon" href="{{ url('public/vendor/vuexy/img/favicon/favicon.ico') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ url('public/vendor/vuexy/vendor/fonts/iconify-icons.css') }}">

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ url('public/vendor/vuexy/vendor/libs/node-waves/node-waves.css') }}">
    <link rel="stylesheet" href="{{ url('public/vendor/vuexy/vendor/libs/pickr/pickr-themes.css') }}">
    <link rel="stylesheet" href="{{ url('public/vendor/vuexy/vendor/css/core.css') }}">
    <link rel="stylesheet" href="{{ url('public/vendor/vuexy/css/demo.css') }}">
    <link rel="stylesheet" href="{{ url('public/vendor/vuexy/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}">

    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ url('public/vendor/vuexy/vendor/css/pages/page-auth.css') }}">

    @stack('styles')

    <!-- Helpers -->
    <script src="{{ url('public/vendor/vuexy/vendor/js/helpers.js') }}"></script>
    <script src="{{ url('public/vendor/vuexy/vendor/js/template-customizer.js') }}"></script>
    <script src="{{ url('public/vendor/vuexy/js/config.js') }}"></script>
  </head>
  <body>
    @if (! empty($supportedLocales))
      <div class="position-absolute top-0 end-0 p-4">
        <div class="dropdown">
          <button class="btn btn-text-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="icon-base ti tabler-language"></i>
            <span class="ms-1">{{ $supportedLocales[$currentLocale] ?? strtoupper($currentLocale) }}</span>
          </button>
          <ul class="dropdown-menu dropdown-menu-end">
            @foreach ($supportedLocales as $locale => $label)
              <li>
                <a class="dropdown-item {{ $locale === $currentLocale ? 'active' : '' }}" href="{{ route('locale.switch', $locale) }}">
                  <span class="badge bg-label-primary text-uppercase me-2">{{ $locale }}</span>
                  <span>{{ $label }}</span>
                </a>
              </li>
            @endforeach
          </ul>
        </div>
      </div>
    @endif
    <div class="container-xxl">
      <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner py-6">
          @yield('content')
        </div>
      </div>
    </div>

    <script src="{{ url('public/vendor/vuexy/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ url('public/vendor/vuexy/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ url('public/vendor/vuexy/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ url('public/vendor/vuexy/vendor/libs/node-waves/node-waves.js') }}"></script>
    <script src="{{ url('public/vendor/vuexy/vendor/libs/pickr/pickr.js') }}"></script>
    <script src="{{ url('public/vendor/vuexy/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ url('public/vendor/vuexy/vendor/js/menu.js') }}"></script>
    <script src="{{ url('public/vendor/vuexy/js/main.js') }}"></script>

    @stack('scripts')
  </body>
</html>


