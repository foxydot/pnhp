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
    $attr['class'] .= ' col-xs-12 col-sm-6 col-md-4';
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
    print '<section class="speaker-headshot">';
    if ( has_post_thumbnail() ){
        printf( '<a href="%s" title="%s" >%s</a>', get_permalink(), the_title_attribute( 'echo=0' ), genesis_get_image( array( 'size' => $size, 'attr' => $default_attr ) ) );
    } else {
        printf( '<a href="%s" title="%s">%s</a>', get_permalink(), the_title_attribute( 'echo=0' ), genesis_get_image( array( 'size' => $size, 'attr' => $default_attr ) ) );
    }
    print '</section>';

}

function msdlab_speaker_entry_hdr_cats(){
    global $post;
    $regions = array();
    $regions['South'] = array(
        'Alabama',
        'Florida',
        'Kentucky',
        'Missouri',
        'Tennessee',
        'Texas',

    );
    $regions['West'] = array(

    );
    $regions['Midwest'] = array(

    );
    $regions['Northeast'] = array(

    );
    $states = get_the_terms($post->ID,'speaker_region');
    foreach($states AS $state){

    }
    $state = get_the_term_list($post->ID,'speaker_region','<span class="state">',', ','</span>');
    print($region);
    $specialty = get_the_term_list($post->ID,'speaker_specialty','<div class="specialty">Specialty: ',', ','</div>');
    print($specialty);
    $topic = get_the_term_list($post->ID,'speaker_topic','<div class="topic">Topic(s): ',', ','</div>');
    print($topic);
}