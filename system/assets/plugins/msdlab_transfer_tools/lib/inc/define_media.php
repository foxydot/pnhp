<?php

class Define_Media{

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
            self::$instance = new Define_Media();
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

    }

    public function define_media($term,$tax){
        $args = array(
            'posts_per_page'   => 4000,
            'post_type'        => 'any',
            'post_status'      => 'any',
            'tax_query' => array(
                array(
                    'taxonomy' => $tax,
                    'field'    => 'slug',
                    'terms'    => $term,
                ),
            ),
        );
        $result = get_posts($args);
        print '<table border="1">';
        foreach($result AS $post){
            $medias = get_post_meta($post->ID, '_member_resource_memberresource', TRUE);
            print '<tr><td>'.$post->post_title.'</td><td>';
            foreach($medias AS $media){
                print $media['mr_file']. '<br />';
            }
            print '</td></tr>';
        }
        print '</table>';
    }
}