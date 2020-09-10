<?php
if(isset($_POST['content']) && isset($_POST['title']) && isset($_POST['chapter']) && isset($_POST['file']) && isset($_POST['cover']) && isset($_POST['bookKey'])){

    $title = $_POST['title'];
    $key = $_POST['content'];
    $chapter = $_POST['chapter'];
    $file = $_POST['file'];   
    $bookKey = $_POST['bookKey']; 
    $bgIMG = "";

    $list = file_get_contents("../../json/book-content/{$file}.json");    
    $bookInfo =  file_get_contents("../../json/books-list-title.json");
    $bookInfo = json_decode($bookInfo,true);
    $bookInfo = $bookInfo[$bookKey];
    $content = json_decode($list);
    // if(isset($_POST['cover'])){
    //     $coverkey = $_POST['cover'];
    //     $cover = file_get_contents("../../json/users/user-bookcover.json");
    //     $cover = json_decode($cover);
    //     $bookcover = "../../media/bookcover/user/{$cover[$coverkey]->filename}";
    // }
    
    $dsounds = file_get_contents("../../json/media/default-sounds.json");
    $dsounds = json_decode($dsounds);  

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
            <div class="col-md-9 px-4 <?php //echo "text-{$content[$key]->align}"; ?>">
                <!-- <h3 class="vb-ch-title mb-3"></h3>
                <h5 class="mb-5"></h5> -->
                <div id="toolbar"></div>
                <div id="style-preview" class="editstyle-page py-2 px-4">
                    
                </div>
            </div>
            <div class="col-md-3 style-widgets-corner">
                <!-- BACKGROUND -->
                <div class="form-group text-center bgContainer">
                    <span class="h6 d-block py-3"><i class="fa fa-music" aria-hidden="true"></i> Backgound</span> 
                    <div class="custom-control custom-radio custom-control-inline" data-act="1">
                        <input type="radio" id="bgColor" name="color" class="custom-control-input" <?php echo ($bookInfo['bgType'] == "color") ? "checked" : ""; ?>>
                        <label class="custom-control-label" for="bgColor">Color</label>
                    </div>
                    <div class="custom-control custom-radio custom-control-inline" data-act="2">
                        <input type="radio" id="bgIMG" name="image" class="custom-control-input" <?php echo ($bookInfo['bgType'] == "image") ? "checked" : ""; ?>>
                        <label class="custom-control-label" for="bgIMG">Image</label>
                    </div>
                    <div class="colorPick-wrap <?php echo ($bookInfo['bgType'] != "color") ? "d-none" : ""; ?>">
                        <div id="colorPicker" class="d-none"></div>
                        <div id="pickerApp"></div>
                    </div>
                    <div class="imgPick-wrap <?php echo ($bookInfo['bgType'] != "image") ? "d-none" : ""; ?>">
                        <div id="imgBackground-preview-wrap"  class="py-3 mx-5" >
                            <i class="fa fa-picture-o <?php echo ($bookInfo['bgType'] == "image") ? "d-none" : ""; ?>" aria-hidden="true"></i>
                            <img id="prev-img-background" class="clearfix <?php echo ($bookInfo['bgType'] != "image") ? "d-none" : ""; ?>" src="/media/background/<?php echo $bookInfo['bgValue']; ?>" alt="" />   
                        </div>
                        <div class="px-4" id="vbIMGbackground">
                            <input type="text" src="" placeholder="" class="d-none form-control rdnly-plchldr" readonly>
                            <form method="POST" action="" id="submit-background">
                                <div class="input-group-btn" style="margin-left:-2px;">
                                    <span class="fileUpload btn btn-warning d-block mx-5">
                                        <span class="upl text-light" id="upload"><?php echo (!is_integer($bgIMG)) ? "Upload" : "Update" ; ?></span>
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
            </div>
        </div>
      </div>
      <div class="modal-footer">       
        <!-- <input id="vbcc-text" type="hidden" value=""> -->
        <!-- <button id="vb-addnew-section" data-key="<?php echo $key; ?>" type="button" class="btn btn-secondary px-3 mr-1">Add New Section</button> -->
        <button id="vb-save-styles" data-key="<?php echo $key; ?>" type="button" class="btn btn-primary px-3 mr-4">Update</button>
      </div>
    </div>
  </div>
  <audio src="" id="vb-prevAudio" class="d-none"></audio>

<script type="text/javascript">
jQuery(document).ready(function($){
    const chapterTitle = $(".ttl-<?php echo $chapter; ?>ch").text();
    const title = "<?php echo $title ?>";     
    const contentKey = <?php echo $key; ?>;

    // QUILL EDITOR
    let container = document.getElementById('style-preview');
    let toolbarOptions = [
        [{ 'font': [] }],
        [{ 'align': [] }],
        ['bold', 'italic', 'underline', 'strike'],        // toggled buttons
        [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
        ['blockquote'],
        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
        [{ 'script': 'sub'}, { 'script': 'super' }],      // superscript/subscript
        [{ 'indent': '-1'}, { 'indent': '+1' }],          // outdent/indent
        [{ 'direction': 'rtl' }],                         // text direction
        [{ 'color': [] }, { 'background': [] }],         // dropdown with defaults from theme
        ['link','image','video']
        //['clean']                                         // remove formatting button
        ];
    let options = {
        debug: false,
        modules: {
            toolbar: toolbarOptions
        },
        placeholder: false,
        readOnly: false,
        theme: 'snow',
        scrollingContainer: false,
    };
    let editor = new Quill(container,options);  

    const getContent = function(key){
        $.ajax({
            url: "../../json/book-content/<?php echo $file; ?>.json",
            method: "GET",
            dataType: "json",
            success: function(data){
                let dataContent = data[key]['content'];
                dataContent = JSON.parse(dataContent);
                editor.setContents(dataContent);
                //console.log(dataContent);
                //console.log(key);
            }
        });
    }

    setTimeout(function(){
        window.Quillcontents = editor.getContents();
    },1000);
    
    editor.on('text-change', function(delta, oldDelta, source) {
    if (source == 'api') {
        console.log("An API call triggered this change.");
        //console.log(delta);
    } else if (source == 'user') {
        console.log("A user action triggered this change."); 
        window.Quillcontents = editor.getContents();             
    }
    });

    //COLOR PICKER
    <?php
        $defaultColor = ($bookInfo["bgType"] == "color") ? $bookInfo['bgValue'] : "#fff";
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
                save: true
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

    loadMyAudio();

    setTimeout(function(){
        <?php if(!empty($content[$key]->content)){ ?>
            getContent(contentKey);
        <?php }else{ ?>
            $("div#style-preview div.ql-editor").prepend("<h1><strong>"+title+"</strong></h1>");
        <?php } ?>
        
        <?php if($bookInfo['bgType'] == "color"){ ?>
            let bgColor = $("div.colorPick-wrap input.pcr-result").val();
            $("div#style-preview").css("background",bgColor);
        <?php }else{ ?>
            $("div#style-preview").css("background","url('../../media/background/<?php echo $bookInfo['bgValue']; ?>')");
        <?php } ?>
    },500);
        

});
</script>
</div>
<div class="modal-backdrop show"></div>

<?php }