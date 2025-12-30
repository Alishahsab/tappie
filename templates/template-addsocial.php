<?php
/* 
	Template Name: User Edit Profile
*/

get_header();

if (!is_user_logged_in()) {
    wp_redirect(site_url());
    exit;
}

$uid = get_current_user_id();

$userinfo = get_user_by('id', $uid);
$firstname = $userinfo->first_name;
$lastname = $userinfo->last_name;
$userbio = $userinfo->description;
$userphone = $userinfo->ext_phone;
$ext_location = $userinfo->ext_location;
$tapcode = get_user_meta($uid, 'passcodes', true);
$saved_socials = tappie_get_available_profiles();

// Profile image handling
$userimg = get_user_meta($uid, 'author_pic', true);
if (empty($userimg)) {
    $userimg = get_avatar_url($uid);
}

// Handle profile image upload
$upload_message = '';
if (isset($_FILES['tappie-prifleimg']) && !empty($_FILES['tappie-prifleimg']['name'])) {
    check_admin_referer('tappie_update_profile', 'tappie_profile_nonce');

    require_once(ABSPATH . 'wp-admin/includes/file.php');
    $file = $_FILES['tappie-prifleimg'];
    $upload = wp_handle_upload($file, array('test_form' => false));

    if (isset($upload['error'])) {
        $upload_message = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">' . esc_html($upload['error']) . '</div>';
    } else {
        update_user_meta($uid, 'author_pic', $upload['url']);
        $userimg = $upload['url'];
        $upload_message = '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">Profile picture updated successfully!</div>';
    }
}
?>

<section class="bg-gray-50 min-h-screen">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-64 bg-white border-r border-gray-200 flex-shrink-0">
            <div class="p-6">
                <div class="flex justify-center mb-8">
                    <img src="<?php echo esc_url(home_url('/images/logo.png')); ?>" alt="Logo" class="h-12">
                </div>

                <div class="space-y-6 text-center">
                    <div>
                        <img src="<?php echo esc_url($userimg); ?>?v=<?php echo time(); ?>" 
                             alt="Profile" 
                             class="w-32 h-32 rounded-full object-cover mx-auto border-4 border-white shadow-lg">

                        <?php if ($upload_message) echo $upload_message; ?>

                        <p class="mt-4 text-lg font-medium text-gray-800">
                            <?php echo esc_html($firstname . ' ' . $lastname); ?>
                        </p>

                        <!-- Tip me toggle (static for now - you can make it functional later) -->
                        <div class="flex justify-center items-center mt-4">
                            <p class="text-sm text-gray-600 mr-3">Tip me</p>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" checked class="sr-only peer">
                                <div class="w-14 h-8 bg-gray-300 rounded-full peer peer-checked:bg-teal-500 transition"></div>
                                <div class="absolute left-1 top-1 w-6 h-6 bg-white rounded-full transition peer-checked:translate-x-6"></div>
                            </label>
                        </div>
                    </div>

                    <nav class="space-y-1">
                        <a href="#" class="flex items-center px-4 py-3 bg-gray-100 text-teal-600 rounded-lg font-medium">
                            <i class="fas fa-user mr-3"></i>
                            My Profile
                        </a>
                        <a href="#" class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-100 rounded-lg transition">
                            <i class="fas fa-chart-line mr-3"></i>
                            Earnings
                        </a>
                        <a href="#" class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-100 rounded-lg transition">
                            <i class="fas fa-users mr-3"></i>
                            Referrals
                        </a>
                        <a href="#" class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-100 rounded-lg transition">
                            <i class="fas fa-cog mr-3"></i>
                            Settings
                        </a>
                    </nav>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto">
            <!-- Top Bar -->
            <div class="bg-white shadow-sm border-b border-gray-200 px-8 py-5 flex items-center justify-between">
                <h2 class="text-xl font-medium text-gray-800">My Profile</h2>
                <div class="flex items-center space-x-6">
                    <button class="text-gray-600 hover:text-gray-800">
                        <i class="fas fa-bell text-xl"></i>
                    </button>
                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-700">v2.tappie.social/<?php echo esc_html(strtolower($firstname . '_' . $lastname)); ?></span>
                        <button class="bg-teal-500 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center hover:bg-teal-600 transition">
                            <i class="fas fa-link mr-2"></i> Copy
                        </button>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 p-8">
                <!-- Form Section -->
                <div class="lg:col-span-2 space-y-8">
                    <div class="bg-white rounded-xl shadow-sm p-8">
                        <div class="flex items-center space-x-3 mb-8">
                            <i class="fas fa-user text-2xl text-teal-600"></i>
                            <h2 class="text-2xl font-bold text-gray-800">Your bio</h2>
                        </div>

                        <form id="tappie-updateprofile" method="POST" enctype="multipart/form-data">
                            <!-- Profile Image Upload -->
                            <div class="mb-10 text-center">
                                <img src="<?php echo esc_url($userimg); ?>?v=<?php echo time(); ?>" 
                                     alt="Profile" 
                                     class="w-32 h-32 rounded-full object-cover mx-auto border-4 border-white shadow-lg mb-4">
                                <div>
                                    <input type="file" name="tappie-prifleimg" id="tappie-prifleimg" class="hidden" accept="image/*">
                                    <label for="tappie-prifleimg" 
                                           class="cursor-pointer inline-block px-6 py-3 bg-teal-500 text-white rounded-lg hover:bg-teal-600 transition text-sm font-medium">
                                        Change Photo
                                    </label>
                                </div>
                            </div>

                            <!-- Name Fields -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                                    <div class="relative">
                                        <i class="fas fa-user absolute left-4 top-3.5 text-gray-400 text-lg"></i>
                                        <input type="text" name="ufname" value="<?php echo esc_attr($firstname); ?>"
                                               class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
                                               placeholder="First Name">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                                    <div class="relative">
                                        <i class="fas fa-user absolute left-4 top-3.5 text-gray-400 text-lg"></i>
                                        <input type="text" name="ulname" value="<?php echo esc_attr($lastname); ?>"
                                               class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
                                               placeholder="Last Name">
                                    </div>
                                </div>
                            </div>

                            <!-- Location -->
                            <div class="mb-8">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                                <div class="relative">
                                    <i class="fas fa-map-marker-alt absolute left-4 top-3.5 text-gray-400 text-lg"></i>
                                    <input type="text" name="ext_location" value="<?php echo esc_attr($ext_location); ?>"
                                           class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
                                           placeholder="City, Country">
                                </div>
                            </div>

                            <!-- Phone Number -->
                            <div class="mb-8">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                                <div class="relative">
                                    <i class="fas fa-phone absolute left-4 top-3.5 text-gray-400 text-lg"></i>
                                    <input type="text" name="uphone" value="<?php echo esc_attr($userphone); ?>"
                                           class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
                                           placeholder="Phone Number">
                                </div>
                            </div>

                            <!-- Bio -->
                            <div class="mb-8">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Bio</label>
                                <textarea name="ubio" rows="5"
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
                                          placeholder="Add Bio"><?php echo esc_textarea($userbio); ?></textarea>
                            </div>

                            <!-- Passcode Display -->
                            <?php if (!empty($tapcode)) : ?>
                                <div class="mb-8 p-6 bg-gray-50 rounded-lg">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Current Tappie Passcode</label>
                                    <p class="text-lg font-mono text-gray-800"><?php echo esc_html($tapcode); ?></p>
                                    <a href="<?php echo esc_url(site_url('/add-passcode')); ?>" 
                                       class="text-teal-600 hover:underline text-sm mt-2 inline-block">Change Tappie Passcode →</a>
                                </div>
                            <?php endif; ?>

                            <!-- Social Links -->
                            <?php if (!empty($saved_socials)) : ?>
                                <div class="space-y-6 mb-10">
                                    <h3 class="text-lg font-semibold text-gray-800">Social Links</h3>
                                    <?php foreach ($saved_socials as $value) :
                                        $postid = $value['id'];
                                        $help_text = get_the_excerpt($postid);
                                        $saved_value = tappie_get_url_by_key($value['key']);

                                        $iconHTML = '<i class="' . esc_attr($value['social_class'] ?? '') . ' text-xl"></i>';
                                        if (!empty($value['icon'])) {
                                            $iconHTML = '<img src="' . esc_url($value['icon']) . '" class="w-6 h-6" alt="">';
                                        } elseif (!empty($value['social_id'])) {
                                            $thumb = get_the_post_thumbnail_url($value['social_id'], 'full');
                                            if ($thumb) $iconHTML = '<img src="' . esc_url($thumb) . '" class="w-6 h-6" alt="">';
                                        }
                                    ?>
                                        <div class="relative">
                                            <?php if (!empty($help_text)) : ?>
                                                <div class="absolute -left-8 top-3 text-gray-400 hover:text-gray-600 cursor-help" title="<?php echo esc_attr($help_text); ?>">
                                                    <i class="fas fa-info-circle"></i>
                                                </div>
                                            <?php endif; ?>
                                            <div class="relative">
                                                <div class="absolute left-4 top-3.5 text-gray-400">
                                                    <?php echo $iconHTML; ?>
                                                </div>
                                                <input type="text" name="<?php echo esc_attr($value['key']); ?>"
                                                       value="<?php echo esc_attr($saved_value); ?>"
                                                       placeholder="<?php echo esc_attr($value['placeholder'] ?? ''); ?>"
                                                       class="w-full pl-16 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                                                <input type="hidden" name="<?php echo esc_attr($value['key']); ?>_id" value="<?php echo esc_attr($postid); ?>">
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <!-- Submit & Delete -->
                            <div class="flex justify-between items-center pt-6 border-t border-gray-200">
                                <button type="submit"
                                        class="bg-teal-500 hover:bg-teal-600 text-white font-medium py-3 px-8 rounded-lg transition shadow-md">
                                    Update Now
                                </button>

                                <?php if (!current_user_can('administrator')) : ?>
                                    <form method="POST" onsubmit="return confirm('Are you sure you want to delete your account? This cannot be undone.');" class="inline">
                                        <button type="submit" name="tappie-delete-account" value="delete"
                                                class="text-red-600 hover:text-red-700 font-medium">
                                            Delete My Account
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>

                            <input type="hidden" name="tappie-update-profile" value="update profile">
                            <?php wp_nonce_field('tappie_update_profile', 'tappie_profile_nonce'); ?>
                        </form>
                    </div>
                </div>

                <!-- Right Sidebar: Public Page Preview -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-sm p-6 sticky top-6">
                        <h3 class="text-center text-lg font-medium mb-6 text-gray-800">Public page preview</h3>
                        <div class="border border-gray-200 rounded-lg bg-gray-50 text-center p-8">
                            <img src="<?php echo esc_url($userimg); ?>?v=<?php echo time(); ?>" 
                                 alt="Profile" 
                                 class="w-32 h-32 rounded-full object-cover mx-auto border-4 border-white shadow-lg mb-6">

                            <h4 class="text-3xl font-bold text-gray-900"><?php echo esc_html($firstname . ' ' . $lastname); ?></h4>
                            
                            <?php if ($ext_location) : ?>
                                <p class="text-gray-600 mt-2 text-lg"><?php echo esc_html($ext_location); ?></p>
                            <?php endif; ?>

                            <!-- Example Role/Title (you can add a field later if needed) -->
                            <?php if (!empty($userbio)) : ?>
                                <p class="text-gray-600 mt-2 italic">Bad Girl</p>
                            <?php endif; ?>

                            <!-- Support Button -->
                            <a href="#" class="inline-block mt-8 px-8 py-3 bg-pink-500 text-white font-medium rounded-lg hover:bg-pink-600 transition">
                                <i class="fas fa-heart mr-2"></i> Support me
                            </a>

                            <!-- Social Buttons (dynamic preview - shows only if links exist) -->
                            <div class="grid grid-cols-2 gap-4 mt-8 max-w-xs mx-auto">
                                <?php 
                                $social_preview = ['facebook', 'instagram', 'twitter', 'youtube'];
                                foreach ($social_preview as $platform) :
                                    $key = array_search($platform, array_column($saved_socials, 'key') ?: []);
                                    if ($key !== false && !empty(tappie_get_url_by_key($platform))) :
                                ?>
                                    <button class="bg-pink-500 hover:bg-pink-600 text-white py-3 rounded-lg flex items-center justify-center gap-2 transition">
                                        <i class="fab fa-<?php echo esc_attr($platform); ?>"></i> <?php echo ucfirst($platform); ?>
                                    </button>
                                <?php 
                                    endif;
                                endforeach;
                                ?>
                            </div>

                            <!-- About Section -->
                            <?php if ($userbio) : ?>
                                <div class="mt-10 text-left">
                                    <h5 class="text-xl font-bold text-gray-800 mb-3">About</h5>
                                    <p class="text-gray-700 leading-relaxed"><?php echo nl2br(esc_html($userbio)); ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</section>

<?php get_footer(); ?>