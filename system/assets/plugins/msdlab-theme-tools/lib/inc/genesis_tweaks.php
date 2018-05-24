<?php
class MSDLab_Genesis_Tweaks
{

    //Properties
    private $options;

    //Methods
    function __construct($options)
    {
        $defaults = array(
            'responsive' => true,
            'preheader' => 'genesis_before_header', //what to hook it to?
            'nav_extras' => array(),
        );

        $this->options = wp_parse_args($options, $defaults);

        if($this->options['responsive']) {
            add_theme_support('genesis-responsive-viewport');//* Add viewport meta tag for mobile browsers
        }
        if($this->options['preheader']){
            add_action($this->options['preheader'], array(&$this,'msdlab_pre_header'));
            add_action('msdlab_pre_header',array(&$this,'msdlab_pre_header_sidebar'), 15);
            add_action('after_setup_theme',array(&$this,'msdlab_add_preheader_sidebar'), 4);
        }
        add_filter( 'wp_nav_menu_items', array(&$this,'msdlab_nav_right'), 10, 2 );


    }

    /**
     * Add pre-header with social and search
     */
    function msdlab_pre_header(){
        print '<div class="pre-header">
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

    function msdlab_add_preheader_sidebar(){
        genesis_register_sidebar(array(
            'name' => 'Pre-header Sidebar',
            'description' => 'Widget above the logo/nav header',
            'id' => 'pre-header'
        ));
    }


    /**
     * Filter the Primary Navigation menu items, appending either RSS links, search form, twitter link, or today's date.
     *
     * @since 1.0.0
     *
     * @param string   $menu HTML string of list items.
     * @param stdClass $args Menu arguments.
     * @return string HTML string of list items with optional nav extras.
     *                Return early unmodified if first Genesis version is higher than 2.0.2.
     */
    function msdlab_nav_right( $menu, stdClass $args ) {
        // Only allow if using 2.0.2 or lower.
        if ( genesis_first_version_compare( '2.0.2', '>' ) ) {
            return $menu;
        }

        if ( 'primary' == $args->theme_location ) {
            return $menu;
        }

        switch ( $this->options['nav_extras'][$args->theme_location] ) {
            case 'rss':
                $rss   = '<a rel="nofollow" href="' . get_bloginfo( 'rss2_url' ) . '">' . __( 'Posts', 'genesis' ) . '</a>';
                $rss  .= '<a rel="nofollow" href="' . get_bloginfo( 'comments_rss2_url' ) . '">' . __( 'Comments', 'genesis' ) . '</a>';
                $menu .= '<li class="right rss">' . $rss . '</li>';
                break;
            case 'search':
                $menu  .= '<li class="right search">' . get_search_form( false ) . '</li>';
                break;
            case 'twitter':
                $menu .= sprintf( '<li class="right twitter"><a href="%s">%s</a></li>', esc_url( 'http://twitter.com/' . genesis_get_option( 'nav_extras_twitter_id' ) ), esc_html( genesis_get_option( 'nav_extras_twitter_text' ) ) );
                break;
            case 'date':
                $menu .= '<li class="right date">' . date_i18n( get_option( 'date_format' ) ) . '</li>';
                break;
        }

        return $menu;

    }


}
