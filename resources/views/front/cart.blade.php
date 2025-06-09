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
<body class="font-poppins p-10">
    <h1 class="text-2xl font-bold mb-4">Your Cart</h1>
    <ul class="space-y-4">
        @forelse($items as $item)
            <li class="flex justify-between items-center border-b pb-2">
                <span>{{ $item->course->name }}</span>
                <form action="{{ route('cart.destroy', $item) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button class="text-red-600">Remove</button>
                </form>
            </li>
        @empty
            <li>No items in cart.</li>
        @endforelse
    </ul>
</body>
</html>
