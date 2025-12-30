<?php

/**
 * Register a custom post type called "book".
 *
 * @see get_post_type_labels() for label keys.
 */
function tapp_scoials_wpdocs_codex_init() {
    $labels = array(
        'name'                  => _x( 'Socials', 'Post type general name', 'textdomain' ),
        'singular_name'         => _x( 'Socials', 'Post type singular name', 'textdomain' ),
        'menu_name'             => _x( 'Socials', 'Admin Menu text', 'textdomain' ),
        'name_admin_bar'        => _x( 'Social', 'Add New on Toolbar', 'textdomain' ),
        'add_new'               => __( 'Add New', 'textdomain' ),
        'add_new_item'          => __( 'Add New Social', 'textdomain' ),
        'new_item'              => __( 'New Social', 'textdomain' ),
        'edit_item'             => __( 'Edit Social', 'textdomain' ),
        'view_item'             => __( 'View Social', 'textdomain' ),
    );
 
    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => false,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'socials' ),
        'capability_type'    => 'post',
        'has_archive'        => false,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array( 'title', 'thumbnail','excerpt'),
    );
 
    register_post_type( 'socials', $args );
}
 
add_action( 'init', 'tapp_scoials_wpdocs_codex_init' );


/*for metas*/

function tappsocial_register_meta_boxes() {

    add_meta_box( 'tappsocial-1', __( 'Social Settings', 'tabsocial' ), 'tappsocial_display_callback', 'socials' );

}

add_action( 'add_meta_boxes', 'tappsocial_register_meta_boxes' );


function tappsocial_display_callback( $post ) {

	$tapp_key = get_post_meta($post->ID, 'tapp_key', true);

	$base_url = get_post_meta($post->ID, 'base_url', true);
    
    $android_url = get_post_meta($post->ID, 'android_url', true);
    
    $ios_url = get_post_meta($post->ID, 'ios_url', true);

	$link_label = get_post_meta($post->ID, 'link_label', true);


    ?>



    	<div class="cyno_box">


    	    <p class="meta-options cyno_field">

    	        <label for="tapp_key">Add Key (without space)</label><br>

    	        <input id="tapp_key" type="text" name="tapp_key" value="<?php echo $tapp_key; ?>" placeholder="facebook">

    	    </p>

    	 

    	    <p class="meta-options cyno_field">

    	        <label for="base_url">Add Social URL(Base URL)</label><br>

    	        <input id="base_url" type="text" name="base_url" value="<?php echo $base_url; ?>" placeholder="http://www.facebook.com/">

    	    </p>



            <p class="meta-options cyno_field">

                <label for="android_url">Add Social URL(Android Base URL)</label><br>

                <input id="android_url" type="text" name="android_url" value="<?php echo $android_url; ?>" placeholder="http://www.facebook.com/">

            </p>

              <p class="meta-options cyno_field">

                <label for="ios_url">Add Social URL(IOS Base URL)</label><br>

                <input id="ios_url" type="text" name="ios_url" value="<?php echo $ios_url; ?>" placeholder="http://www.facebook.com/">

            </p>

            

    	    <p class="meta-options cyno_field">

    	        <label for="link_label">Add Label Placeholder(Add user name / add website url)</label><br>

    	        <input id="link_label" type="text" name="link_label" value="<?php echo $link_label; ?>" placeholder="Add facebook username here">

    	    </p>

    	 

    	</div>



    <?php

}


//save data
function tappie_save_meta_box( $post_id ) {

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

    if ( $parent_id = wp_is_post_revision( $post_id ) ) {

        $post_id = $parent_id;

    }

    $fields = [

        'tapp_key',

        'base_url',

        'android_url',

        'ios_url',

        'link_label',
    ];

    foreach ( $fields as $field ) {

        if ( array_key_exists( $field, $_POST ) ) {

            update_post_meta( $post_id, $field, sanitize_text_field( $_POST[$field] ) );

        }

     }

}

add_action( 'save_post', 'tappie_save_meta_box' );




/*registering option for icon upload of ad contact*/

    // Customiser
function tapp_customize_register( $wp_customize ) {
   
    // Add Settings
       // Add and manipulate theme images to be used.
       $wp_customize->add_section('tappie_contactsocial', array(
	       "title" => 'Contact Social Icon',
	       "priority" => 28,
	       "description" => __( 'Upload contact icon for social profiles', 'theme-slug' )
       ));
       $wp_customize->add_setting('tappie_contact_icon', array(
	       'default' => '',
	       'type' => 'theme_mod',
	       'capability' => 'edit_theme_options',
       ));
       $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'tappie_contact_icon', array(
	       'label' => __( 'Upload icon for contact', 'theme-slug' ),
	       'section' => 'tappie_contactsocial',
	       'settings' => 'tappie_contact_icon',
	       ))
       );


// Add Settings
       // Add and manipulate theme images to be used.
       $wp_customize->add_section('tappie_tappimg', array(
           "title" => 'Tappie round image',
           "priority" => 28,
           "description" => __( 'Upload round image for "add passcode" page', 'theme-slug' )
       ));
       $wp_customize->add_setting('tappie_passcodeimg', array(
           'default' => '',
           'type' => 'theme_mod',
           'capability' => 'edit_theme_options',
       ));
       $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'tappie_passcodeimg', array(
           'label' => __( 'Upload image for passcode on "add passcode" page', 'theme-slug' ),
           'section' => 'tappie_tappimg',
           'settings' => 'tappie_passcodeimg',
           ))
       );

       

}
add_action('customize_register', 'tapp_customize_register');