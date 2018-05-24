<?php

add_action('init','msdlab_mr_cookie');

function msdlab_mr_cookie(){
    global $login_message;
    //check password against md5
    if(isset($_POST['member_key'])){
        $path = '/';
        $host = parse_url(get_option('siteurl'), PHP_URL_HOST);
        $expiry = strtotime('+1 month');
        $member_key = md5($_POST['member_key']);
        $key = 'ccbee73cd81c7f42405e1920409247ec';
        if($member_key === $key){
            //set cookie
            setcookie( 'member_login', 'member', $expiry, $path, $host );
        } else {
            $login_message = "Password did not match.";
        }
    }
    return;
}
function msdlab_mr_cleanup()
{
    remove_action('genesis_before_loop', 'genesis_do_cpt_archive_title_description');
    remove_action('genesis_before_loop', 'genesis_do_date_archive_title');
    remove_action('genesis_before_loop', 'genesis_do_blog_template_heading');
    remove_action('genesis_before_loop', 'genesis_do_posts_page_heading');
    remove_action('genesis_before_loop', 'genesis_do_taxonomy_title_description', 15);
    remove_action('genesis_before_loop', 'genesis_do_author_title_description', 15);
    remove_action('genesis_before_loop', 'genesis_do_author_box_archive', 15);
    remove_action('genesis_entry_content','genesis_do_post_content', 10);
    add_action('genesis_entry_header', 'msdlab_maybe_do_featured_image', 8);
}


function msdlab_mr_entry_attr($attr){
    global $post;
    if(has_term( 'highlighted-research', 'member_resources_category', $post )){
        $attr['class'] .= ' col-xs-12 col-sm-12 col-md-8';
    } else {
        $attr['class'] .= ' col-xs-12 col-sm-6 col-md-4';
    }
    return $attr;
}
