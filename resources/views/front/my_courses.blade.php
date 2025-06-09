<!doctype html>
<html>
<head>
    @include('layouts.seo')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="{{ asset('css/output.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
</head>
<body class="text-black font-poppins pt-10 pb-10">
    <div class="max-w-[1200px] mx-auto">
@include('front.partials.nav')
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
