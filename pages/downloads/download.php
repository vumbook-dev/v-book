<?php 
if(isset($_POST['book']) && isset($_POST['chapters']) && isset($_POST['title']) && isset($_POST['subtitle']) && isset($_POST['file'])){
    $book = $_POST['book'];
    $chapters = $_POST['chapters'];
    //$chapters = str_replace('"',"\'",$chapters);
    //$chapters = str_replace('<',"{",$chapters);
    //$chapters = str_replace('>',"}",$chapters);
    $chapters = explode('|,', $chapters);
    //$chapters = json_encode($chapters);       
    $title = $_POST['title'];
    $subtitle = $_POST['subtitle'];
    $file = $_POST['file'];
    $dsound = file_get_contents("../../json/media/default-sounds.json");
    $dsound = json_decode($dsound);
    $msound = file_get_contents("../../json/users/user-sound.json");
    $msound = json_decode($msound);
    unlink("../../pages/downloads/$title.zip");
    $content = file_get_contents("../../json/book-content/{$file}.json");
    $content = json_decode($content);
    $count = count($content);
    $header = file_get_contents("../../json/users/template/header.php");
    $footer = file_get_contents("../../json/users/template/footer.php");
    $filesArray = array(
        "css/style.css", 
        "css/bootstrap.min.css",
        "css/bootstrap.min.css.map",
        "css/font-awesome.css",
        "css/font-awesome.min.css",
        "js/jquery.min.js"           
    );
    $soundArray = array();

    $html = $header;
    $html .= '<main role="main" class="container pb-5"><section class="row" id="vb-show-content">';
    $html .= '
    <div class="col-md-8"><div id="book-container">';
    //CONTENT
    foreach($content as $key => $value){
        $html .= '
        <div id="c'.$value->chapter.'s'.$key.'" class="px-4 py-5 mt-3 text-'.$value->align;
        $html .= ($key != 0) ? " d-none" : "";
        $html .= '">
            <h3 class="vb-ch-title px-4 mb-5">'.$title.' '.$subtitle.'</h3>
            <h4 class="px-4">';
        $html .= $chapters[$value->chapter];    
        $html .= '</h4>
            <h5 class="px-4 mb-5">'.$value->cpart.'</h5>
            <div id="style-bookview" class="editstyle-page py-2 px-4">
                '.$value->content.'
            </div>
        </div>';
    }


    $html .= '
    </div></div>';
    $html .= '<div class="col-md-4 mt-5"><div id="book-navigation-container">';
    //NAVIGATION
    $i = 0;
    foreach($chapters as $key => $value){

    $html .= '<div class="card-header p-0" id="heading'.$key.'">
                <h5 class="mb-0">
                    <button style="width:100%" class="btn ';
    $html .= ($key == 0) ? "collapsed" : "";  
    $html .= '" type="button" data-toggle="collapse" data-target="#collapse'.$key.'" aria-expanded="true" aria-controls="collapse'.$key.'">
                        '.$value.' <i class="fa fa-angle-right px-3" aria-hidden="true" data-dir="default"></i>
                    </button>
                </h5>
            </div>

            <div id="collapse'.$key.'" class="collapse ';
    $html .= ($key == 0) ? "show" : "";
    $html .= '" aria-labelledby="heading'.$key.'" data-parent="#vbBookNavigationList">
                <div class="card-body p-0">';
                    if($i < $count) {
                        $html .= '<ul class="mb-0 p-0 vb-section-list-nav">';      
                        $prevSound = "";                         
                            foreach($content as $k => $value){
                                if($value->chapter == $key){
                                    $activeChapter = ($k == 0) ? 'act-section' : '';
                                    if(!is_numeric($value->sound)){
                                        $dir = 1;
                                        $sound = $value->sound;
                                        $sound = ltrim($sound,"m");
                                        $sound = $msound[$sound]->filename;
                                        if((empty($prevSound) && $prevSound != $sound) || (!empty($prevSound) && $prevSound != $sound)){
                                            $soundArray[] = "user/{$sound}";
                                        }                                        
                                    }else{
                                        $dir = 0;
                                        $sound = $value->sound;
                                        $sound = $dsound[$sound]->filename;
                                        if((empty($prevSound) && $prevSound != $sound) || (!empty($prevSound) && $prevSound != $sound)){
                                            $soundArray[] = "{$sound}";
                                        }
                                    }
                                    $prevSound = $sound;
                                    $html .= '<li class="'.$activeChapter.'" data-status="0" data-chapter="'.$key.'" data-section="'.$k.'" data-sound="'.$sound.'" data-sdir="'.$dir.'">'.$value->cpart.'</li>';
                                    $i++;
                                }                        
                            }                           
                        $html .= '</ul>';
                     } 
                $html .= '</div></div>';
    }
    $html .= '</div></div>';
    $html .= '
    <input id="vb-bookDataHolder" type="hidden" data-title="'.$title.'" data-subtitle="'.$subtitle.'" data-file="'.$file.'" value="" />';
    $html .= '</section></main>';
    $html .= '
    <script type="text/javascript">';
    $html .= 'jQuery(document).ready(function($){
    const book = '.$book.';';
    $html .= '
    const input = $("input#vb-bookDataHolder");';
    $html .= '
    const title = input.data("title");';
    $html .= '
    const subtitle = input.data("subtitle");';
    $html .= '
    const file = input.data("file");';
    $html .= '
    const PlaySound = function(File,Status){    
    
    if(Status === 0){
        let Sound = document.createElement("audio");
        Sound.src="./src/media/"+File;
        $("ul.vb-section-list-nav > li").attr("data-status",1);
        return Sound; 
    }else{
        return null;
    }
    
    }
    ';

    $html .= '$(document).on("click","ul.vb-section-list-nav > li",function(){
        if(!$(this).hasClass("act-section")){
            let File = $(this).data("sound");
            //let dir = $(this).data("sdir");
            let status = $("ul.vb-section-list-nav > li").data("status");
            let Sound = PlaySound(File,status);
            if(status < 1){
                Sound.play();
                let = status = null;
            }else{
                Sound.pause();
                let = status = null;
            }
            $("ul.vb-section-list-nav > li").removeClass("act-section");
            $(this).addClass("act-section");            
            let c = $(this).data("chapter");
            let s = $(this).data("section");
            $("#book-container > div").addClass("d-none");
            $("#c"+c+"s"+s).removeClass("d-none");  
        }else{
            let Sound = null;
            let status = null;
        }
    });';
    $html .= '
    $(document).on("click",".card-header button",function(){
        let target = $(this).data("target");
        $(this).removeClass("collapsed");
        $(".collapse").removeClass("show");
        $(target).addClass("show");
    });';
    $html .= '});
    </script>';
    $html .= $footer;
    file_put_contents("../../json/users/temp/index.html",$html);

    // Create ZIP file
    if(isset($_POST['action'])){
        if($_POST['action'] == "create"){
            $zip = new ZipArchive();
            $filename = $title.".zip";
        
            if ($zip->open($filename, ZipArchive::CREATE)!==TRUE) {
                exit("cannot open <$filename>\n");
            }else{
                foreach($filesArray as $key => $val){
                    $zip->addFile("../../includes/{$val}","/src/{$val}");
                }
                foreach($soundArray as $k => $sound){
                    $zip->addFile("../../media/sounds/{$sound}","/src/media/{$sound}");
                } 
                $zip->addFile("../../json/users/temp/index.html","index.html");                                   
                $zip->close();
            }

            if (file_exists($filename)) {
                //header('Content-Type: application/zip');
                //header('Content-Disposition: attachment; filename="'.basename($filename).'"');
                //header('Content-Length: ' . filesize($filename));
           
                //flush();
                //readfile($filename);
                // delete file
                echo "http://v-book.test/pages/downloads/{$filename}"; 
                unlink("../../json/users/temp/index.html");
                die();
                //unlink($filename);            
            }

        }
    } 

    print_r($soundArray);
    //echo $chapters[0];
}