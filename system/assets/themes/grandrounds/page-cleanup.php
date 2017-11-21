<?php

$states = array('ALABAMA'=>"AL",
    'ALASKA'=>"AK",
    'AMERICAN SAMOA'=>"AS",
    'ARIZONA'=>"AZ",
    'ARKANSAS'=>"AR",
    'CALIFORNIA'=>"CA",
    'COLORADO'=>"CO",
    'CONNECTICUT'=>"CT",
    'DELAWARE'=>"DE",
    'DISTRICT OF COLUMBIA'=>"DC",
    "FEDERATED STATES OF MICRONESIA"=>"FM",
    'FLORIDA'=>"FL",
    'GEORGIA'=>"GA",
    'GUAM' => "GU",
    'HAWAII'=>"HI",
    'IDAHO'=>"ID",
    'ILLINOIS'=>"IL",
    'INDIANA'=>"IN",
    'IOWA'=>"IA",
    'KANSAS'=>"KS",
    'KENTUCKY'=>"KY",
    'LOUISIANA'=>"LA",
    'MAINE'=>"ME",
    'MARSHALL ISLANDS'=>"MH",
    'MARYLAND'=>"MD",
    'MASSACHUSETTS'=>"MA",
    'MICHIGAN'=>"MI",
    'MINNESOTA'=>"MN",
    'MISSISSIPPI'=>"MS",
    'MISSOURI'=>"MO",
    'MONTANA'=>"MT",
    'NEBRASKA'=>"NE",
    'NEVADA'=>"NV",
    'NEW HAMPSHIRE'=>"NH",
    'NEW JERSEY'=>"NJ",
    'NEW MEXICO'=>"NM",
    'NEW YORK'=>"NY",
    'NORTH CAROLINA'=>"NC",
    'NORTH DAKOTA'=>"ND",
    "NORTHERN MARIANA ISLANDS"=>"MP",
    'OHIO'=>"OH",
    'OKLAHOMA'=>"OK",
    'OREGON'=>"OR",
    "PALAU"=>"PW",
    'PENNSYLVANIA'=>"PA",
    'RHODE ISLAND'=>"RI",
    'SOUTH CAROLINA'=>"SC",
    'SOUTH DAKOTA'=>"SD",
    'TENNESSEE'=>"TN",
    'TEXAS'=>"TX",
    'UTAH'=>"UT",
    'VERMONT'=>"VT",
    'VIRGIN ISLANDS' => "VI",
    'VIRGINIA'=>"VA",
    'WASHINGTON'=>"WA",
    'WEST VIRGINIA'=>"WV",
    'WISCONSIN'=>"WI",
    'WYOMING'=>"WY");
print 'Starting Process<br>';
$args = array(
    'post_type' => 'speaker',
    'showposts' => -1,juas
);
$speakers = new WP_Query($args);
if($speakers->have_posts()) {
    //start loop
    while($speakers->have_posts()) {
        $states_to_add = array();
        $speakers->the_post();
        the_title('','<br>');
        $speaker_states = get_the_terms($post->ID,'speaker_region');
        foreach($speaker_states AS $s){
            $states_to_add[] = $states[strtoupper($s->name)];
        }
        $oldval = get_post_meta($post->ID,'_speaker_state',true);
        update_post_meta($post->ID,'_speaker_state',$states_to_add,$oldval);
        $fields = get_post_meta($post->ID,'_speaker_information_fields',true);
        $oldval = $fields;
        ts_data($fields);
        if(!is_array($fields)){
            $val = $fields;
            $fields[] = array($val);
        }
        if(!in_array('_speaker_state',$fields)) {
            $fields[] = '_speaker_state';
        }
        ts_data($fields);
        ts_data($oldval);
        update_post_meta($post->ID,'_speaker_information_fields',$fields,$oldval);

    } //end loop
} //end loop check


wp_reset_postdata();
