<?php require_once "header.php"; ?>

    <header>
      <!-- Fixed navbar -->
      <nav class="navbar navbar-expand-md navbar-dark bg-dark px-4">
        <a class="navbar-brand" href="#"><i class="fa fa-book" aria-hidden="true"></i> V-Book</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
          <ul class="navbar-nav ml-auto">
            <li class="nav-item active">
              <a class="nav-link" id="vb-home" data-nav="home" href="/">Home</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" id="vb-editor" data-nav="editor" href="/">Editor</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" id="vb-books" data-nav="books" href="/">Books</a>
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
    <main role="main" class="container">
      <section class="row" id="vb-show-content">
        <?php include "pages/home.php"; ?>
      </section>
    </main>

<?php require_once "footer.php"; ?>