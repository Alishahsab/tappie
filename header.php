<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Tappie
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="msapplication-tap-highlight" content="no">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <link rel="profile" href="https://gmpg.org/xfn/11">


    <?php wp_head(); ?>
</head>

<!-- Facebook Pixel Code -->
<script>
! function(f, b, e, v, n, t, s) {
    if (f.fbq) return;
    n = f.fbq = function() {
        n.callMethod ?
            n.callMethod.apply(n, arguments) : n.queue.push(arguments)
    };
    if (!f._fbq) f._fbq = n;
    n.push = n;
    n.loaded = !0;
    n.version = '2.0';
    n.queue = [];
    t = b.createElement(e);
    t.async = !0;
    t.src = v;
    s = b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t, s)
}(window, document, 'script',
    'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '258672561918146');
fbq('track', 'PageView');
</script>
<noscript>
    <img height="1" width="1" src="https://www.facebook.com/tr?id=258672561918146&ev=PageView
&noscript=1" />
</noscript>
<!-- End Facebook Pixel Code -->

<body <?php body_class(); ?>>

    <?php if( current_user_can('administrator') ) {  ?>



    <?php }else{  ?>
    <div class="bodyoverlay"></div>
    <div class="lds-dual-ring position-fixed tappieloading" role="status"></div>
     

    <?php
			if(!is_user_logged_in()){ ?>
    <!-- <div class="d-block d-sm-none">
        <a class="btn btn-success btn-block" href="http://tappie.co/">Shop Now</a>
    </div> -->

    <?php
			}
		?>


    
            <?php
	}
	?>