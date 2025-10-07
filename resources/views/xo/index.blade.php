<!DOCTYPE html>
<html>
<head>
    <title>XO Game</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white flex items-center justify-center h-screen">
    <div class="text-center">
        <h1 class="text-3xl mb-4 font-bold">Tic Tac Toe</h1>

        <div class="grid grid-cols-3 gap-2 w-48 mx-auto mb-4">
            @foreach ($board as $i => $cell)
                <form method="POST" action="{{ route('xo.move', $i) }}">
                    @csrf
                    <button class="w-16 h-16 bg-gray-700 rounded text-3xl font-bold hover:bg-gray-600"
                        {{ $cell || $winner ? 'disabled' : '' }}>
                        {{ $cell }}
                    </button>
                </form>
            @endforeach
        </div>

        @if ($winner)
            <h2 class="text-2xl mb-4">
                {{ $winner === 'Draw' ? "It's a Draw!" : "Winner: $winner" }}
            </h2>
        @else
            <h2 class="text-xl mb-4">Turn: {{ $turn }}</h2>
        @endif

        <form method="POST" action="{{ route('xo.reset') }}">
            @csrf
            <button class="bg-blue-500 px-4 py-2 rounded hover:bg-blue-600">Reset Game</button>
        </form>

        <div class="mt-4">
            <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-white">← Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
