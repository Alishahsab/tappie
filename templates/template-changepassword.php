<?php
/* 
	Template Name: Template Change Password
*/

get_header();

if ( ! is_user_logged_in() ) {
	wp_redirect( site_url() );
	exit;
}

$uid = get_current_user_id();
$message = '';

if ( $_SERVER['REQUEST_METHOD'] === 'POST' && isset( $_POST['tappie-update-password'] ) && $_POST['tappie-update-password'] === 'update password' ) {
	$new_password = sanitize_text_field( $_POST['cpassword'] ?? '' );

	if ( empty( $new_password ) ) {
		$message = '<p class="text-red-500 text-center">Please enter a new password.</p>';
	} else {
		wp_set_password( $new_password, $uid );
		// Update auth cookie to keep user logged in with new password
		wp_set_auth_cookie( $uid, true );
		$message = '<p class="text-green-500 text-center">Your password has been updated successfully.</p>';
	}
}
?>

 
<section class="min-h-screen flex flex-col items-center justify-center font-poppins">
    <div class="w-full min-h-screen flex items-center justify-center relative bg-bgLight">
     <img src="<?php echo get_template_directory_uri(); ?>/assets/img/loudspeker.png" class="absolute bottom-0 lg:w-[246.22px] left-10 w-12 sm:w-[15%]" alt="loudspeaker">
    <img src="<?php echo get_template_directory_uri(); ?>/assets/img/Frame 2661.png" class="absolute bottom-0 lg:w-[142.35px] left-1/2 -translate-x-1/2 w-24 sm:w-[15%]" alt="frame">
    <img src="<?php echo get_template_directory_uri(); ?>/assets/img/Like.png" class="absolute w-16 sm:w-[15%] lg:w-[200px] bottom-0 right-10" alt="like">

        <div class="w-full flex justify-center items-center bg-[url('images/main-bg.png')] bg-center bg-cover">
            <div class="py-20 flex justify-center w-full px-[15px] lg:px-4 sm:px-0">
                <div>
                    <div class="mb-6 flex top-5 left-0 absolute justify-center items-center w-full">
                        <img src="images/logo.png" alt="logo" class="w-32 sm:w-40" />
                    </div>

                    <!-- Card -->
                    <div class="bg-white w-full lg:w-[500px] rounded-xl shadow-md p-6 sm:p-8">
                        <h2 class="font-poppins font-semibold text-[24px] sm:text-[28px] leading-[1] tracking-normal text-center text-secondary mb-4">
                            Change your Password
                        </h2>
                        <p class="font-poppins font-normal text-[14px] sm:text-[16px] leading-[1] tracking-normal text-center text-muted mb-6">
                            Edit this Information
                        </p>

                        <?php echo $message; ?>

                        <form id="tappie-changepassword" class="tappie-changepassword space-y-4" method="POST">
                            <div>
                                <label class="font-poppins font-normal text-[12px] sm:text-[14px] leading-[1] tracking-normal text-secondary block mb-2">
                                    Password
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-primary text-sm">
                                        <img src="images/key-icon.png" alt="key" class="w-[12px] h-[12px] sm:w-[16px] sm:h-[16px]" />
                                    </span>

                                    <input
                                        name="cpassword"
                                        type="password"
                                        placeholder="Enter new password"
                                        id="password"
                                        class="w-full pl-9 pr-10 py-3 border border-borderGray rounded-md focus:outline-none focus:ring-2 focus:ring-primary font-poppins font-normal text-[12px] sm:text-[14px] leading-[1] tracking-[0%]"
                                        required
                                    />

                                    <!-- Eye toggle (optional - you can add JS to toggle visibility if needed) -->
                                    <span id="togglePassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 cursor-pointer">
                                        <img src="images/eye.svg" alt="eye" class="w-[12px] h-[12px] sm:w-[16px] sm:h-[16px]" >
                                    </span>
                                </div>
                            </div>

                            <!-- Button -->
                            <button
                                type="submit"
                                class="w-full bg-primary hover:bg-teal-600 font-poppins font-semibold text-[14px] sm:text-[16px] leading-[1] text-center text-white py-4 rounded-md font-medium transition"
                            >
                                Update Now
                            </button>

                            <input type="hidden" name="tappie-update-password" value="update password">
                        </form>

                        <!-- Optional footer links (adapted from signup design) -->
                        <p class="font-poppins font-normal text-[12px] sm:text-[14px] leading-[1] tracking-normal text-muted text-center mt-4">
                            Already have an account?
                            <a href="<?php echo wp_login_url(); ?>" class="text-primary font-medium">Log In</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
 

<?php
get_footer();