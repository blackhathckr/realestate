<?php
$page_title = 'Properties Management';
require_once 'includes/header.php';
require_once 'includes/property-functions.php';

$propertyManager = new PropertyManager();
$action = $_GET['action'] ?? 'list';
$property_id = $_GET['id'] ?? null;
$success = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if ($action === 'add') {
            $errors = $propertyManager->validatePropertyData($_POST);
            if (empty($errors)) {
                $image_file = $_FILES['image'] ?? null;
                $property_id = $propertyManager->createProperty($_POST, $image_file);
                $success = 'Property added successfully!';
                $action = 'list';
            } else {
                $error = implode('<br>', $errors);
            }
        } elseif ($action === 'edit' && $property_id) {
            $errors = $propertyManager->validatePropertyData($_POST);
            if (empty($errors)) {
                $image_file = $_FILES['image'] ?? null;
                $propertyManager->updateProperty($property_id, $_POST, $image_file);
                $success = 'Property updated successfully!';
                $action = 'list';
            } else {
                $error = implode('<br>', $errors);
            }
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Handle delete action
if ($action === 'delete' && $property_id) {
    try {
        $propertyManager->deleteProperty($property_id);
        $success = 'Property deleted successfully!';
        $action = 'list';
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Get data for edit form
$property = null;
if ($action === 'edit' && $property_id) {
    $property = $propertyManager->getProperty($property_id);
    if (!$property) {
        $error = 'Property not found!';
        $action = 'list';
    }
}

// Get all properties for list view
$properties = [];
if ($action === 'list') {
    $properties = $propertyManager->getAllProperties();
}
?>

<div class="max-w-7xl mx-auto py-6 px-4">
    <!-- Page Header -->
    <div class="md:flex md:items-center md:justify-between mb-8">
        <div class="flex-1 min-w-0">
            <h1 class="text-3xl font-bold text-gray-900">
                <?php 
                echo $action === 'add' ? 'Add Property' : 
                     ($action === 'edit' ? 'Edit Property' : 'Properties Management'); 
                ?>
            </h1>
        </div>
        <?php if ($action === 'list'): ?>
            <div class="mt-4 md:mt-0">
                <a href="properties.php?action=add" class="bg-primary hover:bg-secondary text-white font-medium py-2 px-4 rounded-lg transition duration-300">
                    <i class="fas fa-plus mr-2"></i>Add Property
                </a>
            </div>
        <?php else: ?>
            <div class="mt-4 md:mt-0">
                <a href="properties.php" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg transition duration-300">
                    <i class="fas fa-arrow-left mr-2"></i>Back to List
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Success/Error Messages -->
    <?php if ($success): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            <i class="fas fa-check-circle mr-2"></i>
            <?php echo htmlspecialchars($success); ?>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <i class="fas fa-exclamation-circle mr-2"></i>
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <?php if ($action === 'list'): ?>
        <!-- Properties List -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">All Properties (<?php echo count($properties); ?>)</h3>
            </div>
            
            <?php if (empty($properties)): ?>
                <div class="text-center py-12">
                    <i class="fas fa-home text-gray-400 text-4xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Properties Found</h3>
                    <p class="text-gray-500 mb-6">Get started by adding your first property listing.</p>
                    <a href="properties.php?action=add" class="bg-primary hover:bg-secondary text-white font-medium py-2 px-4 rounded-lg transition duration-300">
                        <i class="fas fa-plus mr-2"></i>Add Property
                    </a>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Property</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cost</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($properties as $prop): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-16 w-16">
                                                <?php if ($prop['image']): ?>
                                                    <img class="h-16 w-16 rounded-lg object-cover" src="<?php echo htmlspecialchars($propertyManager->getImageUrl($prop['image'])); ?>" alt="Property">
                                                <?php else: ?>
                                                    <div class="h-16 w-16 rounded-lg bg-gray-200 flex items-center justify-center">
                                                        <i class="fas fa-home text-gray-400"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($prop['title']); ?></div>
                                                <div class="text-sm text-gray-500">ID: <?php echo $prop['id']; ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <?php echo htmlspecialchars($prop['category']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo htmlspecialchars($prop['cost']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            <?php echo $prop['status'] === 'For Sale' ? 'bg-green-100 text-green-800' : 
                                                      ($prop['status'] === 'For Rent' ? 'bg-blue-100 text-blue-800' : 
                                                      ($prop['status'] === 'For Lease' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800')); ?>">
                                            <?php echo htmlspecialchars($prop['status']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo htmlspecialchars($prop['location']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="properties.php?action=edit&id=<?php echo $prop['id']; ?>" class="text-primary hover:text-secondary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="properties.php?action=delete&id=<?php echo $prop['id']; ?>" 
                                               class="text-red-600 hover:text-red-900"
                                               onclick="return confirm('Are you sure you want to delete this property? This action cannot be undone.')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

    <?php elseif ($action === 'add' || $action === 'edit'): ?>
        <!-- Add/Edit Property Form -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">
                    <?php echo $action === 'add' ? 'Add New Property' : 'Edit Property'; ?>
                </h3>
            </div>
            
            <form method="POST" enctype="multipart/form-data" class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Title -->
                    <div class="md:col-span-2">
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Property Title *</label>
                        <input type="text" id="title" name="title" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                               value="<?php echo htmlspecialchars($property['title'] ?? $_POST['title'] ?? ''); ?>">
                    </div>

                    <!-- Category -->
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                        <select id="category" name="category" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                            <option value="">Select Category</option>
                            <?php 
                            $categories = ['Residential', 'Commercial', 'Land', 'Farmhouse'];
                            $selected_category = $property['category'] ?? $_POST['category'] ?? '';
                            foreach ($categories as $cat): 
                            ?>
                                <option value="<?php echo $cat; ?>" <?php echo $selected_category === $cat ? 'selected' : ''; ?>>
                                    <?php echo $cat; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                        <select id="status" name="status" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                            <option value="">Select Status</option>
                            <?php 
                            $statuses = ['For Sale', 'For Rent', 'For Lease', 'Sold', 'Rented'];
                            $selected_status = $property['status'] ?? $_POST['status'] ?? '';
                            foreach ($statuses as $status): 
                            ?>
                                <option value="<?php echo $status; ?>" <?php echo $selected_status === $status ? 'selected' : ''; ?>>
                                    <?php echo $status; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Cost -->
                    <div>
                        <label for="cost" class="block text-sm font-medium text-gray-700 mb-2">Cost *</label>
                        <input type="text" id="cost" name="cost" required
                               placeholder="e.g., ₹85 Lakhs, ₹1.2 Cr, ₹35,000/month"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                               value="<?php echo htmlspecialchars($property['cost'] ?? $_POST['cost'] ?? ''); ?>">
                    </div>

                    <!-- Location -->
                    <div>
                        <label for="location" class="block text-sm font-medium text-gray-700 mb-2">Location *</label>
                        <input type="text" id="location" name="location" required
                               placeholder="e.g., Sector 14, Faridabad"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                               value="<?php echo htmlspecialchars($property['location'] ?? $_POST['location'] ?? ''); ?>">
                    </div>

                    <!-- Bedrooms -->
                    <div>
                        <label for="bedrooms" class="block text-sm font-medium text-gray-700 mb-2">Bedrooms</label>
                        <input type="number" id="bedrooms" name="bedrooms" min="0"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                               value="<?php echo htmlspecialchars($property['bedrooms'] ?? $_POST['bedrooms'] ?? ''); ?>">
                    </div>

                    <!-- Bathrooms -->
                    <div>
                        <label for="bathrooms" class="block text-sm font-medium text-gray-700 mb-2">Bathrooms</label>
                        <input type="number" id="bathrooms" name="bathrooms" min="0"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                               value="<?php echo htmlspecialchars($property['bathrooms'] ?? $_POST['bathrooms'] ?? ''); ?>">
                    </div>

                    <!-- Area -->
                    <div>
                        <label for="area" class="block text-sm font-medium text-gray-700 mb-2">Area</label>
                        <input type="text" id="area" name="area"
                               placeholder="e.g., 1800 sq.ft, 300 sq.yards"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                               value="<?php echo htmlspecialchars($property['area'] ?? $_POST['area'] ?? ''); ?>">
                    </div>

                    <!-- Property Image -->
                    <div class="md:col-span-2">
                        <label for="image" class="block text-sm font-medium text-gray-700 mb-2">Property Image</label>
                        <?php if ($action === 'edit' && $property['image']): ?>
                            <div class="mb-4">
                                <img src="<?php echo htmlspecialchars($propertyManager->getImageUrl($property['image'])); ?>" 
                                     alt="Current property image" class="h-32 w-48 object-cover rounded-lg">
                                <p class="text-sm text-gray-500 mt-2">Current image (upload new to replace)</p>
                            </div>
                        <?php endif; ?>
                        <input type="file" id="image" name="image" accept="image/*"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                        <p class="text-sm text-gray-500 mt-1">Accepted formats: JPEG, PNG, GIF, WebP. Max size: 5MB</p>
                    </div>

                    <!-- Description -->
                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea id="description" name="description" rows="4"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                                  placeholder="Detailed description of the property..."><?php echo htmlspecialchars($property['description'] ?? $_POST['description'] ?? ''); ?></textarea>
                    </div>

                    <!-- Features -->
                    <div class="md:col-span-2">
                        <label for="features" class="block text-sm font-medium text-gray-700 mb-2">Features</label>
                        <textarea id="features" name="features" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                                  placeholder="e.g., Swimming Pool, Garden, Parking, Lift, etc."><?php echo htmlspecialchars($property['features'] ?? $_POST['features'] ?? ''); ?></textarea>
                    </div>
                </div>

                <div class="mt-8 flex justify-end space-x-4">
                    <a href="properties.php" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-6 rounded-lg transition duration-300">
                        Cancel
                    </a>
                    <button type="submit" class="bg-primary hover:bg-secondary text-white font-medium py-2 px-6 rounded-lg transition duration-300">
                        <i class="fas fa-save mr-2"></i>
                        <?php echo $action === 'add' ? 'Add Property' : 'Update Property'; ?>
                    </button>
                </div>
            </form>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?> 