(function($){
    $(document).ready(function(){
        $.post(
            moove_frontend_activity_scripts.ajaxurl,
            {
                action: "moove_activity_track_pageview",
                post_id: moove_frontend_activity_scripts.post_id,
                is_single: moove_frontend_activity_scripts.is_single,
                is_page: moove_frontend_activity_scripts.is_page,
                user_id: moove_frontend_activity_scripts.current_user,
                referrer: moove_frontend_activity_scripts.referrer
            },
            function( msg ) {

            }
        );
    });

})(jQuery);

