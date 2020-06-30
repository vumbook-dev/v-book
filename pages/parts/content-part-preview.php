<?php
if(isset($_POST['content']) && isset($_POST['title']) && isset($_POST['chapter']) && isset($_POST['file'])){

    $title = $_POST['title'];
    $key = $_POST['content'];
    $chapter = $_POST['chapter'];
    $file = $_POST['file'];

    $list = file_get_contents("../../json/book-content/{$file}.json");
    $content = json_decode($list);
    
?>

<div class="modal" id="vb-modal-preview" tabindex="-1" role="dialog" style="display:block">
  <div class="modal-editor-wrap" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><?php echo $title; ?></h5>
        <div class="p-fixed">
          <span id="btn-stop" class="btn mr-2 text-danger d-none"><i class="fa fa-stop" aria-hidden="true"></i> Stop</span>
          <span class="btn mr-2 text-success preplay-section btn-play" data-status="inactive" data-line="0" data-key="<?php echo $key; ?>" data-chapter="<?php echo $chapter; ?>"><i class="fa fa-play" aria-hidden="true"></i> Play</span>
          <span class="d-inline-block btn btn-light edit-vb-style" data-key="<?php echo $key; ?>" data-chapter="<?php echo $chapter; ?>"><i class="fa fa-ellipsis-v" aria-hidden="true"></i> Edit Style</span>
          <button type="button" class="prev-close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
      </div>
      <div class="modal-body px-5 py-2 my-3" id="vb-preview">
        <?php echo $content[$key]->content; ?>
      </div>
      <div class="modal-footer">       
        <!-- <input id="vbcc-text" type="hidden" value="">
        <button id="vb-submit-content" type="button" class="btn btn-primary">Submit</button> -->
      </div>
    </div>
  </div>

<script type="text/javascript">
jQuery(document).ready(function($){
  let n = $("#vb-preview>span").length;
  for(x=0; x<n; x++){
    let span = $("#vb-preview>span:nth-child("+x+")");
    let txtcount = span.text().length;
    if(txtcount > 30){
      span.addClass("vb-textline");
    }else{
      span.addClass("vb-text");
    }
    //console.log(txtcount);
  }

  $("#vb-preview>span>ol, #vb-preview>span>ul").parent("span").addClass("d-block");
  
  $("#vb-preview").mouseenter(function() {
      $(this).addClass("scrollMouseIn");
  })
  .mouseleave(function() {
      $(this).removeClass("scrollMouseIn");
  });

  //PLAYER PLAY AND PAUSE APP READER
  $('.preplay-section').click(function(){  
      let x = $(this).attr("data-line");  
      if(x == 0){
        $("#vb-preview>span").addClass("invisible");
        $("#btn-stop").toggleClass("d-none");
      }                
      let pause = '<i class="fa fa-pause" aria-hidden="true"></i> Pause';
      let play = '<i class="fa fa-play" aria-hidden="true"></i> Play  ';
      let status = $(this).data("status");      
      $(this).toggleClass("text-success");
      $(this).toggleClass("text-secondary");
      
      if(status != "inactive") {
          $(this).html(play);
          $(this).data("status","inactive");
          clearInterval(prevNowPlaying);
          $("#vb-preview>span").unbind();                 
          line = null;
      }else{
        $(this).data("status","active");
        $(this).html(pause);
        window.prevNowPlaying = setInterval(function(){            
            let r = n - (n - x);
            let line = $("#vb-preview span:nth-child("+r+")");
            line.toggleClass("invisible");               
            x++;      
            $('.preplay-section').attr("data-line",x);        
        },1000);
      }
  }); 

  //PLAYER RESET OR STOP APP READER
  $("#btn-stop").click(function(){
    $('.preplay-section').attr("data-status","active");
    $('.preplay-section').click();
    let x = $(".preplay-section").attr("data-line",0);
    $(this).addClass("d-none");
    $("#vb-preview>span").removeClass("invisible");    
  });

  $("button.prev-close").click(function(){
    $('.preplay-section').attr("data-status","active");
    clearInterval(window.prevNowPlaying);
    setTimeout(function(){
      let status = null;
      let x = null;
    },500);    
    //console.log("Close");
    $("#vb-modal-container div").remove();
  });

  $("#vb-modal-preview .edit-vb-style").click(function(){
    let file = $("#vb-ttl-cdidtfyr").data("universal");
    let chapter = $(this).data("chapter");
    let key = $(this).data("key");
    let title = $("h5.modal-title").text();
    loadEditStyle(chapter,key,title,file);
  });

});
</script>
</div>
<div class="modal-backdrop show"></div>

<?php }