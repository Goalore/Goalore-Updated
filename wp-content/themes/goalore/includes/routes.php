<?php   

    /*
     *CUSTOM ROUTES SLUGS ARE DEFINED IN wp-config.php 
     */
    
add_action('init', function() {

     /*
      *Define Profile Page URL
      */
     global $wp_rewrite;
     $author_slug = PROFILE;
     $wp_rewrite->author_base = $author_slug;

     /*
      *Define custom routes 
      */
     $url_path = trim(parse_url(add_query_arg(array()), PHP_URL_PATH), '/');

     if (is_user_logged_in()) {

         /*Check if member account is not deactivated*/
         $currentUserID = get_current_user_id();
         $TFAV = get_user_meta($currentUserID, '2FAV', true);
         
         //Check if user is OTP verified
         if ($TFAV == '1') {

            $isDeactivated = get_user_meta($currentUserID, 'isDeactivated', true);
            $UNAVAILABLE = false;
            if ($isDeactivated == 1) $UNAVAILABLE = true;

            //check if user is Deactivated
            if (!$UNAVAILABLE) {
                 //By default load profile page
                 if ($url_path === PROFILE) {
                     $load = locate_template('author.php', true);
                     if ($load) {
                         exit();
                     }
                 }
                 //Load my connection page
                 if ($url_path === PROFILE.'/'.MY_CONNECTIONS) {
                     $load = locate_template('templates/my-connections.php', true);
                     if ($load) {
                         exit();
                     }
                 }

                 //Load profile settings page
                 if ($url_path === SearchResult) {
                     $load = locate_template('templates/search.php', true);
                     if ($load) {
                         exit();
                     }
                 }
                 //Load invite friend page
                 if ($url_path === InviteFriend) {
                     $load = locate_template('templates/invite-friend.php', true);
                     if ($load) {
                         exit();
                     }
                 }

                 //Load invite friend page
                 if ($url_path === 'notification') {
                     $load = locate_template('templates/notification-mobile.php', true);
                     if ($load) {
                         exit();
                     }
                 }

                 //Load invite friend page
                 if ($url_path === 'gdp') {
                     $load = locate_template('templates/GDP-mobile.php', true);
                     if ($load) {
                         exit();
                     }
                 }

             }

             //Load invite friend page
             if ($url_path === CONTACT) {
                 $load = locate_template('templates/contact.php', true);
                 if ($load) {
                     exit();
                 }
             }

             //Load profile settings page
             if ($url_path === PROFILE.'/'.SETTINGS) {
                 $load = locate_template('templates/settings.php', true);
                 if ($load) {
                     exit();
                 }
             }
         } else {
            //Not OTP verified
            //check if its custom route 
            if (    $url_path === TFA_SLUG || 
                    $url_path === PROFILE || 
                    $url_path === PROFILE.'/'.MY_CONNECTIONS || 
                    $url_path === SearchResult || 
                    $url_path === InviteFriend || 
                    $url_path === CONTACT || 
                    $url_path === PROFILE.'/'.SETTINGS 
                ) {
                $load = locate_template('templates/verify_otp.php', true);
                if ($load) {
                    exit();
                }
            }
         }


     }


 });