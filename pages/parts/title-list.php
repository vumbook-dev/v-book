<?php

function crud_btn($listKey,$action,$pages = 0){
    $na = ($pages == 0)? "vb-btn-disable" : "";
    $disable = ($pages == 0)? "disabled" : "";
    $btn = '<span class="vb-wrap-btn px-2">
    <button id="vb-view" class="btn btn-success '.$na.'" data-key="'.$listKey.'" '.$disable.'><i class="fa fa-eye" aria-hidden="true"></i> View</button>
    <button id="3" class="btn btn-primary mx-1 vb-link" data-key="'.$listKey.'" data-page="book-chapter">'.$action.'</button>
    <button class="btn btn-danger vb-delete" data-key="'.$listKey.'" data-toggle="modal" data-target="#vb-delete-modal"><i class="fa fa-trash-o" aria-hidden="true"></i></button>
    </span>';
    return $btn;
}

$list = file_get_contents("../../json/books-list-title.json");
$titles = json_decode($list);

$html = '<ul class="title-list-group">';
foreach($titles as $key => $info){
    $table = count($info->chapter);
    $badge = ($info->status == "unpublished")? "secondary" : "success";
    $action = ($table > 0)? '<i class="fa fa-pencil-squire-o" aria-hidden="true"></i> Edit Content' : '<i class="fa fa-plus-circle" aria-hidden="true"></i> Add Content';
    $html .= '<li class="list-item-vbtitle d-flex justify-content-between align-items-center">';
    $html .= '<span class="h5"><span class="vbook-title">'.$info->title.'</span><small>'.$info->subtitle.'</small><span class="badge badge-'.$badge.' badge-pill p-2 ml-3 vb-status">'.$info->status.'</span></span>';
    $html .= '<span class="float-right">'.crud_btn($key,$action,$table);
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
                url: "../controller/books.php",
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

        //ADD CONTENT
        $(document).on("click",".vb-link",function(){
            let page = $(this).data("page");
            let key = $(this).data("key");
            history.pushState(4, `V-Book ${page}`, `./${page}`);
            sendToPage(page,vbloader,key);
        });

    });
</script>