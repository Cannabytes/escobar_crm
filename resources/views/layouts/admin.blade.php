<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
  class="layout-navbar-fixed layout-menu-fixed layout-compact"
  dir="ltr"
  data-skin="default"
  data-bs-theme="light"
  data-assets-path="{{ asset('vendor/vuexy') }}/"
  data-template="vertical-menu-template">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Laravel'))</title>

    <link rel="icon" type="image/x-icon" href="{{ asset('vendor/vuexy/img/favicon/favicon.ico') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('vendor/vuexy/vendor/fonts/iconify-icons.css') }}">
    
    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/vuexy/vendor/libs/node-waves/node-waves.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/vuexy/vendor/libs/pickr/pickr-themes.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/vuexy/vendor/css/core.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/vuexy/css/demo.css') }}">

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/vuexy/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}">

    @stack('styles')

    <!-- Helpers -->
    <script src="{{ asset('vendor/vuexy/vendor/js/helpers.js') }}"></script>
    <!-- Pickr library for template customizer -->
    <script src="{{ asset('vendor/vuexy/vendor/libs/pickr/pickr.js') }}"></script>
    <!-- Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <script src="{{ asset('vendor/vuexy/vendor/js/template-customizer.js') }}"></script>
    <!-- Config: Mandatory theme config file contain global vars & default theme options -->
    <script src="{{ asset('vendor/vuexy/js/config.js') }}"></script>
  </head>
  <body>
    <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container">
        @include('partials.admin.sidebar')
        <div class="layout-page">
          @include('partials.admin.navbar')

          <div class="content-wrapper">
            <div class="container-xxl flex-grow-1 container-p-y">
              @if (session('status'))
                <div class="alert alert-success alert-dismissible" role="alert">
                  {{ session('status') }}
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Закрыть') }}"></button>
                </div>
              @endif

              @yield('content')
            </div>


            <div class="content-backdrop fade"></div>
          </div>
        </div>
      </div>
      <div class="layout-overlay layout-menu-toggle"></div>
      <div class="drag-target"></div>
    </div>

    <script src="{{ asset('vendor/vuexy/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('vendor/vuexy/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('vendor/vuexy/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('vendor/vuexy/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('vendor/vuexy/vendor/libs/node-waves/node-waves.js') }}"></script>
    <script src="{{ asset('vendor/vuexy/vendor/js/menu.js') }}"></script>
    <script src="{{ asset('vendor/vuexy/js/main.js') }}"></script>

    @stack('scripts')
  </body>
</html>

