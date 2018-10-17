<?php

class Convert_News_To_Article{

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
            self::$instance = new Convert_News_To_Article();
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
        add_action( 'wp_ajax_convert_news_to_article', array(&$this,'convert_news_to_article') );

    }

    public function convert_news_to_article(){
        $i = $_POST['start'];
        $args = array(
            'posts_per_page'   => 500,
            'offset'           => $i,
            'post_type'        => 'news',
            'post_status'      => 'publish',
            'tax_query' => array(
                array(
                    'taxonomy' => 'news_category',
                    'field'    => 'slug',
                    'terms'    => array( 'articles-of-interest', 'latest-research' )
                )
            )
        );
        $result = get_posts($args);
        global $wpdb;
        foreach($result AS $post){
            print $i.': '.$this->convert_news($post) . "<br>\n\r";
            $i++;
        }
        wp_die();
    }

    function convert_news($post){
        set_time_limit(1);
        preg_match('/^<p><strong>By (.*?)<\/strong><br \/><em>(.*?)<\/em>/i',$post->post_content,$matches);
        if(isset($matches[1])||isset($matches[2])) {
            preg_match('/^(.*?),\s((?:Jan|Feb|March|April|May|June|July|Aug|Sept|Oct|Nov|Dec)\s\d{1,2},\s\d{4})/i',$matches[2],$matchex);
            preg_match_all('/<a(?:.*?)href="(.*?)">.*?<\/a>/i',trim($post->post_content),$urlmatch);
            $data = array(
                'newstitle' => $post->post_title,
                'newsurl' => array_pop($urlmatch[1]),
                'newsauthor' => $matches[1],
                'newspub' => $matchex[1],
                'newsdate' => $matchex[2],
            );
            $articles = get_post_meta($post->ID,'_news_articles',true);
            if(is_array($articles)) {
                $articles_orig = $articles;
                array_unshift($articles, $data);
            } else {
                $articles = array($data);
            }
            print($post->post_title.'<br>');
            $orig_articles_fields = get_post_meta($post->ID, '_news_information_fields', true);
            update_post_meta($post->ID, '_news_articles', $articles, $articles_orig);
            update_post_meta($post->ID, '_news_information_fields', array('_news_articles'), $orig_articles_fields);
        }
        return true;
    }


}