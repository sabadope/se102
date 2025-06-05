<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Intern Performance Monitoring System</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <div class="bg-gray-800 text-white w-64 space-y-6 py-7 px-2 absolute inset-y-0 left-0 transform -translate-x-full md:relative md:translate-x-0 transition duration-200 ease-in-out">
            <div class="flex items-center space-x-2 px-4">
                <i class="fas fa-user-graduate text-2xl"></i>
                <span class="text-2xl font-extrabold">IPMS</span>
            </div>
            <nav>
                @if(auth()->user()->isSupervisor())
                    <a href="{{ route('supervisor.dashboard') }}" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">
                        <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                    </a>
                    <a href="#" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">
                        <i class="fas fa-users mr-2"></i>My Interns
                    </a>
                    <a href="#" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">
                        <i class="fas fa-tasks mr-2"></i>Tasks
                    </a>
                    <a href="#" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">
                        <i class="fas fa-chart-bar mr-2"></i>Reports
                    </a>
                @elseif(auth()->user()->isIntern())
                    <a href="{{ route('intern.dashboard') }}" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">
                        <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                    </a>
                    <a href="{{ route('intern.profile') }}" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">
                        <i class="fas fa-user mr-2"></i>Profile
                    </a>
                    <a href="#" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">
                        <i class="fas fa-tasks mr-2"></i>My Tasks
                    </a>
                    <a href="#" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">
                        <i class="fas fa-chart-line mr-2"></i>Performance
                    </a>
                @endif
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1">
            <!-- Top Navigation -->
            <header class="bg-white shadow-lg">
                <div class="flex items-center justify-between px-6 py-4">
                    <div class="flex items-center">
                        <button class="text-gray-500 focus:outline-none md:hidden">
                            <i class="fas fa-bars"></i>
                        </button>
                        <h2 class="text-xl font-semibold text-gray-800 ml-4">@yield('title')</h2>
                    </div>
                    <div class="flex items-center">
                        <div class="relative">
                            <button class="flex items-center text-gray-500 focus:outline-none">
                                <span class="mr-2">{{ auth()->user()->name }}</span>
                                <i class="fas fa-chevron-down"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="p-6">
                @yield('content')
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@2.8.2/dist/alpine.min.js" defer></script>
</body>
</html> 