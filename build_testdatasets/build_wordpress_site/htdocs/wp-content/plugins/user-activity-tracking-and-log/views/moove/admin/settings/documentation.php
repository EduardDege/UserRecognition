<div class="moove-activity-plugin-documentation">
    <br>
    <h1><?php _e('Moove Activity Plugin','user-activity-tracking-and-log');?></h1>

    <p><?php _e('This plugin adds the ability to track the content visits / updates for any kind of custom post type or page.','user-activity-tracking-and-log');?></p>
    <p><?php _e('You can enable or disable the tracking for each post type registered on your site.','user-activity-tracking-and-log');?></p>
    <h3><?php _e('The following data will be logged:','user-activity-tracking-and-log');?></h3>
    <ol>
        <li><?php _e('Date / Time','user-activity-tracking-and-log');?></li>
        <li><?php _e('Post Title','user-activity-tracking-and-log');?></li>
        <li><?php _e('Post type','user-activity-tracking-and-log');?></li>
        <li><?php _e('User name','user-activity-tracking-and-log');?></li>
        <li><?php _e('Activity (visited/updated)','user-activity-tracking-and-log');?></li>
        <li><?php _e('Client IP','user-activity-tracking-and-log');?></li>
        <li><?php _e('Client Location (by IP Address)','user-activity-tracking-and-log');?></li>
        <li><?php _e('Referrer URL','user-activity-tracking-and-log');?></li>
    </ol>
    <h3><?php _e('Global Settings','user-activity-tracking-and-log');?></h3>
    <p><?php _e('Under the Global settings page found under Settings -> Moove Activity Log you can set up activity logging globally per all the defined post types in your WordPress installation. Also, you can define the time frame/period to keep the logs in the database. This feature is really handy when you want to log activity for smaller or larger periods of time, but be careful when you set a large period it can affect your server performance and database size.','user-activity-tracking-and-log');?></p>
    <p><?php _e('When you DISABLE logging for a custom post type, all your logs will be deleted from the database. You have to confirm this before it deletes everything, but be sure you want to do this before disabling logging, or export your data in CSV beforehand.','user-activity-tracking-and-log');?></p>

    <h3><?php _e('Overriding the global settings','user-activity-tracking-and-log');?></h3>
    <p><?php _e('You can override the global post type tracking settings for each post by using the Moove Activity meta box when editing a post.','user-activity-tracking-and-log');?></p>

    <h3><?php _e('Activity log','user-activity-tracking-and-log');?></h3>
    <p><?php _e('On the left admin menu, below the Dashboard menu item there is an "','user-activity-tracking-and-log');?><a href="<?php echo admin_url( 'admin.php?page=moove-activity-log'); ?>"><?php _e('Activity log','user-activity-tracking-and-log');?></a><?php _e('" page, this is where you can see the log entries.','user-activity-tracking-and-log');?></p>
    <p><strong><?php _e('Features of the Activity log page include the following:','user-activity-tracking-and-log');?></strong></p>
    <ol>
        <li><?php _e('PAGINATION - load more pagination for loading log entries via Ajax.','user-activity-tracking-and-log');?></li>
        <li><?php _e('CLEARING LOGS - You have the possibility to clear log entries per post type or you can clear all log entries at once.','user-activity-tracking-and-log');?></li>
        <li><?php _e('GROUPING - Activity log entries are grouped by post type and subsequently the logs are grouped by post.','user-activity-tracking-and-log');?></li>
    </ol>

</div>
<!-- moove-activity-plugin-documentation -->