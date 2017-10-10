<?php
remove_all_actions('msdlab_title_area' );
add_action('msdlab_title_area','msdlab_news_category_banner');
function msdlab_news_category_banner(){
    remove_action('genesis_before_loop','genesis_do_cpt_archive_title_description');
    remove_action('genesis_before_loop','genesis_do_date_archive_title');
    remove_action('genesis_before_loop','genesis_do_blog_template_heading');
    remove_action('genesis_before_loop','genesis_do_posts_page_heading');
    remove_action('genesis_before_loop','genesis_do_taxonomy_title_description',15);
    remove_action('genesis_before_loop','genesis_do_author_title_description',15);
    remove_action('genesis_before_loop','genesis_do_author_box_archive',15);
    $bannerclass = sanitize_title_with_dashes(single_term_title('',false));
    $bannerimage = get_stylesheet_directory_uri().'/img/banner-news-category-'.$bannerclass.'.png';
    $background = strlen($bannerimage)>0?' style="background-image:url('.$bannerimage.')"':'';
    add_filter('genesis_post_title_text','msdlab_news_page_title');
    add_filter('genesis_link_post_title','msdlab_news_title_unlink');
    print '<div class="banner clearfix '.$bannerclass.'"'.$background.'>';
    print '<div class="gradient">';
    print '<div class="wrap">';
    print '<div class="bannertext">';
    print genesis_do_post_title();
    print '</div>';
    print '</div>';
    print '</div>';
    print '</div>';
    remove_filter('genesis_link_post_title','msdlab_news_title_unlink');
    remove_filter('genesis_post_title_text','msdlab_news_page_title');
}
function msdlab_news_page_title($title){
    $title = single_term_title('',false);
    return $title;
}
function msdlab_news_title_unlink(){
    return false;
}
add_filter('genesis_attr_entry','msdlab_news_entry_attr');
function msdlab_news_entry_attr($attr){
    $attr['class'] .= ' col-xs-12 col-sm-6 col-md-4 equalize';
    return $attr;
}
add_action('genesis_entry_header','msdlab_multimedia_icons');
function msdlab_multimedia_icons(){
    global $post;
    global $multimedia_info;
    $multimedia_info->the_meta($post->ID);
    if($multimedia_info->get_the_value('hasvideo')){
        print '<i class="fa fa-video-camera"><span class="sr-only">This article includes video</span></i>';
    }
    if($multimedia_info->get_the_value('hasaudio')){
        print '<i class="fa fa-volume-up"><span class="sr-only">This article includes audio</span></i>';
    }
}
genesis();