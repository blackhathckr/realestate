<?php
$page_title = 'Dashboard';
require_once 'includes/header.php';

// Get statistics
try {
    $properties_count = $database->query("SELECT COUNT(*) as count FROM properties")->fetch()['count'];
    $active_properties = $database->query("SELECT COUNT(*) as count FROM properties WHERE status IN ('For Sale', 'For Rent', 'For Lease')")->fetch()['count'];
    $contact_info_count = $database->query("SELECT COUNT(*) as count FROM contact_info WHERE is_active = 1")->fetch()['count'];
    $recent_properties = $database->query("SELECT * FROM properties ORDER BY created_at DESC LIMIT 5")->fetchAll();
} catch (Exception $e) {
    $properties_count = 0;
    $active_properties = 0;
    $contact_info_count = 0;
    $recent_properties = [];
}
?>

<div class="max-w-7xl mx-auto py-6 px-4">
    <!-- Page Header -->
    <div class="md:flex md:items-center md:justify-between mb-8">
        <div class="flex-1 min-w-0">
            <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
            <p class="mt-1 text-gray-600">Welcome to your admin panel</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Properties -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-primary rounded-md flex items-center justify-center">
                            <i class="fas fa-home text-white text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Properties</dt>
                            <dd class="text-lg font-medium text-gray-900"><?php echo $properties_count; ?></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Properties -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                            <i class="fas fa-check text-white text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Active Listings</dt>
                            <dd class="text-lg font-medium text-gray-900"><?php echo $active_properties; ?></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Info -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-secondary rounded-md flex items-center justify-center">
                            <i class="fas fa-address-book text-white text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Contact Info</dt>
                            <dd class="text-lg font-medium text-gray-900"><?php echo $contact_info_count; ?></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                            <i class="fas fa-plus text-white text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Quick Actions</dt>
                            <dd class="text-lg font-medium text-gray-900">
                                <a href="properties.php?action=add" class="text-primary hover:text-secondary">Add Property</a>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Properties and Quick Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Properties -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Recent Properties</h3>
            </div>
            <div class="px-6 py-4">
                <?php if (empty($recent_properties)): ?>
                    <p class="text-gray-500 text-center py-4">No properties added yet.</p>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($recent_properties as $property): ?>
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <h4 class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($property['title']); ?></h4>
                                    <p class="text-sm text-gray-500"><?php echo htmlspecialchars($property['location']); ?> • <?php echo htmlspecialchars($property['cost']); ?></p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        <?php echo $property['status'] === 'For Sale' ? 'bg-green-100 text-green-800' : 
                                                  ($property['status'] === 'For Rent' ? 'bg-blue-100 text-blue-800' : 
                                                  ($property['status'] === 'For Lease' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800')); ?>">
                                        <?php echo htmlspecialchars($property['status']); ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <a href="properties.php" class="text-primary hover:text-secondary text-sm font-medium">
                        View all properties →
                    </a>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Quick Actions</h3>
            </div>
            <div class="px-6 py-4">
                <div class="space-y-4">
                    <!-- Password Change Form -->
                    <div class="p-4 border border-gray-200 rounded-lg">
                        <div class="flex items-center mb-4">
                            <div class="flex-shrink-0">
                                <i class="fas fa-key text-primary text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-sm font-medium text-gray-900">Change Password</h4>
                                <p class="text-sm text-gray-500">Update your admin account password</p>
                            </div>
                        </div>
                        <form id="passwordChangeForm" class="space-y-3">
                            <div>
                                <label for="currentPassword" class="block text-sm font-medium text-gray-700">Current Password</label>
                                <input type="password" id="currentPassword" name="currentPassword" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                            </div>
                            <div>
                                <label for="newPassword" class="block text-sm font-medium text-gray-700">New Password</label>
                                <input type="password" id="newPassword" name="newPassword" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                            </div>
                            <div>
                                <label for="confirmPassword" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                                <input type="password" id="confirmPassword" name="confirmPassword" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                            </div>
                            <div>
                                <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-secondary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                                    Update Password
                                </button>
                            </div>
                            <div id="passwordChangeMessage" class="mt-2 text-sm hidden"></div>
                        </form>
                    </div>

                    <!-- Existing Quick Actions -->
                    <a href="properties.php?action=add" class="block p-4 border border-gray-200 rounded-lg hover:border-primary hover:bg-gray-50 transition duration-300">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-plus-circle text-primary text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-sm font-medium text-gray-900">Add New Property</h4>
                                <p class="text-sm text-gray-500">Create a new property listing</p>
                            </div>
                        </div>
                    </a>
                    
                    <a href="contact-info.php" class="block p-4 border border-gray-200 rounded-lg hover:border-primary hover:bg-gray-50 transition duration-300">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-edit text-primary text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-sm font-medium text-gray-900">Manage Contact Info</h4>
                                <p class="text-sm text-gray-500">Update contact details and social links</p>
                            </div>
                        </div>
                    </a>
                    
                    <a href="../index.html" target="_blank" class="block p-4 border border-gray-200 rounded-lg hover:border-primary hover:bg-gray-50 transition duration-300">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-external-link-alt text-primary text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-sm font-medium text-gray-900">View Website</h4>
                                <p class="text-sm text-gray-500">See how your changes look on the live site</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add JavaScript for password change form -->
<script>
document.getElementById('passwordChangeForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const messageDiv = document.getElementById('passwordChangeMessage');
    const formData = {
        currentPassword: document.getElementById('currentPassword').value,
        newPassword: document.getElementById('newPassword').value,
        confirmPassword: document.getElementById('confirmPassword').value
    };
    
    try {
        const response = await fetch('../api/change-password.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        });
        
        const result = await response.json();
        
        messageDiv.textContent = result.message;
        messageDiv.className = `mt-2 text-sm ${result.success ? 'text-green-600' : 'text-red-600'}`;
        messageDiv.classList.remove('hidden');
        
        if (result.success) {
            document.getElementById('passwordChangeForm').reset();
        }
    } catch (error) {
        messageDiv.textContent = 'An error occurred while updating the password';
        messageDiv.className = 'mt-2 text-sm text-red-600';
        messageDiv.classList.remove('hidden');
    }
});
</script>

<?php require_once 'includes/footer.php'; ?> 