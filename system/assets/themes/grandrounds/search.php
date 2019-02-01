<?php
/**
 * search template
 */

//add_filter( 'genesis_pre_get_option_site_layout', '__genesis_return_full_width_content' );
add_action('genesis_entry_footer', 'add_search_read_button');
function add_search_read_button() {
    global $post;
    //add whatever you want to $content here
    $content .= '<div><a class="button read-more" href="'.get_permalink($post).'">Read More</a></div>';
    print $content;
}
add_filter( 'genesis_pre_get_option_content_archive', 'sk_show_excerpts' );
function sk_show_excerpts() {
    return 'excerpts';
}

genesis();
