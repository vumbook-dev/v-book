<div class="col">
    <h1 class="text-monospace text-center p-5 mb-0 mt-5">Create Books <small class="d-block h6">Lorem ipsum dolor sit amet, adipiscing elit.</small></h1>
</div>

<div class="col-sm-12 cb-wrap">
    <form action="/" method="post">
    <div class="form-group">
        <span>
            <label for="book title">Main Title</label>
            <input name="book-title" id="book-title" type="text" class="form-control">    
        </span>
        <span>
            <label for="book title">Sub Title</label>
            <input placeholder="Optional" name="sub-title" id="sub-title" type="text" class="form-control">
        </span>
        <button class="btn btn-primary px-5">Add New</button>
    </div>
    </form>
</div>

<div class="col-sm-12 book-list-wrap">
    <h2 class="text-success text-center py-5">Book Title List</h2>
    <div class="book-list">

    </div>
</div>


<script type="text/javascript" src="../js/books.js"></script>