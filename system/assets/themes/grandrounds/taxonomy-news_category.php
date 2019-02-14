<?php
remove_all_actions('msdlab_title_area' );
add_action('msdlab_title_area','msdlab_news_cleanup');
add_action('msdlab_title_area','msdlab_news_category_banner');
add_action('genesis_entry_header', 'msdlab_add_pub_name');
add_action('genesis_entry_header', 'genesis_post_info');
add_action('genesis_entry_header','msdlab_multimedia_icons');
add_action('genesis_entry_content','msdlab_news_teaser');
add_action('genesis_before_loop','msdlab_do_taxonomy_description',15);

global $subtitle_support;
remove_action('genesis_entry_header', array($subtitle_support,'msdlab_do_post_subtitle'), 10);

function msdlab_news_category_banner(){
    $bannerclass = sanitize_title_with_dashes(single_term_title('',false));
    $bannerimage = get_stylesheet_directory_uri().'/lib/images/banner-news-category-'.$bannerclass.'.jpg';
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
function msdlab_news_teaser()
{
    global $post;
    $excerpt_length = 25;
    $trailing_character = '<i class="fa fa-arrow-circle-right"></i>';
    $the_excerpt = strip_tags(strip_shortcodes($post->post_excerpt), '<i>,<strong>,<bold>,<em>');

    if (empty($the_excerpt))
        $the_excerpt = strip_tags(strip_shortcodes($post->post_content), '<i>,<strong>,<bold>,<em>');

    $words = explode(' ', $the_excerpt, $excerpt_length + 1);

    if (count($words) > $excerpt_length)
        $words = array_slice($words, 0, $excerpt_length);

    $the_excerpt = implode(' ', $words) . ' ' . $trailing_character;
    print wpautop($the_excerpt);
}
function msdlab_news_page_title($title){
    $title = single_term_title('',false);
    return $title;
}
function msdlab_news_title_unlink(){
    return false;
}
function msdlab_do_taxonomy_description() {

    global $wp_query;

    if ( ! is_category() && ! is_tag() && ! is_tax() ) {
        return;
    }

    $term = is_tax() ? get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) ) : $wp_query->get_queried_object();

    if ( ! $term ) {
        return;
    }

    $heading = get_term_meta( $term->term_id, 'headline', true );
    if ( empty( $heading ) && genesis_a11y( 'headings' ) ) {
        //$heading = $term->name;
        $heading = '';
    }

    $intro_text = get_term_meta( $term->term_id, 'intro_text', true );
    $intro_text = apply_filters( 'genesis_term_intro_text_output', $intro_text ? $intro_text : '' );

    /**
     * Fires at end of doing taxonomy archive title and description.
     *
     * Allows you to reorganize output of the archive headings.
     *
     * @since 2.5.0
     *
     * @param string $heading    Archive heading.
     * @param string $intro_text Archive intro text.
     * @param string $context    Context.
     */
    do_action( 'genesis_archive_title_descriptions', $heading, $intro_text, 'taxonomy-archive-description' );

}
add_filter('genesis_attr_entry','msdlab_news_entry_attr');
add_filter('genesis_attr_entry','msdlab_maybe_equalize_attr');

add_action('genesis_entry_header','msdlab_multimedia_icons');
genesis();