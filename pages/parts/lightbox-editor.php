<?php
include "../../function.php";
if(isset($_POST['chapter']) && isset($_POST['key']) && isset($_POST['name']) && isset($_POST['file'])){
$name = $_POST['name'];
$chapter = $_POST['chapter'];
$key = $_POST['key'];
$file = $_POST['file'];
$list = file_get_contents("../../json/book-content/{$file}.json");
$contentlist = json_decode($list);
$text = $contentlist[$key]->content;
$text = revertTextToEditor($text);

?>
<div class="modal" id="vb-modal-editor" tabindex="-1" role="dialog" style="display:block">
  <div class="modal-editor-wrap" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><?php echo $name; ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="vb-editor">
      <div class="p-4">
        <form action="POST" method="POST">
            <div class="form-group">
              <textarea name="vb-text-editor" id="body" col="30" row="10" style="height:auto;" value="">
                <?php echo $text; ?>
              </textarea>
            </div>
        </form>
      </div>
      </div>
      <div class="modal-footer">       
        <!-- <input id="vbcc-text" type="hidden" value="<?php //echo $text; ?>"> -->
        <button id="vb-submit-content" type="button" class="btn btn-primary" data-file="<?php echo $file; ?>" data-chapter="<?php echo $chapter; ?>" data-key="<?php echo $key; ?>">Submit</button>       
      </div>
    </div>
  </div>
</div>
<div class="modal-backdrop show"></div>

<script type="text/javascript">
ClassicEditor
.create( document.querySelector( '#body' ) )
.catch( error => {
    console.error( error );
} ); 
</script>

<?php } ?>