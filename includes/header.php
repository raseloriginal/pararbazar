<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Parar Bazar</title>
    <link rel="manifest" href="/pararbazar/manifest.json">
    <meta name="theme-color" content="#16a34a">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f9fafb;
            -webkit-tap-highlight-color: transparent;
        }
        
        /* Hide scrollbar for sliding categories */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        
        .soft-shadow {
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05);
        }
        
        /* Premium square/soft-square styling */
        .premium-card {
            border-radius: 12px;
            background: #ffffff;
            border: 1px solid #f3f4f6;
        }
    </style>
</head>
<body class="pb-24">
    
    <!-- Header -->
    <header class="bg-white sticky top-0 z-40 soft-shadow">
        <div class="px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-green-600 text-white rounded-lg flex items-center justify-center font-bold text-lg">
                    P
                </div>
                <h1 class="text-xl font-bold text-gray-800">Parar Bazar</h1>
            </div>
            
            <div class="flex items-center gap-4 text-gray-600">
                <button id="searchBtn" class="text-xl">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
                <a href="/pararbazar/profile" class="text-xl">
                    <i class="fa-regular fa-circle-user"></i>
                </a>
            </div>
        </div>
        
        <!-- Search Bar (Hidden by default) -->
        <div id="searchContainer" class="hidden px-4 pb-3">
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                <input type="text" id="searchInput" placeholder="Search products..." class="w-full bg-gray-100 rounded-lg pl-10 pr-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 transition-all">
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main id="main-content">
