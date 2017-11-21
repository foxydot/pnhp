<?php

remove_all_actions('genesis_loop');
add_action('genesis_sidebar','msdlab_filter_tags');
add_action('genesis_loop','msdlab_speaker_aggregated',11);


//add a modal to the page
add_action('wp_footer','msdlab_add_content_modal');
//use JS to populate it with the selected article?
genesis();

function msdlab_filter_tags(){
    $regions = array('northeast', 'midwest', 'south', 'west');
    $specialties = get_terms(array(
        'taxonomy' => 'speaker_specialty',
    ));
    $topic = get_terms(array(
        'taxonomy' => 'speaker_topic',
    ));
    print '<section class="speaker-filters">
<h3 class="widget-title">View speakers by:</h3>
        <select id="region-select" class="region">
        <option value="">All Regions</option>';
    foreach($regions AS $region){
        $r = $region;
        $rs[] = '<option value="/speaker-region/'.$r.'">'.ucwords($r).'</option>';
    }
    print implode('',$rs);
    print '</select>
        <select id="specialty-select" class="specialty">
        <option value="">All Specialties</option>';
    foreach($specialties AS $specialty){
        $s = $specialty->slug;
        $ss[] = '<option value="/speaker-specialty/'.$s.'">'.ucwords($s).'</option>';
    }
    print implode('',$ss);
    print '</select>
        <select id="topic-select" class="topic">
        <option value="">All Topics</option>';
    foreach($topics AS $topic){
        $t = $topic->slug;
        $ts[] = '<option value="/speaker-topic/'.$t.'">'.ucwords($t).'</option>';
    }
    print implode('',$ts);
    print '</select>
</section>';
}