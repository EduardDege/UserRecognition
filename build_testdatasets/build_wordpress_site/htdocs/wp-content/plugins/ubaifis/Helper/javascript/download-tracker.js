jQuery(document).ready(function($){
  //console.log("hello2")
  /*console.log(navigator.plugins)
  const x = navigator.plugins.length
  let b
  //let port = chrome.extension.connect('gighmmpiobklfepjocnamgkkbiglidom', b)
  var detect = function(base) {
      var s = document.createElement('script');
      document.body.appendChild(s);
      s.src = base
  }
  detect('chrome-extension://' + "gighmmpiobklfepjocnamgkkbiglidom");*/

  $(function() {
      $("a").on('click', function(e) {
          //e.preventDefault();
          $.ajax({
            type: 'GET',
            url: myAjax.ajaxurl,
            contentType: "json",
            data: {
              action: 'download_action',
              download: $(this).attr("href"),
              session_id: myAjax.session_id
            },
            success: function(data, textStatus, XMLHttpRequest){
              console.log(textStatus)
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                    alert(textStatus)
                  }
          })
          /*$('.loading').show();
          url = $('.download').attr('href') + '?cmd=prepare';
          $.ajax({
              url: url,
              type: 'get',
              success: function(filename) {
                  console.log(data);
                  window.location = filename;
                  $('.loading').hide();
              }
          });*/
      });
  });

})
