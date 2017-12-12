<?php

remove_all_actions('genesis_loop');
add_action('genesis_sidebar','msdlab_speaker_filter_tags');
add_action('genesis_loop','msdlab_speaker_aggregated',11);


//add a modal to the page
add_action('wp_footer','msdlab_add_content_modal');
//use JS to populate it with the selected article?
genesis();
