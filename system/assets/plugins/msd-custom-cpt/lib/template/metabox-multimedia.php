<?php global $wpalchemy_media_access; ?>
<table class="form-table">
    <tbody>
    <?php $mb->the_field('videourl'); ?>
    <tr valign="top">
        <th scope="row"><label for="videourl">Video URL</label></th>
        <td>
            <p><input class="large-text" type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" placeholder="http://" /></p>
        </td>
    </tr>
    <?php $mb->the_field('audiourl'); ?>
    <tr valign="top">
        <th scope="row"><label for="audiourl">Audio URL</label></th>
        <td>
            <p><input class="large-text" type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" placeholder="http://" /></p>
        </td>
    </tr>
    </tbody>
</table>