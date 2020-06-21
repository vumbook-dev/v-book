<?php
if(isset($_POST['content']) && isset($_POST['title']) && isset($_POST['chapter']) && isset($_POST['file'])){

    $title = $_POST['title'];
    $key = $_POST['content'];
    $chapter = $_POST['chapter'];
    $file = $_POST['file'];

    $list = file_get_contents("../../json/book-content/{$file}.json");
    $content = json_decode($list);
    $dsounds = file_get_contents("../../json/media/default-sounds.json");
    $dsounds = json_decode($dsounds);
    
?>

<div class="modal" id="vb-modal-editstyle" tabindex="-1" role="dialog" style="display:block">
  <div class="modal-editor-wrap" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Styles</h5>
        <div class="p-fixed">
          <span id="btn-stop" class="btn mr-2 text-danger d-none"><i class="fa fa-stop" aria-hidden="true"></i> Stop</span>
          <span class="btn mr-2 text-success preplay-section btn-play d-none" data-status="inactive" data-line="0" data-key="<?php echo $key; ?>" data-chapter="<?php echo $chapter; ?>"><i class="fa fa-play" aria-hidden="true"></i> Play</span>
          <span class="d-inline-block btn btn-light back-to-preview" data-title="<?php echo $content[$key]->cpart; ?>" data-key="<?php echo $key; ?>" data-chapter="<?php echo $chapter; ?>"><i class="fa fa-chevron-left pr-2" aria-hidden="true"></i> Back to preview</span>
          <button type="button" class="editstyle-close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
      </div>
      <div class="modal-body p-4">
        <div class="row">
            <div class="col-md-9 px-4">
                <h5 class="text-center mb-5"><?php echo $title ?></h5>
                <div id="style-preview" class="editstyle-page py-2 px-4">
                    <?php echo $content[$key]->content; ?>
                </div>
            </div>
            <div class="col-md-3 style-widgets-corner">
                
                <div class="form-group text-center">
                    <span class="h6"><i class="fa fa-music" aria-hidden="true"></i> Page Sounds</span>
                    <!-- SOUNDS SECTION -->
                    <div class="accordion mt-3" id="vbSelectSounds">
                    <div id="default-sounds" class="card">
                        <div class="card-header p-0" id="headingOne">
                        <h5 class="mb-0">
                            <button class="btn" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                Default Sounds
                            </button>
                        </h5>
                        </div>

                        <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#vbSelectSounds">
                        <div class="card-body p-0">
                            <ul class="mb-0 p-0 slct-sounds">
                                <?php 
                                    foreach($dsounds as $k => $value){
                                        $icon = ($value->id != 0) ? '<i class="fa fa-play px-3" aria-hidden="true" data-file="'.$value->filename.'"></i></li>' : ' ';
                                        $activeSound = ($value->id == $content[$key]->sound) ? 'act-sound' : '';
                                        echo '<li class="'.$activeSound.'" data-id="'.$value->id.'">'.$value->alias.' '.$icon;
                                    }
                                ?>
                                <li data-id="0">City Noise <i class="fa fa-play px-3" aria-hidden="true"></i></li>
                                <li data-id="0">Good Chat <i class="fa fa-play px-3" aria-hidden="true"></i></li>
                                <li data-id="0">Wind Blowing <i class="fa fa-play px-3" aria-hidden="true"></i></li>
                                <li data-id="0">Sweet Night <i class="fa fa-play px-3" aria-hidden="true"></i></li>
                            </ul>
                        </div>
                        </div>
                    </div>

                    <div id="personal-sounds" class="card">
                        <div class="card-header p-0" id="headingTwo">
                        <h5 class="mb-0">
                            <button class="btn collapsed" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                My Media Sounds
                            </button>
                        </h5>
                        </div>

                        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#vbSelectSounds">
                        <div class="card-body p-4" style="height:auto;">
                            No Media Available!
                        </div>
                        </div>
                    </div>
                    </div>
                    <!-- SOUNDS SECTION END -->
                </div>
            </div>
        </div>
      </div>
      <div class="modal-footer">       
        <!-- <input id="vbcc-text" type="hidden" value=""> -->
        <button id="vb-save-styles" data-key="<?php echo $key; ?>" type="button" class="btn btn-primary px-3 mr-4">Update</button>
      </div>
    </div>
  </div>

<script type="text/javascript">
jQuery(document).ready(function($){

    $("#style-preview>span>ol, #style-preview>span>ul").parent("span").addClass("d-block");

    $(document).on("click",".editstyle-close",function(){
        $("#vb-modal-container div").remove();
    });

    //SCROLLBAR
    $("#style-preview, #default-sounds div.card-body, #personal-sounds div.card-body").mouseenter(function() {
        $(this).addClass("scrollMouseIn");
    })
    .mouseleave(function() {
        $(this).removeClass("scrollMouseIn");
    });

    $(document).on("click",".slct-sounds > li", function(e){
        SoundStatus = $(this).hasClass("act-sound");

        if(!SoundStatus){
            $(".slct-sounds > li").removeClass("act-sound");            
            $(this).addClass("act-sound");
        }    
        
        $(this).removeEventListener('click', e);           
    });

    const PlayStop = function(Sound,drtn){
        Sound.play();           
        setTimeout(function(){
            $(".slct-sounds > li > i").removeClass("fa-pause");
            $(".slct-sounds > li > i").addClass("fa-play");
            Sound.pause();                
        },drtn);
        console.log(drtn);
    }

    $(document).on("click",".slct-sounds > li > i", function(e){
        File = $(this).data("file");
        Sound = null;        
        if(!$(this).hasClass("fa-play")){
            $(this).find("i").removeClass("fa-pause");
            $(this).find("i").addClass("fa-play");
            Sound.pause();
        }else{
            Sound = new Audio('../../media/sounds/'+File);
            let drtn = 2500;
            $(".slct-sounds > li i").removeClass("fa-pause");
            $(".slct-sounds > li i").addClass("fa-play");
            $(this).toggleClass("fa-pause");
            PlayStop(Sound,drtn);            
        }
        //$(this).removeEventListener('click', e);
    });

    //UPDATE STYLE
    const UpdateStyle = function(sound,key,file,bg){
        $.ajax({
            method: "POST",
            url: "../model/content.php",
            data: {sound:sound,key:key,file:file,bg:bg,action:"update"},
            dataType: "text",
            success: function(data){
                let json = JSON.parse(data);
                let state = (json.status == "success") ? "alert-success" : "alert-danger";
                $("div.style-widgets-corner").prepend('<div class="message-status alert '+state+'" role="alert">'+json.message+'</div>');
                setTimeout(function(){
                    $("div.message-status").remove();
                },3000);
            }
        })
    }

    $("#vb-save-styles").click(function(){
        let key = $(this).data("key");
        let actSound = $("li.act-sound").data("id");
        let file = $("#vb-ttl-cdidtfyr").data("universal");
        let bg = 0;

        UpdateStyle(actSound,key,file,bg);
    });

});
</script>
</div>
<div class="modal-backdrop show"></div>

<?php }