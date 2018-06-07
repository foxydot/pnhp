<?php
remove_all_actions('msdlab_title_area' );
remove_all_actions('genesis_entry_header');
remove_all_actions('genesis_entry_content');
remove_all_actions('genesis_entry_footer');
add_action('genesis_loop','msdlab_mr_info',8);
add_action('genesis_loop','msdlab_mr_challenge',9);
add_action('msdlab_title_area','msdlab_news_cleanup');
add_action('msdlab_title_area','msdlab_mr_category_banner');
function msdlab_mr_category_banner(){
    $bannerclass = sanitize_title_with_dashes(single_term_title('',false));
    $bannerimage = get_stylesheet_directory_uri().'/lib/images/banner-mr-category-'.$bannerclass.'.jpg';
    $background = strlen($bannerimage)>0?' style="background-image:url('.$bannerimage.')"':'';
    add_filter('genesis_post_title_text','msdlab_mr_page_title');
    add_filter('genesis_link_post_title','msdlab_mr_title_unlink');
    print '<div class="banner clearfix '.$bannerclass.'"'.$background.'>';
    print '<div class="gradient">';
    print '<div class="wrap">';
    print '<div class="bannertext">';
    print genesis_do_post_title();
    print '</div>';
    print '</div>';
    print '</div>';
    print '</div>';
    remove_filter('genesis_link_post_title','msdlab_mr_title_unlink');
    remove_filter('genesis_post_title_text','msdlab_mr_page_title');
}
function msdlab_mr_page_title($title){
    $title = single_term_title('',false);
    return $title;
}
function msdlab_mr_title_unlink(){
    return false;
}
function msdlab_mr_info(){
    $term = get_queried_object();
    $headline = apply_filters('the_title',get_term_meta( $term->term_id, 'headline', true ));
    $intro_text = apply_filters('the_content',get_term_meta( $term->term_id, 'intro_text', true ));
    if(strlen($headline) > 0){
        $ret[] = '<h3 class="archive-headline">'.$headline.'</h3>';
    }
    if(strlen($intro_text) > 0){
        $ret[] = '<div class="archive-intro-text">'.$intro_text.'</div>';
    }
    if(count($ret) > 0)
    print '<div class="member_resources_category_header">'.implode("/n",$ret).'</div>';
}
function msdlab_mr_challenge(){
    global $post,$wpalchemy_media_access,$member_resource_info;
    global $login_message;
    $obj = get_queried_object();
    $cat_slug = $obj->slug;

    switch($cat_slug) { // first check for cookie
        case "newsletter":
        case "slideshows":
            //check for cookie
            if(!is_user_logged_in()) {
                if (!isset($_COOKIE['member_login']) || $_COOKIE['member_login'] != 'member') {
                    //if cookie not exist, display input and die
                    if ($login_message) {
                        print '<div classs="alert">' . $login_message . '</div>';
                    }
                    msdlab_mr_login_form();
                    return false;
                }
            }
            //else, add the content to the entries
            add_action('genesis_entry_content', 'msdlab_mr_content');
            break;
        case "webinars":
        default:
            add_action('genesis_entry_content', 'msdlab_mr_content');
            break;
    }
}
function msdlab_mr_content(){
    global $post,$wpalchemy_media_access,$member_resource_info;
    $obj = get_queried_object();
    $cat_slug = $obj->slug;
    //get all the stuff
    $ctr = 0;
    $member_resource_info->the_meta();
    while ($member_resource_info->have_fields('memberresource')) {
        $mr[$ctr]['title'] = $member_resource_info->get_the_value('mr_title') != '' ? $member_resource_info->get_the_value('mr_title') : get_the_title();
        $mr[$ctr]['file'] = $member_resource_info->get_the_value('mr_file') != '' ? $member_resource_info->get_the_value('mr_file') : false;
        $mr[$ctr]['author'] = $member_resource_info->get_the_value('mr_author') != '' ? $member_resource_info->get_the_value('mr_author') : false;
        $mr[$ctr]['date'] = $member_resource_info->get_the_value('mr_date') != '' ? $member_resource_info->get_the_value('mr_date') : get_the_date();
        $mr[$ctr]['tease'] = $member_resource_info->get_the_value('mr_tease') != '' ? apply_filters('the_content',$member_resource_info->get_the_value('mr_tease')) : false;
        $ctr++;
    }
    switch($cat_slug) {
        case "newsletter":
            foreach($mr AS $ctr => $r){
                if($r['file']){
                    print '<h3 class="member-resource-title"><a href="'.$r['file'].'">'.$r['title'].'</a></h3>';
                } else {
                    print '<h3 class="member-resource-title">'.$r['title'].'</h3>';
                }
                if($r['tease']){
                    print '<div>';
                    print '<a class="collapse-btn" data-toggle="collapse" href="#collapse-'.$post->ID.'-'.$ctr.'" role="button" aria-expanded="false" aria-controls="collapse-'.$post->ID.'-'.$ctr.'">In this issue <i class="fa fa-angle-down"><span class="screen-reader-text">expand</span></i></a>';
                    print '<div class="member-resource-teaser collapse" id="collapse-'.$post->ID.'-'.$ctr.'">'.$r['tease'].'</div>';
                    print '</div>';
                }
                if($r['file']){
                    print '<a class="btn btn-primary" href="'.$r['file'].'">Download PDF <i class="fa fa-file-pdf-o"></i></a>';
                }
            }
            break;
        case "slideshows":
            print '<h3>'.get_the_title().'</h3>';
            if(strlen(get_the_content())>0){
                print '<div class="entry-content">'.apply_filters('the_content',get_the_content()).'</div>';
            }
            print '<div class="row">';
            foreach($mr AS $ctr => $r){
                print '<div class="slide_resource_wrapper col-xs-12 col-sm-6 col-md-4">';
                if($r['file']){
                    print '<h4 class="member-resource-title"><a href="'.$r['file'].'">'.$r['title'].'</a></h4>';
                } else {
                    print '<h4 class="member-resource-title">'.$r['title'].'</h4>';
                }
                if($r['tease']){
                    print '<div>';
                    print '<div class="member-resource-teaser">'.$r['tease'].'</div>';
                    print '</div>';
                }
                if($r['file']){
                    print '<a class="btn btn-primary" href="'.$r['file'].'">Download <i class="fa fa-file-powerpoint-o"></i></a>';
                }
                print '</div>';
            }
            print '</div>';
            break;
        case "webinars":
            print '<div class="row">';
            foreach($mr AS $ctr => $r){
                print '<div class="webinar-resource_wrapper col-xs-12 col-sm-6 col-md-4">';
                if($r['file'])
                if($r['file']){
                    print '<h4 class="member-resource-title"><a href="'.$r['file'].'">'.$r['title'].'</a></h4>';
                } else {
                    print '<h4 class="member-resource-title">'.$r['title'].'</h4>';
                }
                if($r['tease']){
                    print '<div>';
                    print '<div class="member-resource-teaser">'.$r['tease'].'</div>';
                    print '</div>';
                }
                if($r['file']){
                    print '<a class="btn btn-primary" href="'.$r['file'].'">Download <i class="fa fa-file-powerpoint-o"></i></a>';
                }
                print '</div>';
            }
            print '</div>';
            break;
    }
}

function msdlab_mr_login_form(){
    ?>
    <form id="member_login_form" method="post">
        <label for="member_key">Login to continue</label>
        <input id="member_key" name="member_key" type="password" />
        <input type="submit" />
    </form>
<?php
}
genesis();