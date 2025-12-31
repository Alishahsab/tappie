<?php
/*
    Template Name: User Edit Profile
*/
get_header();
if (!is_user_logged_in()) {
    wp_redirect(site_url());
    exit();
}
$uid = get_current_user_id();
$userinfo = get_user_by("id", $uid);
$firstname = $userinfo->first_name;
$lastname = $userinfo->last_name;
$userbio = $userinfo->description;
$userphone = $userinfo->ext_phone;
$ext_location = $userinfo->ext_location ?? "";
$tapcode = get_user_meta($uid, "passcodes", true);
$saved_socials = tappie_get_available_profiles();

// Profile image
$userimg = get_user_meta($uid, "author_pic", true);
if (empty($userimg)) {
    $userimg = get_avatar_url($uid);
}
$upload_message = "";
$update_message = "";

// Handle profile image upload
if (
    isset($_FILES["tappie-prifleimg"]) &&
    !empty($_FILES["tappie-prifleimg"]["name"])
) {
    check_admin_referer("tappie_update_profile", "tappie_profile_nonce");
    require_once ABSPATH . "wp-admin/includes/file.php";
    $file = $_FILES["tappie-prifleimg"];
    $upload = wp_handle_upload($file, ["test_form" => false]);
    if (isset($upload["error"])) {
        $upload_message =
            '<div class="bg-red-100 border border-red-400 text-red-700 px-6 py-4 rounded-[5px] mb-6 flex items-center">
                            <i class="fas fa-exclamation-triangle text-2xl mr-3"></i>
                            <div><strong>Error:</strong> ' .
            esc_html($upload["error"]) .
            '</div>
                          </div>';
    } else {
        update_user_meta($uid, "author_pic", $upload["url"]);
        $userimg = $upload["url"];
        $upload_message = '<div class="bg-green-100 border border-green-400 text-green-700 px-6 py-4 rounded-[5px] mb-6 flex items-center">
                            <i class="fas fa-check-circle text-2xl mr-3"></i>
                            <div><strong>Success!</strong> Profile picture updated successfully.</div>
                          </div>';
    }
}

// Handle full profile update
if (
    isset($_POST["tappie-update-profilee"]) &&
    $_POST["tappie-update-profilee"] === "update profile"
) {
    check_admin_referer("tappie_update_profile", "tappie_profile_nonce");
    $ufname = trim(sanitize_text_field($_POST["ufname"] ?? ""));
    $ulname = trim(sanitize_text_field($_POST["ulname"] ?? ""));
    $ubio = sanitize_textarea_field($_POST["ubio"] ?? "");
    $ext_location = sanitize_text_field($_POST["ext_location"] ?? "");
    $uphone = sanitize_text_field($_POST["uphone"] ?? "");
    $new_full_name = trim($ufname . " " . $ulname);
    if (empty($new_full_name)) {
        $current_user = wp_get_current_user();
        $new_full_name = $current_user->user_login;
    }
    $update_result = wp_update_user([
        "ID" => $uid,
        "first_name" => $ufname,
        "last_name" => $ulname,
        "display_name" => $new_full_name,
        "description" => $ubio,
    ]);
// .....................

if (isset($_POST['tappie_color'])) {
    $color = trim($_POST['tappie_color']);

    if ($color === '') {
        // User removed color → delete saved meta
        delete_user_meta($uid, 'tappie_color');
    } elseif (preg_match('/^#[0-9A-Fa-f]{6}$/', $color)) {
        // Valid hex color → save it
        update_user_meta($uid, 'tappie_color', $color);
    } else {
        // Invalid → clean up
        delete_user_meta($uid, 'tappie_color');
    }
}
// ......
$uid = get_current_user_id();
$colorone = get_user_meta($uid, 'tappie_color-one', true);
$default_colorone = '#FFFFFF'; // default color
$colorone = !empty($colorone) ? $colorone : $default_colorone;

if (isset($_POST['tappie_color-one'])) {
    $posted_color = trim($_POST['tappie_color-one']);

    if ($posted_color === '') {
        delete_user_meta($uid, 'tappie_color-one');
        $colorone = $default_colorone;
    } elseif (preg_match('/^#[0-9A-Fa-f]{6}$/', $posted_color)) {
        update_user_meta($uid, 'tappie_color-one', $posted_color);
        $colorone = $posted_color;
    } else {
        delete_user_meta($uid, 'tappie_color-one');
        $colorone = $default_colorone;
    }
}
// ......................


    if (is_wp_error($update_result)) {
        $update_message =
            '<div class="bg-red-100 border border-red-400 text-red-700 px-6 py-4 rounded-[5px] mb-6">
                            <strong>Error:</strong> ' .
            esc_html($update_result->get_error_message()) .
            '
                          </div>';
    } else {
        update_user_meta($uid, "ext_location", $ext_location);
        update_user_meta($uid, "ext_phone", $uphone);
        if (isset($_POST["social_links"]) && is_array($_POST["social_links"])) {
            $social_links = [];
            foreach ($_POST["social_links"] as $link) {
                if (!empty($link["key"]) && !empty($link["url"])) {
                    $social_links[] = [
                        "key" => sanitize_text_field($link["key"]),
                        "url" => sanitize_text_field($link["url"]),
                        "label" => sanitize_text_field($link["label"] ?? ""),
                        "id" => absint($link["id"] ?? 0),
                    ];
                }
            }
            update_user_meta($uid, "saved_profiles", $social_links);
        }
        $update_message = '<div class="bg-green-100 border border-green-400 text-green-700 px-6 py-4 rounded-[5px] mb-6 flex items-center">
                            <i class="fas fa-check-circle text-2xl mr-3"></i>
                            <div><strong>Success!</strong> Your profile has been updated.</div>
                          </div>';
        $userinfo = get_user_by("id", $uid);
        $firstname = $userinfo->first_name;
        $lastname = $userinfo->last_name;
        $userbio = $userinfo->description;
        $ext_location = get_user_meta($uid, "ext_location", true);
        $userphone = get_user_meta($uid, "ext_phone", true);
    }
}

$saved_profiles = get_user_meta($uid, "saved_profiles", true);
if (!is_array($saved_profiles)) {
    $saved_profiles = [];
}

$full_name = trim($firstname . " " . $lastname);
$user_email = $userinfo->user_email;

 
?>
<?php
 
 
$colors = get_user_meta($uid, 'tappie_colors', true);

// $saved_color = get_user_meta($uid, 'tappie_color', true);
// echo '<pre>'; print_r($saved_color); echo '</pre>';
// echo 'Type: ' . gettype($saved_color);
// exit;
?>
 
<section class="bg-gray-50 min-h-screen flex relative">
    <!-- Fixed Left Sidebar -->
    <aside id="sidebar" class="w-64 bg-white border-r border-gray-200 fixed inset-y-0 left-0 z-50 overflow-y-auto transform -translate-x-full md:translate-x-0 transition-transform duration-300">
        <div class="relative p-6">
            <button id="sidebar-close-inside" class="absolute top-4 right-4 md:hidden text-gray-600 hover:text-gray-900 text-2xl">
                <i class="fas fa-times"></i>
            </button>
            <div class="flex justify-center mb-8">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/img/logo.png" alt="Logo" class="h-12 rounded-full">
            </div>
            <div class="space-y-6 text-center">
                <div>
                         <div>
                    <img id="desktop-preview-img" src="<?php echo esc_url(
                        $userimg,
                    ); ?>?v=<?php echo time(); ?>" 
                      alt="Profile" 
                       class="w-32 h-32 rounded-full object-cover object-top mx-auto border-4 border-white shadow-lg mb-4">
                    <p class="mt-4 text-lg font-medium text-gray-800 truncate px-4" title="<?php echo esc_attr(
                        $full_name,
                    ); ?>">
                        <?php echo esc_html(
                            wp_get_current_user()->display_name,
                        ); ?>
                    </p>
                       </div>
                      <nav class="space-y-1">
                    <a href="<?= esc_url(
                        site_url("/dashboard"),
                    ) ?>" class="flex items-center px-4 py-3 text-[14px] text-gray-600 hover:bg-gray-100 rounded-[5px] transition">
                        <i class="fas fa-chart-line mr-3"></i> Dashbord
                    </a>
                    <a href="http://localhost/tapsocial/settings/" class="flex text-[14px] items-center px-4 py-3 bg-gray-100 text-teal-600 rounded-[5px] font-medium">
                        <i class="fas fa-user mr-3"></i> Edit Profile
                    </a>
                </nav>
                </div>
                 <div class=" ">
                        <a href="<?php echo wp_logout_url(site_url()); ?>" 
                           class="inline-flex items-center   px-5 py-2.5 bg-[#54B7B4] w-full justify-center hover:bg-red-700 text-white text-sm font-medium rounded-[5px] transition shadow-sm">
                            <i class="fas fa-sign-out-alt mr-2"></i>
                            Logout
                        </a>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content Wrapper -->
    <div class="flex-1 md:ml-64 flex flex-col relative">
        <!-- Fixed Header -->
          <header class="bg-white shadow-sm border-b border-gray-200 px-4 md:px-8 py-5 flex items-center justify-between fixed top-0 left-0 right-0 md:left-64 z-40 h-20">
                <button id="sidebar-toggle" class="md:hidden text-gray-700 text-xl">
                    <i class="fas fa-bars"></i>
                </button>
                <h2 class="text-xl font-medium hidden lg:block text-gray-800">Edit Profile</h2>
                <div class="flex items-center space-x-3 lg:space-x-6">
                    <div class="px-4 py-2 hidden md:flex items-center  justify-between w-[300px] border border-[#ECECEC] rounded-lg">
                        <input id="user-link" class="text-[#242424]  font-Poppins font-normal text-[14px] leading-[1] tracking-normal flex-1 pr-2" value="<?php echo esc_url( site_url('/dashboard/?user=' . $userinfo->user_login) ); ?>" readonly>
                        <button id="copy-btn" class="font-Poppins bg-[#54B7B4] p-2 rounded-lg text-white font-normal text-[14px] leading-[1] tracking-normal ml-2" type="button">
                            Copy
                        </button>
                    </div>
                    <?php if (is_user_logged_in()) {
                        $profile_url = 'https://tapsocial/' . esc_attr($userinfo->user_login);
                        $qrcodeSmall = "https://api.qrserver.com/v1/create-qr-code/?size=120x120&color=000000&data=" . urlencode($profile_url);
                        $qrcodeModal = "https://api.qrserver.com/v1/create-qr-code/?size=280x280&color=000000&data=" . urlencode($profile_url);
                    ?>
                    <div class="relative">
                        <button type="button" onclick="document.getElementById('profile-qr-modal').classList.remove('hidden')">
                            <img src="<?php echo $qrcodeSmall; ?>" alt="My QR Code" class="h-11 w-11 ">
                        </button>
                    </div>
                    <div id="profile-qr-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center px-4 bg-black bg-opacity-50">
                        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-xs overflow-hidden">
                            <div class="bg-gradient-to-r from-[#54B7B4] to-teal-600 py-4 text-center relative">
                                <h4 class="text-xl font-bold text-white">My Tappie QR Code</h4>
                                <p class="text-white text-sm mt-1 opacity-90">Scan to view my public profile</p>
                                <button type="button" onclick="document.getElementById('profile-qr-modal').classList.add('hidden')" class="absolute top-3 right-4 text-white text-2xl hover:opacity-80">&times;</button>
                            </div>
                            <div class="p-6 bg-gray-50 text-center">
                                <div class="bg-white p-6 rounded-xl shadow-inner inline-block">
                                    <img src="<?php echo $qrcodeModal; ?>" alt="QR Code" class="w-64 h-64 mx-auto">
                                </div>
                            </div>
                            <div class="py-3 bg-gray-100 text-center">
                                <button type="button" onclick="document.getElementById('profile-qr-modal').classList.add('hidden')" class="text-[#54B7B4] font-medium text-sm hover:underline">
                                    Close
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </header>

        <!-- Scrollable Form Section -->
        <main class="flex-1 pt-[5.5rem] lg:pt-10 overflow-y-auto lg:pr-96" id="sep-scroll">
            <div class="px-3 md:p-[1px]">
                <div class=" rounded-[5px]  p-[10px]">
                    <div class="flex items-center space-x-3 mb-4">
                        <i class="fas fa-user text-lg text-teal-600"></i>
                        <h2 class="text-[18px] font-medium text-gray-800">Your bio</h2>
                    </div>
                    <form id="tappie-updateprofilee" method="POST" enctype="multipart/form-data">
                        <?php wp_nonce_field(
                            "tappie_update_profile",
                            "tappie_profile_nonce",
                        ); ?>
                        <input type="hidden" name="tappie-update-profilee" value="update profile">
                        <div class="mb-5 text-left">
                        
                            <div>
                                <input type="file" name="tappie-prifleimg" id="tappie-prifleimg" class="hidden" accept="image/*">
                                <label for="tappie-prifleimg" class="cursor-pointer underline inline-block  text-[#54B7B4]  text-sm font-medium">
                                    Change Photo
                                </label>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                                <div class="relative">
                                    <i class="fas fa-user !text-[#54B7B4] absolute left-4 top-3.5   text-lg" style="color:#54B7B4"></i>
                                    <input type="text" name="ufname" id="input-fname" value="<?php echo esc_attr(
                                        $firstname,
                                    ); ?>" class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-[5px] focus:outline-none focus:ring-2 focus:ring-teal-500" placeholder="First Name">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                                <div class="relative">
                                    <i class="fas fa-user !text-[#54B7B4] absolute left-4 top-3.5  text-lg" style="color:#54B7B4"></i>
                                    <input type="text" name="ulname" id="input-lname" value="<?php echo esc_attr(
                                        $lastname,
                                    ); ?>" class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-[5px] focus:outline-none focus:ring-2 focus:ring-teal-500" placeholder="Last Name">
                                </div>
                            </div>
                        </div>
                        <div class="mb-8">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                            <div class="relative">
                                <i class="fas fa-map-marker-alt !text-[#54B7B4] absolute left-4 top-3.5 text-lg" style="color:#54B7B4"></i>
                                <input type="text" name="ext_location" id="input-location" value="<?php echo esc_attr(
                                    $ext_location,
                                ); ?>" class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-[5px] focus:outline-none focus:ring-2 focus:ring-teal-500" placeholder="City, Country">
                            </div>
                        </div>
 
                        <div class="mb-8">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                            <div class="relative">
                                <i class="fas fa-phone !text-[#54B7B4] absolute left-4 top-3.5 text-lg" style="color:#54B7B4"></i>
                                <input type="text" name="uphone" id="input-phone" value="<?php echo esc_attr(
                                    $userphone,
                                ); ?>" class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-[5px] focus:outline-none focus:ring-2 focus:ring-teal-500" placeholder="Phone Number">
                            </div>
                        </div>
                        <div class="mb-8">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Bio</label>
                            <textarea name="ubio" id="input-bio" rows="5" class="w-full px-4 py-3 border border-gray-300 rounded-[5px] focus:outline-none focus:ring-2 focus:ring-teal-500" placeholder="Add Bio"><?php echo esc_textarea(
                                $userbio,
                            ); ?></textarea>
                        </div>
                        <?php if (!empty($tapcode)): ?>
                            <div class="mb-8 flex justify-between items-center p-6 bg-white rounded-[5px]">
                                <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Current Tappie Passcode</label>
                                <p class="text-lg font-mono text-gray-800"><?php echo esc_html(
                                    $tapcode,
                                ); ?></p>
                        </div>
                                <a href="<?php echo esc_url(
                                    site_url("/add-passcode"),
                                ); ?>" class="text-teal-600 hover:underline text-sm mt-2 inline-block">Change Tappie Passcode →</a>
                            </div>
                        <?php endif; ?>
                      <div class="mb-10">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold flex items-center  text-gray-800">  <img src="<?php echo get_template_directory_uri(); ?>/assets/img/share.png" 
     class="  me-2" 
     style="width:20px; height:20px;" 
     alt="VCF Icon">Social Links</h3>
        <button type="button" id="add-social-btn" class=" items-center px-5 py-2.5 bg-[#54B7B4]  justify-center text-white text-sm font-medium rounded-[5px] transition shadow-sm">
            <i class="fas fa-plus"></i> Add platform
        </button>
    </div>

    <div id="new-social-form" class="hidden bg-white p-5 rounded-[3px] border border-gray-200 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Platform</label>
                <div class="relative">
                    <select id="social-platform-select" class="w-full px-4 py-3 pr-10 border border-gray-300 rounded-[5px] focus:outline-none focus:ring-2 focus:ring-teal-500 appearance-none bg-white">
                        <option value="">Select platform...</option>
                        <?php foreach ($saved_socials as $social): ?>
                            <option value="<?php echo esc_attr(
                                $social["key"],
                            ); ?>" 
                                    data-icon="<?php echo !empty(
                                        $social["icon"]
                                    )
                                        ? esc_url($social["icon"])
                                        : ""; ?>" 
                                    data-postid="<?php echo esc_attr(
                                        $social["id"] ?? "",
                                    ); ?>">
                                <?php echo esc_html(
                                    $social["title"] ?? ucfirst($social["key"]),
                                ); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <!-- Dropdown arrow -->
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-700">
                        <i class="fas fa-chevron-down text-[#54B7B4]" ></i>
                    </div>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Enter URL</label>
               <div class="relative w-full">
                  <img src='<?php echo get_template_directory_uri(); ?>/assets/img/Icon.png'  
                       class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5" 
                       alt="icon">
                  <input type="text" id="social-username" 
                         class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-[5px] focus:outline-none focus:ring-2 focus:ring-teal-500" 
                         placeholder="Enter URL">
                </div>
               </div>
              <div>
                <label class="hidden text-sm font-medium text-gray-700 mb-2">Custom Label (optional)</label>
                <input type="text" id="social-label" class="w-full hidden px-3 py-2.5 border border-gray-300 rounded-[5px] focus:outline-none focus:ring-2 focus:ring-teal-500" placeholder="e.g. My Instagram">
            </div>
        </div>
        <div class="mt-4 flex justify-end gap-3">
            <button type="button" id="cancel-new-social" class="px-5 py-2.5 bg-gray-200 hover:bg-gray-300 rounded-[5px] transition">Cancel</button>
            <button type="button" id="save-new-social" class="px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white rounded-[5px] transition">Add Link</button>
        </div>
    </div>

    <div id="social-links-container" class="space-y-3 sortable-socials">
        <?php foreach ($saved_profiles as $index => $profile):

            $key = $profile["key"] ?? "";
            $username = $profile["url"] ?? "";
            $label = $profile["label"] ?? ($profile["title"] ?? "Link");
            $postid = $profile["id"] ?? "";
            $iconUrl = "";
            if (!empty($profile["icon"])) {
                $iconUrl = esc_url($profile["icon"]);
            } elseif (!empty($postid) && has_post_thumbnail($postid)) {
                $iconUrl = get_the_post_thumbnail_url($postid, "thumbnail");
            }

            // Contact-specific handling
            $isContact = $key == "contacts";
            ?>
            <div class="social-item flex items-center gap-3 p-4 bg-white   rounded-[3px] hover:bg-gray-50 group <?php echo $vcfClass; ?> <?php echo $isContact
              ? "non-editable"
                  : ""; ?>" 
                 data-index="<?php echo $index; ?>" 
                 data-key="<?php echo esc_attr($key); ?>"
                 <?php if ($isContact): ?>data-name="<?php echo esc_attr(
                 $nameDataValue,
                    ); ?>"<?php endif; ?>>

                <!-- Drag Handle: Hidden for contact -->
             
                    <div class="cursor-move text-gray-400 hover:text-gray-700 flex-shrink-0">
                        <i class="fas fa-grip-vertical"></i>
                    </div>
              
                    <!-- <div class="flex-shrink-0 w-6"></div> Spacer for alignment -->
                 

                <!-- Always Visible: View Mode -->
                <div class="flex-1 flex items-center gap-3 view-mode">
                    <div class="w-8 h-8 flex items-center justify-center flex-shrink-0">
                        <?php if (!empty($iconUrl)): ?>
                            <img src="<?php echo $iconUrl; ?>" alt=""  class="w-5 h-5 object-contain filter grayscale" >
                        <?php else: ?>
                            <i class="fas fa-link text-gray-500"></i>
                        <?php endif; ?>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-medium text-[14px] truncate">
                            <?php echo esc_html($label); ?>
                            <?php if ($isContact): ?>
                                <span class="text-xs text-gray-500 ml-2">(VCF)</span>
                            <?php endif; ?>
                        </div>
                        <div class="text-sm text-[#54B7B4] truncate"><?php echo $isContact ? '' : esc_html($username); ?>
</div>
                    </div>
                </div>

                <!-- Edit Mode: COMPLETELY OMITTED for contact -->
              
                    <div class="hidden edit-mode flex-1 flex items-center gap-3">
                        <input type="text" class="edit-label w-32 px-2 py-1 border rounded" value="<?php echo esc_attr(
                            $label,
                        ); ?>">
                        <input type="text" class="edit-url flex-1 px-2 py-1 border rounded" value="<?php echo esc_attr(
                            $username,
                        ); ?>">
                    </div>
              

                <!-- Action Buttons: NONE for contact -->
                <div class="flex gap-2 flex-shrink-0">
                
                        <button type="button" class="edit-social text-blue-600 hover:text-blue-800  transition" title="Edit">
                            <i class="fas fa-edit text-gray-400" ></i>
                        </button>
                        <button type="button" class="delete-social text-red-600 hover:text-red-800   transition" title="Delete">
                            <i class="fas fa-trash-alt text-gray-400"></i>
                        </button>
                        <?php if (!$isContact): ?>
                        <button type="button" class="save-edit hidden text-green-600 hover:text-green-800" title="Save">
                            <i class="fas fa-check"></i>
                        </button>
                        <button type="button" class="cancel-edit hidden text-gray-600 hover:text-gray-800" title="Cancel">
                            <i class="fas fa-times"></i>
                        </button>
                                     <?php endif; ?>
                </div>

                <!-- Hidden inputs remain so data is submitted -->
                <input type="hidden" name="social_links[<?php echo $index; ?>][key]" value="<?php echo esc_attr(
                                               $key,
                                            ); ?>">
                                                           <input type="hidden" name="social_links[<?php echo $index; ?>][url]" value="<?php echo esc_attr(
                                               $username,
                                            ); ?>">
                <input type="hidden" name="social_links[<?php echo $index; ?>][label]" value="<?php echo esc_attr(
                                               $label,
                                            ); ?>">
                <input type="hidden" name="social_links[<?php echo $index; ?>][id]" value="<?php echo esc_attr(
                                              $postid,
                                            ); ?>">
                                                      </div>
                                                    <?php
                                                   
                                                   endforeach; ?>
         </div>
            </div>
            
     <?php 
$uid = get_current_user_id();
$color = get_user_meta($uid, 'tappie_color', true);
$default_color = '#FF8686'; // your default color
$color = !empty($color) ? $color : $default_color;
// ............
$colorone = get_user_meta($uid, 'tappie_color-one', true);
$default_colorone = '#FFF'; // your default color
$colorone = !empty($colorone) ? $colorone : $default_colorone;

?>

<!-- ///////////////// -->
<div class="mb-8 flex items-center">
    <label class="block text-sm font-medium w-[40%] text-gray-700 me-3">
        Public Profile Theme Color:
    </label>

    <div class="relative w-full">
        <!-- Hidden color input for picker (off-screen) -->
        <input type="color" id="hidden-color-picker"
               style="opacity: 0; width: 1px; height: 1px; position: absolute;"
               value="<?php echo esc_attr($color); ?>">

        <input type="text"
               id="color-text-input"
               class="w-full pl-12 pr-3 py-2 border border-gray-300 rounded-[5px] focus:outline-none focus:ring-2 focus:ring-teal-500"
               value="<?php echo esc_attr($color); ?>"
               placeholder="#ffffff">

        <span id="color-circle"
              class="absolute left-2 top-1/2 transform -translate-y-1/2 w-8 h-8 rounded-full border cursor-pointer"
              style="background-color: <?php echo esc_attr($color); ?>;">
        </span>
    </div>

    <!-- Hidden input for form submission -->
    <input type="hidden" id="tappie-color-hidden"
           name="tappie_color"
           value="<?php echo esc_attr($color); ?>">
</div>
<!-- //////////////////// -->
<!-- ///////////////// -->
<div class="mb-8 flex items-center">
    <label class="block text-sm font-medium w-[40%] text-gray-700 me-3">
        Public Profile Text Color:
    </label>

    <div class="relative w-full">
        <!-- Hidden color picker -->
        <input type="color" id="hidden-color-picker-one"
               style="opacity: 0; width: 1px; height: 1px; position: absolute;"
               value="<?php echo esc_attr($colorone); ?>">

        <!-- Text input -->
        <input type="text"
               id="color-text-input-one"
               class="w-full pl-12 pr-3 py-2 border border-gray-300 rounded-[5px] focus:outline-none focus:ring-2 focus:ring-teal-500"
               value="<?php echo esc_attr($colorone); ?>"
               placeholder="#ffffff">

        <!-- Circular swatch -->
        <span id="color-circle-one"
              class="absolute left-2 top-1/2 transform -translate-y-1/2 w-8 h-8 rounded-full border cursor-pointer"
              style="background-color: <?php echo esc_attr($colorone); ?>;">
        </span>
    </div>

    <!-- Hidden input for form submission -->
    <input type="hidden" id="tappie-color-hidden-one"
           name="tappie_color-one"
           value="<?php echo esc_attr($colorone); ?>">
</div>
<!-- //////////////////// -->
                        <button type="submit" class=" items-center px-5 py-2.5 bg-[#54B7B4]  justify-center text-white text-sm font-medium rounded-[5px] transition shadow-sm">Update Profile</button>
                    </form>
                </div>
            </div>
        </main>

        <!-- Fixed Right Preview Panel (Desktop Only) -->
       <div class="hidden lg:block fixed inset-y-0 right-0 top-20 w-96 bg-white shadow-xl border-l border-gray-200 overflow-y-auto z-30">
                <div class="p-6 pt-8">
                    <h3 class="font-poppins w-full mb-5 flex justify-center font-normal text-[18px] leading-[100%] tracking-[0%] text-center">Public page preview</h3>
                    <div class="border border-gray-200 rounded-[5px] bg-gray-50 text-center">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/mobile-header-top.png" alt="Header" class="w-full" />
                        <div class="p-5">
                            <img src="<?php echo esc_url(
                                $userimg,
                            ); ?>?v=<?php echo time(); ?>"
                                               alt="Profile"
                                      class="w-32 h-32 rounded-full object-cover object-top mx-auto border-4 border-white shadow-lg mb-4">
                            <h4 class="font-Syne font-bold text-[22px] leading-[1] tracking-normal text-center truncate px-4">
                                <?php echo esc_html(
                                    wp_get_current_user()->display_name,
                                ); ?>
                            </h4>
                            <?php if ($ext_location): ?>
                                <p class="text-gray-600 mt- text-lg"><?php echo esc_html(
                                    $ext_location,
                                ); ?></p>
                            <?php endif; ?>
                            <?php if ($user_email): ?>
                                <!-- <p class="text-gray-600 mt-1 text-sm"><?php echo esc_html(
                                    $user_email,
                                ); ?></p> -->
                            <?php endif; ?>
                            <?php if (!empty($userphone)): ?>
                        
                            <a id="desktop-preview-phone" href="tel:<?php echo esc_attr(
                                str_replace(
                                    [" ", "-", "(", ")"],
                                    "",
                                    $userphone,
                                ),
                            ); ?>"
                               class="text-gray-600 mt-1 text-sm  flex justify-center">
                                  <!-- <?php echo esc_html($userphone); ?> -->
                            </a>
                        
                        <?php endif; ?>
                              <?php if ($userbio): ?>
                                           <div class="mt-1 text-left">
                                       <h5 class="text-xl font-bold text-gray-800 mb-1 text-left">About</h5>
           
                               <?php
                               $bio = wp_strip_all_tags(trim($userbio));
                               $limit = 120;
                               $needs_read_more = mb_strlen($bio) > $limit;
                               $short_bio = mb_substr($bio, 0, $limit);
                               ?>
           
                              <div class="text-gray-600 text-[14px] text-left   bio-container">
                                 <span class="bio-short inline">
                                     <?php echo esc_html($short_bio); ?>
                                     <?php if ($needs_read_more): ?>
                                         <span class="text-blue-600 font-medium">...</span>
                                     <?php endif; ?>
                                 </span>
               
                                 <?php if ($needs_read_more): ?>
                                       <span class="bio-full hidden">
                                          <?php echo esc_html($bio); ?>
                                       </span>
                   
                                      <button type="button"
                                             class="read-more-btn text-blue-600 font-medium ml-1 hover:underline focus:outline-none cursor-pointer">
                                         Read more
                                       </button>
                                  <?php endif; ?>
                                              </div>
                                              </div>
                                          <?php endif; ?>
                                                                   <?php if (
                                                                       !empty(
                                                                           $userphone
                                                                       )
                                                                   ): ?>
                            
                                                                   <?php endif; ?>
                           
                          
                                             <!-- <div class="mb-4 mt-3">
                                <a href="tel:<?php echo esc_attr(
                                    str_replace(
                                        [" ", "-", "(", ")"],
                                        "",
                                        $userphone,
                                    ),
                                ); ?>"
                                   class="flex justify-center inline-block px-8 py-3 flex items-center bg-[#FF8686] text-white font-medium rounded-[8px] hover:bg-[#54B7B4]">
                                     <?php echo esc_html($userphone); ?>
                                </a>
                            </div> -->
                                           <?php if (
                                               !empty($saved_profiles)
                                           ): ?>
                        <div class="grid grid-cols-2 mt-2 gap-4 max-w-xs mx-auto">
                                    <?php foreach ($saved_profiles as $profile):
// print_r($color);
//                             exit();
                                        $username = $profile["url"] ?? "#";
                                        $label =
                                            $profile["label"] ??
                                            ($profile["title"] ?? "Link");
                                        $postid = $profile["id"] ?? "";
                                        $base_url = $profile["baseurl"] ?? "#";
                                        // $full_url = $base_url . $username;
                                        $iconUrl = "";
                                        // $full_url = $profile["url"]
                                        if (!empty($profile["icon"])) {
                                            $iconUrl = esc_url(
                                                $profile["icon"],
                                            );
                                        } elseif (
                                            !empty($postid) &&
                                            has_post_thumbnail($postid)
                                        ) {
                                            $iconUrl = get_the_post_thumbnail_url(
                                                $postid,
                                                "thumbnail",
                                            );
                                        }
                                        $vcfClass = "";
                                        $nameDataValue = "";
                                        if ($profile["key"] == "contacts") {
                                            $vcfClass = "tapp-contact-vcf";
                                            $nameDataValue =
                                                $userinfo->user_login;
                                        }
                                        // Default icon
                            // print_r($color);
                            // exit();

                            // Special handling for contacts (vCard)
                            if (($profile['key'] ?? '') === 'contacts') {
                                $vcfClass = 'tapp-contact-vcf';
                                $nameDataValue = $userinfo->user_login;
                                $iconUrl = esc_url(get_template_directory_uri() . '/assets/img/vcf.jpg');
                            }
                            // Normal icon from custom field or post thumbnail
                            elseif (!empty($profile['icon'])) {
                                $iconUrl = esc_url($profile['icon']);
                            }
                            elseif (!empty($postid) && has_post_thumbnail($postid)) {
                                $iconUrl = get_the_post_thumbnail_url($postid, 'thumbnail');
                            }
                             $color = get_user_meta($uid, 'tappie_color', true); // fetch from DB
$default_color = '#FF8686';
$style_color = (!empty($color) && preg_match('/^#[0-9A-Fa-f]{6}$/', $color)) ? $color : $default_color;
                                        ?>
                                        
                                        <a href="<?php echo esc_url($full_url); ?>"
                               target="_blank"
                               style="background-color: <?php echo esc_attr($style_color); ?>"
                               class="flex justify-center items-center  px-8 py-3  text-white font-medium rounded-[8px] hover:bg-[#54B7B4] <?php echo esc_attr($vcfClass); ?>"
                               <?php if (!empty($nameDataValue)): ?>data-userurl="<?php echo esc_attr($nameDataValue); ?>"<?php endif; ?>>

                                <?php if (!empty($iconUrl)): ?>
                                    <img src="<?php echo esc_url($iconUrl); ?>"
                                         class="w-5 h-5 object-contain filter grayscale mr-3"
                                         alt="<?php echo esc_attr($label); ?> icon">
                                <?php endif; ?>

                                <span class="text-[14px]"><?php echo esc_html($label); ?></span>
                            </a>
                                    <?php
                                    endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="text-gray-500 text-sm mt-8 text-center">No social links added yet.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

        <!-- Mobile Preview -->
       <div class="lg:hidden p-4 md:p-8">
                 <div class="lg:hidden p-4 md:p-8">
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="font-poppins w-full mb-5 flex justify-center font-normal text-[18px] leading-[100%] tracking-[0%] text-center">Public page preview</h3>
                    <div class="border border-gray-200 rounded-lg bg-gray-50 text-center">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/mobile-header-top.png" alt="Header" class="w-full" />
                        <div class="p-5">
                           <img src="<?php echo esc_url($userimg); ?>?v=<?php echo time(); ?>"
                           id='desktop-preview-img'
                                          alt="Profile"
                                          class="w-32 h-32 rounded-full object-cover object-top mx-auto border-4 border-white shadow-lg mb-4">
                            <h4 class="font-Syne font-bold text-[24px] leading-[1] tracking-normal text-center truncate px-4">
                                <?php echo esc_html(wp_get_current_user()->display_name); ?>
                            </h4>
                            <?php if ($ext_location): ?>
                                <p class="font-syne font-medium text-[18px] text-gray-600 leading-[100%] tracking-[0%] text-center"
                                 ><?php echo esc_html($ext_location); ?></p>
                            <?php endif; ?>
                            <!-- <?php if ($user_email): ?>
                                <p class="text-gray-600 mt-1 text-sm"><?php echo esc_html($user_email); ?></p>
                            <?php endif; ?> -->
                                              <?php if ($userbio): ?>
                                         <div class="mt-10 text-left">
                                             <h5 class="text-xl font-bold text-gray-800 mb-3">About</h5>
           
                                             <?php
                                             $bio = wp_strip_all_tags(trim($userbio));
                                             $limit = 120;
                                             $needs_read_more = mb_strlen($bio) > $limit;
                                             $short_bio = mb_substr($bio, 0, $limit);
                                             ?>
           
                                             <div class="text-gray-600 text-[14px] mt-2 bio-container">
                                                 <span class="bio-short inline">
                                                     <?php echo esc_html($short_bio); ?>
                                                     <?php if ($needs_read_more): ?>
                                                         <span class="text-blue-600 font-medium">...</span>
                                                     <?php endif; ?>
                                                 </span>
               
                                                 <?php if ($needs_read_more): ?>
                                                     <span class="bio-full hidden">
                                                         <?php echo esc_html($bio); ?>
                                                     </span>
                   
                                                     <button type="button"
                                                             class="read-more-btn text-blue-600 font-medium ml-1 hover:underline focus:outline-none cursor-pointer">
                                                         Read more
                                                     </button>
                                                 <?php endif; ?>
                                             </div>
                                         </div>
                                     <?php endif; ?>
                           
                            <?php if (!empty($saved_profiles)): ?>
                                <div class="grid grid-cols-2 gap-4 mt-2 max-w-xs mx-auto">
                                    <?php foreach ($saved_profiles as $profile):
                                        $username = $profile['url'] ?? '';
                                        $label = $profile['label'] ?? ($profile['title'] ?? 'Link');
                                        $postid = $profile['id'] ?? '';
                                        $base_url = $profile['baseurl'] ?? '#';
                                        $full_url = $base_url . $username;
                                        $iconUrl = '';
                                        if (!empty($profile['icon'])) {
                                            $iconUrl = esc_url($profile['icon']);
                                        } elseif (!empty($postid) && has_post_thumbnail($postid)) {
                                            $iconUrl = get_the_post_thumbnail_url($postid, 'thumbnail');
                                        }

                                         if (($profile['key'] ?? '') === 'contacts') {
                                $vcfClass = 'tapp-contact-vcf';
                                $nameDataValue = $userinfo->user_login;
                                $iconUrl = esc_url(get_template_directory_uri() . '/assets/img/vcf.jpg');
                            }
                            // Normal icon from custom field or post thumbnail
                            elseif (!empty($profile['icon'])) {
                                $iconUrl = esc_url($profile['icon']);
                            }
                            elseif (!empty($postid) && has_post_thumbnail($postid)) {
                                $iconUrl = get_the_post_thumbnail_url($postid, 'thumbnail');
                            }
                                    ?>
                                       <a href="<?php echo esc_url($username); ?>"
                               target="_blank"
                               class="flex justify-center items-center  px-8 py-3 bg-[#FF8686] text-white font-medium rounded-[8px] hover:bg-[#54B7B4] <?php echo esc_attr($vcfClass); ?>"
                               <?php if (!empty($nameDataValue)): ?>data-userurl="<?php echo esc_attr($nameDataValue); ?>"<?php endif; ?>>

                                <?php if (!empty($iconUrl)): ?>
                                    <img src="<?php echo esc_url($iconUrl); ?>"
                                         class="w-5 h-5 object-contain filter grayscale mr-3"
                                         alt="<?php echo esc_attr($label); ?> icon">
                                <?php endif; ?>

                                <span class="text-[14px]"><?php echo esc_html($label); ?></span>
                            </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="text-gray-500 text-sm mt-8 text-center">No social links added yet.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            </div>
    </div>
</section>

<!-- Overlay for mobile sidebar -->
<div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden md:hidden"></div>

<!-- <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script> -->
 
<?php get_footer(); ?>


