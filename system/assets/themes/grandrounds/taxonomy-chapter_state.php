<?php
/**
 * Created by PhpStorm.
 * User: CMO
 * Date: 7/16/18
 * Time: 9:38 AM
 */
remove_all_actions('msdlab_title_area' );
add_action('msdlab_title_area','msdlab_news_cleanup');
add_action('msdlab_title_area','msdlab_chapter_state_banner');
add_action('genesis_after_loop',array('MSDChapterCPT','add_state_news'));
function msdlab_chapter_state_banner(){
    $bannerclass = sanitize_title_with_dashes(single_term_title('',false));
    $bannerimage = get_stylesheet_directory_uri().'/lib/images/banner-chapters.jpg';
    $background = strlen($bannerimage)>0?' style="background-image:url('.$bannerimage.')"':'';
    add_filter('genesis_post_title_text','msdlab_term_page_title');
    add_filter('genesis_link_post_title','msdlab_term_title_unlink');
    print '<div class="banner clearfix '.$bannerclass.'"'.$background.'>';
    print '<div class="gradient">';
    print '<div class="wrap">';
    print '<div class="bannertext">';
    print genesis_do_post_title();
    print '</div>';
    print '</div>';
    print '</div>';
    print '</div>';
    remove_filter('genesis_link_post_title','msdlab_term_title_unlink');
    remove_filter('genesis_post_title_text','msdlab_term_page_title');
}
function msdlab_term_page_title($title){
    $title = single_term_title('',false);
    return $title;
}
function msdlab_term_title_unlink(){
    return false;
}
genesis();