<table class="form-table">
    <tbody>

    <?php while($mb->have_fields_and_multi('articles')): ?>
    <?php $mb->the_group_open('tr'); ?>

        <?php $mb->the_field('newsurl'); ?>

            <th scope="row"><label for="<?php $mb->the_name(); ?>">URL to News Article</label></th>
            <td>
                <p><input class="large-text" type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" placeholder="http://" /></p>
            </td>

    <?php $mb->the_group_close(); ?>
    <?php endwhile; ?>

    <tr valign="top">
        <th scope="row">
        </th>
        <td>
            <p><a href="#" class="docopy-articles button">Add Article URL</a></p>
        </td>
    </tr>

    </tbody>
</table>