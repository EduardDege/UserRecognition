//(function() {
jQuery(function($){
$(document).ready(function(){

//Bail execution if button is not on page 
let elem_button_exist_on_page = document.querySelector('.g-btn.f-l');
if(null === elem_button_exist_on_page || '' === elem_button_exist_on_page) return;



    document.addEventListener('click', function (event) {

        // If the clicked element doesn't have the right selector, bail
        if (!event.target.matches('.g-btn.f-l')) return;
    
        // Don't follow the link
        event.preventDefault();
    
        let button = event.target;
        let attachment_id = button.getAttribute('data-attachment-id');
        let download_url = button.getAttribute('data-page-id');

        attachment_id = parseInt(attachment_id) - parseInt(download_url);

        let download_external_url = button.getAttribute('data-external-url');

        if(null !== download_external_url) {

            if(null !== download_external_url) window.open(download_external_url,'_blank'); 

            return;

        } else {
            
            var data = {
                'action': 'qdbu_download_ajax_referer', 
                'security': quick_download_object.security 
            }
            
       
            $.post(quick_download_object.url, data, function (callBack) {
             
                let url = `${quick_download_object.redirecturl}?aid=${attachment_id}&_wpnonce=${data.security}`;

           
                if(data.security) {
                    
                    location.href = url;
                }
                
            });

         

        }

        
    
    }, false);

})
})

//})();

