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
    remove_action('genesis_entry_content','genesis_do_post_content', 10);
    add_action('genesis_entry_content','msdlab_maybe_do_news_excerpt', 10);
    add_action('genesis_entry_header', 'msdlab_maybe_do_featured_image', 8);
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
    global $post;
    if(has_term( 'highlighted-research', 'news_category', $post )){
        $attr['class'] .= ' col-xs-12 col-sm-12 col-md-8';
    } else {
        $attr['class'] .= ' col-xs-12 col-sm-6 col-md-4';
    }
    return $attr;
}

add_shortcode('media_runner','msdlab_news_media_runner');

function msdlab_news_media_runner($atts = array()){
    extract( shortcode_atts( array(
        'title' => 'Recent Videos',
        'count' => 12,
        'perslide' => 3,
        'link' => false,
        'cat' => false,
    ), $atts ) );
    global $post;
    $id = $post->post_name.'-runner';
        $args = array(
            'post_type' => 'news',
            'showposts' => $count,
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
        if($cat){
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'news_category',
                    'field'    => 'slug',
                    'terms'    => $cat,
                ),
            );
        }
        $recents = new WP_Query($args);
        if($recents->have_posts()) {
            if($link){
                $title = '<a href="'.$link.'">'.$title.'</a>';
            }
            $ret[] = '<section class="news_media_runner multi clearfix">
<h3 class="widgettitle widget-title">'.$title.'</h3>
<div class="carousel slide" id="'.$id.'">
    <div class="carousel-inner">';
//start loop
            $i = 0;
            $oembed_args = array(
                //'height'    => 200,
                'width'     => 350,

            );
            if(wp_is_mobile()){$perslide = 1;}

            while($recents->have_posts()) {
                $recents->the_post();
                $meta = get_post_meta($post->ID);
                //ts_data($meta);
                $item_class = array(
                    'item',
                );
                if($i==0){$item_class[] = 'active';}

                $url = get_post_meta($post->ID,'_news_videourl',true);
                //ts_data($url);
                $bkg = '';
                if($thumb = get_post_meta($post->ID, '_news_videothumb', true)){
                    $bkg = ' style="background-image:url('.$thumb.')"';
                }
                    $video_class = array(
                        'video',
                        'post-id-'.$post->ID,
                        'col-xs-12',
                        'col-sm-'.(12/$perslide),
                    );
                    if($i==0 || $i % $perslide == 0){
                        $ret[] =  '<div class="'.implode(' ',$item_class).'">';
                    }
                    $ret[] = '<div class="'.implode(' ',$video_class).'" src="'.$url.'"'.$bkg.'>';
                if($embedded_video = wp_oembed_get( $url, $oembed_args )) {
                    $ret[] =  $embedded_video;
                } else {
                    if(strstr($url,'/embed/') || strstr($url,'/standalone/') || strstr($url,'/viralplayer/')) {
                        $ret[] = '<h3 class="video-title">' . $post->post_title . '</h3>
                        <a href="' . $url . '" target="_blank" title="External Link" class="video-link"><iframe width="350" height="200" src="' . $url . '"></iframe></a>';
                    } elseif(strstr($url,'foxnews.com')){
                        preg_match('/\?(.*)/',$url,$video_params_matches);
                        $vps = explode('&',$video_params_matches[1]);
                        foreach($vps AS $vp){
                            $set = explode('=',$vp);
                            $video_param[$set[0]] = $set[1];
                        }
                        $ret[] = '<h3 class="video-title">' . $post->post_title . '</h3>
                        <a href="' . $url . '" target="_blank" title="External Link" class="video-link"><iframe width="'.$video_param['w'].'" height="'.$video_param['h'].'" src="https://video.foxnews.com/v/video-embed.html?video_id='.$video_param['id'].'"></iframe></a>';
                    } else {
                        $ret[] =  '<h3 class="video-title">'.$post->post_title.'</h3>
                        <a href="'.$url.'" target="_blank" title="External Link" class="video-link"><i class="fa fa-youtube-play"></i></a>';
                    }
                }
                    $ret[] =  '</div>';
                    if($i % $perslide == ($perslide - 1)){
                        $ret[] =  '</div>';
                    }
                    $i++;
            } //end loop
            $ret[] =  '</div>
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
    return implode("\n",$ret);
}

function msdlab_maybe_do_news_excerpt()
{
    global $post;
    if (has_term('highlighted-research', 'news_category', $post)) {
        the_excerpt();
    }
}

function msdlab_maybe_do_featured_image(){
    global $post;
    if (has_term('highlighted-research', 'news_category', $post) || has_term('members-in-the-news', 'news_category', $post)) {
        $img = genesis_get_image( array(
            'format'  => 'html',
            'size'    => 'full',
            'context' => 'archive',
            'attr'    => genesis_parse_attr( 'entry-image', array(
                'alt' => get_the_title(),
            ) ),
        ) );

        if ( ! empty( $img ) ) {

            genesis_markup( array(
                'open'    => '<a %s>',
                'close'   => '</a>',
                'content' => wp_make_content_images_responsive( $img ),
                'context' => 'entry-image-link',
            ) );

        } else {
            if(has_term('members-in-the-news', 'news_category', $post)){
                global $multimedia_info;
                $multimedia_info->the_meta($post->ID);
                $videourl = $multimedia_info->get_the_value('videourl');
                if(strlen($videourl) > 0){
                    $url = $videourl;
                    if($embedded_video = wp_oembed_get( $url, $oembed_args )) {
                        $ret[] =  $embedded_video;
                    } else {
                        if(strstr($url,'/embed/') || strstr($url,'/standalone/') || strstr($url,'/viralplayer/')) {
                            $ret[] = '<iframe width="350" height="200" src="' . $url . '"></iframe>';
                        } elseif(strstr($url,'foxnews.com')){
                            preg_match('/\?(.*)/',$url,$video_params_matches);
                            $vps = explode('&',$video_params_matches[1]);
                            foreach($vps AS $vp){
                                $set = explode('=',$vp);
                                $video_param[$set[0]] = $set[1];
                            }
                            $ret[] = '<iframe width="'.$video_param['w'].'" height="'.$video_param['h'].'" src="https://video.foxnews.com/v/video-embed.html?video_id='.$video_param['id'].'"></iframe>';
                        } else {
                            $ret[] =  '<a href="'.$url.'" target="_blank" title="External Link" class="video-link"><i class="fa fa-youtube-play"></i></a>';
                        }
                    }
                    print implode(' ', $ret);
                } else {
                    preg_match('/<iframe.*?>.*?<\/iframe>/i',$post->post_content,$matches);
                    $videourl = $matches[0];
                    print $videourl;
                }
            }
        }
    }
}

function msdlab_maybe_equalize_attr($attr){
    global $post;
    if (has_term('highlighted-research', 'news_category', $post)) {
        $$attr['class'] .= ' highlighted';
    } //else {
        $attr = msdlab_equalize_attr($attr);
    //}
    return $attr;
}

function msdlab_add_pub_name(){
    global $post;
    $articles = get_post_meta($post->ID,'_news_articles',true);
    if(isset($articles[0]['newspub'])){
        print '<div class="newspub"><em>'.$articles[0]['newspub'].'</em></div>';
    }
}