<!doctype html>
<html lang="en">
<head>
    @include('layouts.seo')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="{{ asset('css/output.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    @stack('styles')
</head>
<body class="text-black font-poppins pt-10 pb-[50px]">
    @yield('content')
    @stack('scripts')
</body>
</html>
