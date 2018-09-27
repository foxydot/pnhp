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
    $size = 'headshot-sm'; // Change this to whatever add_image_size you want
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
    $region = get_the_term_list($post->ID,'speaker_region','<span class="state">Region(s): ',', ','</span>');
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
//add_action( 'wp_ajax_be_ajax_load_more', 'be_ajax_load_more' );
//add_action( 'wp_ajax_nopriv_be_ajax_load_more', 'be_ajax_load_more' );


add_action('wp_enqueue_scripts','msdlab_add_speaker_scripts',12);
function msdlab_add_speaker_scripts()
{
    if(msdlab_is_speaker_page() || is_cpt('speaker')) {
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
    add_filter('genesis_post_title_text','msdlab_speaker_link_to_bio');
    add_action('genesis_entry_header','msdlab_speaker_entry_hdr_img',8);
    add_action('genesis_entry_header','genesis_do_post_title',8);
    add_action('genesis_entry_header','msdlab_speaker_entry_hdr_cats');
    $regions = array('national','northeast','midwest','south','west');
    foreach($regions AS $reg) {
        $defaults = array(
            'post_type' => 'speaker',
            'posts-per-page' => -1,
            'orderby' => 'meta_value',
            'order' => 'ASC',
            'meta_key' => '_speaker_alpha',
            'tax_query' => array(
                array(
                    'taxonomy' => 'speaker_region',
                    'field'    => 'slug',
                    'terms'    => $reg,
                ),
            ),
        );
        $defaults['paged'] = get_query_var('paged') ? get_query_var('paged') : 1;
        $args = wp_parse_args($options, $defaults);
        //$ajax = $args['ajax'];
        unset($args['ajax']);
        $speakers = new WP_Query($args);
        // Pagination fix
        //$temp_query = $wp_query;
        //$wp_query   = NULL;
        //$wp_query   = $speakers;

        if ($speakers->have_posts()) {
            //if (!$ajax) {
                $speaker_region = get_term_by('slug',$reg,'speaker_region');
                $title = $speaker_region->name;
                print '<section class="speaker_aggregate clearfix">
<h2>'.$title.'</h2>
<div class="wrap" id="filterArea">';
            //}
            //start loop
            while ($speakers->have_posts()) {
                $speakers->the_post();
                get_template_part('speaker-aggregate-loop');
            } //end loop
            //if (!$ajax) {
                print '</div></section>';
            //}
            //the_posts_pagination();
        } //end loop check
    }


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

function msdlab_speaker_banner(){
    global $page_banner_metabox;
    //default to the image used for the main speaker bureau page
    $speakers_bureau_page = get_page_by_path('/about-pnhp/speaker-bureau/');
    $page_banner_metabox->the_meta($speakers_bureau_page->ID);
    $bannerimage = $page_banner_metabox->get_the_value('bannerimage');
    if(!$bannerimage){
        if(has_post_thumbnail()){
            $bannerimage = get_the_post_thumbnail_url();
        }
    }
    //check if we should look for a taxonomy banner
    if(is_archive()){
        $bannerclass = sanitize_title_with_dashes(single_term_title('',false));
        if(file_exists(get_stylesheet_directory().'/lib/images/banner-'.$taxonomy.'.png')) {
            $bannerimage = get_stylesheet_directory_uri() . '/lib/images/banner-' . $taxonomy . '.png';
        }
    }
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
    if(is_archive()){
    $title = single_term_title('',false);
    return $title;
}
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

    function msdlab_speaker_filter_tags(){
        $regions = array('northeast', 'midwest', 'south', 'west');
        $specialties = get_terms(array(
            'taxonomy' => 'speaker_specialty',
        ));
        $topic = get_terms(array(
            'taxonomy' => 'speaker_topic',
        ));
        print '<section class="speaker-filters">
<h3 class="widget-title">View speakers by:</h3>
        <select id="region-select" class="region">
        <option value="">All Regions</option>';
        foreach($regions AS $region){
            $r = $region;
            $rs[] = '<option value="/speaker-region/'.$r.'">'.ucwords($r).'</option>';
        }
        print implode('',$rs);
        print '</select>
        <select id="specialty-select" class="specialty">
        <option value="">All Specialties</option>';
        foreach($specialties AS $specialty){
            $s = $specialty->slug;
            $ss[] = '<option value="/speaker-specialty/'.$s.'">'.ucwords($s).'</option>';
        }
        print implode('',$ss);
        print '</select>
        <select id="topic-select" class="topic">
        <option value="">All Topics</option>';
        foreach($topics AS $topic){
            $t = $topic->slug;
            $ts[] = '<option value="/speaker-topic/'.$t.'">'.ucwords($t).'</option>';
        }
        print implode('',$ts);
        print '</select>
</section>';
    }

    function msdlab_speaker_link_to_bio($title_text){
        global $post;
        return '<a href="'.get_the_permalink($post).'" title="'.get_the_title($post).'">'.$title_text.'<span class="bio">BIO<i class="fa fa-arrow-circle-right"></i></span></a>';
    }