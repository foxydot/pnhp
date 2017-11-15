<?php
add_action('wp_enqueue_scripts','msdlab_add_speaker_scripts',12);
function msdlab_add_speaker_scripts()
{
    wp_enqueue_script('msd-speaker-jquery', get_stylesheet_directory_uri() . '/lib/js/msd-speaker-jquery.js', array('jquery', 'bootstrap-jquery'));
}

remove_all_actions('genesis_loop');
add_action('genesis_loop','msdlab_speaker_aggregated',11);
function msdlab_speaker_aggregated(){
    global $wp_query;
    add_filter('genesis_attr_entry','msdlab_speaker_entry_attr');
    add_action('genesis_entry_header','msdlab_speaker_entry_hdr_img',4);
    add_action('genesis_entry_header','msdlab_speaker_entry_hdr_cats');
    $args = array(
        'post_type' => 'speaker',
        'posts-per-page' => 12,
    );
    $args['paged'] = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;

    $speakers = new WP_Query($args);

    // Pagination fix
    $temp_query = $wp_query;
    $wp_query   = NULL;
    $wp_query   = $speakers;

    if($speakers->have_posts()) {
        print '<section class="speaker_aggregate clearfix">
<div class="wrap">';
        //start loop
        while($speakers->have_posts()) {
            $speakers->the_post();
            get_template_part('speaker-aggregate-loop');
        } //end loop
        print '</div></section>';
        the_posts_pagination();
    } //end loop check


    wp_reset_postdata();
    // Reset main query object
    $wp_query = NULL;
    $wp_query = $temp_query;

    remove_filter('genesis_attr_entry','msdlab_speaker_entry_attr');
}

//add a modal to the page
add_action('wp_footer','msdlab_add_content_modal');
//use JS to populate it with the selected article?
genesis();