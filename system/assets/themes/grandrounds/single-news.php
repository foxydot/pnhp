<?php
remove_all_actions('msdlab_title_area' );
add_action('msdlab_title_area','msdlab_news_category_banner');
function msdlab_news_category_banner(){
    global $post,$page_banner_metabox;
    $page_banner_metabox->the_meta();
    $bannerbool = $page_banner_metabox->get_the_value('bannerbool');
    if ($bannerbool == 'true') {
        msdlab_news_single_banner();
        return;
    }
    remove_action('genesis_before_loop','genesis_do_cpt_archive_title_description');
    remove_action('genesis_before_loop','genesis_do_date_archive_title');
    remove_action('genesis_before_loop','genesis_do_blog_template_heading');
    remove_action('genesis_before_loop','genesis_do_posts_page_heading');
    remove_action('genesis_before_loop','genesis_do_taxonomy_title_description',15);
    remove_action('genesis_before_loop','genesis_do_author_title_description',15);
    remove_action('genesis_before_loop','genesis_do_author_box_archive',15);
    $terms = wp_get_post_terms( $post->ID, 'news_category', array() );
    if(count($terms) >= 1){
        $bannerclass = $terms[0]->slug;
        $page_title = '<h2 class="entry-title" itemprop="headline">'.$terms[0]->name.'</h2>';
    }
    $bannerimage = get_stylesheet_directory_uri().'/lib/images/banner-news-category-'.$bannerclass.'.jpg';
    $background = strlen($bannerimage)>0?' style="background-image:url('.$bannerimage.')"':'';
    add_filter('genesis_post_title_text','msdlab_news_page_title');
    add_filter('genesis_link_post_title','msdlab_news_title_unlink');
    print '<div class="banner clearfix '.$bannerclass.'"'.$background.'>';
    print '<div class="gradient">';
    print '<div class="wrap">';
    print '<div class="bannertext">';
    //print genesis_do_post_title();
    print $page_title;
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

function msdlab_news_single_banner(){
    global $post, $page_banner_metabox;
    $page_banner_metabox->the_meta();
    $bannerbool = $page_banner_metabox->get_the_value('bannerbool');
    if ($bannerbool != 'true') {
        return;
    }
    $bannerclass = $page_banner_metabox->get_the_value('bannerclass');
    $bannerslider = $page_banner_metabox->get_the_value('bannerslider');
    if ($bannerslider > 0 && class_exists('LS_Sliders')) { //it's a slider
        layerslider($bannerslider);
    } else { //it's not a slider
        $banneralign = $page_banner_metabox->get_the_value('banneralign');
        $bannerimage = $page_banner_metabox->get_the_value('bannerimage');
        if (!$bannerimage) {
            if (has_post_thumbnail()) {
                $bannerimage = get_the_post_thumbnail_url();
            } else {
                $bannerimage = msdlab_get_random_banner_image();
            }
        }
        $bannercontent = do_shortcode($page_banner_metabox->get_the_value('bannercontent'));

        global $post;
        $background = strlen($bannerimage) > 0 ? ' style="background-image:url(' . $bannerimage . ')"' : '';
        print '<div class="banner clearfix ' . $banneralign . ' ' . $bannerclass . '"' . $background . '>';
        print '<div class="gradient">';
        print '<div class="wrap">';
        print '<div class="bannertext">';
        print '<div class="bannercontent">';
        if ($bannercontent == '') {
            $terms = wp_get_post_terms( $post->ID, 'news_category', array() );
            if(count($terms) >= 1){
                $page_title = '<h2 class="entry-title" itemprop="headline">'.$terms[0]->name.'</h2>';
            }
            //remove_action('genesis_entry_header','genesis_do_post_title');
            //genesis_do_post_title();
            print $page_title;
        } else {
            print $bannercontent;
        }
        print '</div>';
        print '</div>';
        print '</div>';
        print '</div>';
        print '</div>';
    }
}


add_action('genesis_sidebar','msdlab_news_category_recents',8);
function msdlab_news_category_recents(){ //fix to use parent category
    global $post;
    if (has_term('highlighted-research', 'news_category', $post)) {
        $term_id = 213;
    } else {
        $terms = wp_get_post_terms($post->ID, 'news_category', array(
            'hide_empty' => false,
        ));
        $term_obj = $terms[0];
        $term_id = $term_obj->term_id;
    }
    $args = array(
        'post_type' => 'news',
        'showposts' => 5,
        'tax_query' => array(
            array(
                'taxonomy' => 'news_category',
                'field'    => 'id',
                'terms'    => $term_id,
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
            <li><a title = "'.$recents->post->post_title.'" href = "'.get_the_permalink($recents->post->ID).'"> '.$recents->post->post_title.' </a></li>';
        } //end loop
        $ret .= '</ul></div></section>';
} //end loop check

    wp_reset_postdata();
    print $ret;
}
add_action('genesis_after_entry_content','msdlab_add_media_coverage');
function msdlab_add_media_coverage(){
    global $post, $news_info, $multimedia_info, $wpalchemy_media_access;
    $news_info->the_meta();
    // loop a set of field groups
    while ($news_info->have_fields('articles')) {
        $newsurl = $news_info->get_the_value('newsurl');
        $newstitle = $news_info->get_the_value('newstitle');
        $newsauthor = $news_info->get_the_value('newsauthor');
        $newspub = $news_info->get_the_value('newspub');
        $newsdate = $news_info->get_the_value('newsdate');
        $newstease = $news_info->get_the_value('newstease');
        $ret[] = '<div class="media-article">';
        $ret[] = '<h3 class="media-title"><a href="'.$newsurl.'" target="_blank">'.$newstitle.'</a></h3>';
        $ret[] = '<p class="meta">';
        if($newsauthor != ''){ $ret[] = $newsauthor; }
        if($newsauthor != '' && $newspub != ''){ $ret[] = ', '; }
        if($newspub != ''){ $ret[] = $newspub; }
        if($newspub != '' && $newsdate != ''){ $ret[] = ', '; }
        if($newsdate != ''){ $ret[] = 'Published: '.$newsdate; }
        $ret[] = '</p>';
        $ret[] = '<p class="teaser">'.$newstease.' <a href="'.$newsurl.'" target="_blank" class="read-more">Read More</a></p>';
        $ret[] = '</div>';
    }
    if(count($ret)>0) {
            print '<div class="media-coverage"><h2>Media Coverage</h2>';
            print implode("\n", $ret);
            print '</div>';
    }
}
genesis();