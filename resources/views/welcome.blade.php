<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel') }} - Admin Dashboard</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Tailwind CSS CDN as fallback -->
        <script src="https://cdn.tailwindcss.com"></script>
        <style>
            .glass-morphism {
                background: rgba(255, 255, 255, 0.1);
                backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.2);
            }
            .hover-lift {
                transition: all 0.3s ease;
            }
            .hover-lift:hover {
                transform: translateY(-5px);
            }
        </style>
    </head>
    <body class="font-sans antialiased bg-black min-h-screen">
        <!-- Navigation -->
        <nav class="glass-morphism border-b border-white/20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center">
                        <div class="shrink-0">
                            <h1 class="text-2xl font-bold text-white">
                                {{ config('app.name', 'Laravel') }} <span class="text-blue-200">Admin</span>
                            </h1>
                        </div>
                    </div>
                    <div class="hidden md:block">
                        <div class="ml-10 flex items-baseline space-x-4">
                            <span class="text-blue-100 px-3 py-2 text-sm font-medium">
                                Welcome to Admin Panel
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="flex items-center justify-center min-h-[calc(100vh-4rem)] py-12 px-4 sm:px-6 lg:px-8">
            <div class="max-w-4xl w-full space-y-8">
                <!-- Header Section -->
                <div class="text-center">
                    <h2 class="mt-6 text-4xl font-bold text-white">
                        Admin Dashboard
                    </h2>
                    <p class="mt-4 text-lg text-blue-100 max-w-2xl mx-auto">
                        Welcome to your application's admin panel. Manage your system efficiently with powerful tools and insights.
                    </p>
                </div>

                <!-- Cards Section -->
                @if(App\Models\User::count() < 3)
                <div class="grid md:grid-cols-2 gap-8 mt-12">
                @else
                <div class="grid md:grid-cols-1 gap-2 mt-12">
                @endif
                    <!-- Admin Login Card -->
                    <div class="glass-morphism rounded-2xl shadow-xl p-8 hover-lift hover:bg-white/15">
                        <div class="flex items-center justify-center w-12 h-12 bg-blue-500 rounded-lg mb-6">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-white mb-4">Admin Login</h3>
                        <p class="text-blue-100 mb-6">
                            Access your admin dashboard with secure credentials to manage your application.
                        </p>
                        <a href="/admin/login" 
                           class="inline-flex items-center justify-center w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-6 rounded-lg transition-all duration-200 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            Login to Dashboard
                            <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </a>
                    </div>

                    <!-- Admin Register Card -->
                    @if(App\Models\User::count() < 3)
                    <div class="glass-morphism rounded-2xl shadow-xl p-8 hover-lift hover:bg-white/15">
                        <div class="flex items-center justify-center w-12 h-12 bg-green-500 rounded-lg mb-6">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-white mb-4">Create Admin Account</h3>
                        <p class="text-blue-100 mb-6">
                            Register a new admin account to get started with managing your application.
                        </p>
                        <a href="/admin/register" 
                           class="inline-flex items-center justify-center w-full bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-6 rounded-lg transition-all duration-200 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                            Register New Admin
                            <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                        </a>
                    </div>
                    @endif
                </div>

                <!-- Features Section -->
                <div class="mt-16 grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center">
                        <div class="flex items-center justify-center w-10 h-10 bg-blue-500 rounded-full mx-auto mb-4">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h4 class="text-white font-semibold">Secure Access</h4>
                        <p class="text-blue-100 text-sm mt-2">Protected admin authentication system</p>
                    </div>
                    <div class="text-center">
                        <div class="flex items-center justify-center w-10 h-10 bg-purple-500 rounded-full mx-auto mb-4">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <h4 class="text-white font-semibold">Full Control</h4>
                        <p class="text-blue-100 text-sm mt-2">Complete management of your application</p>
                    </div>
                    <div class="text-center">
                        <div class="flex items-center justify-center w-10 h-10 bg-indigo-500 rounded-full mx-auto mb-4">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <h4 class="text-white font-semibold">Fast & Reliable</h4>
                        <p class="text-blue-100 text-sm mt-2">Optimized performance for admin tasks</p>
                    </div>
                </div>

                <!-- Footer -->
                <div class="mt-16 text-center">
                    <p class="text-blue-200 text-sm">
                        &copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. All rights reserved.
                    </p>
                </div>
            </div>
        </div>
    </body>
</html>