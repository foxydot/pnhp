<?php global $wpalchemy_media_access; ?>
<table class="form-table">
    <tbody>
    <?php $mb->the_field('title'); ?>
    <tr valign="top">
        <th scope="row"><label for="<?php $mb->the_name(); ?>">Job Title</label></th>
        <td>
            <p><input class="large-text" type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" placeholder="ex: Director of Marketing" /></p>
        </td>
    </tr>
    <?php $mb->the_field('alpha'); ?>
    <tr valign="top">
        <th scope="row"><label for="<?php $mb->the_name(); ?>">Last Name (for Alphabetizing)</label></th>
        <td>
            <p><input class="large-text" type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" placeholder="" /></p>
        </td>
    </tr>
    </tbody>
</table>

<?php
/*
Contact Info (Chapters?) Should this be State not chapter????
State Legislation
Speakers & Media Contacts (How to classify)
HR 676 Endorements
    Unions
    Municipalities

News (on Output)
*/