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
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="vb-preview">
        <?php echo $content[$key]->content; ?>
      </div>
      <div class="modal-footer">       
        <!-- <input id="vbcc-text" type="hidden" value="">
        <button id="vb-submit-content" type="button" class="btn btn-primary">Submit</button> -->
      </div>
    </div>
  </div>
</div>
<div class="modal-backdrop show"></div>

<?php }