<?php
$title = ""; $chapter = ""; $content = ""; $action = "";
if(isset($_POST['content']) && isset($_POST['title']) && isset($_POST['chapter'])){

$title = $_POST['title'];
$content = $_POST['content'];
$chapter = $_POST['chapter'];

}elseif(isset($_POST['book']) && isset($_POST['title']) && isset($_POST['chapter']) && isset($_POST['action']) && isset($_POST['file'])){
  if($_POST['action'] == "chapter_delete"){
    $content = $_POST['book'];
    $title = $_POST['title'];
    $chapter = $_POST['chapter'];
    $action = $_POST['action'];
    $file = $_POST['file'];
  }
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
        <button id="vb-confirm-delete" type="button" class="btn btn-danger" data-chapter="<?php echo $chapter ?>" data-key="<?php echo $content; ?>">Yes</button> 
        <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>        
      </div>
    </div>
  </div>
</div>

<?php if(!empty($title) && !is_integer($chapter) && empty($action)) { ?>
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

<?php }

elseif(!empty($title) && $_POST['action'] == "chapter_delete") { ?>

<div class="modal-backdrop show"></div>
<script type="text/javascript">
jQuery(document).ready(function($){
$("#vb-delete-modal").css("display","block");

  function deleteChapter(book,chapter,title,file){
    let modal = $("#vb-delete-modal"); 
    $.ajax({
        method: "POST",
        url: "../model/chapters.php",
        data: {chapter:chapter,key:book,title:title,file:file,action:"delete"},
        dataType: "text",
        beforeSend: function(){
          modal.find(".modal-body>p").html(`Deleting ...`);
        },
        success: function(data){
          setTimeout(function(){
            modal.find(".modal-body>p").html(data);            
            setTimeout(function(){           
              listBookChapters(book);                        
              $("#vb-modal-container>div").remove();          
            },1000);   
          },1500);            
        }
    });
  }

  $("#vb-confirm-delete").click(function(){
    let chapter = $(this).data("chapter");
    let book = $(this).data("key");
    let title = "<?php echo $title; ?>";
    let file = "<?php echo $file; ?>";
    deleteChapter(book,chapter,title,file);
  });

  $(document).on("click","#vb-modal-container .close, #vb-modal-container .btn-secondary",function(){
    $("#vb-modal-container>div").remove();
  });

});
</script>

<?php }