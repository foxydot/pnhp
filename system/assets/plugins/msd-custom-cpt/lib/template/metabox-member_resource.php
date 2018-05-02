<table class="form-table">

    <?php while($mb->have_fields_and_multi('files')): ?>
    <?php $mb->the_group_open('tbody'); ?>
        <tr>
            <?php $mb->the_field('mr_title'); ?>

            <th scope="row"><label for="<?php $mb->the_name(); ?>">Title of News Article</label></th>
            <td>
                <p><input class="large-text" type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" placeholder="" /></p>
            </td>
        </tr>
        <tr>
            <?php $mb->the_field('mr_url'); ?>

            <th scope="row"><label for="<?php $mb->the_name(); ?>">URL to News Article</label></th>
            <td>
                <p><input class="large-text" type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" placeholder="http://" /></p>
            </td>
        </tr>
        <tr>
            <?php $mb->the_field('mr_author'); ?>

            <th scope="row"><label for="<?php $mb->the_name(); ?>">Author</label></th>
            <td>
                <p><input class="large-text" type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" placeholder="" /></p>
            </td>
        </tr>
        <tr>
            <?php $mb->the_field('mr_pub'); ?>

            <th scope="row"><label for="<?php $mb->the_name(); ?>">Publication</label></th>
            <td>
                <p><input class="large-text" type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" placeholder="" /></p>
            </td>
        </tr>
        <tr>
            <?php $mb->the_field('mr_date'); ?>

            <th scope="row"><label for="<?php $mb->the_name(); ?>">Date</label></th>
            <td>
                <p><input class="large-text date-picker" type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" placeholder="" /></p>
            </td>
        </tr>
        <tr>
            <?php $mb->the_field('mr_tease'); ?>

            <th scope="row"><label for="<?php $mb->the_name(); ?>">Teaser Text</label></th>
            <td>
                <p><?php
                    wp_editor( stripcslashes($mb->get_the_value()), $mb->get_the_name(), array('media_buttons' => false,'teeny' => true,'textarea_rows' => 3) );
                    ?>
                </p>
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
            <p><a href="#" class="docopy-files button">Add File</a></p>
        </td>
    </tr>

    </tbody>
</table>
<script>
    jQuery(document).ready(function($) {
        $('.date-picker').datepicker();
        $('.tocopy .date-picker').removeClass('hasDatepicker');
        $('[class*=docopy-]').click(function(e){
            $('.wpa_group').not('.tocopy').find('.date-picker').datepicker();
        });
    });
</script>