<?php

function msdlab_speaker_cleanup()
{
    remove_action('genesis_before_loop', 'genesis_do_cpt_archive_title_description');
    remove_action('genesis_before_loop', 'genesis_do_date_archive_title');
    remove_action('genesis_before_loop', 'genesis_do_blog_template_heading');
    remove_action('genesis_before_loop', 'genesis_do_posts_page_heading');
    remove_action('genesis_before_loop', 'genesis_do_taxonomy_title_description', 15);
    remove_action('genesis_before_loop', 'genesis_do_author_title_description', 15);
    remove_action('genesis_before_loop', 'genesis_do_author_box_archive', 15);
}

function msdlab_speaker_entry_attr($attr){
    global $post;
    $attr['class'] .= ' col-xs-12 col-sm-6 col-md-4';
    /*$filters = array('speaker_region','speaker_specialty','speaker_topic');
    foreach($filters AS $filter){
        $arr = array();
        ${$filter} = get_the_terms($post->ID,$filter);
        foreach(${$filter} AS $f){
            $arr[] = $f->name;
        }
        $attr['data-'.$filter] = implode(', ',$arr);
    }*/
    return $attr;
}


function msdlab_speaker_entry_hdr_img(){
    global $post;
    //setup thumbnail image args to be used with genesis_get_image();
    $size = 'full-size'; // Change this to whatever add_image_size you want
    $default_attr = array(
        'class' => "attachment-$size $size",
        'alt'   => $post->post_title,
        'title' => $post->post_title,
    );
    if ( has_post_thumbnail() ){
        printf( '<section class="speaker-headshot"><a href="%s" title="%s" >%s</a></section>', get_permalink(), the_title_attribute( 'echo=0' ), genesis_get_image( array( 'size' => $size, 'attr' => $default_attr ) ) );
    }
}

function msdlab_speaker_entry_hdr_cats(){
    global $post;
    $ret = '';
    $region = get_the_term_list($post->ID,'speaker_region','<span class="state">Region: ',', ','</span>');
    $ret .= $region;
    $specialty = get_the_term_list($post->ID,'speaker_specialty','<div class="specialty">Specialty: ',', ','</div>');
    $ret .= $specialty;
    $topic = get_the_term_list($post->ID,'speaker_topic','<div class="topic">Topic(s): ',', ','</div>');
    $ret .= $topic;
    if(strlen($ret)>0)
    print('<div class="speaker-meta">'.$ret.'</div>');
}

/**
 * AJAX Load More
 * @link http://www.billerickson.net/infinite-scroll-in-wordpress
 */
function be_ajax_load_more() {
    $args = isset( $_POST['query'] ) ? array_map( 'esc_attr', $_POST['query'] ) : array();
    $args['paged'] = esc_attr( $_POST['page'] );
    $args['ajax'] = true;
    ob_start();
    msdlab_speaker_aggregated($args);
    $data = ob_get_clean();
    wp_send_json_success( $data );
    wp_die();
}
add_action( 'wp_ajax_be_ajax_load_more', 'be_ajax_load_more' );
add_action( 'wp_ajax_nopriv_be_ajax_load_more', 'be_ajax_load_more' );


add_action('wp_enqueue_scripts','msdlab_add_speaker_scripts',12);
function msdlab_add_speaker_scripts()
{
    if(msdlab_is_speaker_page()) {
        global $speakers;
        $args = array(
            'url' => admin_url('admin-ajax.php'),
            'query' => $speakers->query,
        );

        wp_enqueue_script('isotope', get_stylesheet_directory_uri() . '/lib/js/isotope-pkgd-min.js', array('jquery'), '3.0.4', true);
        wp_enqueue_script('be-load-more', get_stylesheet_directory_uri() . '/lib/js/speaker-jquery-min.js', array('jquery', 'jquery-masonry'), '1.0', true);
        wp_localize_script('be-load-more', 'beloadmore', $args);
    }
}

function msdlab_speaker_aggregated($options = array()){
    global $wp_query,$speakers;
    add_filter('genesis_attr_entry','msdlab_speaker_entry_attr');
    add_action('genesis_entry_header','msdlab_speaker_entry_hdr_img',8);
    add_action('genesis_entry_header','msdlab_speaker_entry_hdr_cats');
    $defaults = array(
        'post_type' => 'speaker',
        'posts-per-page' => 12,
    );
    $defaults['paged'] = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
    $args = wp_parse_args($options, $defaults);
    $ajax = $args['ajax'];
    unset($args['ajax']);
    $speakers = new WP_Query($args);
    // Pagination fix
    //$temp_query = $wp_query;
    //$wp_query   = NULL;
    //$wp_query   = $speakers;

    if($speakers->have_posts()) {
        if(!$ajax) {
            print '<section class="speaker_aggregate clearfix">
<div class="wrap" id="filterArea">';
        }
        //start loop
        while($speakers->have_posts()) {
            $speakers->the_post();
            get_template_part('speaker-aggregate-loop');
        } //end loop
        if(!$ajax) {
            print '</div></section>';
        }
        //the_posts_pagination();
    } //end loop check


    wp_reset_postdata();
    // Reset main query object
    //$wp_query = NULL;
    //$wp_query = $temp_query;

    remove_filter('genesis_attr_entry','msdlab_speaker_entry_attr');
}

function msdlab_is_speaker_page($post_id = null){
    $match = 'speaker';
    if($post_id == null){
        global $post;
        $post_id = $post->ID;
    }
    if ( $post = get_post( $post_id ) ){
        $post_type = $post->post_type;
        $post_name = $post->post_name;
    }
    //if($post_type == $match && !is_single()){return true;}
    if(preg_match('/'.$match.'/i',$post_name)){return true;}
    return false;
}

function msdlab_speaker_taxonomy_banner(){
    $bannerclass = sanitize_title_with_dashes(single_term_title('',false));
    $bannerimage = get_stylesheet_directory_uri().'/lib/images/banner-'.$taxonomy.'.png';
    $background = strlen($bannerimage)>0?' style="background-image:url('.$bannerimage.')"':'';
    add_filter('genesis_post_title_text','msdlab_speaker_page_title');
    add_filter('genesis_link_post_title','msdlab_speaker_title_unlink');
    print '<div class="banner clearfix '.$bannerclass.'"'.$background.'>';
    print '<div class="gradient">';
    print '<div class="wrap">';
    print '<div class="bannertext">';
    print genesis_do_post_title();
    print '</div>';
    print '</div>';
    print '</div>';
    print '</div>';
    remove_filter('genesis_link_post_title','msdlab_speaker_title_unlink');
    remove_filter('genesis_post_title_text','msdlab_speaker_page_title');
}
function msdlab_speaker_page_title($title){
    $title = single_term_title('',false);
    return $title;
}
function msdlab_speaker_title_unlink(){
    return false;
}

function msdlab_speaker_aggregate_wrapper_open(){
    print '<section class="speaker_aggregate clearfix">
<div class="wrap">';
}
function msdlab_speaker_aggregate_wrapper_close(){
    print '</div></section>';
}