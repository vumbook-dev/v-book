<?php
if(isset($_POST['book']) && isset($_POST['chapter']) && isset($_POST['section']) && isset($_POST['file'])){
    
    $book = $_POST['book'];
    $chapter = $_POST['chapter'];
    $section = $_POST['section'];
    $file = $_POST['file'];

    $listChapter = file_get_contents("../../json/books-list-title.json");
    $bookData = json_decode($listChapter);
    $chapterData = $bookData[$book]->chapter;
    $content = file_get_contents("../../json/book-content/{$file}.json");
    $content = json_decode($content); ?>

<div class="px-4 py-5 mt-5 <?php echo "text-{$content[$section]->align}"; ?>">
    <h3 class="vb-ch-title px-4 mb-3"><?php echo $bookData[$book]->title; ?></h3>
    <h5 class="px-4 mb-5"><?php echo $content[$section]->cpart; ?></h5>
    <div id="style-bookview" class="editstyle-page py-2 px-4">
        <?php echo $content[$section]->content; ?>
    </div>
</div>

<?php 

}