<?php
/**
 * Tappie functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Tappie
 */

// .....................................
add_action('wp_ajax_check_username', 'check_username_ajax');
add_action('wp_ajax_nopriv_check_username', 'check_username_ajax');

function check_username_ajax() {
    $username = sanitize_user($_GET['username'] ?? '');
    wp_send_json(['exists' => username_exists($username) ? true : false]);
    wp_die();
}


// ........................
function tappie_enqueue_custom_scripts() {
    // Enqueue custom-code.js
    // Handle: 'tappie-custom-js' (unique name)
    // Source: path to the file
    // Dependencies: array('jquery') if your script uses jQuery (common in WP); remove if not needed
    // Version: null (or use a version number like '1.0.0' or filemtime for cache busting)
    // Load in footer: true (recommended for performance)
    wp_enqueue_script(
        'tappie-custom-js',
        get_template_directory_uri() . '/assets/js/custom-code.js',
        array('jquery'),  // Change to array() if no jQuery dependency
        '1.0.0',          // Or use filemtime(get_template_directory() . '/assets/js/custom-code.js') for auto-versioning
        true
    );
}
add_action('wp_enqueue_scripts', 'tappie_enqueue_custom_scripts');
function mytheme_enqueue_sortablejs() {
    // Enqueue SortableJS from jsDelivr CDN
    wp_enqueue_script(
        'sortablejs', // handle name
        'https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js', // source URL
        array(), // no dependencies
        
    );
}
add_action('wp_enqueue_scripts', 'mytheme_enqueue_sortablejs');

// ......................
function theme_scripts() {
    wp_enqueue_style(
        'tailwind-cdn',
        get_stylesheet_directory_uri() .'/assets/dist/css/output.css',
         array(),
        time()
    );

wp_enqueue_style(
        'custom-style',
        get_stylesheet_directory_uri() . '/assits/modern-style.css',
        array(),
        time()
    );
   wp_enqueue_script(
        'modern-js',
        get_stylesheet_directory_uri() . '/assits/modern.js',
        array(),
        time()
    );

}
add_action('wp_enqueue_scripts', 'theme_scripts', "wp_enqueue_script");
/////////////////////////
/**
 * Enqueue scripts and styles.
 */
function tappie_scripts() {

	wp_enqueue_style( 'poppin-font', 'https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap', array(), null, false);

	// wp_enqueue_style( 'bootstrap', get_template_directory_uri().'/assets/lib/css/bootstrap.min.css', array(), null, false);

	wp_enqueue_style( 'fontawesome', get_template_directory_uri().'/assets/lib/fontawesome/css/all.min.css', array(), null, false);
	wp_enqueue_style( 'mainthemeresponsive', get_template_directory_uri().'/assets/css/responsive.css', array(), null, false);
	
	if(is_page_template( 'templates/template-admin-dashboard.php' )){
		wp_enqueue_style( 'admincss', get_template_directory_uri().'/assets/css/admin.css', array(), null, false);
	}
	wp_enqueue_style( 'maintheme', get_template_directory_uri().'/assets/css/main.css', array(), null, false);

	


	 
	
	// wp_enqueue_script( 'nicescroll', get_template_directory_uri() . '/assets/lib/jquery.nicescroll-master/jquery.nicescroll.min.js', array('jquery'), null, true );


	// wp_enqueue_script( 'jquertuijs', get_template_directory_uri() . '/assets/js/jquery-ui.js', array('jquery'), null, true );
	// wp_enqueue_script( 'jqueryuitoch', get_template_directory_uri() . '/assets/js/jquery.ui.touch-punch.min.js', array('jquery'), null, true );
	// wp_enqueue_script( 'jqueryuitoch', get_template_directory_uri() . '/assets/js/jquery.ui.touch-punch.js', array('jquery'), null, true );
	
	wp_enqueue_script( 'vcard', get_template_directory_uri() . '/assets/js/vcard.js', array('jquery'), null, true );

		if(is_page_template( 'templates/template-admin-dashboard.php' )){
			wp_enqueue_script( 'adminjs', get_template_directory_uri() . '/assets/js/admin.js', array('jquery'), null, true );
		}


	wp_enqueue_script( 'mainjs', get_template_directory_uri() . '/assets/js/main.js', array('jquery'), null, true );


	wp_localize_script('mainjs', 'ajaxURL', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ),'siteurl' => site_url() ) );



	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}


}
add_action( 'wp_enqueue_scripts', 'tappie_scripts' );














if ( ! function_exists( 'tappie_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function tappie_setup() {
		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on Tappie, use a find and replace
		 * to change 'tappie' to the name of your theme in all the template files.
		 */
		load_theme_textdomain( 'tappie', get_template_directory() . '/languages' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );

		// This theme uses wp_nav_menu() in one location.
		register_nav_menus( array(
			'user-dashboard' => esc_html__( 'User Dashboard', 'tappie' ),
			'visitor-menu' => esc_html__( 'Visitor Menu', 'tappie' ),
			'admin-menu' => esc_html__( 'Admin Menu', 'tappie' ),
		) );

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support( 'html5', array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		) );

		// Set up the WordPress core custom background feature.
		add_theme_support( 'custom-background', apply_filters( 'tappie_custom_background_args', array(
			'default-color' => 'ffffff',
			'default-image' => '',
		) ) );

		// Add theme support for selective refresh for widgets.
		add_theme_support( 'customize-selective-refresh-widgets' );

		/**
		 * Add support for core custom logo.
		 *
		 * @link https://codex.wordpress.org/Theme_Logo
		 */
		add_theme_support( 'custom-logo', array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		) );
	}
endif;
add_action( 'after_setup_theme', 'tappie_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function tappie_content_width() {
	// This variable is intended to be overruled from themes.
	// Open WPCS issue: {@link https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/issues/1043}.
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
	$GLOBALS['content_width'] = apply_filters( 'tappie_content_width', 640 );
}
add_action( 'after_setup_theme', 'tappie_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function tappie_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'tappie' ),
		'id'            => 'sidebar-1',
		'description'   => esc_html__( 'Add widgets here.', 'tappie' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'tappie_widgets_init' );




/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

require get_template_directory() . '/inc/admin-shortcodes.php';

require get_template_directory() . '/inc/shortcodes.php';

require get_template_directory() . '/inc/submit-functions.php';

require get_template_directory() . '/inc/tapp-socials-page.php';

require get_template_directory() . '/inc/backend-options.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}





/*all ajax request*/
add_action( 'wp_ajax_tappie_directbtn_function', 'tappie_directbtn_function' );
add_action( 'wp_ajax_nopriv_tappie_directbtn_function', 'tappie_directbtn_function' );
function tappie_directbtn_function(){

	$uid = get_current_user_id();
	$status = $_POST['status'];
	$selected = $_POST['selected'];
	$profileun = $_POST['profileun'];
	$link = $_POST['link'];
	if(!empty($status)):
			 
			update_user_meta($uid, 'direct_on_type', $selected);
			update_user_meta($uid, 'direct_profile_un', $profileun);
			update_user_meta($uid, 'direct_on', $status);
			update_user_meta($uid, 'link', $link);

		else:

			update_user_meta($uid, 'direct_on', false);
	endif;

	exit(json_encode($_POST['selected']));
}



 
// }



/*save social form btn*/
add_action( 'wp_ajax_tappie_save_this_social_function', 'tappie_save_this_social_function' );
add_action( 'wp_ajax_nopriv_tappie_save_this_social_function', 'tappie_save_this_social_function' );
function tappie_save_this_social_function(){

	$uid = get_current_user_id();

	$saved_profiles = array();

	if(get_user_meta($uid, 'saved_profiles', true)){
		$saved_profiles = get_user_meta($uid, 'saved_profiles', true);
	}

	$social = $_POST['social'];
	$sociallink = $_POST['sociallink'];
	$social_id = $_POST['social_id'];
	$social_name = $_POST['social_name'];
	$baseurl = $_POST['baseurl'];
	if(!empty($social) && !empty($sociallink) ):
		
		if(!isset($saved_profiles["$social"])){
			$saved_profiles[] = array(
											"url" => "$sociallink",
											"title" => "$social_name",
											"key" => "$social",
											"baseurl" => "$baseurl",
											"social_id" => "$social_id"
										);

			update_user_meta($uid, 'saved_profiles', $saved_profiles);
		}


	endif;

	exit(json_encode($_POST['sociallink']));
}



/*user login submit*/
add_action( 'wp_ajax_tappie_login_function', 'tappie_login_function' );
add_action( 'wp_ajax_nopriv_tappie_login_function', 'tappie_login_function' );
function tappie_login_function(){
 
	$returnArray = array();
	$un = $_POST['un'];
	$up = $_POST['up'];

	$credentials = array(
	        'user_login' => $un,
	        'user_password' => $up,
	        'rememberme' => true
	    );

		$uid = null;
		if(is_ssl() || isLocalhost()){
			$signon = wp_signon($credentials, false); 
		}else{
			$signon = wp_signon($credentials, true); // true - use HTTP only cookie
		}
	    
	    $dashboardURL = site_url().'/dashboard';

	    if(is_wp_error($signon)){

	        $returnArray = array(
	        	'error' => true,
	        	'msg' => 'Wrong logins! Please enter correct username and password',
	        );
	     }else{

	     	$uid = $signon->ID;
	     	$unaem = $signon->user_login;



	  




	     	wp_set_current_user($uid);
	     	wp_set_auth_cookie($uid);

	     	/*$dashboardURL = add_query_arg( array(
	     		'user'=> $unaem
	     	), $dashboardURL);*/

	     	$returnArray = array(
	     		'error' => false,
	     		'msg' => 'Success! redirecting soon',
	     		'url' => $dashboardURL,
	     	);



	     }

	
	exit(json_encode($returnArray));
}



/*user login submit*/
add_action( 'wp_ajax_tappie_registeration_function', 'wp_ajax_tappie_registeration_function' );
add_action( 'wp_ajax_nopriv_wp_ajax_tappie_registeration_function', 'wp_ajax_tappie_registeration_function' );
function wp_ajax_tappie_registeration_function(){
 

	$returnArray = array();
	$un = $_POST['un'];
	$uemail = $_POST['uemail'];
	$up = $_POST['up'];
	

	if ( preg_match('/\s/',$un) ) { 
		$returnArray = array(
			'error' => true,
			'msg' => 'Error! Profile name has space',
		);
		exit(json_encode($returnArray));
	}

	$user_id = username_exists( $un );
	 
	if ( ! $user_id && false == email_exists( $uemail ) ) {
	    $random_password = $up;
	    $user_id = wp_create_user( $un, $random_password, $uemail );



	    if(is_wp_error($user_id)){
		    $returnArray = array(
		    	'error' => true,
		    	'msg' => 'Error! Please try again',
		    );
		}else{


			$credentials = array(
			        'user_login' => $un,
			        'user_password' => $up,
			        'rememberme' => true
			    );

			if(is_ssl() || isLocalhost()){
				$signon = wp_signon($credentials, false); 
			}else{
				$signon = wp_signon($credentials, true); // true - use HTTP only cookie
			}

			if(is_wp_error($signon)){
				$returnArray = array(
					'error' => true,
					'msg' => 'Error! Registered but error in login',
				);
			}
			else{

				
				$uid = $signon->ID;
					     	$unaem = $signon->user_login;

					     	wp_set_current_user($uid);
					     	wp_set_auth_cookie($uid);

					     	update_user_meta($uid, 'passcodes', $up);

					     	$dashboardURL = site_url().'/dashboard';
					     	$dashboardURL = add_query_arg( array(
					     		'user'=> $unaem
					     	), $dashboardURL);
					     	update_option($up, $dashboardURL);


					     	// overwriting
					     	
					     	$dashboardURL = site_url().'/add-passcode';
					     	


				$returnArray = array(
					'error' => false,
					'msg' => 'Success! Please wait while redirecting...',
					'url' => $dashboardURL,
				);
			}
			
		}

	} else {
	    $returnArray = array(
	    		    	'error' => true,
	    		    	'msg' => 'Error! User already exists',
	    		    );
	}
	
	
	
	exit(json_encode($returnArray));
}




 







/*edit all btn tappie click*/
add_action( 'wp_ajax_tappie_all_calledit_function', 'tappie_all_calledit_function' );
add_action( 'wp_ajax_nopriv_tappie_all_calledit_function', 'tappie_all_calledit_function' );
function tappie_all_calledit_function(){

	$uid = get_current_user_id();
	$saved_profiles = array();
	$return = array();
	$direct_on = get_user_meta($uid, 'direct_on', true);

	ob_start();

	if(get_user_meta($uid, 'saved_profiles', true)){

		$saved_profiles = get_user_meta($uid, 'saved_profiles', true);
		//print('<pre>'.print_r($saved_profiles, false).'</pre>');
		if (!empty($saved_profiles)) {
			# code...
			ksort($saved_profiles);
			?>


					<?php
					$cnt = 1;
					foreach ($saved_profiles as $key => $value) {
						# code...

							$iconHTML = '<i class="'.$value['social_class'].'"></i>';
							if (isset($value['icon'])) {
								if (!empty($value['icon'])) {
									$iconHTML = '<img class="img-fluid" src="'.$value['icon'].'" alt="">';
								}
							}else{
								if (!empty($value['social_id'])) {
									# code...
									$featured_img_url = get_the_post_thumbnail_url($value['social_id'],'full');
									$iconHTML = '<img class="img-fluid" src="'.$featured_img_url.'" alt="">';
								}
							}

						?>
							<div class="tappliesort col-md-3 col-sm-6 col-6 position-relative sortable ui-sortable-handle" id="item-<?php echo $cnt; ?>"  data-keyid="<?= $key; ?>">
								<a class="userprofile-social  mb-4" href="#" data-tappie="<?php echo $value['key']; ?>">
									<?php echo $iconHTML; ?>
									<span><?php echo $value['title']; ?></span>
								</a>
								<a href="#" class="tappie-delete-profile" data-profileid = "<?php echo $key; ?>" data-profile-key="<?php echo $value['key']; ?>"><i class="far fa-times-circle tappie"></i></a>
							</div>
						<?php

					
					$cnt++;	
						
					}
					?>

			<?php		


		}else{
			echo 'Sorry!, Nothing Found.';
		}

	}

	$btnTag = '
		<a href="'.site_url().'" class="btn btn-info bcktohome">Save</a>
	';

	$return = array('html'=>ob_get_contents(), 'btn'=>$btnTag);
	ob_end_clean();
	exit(json_encode($return));
}







/*save reordering list in db*/
add_action( 'wp_ajax_tappie_save_order_socials_function', 'tappie_save_order_socials_function' );
add_action( 'wp_ajax_nopriv_tappie_save_order_socials_function', 'tappie_save_order_socials_function' );
function tappie_save_order_socials_function(){




	$resturnArray = array();
	$uid = get_current_user_id();
	$sorting_array = $_POST['data'];

	$new_orders_socials = array();
	$direct_on = get_user_meta($uid, 'direct_on', true);

	$saved_profiles = get_user_meta($uid, 'saved_profiles', true);

	$new_array = array();


	if(!empty($sorting_array)){
		// not empty
		foreach ($sorting_array as $key => $value) {
			# code...
			if(isset($saved_profiles[$key])){
				$new_array[] = $saved_profiles[$value];
			}
		}

		// 
		if(!empty($new_array)){
			//update the order
			update_user_meta($uid, 'saved_profiles', $new_array);
		}
	}




	//print('<pre>'.print_r($new_orders_socials, true).'</pre>');

	exit(json_encode($new_array));
}



/*adding user profile more field*/
add_filter('user_contactmethods', 'custom_user_contactmethods');
function custom_user_contactmethods($user_contact){ 
  $user_contact['ext_phone'] = 'Phone number';
  $user_contact['ext_designation'] = 'Designation';
  $user_contact['ext_location'] = 'Location';
  
  return $user_contact;
}



/*delete single profile and refersh page*/
add_action( 'wp_ajax_tappie_delte_this_prifle', 'tappie_delte_this_prifle' );
add_action( 'wp_ajax_nopriv_tappie_delte_this_prifle', 'tappie_delte_this_prifle' );
function tappie_delte_this_prifle(){

	$uid = get_current_user_id();
	$profileid = $_POST['profileid'];
	$profilekey = $_POST['profilekey'];
	$saved_profiles = get_user_meta($uid, 'saved_profiles', true);


	$sorting_array = $_POST['data'];

    $new_array = array();


    if(!empty($sorting_array)){
        // not empty
        foreach ($sorting_array as $key => $value) {
            # code...
            if(isset($saved_profiles[$key])){
                $new_array[] = $saved_profiles[$value];
            }
        }

        // 
        if(!empty($new_array)){
            //update the order
            update_user_meta($uid, 'saved_profiles', $new_array);
        }
    }

    //again getting from db
    $saved_profiles = get_user_meta($uid, 'saved_profiles', true);


	if (!empty($saved_profiles)) {
		# code...
		foreach ($saved_profiles as $key => $value) {
			# code...
			if($value['key']==$profilekey){
				unset($saved_profiles[$key]);
			}
			
		}
		update_user_meta($uid, 'saved_profiles', $saved_profiles);
	}



	



 	

	
	exit(json_encode($return));
}






/*ajax on creating vcf card*/
add_action( 'wp_ajax_tappie_vcf_this_profile', 'tappie_vcf_this_profile' );
add_action( 'wp_ajax_nopriv_tappie_vcf_this_profile', 'tappie_vcf_this_profile' );
function tappie_vcf_this_profile(){


	$uneme = $_POST['uname'];
	$userObj = get_user_by('login', $uneme);
	
	if (!is_wp_error($userObj)) {
		# code...
		$userfname = $userObj->first_name;
		$userlname = $userObj->last_name;
		$useremail = $userObj->user_email;
		$userlogin = $userObj->user_login;
		$userphone = $userObj->ext_phone;
		
		$return = array(
			'userlogin' => $userlogin,
			'userfname' => $userfname,
			'userlname' => $userlname,
			'useremail' => $useremail,
			'userphone' => $userphone
		);
		
		exit(json_encode($return));
	}else{
		exit(json_encode('error'));
	}
	
}


function theme_enqueue_fonts() {
    wp_enqueue_style(
        'syne-font',
        'https://fonts.googleapis.com/css2?family=Syne:wght@700&display=swap',
        false
    );
}
add_action('wp_enqueue_scripts', 'theme_enqueue_fonts');



/*ajax on creating vcf card*/
add_action( 'wp_ajax_tappie_generate_qrcode_profile', 'tappie_generate_qrcode_profile' );
add_action( 'wp_ajax_nopriv_tappie_generate_qrcode_profile', 'tappie_generate_qrcode_profile' );
function tappie_generate_qrcode_profile(){

	$uid = get_current_user_id();
	$userObj = get_user_by('id', $uid);
	$userlogin = $userObj->user_login;

	$baseUrl = site_url().'/dashboard';
	$baseUrl = add_query_arg( array(
					     		'user'=> $userlogin
					     	), $baseUrl);

	$urlls=" https://api.qrserver.com/v1/create-qr-code/?size=110x110&color=6276fc&data=" . $baseUrl;
	exit(json_encode($urlls));
}




/*ajax send reset password link*/
add_action( 'wp_ajax_tappie_send_reset_link', 'tappie_send_reset_link' );
add_action( 'wp_ajax_nopriv_tappie_send_reset_link', 'tappie_send_reset_link' );
function tappie_send_reset_link(){

	$returnArray = array();
	$username = $_POST['userlogin'];
	$userOBJ = null;
	if( !empty(username_exists( $username )) ){
		$userOBJ = get_user_by('login', $username);
	}elseif(!empty(email_exists( $username ))){
		$userOBJ = get_user_by('email', $username);
	}

	if (empty($userOBJ) || is_wp_error($userOBJ)) {
		# error...
		$returnArray = array(
			'error' => true,
			'msg' => 'Error! This username/email not exists',
			);
	}else{



		$uid = $userOBJ->ID;


		$firstname = $userOBJ->first_name;
	    $email = $userOBJ->user_email;
	    $adt_rp_key = get_password_reset_key( $userOBJ );
	    update_user_meta($uid, 'tappie_reset_key', $adt_rp_key);
	    $user_login = $userOBJ->user_login;
	    $rp_link = '<a href="' . site_url()."?tappie_reset_key=$adt_rp_key&login=" . rawurlencode($user_login) . '">' . site_url()."?tappie_reset_key=$adt_rp_key&login=" . rawurlencode($user_login) . '</a>';

	    $message .= "Click here to reset the password for your account: <br>";
	    $message .= $rp_link.'<br>';

	    //deze functie moet je zelf nog toevoegen. 
	   $subject = __("Your account reset password link on ".get_bloginfo( 'name'));
	   $headers = array();

	   add_filter( 'wp_mail_content_type', function( $content_type ) {return 'text/html';});
	   $senderemail = get_option('admin_email');
	   $headers[] = 'From: '.get_bloginfo( "name").' <'.$senderemail.'>'."\r\n";
	   $email_diliery = wp_mail( $email, $subject, $message, $headers);
	   remove_filter( 'wp_mail_content_type', 'set_html_content_type' );

	   if (!empty($email_diliery)) {
	   	# success...
	   		$returnArray = array(
	   			'error' => false,
	   			'msg' => 'Check your email inbox for reset password instructions.',
	   			'link' => $rp_link,
	   			);
	   }else{

	   		$returnArray = array(
	   			'error' => true,
	   			'msg' => 'Error! Please try again.',
	   			'link' => $rp_link,
	   			);

	   }
	}
	exit(json_encode($returnArray));
}






/*reset password proceed in db*/
add_action( 'wp_ajax_tappie_update_password_in_db', 'tappie_update_password_in_db' );
add_action( 'wp_ajax_nopriv_tappie_update_password_in_db', 'tappie_update_password_in_db' );
function tappie_update_password_in_db(){

	$returnArray = array();
	$user_id = $_POST['user_id'];
	$pw = $_POST['pw'];
	
	if ((!empty($pw))) {
		# code...
		delete_user_meta($user_id, 'tappie_reset_key');

		wp_set_password( $pw, $user_id );

		$returnArray = array(
			'error' => false,
			'msg' => 'Success! Password has been changed, <a href="'.site_url().'">Click here</a> to login',
			);
	}else{
		$returnArray = array(
			'error' => true,
			'msg' => 'Error! Please put password in field',
			);
	}

	exit(json_encode($returnArray));
}




/*update profile pic on change*/
add_action( 'wp_ajax_tappie_update_user_pic', 'tappie_update_user_pic' );
add_action( 'wp_ajax_nopriv_tappie_update_user_pic', 'tappie_update_user_pic' );
function tappie_update_user_pic(){

	$returnArray = array($_POST);
	$uid = get_current_user_id();
	$wordpress_upload_dir = wp_upload_dir();
	$i = 1;
	if ( isset($_FILES['tappie-prifleimg']['name'])) {
					if ( !empty($_FILES['tappie-prifleimg']['name'])) {
							$profilepicture = $_FILES['tappie-prifleimg'];
							$new_file_path = $wordpress_upload_dir['path'] . '/' . $profilepicture['name'];
							$new_file_mime = mime_content_type( $profilepicture['tmp_name'] );
							 
							 
							while( file_exists( $new_file_path ) ) {
								$i++;
								$new_file_path = $wordpress_upload_dir['path'] . '/' . $i . '_' . $profilepicture['name'];
							}
							 
							// looks like everything is OK
							if( move_uploaded_file( $profilepicture['tmp_name'], $new_file_path ) ) {
							 
								$upload_id = wp_insert_attachment( array(
									'guid'           => $new_file_path, 
									'post_mime_type' => $new_file_mime,
									'post_title'     => preg_replace( '/\.[^.]+$/', '', $profilepicture['name'] ),
									'post_content'   => '',
									'post_status'    => 'inherit'
								), $new_file_path );

								if (!empty($upload_id)) {
									# code...
									$img_atts = wp_get_attachment_image_src($upload_id, 'full');
									update_user_meta($uid, 'author_pic', $img_atts[0]);
								}
					 		}
					}
					$returnArray = array($_FILES);
			 	}

	exit(json_encode($returnArray));
}









//if localhost

function isLocalhost($whitelist = ['127.0.0.1', '::1']) {
    return in_array($_SERVER['REMOTE_ADDR'], $whitelist);
}

//after logout redirect to home page

//add_action( 'wp_logout', 'auto_redirect_external_after_logout');
function auto_redirect_external_after_logout(){
  wp_redirect( site_url() );
  exit();
}

//add necessary info on user registeration
add_action('user_register','tappie_new_user');

function tappie_new_user($user_id){
  //do your stuff
  		$saved_profiles = get_user_meta($user_id, 'saved_profiles', true);
  		$user_saved_cards = get_user_meta($user_id, 'user_saved_contact_card_auto', true);
		  if (empty($user_saved_cards)) {
			  # code...

			  if(empty($saved_profiles)){
				$saved_profiles = array();
			}
			$icon = get_theme_mod('tappie_contact_icon');
			
			  $saved_profiles[] = array(
											  "url" => "#",
											  "title" => "Contacts",
											  "key" => "contacts",
											  "social_class" => "far fa-address-book",
											  "social_id"		=> '',
											  "icon"		=> $icon,
										  );
	
			  update_user_meta($user_id, 'saved_profiles', $saved_profiles);
			  update_user_meta($user_id, 'user_saved_contact_card_auto', true);
		  }

}


function tappie_user_logingin($user_login) {
    //global $current_user; 
    $user_obj = get_user_by('login', $user_login);
    $user_id = $user_obj->ID;
	tappie_new_user($user_id);
}


add_action('wp_login', 'tappie_user_logingin', 10, 1);

/*add query variable*/





/*all available social profiles*/
function tappie_get_available_profiles(){

	$all_available_profiles = tappie_get_all_profiles();
	$filtered_profiles = array();
	$uid = get_current_user_id();

	if(!empty($uid)){
		
		$saved_keys = array();
		$saved_urls = array();
		$user_profiles = get_user_meta($uid, 'saved_profiles', true);
		if (!empty($user_profiles)) {
			# code...
			foreach ($user_profiles as $key => $value) {
				array_push($saved_keys, $value['key']);
				array_push($saved_urls, $value['url']);
			}
		}


			# code...
			foreach ($all_available_profiles as $key => $value) {
				# code...
				$active = '';
				$thisKey = $value['key'];
				if(array_search($thisKey, $saved_keys)===FALSE){
					$active = 'available';
					$value['url'] = '';
				}else{
					$thisactivekey = array_search($thisKey, $saved_keys);
					$value['url'] =$saved_urls["$thisactivekey"];
					$active = 'already_active';
				}
				$value['status'] = $active;

				array_push($filtered_profiles, $value);
			}


		

	}

	return $filtered_profiles;
}

/*redirect to relevant page*/

function tappie_logged_in_redirect() {

	if( current_user_can('administrator') ) {
		
		// $url = site_url().'/admin-dashboard/';
		// wp_safe_redirect( $url );
		// exit;

	}else{
			$protocol = $_SERVER['HTTPS'] == 'on' ? 'https' : 'http';
			$protocol .= '://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
			$link_array = explode('/',$protocol);
			$thispage = end($link_array);

			$retpage = get_option($thispage);
			if (!empty($retpage)) {
				# code...
				wp_redirect( $retpage );
				die;
			}
			

			if(is_404()){
					wp_safe_redirect( home_url('/') );
					exit;
			}
			
			
			if ( !is_user_logged_in()) 
			{
				if (isset($_GET['user'] )) {
					# code...
					$user = get_user_by('login', $_GET['user']);
					$userID = $user->ID;


					$direct_on = get_user_meta($userID, 'direct_on', true);

					if(!empty($direct_on)){
						$direct_profile = get_user_meta($userID, 'direct_on_type', true);
						$direct_profile_un = get_user_meta($userID, 'direct_profile_un', true);

						$web_social_url = null;
						$android_social_url = null;
						$ios_social_url = null;

						$direct_link = get_user_meta($userID, 'link', true);
						$available_profiels = tappie_get_all_profiles();
						if (!empty($available_profiels)) {
							foreach ($available_profiels as $value) {
								# code...
								if( $direct_profile == $value['key'] ){
									$web_social_url = $value['baseurl'];
									$android_social_url = $value['android_url'];
									$ios_social_url = $value['ios_url'];
								}
							}
						}
						/* if (strpos($direct_link, 'http') === 0){}else{
							$direct_link = 'http://'.$direct_link;
						} */
						
						if ( wp_is_mobile() ) {
							$direct_link = $ios_social_url.$direct_profile_un;
						}
						
						
						//$direct_link = 'http://instagram.com/_u/zaheer257/';
						//$direct_link = 'instagram://user?username=zaheer257';
						wp_redirect($direct_link);
						exit;

						
						
					}
					
				}
			
			}else{
				
				

					if ($_GET["user"] !='') return;
					$uid = get_current_user_id();
					$user = get_user_by('id', $uid);
					$username = $user->user_login;

					$baseUrl = $_SERVER['HTTPS'] == 'on' ? 'https' : 'http';
					$baseUrl .= '://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
					
					$location = add_query_arg( array(
											'user'=> $username
										), $baseUrl);

					wp_redirect( $location );
					exit;
			}
		}
}
add_action( 'template_redirect', 'tappie_logged_in_redirect' );

/*hiding bar for all users*/
show_admin_bar(false);


/*get url by key*/

function tappie_get_url_by_key($keyy){
	$uid = get_current_user_id();
	$saved_profiles = get_user_meta($uid, 'saved_profiles', true);
	if(!empty($saved_profiles)){
		foreach ($saved_profiles as $key => $value) {
			# code...
			if ($value['key']==$keyy) {
				# code...
				return $value['url'];
				break;
			}
		}
	}
}

/*all  social profiles*/

function tappie_get_all_profiles(){
	$all_available_profiles = array();
	$uid = get_current_user_id();

		$args = array(
			'post_type' => 'socials',
			'posts_per_page' => -1,
		);
		$the_query = new WP_Query( $args );
		 
		if ( $the_query->have_posts() ) {
		    while ( $the_query->have_posts() ) {
		        $the_query->the_post();

		        $tapp_key = get_post_meta(get_the_ID(), 'tapp_key', true);
		        $base_url = get_post_meta(get_the_ID(), 'base_url', true);
		        $android_url = get_post_meta(get_the_ID(), 'android_url', true);
		        $ios_url = get_post_meta(get_the_ID(), 'ios_url', true);
		        $placeholder = get_post_meta(get_the_ID(), 'link_label', true);
		        $featured_img_url = get_the_post_thumbnail_url(get_the_ID(),'full'); 
		        $thisSocial = array(
		        	'id' => get_the_ID(),
		        	'key' => $tapp_key,
		        	'title' => get_the_title(),
		        	'baseurl' => $base_url,
		        	'android_url' => $android_url,
		        	'ios_url' => $ios_url,
		        	'placeholder' => $placeholder,
		        	'icon' => $featured_img_url
		        );
		        array_push($all_available_profiles, $thisSocial);

		    }
		    wp_reset_postdata();
		} 
		


	return $all_available_profiles;
}




// REDI
/**
 * WordPress function for redirecting users on login based on user role
 */
function tappie_my_login_redirect( $url, $request, $user ) {
    if ( $user && is_object( $user ) && is_a( $user, 'WP_User' ) ) {
        if ( $user->has_cap( 'administrator' ) ) {
            $url = site_url().'/admin-dashboard/';
        } 
    }
    return $url;
}
 
add_filter( 'login_redirect', 'tappie_my_login_redirect', 10, 3 );


//hide wp-admin access
function tappie_admin_dashboard_redir(){
	if( is_admin() && !defined('DOING_AJAX') && ( current_user_can('administrator') || current_user_can('subscriber') || current_user_can('contributor') ) ){
	  wp_redirect(home_url());
	  exit;
	}
  }
//add_action('init','tappie_admin_dashboard_redir');

//hide wp-loign page



/*add http if not added*/
function tappie_addhttp($url) {
    if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
        $url = "http://" . $url;
    }
    return $url;
}

//////////////////customto update/////////////add_action('init', 'tappie_process_profile_update', 5);

function tappie_process_profile_update() {
    if (!isset($_POST['tappie-update-profile']) || $_POST['tappie-update-profile'] !== 'update profile') {
        return;
    }

    if (!is_user_logged_in()) {
        wp_safe_redirect(home_url());
        exit;
    }

    $uid = get_current_user_id();

    if (!check_admin_referer('tappie_update_profile', 'tappie_profile_nonce')) {
        wp_die('Security check failed.');
    }

    $ufname       = sanitize_text_field($_POST['ufname'] ?? '');
    $ulname       = sanitize_text_field($_POST['ulname'] ?? '');
    $ubio         = sanitize_textarea_field($_POST['ubio'] ?? '');
    $uphone       = sanitize_text_field($_POST['uphone'] ?? '');
    $ext_location = sanitize_text_field($_POST['ext_location'] ?? '');

    wp_update_user([
        'ID'          => $uid,
        'first_name'  => $ufname,
        'last_name'   => $ulname,
        'description' => $ubio,
    ]);

    update_user_meta($uid, 'ext_phone', $uphone);
    update_user_meta($uid, 'ext_location', $ext_location);

    // Add your social profiles logic here if needed...

    wp_safe_redirect(add_query_arg('profile-updated', 'success', wp_get_referer() ?: home_url('/profile')));
    exit;
}