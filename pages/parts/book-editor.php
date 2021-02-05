<?php
require_once "../../config.php";
if(isset($_COOKIE['userdata'])){
    $UID = $_COOKIE['userdata']['id'];
    $UName = $_COOKIE['userdata']['name'];
    $UFolder = DATAPATH;
    if(isset($_POST['content']) && isset($_POST['title']) && isset($_POST['chapter']) && isset($_POST['file']) && isset($_POST['bookKey'])){

        $title = $_POST['title'];
        $key = $_POST['content'];
        $chapter = $_POST['chapter'];
        $file = $_POST['file'];   
        $bookKey = $_POST['bookKey']; 
        $bgIMG = "";

        $list = file_get_contents("../../json/users/bookdata/{$UFolder}/book-content/{$file}.json");    
        $content = json_decode($list);
        $bookInfo =  file_get_contents("../../json/users/bookdata/{$UFolder}/books-list-title.json");
        $booklist = json_decode($bookInfo);  
        $chInfo = $content[$key];
        $bgType = (!empty($chInfo->bgType)) ? $chInfo->bgType : "color";
        $background = (!empty($chInfo->background)) ? $chInfo->background : "#fff";
        //print_r($chInfo);
        
        $dsounds = file_get_contents("../../json/media/default-sounds.json");
        $dsounds = json_decode($dsounds);  
        $mySounds = file_get_contents("../../json/users/bookdata/{$UFolder}/media/user-sound.json");
        $mySounds = json_decode($mySounds);

        $defaultSound = $content[$key]->sound;
        $defaultVolume = (!empty($content[$key]->volume)) ? $content[$key]->volume : 0.5;
        if($defaultSound){
            $a = str_replace("m","",$defaultSound);
            $actSound = $mySounds[$a];
            $alias = (strlen($actSound->alias) > 11) ? substr($actSound->alias,-strlen($actSound->alias),11)."..." : $actSound->alias;
            $SoundID = $actSound->id;
        }else{
            $actSound = null;
            $SoundID = 0;
        }
        $delay = (!empty($content[$key]->delay)) ? $content[$key]->delay : 1;
        
    ?>

    <div class="modal" id="vb-modal-editstyle" tabindex="-1" role="dialog" style="display:block">
    <div class="modal-editor-wrap" role="document">
        <div class="modal-content">
        <div class="modal-header">            
            <div class="float-right">
            <span id="btn-stop" class="btn mr-2 text-danger d-none"><i class="fa fa-stop" aria-hidden="true"></i> Stop</span>
            <span class="btn mr-2 text-success preplay-section btn-play d-none" data-status="inactive" data-line="0" data-key="<?php echo $key; ?>" data-chapter="<?php echo $chapter; ?>"><i class="fa fa-play" aria-hidden="true"></i> Play</span>
            <span class="btn btn-light back-to-preview d-none" data-title="<?php echo $content[$key]->cpart; ?>" data-key="<?php echo $key; ?>" data-chapter="<?php echo $chapter; ?>"><i class="fa fa-eye pr-2" aria-hidden="true"></i> Preview</span>
            <button type="button" class="editstyle-close text-danger" data-dismiss="modal" aria-label="Close">
            <i class="fa fa-times-circle" aria-hidden="true"></i>
            </button>
            </div>
            <button id="vb-save-styles" data-key="<?php echo $key; ?>" type="button" class="btn btn-primary px-3 mr-4 d-none">Update</button>
            <h5 class="modal-title">Book Editor</h5>            
        </div>
        <div class="modal-body p-4">
            <div class="row">
                <div class="col-md-9" style="background-color: #454545; max-height: 75vh; overflow-y: scroll;">
                <?php $hide = (count($content[$key]->content) < 1) ? "d-none" : ""; ?>
                <div id="btmpZoomControl" class="pb-4 pt-2 <?php echo $hide; ?>">
                    <span id="btmp-zoomvalue">120%</span> <input type="range" id="btmp-sliderzoomer" value="4" min="0" max="6" step="2">
                </div>
                <div class="pr-3 <?php echo $hide; ?>" id="btmp-action-control-wrap">
                    <span class="btn btn-primary btmp-ed-action" data-key="0"><i class='bx bx-edit'></i> Edit Page <span>1</span></span>
                    <span class="btn btn-danger btmp-ed-action" data-key="0"><i class='bx bx-trash'></i> Delete Page <span>1</span></span>
                    <span class="btn btn-primary btmp-save-wrap d-none" data-key="0"><i class='bx bx-save'></i> Save</span>
                    <span class="btn btn-secondary btmp-cancel-wrap d-none"><i class='bx bx-x'></i> Cancel</span>
                </div>
                    <div class="btmp-bg <?php echo $hide; ?>" id="bookTemplateEditorWrap">
                        <div class="btmp-editor-wrap bookTempZoomer">
                            <div class="d-none">
                                <div id="toolbar"></div>
                                <span class="charLimiter">1300 / <span>1300</span></span>
                                <div id="style-preview" class="editstyle-page"></div>
                            </div>
                            <div class="btmp-page">
                                    <ul class="btmp-pagelist">
                                        <?php
                                        $np = "";
                                            foreach($content[$key]->content as $xy => $val){
                                                $n = json_decode($val);
                                                $active = ($xy == 0) ? "btmp-active" : "";
                                                $textIndex = $xy+1;
                                                $np = $n->id;
                                                echo "<li class='btmp-pages {$active}' data-pageid='{$np}' data-key='{$xy}'>{$textIndex}</li>";
                                            }
                                        ?>
                                        <li id="btmp-add-page-btn" data-lastKey="<?php echo $np; ?>"><i class='bx bx-plus p-relative' style="left:3px"></i></li>
                                    </ul>
                                    <div class="ql-snow">
                                        <?php
                                            foreach($content[$key]->content as $xy => $val){
                                                $n = json_decode($val);
                                                $active = ($xy != 0) ? "d-none" : "";
                                                echo '<div class="ql-editor btmp-content btmpPage'.$xy.' '.$active.'" data-key="'.$xy.'">'.$n->text.'</div>';
                                            }
                                        ?>
                                    </div>                  
                            </div>
                        </div>                 
                    </div>
                    <div class="bmtp-startpage text-center <?php echo (empty($hide)) ? "d-none" : ""; ?>">
                        <label class="text-muted" for="Start New Page">Add Page to Start</label>
                        <span class="btn btn-success">Add New Page</span>
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
                                <img id="prev-img-background" class="clearfix <?php echo ($bgType != "image") ? "d-none" : ""; ?>" src="/media/page-background/<?php echo $UFolder."/".$background; ?>" alt="" />   
                            </div>
                            <div class="px-4" id="vbIMGbackground">
                                <input type="text" src="" placeholder="" class="d-none form-control rdnly-plchldr" readonly>
                                <form method="POST" action="" id="submit-background">
                                    <div class="input-group-btn" style="margin-left:-2px;">
                                        <span class="fileUpload btn btn-warning d-block mx-2">
                                            <span class="upl text-light" id="upload"><?php echo (!is_integer($bgIMG)) ? "Upload" : "Update" ; ?></span>
                                            <input type="hidden" name="section" value="<?php echo $key; ?>">
                                            <input type="hidden" name="book" value="<?php echo $bookKey; ?>">
                                            <input type="hidden" name="file" value="<?php echo $file; ?>">
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
                                <div id="vbMyAudioWrap"><span id="vb-my-audio" class="slct-sounds-list act-sound h5" data-id="<?php echo $SoundID; ?>"><?php echo $alias; ?> <i class="fa fa-play" aria-hidden="true" data-dir="1" data-file="<?php echo $actSound->filename; ?>"  data-path="<?php echo $UFolder; ?>"></i></span></div>  
                                <div class="vb-volume-wrap"><i class="fa fa-volume-up" aria-hidden="true"></i> <input type="range" name="vb-volume-control" value="<?php echo $defaultVolume; ?>" min="0.0" max="1" step="0.01"></div>  
                            </div>
                            <div class="py-3 px-5 delay-wrap <?php echo $aSound; ?>"><span class="p-2 mx-0 h5">Sound Delay : </span><input type="number" name="delay" value="<?php echo $delay; ?>" min="1" max="100" class="form-control py-0"></div>
                            <div class="input-group">
                            <input type="text" class="form-control d-none rdnly-plchldr" readonly>
                            <form class="input-empty" id="submit-audio" method="post" action="">
                            <div class="input-group-btn" style="margin-left:-2px;">
                                <span class="fileUpload btn btn-info">
                                    <span class="upl" id="upload">Upload</span>
                                    <input type="file" accept="audio/*" class="upload up" id="up" name="audio[]"/>
                                </span><!-- btn-orange -->
                                <span class="save_changes btn btn-primary d-none" style="width:100%;">
                                    Save Changes
                                </span>
                            </div><!-- btn -->
                            <button id="vb-sound-upload" class="btn btn-primary d-none">Submit</button>
                            </form>
                            </div>
                        </div>
                    </div>
                    <!-- SOUNDS SECTION END -->
                </div>
            </div>
        </div>
        <div class="modal-footer">       
            <!-- <input id="vbcc-text" type="hidden" value=""> -->
            <!-- <button id="vb-addnew-section" data-key="<?php //echo $key; ?>" type="button" class="btn btn-secondary px-3 mr-1">Add New Section</button> -->            
        </div>
        </div>
    </div>
    <audio src="" id="vb-prevAudio" class="d-none"></audio>

    <script type="text/javascript">
    jQuery(document).ready(function($){
    let chapterTitle = $(".ttl-<?php echo $chapter; ?>ch").text();
    let title = "<?php echo $title ?>";     
    let contentKey = <?php echo $key; ?>;

    // QUILL EDITOR
    let container = document.getElementById('style-preview');
    let editor = QuillEditor(container,1300);

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
                hex: false,
                rgba: false,
                input: true,
                save: false
            },

        }
    });

    pickr.on('save', (color, instance) => {
        window.saveBG();
    }).on('change', (color, instance) => {
        //console.log('change', color, instance);
        let value = $("div.colorPick-wrap input.pcr-result").val();
        $('div.colorPick-wrap input.pcr-save').addClass('pckrbtn');
        $("div#style-preview div.ql-editor, div.ql-editor.btmp-content").css("background-color",value);
    });

    setTimeout(function(){            
        <?php if($bgType == "color"){ ?>
            let bgColor = $("div.colorPick-wrap input.pcr-result").val();
            $("div#style-preview div.ql-editor, div.ql-editor.btmp-content").css("background",bgColor);
        <?php }else{ ?>
            $("div#style-preview div.ql-editor, div.ql-editor.btmp-content").css("background","url('../../media/page-background/<?php echo $UFolder."/".$background; ?>')");
        <?php } ?>
    },500);
            

    //ZOOMER
    $(document).on('input', '#btmp-sliderzoomer', function(){
        let value = $(this).val();
        let mrgn;
        let zoom;
        switch(parseInt(value)){
            case 0: zoom = 60; mrgn = 0; break;
            case 2: zoom = 80; mrgn = 0; break;
            case 4: zoom = 120; mrgn = 9; break;
            case 6: zoom = 160; mrgn = 9; break;
        }
        $("div#bookTemplateEditorWrap").css("zoom",zoom+"%");
        $("div#bookTemplateEditorWrap").css({"-moz-transform":"scale("+zoom+"%,"+zoom+"%)","-moz-transform-origin":"top"});
        $("div#bookTemplateEditorWrap").css({"-ms-transform":"scale("+zoom+"%,"+zoom+"%)","-ms-transform-origin":"top"});
        $("div#bookTemplateEditorWrap").css("-webkit-zoom",zoom+"%");
        $("div.btmp-editor-wrap").css("margin-top",mrgn+"%");
        $("span#btmp-zoomvalue").text(zoom+"%");
    });
});

    </script>
    </div>
    <div class="modal-backdrop show"></div>
    <?php }
}