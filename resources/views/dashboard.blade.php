<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <title>Join the party</title>
</head>
<body>
    <h1>The Twilight bar</h1>
    
    <div class="dashboard-container">
        <!-- Main Content -->
        <div class="main-content">
            <div class="menu">
                <a href="{{route('cosmetic.index')}}">⚙ Cosmetic</a>
                <a href="{{route('shop.index')}}">🛒 Shop</a>
            </div>
            
            <div class="coin">🪙 {{ auth()->user()->coins }} : coin</div>

            <div class="container">
                <div class="message">let's find someone to talk!!!!</div>
                <button class="btn-join">
                    <a href="{{route('rooms.index')}}">join the party ★</a>
                </button>
            </div>
        </div>

        <div class="my-character-position">
    <x-character-preview 
        :user="auth()->user()" 
        size="small"
      />
</div>
    </div>
</body>
</html>