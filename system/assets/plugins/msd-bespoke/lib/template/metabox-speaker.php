<?php global $wpalchemy_media_access; ?>
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
?>
<table class="form-table">
    <tbody>
    <?php $mb->the_field('location'); ?>
    <tr valign="top">
        <th scope="row"><label for="<?php $mb->the_name(); ?>">Speaker Location</label></th>
        <td>
            <p><input class="large-text" type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" placeholder="ex: New York/Boston" /></p>
        </td>
    </tr>
    <?php $mb->the_field('alpha'); ?>
    <tr valign="top">
        <th scope="row"><label for="<?php $mb->the_name(); ?>">Last Name (for Alphabetizing)</label></th>
        <td>
            <p><input class="large-text" type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" placeholder="" /></p>
        </td>
    </tr>
    <?php $mb->the_field('mediacontact'); ?>
    <tr valign="top">
        <th scope="row"><label for="<?php $mb->the_name(); ?>"></label></th>
        <td>
            <input type="checkbox" name="<?php $mb->the_name(); ?>" value="true"<?php $mb->the_checkbox_state('true'); ?>/> List as Media Contact Only
        </td>
    </tr>
    <tr valign="top">
        <th scope="row"><label for="<?php $mb->the_name(); ?>">State(s)</label></th>
        <td>
            <ul style="column-count: 3;">
                <?php foreach($states AS $state => $st){ ?>
                    <?php $mb->the_field('state'); ?>
                    <li>
                        <input type="checkbox" name="<?php $mb->the_name(); ?>[]" value="<?php print $st; ?>"<?php $mb->the_checkbox_state($st); ?>/> <?php print ucwords(strtolower($state)); ?>
                    </li>
                <?php } ?>
            </ul>
        </td>
    </tr>
    </tbody>
</table>