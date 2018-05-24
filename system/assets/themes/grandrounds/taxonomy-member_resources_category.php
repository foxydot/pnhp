<?php
remove_all_actions('msdlab_title_area' );
remove_all_actions('genesis_entry_header');
remove_all_actions('genesis_entry_content');
remove_all_actions('genesis_entry_footer');
add_action('genesis_before_loop','msdlab_mr_challenge');
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
function msdlab_mr_challenge(){
    global $post,$wpalchemy_media_access,$member_resource_info;
    global $login_message;
    $obj = get_queried_object();
    $cat_slug = $obj->slug;

    switch($cat_slug){ // first check for cookie
        case "newsletter":;
        case "slideshow":
        case "webinar":
            //check for cookie
            ts_data($_COOKIE);
            if(!isset($_COOKIE['member_login']) || $_COOKIE['member_login'] != 'member'){
                //if cookie not exist, display input and die
                if($login_message){print '<div classs="alert">'.$login_message.'</div>';}
                msdlab_mr_login_form();
                return false;
            }
            //else, add the content to the entries
            add_action('genesis_entry_content','msdlab_mr_content');
        break;
        default:
            add_action('genesis_entry_content','msdlab_mr_content');
            break;
    }
}
function msdlab_mr_content(){
    global $post,$wpalchemy_media_access,$member_resource_info;
    $obj = get_queried_object();
    $cat_slug = $obj->slug;
    switch($cat_slug){
        case "newsletter":
            $member_resource_info->the_meta();
            while($member_resource_info->have_fields('memberresource'))
            $title = $member_resource_info->get_the_value('mr_title')!=''?$member_resource_info->get_the_value('mr_title'):get_the_title();
            print $title;
            break;
        case "slideshow":
            break;
        case "webinar":
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