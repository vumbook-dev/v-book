<?php
if(isset($_POST['data'])){
    $key = $_POST['data'];
    $list = file_get_contents("../json/books-list-title.json");
    $book = json_decode($list);    
?>

<div class="col-sm-12">
    <h1 id="vb-full-title" data-book="<?php echo $key; ?>" class="text-monospace text-center p-5 mb-0 mt-5"><?php echo $book[$key]->title ?> <small class="d-block h6"><?php echo $book[$key]->subtitle ?></small></h1>
</div>

<div class="col-sm-12 bc-wrap">
    <form action="/" method="post">
    
    <div class="form-group">
    <span>
        <label for="Chapter">New Chapter</label>
        <input name="chapter-name" id="chapter-name" type="text" class="form-control">
    </span>
    <button class="btn btn-primary float-right px-5" style="margin-top:-38px;" data-key="<?php echo $key; ?>">Add New</button>
    </div>
    </form>
</div>

<div class="col-sm-12 chapter-list-wrap">
    <h2 class="text-success text-center py-5">Chapter List</h2>
    <div class="chapter-list">

    </div>
</div>

<script type="text/javascript" src="../js/chapters.js"></script>
<?php

}else{
    header("Location: /");
}