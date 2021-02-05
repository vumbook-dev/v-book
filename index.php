<?php require_once "function.php"; ?>
<?php require_once "header.php"; ?>
<div id="vbUpdateMessage"></div>
    <header id="vb-header">
      <!-- Fixed navbar -->
      <nav class="navbar navbar-expand-md navbar-dark bg-dark px-4">
        <a class="navbar-brand" href="/home" data-nav="home">G-Book Editor</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
          <ul class="navbar-nav ml-auto">
            <li class="nav-item active mx-2">
              <a class="nav-link" id="0" data-nav="home" href="/">Home</a>
            </li>
            <?php if(isset($_COOKIE['userdata'])) : ?>
            <li id="btn-nav-holder" class="nav-item my-2">
              <a class="nav-link btn btn-success text-white d-inline d-none" id="2" data-nav="create" href="/create/">Create G-Books</a>
            </li>
            <?php endif; ?>
            <ul class="navbar_user <?php echo (!isset($_COOKIE['userdata'])) ? "d-none" : ""; ?>">
              <li class="account_dropdown"><span class="pr-2"><a href="<?php echo VUMBOOK; ?>user/account"><i class="fa fa-user pr-1" aria-hidden="true"></i> <?php if(isset($_COOKIE['userdata']['name'])) echo $_COOKIE['userdata']['name']; ?></a></span><i class="fa fa-angle-down"></i></li>
              <li class="account">
                  <ul class="account_selection">
                    <?php if(!isset($_COOKIE['userdata'])) : ?>
                    <li><a href="<?php echo VUMBOOK; ?>user/login"><i class="fa fa-sign-in" aria-hidden="true"></i> Login</a></li>
                    <li><a href="<?php echo VUMBOOK; ?>user/signup"><i class="fa fa-user-plus" aria-hidden="true"></i> Sign Up</a></li>
                    <?php else : ?>
                      <li><a href="<?php echo VUMBOOK; ?>user/account">My Account</a></li>
                    <li><a href="<?php echo VUMBOOK; ?>user/logout"><i class="fa fa-sign-out" aria-hidden="true"></i> Logout</a></li>
                    <?php endif; ?>
                  </ul>
              </li>
            </ul>
        <?php if(!isset($_COOKIE['userdata'])) : ?>
        <li class="min-screen"><a href="<?php echo VUMBOOK; ?>user/login"><i class="fa fa-sign-in" aria-hidden="true"></i> Login</a></li>
        <li class="min-screen"><a href="<?php echo VUMBOOK; ?>user/signup"><i class="fa fa-user-plus" aria-hidden="true"></i> Sign Up</a></li>
        <?php else : ?>
          <li class="min-screen"><a href="<?php echo VUMBOOK; ?>user/logout"><i class="fa fa-sign-out" aria-hidden="true"></i> Logout</a></li>
        <?php endif; ?>
        </ul>
        </div>
      </nav>
    </header>

    <!-- Begin page content -->
    <main role="main" class="container pb-5 main-editor">
      <section class="row" id="vb-show-content">
      </section>
    </main>

<?php require_once "footer.php"; ?>