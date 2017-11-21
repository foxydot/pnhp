<?php
remove_all_actions('msdlab_title_area' );
add_action('msdlab_title_area','msdlab_speaker_cleanup');
add_action('msdlab_title_area','msdlab_speaker_taxonomy_banner');
add_action('genesis_entry_header','msdlab_speaker_entry_hdr_img',8);
add_action('genesis_entry_header','msdlab_speaker_entry_hdr_cats');
//add_filter('genesis_attr_entry','msdlab_speaker_entry_attr');
//add_action('genesis_before_while','msdlab_speaker_aggregate_wrapper_open');
//add_action('genesis_after_while','msdlab_speaker_aggregate_wrapper_close');
genesis();