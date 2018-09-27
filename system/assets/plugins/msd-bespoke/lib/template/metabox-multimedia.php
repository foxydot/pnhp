<?php global $wpalchemy_media_access; ?>
<table class="form-table">
    <tbody>
    <?php $mb->the_field('hasvideo'); ?>
    <tr valign="top">
        <th scope="row"><label for="hasvideo">Has Video?</label></th>
        <td>
            <p><input type="checkbox" name="<?php $mb->the_name(); ?>" value="true"<?php $mb->the_checkbox_state('true'); ?>/></p>
        </td>
    </tr>
    <?php $mb->the_field('videourl'); ?>
    <tr valign="top">
        <th scope="row"><label for="videourl">Video URL</label></th>
        <td>
            <p><input class="large-text" type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" placeholder="http://" /></p>
        </td>
    </tr>
    <?php $mb->the_field('videothumb'); ?>
    <tr valign="top">
        <th scope="row"><label for="videothumb">Video Thumbnail</label></th>
        <td>
            <?php if($mb->get_the_value() != ''){
                print '<img src="'.$mb->get_the_value().'">';
            } ?>
            <?php $group_name = 'vid-thumb-'. $mb->get_the_index(); ?>
            <?php $wpalchemy_media_access->setGroupName($group_name)->setInsertButtonLabel('Insert This')->setTab('upload'); ?>
            <?php echo $wpalchemy_media_access->getField(array('name' => $mb->get_the_name(), 'value' => $mb->get_the_value())); ?>
            <?php echo $wpalchemy_media_access->getButton(array('label' => 'Add Image')); ?>
        </td>
    </tr>
    <?php $mb->the_field('videomemberinnews'); ?>
    <tr valign="top">
        <th scope="row"><label for="videomemberinnews">PNHP Members in the News</label></th>
        <td>
            <p><input type="checkbox" name="<?php $mb->the_name(); ?>" value="true"<?php $mb->the_checkbox_state('true'); ?>/></p>
        </td>
    </tr>
    <tr>
        <td colspan="2"><hr></td>
    </tr>
    <?php $mb->the_field('hasaudio'); ?>
    <tr valign="top">
        <th scope="row"><label for="hasaudio">Has Audio?</label></th>
        <td>
            <p><input type="checkbox" name="<?php $mb->the_name(); ?>" value="true"<?php $mb->the_checkbox_state('true'); ?>/></p>
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