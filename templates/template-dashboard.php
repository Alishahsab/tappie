<?php
/*
    Template Name: User Dashboard
*/

get_header();
?>

<?php
 /* ============================
   Auth & Redirects
============================ */
if (!is_user_logged_in() && !isset($_GET['user'])) {
    wp_redirect(site_url());
    exit;
}

if (current_user_can('administrator')) {
    wp_safe_redirect(site_url() . '/admin-dashboard/');
    exit;
}

/* ============================
   User Data
============================ */
$userID = get_current_user_id();

if (empty($userID) && isset($_GET['user'])) {
    $user = get_user_by('login', sanitize_text_field($_GET['user']));
    if ($user) {
        $userID = $user->ID;
    }
}

$userinfo = get_user_by('id', $userID);
$firstname     = $userinfo->first_name ?? '';
$lastname      = $userinfo->last_name ?? '';
$display_name  = trim($firstname . ' ' . $lastname) ?: $userinfo->display_name;
$userbio       = $userinfo->description ?? '';
$ext_location  = get_user_meta($userID, 'ext_location', true);
$user_email    = $userinfo->user_email ?? '';
$user_phone    = get_user_meta($userID, 'ext_phone', true);
$user_profiles = get_user_meta($userID, 'saved_profiles', true);
$username_link1 = site_url().'/dashboard/?user=' . $userinfo->user_login;
$color = get_user_meta($userID, 'tappie_color', true);
//  print_r($color);


// exit();
if (!empty($user_profiles)) {
    ksort($user_profiles);
}

/* Profile Image */
$userimg = get_user_meta($userID, 'author_pic', true);
if (empty($userimg)) {
    $userimg = get_avatar_url($userID);
}
?>

<?php if (is_user_logged_in() && $userID === get_current_user_id()) : ?>
    <?php
    // ==================== CODE A - Full Logged-in Dashboard ====================
    $uid = get_current_user_id();
    $userinfo = get_user_by('id', $uid);
    $firstname = $userinfo->first_name;
    $lastname = $userinfo->last_name;
    $userbio = $userinfo->description;
    $userphone = get_user_meta($uid, 'ext_phone', true);
    $ext_location = get_user_meta($uid, 'ext_location', true);
    $tapcode = get_user_meta($uid, 'passcodes', true);
    $saved_socials = tappie_get_available_profiles();
    // Profile image
    $userimg = get_user_meta($uid, 'author_pic', true);
    if (empty($userimg)) {
        $userimg = get_template_directory_uri() . '/assets/img/your-uploaded-image.png';
    }
    $upload_message = '';
    $update_message = '';
    $saved_profiles = get_user_meta($uid, 'saved_profiles', true) ?: [];
    $full_name = trim($firstname . ' ' . $lastname);
    $username_link = site_url().'/dashboard/?user=' . $userinfo->user_login;
    $user_email = $userinfo->user_email ?? '';
    ?>
  

    <section class="bg-white min-h-screen flex relative">
        <!-- Fixed Left Sidebar -->
        <aside id="sidebar"
               class="w-64 bg-white border-r border-gray-200 fixed inset-y-0 left-0 z-50 overflow-y-auto transform -translate-x-full md:translate-x-0 transition-transform duration-300">
            <div class="relative p-6">
                <!-- Cross Icon inside Sidebar (visible only on mobile when sidebar open) -->
                <button id="sidebar-close-inside" class="absolute top-4 right-4 md:hidden text-gray-600 hover:text-gray-900 text-2xl">
                    <i class="fas fa-times"></i>
                </button>
                <div class="flex justify-center mb-8">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/img/logo.png" alt="Logo" class="h-12 rounded-full">
                </div>
                <div class="space-y-6 text-center">
                    <div>
                       <img id='desktop-preview-img' src="<?php echo esc_url($userimg); ?>?v=<?php echo time(); ?>"
         alt="Profile"
         class="w-32 h-32 rounded-full object-cover object-top mx-auto border-4 border-white shadow-lg mb-4">
                        <p class="mt-4 text-lg font-medium text-gray-800 truncate px-4" title="<?php echo esc_attr($full_name); ?>">
                            <?php echo esc_html(wp_get_current_user()->display_name); ?>
                        </p>
                    </div>
                    <nav class="space-y-1">
                        <!-- <a href="<?php echo esc_url( site_url('/dashboard') ); ?>" class="flex items-center lg:block hidden px-4 py-3 bg-gray-100 text-teal-600 rounded-lg font-medium" >
                            <i class="fas fa-chart-line mr-3"></i>
                            Dashbord
                        </a>
                        <a href="http://localhost/tapsocial/settings/" class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-100 rounded-lg transition">
                            <i class="fas fa-user mr-3"></i>
                            My Profile
                        </a> -->
                        <nav class="space-y-1">
                    <a href="<?= esc_url( site_url('/dashboard') ) ?>" class="flex text-[14px] items-center px-4 py-3  text-teal-600 bg-gray-100 hover:bg-gray-100 rounded-lg transition">
                        <i class="fas fa-chart-line mr-3"></i> Dashbord
                    </a>
                    <a href="http://localhost/tapsocial/settings/" class="flex text-[14px] items-center px-4 py-3 text-gray-600     rounded-lg font-medium">
                        <i class="fas fa-user mr-3"></i> Edit Profile
                    </a>
                </nav>
                    </nav>
                     <div class="mt-6">
                            <a href="<?php echo wp_logout_url(site_url()); ?>"
                               class="inline-flex items-center px-5 py-2.5 w-full justify-center bg-[#54B7B4] hover:bg-red-700 text-white text-sm font-medium rounded-lg transition shadow-sm">
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
                <h2 class="text-xl font-medium hidden lg:block text-gray-800">Dashboard</h2>
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
            <!-- Scrollable Middle Section (Form & Details) -->
            <main class="flex-1 pt-5 overflow-y-auto lg:pr-96" id="sep-scroll">
                <div class="">
                    <div class="grid grid-cols-1 lg:grid-cols-1 pt-[60px]   gap-8">
                        <!-- Left: Form & Details Section -->
                        <div class="lg:col-span-2 space-y-8">
                            <div class="lg:col-span-2 space-y-6 border border-[#ECECEC] px-3 py-3 !bg-[#FAFAFB]">
                                <!-- Your Bio Details Block -->
                                <div class="bg-white   flex justify-between md:p-6 p-4 space-y-6 rounded-[5px] mb-8">
                                    <div class="w-full  ">
                                        <span class="flex items-center mb-4">
                                             <i class="fas fa-user text-lg text-teal-600"></i>
                                            <span class="font-poppins ms-4 text-[#AAAAAA] font-normal text-[16px] leading-[100%] tracking-[0%]">
                                                Your bio
                                            </span>
                                        </span>
                                        <p class="font-poppins font-normal text-[15px] mt-4 leading-[100%] tracking-[0]">
                                            <?php echo esc_html($full_name ?: wp_get_current_user()->display_name); ?>
                                            <!-- <?php echo esc_html($tapcode); ?> -->
                                        </p>
                                         
                                        <div class="md:flex block md:gap-4  gap-y-5 mt-3">
                                            <?php if ($ext_location): ?>
                                                <div class="flex items-center mt-2 space-x-3">
                                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/img/Location.svg" alt="Location" class="w-[20px]" />
                                                    <p class="font-poppins font-normal !mt-0 text-[12px] leading-[100%] tracking-[0] text-center">
                                                        <?php echo esc_html($ext_location); ?>
                                                    </p>
                                                </div>
                                            <?php endif; ?>
                                            <?php if ($userphone): ?>
                                                <div class="flex items-center mt-2 space-x-3">
                                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/img/Frame.svg" alt="Phone" class="w-[20px]" />
                                                    <p class="font-poppins font-normal !mt-0 text-[12px] leading-[100%] tracking-[0] text-center">
                                                        <?php echo esc_html($userphone); ?>
                                                    </p>
                                                </div>
                                            <?php endif; ?>
                                            <div class="flex items-center mt-2 space-x-3">
                                                <img src="<?php echo get_template_directory_uri(); ?>/assets/img/Group.svg" alt="Link" class="w-[20px]" />
                                                <p class="font-poppins font-normal  !mt-0 text-[14px] leading-[100%] tracking-[0] text-center truncate max-w-[200px]">
                                                    <?php echo esc_html($username_link); ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                  <!-- Your Bio Details Block -->
                                <div class="bg-white  flex justify-between p-6 space-y-6   rounded-[5px] mb-8">
                                    <div class="w-full ">
                                         
                                        
                                        <p class="font-poppins font-normal text-[15px] flex justify-between  leading-[100%] tracking-[0]">
                                           <span>
 Tappie Passcode:
                                            </span>
                                           <span style="color:#54B7B4"> <?php echo esc_html($tapcode); ?></span>
                                        </p>
                                         
                                    </div>
                                </div>
                                <!-- Social Links List (Read-only) -->
                                <div class="space-y-3">
                                    <h3 class="text-lg font-semibold flex items-center  text-gray-800">  <img src="<?php echo get_template_directory_uri(); ?>/assets/img/share.png" 
     class="  me-2" 
     style="width:20px; height:20px;" 
     alt="VCF Icon">Social Links</h3>
                                    <div id="social-links-container" class="space-y-3">
                                        <?php foreach ($saved_profiles as $index => $profile):
                                            $key = $profile['key'] ?? '';
                                            $username = $profile['url'] ?? '';
                                            $label = $profile['label'] ?? ($profile['title'] ?? 'Link');
                                            $postid = $profile['id'] ?? '';
                                            $iconUrl = '';
                                            if (!empty($profile['icon'])) {
                                                $iconUrl = esc_url($profile['icon']);
                                            } elseif (!empty($postid) && has_post_thumbnail($postid)) {
                                                $iconUrl = get_the_post_thumbnail_url($postid, 'thumbnail');
                                            }

                                             if($profile['key'] ==  'contacts') { 
                                              $vcfClass='tapp-contact-vcf cursor-pointer';
                                             $nameDataValue=  $userinfo->user_login;
                                        }
                                        ?>
                                            <div class="gap-3 p-4 bg-white  rounded-[5px]   <?php echo $vcfClass; ?>" data-userurl="<?php echo $nameDataValue; ?>">
                                                <span class="flex items-center">
                                                   <?php if (!empty($iconUrl) || !empty($key)): ?>
    <?php if ($key === 'contacts'): ?>
        <!-- Show VCF card icon in gray -->
        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/vcf.jpg" 
     class="filter grayscale" 
     style="width:30px; height:30px;" 
     alt="VCF Icon">

    <?php else: ?>
        <!-- Show original image -->
        <img src="<?php echo $iconUrl; ?>" 
             class="w-5 h-5 object-contain filter grayscale" 
             alt="">
    <?php endif; ?>
<?php endif; ?>

                                                    <span class="font-poppins ms-4 font-normal text-[16px] text-[#AAAAAA] leading-[100%] tracking-[0%] text-center">
                                                      <?php echo ($key == 'contacts') ? 'VCF Card' : 'Social link'; ?>
                                                    </span>
                                                </span>
                                                <div class="flex">
                                                    <div class="flex-1 min-w-0">
                                                        <div class="font-poppins font-normal text-[16px] mt-4 leading-[100%] tracking-[0]">
                                                            <?php echo esc_html($label); ?>
                                                        </div>
                                                        <?php if (($profile['key'] ?? '') !== 'contacts') : ?>
    <div class="font-poppins font-normal text-[12px] leading-[100%] tracking-[0] text-center pt-5 flex items-center truncate">
        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/Icon.png"
             class="h-[15px] w-[15px] me-2"
             alt="">
        <?php echo esc_html($username); ?>
    </div>
<?php endif; ?>

                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
          <!-- Fixed Right Preview Panel (Desktop Only) -->
<div class="hidden lg:block fixed inset-y-0 right-0 top-20 w-96 bg-white shadow-xl border-l border-gray-200 overflow-y-auto z-30">
    <div class="p-6 pt-8">
        <h3 class="font-poppins w-full mb-5 flex justify-center font-normal text-[18px] leading-[100%] tracking-[0%] text-center">
            Public page preview
        </h3>

        <div class="border border-gray-200 rounded-lg bg-gray-50 text-center">
            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/mobile-header-top.png" alt="Header" class="w-full" />

            <div class="p-5">
                <img src="<?php echo esc_url($userimg); ?>?v=<?php echo time(); ?>"
                     alt="Profile"
                     class="w-32 h-32 rounded-full object-cover object-top mx-auto border-4 border-white shadow-lg mb-4">

                <h4 class="font-Syne font-bold text-[22px] leading-[1] tracking-normal text-center truncate px-4">
                    <?php echo esc_html(wp_get_current_user()->display_name); ?>
                </h4>

                <?php if (!empty($ext_location)): ?>
                    <p class="font-syne font-medium text-gray-600 text-[18px] leading-[100%] tracking-[0%] text-center">
                        <?php echo esc_html($ext_location); ?>
                    </p>
                <?php endif; ?>

                <?php if (!empty($userbio)): ?>
                    <div class="mt-4 text-left">
                        <h5 class="text-xl font-bold text-gray-800 mb-2 text-left">About</h5>

                        <?php
                        $bio = wp_strip_all_tags(trim($userbio));
                        $limit = 120;
                        $needs_read_more = mb_strlen($bio) > $limit;
                        $short_bio = mb_substr($bio, 0, $limit);
                        ?>

                        <div class="text-gray-600 text-[14px] text-left bio-container">
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

                <!-- Social Profiles / Links Section -->
                <?php if (!empty($saved_profiles)): ?>
                    <div class="grid grid-cols-2 gap-4 max-w-xs mx-auto mt-6">
                        <?php foreach ($saved_profiles as $profile):
                            $username     = $profile['url'] ?? '';
                            $label        = $profile['label'] ?? ($profile['title'] ?? 'Link');
                            $postid       = $profile['id'] ?? '';
                            $base_url     = $profile['baseurl'] ?? '#';
                            $full_url     = $base_url . $username;

                            $vcfClass     = '';
                            $nameDataValue = '';

                            // Default icon
                            $iconUrl = '';

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
                            
   $color = get_user_meta($uid, 'tappie_bg_color', true); // fetch from DB
                                 $colortext = get_user_meta($uid, 'tappie_text_color', true); // fetch from DB
                                $default_color = '#FF8686';
                                $style_color = (!empty($color) && preg_match('/^#[0-9A-Fa-f]{6}$/', $color)) ? $color : $default_color;


                            $default_colortext = '#FFFFFF';
                            $style_colortext = (!empty($colortext) && preg_match('/^#[0-9A-Fa-f]{6}$/', $colortext)) ? $colortext : $default_color;


                        ?>
                            <a href="<?php echo esc_url($username); ?>"
                               target="_blank"
                                 style="background-color: <?php echo esc_attr($style_color); ?>"
                               class="flex justify-center items-center  px-8 py-3  text-white font-medium rounded-[8px] hover:bg-[#54B7B4] <?php echo esc_attr($vcfClass); ?>"
                               <?php if (!empty($nameDataValue)): ?>data-userurl="<?php echo esc_attr($nameDataValue); ?>"<?php endif; ?>>

                                <?php if (!empty($iconUrl)): ?>
                                    <img src="<?php echo esc_url($iconUrl); ?>"
                                         class="w-5 h-5 object-contain filter grayscale mr-3"
                                         alt="<?php echo esc_attr($label); ?> icon">
                                <?php endif; ?>

                                <span  style="color: <?php echo esc_attr($style_colortext); ?>" class="text-[14px]"><?php echo esc_html($label); ?></span>
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
            <!-- Mobile Preview (Below content on small screens) -->
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


   $color = get_user_meta($uid, 'tappie_bg_color', true); // fetch from DB
                                 $colortext = get_user_meta($uid, 'tappie_text_color', true); // fetch from DB
                                $default_color = '#FF8686';
                                $style_color = (!empty($color) && preg_match('/^#[0-9A-Fa-f]{6}$/', $color)) ? $color : $default_color;


                            $default_colortext = '#FFFFFF';
                            $style_colortext = (!empty($colortext) && preg_match('/^#[0-9A-Fa-f]{6}$/', $colortext)) ? $colortext : $default_color;


                                    ?>
                                       <a href="<?php echo esc_url($username); ?>"
                               target="_blank"
                                style="background-color: <?php echo esc_attr($style_color); ?>"
                               class="flex justify-center items-center  px-8 py-3   text-white font-medium rounded-[8px] hover:bg-[#54B7B4] <?php echo esc_attr($vcfClass); ?>"
                               <?php if (!empty($nameDataValue)): ?>data-userurl="<?php echo esc_attr($nameDataValue); ?>"<?php endif; ?>>

                                <?php if (!empty($iconUrl)): ?>
                                    <img src="<?php echo esc_url($iconUrl); ?>"
                                         class="w-5 h-5 object-contain filter grayscale mr-3"
                                         alt="<?php echo esc_attr($label); ?> icon">
                                <?php endif; ?>

                                <span  style="color: <?php echo esc_attr($style_colortext); ?>" class="text-[14px]"><?php echo esc_html($label); ?></span>
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
    </section>
    <!-- Mobile Sidebar Overlay -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden md:hidden"></div>
    <script>
    </script>

<?php else: ?>
    <!-- ==================== CODE B - Simple Public Preview (when not logged in or viewing someone else's profile) ==================== -->
    <!-- Fixed Right Preview Panel (Desktop Only) -->
    <div class="flex justify-center w-full items-center  t    border-gray-200   z-30">
        <div class="p-6 pt-8 w-96">
            <!-- <h3 class="font-poppins mb-5 text-center text-[18px] font-normal leading-tight">
                Public page preview
            </h3> -->

            <div class="border border-gray-200 rounded-lg bg-gray-50 text-center overflow-hidden">
                <!-- Mobile Header Image -->
                 <div class="flex bg-white shadow-sm justify-between p-[10px]">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/img/logo.png" 
                     alt="Header" 
                     class="w-[50px] h-[32px] " />


                     <?php
// Set username for profile URL
$username = is_user_logged_in() ? $userinfo->user_login : $username_link1;

// Profile URLs
$profile_url  = 'https://tapsocial/' . esc_attr($username);
$qrcodeSmall  = "https://api.qrserver.com/v1/create-qr-code/?size=120x120&color=000000&data=" . urlencode($profile_url);
$qrcodeModal  = "https://api.qrserver.com/v1/create-qr-code/?size=280x280&color=000000&data=" . urlencode($profile_url);
?>

<div class="relative">
    <button type="button" onclick="document.getElementById('profile-qr-modal').classList.remove('hidden')">
        <img src="<?php echo $qrcodeSmall; ?>" alt="My QR Code" class="w-[32px] h-[32px]">
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

                 </div>

                <div class="p-5 pt-10"> <!-- Extra top padding to make room for profile pic -->
                    <!-- Profile Image -->
                    <div class="relative  flex  ">
                        <img src="<?php echo esc_url($userimg); ?>?v=<?php echo time(); ?>"
                             alt="Profile "
                             class="w-32 h-32 rounded-full object-cover object-top mx-auto border-4 border-white shadow-lg">
                    </div>

                    <!-- Name -->
                    <h4 class="font-bold text-[22px] leading-tight mt-4 px-4 truncate">
                        <?php echo esc_html($display_name); ?>
                    </h4>

                    <!-- Location -->
                    <?php if (!empty($ext_location)) : ?>
                        <p class="font-syne font-medium text-[18px] leading-[100%] text-gray-600 tracking-[0%] text-center"
>
                            <?php echo esc_html($ext_location); ?>
                        </p>
                    <?php endif; ?>

                    <!-- Email -->
                    <!-- <?php if (!empty($user_email)) : ?>
                        <p class="text-gray-600 mt-1 text-sm">
                            <?php echo esc_html($user_email); ?>
                        </p>
                    <?php endif; ?>
<?php if (!empty($user_phone)) : ?>
                    
                            <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $user_phone)); ?>"
                               class="text-gray-600 mt-1 text-sm flex justify-center">
                                
                                <?php echo esc_html($user_phone); ?>
                            </a>
                     
                    <?php endif; ?> -->



                   
                    <!-- Bio with Read More -->
   <!-- Bio with Read More / Read Less -->
<?php if ( ! empty( $userbio ) ) : ?>
<?php
    $bio   = wp_strip_all_tags( trim( $userbio ) );
    $limit = 120;

    $needs_read_more = mb_strlen( $bio ) > $limit;
    $short_bio       = mb_substr( $bio, 0, $limit );

    $toggle_id = 'bio_toggle_' . get_current_user_id();
?>

<div class="mt-3">
    <h5 class="text-xl font-bold text-gray-800 mb-2 text-left">About</h5>

    <div class="tappie-bio-wrapper text-gray-600 text-[14px] text-left ">

        <?php if ( $needs_read_more ) : ?>
            <input
                type="checkbox"
                id="<?php echo esc_attr( $toggle_id ); ?>"
                class="tappie-bio-toggle"
            >
        <?php endif; ?>

        <!-- Short Bio -->
        <span class="tappie-bio-short">
            <?php echo esc_html( $short_bio ); ?>...
        </span>

        <?php if ( $needs_read_more ) : ?>
            <!-- Full Bio -->
            <span class="tappie-bio-full">
                <?php echo esc_html( $bio ); ?>
            </span>

            <!-- Controls -->
            <label for="<?php echo esc_attr( $toggle_id ); ?>"
                class="tappie-bio-more text-blue-600 font-medium ml-1 cursor-pointer hover:underline">
                Read more
            </label>

            <label for="<?php echo esc_attr( $toggle_id ); ?>"
                class="tappie-bio-less text-blue-600 font-medium ml-1 cursor-pointer hover:underline">
                Read less
            </label>
        <?php endif; ?>

    </div>
</div>
<?php endif; ?>



                    <!-- Phone Button -->
                     

                    <!-- Social Links -->
                  <?php if (!empty($user_profiles)) : ?>
    <div class="grid grid-cols-2 gap-4 max-w-xs mx-auto mt-2">
        <?php foreach ($user_profiles as $profile) :

            $username      = $profile['url'] ?? '';
            $base_url      = $profile['baseurl'] ?? '#';
            $full_url      = $base_url . $username;
            $label         = $profile['label'] ?? ($profile['title'] ?? 'Link');
            $postid        = $profile['id'] ?? '';
            $iconUrl       = '';
            $vcfClass      = ''; // reset for each iteration
            $nameDataValue = ''; // reset for each iteration
 
            if (!empty($profile['icon'])) {
                $iconUrl = esc_url($profile['icon']);
            } elseif (!empty($postid) && has_post_thumbnail($postid)) {
                $iconUrl = get_the_post_thumbnail_url($postid, 'thumbnail');
            }

            if (($profile['key'] ?? '') === 'contacts') {
                $vcfClass      = 'tapp-contact-vcf';
                $nameDataValue = $userinfo->user_login;
            }
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

        $color = get_user_meta($userID, 'tappie_bg_color', true); // fetch from DB
                                 $colortext = get_user_meta($userID, 'tappie_text_color', true); // fetch from DB
                                $default_color = '#FF8686';
                                $style_color = (!empty($color) && preg_match('/^#[0-9A-Fa-f]{6}$/', $color)) ? $color : $default_color;


                            $default_colortext = '#FFFFFF';
                            $style_colortext = (!empty($colortext) && preg_match('/^#[0-9A-Fa-f]{6}$/', $colortext)) ? $colortext : $default_color;


        ?>

            <a href="<?php echo esc_url($username); ?>" 
               target="_blank"
                 style="background-color: <?php echo esc_attr($style_color); ?>"
               class="flex justify-center inline-block px-8 py-3 flex items-center   text-white font-medium rounded-[8px] hover:bg-[#54B7B4] <?php echo esc_attr($vcfClass); ?>" 
               data-userurl="<?php echo esc_attr($nameDataValue); ?>">
                <?php if (!empty($iconUrl)) : ?>
                    <img src="<?php echo esc_url($iconUrl); ?>" class="w-5 h-5 object-contain filter grayscale me-2" alt="">
                <?php else : ?>
                    <i class="fas fa-link me-2"></i>
                <?php endif; ?>
                <span  style="color: <?php echo esc_attr($style_colortext); ?>" class="text-sm font-medium"><?php echo esc_html($label); ?></span>
            </a>
        <?php endforeach; ?>
    </div>
<?php else : ?>
    <p class="text-gray-500 text-sm mt-8 text-center">No social links added yet.</p>
<?php endif; ?>

                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php get_footer(); ?>