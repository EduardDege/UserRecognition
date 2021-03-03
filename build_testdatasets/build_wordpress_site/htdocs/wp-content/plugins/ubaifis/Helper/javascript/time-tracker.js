jQuery(document).ready(function($){

  startTime = new Date()
  let counter = 0

  setInterval(()=> {
    counter++
  }, 5 * 1000)

  setInterval(()=> {
    console.log(counter * 5)
  }, 5 * 1000)


  $(window).on("beforeunload", ()=> {
  //$("body").on("click", ()=>{
    console.log("HELLO§")
    $.ajax({
      type: 'GET',
      url: myAjax.ajaxurl,
      contentType: "json",
      data: {
        action: 'timer_action',
        timer: counter * 5,
        session_id: myAjax.session_id
      },
      success: function(data, textStatus, XMLHttpRequest){
        console.log(textStatus)
      },
      error: function (XMLHttpRequest, textStatus, errorThrown) {
              alert(textStatus)
            }
    })
  })

})
