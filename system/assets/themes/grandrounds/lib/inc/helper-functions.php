<?php
/**
 * Determine if the div.site-inner should be wrapped
 */
function msdlab_maybe_wrap_inner(){
    global $do_wrap;

    $layout = genesis_site_layout();
    $template = get_page_template();
    switch($layout){
        case 'content-sidebar':
        case 'sidebar-content':
        case 'content-sidebar-sidebar':
        case 'sidebar-sidebar-content':
        case 'sidebar-content-sidebar':
            $do_wrap['site-inner'] = true;
            break;
        case 'full-width-content':
            $do_wrap['site-inner'] = false;
            break;
    }
}
/**
 * Customize search form input
 */
function msdlab_search_text($text) {
    $text = "Search";
    return $text;
}

/**
 * Customize search button text
 */
function msdlab_search_button($text) {
    $text = "&#xF002;";
    return $text;
}

/**
 * Add pre-header with social and search
 */
function msdlab_pre_header(){
    print '<div id="pre-header" class="pre-header">
        <div class="wrap">';
    do_action('msdlab_pre_header');
    print '
        </div>
    </div>';
}

function msdlab_pre_header_sidebar(){
    print '<div class="widget-area">';
    dynamic_sidebar( 'pre-header' );
    print '</div>';
}

/*** HEADER ***/
function msdlab_do_header() {
    genesis_markup( array(
        'html5'   => '<div %s>',
        'xhtml'   => '<div id="title-area">',
        'context' => 'title-area',
    ) );
    do_action( 'genesis_site_title' );
    do_action( 'genesis_site_description' );
    echo '</div>';
}

function msdlab_header_right(){
    global $wp_registered_sidebars;

    if ( ( isset( $wp_registered_sidebars['header-right'] ) && is_active_sidebar( 'header-right' ) ) || has_action( 'genesis_header_right' ) ) {
        genesis_markup( array(
            'html5'   => '<aside %s>',
            'xhtml'   => '<div class="widget-area header-widget-area">',
            'context' => 'header-widget-area',
        ) );

        do_action( 'genesis_header_right' );
        add_filter( 'wp_nav_menu_args', 'genesis_header_menu_args' );
        add_filter( 'wp_nav_menu', 'genesis_header_menu_wrap' );
        dynamic_sidebar( 'header-right' );
        remove_filter( 'wp_nav_menu_args', 'genesis_header_menu_args' );
        remove_filter( 'wp_nav_menu', 'genesis_header_menu_wrap' );

        genesis_markup( array(
            'html5' => '</aside>',
            'xhtml' => '</div>',
        ) );
    }
}

/*** NAV ***/
function msdlab_do_nav() {

    //* Do nothing if menu not supported
    if ( ! genesis_nav_menu_supported( 'primary' ) )
        return;

    $class = 'menu genesis-nav-menu menu-primary';
    if ( genesis_superfish_enabled() ) {
        $class .= ' js-superfish';
    }

    genesis_nav_menu( array(
        'theme_location' => 'primary',
        'menu_class'     => $class,
    ) );

}

/*** SIDEBARS ***/
/**
 * This ensures that the primary sidebar is always to the left.
 */
function msdlab_sb_layout_logic() {
    $site_layout = genesis_site_layout();
    if ( $site_layout == 'sidebar-content-sidebar' ) {
        // Remove default genesis sidebars
        remove_action( 'genesis_after_content', 'genesis_get_sidebar' );
        remove_action( 'genesis_after_content_sidebar_wrap', 'genesis_get_sidebar_alt');
        // Add layout specific sidebars
        add_action( 'genesis_before_content_sidebar_wrap', 'genesis_get_sidebar' );
        add_action( 'genesis_after_content', 'genesis_get_sidebar_alt');
    }
}

function msdlab_add_extra_theme_sidebars(){
    //* Remove the header right widget area
    //unregister_sidebar( 'header-right' );
    genesis_register_sidebar(array(
        'name' => 'Pre-header Sidebar',
        'description' => 'Widget above the logo/nav header',
        'id' => 'pre-header'
    ));
    genesis_register_sidebar(array(
        'name' => 'Blog Sidebar',
        'description' => 'Widgets on the Blog Pages',
        'id' => 'blog'
    ));
}

/**
 * Customize Breadcrumb output
 */
function msdlab_breadcrumb_args($args) {
    $args['home'] = '';
    $args['labels']['prefix'] = ''; //marks the spot
    $args['sep'] = ' / ';
    return $args;
}

function msdlab_post_info_filter($post_info) {
    $post_info = 'Posted [post_date]';
    return $post_info;
}

/**
 * Move titles
 */

function msdlab_maybe_move_title(){
    global $post;
    $template_file = get_post_meta($post->ID,'_wp_page_template',TRUE);
    if(is_page() && $template_file=='default'){
        remove_action('genesis_entry_header','genesis_do_post_title'); //move the title out of the content area
        add_action('msdlab_title_area','msdlab_do_section_title');
        add_action('genesis_after_header','msdlab_do_title_area');
    }
}
function msdlab_do_section_title(){
    if(is_front_page()){
    } elseif(is_page()){
        global $post;
        $myid = $post->ID;
        $lvl = 2;
        if(get_section_title()!=$post->post_title){
            add_action('genesis_entry_header','genesis_do_post_title',5);
            $lvl = 2;
        }
        $background = strlen(msdlab_get_thumbnail_url($myid,'full'))>0?' style="background-image:url('.msdlab_get_thumbnail_url($myid,'full').')"':'';
        print '<div class="banner clearfix"'.$background.'>';
        print '<div class="texturize">';
        print '<div class="gradient">';
        print '<div class="wrap">';
        print '<h'.$lvl.' class="section-title">';
        print get_section_title();
        print '</h'.$lvl.'>';
        print '</div>';
        print '</div>';
        print '</div>';
        print '</div>';
    } elseif(is_home() || is_single()) {
        $blog_home = get_post(get_option( 'page_for_posts' ));
        $title = apply_filters( 'genesis_post_title_text', $blog_home->post_title );//* Wrap in H1 on singular pages
        $background = strlen(msdlab_get_thumbnail_url($myid,'full'))>0?' style="background-image:url('.msdlab_get_thumbnail_url($blog_home->ID,'full').')"':'';
        print '<div class="banner clearfix"'.$background.'>';
        print '<div class="texturize">';
        print '<div class="gradient">';
        print '<div class="wrap">';
        print '<h2 class="section-title">';
        print $title;
        print '</h2>';
        print '</div>';
        print '</div>';
        print '</div>';
        print '</div>';
    } else {
        genesis_do_post_title();
    }
}

function msdlab_do_title_area(){
    global $post;
    $postid = is_admin()?$_GET['post']:$post->ID;
    $template_file = get_post_meta($postid,'_wp_page_template',TRUE);
    if ($template_file == 'page-sectioned.php') {
        print '<div id="page-title-area" class="page-title-area">';
        do_action('msdlab_title_area');
        print '</div>';
    } else {
        print '<div id="page-title-area" class="page-title-area">';
        do_action('msdlab_title_area');
        print '</div>';
    }
}

/**
 * Manipulate the featured image
 */
function msd_post_image() {
    global $post;
    //setup thumbnail image args to be used with genesis_get_image();
    $size = 'post-image'; // Change this to whatever add_image_size you want
    $default_attr = array(
        'class' => "attachment-$size $size",
        'alt'   => $post->post_title,
        'title' => $post->post_title,
    );

    // This is the most important part!  Checks to see if the post has a Post Thumbnail assigned to it. You can delete the if conditional if you want and assume that there will always be a thumbnail
    if ( has_post_thumbnail() && is_page() ) {
        msdlab_page_banner();
    } elseif ( has_post_thumbnail() && is_cpt('project') ) {
        if( is_single() ){
            msdlab_page_banner();
        }
    } elseif ( has_post_thumbnail() ){
        print '<section class="header-image">';
        printf( '<a title="%s" href="%s">%s</a>', get_permalink(), the_title_attribute( 'echo=0' ), genesis_get_image( array( 'size' => $size, 'attr' => $default_attr ) ) );
        print '</section>';
    }

}

function msdlab_page_banner(){
    if(is_front_page())
        return;
    global $post;
    $featured_image = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'page_banner' );
    $background = $featured_image[0];
    $ret = '<div class="banner clearfix" style="background-image:url('.$background.')"></div>';
    print $ret;
}

function msdlab_get_thumbnail_url($post_id = null, $size = 'post-thumbnail'){
    global $post;
    if(!$post_id)
        $post_id = $post->ID;
    $featured_image = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), $size );
    $url = $featured_image[0];
    return $url;
}

/**
 * Previous next links
 */
function msdlab_read_more_link() {
    return '<a class="more-link nobr" href="' . get_permalink() . '">&hellip;read more</a>';
}

function msdlab_older_link_text($content) {
    $olderlink = 'Older Posts &raquo;';
    return $olderlink;
}

function msdlab_newer_link_text($content) {
    $newerlink = '&laquo; Newer Posts';
    return $newerlink;
}

/**
 * Display links to previous and next post, from a single post.
 *
 * @since 1.5.1
 *
 * @return null Return early if not a post.
 */
function msdlab_prev_next_post_nav() {
    if ( ! is_singular() || is_page() )
        return;

    $in_same_term = false;
    $excluded_terms = false;
    $previous_post_link = get_previous_post_link('&laquo; %link', '%title', $in_same_term, $excluded_terms, 'category');
    $next_post_link = get_next_post_link('%link &raquo;', '%title', $in_same_term, $excluded_terms, 'category');
    if(is_cpt('project')){
        $taxonomy = 'project_type';
        $prev_post = get_adjacent_post( $in_same_term, $excluded_terms, true, $taxonomy );
        $next_post = get_adjacent_post( $in_same_term, $excluded_terms, false, $taxonomy );
        $size = 'nav-post-thumb';
        $previous_post_link = $prev_post?'<a href="'.get_post_permalink($prev_post->ID).'" style="background-image:url('.msdlab_get_thumbnail_url($prev_post->ID, $size).'")><span class="nav-title"><i class="fa fa-angle-double-left"></i> '.$prev_post->post_title.'</span></a>':'<div href="'.get_post_permalink($post->ID).'" style="opacity: 0.5;background-image:url('.msdlab_get_thumbnail_url($post->ID, $size).'")><span class="nav-title">You are at the beginning of the portfolio.</span></div>';
        $next_post_link = $next_post?'<a href="'.get_post_permalink($next_post->ID).'" style="background-image:url('.msdlab_get_thumbnail_url($next_post->ID, $size).'")><span class="nav-title">'.$next_post->post_title.' <i class="fa fa-angle-double-right"></i></span></a>':'<div href="'.get_post_permalink($post->ID).'" style="opacity: 0.5;background-image:url('.msdlab_get_thumbnail_url($post->ID, $size).'")><span class="nav-title">You are at the end of the portfolio.</span></div>';

    }

    genesis_markup( array(
        'html5'   => '<div %s>',
        'xhtml'   => '<div class="navigation">',
        'context' => 'adjacent-entry-pagination',
    ) );



    echo '<div class="pagination-previous pull-left col-xs-6">';
    echo $previous_post_link;
    echo '</div>';

    echo '<div class="pagination-next pull-right col-xs-6">';
    echo $next_post_link;
    echo '</div>';

    echo '</div>';

}

/*** FOOTER ***/

/**
 * Menu area for footer menus
 */
register_nav_menus( array(
    'footer_menu' => 'Footer Menu'
) );
function msdlab_do_footer_menu(){
    if(has_nav_menu('footer_menu')){$footer_menu = wp_nav_menu( array( 'theme_location' => 'footer_menu','container_class' => 'ftr-menu ftr-links','echo' => FALSE, 'walker' => new Description_Walker ) );}
    print '<div id="footer_menu" class="footer-menu"><div class="wrap">'.$footer_menu.'</div></div>';
}


function msdlab_do_footer_widget(){
    print '<div id="page_footer_widget" class="page-footer-widget">';
    if(is_active_sidebar('msdlab_page_footer')){
        dynamic_sidebar('msdlab_page_footer');
    }
    print '</div>';
}

/**
 * Footer replacement with MSDSocial support
 */
function msdlab_do_social_footer(){
    global $msd_social;

    if(has_nav_menu('footer_menu')){$footer_menu .= wp_nav_menu( array( 'theme_location' => 'footer_menu','container_class' => 'menu genesis-nav-menu nav-footer','echo' => FALSE ) );}

    if($msd_social && get_option('msdsocial_street')!=''){
        $address = '<span itemprop="streetAddress">'.get_option('msdsocial_street').'</span>, <span itemprop="addressLocality">'.get_option('msdsocial_city').'</span>, <span itemprop="addressRegion">'.get_option('msdsocial_state').'</span> <span itemprop="postalCode">'.get_option('msdsocial_zip').'</span> '.$msd_social->get_digits(true,'').' &middot; <a href="mailto:'.antispambot(get_option('msdsocial_email')).'">'.antispambot(get_option('msdsocial_email')).'</a>';
        $copyright .= '&copy;'.date('Y').' '.$msd_social->get_bizname().', ';
    } else {
        $copyright .= '&copy;'.date('Y').' '.get_bloginfo('name').'';
    }
    print '<div class="row">';
    //print '<nav class="footer-menu" itemtype="http://schema.org/SiteNavigationElement" itemscope="itemscope" role="navigation">'.$footer_menu.'</nav>';
    //print '<div class="social">'.$msd_social->social_media().'</div>';
    print '<div class="legal">'.$copyright.'</div> <div class="address">'.$address.'</div> <nav class="footer-menu" itemtype="http://schema.org/SiteNavigationElement" itemscope="itemscope" role="navigation">'.$footer_menu.'</nav>';
    print '</div>';
    //print '<div class="backtotop"><a href="#pre-header"><i class="fa fa-angle-up"></i></a></div>';
}


/*** SITEMAP ***/
function msdlab_sitemap(){
    $col1 = '
            <h4>'. __( 'Pages:', 'genesis' ) .'</h4>
            <ul>
                '. wp_list_pages( 'echo=0&title_li=' ) .'
            </ul>

            <h4>'. __( 'Categories:', 'genesis' ) .'</h4>
            <ul>
                '. wp_list_categories( 'echo=0&sort_column=name&title_li=' ) .'
            </ul>
            ';

    foreach( get_post_types( array('public' => true) ) as $post_type ) {
        if ( in_array( $post_type, array('post','page','attachment') ) )
            continue;

        $pt = get_post_type_object( $post_type );

        $col2 .= '<h4>'.$pt->labels->name.'</h4>';
        $col2 .= '<ul>';

        query_posts('post_type='.$post_type.'&posts_per_page=-1');
        while( have_posts() ) {
            the_post();
            if($post_type=='news'){
                $col2 .= '<li><a href="'.get_permalink().'">'.get_the_title().' '.get_the_content().'</a></li>';
            } else {
                $col2 .= '<li><a href="'.get_permalink().'">'.get_the_title().'</a></li>';
            }
        }
        wp_reset_query();

        $col2 .= '</ul>';
    }

    $col3 = '<h4>'. __( 'Blog Monthly:', 'genesis' ) .'</h4>
            <ul>
                '. wp_get_archives( 'echo=0&type=monthly' ) .'
            </ul>

            <h4>'. __( 'Recent Posts:', 'genesis' ) .'</h4>
            <ul>
                '. wp_get_archives( 'echo=0&type=postbypost&limit=20' ) .'
            </ul>
            ';
    $ret = '<div class="row">
       <div class="col-md-4 col-sm-12">'.$col1.'</div>
       <div class="col-md-4 col-sm-12">'.$col2.'</div>
       <div class="col-md-4 col-sm-12">'.$col3.'</div>
    </div>';
    print $ret;
}


function msdlab_equalize_attr($attr){
    $attr['class'] .= ' equalize';
    return $attr;
}

function msdlab_add_content_modal(){
    print '<div class="content_modal modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"></h4>
      </div>
      <div class="modal-body">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->';
}

function msdlab_do_parent_sidebar(){
    global $wp_query, $post;
    switch($post->post_type){
        case 'speaker':
            //set parent "page"
            $parent_page = get_page_by_path('/about-pnhp/speaker-bureau/');
            $args['page_id'] = $parent_page->ID;
            break;
        case 'page':
        case 'post':
        default:
            return;
    }
    if(count($args)>0) {
        $parent_query = new WP_Query($args);
    }
    //store main post, switch main post to parent, print out menu, switch back
    $orig_query = $wp_query;
    $wp_query = $parent_query;
    dynamic_sidebar( 'sidebar' );
    $wp_query = $orig_query;
}