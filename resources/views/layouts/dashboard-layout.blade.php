<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Autorank')</title>

    <!-- Google Font Links -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    <!-- CSS Links -->
    <link rel="stylesheet" href="{{ asset('css/global-styles.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard-styles.css') }}">
    <link rel="stylesheet" href="{{ asset('css/responsive-styles.css') }}">

    <!-- Fontawesome CDN -->
    <script src="https://kit.fontawesome.com/5ba477d22e.js" crossorigin="anonymous"></script>
</head>

<body>
    <!-- debugging -->
    @if(Auth::check())
    <p>Logged in as: {{ Auth::user()->email }}</p>
    <p>Roles: {{ Auth::user()->roles->pluck('name')->implode(', ') }}</p>
    <p>Is Admin: {{ Auth::user()->hasRole('admin') ? 'Yes' : 'No' }}</p>
    <p>Is Super Admin: {{ Auth::user()->hasRole('super_admin') ? 'Yes' : 'No' }}</p>
    <p>Is User: {{ Auth::user()->hasRole('user') ? 'Yes' : 'No' }}</p>
    @else
    <p>Not logged in.</p>
    @endif

    @auth
    @if(Auth::user()->hasRole('admin') || Auth::user()->hasRole('super_admin'))
    @include('partials.admin-navbar')
    @elseif(Auth::user()->hasRole('user'))
    @include('partials.user-navbar')
    @endif
    @endauth
    <!-- debugging -->

    <main>
        @yield('content')
    </main>

    <script src="{{ asset('js/global-scripts.js') }}"></script>
</body>

</html>