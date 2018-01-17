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

function msdlab_news_media_runner(){
    global $post;
    $id = $post->post_name.'-runner';
        $args = array(
            'post_type' => 'news',
            'showposts' => 12,
            'meta_query' => array(
                'relation' => 'AND',
                'url' => array(
                    'key' => '_news_videourl',
                    'value' => 'http',
                    'compare' => 'LIKE'
                ),
                'tick' => array(
                    'key' => '_news_hasvideo',
                    'compare' => 'EXISTS',
                ),
            ),
            'order_by' => 'post_date',
            'order' => 'DESC',
        );
        $recents = new WP_Query($args);
        if($recents->have_posts()) {
            print '<section class="news_media_runner multi clearfix">
<h3 class="widgettitle widget-title">Recent Videos </h3>
<div class="carousel slide" id="'.$id.'">
    <div class="carousel-inner">';
//start loop
            $i = 0;
            $oembed_args = array(
                'height'    => 140,
                'width'     => 240,

            );
            while($recents->have_posts()) {
                $recents->the_post();
                $item_class = array(
                    'item',
                );
                if($i==0){$item_class[] = 'active';}

                $url = get_post_meta($post->ID,'_news_videourl',true);

                    $video_class = array(
                        'video',
                        'post-id-'.$post->ID,
                        'col-xs-3'
                    );
                    if($i==0 || $i % 4 == 0){
                        print '<div class="'.implode(' ',$item_class).'">';
                    }
                    print '<div class="'.implode(' ',$video_class).'" src="'.$url.'">';
                if($embedded_video = wp_oembed_get( $url, $oembed_args )) {
                    print $embedded_video;
                } else {
                    print '<a href="'.$url.'" target="_blank" title="External Link"><div class="video-title">'.$post->post_title.'</div><i class="fa fa-youtube-play"></i></a>';
                }
                    print '</div>';
                    if($i % 4 == 3){
                        print '</div>';
                    }
                    $i++;
            } //end loop
            print '</div>
      <a class="left carousel-control" href="#'.$id.'" data-slide="prev"><i class="glyphicon glyphicon-chevron-left"></i></a>
      <a class="right carousel-control" href="#'.$id.'" data-slide="next"><i class="glyphicon glyphicon-chevron-right"></i></a>
    </div>
</section>
<script>
jQuery(document).ready(function($) {
    $(\'#'.$id.'\').carousel({pause: true,
interval: false});
});
</script>
';
        } //end loop check
}
