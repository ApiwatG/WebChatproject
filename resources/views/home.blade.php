<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} - Home</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-purple-900 via-purple-600 to-indigo-800 text-white overflow-x-hidden font-sans">
    
    <!-- Navigation Bar -->
    <nav class="fixed top-0 left-0 right-0 z-50 bg-white/10 backdrop-blur-lg border-b border-white/20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="{{ route('dashboard') }}" class="text-2xl font-bold text-white">
                        {{ config('app.name', 'Laravel') }}
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden md:flex space-x-6">
                    <a href="{{ route('dashboard') }}" class="text-white hover:text-purple-200 transition-colors font-medium">
                        Home
                    </a>
                    <a href="#" class="text-white/70 hover:text-white transition-colors font-medium">
                        Cosmetic
                    </a>
                    <a href="#" class="text-white/70 hover:text-white transition-colors font-medium">
                        Shop
                    </a>
                </div>

                <!-- User Menu -->
                <div class="flex items-center space-x-4">
                    @auth
                        <div class="relative">
                            <button id="userMenuButton" class="flex items-center space-x-2 text-white hover:text-purple-200 transition-colors">
                                <span>{{ Auth::user()->name }}</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div id="userMenu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                                <a href="{{ route('profile.show') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-white hover:text-purple-200 transition-colors font-medium">Login</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="bg-white/20 text-white px-4 py-2 rounded-lg hover:bg-white/30 transition-colors font-medium">Register</a>
                        @endif
                    @endauth
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button id="mobileMenuButton" class="text-white hover:text-purple-200 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobileMenu" class="hidden md:hidden bg-white/10 backdrop-blur-lg border-t border-white/20">
            <div class="px-4 py-3 space-y-2">
                <a href="{{ route('dashboard') }}" class="block text-white hover:text-purple-200 transition-colors font-medium py-2">Home</a>
                <a href="#" class="block text-white/70 hover:text-white transition-colors font-medium py-2">Cosmetic</a>
                <a href="#" class="block text-white/70 hover:text-white transition-colors font-medium py-2">Shop</a>
                @auth
                    <hr class="border-white/20 my-2">
                    <a href="{{ route('profile.show') }}" class="block text-white hover:text-purple-200 transition-colors font-medium py-2">Profile</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block text-left text-white hover:text-purple-200 transition-colors font-medium py-2">
                            Logout
                        </button>
                    </form>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="pt-16">
        <!-- Flash Messages -->
        @if(session('success'))
            <div class="fixed top-24 left-1/2 transform -translate-x-1/2 bg-green-500 text-white px-8 py-4 rounded-2xl shadow-2xl text-lg font-bold z-40">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="fixed top-24 left-1/2 transform -translate-x-1/2 bg-red-500 text-white px-8 py-4 rounded-2xl shadow-2xl text-lg font-bold z-40">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Header Navigation -->
        <div class="fixed top-20 right-5 z-40 flex flex-col gap-3">
            <a href="#" class="bg-white/90 text-gray-800 px-5 py-3 rounded-full font-bold transition-colors hover:bg-white">
                Cosmetic
            </a>
            <a href="#" class="bg-white/90 text-gray-800 px-5 py-3 rounded-full font-bold transition-colors hover:bg-white">
                Shop
            </a>
        </div>

        <!-- Coin Display -->
        <div class="fixed top-20 left-5 bg-white/10 backdrop-blur-lg border border-white/20 px-5 py-3 rounded-full flex items-center gap-2 z-40">
            <div class="w-5 h-5 bg-yellow-400 rounded-full flex items-center justify-center text-xs font-bold text-gray-800">C</div>
            <span class="font-semibold">0 : coin</span>
        </div>

        <!-- Main Title -->
        <div class="text-center pt-12 pb-12">
            <h1 class="text-5xl lg:text-6xl font-bold tracking-wider drop-shadow-2xl">
                Welcome Home
            </h1>
            <div class="w-32 h-1 bg-gradient-to-r from-purple-400 to-pink-400 mx-auto mt-4 rounded-full"></div>
        </div>

        <!-- Content Section -->
        <div class="text-center mb-16 px-4">
            <h2 class="text-2xl lg:text-3xl mb-8 drop-shadow-lg">
                Let's start our journey!
            </h2>
        </div>


    </div>

    <!-- Custom Scripts -->
    <script>
        // Mobile menu toggle
        document.getElementById('mobileMenuButton').addEventListener('click', function() {
            const mobileMenu = document.getElementById('mobileMenu');
            mobileMenu.classList.toggle('hidden');
        });

        // User menu toggle
        const userMenuButton = document.getElementById('userMenuButton');
        const userMenu = document.getElementById('userMenu');
        
        if (userMenuButton && userMenu) {
            userMenuButton.addEventListener('click', function() {
                userMenu.classList.toggle('hidden');
            });

            // Close user menu when clicking outside
            document.addEventListener('click', function(event) {
                if (!userMenuButton.contains(event.target) && !userMenu.contains(event.target)) {
                    userMenu.classList.add('hidden');
                }
            });
        }

        // Hide success/error messages after some time
        document.addEventListener('DOMContentLoaded', function() {
            const successMessage = document.querySelector('.bg-green-500');
            if (successMessage) {
                setTimeout(() => {
                    successMessage.remove();
                }, 3000);
            }

            const errorMessage = document.querySelector('.bg-red-500');
            if (errorMessage) {
                setTimeout(() => {
                    errorMessage.remove();
                }, 5000);
            }
        });
    </script>
</body>
</html>