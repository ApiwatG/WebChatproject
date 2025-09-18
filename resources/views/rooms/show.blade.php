<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Room: {{ $room->name }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="mb-2">Users in this room ({{ $room->users->count() }}/{{ $room->max_users }}):</h3>
                <ul class="mb-4">
                    @foreach($room->users as $user)
                        <li>{{ $user->name }}</li>
                    @endforeach
                </ul>

                @if($room->isFull())
                    <p class="text-red-500">The room is full.</p>
                @else
                    <p class="text-green-500">There is space for more users.</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
