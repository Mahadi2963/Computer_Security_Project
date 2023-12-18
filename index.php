<!DOCTYPE html>
<html lang="en">
<?php
session_start();
include('admin/db_connect.php');
ob_start();
$query = $conn->query("SELECT * FROM system_settings limit 1")->fetch_array();
foreach ($query as $key => $value) {
  if (!is_numeric($key))
    $_SESSION['system'][$key] = $value;
}
ob_end_flush();
include('header.php');


?>

<style>
  body {
    background: lightblue;
  }

  footer {
    background: #000000 !important;
  }
</style>

<body id="page-top">
  <!-- Navigation-->
  

  <div class="toast" id="alert_toast" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="toast-body text-white bg-primary">
    </div>
  </div>
  <nav class="navbar navbar-expand-lg navbar-light fixed-top py-3  text-white bg-primary" id="mainNav">
    <div class="container">
      <a class="navbar-brand js-scroll-trigger" href="./"><?php echo "UIU StudentSphere" ?></a>
      <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
      <div class="collapse navbar-collapse" id="navbarResponsive">
        <ul class="navbar-nav ml-auto my-2 my-lg-0">
          <li class="nav-item"><a class="nav-link js-scroll-trigger" href="index.php?page=home">Home</a></li>
          <li class="nav-item"><a class="nav-link js-scroll-trigger" href="index.php?page=gallery">Gallery</a></li>
          <?php if (isset($_SESSION['login_id'])) : ?>
            <li class="nav-item"><a class="nav-link js-scroll-trigger" href="index.php?page=careers">Jobs</a></li>

            <li class="nav-item"><a class="nav-link js-scroll-trigger" href="index.php?page=forum">Forums</a></li>

            <li class="nav-item"><a class="nav-link js-scroll-trigger" href="index.php?page=courses">Resources</a></li>

            <li class="nav-item"><a class="nav-link js-scroll-trigger" href="index.php?page=gitsearch">Git Search</a></li>

            <li class="nav-item"><a class="nav-link js-scroll-trigger" href="index.php?page=alumni_list">Participants</a></li>

          <?php endif; ?>
 
          <?php if (!isset($_SESSION['login_id'])) : ?>
            <li class="nav-item"><a class="nav-link js-scroll-trigger" href="#" id="login">Login</a></li>
          <?php else : ?>
            <li class="nav-item">
              <div class=" dropdown mr-4">
                <a href="#" class="nav-link js-scroll-trigger" id="account_settings" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo $_SESSION['login_name'] ?> <i class="fa fa-angle-down"></i></a>
                <div class="dropdown-menu" aria-labelledby="account_settings" style="left: -2.5em;">
                  <a class="dropdown-item" href="admin/ajax.php?action=logout2"><i class="fa fa-power-off"></i> Logout</a>
                </div>
              </div>
            </li>
          <?php endif; ?>


        </ul>
      </div>
    </div>
  </nav>









  <?php
  $page = isset($_GET['page']) ? $_GET['page'] : "home";
  include $page . '.php';
  ?>


  <div class="modal fade" id="confirm_modal" role='dialog'>
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Confirmation</h5>
        </div>
        <div class="modal-body">
          <div id="delete_content"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" id='confirm' onclick="">Continue</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="uni_modal" role='dialog'>
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"></h5>
        </div>
        <div class="modal-body">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" id='submit' onclick="$('#uni_modal form').submit()">Save</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="uni_modal_right" role='dialog'>
    <div class="modal-dialog modal-full-height  modal-md" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span class="fa fa-arrow-right"></span>
          </button>
        </div>
        <div class="modal-body">
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="viewer_modal" role='dialog'>
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
        <button type="button" class="btn-close" data-dismiss="modal"><span class="fa fa-times"></span></button>
        <img src="" alt="">
      </div>
    </div>
  </div>

  <br>




  <div id="preloader"></div>
  <footer class=" py-5">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-8 text-center">
          <h2 class="mt-0 text-white">Contact us</h2>
          <hr class="divider my-4" />
        </div>
      </div>
      <div class="row">
        <div class="col-lg-4 ml-auto text-center mb-5 mb-lg-0">
          <i class="fas fa-phone fa-3x mb-3 text-muted"></i>
          <div class="text-white">+880 0123456789</div>
        </div>
        <div class="col-lg-4 mr-auto text-center">
          <i class="fas fa-envelope fa-3x mb-3 text-muted"></i>
          <a class="d-block" href="mailto:uiustudentsphere@gmial.com">uiustudentsphere@gmial.com</a>
        </div>
      </div>
    </div>
    <br>
    <div class="container">
      <div class="small text-center text-muted">Copyright © 2023 --- UIU_StudentSphere --- <a href="https://www.uiu.ac.bd" target="_blank"> UIU_Officials</a></div>
    </div>
  </footer>



  <?php include('footer.php') ?>
</body>

<script type="text/javascript">
  $('#login').click(function() {
    uni_modal("Login", 'login.php')
  })
</script>
<?php $conn->close() ?>

</html>