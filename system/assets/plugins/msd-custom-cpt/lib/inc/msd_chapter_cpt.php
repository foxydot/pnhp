<?php 
if (!class_exists('MSDChapterCPT')) {
	class MSDChapterCPT {
		//Properties
		var $cpt = 'chapter';
		//Methods
	    /**
	    * PHP 4 Compatible Constructor
	    */
		public function MSDChapterCPT(){$this->__construct();}
	
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

			//shortcodes
            add_shortcode('chapter-map',array(&$this,'map_shortcode_handler'));


            //add cols to manage panel
            add_filter( 'manage_edit-'.$this->cpt.'_columns', array(&$this,'my_edit_columns' ));
            add_action( 'manage_'.$this->cpt.'_posts_custom_column', array(&$this,'my_manage_columns'), 10, 2 );
		}


        function register_taxonomies(){

            $labels = array(
                'name' => _x( 'Chapter states', 'chapter-state' ),
                'singular_name' => _x( 'Chapter state', 'chapter-state' ),
                'search_items' => _x( 'Search chapter states', 'chapter-state' ),
                'popular_items' => _x( 'Popular chapter states', 'chapter-state' ),
                'all_items' => _x( 'All chapter states', 'chapter-state' ),
                'parent_item' => _x( 'Parent chapter state', 'chapter-state' ),
                'parent_item_colon' => _x( 'Parent chapter state:', 'chapter-state' ),
                'edit_item' => _x( 'Edit chapter state', 'chapter-state' ),
                'update_item' => _x( 'Update chapter state', 'chapter-state' ),
                'add_new_item' => _x( 'Add new chapter state', 'chapter-state' ),
                'new_item_name' => _x( 'New chapter state name', 'chapter-state' ),
                'separate_items_with_commas' => _x( 'Separate chapter states with commas', 'chapter-state' ),
                'add_or_remove_items' => _x( 'Add or remove chapter states', 'chapter-state' ),
                'choose_from_most_used' => _x( 'Choose from the most used chapter states', 'chapter-state' ),
                'menu_name' => _x( 'Chapter states', 'chapter-state' ),
            );

            $args = array(
                'labels' => $labels,
                'public' => true,
                'show_in_nav_menus' => true,
                'show_ui' => true,
                'show_tagcloud' => false,
                'hierarchical' => true,

                'rewrite' => array('slug'=>'chapter-state','with_front'=>false),
                'query_var' => true
            );

            register_taxonomy( 'chapter_state', array($this->cpt,'news'), $args );
        }

		
		function register_cpt() {
		
		    $labels = array( 
		        'name' => _x( 'Chapter', 'chapter' ),
		        'singular_name' => _x( 'Chapter', 'chapter' ),
		        'add_new' => _x( 'Add New', 'chapter' ),
		        'add_new_item' => _x( 'Add New Chapter', 'chapter' ),
		        'edit_item' => _x( 'Edit Chapter', 'chapter' ),
		        'new_item' => _x( 'New Chapter', 'chapter' ),
		        'view_item' => _x( 'View Chapter', 'chapter' ),
		        'search_items' => _x( 'Search Chapter', 'chapter' ),
		        'not_found' => _x( 'No chapter found', 'chapter' ),
		        'not_found_in_trash' => _x( 'No chapter found in Trash', 'chapter' ),
		        'parent_item_colon' => _x( 'Parent Chapter:', 'chapter' ),
		        'menu_name' => _x( 'Chapter', 'chapter' ),
		    );
		
		    $args = array( 
		        'labels' => $labels,
		        'hierarchical' => false,
		        'description' => 'Chapter',
		        'supports' => array( 'title', 'editor', 'author', 'thumbnail' ),
		        'taxonomies' => array( 'chapter_state' ),
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
		        'rewrite' => array('slug'=>'chapter','with_front'=>false),
		        'capability_type' => 'post',
                'menu_icon' => 'dashicons-location-alt',
		    );
		
		    register_post_type( $this->cpt, $args );
		}


        function register_metaboxes(){
            global $chapter_info;
            $chapter_info = new WPAlchemy_MetaBox(array
            (
                'id' => '_chapter_information',
                'title' => 'Chapter Info',
                'types' => array($this->cpt),
                'context' => 'normal',
                'priority' => 'high',
                'template' => plugin_dir_path(dirname(__FILE__)).'/template/metabox-chapter.php',
                'autosave' => TRUE,
                'mode' => WPALCHEMY_MODE_EXTRACT, // defaults to WPALCHEMY_MODE_ARRAY
                'prefix' => '_chapter_' // defaults to NULL
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
                } elseif ($wp->query_vars["taxonomy"] == 'chapter_category') {
                    $templatefilename = 'taxonomy-chapter_category.php';
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
                        $obj = get_queried_object();
                        if($obj->taxonomy == 'chapter_state'){
                            $query->set('orderby',array(
                                'post_type'      => 'ASC',
                                'post_date' => 'DESC'
                            ) );
                            $post_types = array($this->cpt);
                        }
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
                'chapter_state' => __( 'State(s)' ),
                'author' => __( 'Author' ),
                'date' => __( 'Date' )
            );

            return $columns;
        }

        function my_manage_columns( $column, $post_id ) {
            global $post;

            switch( $column ) {
                /* If displaying the 'logo' column. */
                case 'chapter_state':
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
                return __('Chapter Name','chapter');
            } else {
                return $title;
            }
        }

        function map_shortcode_handler($atts){
            extract(shortcode_atts( array(
                'title' => 'Find Members In Your Area',
            ), $atts ));
            if(strlen($title)>0) {
                $title = '<h3 style="text-align:center">' . $title . '</h3>';
            }
            $javascript = "<script>
//Makes sure the hovered state is always on top preventing overlap.
jQuery(document).ready(function($){
    /*$('svg.map g:not(#Layer_1)').hover(function(){
        $('svg.map').append(this);
    });*/
    $('svg.map g:not(#Layer_1)').each(function(){
        $(this).find('path').append('<text x=\"20\" y=\"20\" font-family=\"sans-serif\" font-size=\"0.8em\" fill=\"white\">' + $(this).attr('id') + '</text>');
    });

    $('#stateInput').change(function(){
       var stateSelect = document.getElementById('stateInput').value;
      var url = '/chapter-state/' + stateSelect;
      //console.log(url);
      window.location.href = url;
    });
});

</script>";
            $svg = '<div class="chapter-finder">'.file_get_contents(dirname(__FILE__).'/chapter_finder.svg').'</div>';
            $tax_args = array(
                'show_option_none'   => 'Select State',
                'orderby'            => 'Name',
                'hide_empty'         => 0,
                'echo'               => 0,
                'name'               => 'stateInput',
                'class'              => 'postform hidden-md hidden-lg',
                'taxonomy'           => 'chapter_state',
                'value_field'	     => 'slug',
            );
            $mobile = wp_dropdown_categories( $tax_args );
            $legend = '<div class="chapter-finder-legend">
    <span class="def"><span class="sample multiple"></span> Multiple PNHP Chapters</span>
    <span class="def"><span class="sample chapter"></span> Active PNHP Chapter</span>
    <span class="def"><span class="sample members"></span> Active PNHP Members</span>
    <span class="def"><span class="sample nope"></span> Email Us for More Information</span>
</div>';
            return $javascript.$title.$svg.$mobile.$legend;
        }

        function sidebar_menu(){
		    global $post;
		    $theID = $post->ID;
            $ret = array();
            $args = array(
                'post_type' => 'chapter',
                'orderby' => 'name',
                'order' => 'ASC',
                'posts_per_page' => -1,
            );
            $chapter_query = new WP_Query($args);
            if($chapter_query->have_posts()){
                print '<nav class="sidebar-menu">
<ul class="menu">';
                while($chapter_query->have_posts()){
                    $chapter_query->the_post();
                    $classes[] = 'menu-item';
                    $classes[] = 'menu-item-' . $post->ID;
                    if($post->ID == $theID){
                        $classes[] = 'current-menu-item';
                    };
                    print '<li class="'.$classes.'"><a href="'.get_permalink().'">'.$post->post_title.'</a></li>';
                }
                wp_reset_postdata();
                print '</ul></div>';
            }
        }

        function add_state_news(){
		    $obj = get_queried_object();
		    if(is_single()) {
                $state = $obj->post_name;
            } else {
                $state = $obj->slug;
            }
		    $ret = array();
		    $args = array(
		        'post_type' => 'news',
                'orderby' => 'post_date',
                'order' => 'DESC',
                'tax_query' => array(
                        array(
                            'taxonomy' => 'chapter_state',
                            'terms' => $state,
                            'field' => 'slug',
                        )
                ),
            );
		    $news_query = new WP_Query($args);
		    if($news_query->have_posts()){
                add_filter('genesis_attr_entry','msdlab_news_entry_attr');
                add_filter('genesis_attr_entry','msdlab_maybe_equalize_attr');
                add_action('genesis_entry_header','msdlab_multimedia_icons');
                add_action('genesis_entry_header', 'genesis_post_info');
                add_action('genesis_entry_footer','msdlab_post_link_block',30);
                global $subtitle_support;
                remove_action('genesis_entry_header', array($subtitle_support,'msdlab_do_post_subtitle'), 10);
                //print '<h2 class="news-title">'.$obj->name.' News</h2>';
                while($news_query->have_posts()){
                    $news_query->the_post();
                    do_action( 'genesis_before_entry' );
                    genesis_markup( array(
                        'open'    => '<article %s>',
                        'context' => 'entry',
                    ) );
                    do_action( 'genesis_entry_header' );
                    do_action( 'genesis_before_entry_content' );
                    printf( '<div %s>', genesis_attr( 'entry-content' ) );
                    //do_action( 'genesis_entry_content' );
                    echo '</div>';
                    do_action( 'genesis_after_entry_content' );
                    do_action( 'genesis_entry_footer' );
                    genesis_markup( array(
                        'close'   => '</article>',
                        'context' => 'entry',
                    ) );
                    do_action( 'genesis_after_entry' );
                }
                wp_reset_postdata();
                remove_filter('genesis_attr_entry','msdlab_news_entry_attr');
                remove_filter('genesis_attr_entry','msdlab_maybe_equalize_attr');
                remove_action('genesis_entry_header','msdlab_multimedia_icons');
                remove_action('genesis_entry_header', 'genesis_post_info');
                add_action('genesis_entry_header', array($subtitle_support,'msdlab_do_post_subtitle'), 10);
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