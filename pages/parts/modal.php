<?php
$title = ""; $chapter = ""; $content = "";
if(isset($_POST['content']) && isset($_POST['title']) && isset($_POST['chapter'])){

$title = $_POST['title'];
$content = $_POST['content'];
$chapter = $_POST['chapter'];

}
?>

<div class="modal" id="vb-delete-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Delete</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete <span id="vb-title-handler"><?php echo "\"{$title}\""; ?></span>?</p>
      </div>
      <div class="modal-footer">       
        <button id="vb-confirm-delete" type="button" class="btn btn-danger" data-key="<?php echo $content; ?>">Yes</button> 
        <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>        
      </div>
    </div>
  </div>
</div>

<?php if(!empty($title) && !is_integer($chapter)) { ?>
<div class="modal-backdrop show"></div>
<script type="text/javascript">
jQuery(document).ready(function($){

$("#vb-delete-modal").css("display","block");

//DELETE BOOK   
function deleteContent(key,lctn){
  let modal = $("#vb-delete-modal"); 
  $.ajax({
      method: "POST",
      url: "../model/content.php",
      data: {key:key,lctn:lctn,action:"delete"},
      dataType: "text",
      beforeSend: function(){
        modal.find(".modal-body>p").html(`Deleting ...`);
      },
      success: function(data){
        setTimeout(function(){
          modal.find(".modal-body>p").html(data);            
          setTimeout(function(){           
            loadChapterPart(<?php echo $chapter; ?>);                        
            $("#vb-modal-container>div").remove();          
          },1000);   
        },1500);            
      }
  });
}

$("#vb-confirm-delete").click(function(){
    let x = $(this).data("key");
    let lctn = $("#vb-ttl-cdidtfyr").data("universal");
    deleteContent(x,lctn);
});

$(document).on("click","#vb-modal-container .close, #vb-modal-container .btn-secondary",function(){
  $("#vb-modal-container>div").remove();
});

});
</script>

<?php } ?>