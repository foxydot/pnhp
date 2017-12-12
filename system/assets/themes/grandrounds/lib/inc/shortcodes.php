<?php
if(!class_exists('MSDLab_Theme_Shortcodes')){
    class MSDLab_Theme_Shortcodes{
        //vars or declarations?
        //construct
        function __construct(){
            //vars
            //actions
            //filters
            //shortcodes
            add_shortcode('wrap',array(&$this,'wrap_sh'));
        }
        //methods
        function wrap_sh($atts,$content = null){
            extract(shortcode_atts( array(
                'context' => '',
            ), $atts ));
            //if there is no content, it's an error so toss it back.
            if($content === null){
                return $content;
            }
            $ret = $content;
            //there's content! figure out the context.
            switch($context){
                case 'officer-intro':
                    $ret = '';
                    $reader = new DOMDocument();
                    $reader->loadHTML($content);
                    foreach($reader->getElementsByTagName('img') as $img) {
                        $i = $reader->saveHTML($img);
                        $str[] = '<div class="img image-wrapper alignleft">'.$i.'</div>';
                    }
                    $str[] = '<div class="name">';
                    foreach($reader->getElementsByTagName('h3') as $h3) {
                        $str[] = $reader->saveHTML($h3);
                    }
                    $str[] = '</div>';
                    $str[] = '<div class="meta">';
                    foreach($reader->getElementsByTagName('h4') as $h4) {
                        $str[] = $reader->saveHTML($h4);
                    }
                    $str[] = '</div>';
                    $ret = '<div class="element-wrapper officer-intro">'.implode(' ',$str).'</div>';
                    break;
                default:
                    break;
            }
            return $ret;
        }
    }
    global $msdlab_theme_shortcodes;
    $msdlab_theme_shortcodes = new MSDLab_Theme_Shortcodes();
}