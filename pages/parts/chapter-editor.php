<?php
if(isset($_POST['chapter']) && isset($_POST['book'])){

    $chapter = $_POST['chapter']; 
    $bookKey = $_POST['book']; 
    $bgIMG = "";

    $bookInfo =  file_get_contents("../../json/books-list-title.json");
    $booklist = json_decode($bookInfo);  
    $booklist = $booklist[$bookKey];
    $chInfo = $booklist->chapter;
    $chInfo = json_decode($chInfo[$chapter]);
    $bgType = (!empty($chInfo->bgType)) ? $chInfo->bgType : "color";
    $background = (!empty($chInfo->background)) ? $chInfo->background : "#fff";
    if($chInfo->name === "Book Info"){
        $title = "<h1 class='text-center ch-main-title pt-5'>{$booklist->title}</h1> <p class='ch-subtitle text-center'>{$booklist->subtitle}</p>";
    }else{
        $title = $chInfo->name;
        $title = str_replace("<small class='vb-content-subtitle h6'>","</h1><p class='ch-subtitle text-center'>",$title);
        $title = str_replace("</small>","</p>",$title);
        $title = "<h1 class='text-center ch-main-title pt-5'>".$title;
    }   
    
    $pageTitle = ($chInfo->name !== "Book Info") ? $chInfo->name : $title;
    $dsounds = file_get_contents("../../json/media/default-sounds.json");
    $dsounds = json_decode($dsounds);  
    $mySounds = file_get_contents("../../json/users/user-sound.json");
    $mySounds = json_decode($mySounds);

    $defaultSound = (!empty($chInfo->sound)) ? $chInfo->sound : 0;
    $defaultVolume = (!empty($chInfo->volume)) ? $chInfo->volume : 0.5;
    if(!empty($defaultSound)){
        $a = str_replace("m","",$defaultSound);
        $actSound = $mySounds[$a];
        $alias = (strlen($actSound->alias) > 11) ? substr($actSound->alias,strlen($actSound->alias)-11) : $actSound->alias;
        $SoundID = $actSound->id;
    }else{
        $actSound = null;
        $SoundID = 0;
        $alias = "";
    }
    
?>

<div class="modal" id="vb-modal-editstyle" tabindex="-1" role="dialog" style="display:block">
  <div class="modal-editor-wrap" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Editor</h5>
        <div class="p-fixed">
          <span id="btn-stop" class="btn mr-2 text-danger d-none"><i class="fa fa-stop" aria-hidden="true"></i> Stop</span>
          <span class="btn mr-2 text-success preplay-section btn-play d-none" data-status="inactive" data-line="0" data-key="<?php echo $key; ?>" data-chapter="<?php echo $chapter; ?>"><i class="fa fa-play" aria-hidden="true"></i> Play</span>
          <span class="btn btn-light back-to-preview d-none" data-title="<?php echo $content[$key]->cpart; ?>" data-key="<?php echo $key; ?>" data-chapter="<?php echo $chapter; ?>"><i class="fa fa-eye pr-2" aria-hidden="true"></i> Preview</span>
          <button type="button" class="editstyle-close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
      </div>
      <div class="modal-body p-4">
        <div class="row">
            <div class="col-md-9 px-4">
                <div id="toolbar"></div>
                <div id="style-preview" class="editstyle-page py-2 px-4">
                    
                </div>
            </div>
            <div class="col-md-3 style-widgets-corner">
                <!-- BACKGROUND -->
                <div class="form-group text-center bgContainer">
                    <span class="h6 d-block py-3"><i class="fa fa-music" aria-hidden="true"></i> Backgound</span> 
                    <div class="custom-control custom-radio custom-control-inline" data-act="1">
                        <input type="radio" id="bgColor" name="color" class="custom-control-input" <?php echo ($bgType == "color") ? "checked" : ""; ?>>
                        <label class="custom-control-label" for="bgColor">Color</label>
                    </div>
                    <div class="custom-control custom-radio custom-control-inline" data-act="2">
                        <input type="radio" id="bgIMG" name="image" class="custom-control-input" <?php echo ($bgType == "image") ? "checked" : ""; ?>>
                        <label class="custom-control-label" for="bgIMG">Image</label>
                    </div>
                    <div class="colorPick-wrap <?php echo ($bgType != "color") ? "d-none" : ""; ?>">
                        <div id="colorPicker" class="d-none"></div>
                        <div id="pickerApp"></div>
                    </div>
                    <div class="imgPick-wrap <?php echo ($bgType != "image") ? "d-none" : ""; ?>">
                        <div id="imgBackground-preview-wrap"  class="py-3" >
                            <span class="float-right" style="display:none;" id="rm-image-background" aria-hidden="true">×</span>
                            <i class="fa fa-picture-o <?php echo ($bgType == "image") ? "d-none" : ""; ?>" aria-hidden="true"></i>
                            <img id="prev-img-background" class="clearfix <?php echo ($bgType != "image") ? "d-none" : ""; ?>" src="/media/background/<?php echo $background; ?>" alt="" />   
                        </div>
                        <div class="px-4" id="vbIMGbackground">
                            <input type="text" src="" placeholder="" class="d-none form-control rdnly-plchldr" readonly>
                            <form method="POST" action="" id="submit-background">
                                <div class="input-group-btn" style="margin-left:-2px;">
                                    <span class="fileUpload btn btn-warning d-block mx-2">
                                        <span class="upl text-light" id="upload"><?php echo (!is_integer($bgIMG)) ? "Upload" : "Update" ; ?></span>
                                        <input type="hidden" name="chapter" value="<?php echo $chapter; ?>">
                                        <input type="hidden" name="book" value="<?php echo $bookKey; ?>">
                                        <input type="file" accept="image/*" class="upload up" id="upbackground" name="background[]"/>
                                    </span><!-- btn-orange -->
                                </div><!-- btn -->
                                <button class="btn btn-primary d-none float-right" style="margin-top: -38px;">Save</button>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- SOUNDS SECTION -->
                <div class="form-group text-center sound-option-wrap">
                    <span class="h6"><i class="fa fa-music" aria-hidden="true"></i> Page Sounds</span>                    
                    <div class="vbSoundDemo form-group" id="vbSelectSounds">
                        <?php if($actSound === null){ $noSound = ""; $aSound = "d-none"; }
                              else{ $noSound = "d-none"; $aSound = ""; }?>
                        <h4 class="text-center p-3 <?php echo $noSound; ?>" style="font-weight:200;">No Media Sound!</h4>
                        <div id="vbMediaPlayerWrap" class="<?php echo $aSound; ?> py-3 px-2">
                            <div id="vbMyAudioWrap"><span id="vb-my-audio" class="slct-sounds-list act-sound h5" data-id="<?php echo $SoundID; ?>"><?php echo $alias; ?> <i class="fa fa-play" aria-hidden="true" data-dir="1" data-file="<?php echo $actSound->filename; ?>"></i></span></div>  
                            <div class="vb-volume-wrap"><i class="fa fa-volume-up" aria-hidden="true"></i> <input type="range" name="vb-volume-control" value="<?php echo $defaultVolume; ?>" min="0.0" max="1" step="0.01"></div>  
                        </div>
                        <div class="input-group">
                        <input type="text" class="form-control d-none rdnly-plchldr" readonly>
                        <form class="input-empty" id="submit-audio" method="post" action="">
                        <div class="input-group-btn" style="margin-left:-2px;">
                            <span class="fileUpload btn btn-info">
                                <span class="upl" id="upload">Upload</span>
                                <input type="file" accept="audio/*" class="upload up" id="up" name="audio[]"/>
                            </span><!-- btn-orange -->
                        </div><!-- btn -->
                        <button class="btn btn-primary d-none">Submit</button>
                        </form>
                        </div>
                    </div>
                </div>
                <!-- SOUNDS SECTION END -->
            </div>
        </div>
      </div>
      <div class="modal-footer">       
        <button id="vb-save-chPage" data-key="<?php echo $chapter; ?>" type="button" class="btn btn-primary px-3 mr-4">Update</button>
      </div>
    </div>
  </div>
  <audio src="" id="vb-prevAudio" class="d-none"></audio>

<script type="text/javascript">
jQuery(document).ready(function($){
    let chapterTitle = $(".ttl-<?php echo $chapter; ?>ch").text();
    let title = `<?php echo $title ?>`;     
    //let contentKey = <?php //echo $key; ?>;

    //QUILL EDITOR
    let container = document.getElementById('style-preview');
    let editor = QuillEditor(container,false,false);

    setTimeout(function(){
        $("div.ql-editor").html(title);
    },1000);

    //COLOR PICKER
    <?php

    $defaultColor = ($bgType == "color") ? $background : "#fff";
    echo "let defaultColor = '{$defaultColor}';";

    ?>
    const pickr = Pickr.create({
        el: '#colorPicker',
        container: '#pickerApp',
        theme: 'nano', // or 'monolith', or 'nano'
        showAlways: true,
        position: 'top-start',
        useAsButton: false,
        inline: true,
        autoReposition: false,
        default: defaultColor,

        components: {

            // Main components
            preview: true,
            opacity: true,
            hue: true,

            // Input / output Options
            interaction: {
                hex: true,
                rgba: true,
                input: true,
                //save: true
            },

        }
    });

    pickr.on('save', (color, instance) => {
        //console.log('save', color, instance);
        let value = $("div.colorPick-wrap input.pcr-result").val();
        let key = $("#vb-full-title").data("book");
        saveBG(key,"color",value);
    }).on('change', (color, instance) => {
        //console.log('change', color, instance);
        let value = $("div.colorPick-wrap input.pcr-result").val();
        $("div#style-preview").css("background",value);
    });

    //LOAD MY AUDIO
    // const loadMyAudio = function(){
    //     $.ajax({
    //         url: "../../model/media.php",  
    //         type: "POST",
    //         data: {action: "load", file: "<?php //echo $file; ?>",key: "<?php //echo $key; ?>"},
    //         dataType: "text",
    //         success: function(data){
    //             $("div#vb-my-audio").html(data);
    //         }
    //     });
    // }

    // loadMyAudio();

    setTimeout(function(){        
        <?php if($bgType == "color"){ ?>
            let bgColor = $("div.colorPick-wrap input.pcr-result").val();
            $("div#style-preview").css("background",bgColor);
        <?php }else{ ?>
            $("div#style-preview").css("background","url('../../media/background/<?php echo $background; ?>')");
        <?php } ?>
    },500);
        

});
</script>
</div>
<div class="modal-backdrop show"></div>

<?php }