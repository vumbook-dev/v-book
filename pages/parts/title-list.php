<?php
require_once "../../config.php";
if(isset($_COOKIE['userdata'])){
    $UID = $_COOKIE['userdata']['id'];
    $UName = $_COOKIE['userdata']['name'];
}
$UFolder = DATAPATH;

function crud_btn($listKey = '',$action = '',$pages = 0,$template = ''){
    $na = ($pages == 0)? "vb-btn-disable" : "";
    $disable = ($pages == 0)? "disabled" : "";
    $icon = ($pages == 0)? "fa-eye-slash" : "fa-eye";
    $btnClass = ($pages == 0)? "btn-secondary" : "btn-success";
    $book = $listKey + 1;
    $btn = '<span class="vb-wrap-btn px-2">
    <a id="vb-view" href="/read/'.$template.'='.$book.'" target="_blank" class="btn '.$btnClass.' '.$na.'" data-key="'.$listKey.'" '.$disable.'><i class="fa '.$icon.'" aria-hidden="true"></i> View</a>
    <a id="3" class="btn btn-primary mx-1 vb-link" href="/table-of-contents/'.$template.'='.$book.'" data-page="book-chapter">'.$action.'</a>
    <button class="btn btn-danger vb-delete" data-key="'.$listKey.'" data-toggle="modal" data-target="#vb-delete-modal"><i class="fa fa-trash-o" aria-hidden="true"></i></button>
    </span>';
    return $btn;
}

$list = file_get_contents("../../json/users/bookdata/{$UFolder}/books-list-title.json");
$titles = json_decode($list);

$html = '<ul class="title-list-group">';
foreach($titles as $key => $info){
    $table = count($info->chapter);
    $template = ($info->template == 'book') ? "<i class='bx bx-book'></i>" : "<i class='bx bx-news' ></i>";
    $badge = ($info->status == "unpublished")? "secondary" : "success";
    $action = ($table > 0)? '<i class="fa fa-pencil" aria-hidden="true"></i> Edit Content' : '<i class="fa fa-plus-circle" aria-hidden="true"></i> Add Content';
    $html .= '<li class="list-item-vbtitle d-flex justify-content-between align-items-center"><span class="p-absolute btmp-icon">'.$template.'</span>';
    $html .= '<span class="h5"><span class="vbook-title">'.$info->title.'</span><small>'.$info->subtitle.'</small><span class="badge badge-'.$badge.' badge-pill p-2 ml-3 vb-status">'.$info->status.'</span></span>';
    $html .= '<span class="float-right">'.crud_btn($key,$action,$table,$info->template);
    $html .= 'Contents: <span class="badge badge-primary badge-pill">'.$table.'</span></span>';
    $html .= '</li>';
}
$html .= '</ul>';

echo $html;
//print_r($titles);
require_once "modal.php"; ?>

<script type="text/javascript">
    jQuery(document).ready(function(){
        //LIST BOOK TITLE
        function refresh(){
            $.ajax({
                method: "GET",
                url: "../pages/parts/title-list.php",
                beforeSend: function(){
                    $("div.modal-backdrop").remove();
                },
                success: function(data){
                    $("div.book-list").html(data);
                }
            });
        }

        //DELETE BOOK
        function deleteBook(key){
            let modal = $("#vb-delete-modal"); 
            $.ajax({
                method: "POST",
                url: "../model/books.php",
                data: {key:key,action:"delete"},
                dataType: "text",
                beforeSend: function(){
                    modal.find(".modal-body>p").html(`Deleting ...`);
                },
                success: function(data){
                    setTimeout(function(){
                        modal.find(".modal-body>p").html(data+" is deleted successfully.");                
                        setTimeout(function(){                                    
                            //modal.find(".close").click();
                            refresh();
                        },1000);   
                    },1500);            
                }
            });
        }
        
        $(document).on("click",".vb-delete",function(){
            let key = $(this).data("key");
            let title = $(this).parents(".list-item-vbtitle").find(".vbook-title").text();
            $("#vb-title-handler").text(title);
            $("#vb-confirm-delete").attr("data-key",key);               
        });

        $("#vb-confirm-delete").click(function(){
            let x = $(this).data("key");
            deleteBook(x);
        });

    });
</script>