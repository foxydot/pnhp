<?php
add_action('wp_enqueue_scripts','msdlab_add_news_scripts',12);
function msdlab_add_news_scripts()
{
    wp_enqueue_script('msd-news-jquery', get_stylesheet_directory_uri() . '/lib/js/msd-news-jquery.js', array('jquery', 'bootstrap-jquery'));
}
add_action('msdlab_title_area','msdlab_news_cleanup');
//add_action('genesis_entry_header','msdlab_multimedia_icons',12);
//add_filter('')
remove_all_actions('genesis_loop');
add_action('genesis_loop','msdlab_news_media_runner',11);
add_action('genesis_loop','msdlab_news_recents_aggregated',11);
function msdlab_news_recents_aggregated(){
    add_filter('genesis_attr_entry','msdlab_news_entry_attr');
    add_action('genesis_entry_header','msdlab_multimedia_icons');
    //Do I need to strip away links in these? add_filter('the_content','msdlab_strip_long_links');
    $terms = get_terms( array(
        'taxonomy' => 'news_category',
        'hide_empty' => false,
    ) );
    $allowed_terms = array('articles-of-interest','members-in-the-news','quote-of-the-day','state-single-payer-news');

    if(wp_is_mobile()){$postcnt = 1;}else{$postcnt = '3';}
    foreach ($terms as $term_obj){
        if(!in_array($term_obj->slug,$allowed_terms)){ continue; }
        $args = array(
            'post_type' => 'news',
            'showposts' => $postcnt,
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
            print '<section class="news_category_aggregate news_category_'.$term_obj->slug.' clearfix">
<div class="wrap">
<h3 class="widgettitle widget-title">Recent ' . $term_obj->name . ' </h3>';
//start loop
            while($recents->have_posts()) {
                $recents->the_post();
                do_action( 'genesis_before_entry' );

                genesis_markup( array(
                    'open'    => '<article %s>',
                    'context' => 'entry',
                ) );

                do_action( 'genesis_entry_header' );

                do_action( 'genesis_before_entry_content' );

                printf( '<div %s>', genesis_attr( 'entry-content' ) );
                do_action( 'genesis_entry_content' );
                echo '</div>';

                do_action( 'genesis_after_entry_content' );

                do_action( 'genesis_entry_footer' );

                genesis_markup( array(
                    'close'   => '</article>',
                    'context' => 'entry',
                ) );

                do_action( 'genesis_after_entry' );
            } //end loop
            print '</div></section>';
        } //end loop check
    }


    wp_reset_postdata();

    remove_filter('genesis_attr_entry','msdlab_news_entry_attr');
    remove_action('genesis_entry_header','msdlab_multimedia_icons');
}

//add a modal to the page
add_action('wp_footer','msdlab_add_content_modal');
//use JS to populate it with the selected article?
genesis();