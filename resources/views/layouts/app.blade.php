<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'VOHISIX')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
    <link rel="stylesheet" href="{{ asset('css/nav.css') }}">
    <link rel="stylesheet" href="{{ asset('css/footer.css') }}">
    @if(request()->is('admin/*'))
        <link rel="stylesheet" href="{{ asset('css/admin-style.css') }}">
    @endif
    @yield('head')
</head>
<body>
    @include('layouts.partials.navbar')
    @include('layouts.partials.sidebar')

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            @if($message = Session::get('success'))
                <div class="alert alert-success">
                    {{ $message }}
                </div>
            @endif

            @if($message = Session::get('error'))
                <div class="alert alert-danger">
                    {{ $message }}
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    @include('layouts.partials.footer')

    <button id="scrollToTop" class="scroll-to-top" title="Kembali ke atas">
        <i class="fas fa-arrow-up"></i>
    </button>

    @include('layouts.partials.scripts')
    @stack('scripts')
</body>
</html>
