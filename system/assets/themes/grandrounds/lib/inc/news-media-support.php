<?php

function msdlab_news_cleanup()
{
remove_action('genesis_before_loop', 'genesis_do_cpt_archive_title_description');
remove_action('genesis_before_loop', 'genesis_do_date_archive_title');
remove_action('genesis_before_loop', 'genesis_do_blog_template_heading');
remove_action('genesis_before_loop', 'genesis_do_posts_page_heading');
remove_action('genesis_before_loop', 'genesis_do_taxonomy_title_description', 15);
remove_action('genesis_before_loop', 'genesis_do_author_title_description', 15);
remove_action('genesis_before_loop', 'genesis_do_author_box_archive', 15);
}

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

function msdlab_news_entry_attr($attr){
$attr['class'] .= ' col-xs-12 col-sm-6 col-md-4';
return $attr;
}