<?php

class Convert_Subtitles{

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
            self::$instance = new Convert_Subtitles();
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
        add_action( 'wp_ajax_convert_subtitles', array(&$this,'convert_subtitles') );

    }

    public function convert_subtitles(){
        $i = $_POST['start'];
        $args = array(
            'posts_per_page'   => 500,
            'offset'           => $i,
            'post_type'        => 'news',
            'post_status'      => 'publish',
        );
        $result = get_posts($args);
        foreach($result AS $post){
            print $i.': '.$this->convert_subtitle($post) . "<br>\n\r";
            $i++;
        }
        wp_die();
    }

    function convert_subtitle($post){
        set_time_limit(1);
        preg_match('/^<h2>(.*?)<\/h2>/i',$post->post_content,$matches);
        if(isset($matches[1])) {
            $subtitle = $matches[1];
            print($post->post_title.' has subtitle<br>');
            if (preg_match('/(<img.*?\/>|<a.*?>(.*?)<\/a>)/i', $subtitle, $xmatch)) {
                return false;
            }
            //do things
            print($post->post_title.' has legit subtitle<br>');
            $orig_subtitle = get_post_meta($post->ID,'_msdlab_subtitle',true);
            if(strlen($orig_subtitle)>0) {
                print($post->post_title.' already has a subtitle: '.$orig_subtitle.'<br>');
            } else {
                $orig_subtitle_fields = get_post_meta($post->ID, '_subtitle_fields', true);
                update_post_meta($post->ID, '_msdlab_subtitle', $subtitle, $orig_subtitle);
                update_post_meta($post->ID, '_subtitle_fields', array('_msdlab_subtitle'), $orig_subtitle_fields);
                $new_content = preg_replace('/^<h2>(.*?)<\/h2>/i', '', $post->post_content);
                $post->post_content = $new_content;
                wp_update_post($post);
            }
        }
        return true;
    }


}