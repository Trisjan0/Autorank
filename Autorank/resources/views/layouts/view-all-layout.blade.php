<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'AutoRank')</title>

    <!-- Google Font Links -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    <!-- CSS Links -->
    <link rel="stylesheet" href="{{ asset('css/global-styles.css') }}">
    <link rel="stylesheet" href="{{ asset('css/view-all-styles.css') }}">
    <link rel="stylesheet" href="{{ asset('css/responsive-styles.css') }}">

    <!-- Fontawesome CDN -->
    <script src="https://kit.fontawesome.com/5ba477d22e.js" crossorigin="anonymous"></script>
</head>

<body>
    @include('partials.default-navbar')

    <main>
        @yield('content')
    </main>

    <script src="{{ asset('js/global-scripts.js') }}"></script>
</body>

</html>