<div class="col-sm-12">
    <h1 class="text-monospace text-center p-5 mb-0 mt-5">Editor <small class="d-block h6">Lorem ipsum dolor sit amet, adipiscing elit.</small></h1>
</div>
<div class="col-sm-12">
<form action="POST" method="POST">
<div class="form-group">
<textarea name="vb-text-editor" id="body" col="30" row="10" style="height:auto;" value=""></textarea>
</div>
</form>
</div>
<script type="text/javascript" src="../js/editor.js"></script>
<script type="text/javascript">
//TEXT EDITOR
ClassicEditor
.create( document.querySelector( '#body' ) )
.catch( error => {
    console.error( error );
} ); 
</script>