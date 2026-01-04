<?php
ob_start();

function tappie_register_login_shortcode() { 
    $htmlForms = null; 

    // Fixed: Redirect already logged-in users to /admin-dashboard (not /dashboard)
    // Also skip redirect during form submission to allow custom login/signup redirects
    if (is_user_logged_in() && !isset($_POST['signup-submit']) && !isset($_POST['un'])) {
        wp_redirect(site_url() . '/dashboard');
        exit;
    }

    // === Handle Signup Form Submission ===
    if (isset($_POST['signup-submit']) && $_POST['signup-submit'] === 'Sign Up') {
        $username = sanitize_user($_POST['un'] ?? ''); // custom link slug (username)
        $name     = sanitize_text_field($_POST['name'] ?? ''); // full name
        $email    = sanitize_email($_POST['uemail'] ?? '');
        $password = $_POST['up'] ?? '';
        $confirm  = $_POST['confirmPassword'] ?? '';

        $errors = [];

        // Basic validation
        if (empty($username) || empty($name) || empty($email) || empty($password) || empty($confirm)) {
            $errors[] = 'All fields are required.';
        }
        if ($password !== $confirm) {
            $errors[] = 'Passwords do not match.';
        }
        if (!is_email($email)) {
            $errors[] = 'Invalid email format.';
        }
        if (username_exists($username)) {
            $errors[] = 'Username (link) already taken.';
        }
        if (email_exists($email)) {
            $errors[] = 'Email already registered.';
        }

        if (empty($errors)) {
            // Create user
            $user_id = wp_create_user($username, $password, $email);

            if (!is_wp_error($user_id)) {
                // Update core fields in wp_users table
                wp_update_user([
                    'ID'          => $user_id,
                    'first_name'  => $name,           // Full name → first_name
                    'last_name'   => '',              // Last name empty at signup
                    'description' => '',              // Bio empty at signup
                ]);

                // Update custom meta
                update_user_meta($user_id, 'ext_phone', '');
                update_user_meta($user_id, 'ext_location', '');

                // Auto-login the new user
                $creds = [
                    'user_login'    => $username,
                    'user_password' => $password,
                    'remember'      => true
                ];
                $user = wp_signon($creds, false);

                if (!is_wp_error($user)) {
                    wp_redirect(site_url() . '/admin-dashboard');
                    exit;
                } else {
                    $errors[] = 'Signup successful, but auto-login failed. Please log in manually.';
                }
            } else {
                $errors[] = $user_id->get_error_message();
            }
        }

        // Show errors
        if (!empty($errors)) {
            echo '<div class="bg-red-100 border border-red-400 text-red-700 px-6 py-4 rounded-lg mb-6">';
            echo '<ul class="list-disc pl-5">';
            foreach ($errors as $error) {
                echo '<li>' . esc_html($error) . '</li>';
            }
            echo '</ul></div>';
        }
    }

    // === Handle Login Form Submission (this was missing before) ===
    if (isset($_POST['un']) && isset($_POST['up']) && !isset($_POST['signup-submit'])) {
        $username = sanitize_user($_POST['un']);
        $password = $_POST['up'];

        $creds = [
            'user_login'    => $username,
            'user_password' => $password,
            'remember'      => true
        ];

        $user = wp_signon($creds, false);

        if (is_wp_error($user)) {
            echo '<div class="bg-red-100 border border-red-400 text-red-700 px-6 py-4 rounded-lg mb-6">';
            echo '<ul class="list-disc pl-5">';
            echo '<li>' . esc_html($user->get_error_message()) . '</li>';
            echo '</ul></div>';
        } else {
            wp_redirect(site_url() . '/dashboard');
            exit;
        }
    }

    // === Your existing HTML design (unchanged) ===
?>
<div 
 style="background-image: url('<?php echo get_template_directory_uri(); ?>/assets/img/main-bg.png');"
class="w-full min-h-screen relative flex items-center justify-center bg-center bg-cover !bg-[#F3FBFA]">
    <!-- Decorative images -->
    <img src="<?php echo get_template_directory_uri(); ?>/assets/img/loudspeker.png" class="absolute bottom-0 lg:w-[246.22px] left-10 w-12 sm:w-[15%]" alt="loudspeaker">
    <img src="<?php echo get_template_directory_uri(); ?>/assets/img/Frame 2661.png" class="absolute bottom-0 lg:w-[142.35px] left-1/2 -translate-x-1/2 w-24 sm:w-[15%]" alt="frame">
    <img src="<?php echo get_template_directory_uri(); ?>/assets/img/Like.png" class="absolute w-16 sm:w-[15%] lg:w-[200px] bottom-0 right-10" alt="like">

    <div 
  class="w-full flex justify-center items-center bg-center bg-cover"
   
>

        <div class="py-20 flex justify-center w-full px-[15px] lg:px-4">
            <div class="w-full max-w-[500px]">
                <!-- Logo -->
                <div class="mb-6 flex justify-center items-center w-full absolute top-5 left-0">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/img/logo.png" alt="Tappie Logo" class="w-32 sm:w-40" />
                                        <!-- <img src="<?php echo get_template_directory_uri(); ?>/assets/img/main-bg.png" alt="Tappie Logo" class="w-32 sm:w-40" /> -->

                </div>

                <!-- Card -->
                <div class="bg-white w-full lg:w-[500px] rounded-xl shadow-md p-6 sm:p-8 mt-20">

                    <?php
                    $displayclass = null;
                    $displayChangePw = 'hidden';
                    if (isset($_GET['tappie_reset_key']) && isset($_GET['login'])) {
                        if (!empty($_GET['tappie_reset_key']) && !empty($_GET['login'])) {
                            $un = $_GET['login'];
                            $key = $_GET['tappie_reset_key'];
                            $userOBJ = get_user_by('login', $un);
                            $uid = null;
                            if (!is_wp_error($userOBJ) && !empty($userOBJ)) {
                                $uid = $userOBJ->ID;
                                $userKey = get_user_meta($uid, 'tappie_reset_key', true);
                                if ($userKey == $key) {
                                    $displayclass = 'hidden';
                                    $displayChangePw = '';
                                }
                            }
                            ?>
                            <!-- Password Reset Form -->
                            <div id="tappie-setchangepassword" class="tappie-setchangepassword <?php echo $displayChangePw; ?>">
                                <h2 class="font-semibold text-[24px] sm:text-[28px] text-center text-secondary mb-4">Reset Your Password</h2>
                                <p class="text-[14px] sm:text-[16px] text-center text-muted mb-6">Enter your new password below</p>

                                <form method="POST" class="space-y-4">
                                    <div>
                                        <label class="block text-[12px] sm:text-[14px] text-secondary mb-2">New Password</label>
                                        <div class="relative">
                                            <span class="absolute left-3 top-1/2 -translate-y-1/2">
                                                <img src="<?php echo get_template_directory_uri(); ?>/assets/img/key-icon.png" class="w-[12px] h-[12px] sm:w-[16px] sm:h-[16px]" alt="key">
                                            </span>
                                            <input type="password" name="user_new_password" placeholder="Enter new password" class="w-full pl-9 pr-3 py-3 border border-borderGray rounded-md focus:outline-none focus:ring-2 focus:ring-primary text-[12px] sm:text-[14px]">
                                        </div>
                                    </div>

                                    <input type="hidden" name="user_id" value="<?php echo esc_attr($uid); ?>">

                                    <button type="submit" name="wp-submit" class="w-full bg-primary hover:bg-teal-600 text-white py-4 rounded-md font-semibold text-[14px] sm:text-[16px]">
                                        Set New Password
                                    </button>

                                    <p class="tap-ajax-rsp text-center text-sm"></p>
                                </form>
                            </div>
                            <?php
                        }
                    }
                    ?>

                    <!-- Forgot Password Link Form -->
                    <div id="tappie-changepassword-link" class="tappie-changepassword-link">
                        <h2 class="font-semibold text-[24px] sm:text-[28px] text-center text-secondary mb-4">Forgot Password?</h2>
                        <p class="text-[14px] sm:text-[16px] text-center text-muted mb-6">Don’t worry, we got you! Enter your e-mail please</p>

                        <form method="POST" class="space-y-4">
                            <div>
                                <label class="block text-[12px] sm:text-[14px] text-secondary mb-2">E-mail</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2">
                                        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/drop.png" class="w-[12px] h-[12px] sm:w-[16px] sm:h-[16px]" alt="email">
                                    </span>
                                    <input type="email" name="user_login" placeholder="example@gmail.com" class="w-full pl-9 pr-3 py-3 border border-borderGray rounded-md focus:outline-none focus:ring-2 focus:ring-primary text-[12px] sm:text-[14px]">
                                </div>
                            </div>

                            <button type="submit" name="wp-submit" class="w-full bg-primary hover:bg-teal-600 text-white py-4 rounded-md font-semibold text-[14px] sm:text-[16px]">
                                Get Reset Link
                            </button>

                            <p class="tap-ajax-rsp text-center text-sm"></p>
                        </form>
                    </div>

                    <!-- Login Form -->
                    <div id="tappie-loginscreen" class="tappie-loginscreen">
                        <h2 class="font-semibold text-[24px] sm:text-[28px] text-center text-secondary mb-4">Welcome back to Tappie</h2>

                        <form method="POST" class="space-y-4">
                            <div>
                                <label class="block text-[12px] sm:text-[14px] text-secondary mb-2">E-mail</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2">
                                        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/drop.png" class="w-[12px] h-[12px] sm:w-[16px] sm:h-[16px]" alt="email">
                                    </span>
                                    <input name="un" type="text" placeholder="example@gmail.com" class="w-full pl-9 pr-3 py-3 border border-borderGray rounded-md focus:outline-none focus:ring-2 focus:ring-primary text-[12px] sm:text-[14px]">
                                </div>
                            </div>

                            <div>
                                <label class="block text-[12px] sm:text-[14px] text-secondary mb-2">Password</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2">
                                        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/key-icon.png" class="w-[12px] h-[12px] sm:w-[16px] sm:h-[16px]" alt="key">
                                    </span>
                                    <input name="up" type="password" id="login-password" placeholder="Enter password" class="w-full pl-9 pr-10 py-3 border border-borderGray rounded-md focus:outline-none focus:ring-2 focus:ring-primary text-[12px] sm:text-[14px]">
                                    <span id="toggleLoginPassword" class="absolute right-3 top-1/2 -translate-y-1/2 cursor-pointer hidden">
                                        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/eye.svg" class="w-[12px] h-[12px] sm:w-[16px] sm:h-[16px]" alt="eye">
                                    </span>
                                </div>
                            </div>

                            <div class="w-full flex justify-end">
                                <button type="button" class="text-primary text-[12px] sm:text-[14px] tappie-open-changepassword">Forgot password?</button>
                            </div>

                            <button type="submit" class="w-full bg-primary hover:bg-teal-600 text-white py-4 rounded-md font-semibold text-[14px] sm:text-[16px]">
                                Log In
                            </button>

                            <div class="alert alert-success !text-primary hiddenbtn" role="alert"></div>
                            <div class="alert alert-danger !text-[#e53935] hiddenbtn" role="alert"></div>
                        </form>

                        <p class="text-center text-muted text-[12px] sm:text-[14px] mt-4">
                            Don't have an account? <button type="button" class="text-primary font-medium signupnow">Sign Up</button>
                        </p>
                    </div>

                    <!-- Signup Form -->
                    <div id="tappie-signupscreen" class="tappie-signupscreen <?php echo $displayclass; ?>">
                        <h2 class="font-semibold text-[24px] sm:text-[28px] text-center text-secondary mb-4">Welcome to Tappie</h2>
                        <p class="text-[14px] sm:text-[16px] text-center text-muted mb-6">Create your account to start monetizing</p>

                        <form method="POST" class="space-y-4">
                            <div>
                                <label class="block text-[12px] sm:text-[14px] text-secondary mb-2">Create your link</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-secondary text-[12px] sm:text-[14px] flex items-center">
                                        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/Icon.png" class="mr-2 w-[12px] h-[12px] sm:w-[16px] sm:h-[16px]" alt="icon">
                                        tapsocial.co/
                                    </span>
                                    <input name="un" type="text" placeholder="your-name" class="w-full pl-[130px] pr-3 py-3 border border-borderGray rounded-md focus:outline-none focus:ring-2 focus:ring-primary text-[12px] sm:text-[14px]" required>
                                </div>
                            </div>

                            <div>
                                <label class="block text-[12px] sm:text-[14px] text-secondary mb-2">Name</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2">
                                        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/Profile.png" class="w-[12px] h-[12px] sm:w-[16px] sm:h-[16px]" alt="profile">
                                    </span>
                                    <input type="text" name="name" placeholder="Your Name" class="w-full pl-9 pr-3 py-3 border border-borderGray rounded-md focus:outline-none focus:ring-2 focus:ring-primary text-[12px] sm:text-[14px]" required>
                                </div>
                            </div>

                            <div>
                                <label class="block text-[12px] sm:text-[14px] text-secondary mb-2">E-mail</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2">
                                        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/drop.png" class="w-[12px] h-[12px] sm:w-[16px] sm:h-[16px]" alt="email">
                                    </span>
                                    <input name="uemail" type="email" placeholder="example@gmail.com" class="w-full pl-9 pr-3 py-3 border border-borderGray rounded-md focus:outline-none focus:ring-2 focus:ring-primary text-[12px] sm:text-[14px]" required>
                                </div>
                            </div>

                            <div>
                                <label class="block text-[12px] sm:text-[14px] text-secondary mb-2">Password</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2">
                                        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/key-icon.png" class="w-[12px] h-[12px] sm:w-[16px] sm:h-[16px]" alt="key">
                                    </span>
                                    <input name="up" type="password" id="signup-password" placeholder="Enter password" class="w-full pl-9 pr-10 py-3 border border-borderGray rounded-md focus:outline-none focus:ring-2 focus:ring-primary text-[12px] sm:text-[14px]" required>
                                    <span id="toggleSignupPassword" class="absolute right-3 top-1/2 -translate-y-1/2 cursor-pointer hidden">
                                        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/eye.svg" class="w-[12px] h-[12px] sm:w-[16px] sm:h-[16px]" alt="eye">
                                    </span>
                                </div>
                            </div>

                            <div>
                                <label class="block text-[12px] sm:text-[14px] text-secondary mb-2">Confirm Password</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2">
                                        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/key-icon.png" class="w-[12px] h-[12px] sm:w-[16px] sm:h-[16px]" alt="key">
                                    </span>
                                    <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Enter password again" class="w-full pl-9 pr-10 py-3 border border-borderGray rounded-md focus:outline-none focus:ring-2 focus:ring-primary text-[12px] sm:text-[14px]" required>
                                    <span id="toggleConfirmPassword" class="absolute right-3 top-1/2 -translate-y-1/2 cursor-pointer hidden">
                                        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/eye.svg" class="w-[12px] h-[12px] sm:w-[16px] sm:h-[16px]" alt="eye">
                                    </span>
                                </div>
                            </div>

                            <!-- Hidden field to detect signup -->
                            <input type="hidden" name="signup-submit" value="Sign Up">

                            <button type="submit" class="w-full bg-primary hover:bg-teal-600 text-white py-4 rounded-md font-semibold text-[14px] sm:text-[16px]">
                                Sign Up
                            </button>

                            <div class="alert alert-success !text-primary hiddenbtn" role="alert"></div>
                            <div class="alert alert-danger !text-[#e53935] hiddenbtn" role="alert"></div>

                            <p class="text-center text-muted text-[12px] sm:text-[14px] mt-4">
                                By registering, you agree to our <a href="#" class="text-primary">Terms</a> and <a href="#" class="text-primary">Privacy policy</a>
                            </p>

                            <p class="text-center text-muted text-[12px] sm:text-[14px] mt-4">
                                Already Setup? <button type="button" class="text-primary font-medium loginnow">Login now</button>
                            </p>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
 
<!-- Password toggle scripts -->
<!-- <script> 
</script>

<style>
  body { background: radial-gradient(circle at center, #e9f7f6 0%, #f4fbfb 100%); }
</style> -->

<?php
    $htmlForms = ob_get_contents();
    ob_end_clean();
    return $htmlForms;
}

add_shortcode('tappie_register_login', 'tappie_register_login_shortcode');