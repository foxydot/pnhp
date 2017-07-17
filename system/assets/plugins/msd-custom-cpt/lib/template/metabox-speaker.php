<?php global $wpalchemy_media_access; ?>
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
    </tbody>
</table>