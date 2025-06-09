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
        <div class="flex gap-6 mt-6">
            <aside class="w-1/4">
                <form id="filterForm" method="GET" class="flex flex-col gap-4">
                    <input type="text" name="search" placeholder="Search" value="{{ request('search') }}" class="border rounded p-2">
                    <select name="category_id" class="border rounded p-2">
                        <option value="">All Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id')==$category->id?'selected':'' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    <select name="course_mode_id" class="border rounded p-2">
                        <option value="">All Modes</option>
                        @foreach($modes as $mode)
                            <option value="{{ $mode->id }}" {{ request('course_mode_id')==$mode->id?'selected':'' }}>{{ $mode->name }}</option>
                        @endforeach
                    </select>
                    <select name="course_level_id" class="border rounded p-2">
                        <option value="">All Levels</option>
                        @foreach($levels as $level)
                            <option value="{{ $level->id }}" {{ request('course_level_id')==$level->id?'selected':'' }}>{{ $level->name }}</option>
                        @endforeach
                    </select>
                    <button class="px-4 py-2 bg-[#FF6129] text-white rounded">Filter</button>
                </form>
            </aside>
            <div class="flex-1">
                <div id="courseContent" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @include('partials.course-list', ['courses' => $courses])
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script>
        $(document).ready(function(){
            $('#filterForm').on('submit', function(e){
                e.preventDefault();
                $.ajax({
                    url: '{{ route('courses.index') }}',
                    type: 'GET',
                    data: $(this).serialize(),
                    success: function(data){
                        const html = $(data).find('#courseContent').html();
                        $('#courseContent').html(html);
                    }
                });
            });

            $('#dropdownAvatar').on('click', function(e){
                e.stopPropagation();
                $('#dropdownMenu').toggleClass('hidden');
            });
            $(document).on('click', function(e){
                if(!$('#dropdownWrapper').is(e.target) && $('#dropdownWrapper').has(e.target).length === 0){
                    $('#dropdownMenu').addClass('hidden');
                }
            });
        });
    </script>
</body>
</html>
