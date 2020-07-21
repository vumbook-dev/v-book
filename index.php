<?php require_once "function.php"; ?>
<?php require_once "header.php"; ?>

    <header id="vb-header">
      <!-- Fixed navbar -->
      <nav class="navbar navbar-expand-md navbar-dark bg-dark px-4">
        <a class="navbar-brand" href="/home" data-nav="home"><i class="fa fa-book" aria-hidden="true"></i> V-Book</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
          <ul class="navbar-nav ml-auto">
            <li class="nav-item active mx-2">
              <a class="nav-link" id="0" data-nav="home" href="/home/">Home</a>
            </li>
            <li class="nav-item d-none">
              <a class="nav-link" id="1" data-nav="editor" href="/editor/">Editor</a>
            </li>
            <li class="nav-item">
              <a class="nav-link btn btn-success text-white" id="2" data-nav="create-books" href="/create-books/">Create Books</a>
            </li>
          </ul>
          <!-- <form class="form-inline mt-2 mt-md-0">
            <input class="form-control mr-sm-2" type="text" placeholder="Search" aria-label="Search">
            <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
          </form> -->
        </div>
      </nav>
    </header>

    <!-- Begin page content -->
    <main role="main" class="container pb-5">
      <section class="row" id="vb-show-content">
      </section>
    </main>

<?php require_once "footer.php"; ?>