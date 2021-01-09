jQuery(document).ready(function($){
    /*** START BOOK SECTION ***/
    
    //SUBMIT SECTION TITLE
    $(document).on('submit','.vb-new-section',function(e){
        e.preventDefault();
        let selector = $(this).parents("div.card-body");
        let parent = $(this).parents(".tc-wrap");
        let input = parent.find("input.content-name");
        let content = input.val();
        let id = parent.find("input[type=hidden]").val();
        let key = parent.find("button.vb-new-content").data("key");
        let title = parent.find("input[type=hidden]").data("title");
        let bookIndex = parent.find("input[type=hidden]").data("bookindex");
        parent.addClass('d-none');
        selector.find('li.d-none').removeClass('d-none');

        if(content.length != 0){
            addContent(id,content,key,title,"",bookIndex);
            window.flashMessage(input.val()+' Successfully Added','success');
        }        

        input.val("");
    });
    
});