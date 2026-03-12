<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <main class="container mx-auto px-4 py-8">

        <!-- Page Title -->
        <section class="text-center mb-8">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-2">My Profile</h1>
            <p class="text-gray-600 text-lg">View your account information</p>
        </section>

        <div class="max-w-2xl mx-auto">
            <div class="bg-white rounded-xl shadow-md overflow-hidden mb-8">

                <!-- Header -->
                <div class="bg-gradient-to-b from-green-900 to-yellow-500 p-4 text-white">
                    <h2 class="text-xl font-bold flex items-center">
                        <i class="fas fa-user-circle mr-2"></i>
                        USER PROFILE
                    </h2>
                </div>

                <!-- Profile Info -->
                <div class="p-6">
                    <div class="space-y-6">

                        <div>
                            <label class="block text-gray-700 text-sm font-medium mb-2">
                                <i class="fas fa-id-card mr-2 text-green-600"></i>User ID
                            </label>
                            <p class="text-gray-900 font-semibold text-lg"><?= esc($user['user_id']) ?></p>
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-medium mb-2">
                                <i class="fas fa-user mr-2 text-green-600"></i>Username
                            </label>
                            <p class="text-gray-900"><?= esc($user['username']) ?></p>
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-medium mb-2">
                                <i class="fas fa-user mr-2 text-green-600"></i>NAME
                            </label>
                            <p class="text-gray-900"><?= esc($user['username']) ?></p>
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-medium mb-2">
                                <i class="fas fa-envelope mr-2 text-green-600"></i>Email
                            </label>
                            <p class="text-gray-900"><?= esc($user['email']) ?></p>
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-medium mb-2">
                                <i class="fas fa-user-tag mr-2 text-green-600"></i>Role
                            </label>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                bg-blue-100 text-blue-800">
                                <i class="fas fa-shield-alt mr-1"></i>
                                <?= ucfirst($user['role']) ?>
                            </span>
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-medium mb-2">
                                <i class="fas fa-calendar-plus mr-2 text-green-600"></i>Account Created
                            </label>
                            <p class="text-gray-900">
                                <?= date("F d, Y", strtotime($user['created_at'])) ?>
                            </p>
                            <label class="block text-gray-700 text-sm font-medium mb-2">
                                <i class="fas fa-calendar-plus mr-2 text-green-600"></i>Last Updated
                            </label>
                            <p class="text-gray-900">
                                <?= "something date" ?>
                            </p>
                        </div>

                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-between items-center mt-8 pt-6 border-t border-gray-200">

                        <a href="<?= base_url('dashboard') ?>" 
                           class="text-gray-600 hover:text-gray-800 font-medium flex items-center">
                            <i class="fas fa-arrow-left mr-2"></i> Back
                        </a>

                        <a href="<?= base_url('user/edit/' . $user['user_id']) ?>"
                           class="bg-green-600 hover:bg-yellow-500 text-white px-6 py-3 rounded-lg font-medium flex items-center transition duration-200 shadow-md hover:shadow-lg">
                            <i class="fas fa-edit mr-2"></i>Edit Profile
                        </a>
                    </div>
                    <div class="flex justify-between items-center mt-8 pt-6 border-t border-gray-200">

                        <a href="<?= base_url('user/deactivate')?>"
                           class="bg-green-600 hover:bg-yellow-500 text-white px-6 py-3 rounded-lg font-medium flex items-center transition duration-200 shadow-md hover:shadow-lg">
                            <i class="fas fa-edit mr-2"></i>Deactivate Account
                        </a>

                        <a href="<?= base_url('password/change') ?>"
                           class="bg-green-600 hover:bg-yellow-500 text-white px-6 py-3 rounded-lg font-medium flex items-center transition duration-200 shadow-md hover:shadow-lg">
                            <i class="fas fa-edit mr-2"></i>Change Password
                        </a>
                    </div>
                </div>
                
            </div>
            
        </div>
        
    </main>
</body>
</html>
<?= $this->endSection() ?>
