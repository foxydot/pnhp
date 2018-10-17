<?php
/*
Plugin Name: MSDLab Transfer Tools Transfer Tools
Description: A small set of tools for Transfer Tools transfer.
Author: MSDLAB
Version: 0.0.1
Author URI: http://msdlab.com
*//*
 * Pull in some stuff from other files
*/
if(!function_exists('requireDir')){
    function requireDir($dir){
        $dh = @opendir($dir);

        if (!$dh) {
            throw new Exception("Cannot open directory $dir");
        } else {
            while($file = readdir($dh)){
                $files[] = $file;
            }
            closedir($dh);
            sort($files); //ensure alpha order
            foreach($files AS $file){
                if ($file != '.' && $file != '..') {
                    $requiredFile = $dir . DIRECTORY_SEPARATOR . $file;
                    if ('.php' === substr($file, strlen($file) - 4)) {
                        require_once $requiredFile;
                    } elseif (is_dir($requiredFile)) {
                        requireDir($requiredFile);
                    }
                }
            }
        }
        unset($dh, $dir, $file, $requiredFile);
    }
}

global $transfer_tools_db;
if (!class_exists('MSDTransferTools')) {
    class MSDTransferTools {
        //Properites
        /**
         * @var string The plugin version
         */
        var $version = '0.0.1';

        /**
         * @var string The options string name for this plugin
         */
        var $optionsName = 'msd_transfer_tools_options';

        /**
         * @var string $nonce String used for nonce security
         */
        var $nonce = 'msd_transfer_tools-update-options';

        /**
         * @var string $localizationDomain Domain used for localization
         */
        var $localizationDomain = "msd_transfer_tools";

        /**
         * @var string $pluginurl The path to this plugin
         */
        var $plugin_url = '';
        /**
         * @var string $pluginurlpath The path to this plugin
         */
        var $plugin_path = '';

        /**
         * @var array $options Stores the options for this plugin
         */
        var $options = array();
        //Methods

        /**
         * PHP 5 Constructor
         */
        function __construct(){
            //"Constants" setup
            $this->plugin_url = plugin_dir_url(__FILE__).'/';
            $this->plugin_path = plugin_dir_path(__FILE__).'/';
            //Initialize the options
            $this->get_options();
            //check requirements
            register_activation_hook(__FILE__, array(&$this,'check_requirements'));
            //get sub-packages
            requireDir(plugin_dir_path(__FILE__).'/lib/inc');
            add_action( 'wp_enqueue_scripts', array( &$this, 'maybe_load_bootstrap' ), 30 );
            add_action( 'admin_enqueue_scripts', array( &$this, 'maybe_load_jqueryui' ), 30 );
            add_action('admin_menu', array(&$this,'settings_page'));
            //here are some examples to get started with
            if(class_exists('Convert_Subtitles')){
                $this->subtitles_class = new Convert_Subtitles();
            }
            if(class_exists('Convert_Media')){
                $this->media_class = new Convert_Media();
            }
            if(class_exists('Convert_News_To_Article')){
                $this->news_class = new Convert_News_To_Article();
            }
            if(class_exists('Other_Presentations')){
                $this->other_class = new Other_Presentations();
            }
        }
        /**
         * @desc Loads the options. Responsible for handling upgrades and default option values.
         * @return array
         */
        function check_options() {
            $options = null;
            if (!$options = get_option($this->optionsName)) {
                // default options for a clean install
                $options = array(
                    'version' => $this->version,
                    'reset' => true
                );
                update_option($this->optionsName, $options);
            }
            else {
                // check for upgrades
                if (isset($options['version'])) {
                    if ($options['version'] < $this->version) {
                        // post v1.0 upgrade logic goes here
                    }
                }
                else {
                    // pre v1.0 updates
                    if (isset($options['admin'])) {
                        unset($options['admin']);
                        $options['version'] = $this->version;
                        $options['reset'] = true;
                        update_option($this->optionsName, $options);
                    }
                }
            }
            return $options;
        }

        /**
         * @desc Retrieves the plugin options from the database.
         */
        function get_options() {
            $options = $this->check_options();
            $this->options = $options;
        }
        /**
         * @desc Check to see if requirements are met
         */
        function check_requirements(){

        }
        /***************************/

        function maybe_load_bootstrap(){
            if(!wp_script_is( 'bootstrap-jquery', $list = 'enqueued' ) && !wp_script_is( 'bootstrap', $list = 'enqueued' )){
                wp_enqueue_script('bootstrap-jquery','//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js',array('jquery'));
            }
            if(!wp_style_is( 'bootstrap-style', $list = 'enqueued' ) && !wp_style_is( 'bootstrap', $list = 'enqueued' )){
                wp_enqueue_style('bootstrap-style','//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css');
            }
        }
        function maybe_load_jqueryui(){
            if(!wp_script_is( 'jquery-ui-core', $list = 'enqueued' )){
                wp_enqueue_script('jquery-ui-core');
            }
            if(!wp_script_is( 'jquery-ui-datepicker', $list = 'enqueued' )){
                wp_enqueue_script('jquery-ui-datepicker');
            }
            if(!wp_style_is( 'jquery-ui-smoothness', $list = 'enqueued' )){
                global $wp_scripts;
                $ui = $wp_scripts->query('jquery-ui-core');
                $protocol = is_ssl() ? 'https' : 'http';
                $url = "$protocol://ajax.googleapis.com/ajax/libs/jqueryui/{$ui->ver}/themes/smoothness/jquery-ui.min.css";
                wp_enqueue_style('jquery-ui-smoothness', $url, false, null);
            }
            //wp_enqueue_style('transfer_tools-style',$this->plugin_url.'lib/css/style.css');
        }

        function settings_page()
        {
            if ( count($_POST) > 0 && isset($_POST['transfer_tools_settings']) )
            {
                $options = array (
                    'clientid',
                );

                foreach ( $options as $opt )
                {
                    delete_option ( 'transfer_tools_'.$opt, $_POST[$opt] );
                    add_option ( 'transfer_tools_'.$opt, $_POST[$opt] );
                }

            }
            add_submenu_page('tools.php',__('Transfer Tools'),__('Transfer Tools'), 'administrator', 'transfer_tools-options', array(&$this,'settings_page_content'));
        }
        function settings_page_content()
        {

            ?>
            <style>
                span.note{
                    display: block;
                    font-size: 0.9em;
                    font-style: italic;
                    color: #999999;
                }
                body{
                    background-color: transparent;
                }
                .input-table.even{background-color: rgba(0,0,0,0.1);padding: 2rem 0;}
                .input-table .description{display:none}
                .input-table li:after{content:".";display:block;clear:both;visibility:hidden;line-height:0;height:0}
                .input-table label{display:block;font-weight:bold;margin-right:1%;float:left;width:14%;text-align:right}
                .input-table label span{display:inline;font-weight:normal}
                .input-table span{color:#999;display:block}
                .input-table .input{width:85%;float:left}
                .input-table .input .half{width:48%;float:left}
                .input-table textarea,.input-table input[type='text'],.input-table select{display:inline;margin-bottom:3px;width:90%}
                .input-table .mceIframeContainer{background:#fff}
                .input-table h4{color:#999;font-size:1em;margin:15px 6px;text-transform:uppercase}
            </style>
            <script>
                jQuery(document).ready(function($) {
                    $('.subtitles').click(function(){
                        var data = {
                            action: 'convert_subtitles',
                            start: $('#subtitle_start').val(),
                        };
                        jQuery.post(ajaxurl, data, function(response) {
                            $('.response1').html(response);
                            $('#subtitle_start').val($('#subtitle_start').val() + 500);
                            console.log(response);
                        });
                    });
                    $('.media').click(function(){
                        var data = {
                            action: 'convert_media',
                            start: $('#media_start').val(),
                        };
                        jQuery.post(ajaxurl, data, function(response) {
                            $('.response1').html(response);
                            $('#media_start').val($('#media_start').val() + 500);
                            console.log(response);
                        });
                    });
                    $('.news').click(function(){
                        var data = {
                            action: 'convert_news_to_article',
                            start: $('#news_start').val(),
                        };
                        jQuery.post(ajaxurl, data, function(response) {
                            $('.response1').html(response);
                            $('#news_start').val(Number($('#news_start').val()) + 500);
                            console.log(response);
                        });
                    });
                    $('.other_presentations').click(function(){
                        var data = {
                            action: 'other_presentations',
                        };
                        jQuery.post(ajaxurl, data, function(response) {
                            $('.response1').html(response);
                            console.log(response);
                        });
                    });
                });
            </script>
            <div class="wrap">
                <h2>Transfer Tools</h2>
                <dl>
                    <dt>Transfer Subtitles:</dt>
                    <dd><input type="number" id="subtitle_start"></input> <button class="subtitles">Go</button></dd>
                </dl>
                <dl>
                    <dt>Transfer Media:</dt>
                    <dd><input type="number" id="media_start"></input> <button class="media">Go</button></dd>
                </dl>
                <dl>
                    <dt>Transfer News:</dt>
                    <dd><input type="number" id="news_start"></input> <button class="news">Go</button></dd>
                </dl>
                <dl>
                    <dt>Other Presentations:</dt>
                    <dd><button class="other_presentations">Go</button></dd>
                </dl>
                <div class="response1"></div>
            </div>
            <?php
        }
    } //End Class
} //End if class exists statement

//instantiate
$msd_transfer_tools = new MSDTransferTools();
