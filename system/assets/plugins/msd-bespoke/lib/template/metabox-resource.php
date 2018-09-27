<?php global $wpalchemy_media_access; ?>
<table class="form-table">

    <?php while($mb->have_fields_and_multi('pdfs')): ?>
        <?php $mb->the_group_open('tbody'); ?>
        <tr>
        <?php $mb->the_field('title'); ?>

            <th scope="row"><label for="<?php $mb->the_name(); ?>">File title</label></th>
            <td>
                <p><input class="large-text" type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" placeholder="" /></p>
            </td>
        </tr>
        <?php $mb->the_field('authorname'); ?>
        <tr valign="top">
            <th scope="row"><label for="<?php $mb->the_name(); ?>">Resource Author Name</label></th>
            <td>
                <p><input class="large-text" type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" placeholder="" /></p>
            </td>
        </tr>
        <?php $mb->the_field('file'); ?>
        <tr>
            <th scope="row"><label for="<?php $mb->the_name(); ?>">File</label></th>
            <td>
                <?php $group_name = 'pdf-file-'. $mb->get_the_index(); ?>
                <?php $wpalchemy_media_access->setGroupName($group_name)->setInsertButtonLabel('Insert This')->setTab('upload'); ?>
                <?php echo $wpalchemy_media_access->getField(array('name' => $mb->get_the_name(), 'value' => $mb->get_the_value())); ?>
                <?php echo $wpalchemy_media_access->getButton(array('label' => 'Add File')); ?>
            </td>
        </tr>
        <tr>
            <td colspan="2"><hr></td>
        </tr>
        <?php $mb->the_group_close(); ?>
    <?php endwhile; ?>
    <tbody>
    <tr valign="top">
        <th scope="row">
        </th>
        <td>
            <p><a href="#" class="docopy-pdfs button">Add PDF</a></p>
        </td>
    </tr>
    </tbody>
</table>