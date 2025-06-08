<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="{{ asset('css/output.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
</head>
<body class="text-black font-poppins pt-10 pb-10">
    <div class="max-w-[1200px] mx-auto">
        <nav class="flex justify-between items-center py-6 px-[50px]">
            <div class="flex items-center gap-3">
                <img src="{{ asset('assets/logo/logo.png') }}" alt="logo" class="w-[50px] h-[50px] object-contain">
                <div class="leading-tight text-black">
                    <p class="font-semibold text-xs">Pusat Unggulan IPTEK Perguruan Tinggi</p>
                    <h1 class="font-bold text-sm">Intelligent Sensing-IoT</h1>
                </div>
            </div>
            <ul class="flex items-center gap-[30px]">
                <li><a href="{{ route('front.index') }}" class="font-semibold">Home</a></li>
                <li><a href="#" class="font-semibold">My Certificate</a></li>
                <li><a href="{{ route('courses.my') }}" class="font-semibold">My Course</a></li>
                <li>
                    <a href="{{ route('cart.index') }}" class="flex items-center">
                        <img src="{{ asset('asset/vendor/fontawesome-free/svgs/solid/shopping-cart.svg') }}" class="w-5 h-5" alt="cart">
                    </a>
                </li>
            </ul>
            @auth
            <div class="relative" id="dropdownWrapper">
                <div class="w-[56px] h-[56px] overflow-hidden rounded-full cursor-pointer" id="dropdownAvatar">
                    <img src="{{ Storage::url(Auth::user()->avatar) }}" class="w-full h-full object-cover" alt="photo">
                </div>
                <div class="absolute right-0 mt-2 bg-white border rounded shadow hidden" id="dropdownMenu">
                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 hover:bg-gray-100">Profile Settings</a>
                    <a href="{{ route('courses.my') }}" class="block px-4 py-2 hover:bg-gray-100">My Course</a>
                    <a href="{{ route('cart.index') }}" class="block px-4 py-2 hover:bg-gray-100">My Cart</a>
                    <a href="{{ route('dashboard') }}" class="block px-4 py-2 hover:bg-gray-100">Dashboard</a>
                </div>
            </div>
            @endauth
            @guest
            <div class="flex gap-[10px] items-center">
                <a href="{{ route('register') }}" class="font-semibold rounded-[30px] p-[16px_32px] ring-1 ring-black transition-all">Register</a>
                <a href="{{ route('login') }}" class="font-semibold rounded-[30px] p-[16px_32px] bg-[#FF6129] text-white">Login In</a>
            </div>
            @endguest
        </nav>
        <h1 class="text-2xl font-bold mb-4 px-[50px]">My Courses</h1>
        <div class="px-[50px]" id="courseContent">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @include('partials.course-list', ['courses' => $courses])
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script>
        $('#dropdownAvatar').on('click', function(e){
            e.stopPropagation();
            $('#dropdownMenu').toggleClass('hidden');
        });
        $(document).on('click', function(e){
            if(!$('#dropdownWrapper').is(e.target) && $('#dropdownWrapper').has(e.target).length === 0){
                $('#dropdownMenu').addClass('hidden');
            }
        });
    </script>
</body>
</html>
