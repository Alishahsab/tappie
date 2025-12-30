<?php
/* 
	Template Name: Template Add Tapp Passcode
*/

get_header();
?>

<?php
	if( !is_user_logged_in()){
		wp_redirect( site_url());
		exit;
	}

	$uid = get_current_user_id();
	$icon = get_theme_mod('tappie_passcodeimg');
	
	// Assuming your theme has a logo set (e.g., via customizer). Adjust if needed.
	$logo = get_theme_mod('custom_logo');
	$logo_url = $logo ? wp_get_attachment_image_url($logo, 'full') : 'images/logo.png'; // fallback to your path
?>

<!-- Tailwind CSS CDN -->
<script src="https://cdn.tailwindcss.com"></script>

<!-- Google Fonts: Poppins (with common weights) -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

<script>
  tailwind.config = {
    theme: {
      extend: {
        colors: {
          primary: '#00BFA5',        // teal-500 equivalent
          secondary: '#242424',      // dark text
          muted: '#666568',          // muted text
          bgLight: '#F0F9F9',        // card bg
          bgGradientStart: '#e9f7f6',
          bgGradientEnd: '#f4fbfb',
          borderGray: '#E9F0F0'
        },
        fontFamily: {
          poppins: ['Poppins', 'sans-serif']
        }
      }
    }
  }
</script>

<style>
  body {
    background: radial-gradient(circle at center, #e9f7f6 0%, #f4fbfb 100%);
  }
</style>

<div class="min-h-screen flex flex-col items-center justify-center font-poppins">
  <div class="w-full min-h-screen flex items-center justify-center relative bg-bgLight">
    <img src="<?php echo get_template_directory_uri(); ?>/assets/img/loudspeker.png" class="absolute bottom-0 lg:w-[246.22px] left-10 w-12 sm:w-[15%]" alt="loudspeaker">
    <img src="<?php echo get_template_directory_uri(); ?>/assets/img/Frame 2661.png" class="absolute bottom-0 lg:w-[142.35px] left-1/2 -translate-x-1/2 w-24 sm:w-[15%]" alt="frame">
    <img src="<?php echo get_template_directory_uri(); ?>/assets/img/Like.png" class="absolute w-16 sm:w-[15%] lg:w-[200px] bottom-0 right-10" alt="like">

    <div class="w-full flex justify-center items-center bg-[url('<?php echo get_template_directory_uri(); ?>/images/main-bg.png')] bg-center bg-cover">
      <div class="py-20 flex justify-center w-full px-4 sm:px-0 lg:px-4">
        <div class="w-full max-w-lg">
          <!-- Logo -->
          <div class="mb-10 flex justify-center">
             <img src="<?php echo get_template_directory_uri(); ?>/assets/img/logo.png" alt="Tappie Logo" class="w-32 sm:w-40" />
          </div>

          <!-- Card -->
          <div class="bg-white w-full rounded-xl shadow-md p-6 sm:p-8">
            <h2 class="font-poppins font-semibold text-[24px] sm:text-[28px] text-center text-secondary mb-4">
              Link Your Tappie
            </h2>

            <p class="font-poppins font-normal text-[14px] sm:text-[16px] text-center text-muted mb-8">
              Make sure to enter the exact code behind your Tappie to set up your account
            </p>

            <!-- Passcode Image (from your original code) -->
            <!-- <div class="flex justify-center mb-8">
              <img src="<?php echo esc_url($icon); ?>" alt="Passcode Image" class="img-fluid max-w-full h-auto" />
            </div> -->

            <form id="tappie-addpasscode" class="tappie-addpasscode space-y-6" method="POST" enctype="multipart/form-data">
              <!-- Passcode Field -->
              <div>
                <label class="font-poppins font-normal text-[14px] text-secondary block mb-2">
                  Enter Passcode
                </label>
                <div class="relative">
                  <input
                    name="cpasscode"
                    type="text"
                    maxlength="6"
                    placeholder="------"
                    class="w-full text-center text-2xl font-bold tracking-widest py-4 border border-borderGray rounded-md focus:outline-none focus:ring-2 focus:ring-primary font-mono"
                    value=""
                  />
                </div>
              </div>

              <!-- Hint / Instruction -->
              <p class="text-center text-muted text-sm">
                Go to <span class="font-semibold text-primary">TapSocial.co</span> and register with the Passcode
              </p>

              <!-- Submit Button -->
              <button
                type="submit"
                class="w-full bg-primary hover:bg-teal-600 font-poppins font-semibold text-base text-white py-4 rounded-md transition"
              >
                Link Your Tappie
              </button>

              <input type="hidden" name="tappie-add-passcode" value="add passcode">
            </form>

            <!-- Footer Links -->
            <p class="font-poppins text-sm text-muted text-center mt-6">
              By continuing, you agree to our
              <a href="#" class="text-primary">Terms</a> and
              <a href="#" class="text-primary">Privacy policy</a>
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
get_footer();