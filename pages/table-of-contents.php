<?php
if(isset($_POST['data'])){
    if(isset($_COOKIE['userdata'])){
        $UID = $_COOKIE['userdata']['id'];
        $UName = $_COOKIE['userdata']['name'];
    }
    $UFolder = "{$UName}{$UID}";
    $key = $_POST['data'];
    $key = $key - 1;
    $list = file_get_contents("../json/users/bookdata/{$UFolder}/books-list-title.json");
    $book = json_decode($list);    
    $file = $book[$key]->storage;
    $btmp = $book[$key]->template;
    $bookcover = "";

    $coverkey = $book[$key]->cover;
    if(is_integer($coverkey)){
        $cover = file_get_contents("../json/users/bookdata/{$UFolder}/media/user-bookcover.json");
        $cover = json_decode($cover);
        $bookcover = "../media/bookcover/user/{$cover[$coverkey]->filename}";
    }

    if(!empty($book[$key]->background)){
        $BBGkey = $book[$key]->background;
        if(is_integer($BBGkey)){
            $BBG = file_get_contents("../json/users/bookdata/{$UFolder}/media/book-background.json");
            $BBG = json_decode($BBG);
            $bookBG = "../media/book-background/user/{$BBG[$BBGkey]->filename}";
        }
    }else{
        $BBGkey = null;
    }
?>
<div id="vbUpdateMessage">
</div>
<div class="col-sm-12 my-5 pt-5">    
    <label for="Book Title" class="d-none text-right editable-label-title"><small>Put text inside <strong class="h4">{...}</strong> for book subtitle</small></label>
    <h1 id="vb-full-title" data-template="<?php echo $btmp; ?>" data-cover="<?php echo $book[$key]->cover; ?>" data-book="<?php echo $key; ?>" data-title="<?php echo $book[$key]->title; ?>" class="text-monospace text-center p-1 mb-5"><?php echo $book[$key]->title ?> <span class="editable-title"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></span><small class="d-block h6"><?php echo $book[$key]->subtitle ?></small></h1>  
    <div class="float-right d-none editable-btn" style="margin-top: -42px;"><button class="btn btn-danger">Cancel</button>
    <button class="btn btn-primary">Save</button></div>
</div>
<div class="col-sm-3">
    <!-- BOOK BACKGROUND UPLOAD -->
    <div class="form-group edit-book-background py-3 text-center">
        <span class="h5">Book Background</span>
        <div id="book-background-preview-wrap"  class="py-3 mx-5" >
            <i class="fa fa-picture-o <?php echo (is_integer($BBGkey)) ? "d-none" : ""; ?>" aria-hidden="true"></i>
            <img id="prev-img-bookbackground" class="clearfix <?php echo (!is_integer($BBGkey)) ? "d-none" : ""; ?>" src="<?php echo $bookBG; ?>" alt="" />   
        </div>
        <div id="vbUploadBookBackground">                        
            <input type="text" src="" placeholder="" class="d-none form-control rdnly-plchldr" readonly>
            <form method="POST" action="" id="submit-book-background">
                <div class="input-group-btn" style="margin-left:-2px;">
                    <span class="fileUpload btn btn-success d-block mx-5">
                        <span class="upl text-light" id="upload"><?php echo (!is_integer($BBGkey)) ? "Upload" : "Update" ; ?></span>
                        <input type="hidden" name="book" value="<?php echo $key; ?>">
                        <input type="file" accept="image/*" class="upload up" data-type="background" id="upbackground" name="book-background[]"/>
                    </span><!-- btn-orange -->
                </div><!-- btn -->
                <button class="btn btn-primary d-none">Submit</button>
            </form>
        </div>
    </div>
    <!-- BOOK BACKGROUND UPLOAD END -->
    <!-- BOOK COVER UPLOAD -->
    <div class="form-group edit-book-cover py-3 text-center">
        <span class="h5">Book Cover</span>
        <div id="book-cover-preview-wrap"  class="py-3 mx-5" >
            <i class="fa fa-picture-o <?php echo (is_integer($coverkey)) ? "d-none" : ""; ?>" aria-hidden="true"></i>
            <img id="prev-img-bookcover" class="clearfix <?php echo (!is_integer($coverkey)) ? "d-none" : ""; ?>" src="<?php echo $bookcover; ?>" alt="" />   
        </div>
        <div id="vbUploadBookCover">                        
            <input type="text" src="" placeholder="" class="d-none form-control rdnly-plchldr" readonly>
            <form method="POST" action="" id="submit-book-cover">
                <div class="input-group-btn" style="margin-left:-2px;">
                    <span class="fileUpload btn btn-warning d-block mx-5">
                        <span class="upl text-light" id="upload"><?php echo (!is_integer($coverkey)) ? "Upload" : "Update" ; ?></span>
                        <input type="hidden" name="book" value="<?php echo $key; ?>">
                        <input type="file" accept="image/*" class="upload up" data-type="cover" id="upcover" name="book-cover[]"/>
                    </span><!-- btn-orange -->
                </div><!-- btn -->
                <button class="btn btn-primary d-none">Submit</button>
            </form>
        </div>
    </div>
    <!-- BOOK COVER UPLOAD END -->
</div>
<!-- <div class="col-sm-9 bc-wrap" style="place-self: center;">
    
</div> -->

<div class="col-sm-9 chapter-list-wrap bc-wrap">    
    <form action="/" method="get" autocomplete="off" id="submit-chapter">
    
    <div class="form-group">
    <span>
        <label for="Chapter" class="h5">New Chapter <small>Put text inside <strong class="h4">{...}</strong> for chapter subtitle</small></label>
        <input name="chapter-name" id="chapter-name" type="text" class="form-control">
        
    </span>
    <button class="btn btn-primary float-right px-5" style="margin-top:-38px;" data-key="<?php echo $key; ?>">Add New</button>
    </div>
    </form>  
    <h2 class="text-success text-center py-5">Table of Contents</h2>
    <div class="chapter-list">
    </div>  
</div>
<input id="vb-ttl-cdidtfyr" data-bookid="<?php echo $book[$key]->id; ?>" data-universal="<?php echo $file; ?>" type="hidden">

<div id="vb-modal-container"></div>
<script type="text/javascript" src="/js/chapters.js"></script>
<script type="text/javascript" src="/js/section.js"></script>
<script type="text/javascript" src="/js/content.js"></script>
<script type="text/javascript" src="/js/<?php echo $btmp; ?>-editor.js"></script>
<?php

}else{
    header("Location: /");
}