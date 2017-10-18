<style type="text/css">
  .red-text {
    color: #B92B27 !important;
    font-weight: bold;
    background-color: #FBFBFB !important;
  }
  @media screen and (max-width: 766px) {
    .login-register {
      padding-left: 30px !important;
    }
    .sub-div {
      margin-top: 20px;
    }
    #navbar {
      overflow: hidden;
    }
  }
  @media screen and (max-width: 500px) {
    .sub-div {
      margin-top: 30px;
    }
  }
</style>
<nav class="navbar navbar-default navbar-fixed-top" style = "background-color: #FFFFFF; border-bottom: 2px solid #B92B27;">
    <div class="container">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="feed.php" style = "color: #B92B27; font-weight: bold; font-size: 25px;font-family: georgia;">E-Album</a>
      </div>
      <div id="navbar" class="navbar-collapse collapse">
        <ul class="nav navbar-nav">
          <li class = "<?php if (basename($_SERVER["REQUEST_URI"], ".php") == "feed") { echo "active"; } ?>"><a href="/album/feed.php" class = "<?php if (basename($_SERVER["REQUEST_URI"], ".php") == "feed") { echo "red-text"; } ?>">Feed</a></li>
          <li class = "<?php if (basename($_SERVER["REQUEST_URI"], ".php") == "home") { echo "active"; } ?>"><a href="/album/home.php" class = "<?php if (basename($_SERVER["REQUEST_URI"], ".php") == "home") { echo "red-text"; } ?>">My Album</a></li>
        </ul>
        <ul class="nav navbar-nav navbar-right">
          <?php 
          if (isset($_SESSION['user'])) { 
            $res = mysqli_query($conn, "SELECT * FROM users WHERE userId = ".$_SESSION['user']);
            $userRow = mysqli_fetch_array($res);
          ?>
          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
            <?php
            if ($userRow['profileImage'] == "") {
              $tempUrl1 = "/album/profilePicture.png";
            } else {
              $tempUrl1 = "/album/uploads/".$userRow['userName']."/".$userRow['profileImage'];          
            }
            ?>
            <div class = 'profile-icon-top' style = 'background-image: url("<?php echo $tempUrl1; ?>");'></div> Hi <?php echo strtok($userRow['userName'], " "); ?>&nbsp;<span class="caret"></span></a>
            <ul class="dropdown-menu" style = "">
              <li><a href="logout.php?logout"><span class="glyphicon glyphicon-log-out"></span>&nbsp;Sign Out</a></li>
            </ul>
          </li>
          <?php } else { ?>
          <ul class="nav navbar-nav">
            <li class = "<?php if (basename($_SERVER["REQUEST_URI"], ".php") == "index") { echo "active"; } ?>"><a href="/album/index.php" class = "login-register <?php if (basename($_SERVER["REQUEST_URI"], ".php") == "index") { echo "red-text"; } ?>">Login</a></li>
            <li class = "<?php if (basename($_SERVER["REQUEST_URI"], ".php") == "register") { echo "active"; } ?>"><a href="/album/register.php" class = "login-register <?php if (basename($_SERVER["REQUEST_URI"], ".php") == "register") { echo "red-text"; } ?>">Register</a></li>
          </ul>  
          <?php } ?>
        </ul>
      </div>
    </div>
  </nav>