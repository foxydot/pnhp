<?php
remove_all_actions('msdlab_title_area' );
remove_all_actions('genesis_entry_header');
remove_all_actions('genesis_entry_content');
remove_all_actions('genesis_entry_footer');
add_action('genesis_loop','msdlab_mr_info',8);
add_action('genesis_loop','msdlab_mr_challenge',9);
add_action('msdlab_title_area','msdlab_news_cleanup');
add_action('msdlab_title_area','msdlab_mr_category_banner');
add_filter('genesis_attr_entry','msdlab_mr_entry_attr');

genesis();