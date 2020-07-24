<?php
if(isset($_POST['content']) && isset($_POST['title']) && isset($_POST['chapter']) && isset($_POST['file']) && isset($_POST['cover'])){

    $title = $_POST['title'];
    $key = $_POST['content'];
    $chapter = $_POST['chapter'];
    $file = $_POST['file'];    
    $bookcover = "";

    $list = file_get_contents("../../json/book-content/{$file}.json");    
    $content = json_decode($list);
    if(isset($_POST['cover'])){
        $coverkey = $_POST['cover'];
        $cover = file_get_contents("../../json/users/user-bookcover.json");
        $cover = json_decode($cover);
        $bookcover = "../../media/bookcover/user/{$cover[$coverkey]->filename}";
    }
    
    $dsounds = file_get_contents("../../json/media/default-sounds.json");
    $dsounds = json_decode($dsounds);  

    $textAlign = $content[$key]->align;

?>

<div class="modal" id="vb-modal-editstyle" tabindex="-1" role="dialog" style="display:block">
  <div class="modal-editor-wrap" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Styles</h5>
        <div class="p-fixed">
          <span id="btn-stop" class="btn mr-2 text-danger d-none"><i class="fa fa-stop" aria-hidden="true"></i> Stop</span>
          <span class="btn mr-2 text-success preplay-section btn-play d-none" data-status="inactive" data-line="0" data-key="<?php echo $key; ?>" data-chapter="<?php echo $chapter; ?>"><i class="fa fa-play" aria-hidden="true"></i> Play</span>
          <span class="d-inline-block btn btn-light back-to-preview" data-title="<?php echo $content[$key]->cpart; ?>" data-key="<?php echo $key; ?>" data-chapter="<?php echo $chapter; ?>"><i class="fa fa-eye pr-2" aria-hidden="true"></i> Preview</span>
          <button type="button" class="editstyle-close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
      </div>
      <div class="modal-body p-4">
        <div class="row">
            <div class="col-md-9 px-4 <?php echo "text-{$content[$key]->align}"; ?>">
                <h3 class="vb-ch-title mb-3"></h3>
                <h5 class="mb-5"><?php echo $title ?></h5>
                <div id="style-preview" class="editstyle-page py-2 px-4">
                    <?php echo $content[$key]->content; ?>
                </div>
            </div>
            <div class="col-md-3 style-widgets-corner">
                <!-- CONTENT ALIGNMENT -->
                <div id="vb-edit-align" class="form-group text-center mb-3 pb-2">
                    <span class="h6"><i class="fa fa-align-justify" aria-hidden="true"></i> Text Align</span>    
                    <ul class="my-3 vb-text-alignment">
                        <li data="left" <?php echo ($textAlign == "left") ? 'class="act-align btn"' : '' ; ?> > Left </li>
                        <li data="center" <?php echo ($textAlign == "center") ? 'class="act-align btn"' : '' ; ?> > Center </li>
                        <li data="justify" <?php echo ($textAlign == "justify") ? 'class="act-align btn"' : '' ; ?> > Justify </li>
                        <li data="right" <?php echo ($textAlign == "right") ? 'class="act-align btn"' : '' ; ?>> Right </li>
                    </ul>
                </div>
                <!-- CONTENT ALIGNMENT END -->
                <!-- SOUNDS SECTION -->
                <div class="form-group text-center">
                    <span class="h6"><i class="fa fa-music" aria-hidden="true"></i> Page Sounds</span>                    
                    <div class="accordion mt-3" id="vbSelectSounds">
                    <div id="default-sounds" class="card">
                        <div class="card-header p-0" id="headingOne">
                        <h5 class="mb-0">
                            <button class="btn collapsed" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                Default Sounds
                            </button>
                        </h5>
                        </div>

                        <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#vbSelectSounds">
                        <div class="card-body p-0">
                            <ul class="mb-0 p-0 slct-sounds">
                                <?php 
                                    foreach($dsounds as $k => $value){
                                        $icon = ($value->id != 0) ? '<i class="fa fa-play px-3" aria-hidden="true" data-dir="default" data-file="'.$value->filename.'"></i></li>' : ' ';
                                        if(is_numeric($content[$key]->sound)){
                                            $activeSound = ($value->id == $content[$key]->sound) ? 'act-sound' : '';
                                        }else{
                                            $activeSound = "";
                                        }                                        
                                        echo '<li class="slct-sounds-list '.$activeSound.'" data-id="'.$value->id.'">'.$value->alias.' '.$icon;
                                    }
                                ?>
                            </ul>
                        </div>
                        </div>
                    </div>

                    <div id="personal-sounds" class="card">
                        <div class="card-header p-0" id="headingTwo">
                        <h5 class="mb-0">
                            <button class="btn" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                My Media Sounds
                            </button>
                        </h5>
                        </div>

                        <div id="collapseTwo" class="collapse show" aria-labelledby="headingTwo" data-parent="#vbSelectSounds">
                        <div class="card-body p-0 pb-2" style="height:auto;">
                            <div id="vb-my-audio"></div>                                                                               
                            <div id="vb-sound-upload" class="form-group px-2 mb-0 pt-2">
                            <div class="input-group">
                            <input type="text" class="form-control d-none rdnly-plchldr" readonly>
                            <form class="input-empty" id="submit-audio" method="post" action="">
                            <div class="input-group-btn" style="margin-left:-2px;">
                                <span class="fileUpload btn btn-info">
                                    <span class="upl" id="upload">Upload</span>
                                    <input type="file" accept="audio/*" class="upload up" id="up" name="audio[]" multiple/>
                                </span><!-- btn-orange -->
                            </div><!-- btn -->
                            <button class="btn btn-primary d-none">Submit</button>
                            </form>
                            </div><!-- group -->
                            </div><!-- form-group -->
                        </div>
                        </div>
                    </div>
                </div>
                </div>
                <!-- SOUNDS SECTION END -->
                <!-- BOOK COVER UPLOAD -->
                <div class="form-group edit-book-cover py-3 mt-5">
                    <span class="h6"><i class="fa fa-picture-o" aria-hidden="true"></i> Book Cover</span>
                    <div class="accordion mt-3" id="vbUploadBookCover">                        
                        <input type="text" src="" placeholder="" class="d-none form-control rdnly-plchldr" readonly>
                        <form method="POST" action="" id="submit-book-cover">
                            <div class="input-group-btn" style="margin-left:-2px;">
                                <span class="fileUpload btn btn-warning d-block">
                                    <span class="upl text-light" id="upload"><?php echo (is_integer($bookcover)) ? "Upload" : "Update" ; ?></span>
                                    <input type="hidden" name="book" value="">
                                    <input type="file" accept="image/*" class="upload up" id="upcover" name="book-cover[]"/>
                                </span><!-- btn-orange -->
                            </div><!-- btn -->
                            <button class="btn btn-primary d-none">Submit</button>
                        </form>
                        <img id="prev-img-bookcover" class="<?php echo (is_integer($bookcover)) ? "d-none" : "" ; ?> clearfix py-3" src="<?php echo $bookcover; ?>" alt="" />
                    </div>
                </div>
                <!-- BOOK COVER UPLOAD END -->
            </div>
        </div>
      </div>
      <div class="modal-footer">       
        <!-- <input id="vbcc-text" type="hidden" value=""> -->
        <button id="vb-addnew-section" data-key="<?php echo $key; ?>" type="button" class="btn btn-secondary px-3 mr-1">Add New Section</button>
        <button id="vb-save-styles" data-key="<?php echo $key; ?>" type="button" class="btn btn-primary px-3 mr-4">Update</button>
      </div>
    </div>
  </div>
  <audio src="" id="vb-prevAudio" class="d-none"></audio>

<script type="text/javascript">
jQuery(document).ready(function($){
    const chapterTitle = $(".ttl-<?php echo $chapter; ?>ch").text();
    //const bookk = $("input#vb-ttl-cdidtfyr").data("bookid");    
    $(".vb-ch-title").html(chapterTitle);
    $("#style-preview>span>ol, #style-preview>span>ul").parent("span").addClass("d-block");

    //SHOW ADD NEW SECTION BUTTON
    $(document).on("click","#vb-addnew-section",function(){
        //console.log(bookk);
        addSectionLightbox(chapterTitle,"<?php echo $file; ?>",<?php echo $chapter + 1; ?>,bookIndex);
    });

   //LOAD MY AUDIO
   loadMyAudio = function(){
        $.ajax({
            url: "../../model/media.php",  
            type: "POST",
            data: {action: "load", file: "<?php echo $file; ?>",key: "<?php echo $key; ?>"},
            dataType: "text",
            success: function(data){
                $("div#vb-my-audio").html(data);
            }
        });
    }

    loadMyAudio();
});
</script>
</div>
<div class="modal-backdrop show"></div>

<?php }