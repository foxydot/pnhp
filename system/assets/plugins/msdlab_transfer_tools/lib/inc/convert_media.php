<?php

class Convert_Media{

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
            self::$instance = new Convert_Media();
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
        add_action( 'wp_ajax_convert_media', array(&$this,'convert_media') );

    }

    public function convert_media(){
        $i = $_POST['start'];
        $args = array(
            'posts_per_page'   => 4000,
            'offset'           => $i,
            'post_type'        => 'news',
            'post_status'      => 'publish',
        );
        $result = get_posts($args);
        foreach($result AS $post){
            print $i.' : '. $post->ID .' : '.$this->convert_medium($post) . "<br>\n\r";
            $i++;
        }
        wp_die();
    }

    function convert_medium($post){
        set_time_limit(1);
        $providers = array(
            'ytembed' => '#(https?://((m|www)\.)?youtube\.com/embed/([=&_\-A-Za-z0-9]*))#i',
            'ytwatch' => '#(https?://((m|www)\.)?youtube\.com/watch\?([=&_\-A-Za-z0-9]*))#i',
            'ytplaylist' => '#(https?://((m|www)\.)?youtube\.com/playlist\?([=&_\-A-Za-z0-9]*))#i',
            'ytshort' => '#(https?://youtu\.be/\?([=&_\-A-Za-z0-9]*))#i',
            'vimeo' => '#(https?://(.+\.)?vimeo\.com/\?([=&_\-A-Za-z0-9]*))#i',
            'slideshare' => '#(https?://(.+?\.)?slideshare\.net/\?([=&_\-A-Za-z0-9]*))#i',
        );
        foreach($providers AS $k => $pattern){
            if(preg_match($pattern,$post->post_content,$matches)){
                if(isset($matches[1])) {
                    $video = $matches[1];
                    print($post->post_title.' has video<br>');
                    if($k == 'ytembed'){
                        $video = preg_replace('#embed/#i','watch?v=', $video);
                    }
                    //do things
                    $orig_video = get_post_meta($post->ID,'_news_videourl',true);
                    //if(strlen($orig_video)>0) {
                      //  print($post->post_title.' already has a video: '.$orig_video.'<br>');
                    //} else {
                        $orig_hasvideo = get_post_meta($post->ID,'_news_hasvideo', true);
                        $orig_media_fields = get_post_meta($post->ID, '_multimedia_information_fields', true);
                        if(is_array($orig_media_fields)){
                        $media_fields = $orig_media_fields;}
                        $media_fields[] = '_news_videourl';
                        $media_fields[] = '_news_hasvideo';
                        update_post_meta($post->ID, '_news_hasvideo', 'true', $orig_hasvideo);
                        update_post_meta($post->ID, '_news_videourl', $video, $orig_video);
                        update_post_meta($post->ID, '_multimedia_information_fields', $media_fields, $orig_media_fields);

                    //}
                }
            }
        }
        $audio_providers = array(
            '#(https?://((m|www)\.)?npr\.org/player/embed/([/=&_\-A-Za-z0-9]*))#i',
            '#(https?://(www\.)?soundcloud\.com/\?([=&_\-A-Za-z0-9]*))#i',
        );
        foreach($audio_providers AS $pattern){
            if(preg_match($pattern,$post->post_content,$matches)){
                if(isset($matches[1])) {
                    $audio = $matches[1];
                    print($post->post_title.' has audio<br>');
                    //do things
                    $orig_audio = get_post_meta($post->ID,'_news_audiourl',true);
                    //if(strlen($orig_audio)>0) {
                    //  print($post->post_title.' already has audio: '.$orig_audio.'<br>');
                    //} else {
                    $orig_hasaudio = get_post_meta($post->ID,'_news_hasaudio', true);
                    $orig_media_fields = get_post_meta($post->ID, '_multimedia_information_fields', true);
                    if(is_array($orig_media_fields)){
                        $media_fields = $orig_media_fields;}
                    $media_fields[] = '_news_audiourl';
                    $media_fields[] = '_news_hasaudio';
                    update_post_meta($post->ID, '_news_hasaudio', 'true', $orig_hasaudio);
                    update_post_meta($post->ID, '_news_audiourl', $audio, $orig_audio);
                    update_post_meta($post->ID, '_multimedia_information_fields', $media_fields, $orig_media_fields);

                    //}
                }
            }
        }
        return true;
    }


}