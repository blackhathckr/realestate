<?php
$page_title = 'Contact Information Management';
require_once 'includes/header.php';
require_once 'includes/contact-functions.php';

$contactManager = new ContactManager();
$action = $_GET['action'] ?? 'list';
$contact_id = $_GET['id'] ?? null;
$success = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if ($action === 'add') {
            $errors = $contactManager->validateContactData($_POST);
            if (empty($errors)) {
                $contact_id = $contactManager->createContactInfo($_POST);
                $success = 'Contact information added successfully!';
                $action = 'list';
            } else {
                $error = implode('<br>', $errors);
            }
        } elseif ($action === 'edit' && $contact_id) {
            $errors = $contactManager->validateContactData($_POST);
            if (empty($errors)) {
                $contactManager->updateContactInfo($contact_id, $_POST);
                $success = 'Contact information updated successfully!';
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
if ($action === 'delete' && $contact_id) {
    try {
        $contactManager->deleteContactInfo($contact_id);
        $success = 'Contact information deleted successfully!';
        $action = 'list';
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Get data for edit form
$contact = null;
if ($action === 'edit' && $contact_id) {
    $contact = $contactManager->getContactInfo($contact_id);
    if (!$contact) {
        $error = 'Contact information not found!';
        $action = 'list';
    }
}

// Get all contact info for list view
$contacts = [];
$contact_counts = [];
if ($action === 'list') {
    $contacts = $contactManager->getAllContactInfo();
    $contact_counts = $contactManager->getContactTypeCounts();
}
?>

<div class="max-w-7xl mx-auto py-6 px-4">
    <!-- Page Header -->
    <div class="md:flex md:items-center md:justify-between mb-8">
        <div class="flex-1 min-w-0">
            <h1 class="text-3xl font-bold text-gray-900">
                <?php 
                echo $action === 'add' ? 'Add Contact Information' : 
                     ($action === 'edit' ? 'Edit Contact Information' : 'Contact Information Management'); 
                ?>
            </h1>
            <p class="mt-1 text-gray-600">Manage your contact details, phone numbers, addresses, and social media links</p>
        </div>
        <?php if ($action === 'list'): ?>
            <div class="mt-4 md:mt-0">
                <a href="contact-info.php?action=add" class="bg-primary hover:bg-secondary text-white font-medium py-2 px-4 rounded-lg transition duration-300">
                    <i class="fas fa-plus mr-2"></i>Add Contact Info
                </a>
            </div>
        <?php else: ?>
            <div class="mt-4 md:mt-0">
                <a href="contact-info.php" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg transition duration-300">
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
        <!-- Contact Type Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <?php 
            $type_icons = [
                'phone' => 'fas fa-phone',
                'email' => 'fas fa-envelope',
                'address' => 'fas fa-map-marker-alt',
                'social' => 'fab fa-share-alt'
            ];
            $type_colors = [
                'phone' => 'bg-blue-500',
                'email' => 'bg-green-500',
                'address' => 'bg-yellow-500',
                'social' => 'bg-purple-500'
            ];
            
            foreach (['phone', 'email', 'address', 'social'] as $type):
                $count = $contact_counts[$type] ?? ['total' => 0, 'active' => 0];
            ?>
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 <?php echo $type_colors[$type]; ?> rounded-md flex items-center justify-center">
                                    <i class="<?php echo $type_icons[$type]; ?> text-white text-sm"></i>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate"><?php echo ucfirst($type); ?></dt>
                                    <dd class="text-lg font-medium text-gray-900"><?php echo $count['active']; ?>/<?php echo $count['total']; ?></dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Contact Information List -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">All Contact Information (<?php echo count($contacts); ?>)</h3>
            </div>
            
            <?php if (empty($contacts)): ?>
                <div class="text-center py-12">
                    <i class="fas fa-address-book text-gray-400 text-4xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Contact Information Found</h3>
                    <p class="text-gray-500 mb-6">Get started by adding your contact details.</p>
                    <a href="contact-info.php?action=add" class="bg-primary hover:bg-secondary text-white font-medium py-2 px-4 rounded-lg transition duration-300">
                        <i class="fas fa-plus mr-2"></i>Add Contact Info
                    </a>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Label</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Value</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($contacts as $info): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 w-8 h-8 <?php echo $type_colors[$info['type']] ?? 'bg-gray-500'; ?> rounded-md flex items-center justify-center mr-3">
                                                <i class="<?php echo $type_icons[$info['type']] ?? 'fas fa-info'; ?> text-white text-sm"></i>
                                            </div>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                <?php echo ucfirst(htmlspecialchars($info['type'])); ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($info['label']); ?></div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 max-w-xs truncate">
                                            <?php if ($info['type'] === 'email'): ?>
                                                <a href="mailto:<?php echo htmlspecialchars($info['value']); ?>" class="text-primary hover:text-secondary">
                                                    <?php echo htmlspecialchars($info['value']); ?>
                                                </a>
                                            <?php elseif ($info['type'] === 'phone'): ?>
                                                <a href="tel:<?php echo htmlspecialchars($info['value']); ?>" class="text-primary hover:text-secondary">
                                                    <?php echo htmlspecialchars($info['value']); ?>
                                                </a>
                                            <?php elseif ($info['type'] === 'social' && $info['value'] !== '#'): ?>
                                                <a href="<?php echo htmlspecialchars($info['value']); ?>" target="_blank" class="text-primary hover:text-secondary">
                                                    <?php echo htmlspecialchars($info['value']); ?>
                                                </a>
                                            <?php else: ?>
                                                <?php echo htmlspecialchars($info['value']); ?>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            <?php echo $info['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                            <?php echo $info['is_active'] ? 'Active' : 'Inactive'; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo $info['sort_order']; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="contact-info.php?action=edit&id=<?php echo $info['id']; ?>" class="text-primary hover:text-secondary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="contact-info.php?action=delete&id=<?php echo $info['id']; ?>" 
                                               class="text-red-600 hover:text-red-900"
                                               onclick="return confirm('Are you sure you want to delete this contact information? This action cannot be undone.')">
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
        <!-- Add/Edit Contact Info Form -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">
                    <?php echo $action === 'add' ? 'Add New Contact Information' : 'Edit Contact Information'; ?>
                </h3>
            </div>
            
            <form method="POST" class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Type -->
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Type *</label>
                        <select id="type" name="type" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                            <option value="">Select Type</option>
                            <?php 
                            $types = [
                                'phone' => 'Phone Number',
                                'email' => 'Email Address',
                                'address' => 'Address',
                                'social' => 'Social Media'
                            ];
                            $selected_type = $contact['type'] ?? $_POST['type'] ?? '';
                            foreach ($types as $value => $label): 
                            ?>
                                <option value="<?php echo $value; ?>" <?php echo $selected_type === $value ? 'selected' : ''; ?>>
                                    <?php echo $label; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Label -->
                    <div>
                        <label for="label" class="block text-sm font-medium text-gray-700 mb-2">Label *</label>
                        <input type="text" id="label" name="label" required
                               placeholder="e.g., Primary Phone, Faridabad Office, Facebook"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                               value="<?php echo htmlspecialchars($contact['label'] ?? $_POST['label'] ?? ''); ?>">
                    </div>

                    <!-- Value -->
                    <div class="md:col-span-2">
                        <label for="value" class="block text-sm font-medium text-gray-700 mb-2">Value *</label>
                        <input type="text" id="value" name="value" required
                               placeholder="Enter the contact information"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                               value="<?php echo htmlspecialchars($contact['value'] ?? $_POST['value'] ?? ''); ?>">
                        <p class="text-sm text-gray-500 mt-1" id="value-help">
                            Enter the appropriate contact information based on the type selected.
                        </p>
                    </div>

                    <!-- Sort Order -->
                    <div>
                        <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-2">Sort Order</label>
                        <input type="number" id="sort_order" name="sort_order" min="0"
                               placeholder="0"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                               value="<?php echo htmlspecialchars($contact['sort_order'] ?? $_POST['sort_order'] ?? '0'); ?>">
                        <p class="text-sm text-gray-500 mt-1">Lower numbers appear first</p>
                    </div>

                    <!-- Active Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <div class="flex items-center">
                            <input type="checkbox" id="is_active" name="is_active" value="1"
                                   class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded"
                                   <?php echo (!isset($contact) || $contact['is_active']) ? 'checked' : ''; ?>>
                            <label for="is_active" class="ml-2 block text-sm text-gray-900">
                                Active (show on website)
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex justify-end space-x-4">
                    <a href="contact-info.php" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-6 rounded-lg transition duration-300">
                        Cancel
                    </a>
                    <button type="submit" class="bg-primary hover:bg-secondary text-white font-medium py-2 px-6 rounded-lg transition duration-300">
                        <i class="fas fa-save mr-2"></i>
                        <?php echo $action === 'add' ? 'Add Contact Info' : 'Update Contact Info'; ?>
                    </button>
                </div>
            </form>
        </div>
    <?php endif; ?>
</div>

<script>
// Update placeholder and help text based on type selection
document.getElementById('type').addEventListener('change', function() {
    const valueInput = document.getElementById('value');
    const helpText = document.getElementById('value-help');
    
    switch(this.value) {
        case 'phone':
            valueInput.placeholder = 'e.g., +91 7091651137';
            helpText.textContent = 'Enter phone number with country code if needed.';
            break;
        case 'email':
            valueInput.placeholder = 'e.g., property@kartikeyarealty.com';
            helpText.textContent = 'Enter a valid email address.';
            break;
        case 'address':
            valueInput.placeholder = 'e.g., 716 Piyush Heights, Sector 89 Faridabad';
            helpText.textContent = 'Enter the complete address.';
            break;
        case 'social':
            valueInput.placeholder = 'e.g., https://facebook.com/kartikeyarealty';
            helpText.textContent = 'Enter complete URL for social media links or # for placeholder.';
            break;
        default:
            valueInput.placeholder = 'Enter the contact information';
            helpText.textContent = 'Enter the appropriate contact information based on the type selected.';
    }
});
</script>

<?php require_once 'includes/footer.php'; ?> 