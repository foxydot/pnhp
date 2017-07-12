<?php 
if (!class_exists('MSDSpeakerCPT')) {
	class MSDSpeakerCPT {
		//Properties
		var $cpt = 'speaker';
		//Methods
	    /**
	    * PHP 4 Compatible Constructor
	    */
		public function MSDSpeakerCPT(){$this->__construct();}
	
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
			add_action('admin_head', array(&$this,'plugin_header'));
			add_action('admin_print_scripts', array(&$this,'add_admin_scripts') );
			add_action('admin_print_styles', array(&$this,'add_admin_styles') );
			add_action('admin_footer',array(&$this,'info_footer_hook') );
			// important: note the priority of 99, the js needs to be placed after tinymce loads
			add_action('admin_print_footer_scripts',array(&$this,'print_footer_scripts'),99);
            add_action('template_redirect', array(&$this,'my_theme_redirect'));
            add_action('admin_head', array(&$this,'codex_custom_help_tab'));
			
			//Filters
			add_filter( 'pre_get_posts', array(&$this,'custom_query') );
			add_filter( 'enter_title_here', array(&$this,'change_default_title') );
		}


        function register_taxonomies(){

            $labels = array(
                'name' => _x( 'Speaker categories', 'speaker-category' ),
                'singular_name' => _x( 'Speaker category', 'speaker-category' ),
                'search_items' => _x( 'Search speaker categories', 'speaker-category' ),
                'popular_items' => _x( 'Popular speaker categories', 'speaker-category' ),
                'all_items' => _x( 'All speaker categories', 'speaker-category' ),
                'parent_item' => _x( 'Parent speaker category', 'speaker-category' ),
                'parent_item_colon' => _x( 'Parent speaker category:', 'speaker-category' ),
                'edit_item' => _x( 'Edit speaker category', 'speaker-category' ),
                'update_item' => _x( 'Update speaker category', 'speaker-category' ),
                'add_new_item' => _x( 'Add new speaker category', 'speaker-category' ),
                'new_item_name' => _x( 'New speaker category name', 'speaker-category' ),
                'separate_items_with_commas' => _x( 'Separate speaker categories with commas', 'speaker-category' ),
                'add_or_remove_items' => _x( 'Add or remove speaker categories', 'speaker-category' ),
                'choose_from_most_used' => _x( 'Choose from the most used speaker categories', 'speaker-category' ),
                'menu_name' => _x( 'Speaker categories', 'speaker-category' ),
            );

            $args = array(
                'labels' => $labels,
                'public' => true,
                'show_in_nav_menus' => true,
                'show_ui' => true,
                'show_tagcloud' => false,
                'hierarchical' => true, //we want a "category" style taxonomy, but may have to restrict selection via a dropdown or something.

                'rewrite' => array('slug'=>'speaker-category','with_front'=>false),
                'query_var' => true
            );

            register_taxonomy( 'speaker_category', array($this->cpt), $args );
        }
		
		function register_cpt() {
		
		    $labels = array( 
		        'name' => _x( 'Speaker', 'speaker' ),
		        'singular_name' => _x( 'Speaker', 'speaker' ),
		        'add_new' => _x( 'Add New', 'speaker' ),
		        'add_new_item' => _x( 'Add New Speaker', 'speaker' ),
		        'edit_item' => _x( 'Edit Speaker', 'speaker' ),
		        'new_item' => _x( 'New Speaker', 'speaker' ),
		        'view_item' => _x( 'View Speaker', 'speaker' ),
		        'search_items' => _x( 'Search Speaker', 'speaker' ),
		        'not_found' => _x( 'No speaker found', 'speaker' ),
		        'not_found_in_trash' => _x( 'No speaker found in Trash', 'speaker' ),
		        'parent_item_colon' => _x( 'Parent Speaker:', 'speaker' ),
		        'menu_name' => _x( 'Speaker', 'speaker' ),
		    );
		
		    $args = array( 
		        'labels' => $labels,
		        'hierarchical' => false,
		        'description' => 'Speaker',
		        'supports' => array( 'title', 'editor', 'author', 'thumbnail' ),
		        'taxonomies' => array( 'speaker_category' ),
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
		        'rewrite' => array('slug'=>'speaker','with_front'=>false),
		        'capability_type' => 'post',
                'menu_icon' => 'dashicons-microphone',
		    );
		
		    register_post_type( $this->cpt, $args );
		}


        function register_metaboxes(){
            global $speaker_info;
            $speaker_info = new WPAlchemy_MetaBox(array
            (
                'id' => '_speaker_information',
                'title' => 'Speaker Info',
                'types' => array($this->cpt),
                'context' => 'normal',
                'priority' => 'high',
                'template' => plugin_dir_path(dirname(__FILE__)).'/template/metabox-speaker.php',
                'autosave' => TRUE,
                'mode' => WPALCHEMY_MODE_EXTRACT, // defaults to WPALCHEMY_MODE_ARRAY
                'prefix' => '_speaker_' // defaults to NULL
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
                } elseif ($wp->query_vars["taxonomy"] == 'speaker_category') {
                    $templatefilename = 'taxonomy-speaker_category.php';
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
				if($query->is_main_query() && $query->is_search){
					$searchterm = $query->query_vars['s'];
					// we have to remove the "s" parameter from the query, because it will prevent the posts from being found
					$query->query_vars['s'] = "";
					
					if ($searchterm != "") {
						$query->set('meta_value',$searchterm);
						$query->set('meta_compare','LIKE');
					};
					$query->set( 'post_type', array('post','page',$this->cpt) );
					ts_data($query);
				}
				elseif( $query->is_main_query() && $query->is_archive ) {
					$query->set( 'post_type', array('post','page',$this->cpt) );
				}
			}
		}

        function change_default_title( $title ){
            global $current_screen;
            if  ( $current_screen->post_type == $this->cpt ) {
                return __('Speaker Name','speaker');
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