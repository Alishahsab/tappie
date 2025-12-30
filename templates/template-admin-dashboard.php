<?php
/* 
	Template Name: Admin Dashboard
*/

get_header();

?>

<nav class="navbar navbar-fixed-top navbar-toggleable-sm navbar-inverse bg-primary mb-3">

    <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse"
        data-target="#collapsingNavbar">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
    </button>
    <div class="flex-row d-flex">
        <?php
                

                if ( has_custom_logo() ) :
                    $image = wp_get_attachment_image_src( get_theme_mod( 'custom_logo' ), 'full' );
                    if (count($image)>0):
            ?>

        <a class="navbar-brand mx-auto tappie-logo" href="#">
            <img src="<?php echo $image[0]; ?>" class="img-fluid">
        </a>
        <?php 
                	endif;
                endif;
            ?>
    </div>

    <div class="navbar-collapse collapse" id="collapsingNavbar">
        <!-- code for mobile dropdown -->
        <?php
                wp_nav_menu( array(
                    'theme_location' => 'admin-menu',
                    'menu_id'        => 'admin-menu',
                    'menu_class'        => 'nav flex-column pl-1',
                ) );
                
                ?>

        <ul class="nav flex-column pl-1">
            <li>
                <a href="<?php echo wp_logout_url( home_url() ); ?>">Logout</a>
            </li>
        </ul>
    </div>
</nav>
<div class="container-fluid" id="main">
    <div class="row row-offcanvas row-offcanvas-left">
        <div class="col-md-3 col-lg-2 sidebar-offcanvas" id="sidebar" role="navigation">
            <?php
                wp_nav_menu( array(
                    'theme_location' => 'admin-menu',
                    'menu_id'        => 'admin-menu',
                    'menu_class'        => 'nav flex-column pl-1',
                ) );
                
                ?>

            <ul class="nav flex-column pl-1">
                <li>
                    <a href="<?php echo wp_logout_url( home_url() ); ?>">Logout</a>
                </li>
            </ul>

        </div>
        <!--/col-->

        <div class="col-md-9 col-lg-10 main">

            <?php


            while ( have_posts() ) :
                the_post();
                the_content();
            endwhile; // End of the loop.
            ?>


            <hr>
        </div>



        <?php
get_footer();