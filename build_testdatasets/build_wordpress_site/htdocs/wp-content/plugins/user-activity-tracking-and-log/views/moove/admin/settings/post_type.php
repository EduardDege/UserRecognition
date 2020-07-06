<select name="moove_post_act[<?php echo $data['post_type'];?>]" id="<?php echo $data['post_type'];?>" class="moove-activity-log-settings" data-postcount="<?php echo $data['post_count']; ?>">

	<option value="0"<?php echo isset($data['options'][$data['post_type']]) && $data['options'][$data['post_type']] == 0 ? ' selected="selected"':''?>>
        <?php _e('Disabled', 'user-activity-tracking-and-log'); ?>
    </option>

	<option value="1"<?php echo isset($data['options'][$data['post_type']]) && $data['options'][$data['post_type']] == 1 ? ' selected="selected"':''?>>
        <?php _e('Enabled', 'user-activity-tracking-and-log'); ?>
    </option>

</select>
<h5 class="moove-activity-log-delete-text">Delete logs older than: </h5>
<select name="moove_post_act[<?php echo $data['post_type'].'_transient';?>]" id="<?php echo $data['post_type'].'_transient';?>" class="moove-activity-log-transient">

    <?php
        $activity_settings = get_option( 'moove_post_act' );
        if (!$activity_settings) :
            $data['options'][$data['post_type'].'_transient'] = 7;
        endif;
    ?>

    <option value="1"<?php echo isset($data['options'][$data['post_type'].'_transient']) && $data['options'][$data['post_type'].'_transient'] == 1 ? ' selected="selected"':''?>>
        <?php _e('1 day', 'user-activity-tracking-and-log'); ?>
    </option>

    <option value="2"<?php echo isset($data['options'][$data['post_type'].'_transient']) && $data['options'][$data['post_type'].'_transient'] == 2 ? ' selected="selected"':''?>>
        <?php _e('2 days', 'user-activity-tracking-and-log'); ?>
    </option>

    <option value="3"<?php echo isset($data['options'][$data['post_type'].'_transient']) && $data['options'][$data['post_type'].'_transient'] == 3 ? ' selected="selected"':''?>>
        <?php _e('3 days', 'user-activity-tracking-and-log'); ?>
    </option>

    <option value="4"<?php echo isset($data['options'][$data['post_type'].'_transient']) && $data['options'][$data['post_type'].'_transient'] == 4 ? ' selected="selected"':''?>>
        <?php _e('4 days', 'user-activity-tracking-and-log'); ?>
    </option>

    <option value="5"<?php echo isset($data['options'][$data['post_type'].'_transient']) && $data['options'][$data['post_type'].'_transient'] == 5 ? ' selected="selected"':''?>>
        <?php _e('5 days', 'user-activity-tracking-and-log'); ?>
    </option>

    <option value="6"<?php echo isset($data['options'][$data['post_type'].'_transient']) && $data['options'][$data['post_type'].'_transient'] == 6 ? ' selected="selected"':''?>>
        <?php _e('6 days', 'user-activity-tracking-and-log'); ?>
    </option>

    <option value="7"<?php echo isset($data['options'][$data['post_type'].'_transient']) && $data['options'][$data['post_type'].'_transient'] == 7 ? ' selected="selected"':''?>>
        <?php _e('1 week', 'user-activity-tracking-and-log'); ?>
    </option>

    <option value="14"<?php echo isset($data['options'][$data['post_type'].'_transient']) && $data['options'][$data['post_type'].'_transient'] == 14 ? ' selected="selected"':''?>>
        <?php _e('2 weeks', 'user-activity-tracking-and-log'); ?>
    </option>

    <?php do_action( 'moove-activity-delete-options', $data ); ?>

</select>





