<?php

class Other_Presentations{

    private $field_map;
    private $meta_map;

    /**
     * A reference to an instance of this class.
     */
    private static $instance;


    /**
     * Returns an instance of this class.
     */
    public static function get_instance() {

        if( null == self::$instance ) {
            self::$instance = new Other_Presentations();
        }

        return self::$instance;

    }

    /**
     * XProfile ID.
     *
     * @since 1.6.0
     * @var int $id
     */
    public $id;

    /**
     * User ID.
     *
     * @since 1.6.0
     * @var int $user_id
     */
    public $user_id;

    /**
     * XProfile field ID.
     *
     * @since 1.6.0
     * @var int $field_id
     */
    public $field_id;

    /**
     * XProfile field value.
     *
     * @since 1.6.0
     * @var string $value
     */
    public $value;

    /**
     * XProfile field last updated time.
     *
     * @since 1.6.0
     * @var string $last_updated
     */
    public $last_updated;

    /**
     * Initializes the plugin by setting filters and administration functions.
     */
    public function __construct() {
        add_action( 'wp_ajax_other_presentations', array(&$this,'other_presentations') );

    }

    public function other_presentations(){
        global $member_resource_info;
        $hierarchical_tax = array( 219 ); // Array of tax ids.
        $non_hierarchical_tax = 'additional'; // Can use array of ids or string of tax names separated by commas

        $member_resource_info->the_meta(11175);
        while ($member_resource_info->have_fields('memberresource')) {
            $title = $member_resource_info->get_the_value('mr_title');
            $file = $member_resource_info->get_the_value('mr_file');
            $author = $member_resource_info->get_the_value('mr_author');
            $date = $member_resource_info->get_the_value('mr_date');
            $tease = $member_resource_info->get_the_value('mr_tease');
            $ctr++;
            // Create post object
            $my_post = array(
                'post_title'    => $title,
                'post_content'  => '&nbsp;',
                'post_date' => date("Y-m-d H:i:s",strtotime($date)),
                'post_status'   => 'publish',
                'post_type' => 'member-resources',
                'tax_input'    => array(
                    'hierarchical_tax'     => $hierarchical_tax,
                    'non_hierarchical_tax' => $non_hierarchical_tax,
                ),
                'meta_input'   => array(
                    '_member_resource_memberresource' => array(
                        array(
                            'mr_title' => $title,
                            'mr_file' => $file,
                            'mr_author' => $author,
                            'mr_date' => $date,
                            'mr_tease' => $tease)
                    ),
                    '_member_resource_information_fields' => array('_member_resource_memberresource'),
                ),
            );

            // Insert the post into the database
            if($post_id = wp_insert_post( $my_post ,true)){
                if(is_wp_error($post_id)){
                    ts_data($post_id);
                } else {
                    $success =  $title . ' inserted as ' . $post_id;
                    error_log($success);
                    print $success;
                }
            }
        }
        wp_die();
    }
}