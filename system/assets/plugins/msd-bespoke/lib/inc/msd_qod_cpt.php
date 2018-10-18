<?php
if (!class_exists('MSDQODCPT')) {
    class MSDQODCPT {
        //Properties
        var $cpt = 'qod';
        //Methods
        /**
         * PHP 4 Compatible Constructor
         */
        public function MSDQODCPT(){$this->__construct();}

        /**
         * PHP 5 Constructor
         */
        function __construct(){
            global $current_screen;
            //"Constants" setup
            $this->plugin_url = plugin_dir_url('msd-custom-cpt/msd-custom-cpt.php');
            $this->plugin_path = plugin_dir_path('msd-custom-cpt/msd-custom-cpt.php');
            //Actions
            add_action( 'init', array(&$this,'register_taxonomies') );
            add_action( 'init', array(&$this,'register_cpt') );
            add_action( 'init', array(&$this,'register_metaboxes') );
            //add_action('admin_head', array(&$this,'plugin_header'));
            add_action('admin_print_scripts', array(&$this,'add_admin_scripts') );
            add_action('admin_print_styles', array(&$this,'add_admin_styles') );
            add_action('admin_footer',array(&$this,'info_footer_hook') );
            // important: note the priority of 99, the js needs to be placed after tinymce loads
            add_action('admin_print_footer_scripts',array(&$this,'print_footer_scripts'),99);
            add_action('template_redirect', array(&$this,'my_theme_redirect'));
            add_action('admin_head', array(&$this,'codex_custom_help_tab'));

            //Filters
            //add_filter( 'pre_get_posts', array(&$this,'custom_query') );
            add_filter( 'enter_title_here', array(&$this,'change_default_title') );
        }


        function register_taxonomies(){

            $labels = array(
                'name' => _x( 'Quote categories', 'qod-category' ),
                'singular_name' => _x( 'Quote category', 'qod-category' ),
                'search_items' => _x( 'Search quote categories', 'qod-category' ),
                'popular_items' => _x( 'Popular quote categories', 'qod-category' ),
                'all_items' => _x( 'All quote categories', 'qod-category' ),
                'parent_item' => _x( 'Parent quote category', 'qod-category' ),
                'parent_item_colon' => _x( 'Parent quote category:', 'qod-category' ),
                'edit_item' => _x( 'Edit quote category', 'qod-category' ),
                'update_item' => _x( 'Update quote category', 'qod-category' ),
                'add_new_item' => _x( 'Add new quote category', 'qod-category' ),
                'new_item_name' => _x( 'New quote category name', 'qod-category' ),
                'separate_items_with_commas' => _x( 'Separate quote categories with commas', 'qod-category' ),
                'add_or_remove_items' => _x( 'Add or remove quote categories', 'qod-category' ),
                'choose_from_most_used' => _x( 'Choose from the most used quote categories', 'qod-category' ),
                'menu_name' => _x( 'Quote categories', 'qod-category' ),
            );

            $args = array(
                'labels' => $labels,
                'public' => true,
                'show_in_nav_menus' => true,
                'show_ui' => true,
                'show_tagcloud' => false,
                'hierarchical' => true, //we want a "category" style taxonomy, but may have to restrict selection via a dropdown or something.

                'rewrite' => array('slug'=>'qod-category','with_front'=>false),
                'query_var' => true
            );

            register_taxonomy( 'qod_category', array($this->cpt), $args );
        }

        function register_cpt() {

            $labels = array(
                'name' => _x( 'Quote of the Day', 'qod' ),
                'singular_name' => _x( 'Quote of the Day', 'qod' ),
                'add_new' => _x( 'Add New', 'qod' ),
                'add_new_item' => _x( 'Add New Quote', 'qod' ),
                'edit_item' => _x( 'Edit Quote', 'qod' ),
                'new_item' => _x( 'New Quote', 'qod' ),
                'view_item' => _x( 'View Quote', 'qod' ),
                'search_items' => _x( 'Search Quote', 'qod' ),
                'not_found' => _x( 'No quotes found', 'qod' ),
                'not_found_in_trash' => _x( 'No quotes found in Trash', 'qod' ),
                'parent_item_colon' => _x( 'Parent Quote:', 'qod' ),
                'menu_name' => _x( 'Quote', 'qod' ),
            );

            $args = array(
                'labels' => $labels,
                'hierarchical' => false,
                'description' => 'Quote of the Day',
                'supports' => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail' ),
                'taxonomies' => array( 'qod_category' ),
                'public' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'menu_position' => 20,

                'show_in_nav_menus' => true,
                'publicly_queryable' => true,
                'exclude_from_search' => true,
                'has_archive' => true,
                'query_var' => true,
                'can_export' => true,
                'rewrite' => array('slug'=>'qod','with_front'=>false),
                'capability_type' => 'post',
                'menu_icon' => 'dashicons-format-quote',
            );

            register_post_type( $this->cpt, $args );
        }


        function register_metaboxes(){
            global $qod_info;
            $qod_info = new WPAlchemy_MetaBox(array
            (
                'id' => '_qod_information',
                'title' => 'Quote of the Day Info',
                'types' => array($this->cpt),
                'context' => 'normal',
                'priority' => 'high',
                'template' => plugin_dir_path(dirname(__FILE__)).'/template/metabox-qod.php',
                'autosave' => TRUE,
                'mode' => WPALCHEMY_MODE_EXTRACT, // defaults to WPALCHEMY_MODE_ARRAY
                'prefix' => '_qod_' // defaults to NULL
            ));
        }


        function add_admin_scripts() {
            global $current_screen;
            if($current_screen->post_type == $this->cpt){
            }
        }

        function add_admin_styles() {
            global $current_screen;
            if($current_screen->post_type == $this->cpt){
                wp_enqueue_style('custom_meta_css',plugin_dir_url(dirname(__FILE__)).'/css/meta.css');
            }
        }

        function print_footer_scripts()
        {
            global $current_screen;
            if($current_screen->post_type == $this->cpt){
                print '<script type="text/javascript">/* <![CDATA[ */
					jQuery(function($)
					{
						var i=1;
						$(\'.customEditor textarea\').each(function(e)
						{
							var id = $(this).attr(\'id\');
			 
							if (!id)
							{
								id = \'customEditor-\' + i++;
								$(this).attr(\'id\',id);
							}
			 
							tinyMCE.execCommand(\'mceAddControl\', false, id);
			 
						});
					});
				/* ]]> */</script>';
            }
        }

        function info_footer_hook()
        {
            global $current_screen;
            if($current_screen->post_type == $this->cpt){
                ?><script type="text/javascript">
                    jQuery('#postdivrich').before(jQuery('#_contact_info_metabox'));
                </script><?php
            }
        }


        function my_theme_redirect() {
            global $wp;

            //A Specific Custom Post Type
            if ($wp->query_vars["post_type"] == $this->cpt) {
                if(is_single()){
                $templatefilename = 'single-'.$this->cpt.'.php';
                if (file_exists(STYLESHEETPATH . '/' . $templatefilename)) {
                    $return_template = STYLESHEETPATH . '/' . $templatefilename;
                } else {
                    $return_template = plugin_dir_path(dirname(__FILE__)). 'template/' . $templatefilename;
                }
                do_theme_redirect($return_template);

                //A Custom Taxonomy Page
                } elseif ($wp->query_vars["taxonomy"] == 'qod_category') {
                    $templatefilename = 'taxonomy-qod_category.php';
                    if (file_exists(STYLESHEETPATH . '/' . $templatefilename)) {
                        $return_template = STYLESHEETPATH . '/' . $templatefilename;
                    } else {
                        $return_template = plugin_dir_path(dirname(__FILE__)) . 'template/' . $templatefilename;
                    }
                    do_theme_redirect($return_template);
                }
            }
        }

        function codex_custom_help_tab() {
            global $current_screen;
            if($current_screen->post_type != $this->cpt)
                return;

            // Setup help tab args.
            $args = array(
                'id'      => 'title', //unique id for the tab
                'title'   => 'Title', //unique visible title for the tab
                'content' => '<h3>The Event Title</h3>
                          <p>The title of the event.</p>
                          <h3>The Permalink</h3>
                          <p>The permalink is created by the title, but it doesn\'t change automatically if you change the title. To change the permalink when editing an event, click the [Edit] button next to the permalink. 
                          Remove the text that becomes editable and click [OK]. The permalink will repopulate with the new Location and date!</p>
                          ',  //actual help text
            );

            // Add the help tab.
            $current_screen->add_help_tab( $args );

            // Setup help tab args.
            $args = array(
                'id'      => 'event_info', //unique id for the tab
                'title'   => 'Event Info', //unique visible title for the tab
                'content' => '<h3>Event URL</h3>
                          <p>The link to the page describing the event</p>
                          <h3>The Event Date</h3>
                          <p>The Event Date is the date of the event. This value is restrained to dates (chooseable via a datepicker module). This value is also used to sort events for the calendars, upcoming events, etc.</p>
                          <p>For single day events, set start and end date to the same date.',  //actual help text
            );

            // Add the help tab.
            $current_screen->add_help_tab( $args );

        }


        function custom_query( $query ) {
            if(!is_admin()){
                if(is_page()){
                    return $query;
                }
                if($query->is_main_query()) {
                    $post_types = $query->get('post_type');             // Get the currnet post types in the query

                    if(!is_array($post_types) && !empty($post_types))   // Check that the current posts types are stored as an array
                        $post_types = explode(',', $post_types);

                    if(empty($post_types))
                        $post_types = array('post'); // If there are no post types defined, be sure to include posts so that they are not ignored

                    if ($query->is_search) {
                        $searchterm = $query->query_vars['s'];
                        // we have to remove the "s" parameter from the query, because it will prevent the posts from being found
                        $query->query_vars['s'] = "";

                        if ($searchterm != "") {
                            $query->set('meta_value', $searchterm);
                            $query->set('meta_compare', 'LIKE');
                        };
                        $post_types[] = $this->cpt;                         // Add your custom post type

                    } elseif ($query->is_archive) {
                        $post_types[] = $this->cpt;                         // Add your custom post type
                    }

                    $post_types = array_map('trim', $post_types);       // Trim every element, just in case
                    $post_types = array_filter($post_types);            // Remove any empty elements, just in case

                    $query->set('post_type', $post_types);              // Add the updated list of post types to your query
                }
            }
        }

        function change_default_title( $title ){
            global $current_screen;
            if  ( $current_screen->post_type == $this->cpt ) {
                return __('Quote Title','qod');
            } else {
                return $title;
            }
        }

        function cpt_display(){
            global $post;
            if(is_cpt($this->cpt)) {
                if (is_single()){
                    //display content here
                } else {
                    //display for aggregate here
                }
            }
        }
    } //End Class
} //End if class exists statement