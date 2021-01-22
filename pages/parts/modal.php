<?php
$title = ""; $chapter = ""; $content = ""; $action = ""; $file = "";
if(isset($_POST['content']) && isset($_POST['title']) && isset($_POST['chapter'])){

$title = $_POST['title'];
$content = $_POST['content'];
$chapter = $_POST['chapter'];
$action = $_POST['action'];

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

<div class="modal" id="vb-delete-modal" tabindex="-1" role="dialog" data-chapter="<?php echo $chapter; ?>" data-file="<?php echo $file; ?>">
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
        <button id="<?php echo $action; ?>vb-confirm-delete" type="button" class="btn btn-danger" data-chapter="<?php echo $chapter ?>" data-key="<?php echo $content; ?>">Yes</button> 
        <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>        
      </div>
    </div>
  </div>
</div>

<div class="modal-backdrop show d-none"></div>