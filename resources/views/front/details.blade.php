<!doctype html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="{{asset('css//output.css')}}" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    <!-- CSS -->
    <link rel="stylesheet" href="https://unpkg.com/flickity@2/dist/flickity.min.css">
</head>
<body class="text-black font-poppins pt-10 pb-[50px]">
    <div style="background-image: url('{{ asset('assets/background/Hero-Banner.png') }}');" id="hero-section"
        class="max-w-[1200px] mx-auto w-full h-[393px] flex flex-col gap-10 pb-[50px] bg-center bg-no-repeat bg-cover rounded-[32px] overflow-hidden absolute transform -translate-x-1/2 left-1/2">
        
        <!-- Navigation Bar -->
@include('front.partials.nav')
        </nav>
    </div>
    <section id="video-content" class="max-w-[1100px] w-full mx-auto mt-[130px] flex flex-col gap-8">
    <!-- Video Player -->
    <div class="plyr__video-embed w-full overflow-hidden relative rounded-[20px]" id="player">
        <iframe
            src="https://www.youtube.com/embed/{{ $course->path_trailer}}?origin=https://plyr.io&amp;iv_load_policy=3&amp;modestbranding=1&amp;playsinline=1&amp;showinfo=0&amp;rel=0&amp;enablejsapi=1"
            allowfullscreen
            allowtransparency
            allow="autoplay"
            class="w-full h-[500px] sm:h-[600px]"
        ></iframe>
    </div>

    <!-- Modules List -->
    <div id="module-list" class="flex flex-col w-full bg-[#F5F8FA] rounded-[20px] p-6 gap-5">
        <p class="font-bold text-lg text-black">{{ $course->modules->count() }} Modules</p>
        <div class="flex flex-col gap-4">
            @forelse($course->modules as $module)
                <div class="flex flex-col p-5 rounded-2xl bg-[#FFF8F4] has-[.hide]:bg-transparent border-t-4 border-[#FF6129] has-[.hide]:border-0 w-full">
                    <button class="accordion-button flex justify-between gap-1 items-center" data-accordion="module-{{ $module->id }}">
                        <span class="font-semibold text-lg text-left">{{ $module->name }}</span>
                        <div class="arrow w-9 h-9 flex shrink-0">
                            <img src="{{ asset('assets/icon/add.svg') }}" alt="icon">
                        </div>
                    </button>
                    <div id="module-{{ $module->id }}" class="accordion-content hide">
                        <div class="pt-2">
                            <h5 class="font-semibold">Videos</h5>
                            <ul class="list-disc list-inside">
                                @foreach($module->videos as $v)
                                    <li>{{ $v->name }}</li>
                                @endforeach
                            </ul>

                            <h5 class="font-semibold mt-2">Materials</h5>
                            <ul class="list-disc list-inside">
                                @foreach($module->materials as $m)
                                    <li>{{ $m->name }}</li>
                                @endforeach
                            </ul>

                            <h5 class="font-semibold mt-2">Tasks</h5>
                            <ul class="list-disc list-inside">
                                @foreach($module->tasks as $t)
                                    <li>{{ $t->name }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-gray-500">Belum ada modul.</p>
            @endforelse
        </div>
    </div>
</section>

@php
    $hasJoined = auth()->check() && auth()->user()->hasActiveSubscription($course);
    $trainerUser = optional($course->trainer?->user);
@endphp

<section id="Video-Resources" class="flex flex-col mt-5">
    <div class="max-w-[1100px] w-full mx-auto flex flex-col gap-3">
        <h1 class="title font-extrabold text-[30px] leading-[45px]">{{ $course->name }}</h1>
        <p class="font-semibold text-lg">
            {{ $course->price > 0 ? 'Rp ' . number_format($course->price, 0, ',', '.') : 'FREE' }}
        </p>

        @unless($hasJoined)
            <form action="{{ route('cart.store', $course->slug) }}" method="POST" class="my-2">
                @csrf
                <button class="px-4 py-2 bg-[#FF6129] text-white rounded">Add to Cart</button>
            </form>
            @auth
                @if($course->price > 0)
                    <a href="{{ route('front.pricing', $course->slug) }}" class="px-4 py-2 bg-[#FF6129] text-white rounded inline-block mb-2">Bergabung Sekarang</a>
                @else
                    <form action="{{ route('courses.join', $course->slug) }}" method="POST" class="mb-2">
                        @csrf
                        <button class="px-4 py-2 bg-[#FF6129] text-white rounded">Bergabung Sekarang</button>
                    </form>
                @endif
            @endauth
        @endunless

        <div class="flex items-center gap-5">
            <div class="flex items-center gap-[6px]">
                <img src="{{ asset('assets/icon/crown.svg') }}" alt="icon">
                <p class="font-semibold">{{ $course->category->name }}</p>
            </div>
            <div class="flex items-center gap-[6px]">
                <img src="{{ asset('assets/icon/award-outline.svg') }}" alt="icon">
                <p class="font-semibold">Certificate</p>
            </div>
            <div class="flex items-center gap-[6px]">
                <img src="{{ asset('assets/icon/profile-2user.svg') }}" alt="icon">
                <p class="font-semibold">{{ $course->trainees->count() }} Trainees</p>
            </div>
        </div>
    </div>

    <!-- TAB MENU -->
    <div class="max-w-[1100px] w-full mx-auto mt-10 tablink-container flex gap-3 px-4 sm:p-0 no-scrollbar overflow-x-scroll">
        <div class="tablink font-semibold text-lg h-[47px] transition-all duration-300 cursor-pointer hover:text-[#FF6129]" onclick="openPage('About', this)" id="defaultOpen">About</div>
        <div class="tablink font-semibold text-lg h-[47px] transition-all duration-300 cursor-pointer hover:text-[#FF6129]" onclick="openPage('Rewards', this)">Rewards</div>
        <div class="tablink font-semibold text-lg h-[47px] transition-all duration-300 cursor-pointer hover:text-[#FF6129]" onclick="openPage('Quiz', this)">Quiz</div>
    </div>

    <!-- TAB CONTENT SECTION -->
    <div class="w-full bg-[#F5F8FA] py-[50px]">
        <div class="max-w-[1100px] w-full mx-auto flex flex-wrap lg:flex-nowrap gap-[50px] px-4 sm:px-0">
        
            <!-- TAB CONTENT -->
            <div class="tabs-container w-full max-w-[700px] flex flex-col z-0 min-w-0">
                <!-- About -->
                <div id="About" class="tabcontent hidden">
                    <div class="flex flex-col gap-5">
                        <h3 class="font-bold text-2xl">Grow Your Career</h3>
                        <p class="font-medium leading-[30px]">
                            {{ $course->about }}
                        </p>
                        <div class="grid grid-cols-2 gap-x-[30px] gap-y-5">
                            @forelse($course->course_keypoints as $keypoint)
                                <div class="benefit-card flex items-center gap-3">
                                    <img src="{{ asset('assets/icon/tick-circle.svg') }}" alt="icon" class="w-6 h-6">
                                    <p class="font-medium leading-[30px]">{{ $keypoint->name }}</p>
                                </div>
                            @empty
                                <p class="text-gray-500">Belum ada poin kunci.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Rewards -->
                <div id="Rewards" class="tabcontent hidden">
                    <div class="flex flex-col gap-5">
                        <h3 class="font-bold text-2xl">Rewards</h3>
                        <p class="font-medium leading-[30px]">
                            Lorem ipsum dolor sit amet consectetur adipisicing elit. Nesciunt eos et accusantium quia exercitationem reiciendis?
                        </p>
                    </div>
                </div>

                <!-- Quiz -->
                <div id="Quiz" class="tabcontent hidden">
                    <div class="flex flex-col gap-5">
                        <h3 class="font-bold text-2xl">Quiz</h3>
                        <p class="font-medium leading-[30px]">Coming soon.</p>
                    </div>
                </div>
            </div>

            <!-- SIDEBAR TRAINER -->
            <div class="mentor-sidebar w-full max-w-[100px] flex flex-col gap-[30px] z-10">
                <div class="mentor-info bg-white flex flex-col gap-4 rounded-2xl p-5">
                    <p class="font-bold text-lg text-left w-full">Trainer</p>
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full overflow-hidden">
                            <img
                                src="{{ $trainerUser->avatar_url ?? asset('images/default-avatar.png') }}"
                                class="w-full h-full object-cover"
                                alt="avatar">
                        </div>
                        <div class="flex flex-col">
                            <p class="font-semibold">{{ $trainerUser->name ?? 'Unknown Trainer' }}</p>
                            <p class="text-[#6D7786]">{{ $trainerUser->pekerjaan ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
    <section id="FAQ" class="max-w-[1200px] mx-auto flex flex-col py-[70px] px-[100px]">
        <div class="flex justify-between items-center">
            <div class="flex flex-col gap-[30px]">
                <div class="gradient-badge w-fit p-[8px_16px] rounded-full border border-[#FED6AD] flex items-center gap-[6px]">
                    <div>
                        <img src="{{asset('assets/icon/medal-star.svg')}}" alt="icon">
                    </div>
                    <p class="font-medium text-sm text-[#FF6129]">Grow Your Career</p>
                </div>
                <div class="flex flex-col">
                    <h2 class="font-bold text-[36px] leading-[52px]">Get Your Answers</h2>
                    <p class="text-lg text-[#475466]">It’s time to upgrade skills without limits!</p>
                </div>
                <a href="" class="text-white font-semibold rounded-[30px] p-[16px_32px] bg-[#FF6129] transition-all duration-300 hover:shadow-[0_10px_20px_0_#FF612980] w-fit">Contact Our Sales</a>
            </div>
            <div class="flex flex-col gap-[30px] w-[552px] shrink-0">
                <div class="flex flex-col p-5 rounded-2xl bg-[#FFF8F4] has-[.hide]:bg-transparent border-t-4 border-[#FF6129] has-[.hide]:border-0 w-full">
                    <button class="accordion-button flex justify-between gap-1 items-center" data-accordion="accordion-faq-1">
                        <span class="font-semibold text-lg text-left">Can beginner join the course?</span>
                        <div class="arrow w-9 h-9 flex shrink-0">
                            <img src="{{asset('assets/icon/add.svg')}}" alt="icon">
                        </div>
                    </button>
                    <div id="accordion-faq-1" class="accordion-content hide">
                        <p class="leading-[30px] text-[#475466] pt-[10px]">Yes, we have provided a variety range of course from beginner to intermediate level to prepare your next big career,</p>
                    </div>
                </div>
                <div class="flex flex-col p-5 rounded-2xl bg-[#FFF8F4] has-[.hide]:bg-transparent border-t-4 border-[#FF6129] has-[.hide]:border-0 w-full">
                    <button class="accordion-button flex justify-between gap-1 items-center" data-accordion="accordion-faq-2">
                        <span class="font-semibold text-lg text-left">How long does the implementation take?</span>
                        <div class="arrow w-9 h-9 flex shrink-0">
                            <img src="{{asset('assets/icon/add.svg')}}" alt="icon">
                        </div>
                    </button>
                    <div id="accordion-faq-2" class="accordion-content hide">
                        <p class="leading-[30px] text-[#475466] pt-[10px]">Lorem ipsum dolor, sit amet consectetur adipisicing elit. Dolore placeat ut nostrum aperiam mollitia tempora aliquam perferendis explicabo eligendi commodi.</p>
                    </div>
                </div>
                <div class="flex flex-col p-5 rounded-2xl bg-[#FFF8F4] has-[.hide]:bg-transparent border-t-4 border-[#FF6129] has-[.hide]:border-0 w-full">
                    <button class="accordion-button flex justify-between gap-1 items-center" data-accordion="accordion-faq-3">
                        <span class="font-semibold text-lg text-left">Do you provide the job-guarantee program?</span>
                        <div class="arrow w-9 h-9 flex shrink-0">
                            <img src="{{asset('assets/icon/add.svg')}}" alt="icon">
                        </div>
                    </button>
                    <div id="accordion-faq-3" class="accordion-content hide">
                        <p class="leading-[30px] text-[#475466] pt-[10px]">Lorem ipsum dolor sit amet consectetur adipisicing elit. Molestiae itaque facere ipsum animi sunt iure!</p>
                    </div>
                </div>
                <div class="flex flex-col p-5 rounded-2xl bg-[#FFF8F4] has-[.hide]:bg-transparent border-t-4 border-[#FF6129] has-[.hide]:border-0 w-full">
                    <button class="accordion-button flex justify-between gap-1 items-center" data-accordion="accordion-faq-4">
                        <span class="font-semibold text-lg text-left">How to issue all course certificates?</span>
                        <div class="arrow w-9 h-9 flex shrink-0">
                            <img src="{{asset('assets/icon/add.svg')}}" alt="icon">
                        </div>
                    </button>
                    <div id="accordion-faq-4" class="accordion-content hide">
                        <p class="leading-[30px] text-[#475466] pt-[10px]">Lorem ipsum dolor sit amet consectetur adipisicing elit. Molestiae itaque facere ipsum animi sunt iure!</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- JavaScript -->
    <script
        src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo="
        crossorigin="anonymous">
    </script>

    <script src="https://cdn.plyr.io/3.7.8/plyr.polyfilled.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>  

    <script src="{{ asset('js/main.js') }}"></script>
    </body>
</html>