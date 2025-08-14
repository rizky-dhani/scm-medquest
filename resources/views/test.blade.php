<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="{{ asset('assets/fontawesome/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fontawesome/css/solid.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fontawesome/css/regular.min.css') }}">
    @vite('resources/css/app.css')
</head>

<body class="bg-gray-100 text-[#1a1a1a]">
    <div class="flex h-screen flex-col lg:flex-row">
        <!-- Sidebar (Hidden on mobile, shown on desktop) -->
        <aside id="sidebar" class="w-64 bg-white p-6 shadow-lg hidden lg:block">
            <h2 class="text-xl font-bold mb-6">Admin Dashboard</h2>
            <ul class="space-y-2">
                <li class="flex items-center gap-3 p-3 hover:bg-gray-200 rounded cursor-pointer text-base">
                    <i class="fas fa-home"></i> <span class="sidebar-text">Dashboard</span>
                </li>
                <li class="flex items-center gap-3 p-3 hover:bg-gray-200 rounded cursor-pointer text-base">
                    <i class="fas fa-users"></i> <span class="sidebar-text">Users</span>
                </li>
                <li class="flex items-center gap-3 p-3 hover:bg-gray-200 rounded cursor-pointer text-base">
                    <i class="fas fa-cog"></i> <span class="sidebar-text">Settings</span>
                </li>
                <li class="relative">
                    <button id="dropdown-button"
                        class="flex items-center justify-between w-full p-3 hover:bg-gray-200 rounded cursor-pointer text-base">
                        <span class="flex items-center gap-3">
                            <i class="fas fa-folder"></i> <span>Dropdown</span>
                        </span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <ul id="dropdown-menu" class="hidden absolute left-0 w-full bg-white shadow-lg rounded mt-1 z-10">
                        <li class="p-3 hover:bg-gray-200 cursor-pointer text-base">Option 1</li>
                        <li class="p-3 hover:bg-gray-200 cursor-pointer text-base">Option 2</li>
                    </ul>
                </li>
                <li class="flex items-center gap-3 p-3 hover:bg-gray-200 rounded cursor-pointer text-base">
                    <i class="fas fa-cog"></i> <span class="sidebar-text">Settings</span>
                </li>
            </ul>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col">
            <!-- Navbar -->
            <nav class="bg-white shadow-md p-4 flex items-center justify-between">
                <!-- Mobile Sidebar Dropdown -->
                <div class="relative lg:hidden">
                    <button id="mobile-menu-button" class="text-xl p-2">☰</button>
                    <div id="mobile-menu"
                        class="hidden fixed top-0 left-0 w-full h-full bg-gray-900 bg-opacity-50 z-50 flex flex-col">
                        <div class="bg-white w-full p-6 shadow-lg h-full">
                            <button id="close-mobile-menu" class="absolute top-4 right-4 text-xl">✖</button>
                            <h2 class="text-lg font-bold mb-4">Dashboard</h2>
                            <ul class="space-y-3">
                                <li
                                    class="flex items-center gap-3 p-3 hover:bg-gray-200 rounded cursor-pointer text-lg">
                                    <i class="fas fa-home"></i> <span class="sidebar-text">Dashboard</span>
                                </li>
                                <li
                                    class="flex items-center gap-3 p-3 hover:bg-gray-200 rounded cursor-pointer text-lg">
                                    <i class="fas fa-users"></i> <span class="sidebar-text">Users</span>
                                </li>
                                <li
                                    class="flex items-center gap-3 p-3 hover:bg-gray-200 rounded cursor-pointer text-lg">
                                    <i class="fas fa-cog"></i> <span class="sidebar-text">Settings</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <h2 class="text-lg font-bold">Dashboard</h2>

                <!-- User Dropdown -->
                <div class="dropdown dropdown-end">
                    <button tabindex="0" class="btn btn-ghost flex items-center gap-2 text-base">
                        <span class="font-medium">John Doe</span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <ul tabindex="0" class="dropdown-content menu p-3 shadow bg-white rounded-box w-52 text-base">
                        <li><a href="#">👤 Profile</a></li>
                        <li><a href="#">🚪 Logout</a></li>
                    </ul>
                </div>
            </nav>

            <!-- Dashboard Content -->
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="p-5 bg-white shadow-md rounded-lg text-center">
                    <h3 class="text-lg font-bold mb-2">Total Users</h3>
                    <p class="text-2xl">1,250</p>
                </div>
                <div class="p-5 bg-white shadow-md rounded-lg text-center">
                    <h3 class="text-lg font-bold mb-2">Sales</h3>
                    <p class="text-2xl">$12,340</p>
                </div>
                <div class="p-5 bg-white shadow-md rounded-lg text-center">
                    <h3 class="text-lg font-bold mb-2">New Orders</h3>
                    <p class="text-2xl">85</p>
                </div>
                <div class="p-5 bg-white shadow-md rounded-lg text-center">
                    <h3 class="text-lg font-bold mb-2">Pending Requests</h3>
                    <p class="text-2xl">42</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('mobile-menu-button').addEventListener('click', function () {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });

        document.getElementById('close-mobile-menu').addEventListener('click', function () {
            document.getElementById('mobile-menu').classList.add('hidden');
        });

        document.getElementById('dropdown-button').addEventListener('click', function () {
            document.getElementById('dropdown-menu').classList.toggle('hidden');
        });
    </script>
</body>

</html>