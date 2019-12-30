<?php global $wpalchemy_media_access; ?>
<table class="form-table">
    <tbody>
    <?php $mb->the_field('bannerbool'); ?>
    <tr valign="top">
        <th scope="row"><label for="bannerbool"></label></th>
        <td>
            <p><input type="checkbox" name="<?php $mb->the_name(); ?>" value="true"<?php $mb->the_checkbox_state('true'); ?>/> Use page banner?</p>
        </td>
    </tr>
    <?php if(class_exists('LS_Sliders')){ ?>
        <?php $mb->the_field('bannerslider');
        //get all sliders for options
        $sliders = LS_Sliders::find($filters);
        foreach($sliders AS $slider){
            $option[] = '<option value="'.$slider['id'].'"'.selected( $mb->get_the_value(), $slider['id'], 0).'>'.$slider['name'].'</option>';
        }
        $options = implode("\n",$option);
        ?>

        <tr valign="top">
        <th scope="row"><label for="bannerslider"></label></th>
        <td>
            <p><select name="<?php $mb->the_name(); ?>">
                    <option value="0">Static</option>
                    <?php print $options; ?>
                </select></p>
        </td>
    </tr>
    <?php } ?>
    <?php $mb->the_field('banneralign'); ?>
    <tr valign="top">
        <th scope="row"><label for="banneralign"></label>Banner text alignment</th>
        <td>
            <p><input type="radio" name="<?php $mb->the_name(); ?>" value="textleft"<?php $mb->the_radio_state('textleft'); ?>/> Text left</p>
            <p><input type="radio" name="<?php $mb->the_name(); ?>" value="textright"<?php $mb->the_radio_state('textright'); ?>/> Text right</p>
        </td>
    </tr>
    <?php $mb->the_field('bannerimage'); ?>
    <tr valign="top">
        <th scope="row"><label for="bannerimage">Banner Image</label></th>
        <td>
            <?php $img_btn_label = "Add Image"; ?>
            <?php if($mb->get_the_value() != ''){
                $thumb_array = wp_get_attachment_image_src( get_attachment_id_from_src($mb->get_the_value()), 'thumbnail' );
                $thumb = $thumb_array[0];
                $img_btn_label = "Change Image";
                ?>
                <img class="banner-preview-img" src="<?php print $thumb; ?>"><br />
                <?php
            } ?>
            <?php $group_name = 'bannerimage-'. $mb->get_the_index(); ?>
            <?php $wpalchemy_media_access->setGroupName($group_name)->setInsertButtonLabel('Insert This')->setTab('upload'); ?>
            <?php echo $wpalchemy_media_access->getField(array('name' => $mb->get_the_name(), 'value' => $mb->get_the_value())); ?>
            <?php echo $wpalchemy_media_access->getButton(array('label' => $img_btn_label)); ?>
        </td>
    </tr>
    <?php $mb->the_field('bannertitleinbanner'); ?>
    <tr valign="top">
        <th scope="row"><label for="bannertitleinbanner"></label></th>
        <td>
            <p><input type="checkbox" name="<?php $mb->the_name(); ?>" value="true"<?php $mb->the_checkbox_state('true'); ?>/> Hide title in banner?</p>
        </td>
    </tr>
    <?php $mb->the_field('bannertitleincontent'); ?>
    <tr valign="top">
        <th scope="row"><label for="bannertitleincontent"></label></th>
        <td>
            <p><input type="checkbox" name="<?php $mb->the_name(); ?>" value="true"<?php $mb->the_checkbox_state('true'); ?>/> Hide title in content?</p>
        </td>
    </tr>
    <?php $mb->the_field('bannercontent'); ?>
    <tr valign="top">
        <th scope="row"><label for="bannercontent">Banner Content</label></th>
        <td>
            <?php
            $mb_content = html_entity_decode($mb->get_the_value(), ENT_QUOTES, 'UTF-8');
            $mb_editor_id = sanitize_key($mb->get_the_name());
            $mb_settings = array('textarea_name'=>$mb->get_the_name(),'textarea_rows' => '5',);
            wp_editor( $mb_content, $mb_editor_id, $mb_settings );
            ?>
        </td>
    </tr>
    <?php $mb->the_field('bannerclass'); ?>
    <tr valign="top">
        <th scope="row"><label for="bannerclass">Any custom class names for banner styling</label></th>
        <td>
            <input type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" />
        </td>
    </tr>
    </tbody>
</table>