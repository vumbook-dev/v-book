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
                <h3 class="vb-ch-title px-4 mb-3"></h3>
                <h5 class="px-4 mb-5"><?php echo $title ?></h5>
                <div id="style-preview" class="editstyle-page py-2 px-4">
                    <?php echo $content[$key]->content; ?>
                </div>
            </div>
            <div class="col-md-3 style-widgets-corner">
                <div id="vb-edit-align" class="form-group text-center mb-3 pb-2">
                    <span class="h6"><i class="fa fa-align-justify" aria-hidden="true"></i> Text Align</span>    
                    <ul class="my-3 vb-text-alignment">
                        <li data="left" <?php echo ($textAlign == "left") ? 'class="act-align btn"' : '' ; ?> > Left </li>
                        <li data="center" <?php echo ($textAlign == "center") ? 'class="act-align btn"' : '' ; ?> > Center </li>
                        <li data="right" <?php echo ($textAlign == "right") ? 'class="act-align btn"' : '' ; ?>> Right </li>
                    </ul>
                </div>
                <div class="form-group text-center">
                    <span class="h6"><i class="fa fa-music" aria-hidden="true"></i> Page Sounds</span>
                    <!-- SOUNDS SECTION -->
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
                                        $activeSound = ($value->id === $content[$key]->sound) ? 'act-sound' : '';
                                        echo '<li class="'.$activeSound.'" data-id="'.$value->id.'">'.$value->alias.' '.$icon;
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
                    <!-- SOUNDS SECTION END -->
                </div>
            </div>
        </div>
      </div>
      <div class="modal-footer">       
        <!-- <input id="vbcc-text" type="hidden" value=""> -->
        <button id="vb-new-section" data-key="<?php echo $key; ?>" type="button" class="btn btn-secondary px-3 mr-1">Add New Section</button>
        <button id="vb-save-styles" data-key="<?php echo $key; ?>" type="button" class="btn btn-primary px-3 mr-4">Update</button>
      </div>
    </div>
  </div>

<script type="text/javascript">
jQuery(document).ready(function($){
    const chapterTitle = $(".ttl-<?php echo $chapter; ?>ch").text();
    const bookk = $("input#vb-ttl-cdidtfyr").data("bookid");
    $(".vb-ch-title").html(chapterTitle);
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
        let File = $(this).data("file");
        let dir = $(this).data("dir");
        let path = (dir == 1) ? "user/" : "";
        let Sound = null;        
        if(!$(this).hasClass("fa-play")){
            $(this).find("i").removeClass("fa-pause");
            $(this).find("i").addClass("fa-play");
            Sound.pause();
        }else{
            Sound = new Audio('../../media/sounds/'+path+File);
            let drtn = 2500;
            $(".slct-sounds > li i").removeClass("fa-pause");
            $(".slct-sounds > li i").addClass("fa-play");
            $(this).toggleClass("fa-pause");
            PlayStop(Sound,drtn);            
        }
        //$(this).removeEventListener('click', e);
    });

    //UPDATE STYLE
    const UpdateStyle = function(sound,key,file,bg,align){
        $.ajax({
            method: "POST",
            url: "../model/content.php",
            data: {sound:sound,key:key,file:file,bg:bg,align:align,action:"update"},
            dataType: "text",
            success: function(data){
                let json = JSON.parse(data);
                let state = (json.status == "success") ? "alert-success" : "alert-danger";
                $("div.style-widgets-corner").prepend('<div class="message-status alert '+state+'" role="alert">'+json.message+'</div>');
                //$("#vb-new-section").removeClass("d-none");
                setTimeout(function(){
                    $("div.message-status").remove();                    
                },3000);
            }
        })
    }

    $(document).on("click",".vb-text-alignment > li",function(){
        let act = $(".vb-text-alignment > li.act-align").attr("data");
        let alignTxt = $(this).attr("data");        
        let element = $(".modal-body > .row > div.text-"+act);
        element.removeClass("text-"+act);
        element.addClass("text-"+alignTxt);        
        $(".vb-text-alignment > li").removeClass("act-align btn");
        $(this).addClass("act-align btn");
    });

    //SHOW ADD NEW SECTION BUTTON
    $(document).on("click","#vb-new-section",function(){
        //console.log(bookk);
        addSectionLightbox(chapterTitle,"<?php echo $file; ?>",<?php echo $chapter + 1; ?>,bookk);
    });

    // const UpdateAlignSpeed = function(align){
    //     $.ajax({
    //         method: "POST",
    //         url: "../../model/books.php",
    //         data: {align:align,action:"update"},
    //         dataType: "text",
    //         success: function(){

    //         }
    //     })
    // }

    $("#vb-save-styles").click(function(){
        let key = $(this).data("key");
        let actSound = $("li.act-sound").data("id");
        let file = $("#vb-ttl-cdidtfyr").data("universal");
        let align = $(".vb-text-alignment > li.act-align").attr("data");
        let bg = 0;

        UpdateStyle(actSound,key,file,bg,align);
    });

    //MULTIPLE SOUND UPLOADS
    $(document).on('change','#vb-sound-upload .up', function(){
        let names = [];
        let length = $(this).get(0).files.length;
        let uploader = $(this).parents("span.fileUpload");
        let submit = $("#vb-sound-upload button.btn-primary");
        let fileExtension = ['mp3', 'wav'];
        if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
            $("div.style-widgets-corner").prepend('<div class="message-status alert alert-danger" role="alert">Please Upload mp3 or wav format only</div>');
            setTimeout(function(){
                $("div.message-status").remove();
            },4000);
        }else{
            for (var i = 0; i < $(this).get(0).files.length; ++i) {
                names.push($(this).get(0).files[i].name);
            }
            // $("input[name=file]").val(names);
            if(length>1){
                var fileName = names.join(', ');
                $(this).closest('.form-group').find('.form-control').attr("value",length+" files selected");
            }
            else{
                $(this).closest('.form-group').find('.form-control').attr("value",names);
            }

            uploader.addClass("d-none");
            submit.removeClass("d-none");
            $("form.input-empty").removeClass("input-empty");
            $("input.rdnly-plchldr").removeClass("d-none");

            $(this).unbind();
        }
            
    });

    //LOAD MY AUDIO
    const loadMyAudio = function(){
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

    //SUBMIT MULTI SOUND
    $(document).on("submit","#submit-audio",function(e){
        e.preventDefault();
        window.selfsubmit = $.ajax({  
            url: "../../model/media.php",  
            type: "POST",  
            data: new FormData(this),  
            contentType: false,  
            processData:false,  
            success: function(){                
                $("div.style-widgets-corner").prepend('<div class="message-status alert alert-success" role="alert">Audio Successfully Uploaded</div>');
                setTimeout(function(){
                    $("div.message-status").remove();
                },4000);
                $("form#submit-audio").addClass("input-empty");
                $("input.rdnly-plchldr").addClass("d-none");
                $("form#submit-audio button.btn-primary").addClass("d-none");
                $("span.fileUpload").removeClass("d-none");
                //$(this).removeEventListener();
                loadMyAudio();
                selfsubmit.clear();                      
            }  
        }); 
    });

    loadMyAudio();
});
</script>
</div>
<div class="modal-backdrop show"></div>

<?php }