<?php
require_once 'auth.php';
$auth->requireLogin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Admin Dashboard'; ?> | Kartikeya Realty</title>
    <link rel="icon" href="../logo.jpeg" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#0F2C59',
                        secondary: '#D4AF37',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-primary text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <img src="../logo.jpeg" alt="Kartikeya Realty" class="h-10 mr-3">
                    <span class="text-xl font-bold">Admin Panel</span>
                </div>
                
                <div class="hidden md:flex items-center space-x-8">
                    <a href="dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'text-secondary' : 'text-white hover:text-secondary'; ?> transition duration-300">
                        <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                    </a>
                    <a href="properties.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'properties.php' ? 'text-secondary' : 'text-white hover:text-secondary'; ?> transition duration-300">
                        <i class="fas fa-home mr-2"></i>Properties
                    </a>
                    <a href="contact-info.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'contact-info.php' ? 'text-secondary' : 'text-white hover:text-secondary'; ?> transition duration-300">
                        <i class="fas fa-address-book mr-2"></i>Contact Info
                    </a>
                </div>
                
                <div class="flex items-center space-x-4">
                    <span class="text-sm">Welcome, <?php echo htmlspecialchars($auth->getAdminUsername()); ?></span>
                    <a href="../index.html" target="_blank" class="text-white hover:text-secondary transition duration-300">
                        <i class="fas fa-external-link-alt mr-1"></i>View Site
                    </a>
                    <a href="logout.php" class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded-lg transition duration-300">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </a>
                </div>
                
                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button id="mobile-menu-btn" class="text-white hover:text-secondary">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Mobile menu -->
        <div id="mobile-menu" class="md:hidden hidden bg-primary border-t border-blue-800">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <a href="dashboard.php" class="block px-3 py-2 text-white hover:text-secondary">
                    <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                </a>
                <a href="properties.php" class="block px-3 py-2 text-white hover:text-secondary">
                    <i class="fas fa-home mr-2"></i>Properties
                </a>
                <a href="contact-info.php" class="block px-3 py-2 text-white hover:text-secondary">
                    <i class="fas fa-address-book mr-2"></i>Contact Info
                </a>
                <a href="../index.html" target="_blank" class="block px-3 py-2 text-white hover:text-secondary">
                    <i class="fas fa-external-link-alt mr-2"></i>View Site
                </a>
            </div>
        </div>
    </nav>

    <script>
        // Mobile menu toggle
        document.getElementById('mobile-menu-btn').addEventListener('click', function() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        });
    </script> 