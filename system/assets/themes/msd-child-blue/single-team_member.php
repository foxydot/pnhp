<?php
$msd_team_display = new MSDTeamDisplay;
/** Force full width layout */
add_filter( 'genesis_pre_get_option_site_layout', 'msdlab_force_layout' );
function msdlab_force_layout( $opt ) {
    $opt = 'full-width-content'; // You can change this to any Genesis layout
    return $opt;
}
remove_action('genesis_sidebar','genesis_do_sidebar');
//add_action('genesis_sidebar','msdlab_do_about_us_sidebar');
function msdlab_do_about_us_sidebar(){
    if(is_active_sidebar('team')){
        dynamic_sidebar('team');
    }
}

function msdlab_team_content_wrap($content){
    return '<div class="bio col-md-8 col-xs-12">'.$content.'</div>';
}
remove_action('genesis_entry_header','genesis_do_post_title'); //move the title out of the content area
add_action('genesis_entry_content',array(&$msd_team_display,'msd_add_team_member_headshot'),2);
add_action('msdlab_after_team_member_headshot','genesis_do_post_title');
add_action('msdlab_after_team_member_headshot',array(&$msd_team_display,'msd_team_member_info'));
//add_action('genesis_entry_header',array(&$msd_team_display,'msd_do_team_member_job_title'));
remove_action( 'genesis_entry_header', 'genesis_post_info', 12);
add_filter('the_content','msdlab_team_content_wrap',999);
//global $wp_filter; ts_var( $wp_filter['genesis_entry_header'] );
genesis();