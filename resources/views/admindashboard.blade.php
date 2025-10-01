<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 flex">

    {{-- Sidebar Menu --}}
    <div class="flex items-center justify-between border-b pb-4 mb-6">
                <div class="flex items-left space-x-4">
                    <div class="w-12 h-12 bg-gray-200 rounded-full"></div>
                    <div>
                        <p class="text-xl font-semibold">Apiwat ganglon</p>
                        <p class="text-gray-500 text-sm">Apiwat.gl@email.com</p>
                    </div>
                </div>
            </div>
        <ul class="space-y-2">
            <li><a href="#" class="flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded-md bg-gray-200">Dash Bord</a></li>
            <li><a href="#" class="flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded-md">BanUser</a></li>
            <li><a href="#" class="flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded-md">Chat log</a></li>
            <li><a href="#" class="flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded-md">Add something</a></li>
        </ul>
    </div>

    {{-- Main Content: Dashboard/User View --}}
    <div class="flex-1 p-8">
        <div class="bg-white shadow-xl rounded-lg p-6 max-w-4xl mx-auto">
            
            {{-- Header/Admin Info --}}
            <div class="flex items-center justify-between border-b pb-4 mb-6">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-gray-200 rounded-full"></div>
                    <div>
                        <p class="text-xl font-semibold">Apiwat ganglon</p>
                        <p class="text-gray-500 text-sm">Apiwat.gl@email.com</p>
                    </div>
                </div>
            </div>

            <h2 class="text-2xl font-bold mb-4">User Management</h2>
            
            {{-- User List Section --}}
            <div class="space-y-3">
                @for ($i = 0; $i < 4; $i++)
                <div class="flex items-center justify-between p-3 bg-gray-50 border rounded-lg">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-green-200 rounded-full"></div>
                        <p class="font-medium">Apiwat ganglon</p>
                    </div>
                    <div class="flex items-center space-x-6 text-sm">
                        <span class="text-gray-600">XXXXXXX</span>
                        <span class="text-gray-600">RoomID:58464</span>
                        <a href="#" class="bg-green-500 text-white text-xs px-3 py-1 rounded hover:bg-green-600 font-semibold">View</a>
                    </div>
                </div>
                @endfor
            </div>

        </div>
    </div>
</body>
</html>