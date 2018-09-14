<?php
/**
 * Created by PhpStorm.
 * User: CMO
 * Date: 7/16/18
 * Time: 9:38 AM
 */
add_action('genesis_sidebar',array('MSDChapterCPT','sidebar_menu'));
add_action('genesis_after_loop',array('MSDChapterCPT','add_state_news'));

genesis();