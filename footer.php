<footer class="footer bg-dark d-none">
    <span class="text-white py-2 px-4 d-block">Prototype version 1.0</span>
</footer>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/pickr.min.js"></script>
    <script src="/includes/js/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script type="text/javascript" src="/js/navigation.js"></script>
    <script type="text/javascript">
          jQuery(document).ready(function($){
            <?php 
            
            $page = redirectToPages(); 
            if(!empty($page) && $page !== 'usernotLoggedIn'){
              ?>
              let userdata = getCookie('userdata[id]');              
              if(userdata != null){
                <?php echo $page; ?>
                $("a[data-nav=create]").removeClass('d-none');
              }else{
                $("a[data-nav=create]").remove();
                window.location.href = '/';
              }
              //console.log(userdata);
              <?php
            }else{ ?>
            let session = getCookie('sessiondata[session]');
            <?php if($page === 'usernotLoggedIn'){ ?>                
                if(session != null){                 
                  $("div#vbUpdateMessage").prepend('<div class="message-status alert alert-danger" role="alert"><i class="fa fa-exclamation-circle" aria-hidden="true"></i> Your session expired please login to your account.</div>');
                  setTimeout(function(){
                    $("div#vbUpdateMessage>div").fadeOut(500);
                  },1800);
                }
                history.replaceState(0, `V-Book > Home`, `/`);                
            <?php } ?>      
            if(session != null){
              $("a[data-nav=create]").remove(); 
              $("li.account_dropdown>span.pr-2").html('<i class="fa fa-user pr-1" aria-hidden="true"></i>');
              $("ul.account_selection").html(`<li><a href="<?php echo VUMBOOK; ?>user/login"><i class="fa fa-sign-in" aria-hidden="true"></i>Login</a></li><li><a href="<?php echo VUMBOOK; ?>user/signup"><i class="fa fa-user-plus" aria-hidden="true"></i>Sign Up</a></li>`);
            }else{
              $("a[data-nav=create]").removeClass('d-none');
            }      
            <?php  echo "loadPage('home',vbloader);";
            }
            
            ?>
            $(document).on('click','li.account_dropdown',function(){
              $('ul.account_selection').toggleClass('show_dropdown');
            });
          });
    </script>
  </body>
</html>