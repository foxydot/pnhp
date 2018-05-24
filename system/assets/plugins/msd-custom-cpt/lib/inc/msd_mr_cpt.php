<?php 
if (!class_exists('MSDMemberResourceCPT')) {
	class MSDMemberResourceCPT {
		//Properties
		var $cpt = 'member-resources';
		//Methods
	    /**
	    * PHP 4 Compatible Constructor
	    */
		public function MSDMemberResourceCPT(){$this->__construct();}
	
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
            //add_action('template_redirect', array(&$this,'my_theme_redirect'));
            add_action('admin_head', array(&$this,'codex_custom_help_tab'));

			
			//Filters
			add_filter( 'pre_get_posts', array(&$this,'custom_query') );
			add_filter( 'enter_title_here', array(&$this,'change_default_title') );

			//Shortcodes
            add_shortcode('member_resource', array(&$this,'member_resource_shortcode_handler'));

			//add cols to manage panel
            add_filter( 'manage_edit-'.$this->cpt.'_columns', array(&$this,'my_edit_columns' ));
            add_action( 'manage_'.$this->cpt.'_posts_custom_column', array(&$this,'my_manage_columns'), 10, 2 );
        }


        function register_taxonomies(){

            $labels = array(
                'name' => _x( 'Member Resource categories', 'member_resource-category' ),
                'singular_name' => _x( 'Member Resource category', 'member_resource-category' ),
                'search_items' => _x( 'Search member_resource categories', 'member_resource-category' ),
                'popular_items' => _x( 'Popular member_resource categories', 'member_resource-category' ),
                'all_items' => _x( 'All member_resource categories', 'member_resource-category' ),
                'parent_item' => _x( 'Parent member_resource category', 'member_resource-category' ),
                'parent_item_colon' => _x( 'Parent member_resource category:', 'member_resource-category' ),
                'edit_item' => _x( 'Edit member_resource category', 'member_resource-category' ),
                'update_item' => _x( 'Update member_resource category', 'member_resource-category' ),
                'add_new_item' => _x( 'Add new member_resource category', 'member_resource-category' ),
                'new_item_name' => _x( 'New member_resource category name', 'member_resource-category' ),
                'separate_items_with_commas' => _x( 'Separate member_resource categories with commas', 'member_resource-category' ),
                'add_or_remove_items' => _x( 'Add or remove member_resource categories', 'member_resource-category' ),
                'choose_from_most_used' => _x( 'Choose from the most used member_resource categories', 'member_resource-category' ),
                'menu_name' => _x( 'Member Resource categories', 'member_resource-category' ),
            );

            $args = array(
                'labels' => $labels,
                'public' => true,
                'show_in_nav_menus' => true,
                'show_ui' => true,
                'show_tagcloud' => false,
                'hierarchical' => true, //we want a "category" style taxonomy, but may have to restrict selection via a dropdown or something.

                'rewrite' => array('slug'=>'member-resources','with_front'=>false),
                'query_var' => true
            );

            register_taxonomy( 'member_resources_category', array($this->cpt), $args );


            $labels = array(
                'name' => _x( 'Member Resource tags', 'member_resource-tag' ),
                'singular_name' => _x( 'Member Resource tag', 'member_resource-tag' ),
                'search_items' => _x( 'Search member_resource tags', 'member_resource-tag' ),
                'popular_items' => _x( 'Popular member_resource tags', 'member_resource-tag' ),
                'all_items' => _x( 'All member_resource tags', 'member_resource-tag' ),
                'parent_item' => _x( 'Parent member_resource tag', 'member_resource-tag' ),
                'parent_item_colon' => _x( 'Parent member_resource tag:', 'member_resource-tag' ),
                'edit_item' => _x( 'Edit member_resource tag', 'member_resource-tag' ),
                'update_item' => _x( 'Update member_resource tag', 'member_resource-tag' ),
                'add_new_item' => _x( 'Add new member_resource tag', 'member_resource-tag' ),
                'new_item_name' => _x( 'New member_resource tag name', 'member_resource-tag' ),
                'separate_items_with_commas' => _x( 'Separate member_resource tags with commas', 'member_resource-tag' ),
                'add_or_remove_items' => _x( 'Add or remove member_resource tags', 'member_resource-tag' ),
                'choose_from_most_used' => _x( 'Choose from the most used member_resource tags', 'member_resource-tag' ),
                'menu_name' => _x( 'Member Resource tags', 'member_resource-tag' ),
            );

            $args = array(
                'labels' => $labels,
                'public' => true,
                'show_in_nav_menus' => false,
                'show_ui' => true,
                'show_tagcloud' => true,
                'hierarchical' => false,

                'rewrite' => array('slug'=>'member-resources-tag','with_front'=>false),
                'query_var' => true
            );

            register_taxonomy( 'member_resources_tag', array($this->cpt), $args );
        }
		
		function register_cpt() {
		
		    $labels = array( 
		        'name' => _x( 'Member Resource', 'member_resource' ),
		        'singular_name' => _x( 'Member Resource', 'member_resource' ),
		        'add_new' => _x( 'Add New', 'member_resource' ),
		        'add_new_item' => _x( 'Add New Member Resource', 'member_resource' ),
		        'edit_item' => _x( 'Edit Member Resource', 'member_resource' ),
		        'new_item' => _x( 'New Member Resource', 'member_resource' ),
		        'view_item' => _x( 'View Member Resource', 'member_resource' ),
		        'search_items' => _x( 'Search Member Resource', 'member_resource' ),
		        'not_found' => _x( 'No member_resource found', 'member_resource' ),
		        'not_found_in_trash' => _x( 'No member_resource found in Trash', 'member_resource' ),
		        'parent_item_colon' => _x( 'Parent Member Resource:', 'member_resource' ),
		        'menu_name' => _x( 'Member Resource', 'member_resource' ),
		    );
		
		    $args = array( 
		        'labels' => $labels,
		        'hierarchical' => false,
		        'description' => 'Member Resource',
                'supports' => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail' ),
		        'taxonomies' => array( 'member_resources_category', 'member_resources_tag' ),
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
		        'rewrite' => array('slug'=>'member-resource','with_front'=>false),
		        'capability_type' => 'post',
                'menu_icon' => 'dashicons-portfolio',
		    );
		
		    register_post_type( $this->cpt, $args );
		}


        function register_metaboxes(){
            global $member_resource_info;
            $member_resource_info = new WPAlchemy_MetaBox(array
            (
                'id' => '_member_resource_information',
                'title' => 'Member Resource Info',
                'types' => array($this->cpt),
                'context' => 'normal',
                'priority' => 'high',
                'template' => plugin_dir_path(dirname(__FILE__)).'/template/metabox-member_resource.php',
                'autosave' => TRUE,
                'mode' => WPALCHEMY_MODE_EXTRACT, // defaults to WPALCHEMY_MODE_ARRAY
                'prefix' => '_member_resource_' // defaults to NULL
            ));
        }
		
        
		function add_admin_scripts() {
			global $current_screen;
            if($current_screen->post_type == $this->cpt){
                wp_enqueue_script('bootstrap-jquery','//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js',array('jquery'),$this->ver,TRUE);
                wp_enqueue_script('timepicker-jquery',plugin_dir_url(dirname(__FILE__)).'/js/jquery.timepicker.min.js',array('jquery'),$this->ver,FALSE);
                wp_enqueue_script( 'jquery-ui-datepicker' );
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
                if(is_single() && $wp->query_vars["post_type"] == $this->cpt){
                    $templatefilename = 'single-'.$this->cpt.'.php';
                } elseif (isset($wp->query_vars["member_resource_category"])) {
                    $templatefilename = 'taxonomy-member_resource_category.php';
                } elseif (is_archive() && $wp->query_vars["post_type"] == $this->cpt) {
                    $templatefilename = 'archive-'.$this->cpt.'.php';
                    //A Custom Taxonomy Page
                }
            if($templatefilename) {
                if (file_exists(STYLESHEETPATH . '/' . $templatefilename)) {
                    $return_template = STYLESHEETPATH . '/' . $templatefilename;
                } else {
                    $return_template = plugin_dir_path(dirname(__FILE__)) . 'template/' . $templatefilename;
                }
                do_theme_redirect($return_template);
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


        function my_edit_columns( $columns ) {

            $columns = array(
                'cb' => '<input type="checkbox" />',
                'title' => __( 'Title' ),
                $this->cpt.'_category' => __( 'Categories' ),
                $this->cpt.'_tag' => __( 'Tags' ),
                'author' => __( 'Author' ),
                'date' => __( 'Date' )
            );

            return $columns;
        }

        function my_manage_columns( $column, $post_id ) {
            global $post;

            switch( $column ) {
                /* If displaying the 'logo' column. */
                case $this->cpt.'_category' :
                case $this->cpt.'_tag' :
                    $taxonomy = $column;
                    if ( $taxonomy ) {
                        $taxonomy_object = get_taxonomy( $taxonomy );
                        $terms = get_the_terms( $post->ID, $taxonomy );
                        if ( is_array( $terms ) ) {
                            $out = array();
                            foreach ( $terms as $t ) {
                                $posts_in_term_qv = array();
                                if ( 'post' != $post->post_type ) {
                                    $posts_in_term_qv['post_type'] = $post->post_type;
                                }
                                if ( $taxonomy_object->query_var ) {
                                    $posts_in_term_qv[ $taxonomy_object->query_var ] = $t->slug;
                                } else {
                                    $posts_in_term_qv['taxonomy'] = $taxonomy;
                                    $posts_in_term_qv['term'] = $t->slug;
                                }

                                $label = esc_html( sanitize_term_field( 'name', $t->name, $t->term_id, $taxonomy, 'display' ) );
                                $out[] = $this->get_edit_link( $posts_in_term_qv, $label );
                            }
                            /* translators: used between list items, there is a space after the comma */
                            echo join( __( ', ' ), $out );
                        } else {
                            echo '<span aria-hidden="true">&#8212;</span><span class="screen-reader-text">' . $taxonomy_object->labels->no_terms . '</span>';
                        }
                    }
                    break;
                default :
                    break;
            }
        }

        function get_edit_link( $args, $label, $class = '' ) {
            $url = add_query_arg( $args, 'edit.php' );

            $class_html = '';
            if ( ! empty( $class ) ) {
                $class_html = sprintf(
                    ' class="%s"',
                    esc_attr( $class )
                );
            }

            return sprintf(
                '<a href="%s"%s>%s</a>',
                esc_url( $url ),
                $class_html,
                $label
            );
        }


        function change_default_title( $title ){
            global $current_screen;
            if  ( $current_screen->post_type == $this->cpt ) {
                return __('Member Resource Name','member_resource');
            } else {
                return $title;
            }
        }

        function member_resource_shortcode_handler($atts, $content){
            extract(shortcode_atts( array(
                'title' => 'Member Resource',
                'count' => 5,
                $this->cpt.'_category' => false,
                $this->cpt.'_tag' => false,
            ), $atts ));
            $allowed_terms = array('articles-of-interest','members-in-the-member_resource','quote-of-the-day','state-single-payer-member_resource');
                $args = array(
                    'post_type' => 'member_resource',
                    'showposts' => $count,

                );
            $item_template = '<dt><span class="member_resource-category">%term_list%</span> <span class="date">%date%</span></dt><dd><a href="%permalink%">%title%</a></dd>';

            $patterns = array(
                '|%term_list%|i',
                '|%date%|i',
                '|%permalink%|i',
                '|%title%|i',
                '|%publication%|i',
            );

            if(${$this->cpt.'_category'}) {
                    $class = $this->cpt.'_category'.${$this->cpt.'_category'};
                    $args['tax_query'] = array(
                        array(
                            'taxonomy' => $this->cpt.'_category',
                            'field'    => 'slug',
                            'terms'    => ${$this->cpt.'_category'},
                        ),
                    );
                    switch(${$this->cpt.'_category'}){
                        case 'quote-of-the-day':
                            $item_template = '<dt><span class="date">%date%</span></dt><dd><a href="%permalink%">%title%</a></dd>';
                            break;
                    }
                } elseif (${$this->cpt.'_tag'}) {
                    $class = $this->cpt.'_tag'.${$this->cpt.'_tag'};
                    $args['tax_query'] = array(
                        array(
                            'taxonomy' => $this->cpt.'_tag',
                            'field'    => 'slug',
                            'terms'    => ${$this->cpt.'_tag'},
                        ),
                    );
                } else {
                    $class = $this->cpt.'_all';
                    $args['tax_query'] = array(
                        array(
                            'taxonomy' => 'member_resource_category',
                            'field'    => 'slug',
                            'terms'    => $allowed_terms,
                        ),
                    );
                }

                $recents = new WP_Query($args);
                if($recents->have_posts()) {
                    global $post;
                    $ret[] = '<section class="widget member_resource-widget clearfix '.$class.'">
<h3 class="widgettitle widget-title">' . $title . ' </h3>
<div class="wrap">
<dl class="member_resource-widget-list">';
//start loop
                    ob_start();
                    while($recents->have_posts()) {
                        $recents->the_post();
                        $replacements = array(
                            get_the_term_list($post->ID,$this->cpt.'_category'),
                            get_the_date(),
                            get_the_permalink(),
                            get_the_title(),
                        );
                        print preg_replace($patterns,$replacements,$item_template);
                    } //end loop
                    $ret[] = ob_get_contents();
                    ob_end_clean();
                    $ret[] = '</dl></div></section>';
                } //end loop check

            wp_reset_postdata();

            return implode("\n",$ret);
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