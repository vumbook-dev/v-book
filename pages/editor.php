<div class="p-4">
<form action="POST" method="POST">
    <div class="form-group">
        <textarea name="vb-text-editor" id="body" col="30" row="10" style="height:auto;" value=""></textarea>
    </div>
</form>
</div>
<!-- <script type="text/javascript" src="../js/editor.js"></script> -->
<script type="text/javascript">
//TEXT EDITOR
ClassicEditor
.create( document.querySelector( '#body' ) )
.catch( error => {
    console.error( error );
} ); 
</script>