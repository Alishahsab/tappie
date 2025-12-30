<?php
ob_start();
// function that runs when shortcode is called
function tappie_admin_home_shortcode() { 
 
// Things that you want to do. 
$htmlForms = null; 


//$tappie_subscribers = '5344';
//$tappie_subscribers = get_users( [ 'role__in' => [ 'subscriber' ] ] );

?>



<!-- home admin dashboard-->


<div class="row mb-3">
     <!-- 
    <div class="col-xl-3 col-lg-6">
        <div class="card card-inverse card-success">
            <div class="card-block bg-success">
                <div class="rotate">
                    <i class="fa fa-user fa-5x"></i>
                </div>
                <h6 class="text-uppercase">Subscribers</h6>
                <h1 class="display-1"><?php echo $tappie_subscribers; ?></h1>
            </div>
        </div>
    </div>

   <div class="col-xl-3 col-lg-6">
        <div class="card card-inverse card-danger">
            <div class="card-block bg-danger">
                <div class="rotate">
                    <i class="fa fa-list fa-4x"></i>
                </div>
                <h6 class="text-uppercase">Subscribers</h6>
                <h1 class="display-1"><?php echo $tappie_subscribers; ?></h1>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-6">
        <div class="card card-inverse card-info">
            <div class="card-block bg-info">
                <div class="rotate">
                    <i class="fa fa-twitter fa-5x"></i>
                </div>
                <h6 class="text-uppercase">Subscribers</h6>
                <h1 class="display-1"><?php echo $tappie_subscribers; ?></h1>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-6">
        <div class="card card-inverse card-warning">
            <div class="card-block bg-warning">
                <div class="rotate">
                    <i class="fa fa-share fa-5x"></i>
                </div>
                <h6 class="text-uppercase">Subscribers</h6>
                <h1 class="display-1"><?php echo $tappie_subscribers; ?></h1>
            </div>
        </div>
    </div> -->

</div>
<!--/row-->


<h2 class="leadd">
    Following is the list of all subscribers
    <span class="float-right">
        <button class="tappie-download-users-in-csv btn btn-danger" type="button">Export in csv</button>
    </span>
</h2>

<hr>
<div class="row mb-3">

    <div class="col-lg-12 col-md-12">

        <div class="table-responsive">
            <table class="table table-striped tappie-users-table">

                <?php

                // Pagination vars
                $current_page = get_query_var('paged') ? (int) get_query_var('paged') : 1;
                $users_per_page = 10; // RAISE THIS AFTER TESTING ;)
                $count = 1;
                $args = array(
                    'number' => $users_per_page, // How many per page
                    'paged' => $current_page, // What page to get, starting from 1.
                    'role'      => 'Subscriber',
                );
                
                $users = new WP_User_Query( $args );
                // echo '<pre>';
                // print_r($users);
                // echo '</pre>';
                

                $total_users = $users->get_total(); // How many users we have in total (beyond the current page)
                $num_pages = ceil($total_users / $users_per_page); // How many pages of users we will need
                
                ?>
                <h5 class="subheaders">Subscribers : <span>Page <?php echo $current_page; ?> of
                        <?php echo $num_pages; ?></span></h5>


                <thead class="thead-inverse">
                    <tr>
                        <th>#</th>
                        <th>Username</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Joined On</th>
                        <th>Tappie</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                            if ( $users->get_results() ) foreach( $users->get_results() as $user )  {
                                $display_name = $user->display_name;
                                $user_login = $user->user_login;
                                $email = $user->user_email;
                                $user_registered = $user->user_registered;
                                $tapcode = get_user_meta($user->ID, 'passcodes', true);
                                ?>
                    <tr>
                        <td><?php echo esc_html($count); ?></td>
                        <td><?php echo esc_html($user_login); ?></td>
                        <td><?php echo esc_html($display_name); ?></td>
                        <td><?php echo esc_html($email); ?></td>
                        <td><?php echo date_i18n( get_option( 'date_format' ), strtotime( $user_registered ) );?></td>
                        <td><?php echo esc_html($tapcode); ?></td>
                    </tr>
                    <?php
                                $count++;
                            }
                            ?>
                </tbody>
            </table>

            <p class="tappie-user-paging">
                <?php
                        // Previous page
                        if ( $current_page > 1 ) {
                            echo '<a href="'. add_query_arg(array('paged' => $current_page-1)) .'">Previous Page</a>';
                        }
                
                        // Next page
                        if ( $current_page < $num_pages ) {
                            echo '<a href="'. add_query_arg(array('paged' => $current_page+1)) .'">Next Page</a>';
                        }
                        ?>
            </p>

        </div>
    </div>
</div>
<!--/row-->






<?php
		$htmlForms = ob_get_contents();
		ob_end_clean();
		// Output needs to be return
		return $htmlForms;
} 
// register shortcode
add_shortcode('tappie_admin_home', 'tappie_admin_home_shortcode');