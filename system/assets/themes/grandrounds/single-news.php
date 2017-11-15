<?php
remove_all_actions('msdlab_title_area' );
add_action('msdlab_title_area','msdlab_news_category_banner');
function msdlab_news_category_banner(){
    global $post;
    remove_action('genesis_before_loop','genesis_do_cpt_archive_title_description');
    remove_action('genesis_before_loop','genesis_do_date_archive_title');
    remove_action('genesis_before_loop','genesis_do_blog_template_heading');
    remove_action('genesis_before_loop','genesis_do_posts_page_heading');
    remove_action('genesis_before_loop','genesis_do_taxonomy_title_description',15);
    remove_action('genesis_before_loop','genesis_do_author_title_description',15);
    remove_action('genesis_before_loop','genesis_do_author_box_archive',15);
    $terms = wp_get_post_terms( $post->ID, 'news_category', array() );
    if(count($terms) == 1){
        $bannerclass = $terms[0]->slug;
    }
    $bannerimage = get_stylesheet_directory_uri().'/lib/images/banner-news-category-'.$bannerclass.'.png';
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
    global $post;
    $terms = wp_get_post_terms( $post->ID, 'news_category', array() );
    if(count($terms) == 1){
        $title = $terms[0]->name;
    }
    return $title;
}
function msdlab_news_title_unlink(){
    return false;
}


add_action('genesis_sidebar','msdlab_news_category_recents',8);
function msdlab_news_category_recents(){
    global $post;
    $terms = wp_get_post_terms( $post->ID, 'news_category', array(
        'hide_empty' => false,
    ) );
    $term_obj = $terms[0];
    $args = array(
        'post_type' => 'news',
        'showposts' => 5,
        'tax_query' => array(
            array(
                'taxonomy' => 'news_category',
                'field'    => 'id',
                'terms'    => $term_obj->term_id,
            ),
        ),
    );
    $recents = new WP_Query($args);
    if($recents->have_posts()) {
        $ret = '<section class="widget widget_recent_entries">
<div class="widget-wrap">
<h3 class="widgettitle widget-title">Recent ' . $term_obj->name . ' </h3>
<ul>';
//start loop
        while($recents->have_posts()) {
            $recents->the_post();
            $ret .= '
            <li><a title = "'.$recents->post->post_title.'" href = "'.$recents->post->permalink.'"> '.$recents->post->post_title.' </a></li>';
        } //end loop
        $ret .= '</ul></div></section>';
} //end loop check

    wp_reset_postdata();
    print $ret;
}
genesis();