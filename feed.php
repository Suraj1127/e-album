<?php 
  session_start();
  require_once "dbconnect1.php"; 
  function dateAndTime($string) {
   $currentDate = date("Y m d g i a");
   $currentDateArray = preg_split('/ /', $currentDate);
   $uploadDateArray = preg_split('/ /', $string);
   //$currentDateArray = array('2017', '6', '10', '12', '23', 'pm');
   if ($currentDateArray[5] == 'pm') {
    $temp1 = $currentDateArray[3] + 12;
   } else {
    $temp1 = $currentDateArray[3];
   }
   if ($uploadDateArray[5] == 'pm') {
    $temp2 = $uploadDateArray[3] + 12;
   } else {
    $temp2 = $uploadDateArray[3];
   }
   $currentTimeAmount = ($currentDateArray[0]-2017)*365*24*60*60 + $currentDateArray[1]*30*24*60*60+$currentDateArray[2]*24*60*60+$temp1*60*60+$currentDateArray[4]*60;
   $uploadTimeAmount = ($uploadDateArray[0]-2017)*365*24*60*60 + $uploadDateArray[1]*30*24*60*60+$uploadDateArray[2]*24*60*60+$temp2*60*60+$uploadDateArray[4]*60;
   $differenceTime = $currentTimeAmount - $uploadTimeAmount;
   if ($differenceTime <= 60) {
    return "Just Now";
   } else if ($differenceTime <= 3600 ) {
    return floor($differenceTime/60)." min ago";
   } else if ($differenceTime <= 86400) {
    if (round($differenceTime/3600) == 1) {
      return round($differenceTime/3600)." hour ago";
    }
    return round($differenceTime/3600)." hours ago";
   } else if ($differenceTime <= 172800) {
    return "Yesterday at ".$uploadDateArray[3].":".$uploadDateArray[4]." ".strtoupper($uploadDateArray[5]);
   } else if ($differenceTime <= 604800) {
    return $uploadDateArray[7].", ".$uploadDateArray[3].":".$uploadDateArray[4]." ".strtoupper($uploadDateArray[5]);
   } else if ($currentDateArray[0] == $uploadDateArray[0]) {
    return ltrim($uploadDateArray[2], '0')."th ".$uploadDateArray[6];
   } else {
    return ltrim($uploadDateArray[2], '0')."th ".$uploadDateArray[6].", ".$uploadDateArray[0];

   }
   return;
 }
?>

<!DOCTYPE html>
<html>
<head>
<meta property="og:image" content="http://creatovert.com/album/display.jpg" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Feed - Album</title>
<link rel="icon" href="/album/logo.png">
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<link rel="stylesheet" href="style.php" type="text/css" />
<link rel="stylesheet" type="text/css" href="lightbox.css">
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
<style type = "text/css">
.feed-single-picture-div {
  max-width: 600px;
  margin: auto auto;
  margin-bottom: 4%;
}
.feed-single-picture-div img {
  width: 100%;
}
.profile-icon {
  width: 40px !important;
  height: 40px !important;
  border-radius: 50%;
  background-size: cover !important;
  background-position: center !important;
  background-repeat: no-repeat !important;
  display: block;
  margin: 20px;
}
.profile-icon span {
  position: relative;
  left: 60px;
  font-size: 20px;
}
.disabled {
  pointer-events: none;
}
.profile-icon-top {
  width: 18px !important;
  height: 18px !important;
  background-size: cover !important;
  background-position: center !important;
  background-repeat: no-repeat !important;
  display: inline-block;
  margin-bottom: -4px;
}
body {
  background-color: #FAFAFA;
}
.swt-button {
  border: 2px solid brown;
  border-radius: 50%; 
  width: 40px; 
  height: 40px;
  margin-top: 10px;
  font-family: Georgia, serif;
  font-weight: bold;
  padding-top: 7px;
  color: #B92B27;
  background-color: yellow;
  margin-left: 2px;
  cursor: pointer;
}
.photo-things {
  margin: 10px;
}
.liker-picture {
  width: 40px;
  height: 40px;
  background-position: center;
  background-repeat: none;
  background-size: cover;
  border-radius: 50%;
  margin-bottom: 20px;
  padding-top: 8px;
}
#liker-name {
  position: relative;
  left: 60px;
  font-size: 15px;
  font-weight: 125%;
}
.likes-modal {
  cursor: pointer;
}
.likes-modal:hover {
  text-decoration: underline;
}
.liked {
  color: #DF2B27;
}
.not-logged-in-trigger {
  display: none;
}
@media screen and (max-width: 766px) {
    #login-button {
      margin-bottom: 10px;
    }
}
.profile-anchor {
  color: black;
}
.profile-anchor:hover {
  text-decoration: none;
  color: black;
  cursor: pointer;
}
</style>

</head>
<body>

  <?php include "header.php"; ?>

  <!-- for getting username of logged in user -->
  <div id = "hidden" style = "display: none;">
  <?php 
  if (isset($_SESSION['user'])) {
    echo $_SESSION['user'];
  }
  ?>
  </div>

  <div id = "wrapper">
    <div class = "container text-center" style = 'margin-top: 50px;'>
    <?php
      if (!isset($_GET['a'])) {
        $_GET['a'] = 0;
        $startingPoint = 0;
      } else { 
        if ($_GET['a'] < 0) {
          header("Location: feed.php");
        }
        $startingPoint = $_GET['a']*10;
      }

      $query = "SELECT * FROM photos_collection ORDER BY id DESC LIMIT ".$startingPoint.",10;";
      $res = mysqli_query($conn, $query);
      $grandTotal = mysqli_num_rows($res);

      $query3 = "SELECT * FROM photos_collection ORDER BY id DESC LIMIT ".($startingPoint + 10).",10;";
      $res3 = mysqli_query($conn, $query3);
      $grandTotal1 = mysqli_num_rows($res3);

      for ($i = 0; $i < $grandTotal; $i++ ) {
        $singleImage = mysqli_fetch_array($res);
        echo "<div class = 'thumbnail feed-single-picture-div' id = '".$singleImage['id']."''>";
        $query1 = "SELECT * FROM users WHERE userName = '".$singleImage['username']."'";
        $res1 = mysqli_query($conn, $query1);
        $singleImage1 = mysqli_fetch_array($res1);
        if ($singleImage1['profileImage'] == "") {
          $tempUrl = "profilePicture.png";
        } else {
          $tempUrl = "uploads/".$singleImage['username']."/".$singleImage1['profileImage'];          
        }
        ?>
        <div class = "profile-icon" style = "background: url('<?php echo $tempUrl; ?>');"><span><a class = 'profile-anchor' <?php if (isset($_SESSION['user'])) { echo "href = '/album/".$singleImage['username']."'";} ?>><?php echo $singleImage['username']; ?><a></span></div>
        <img src ='uploads/<?php echo $singleImage['username']; ?>/<?php echo $singleImage['image'];?>'> 
        

        <?php
        if (isset($userRow)) {
          $queryb = "SELECT * FROM likes_table WHERE photo_id = '".$singleImage['id']."' AND liker = '".$userRow['userName']."';";
          $resb = mysqli_query($conn, $queryb);
          if (mysqli_num_rows($resb) != 0) {
            $class = "liked";
          } else {
            $class = "none";
          }          
        }
        
        ?>
        <div class = "text-left heart-div photo-things">
          <i style = 'cursor: pointer;' id = "<?php echo $singleImage['id']; ?>" class="fa fa-2x fa-heart-o heart-icon <?php if (isset($class)) { if ($class == "liked") { echo "liked";} }?>" aria-hidden="true"></i>
          <a href = 'https://www.facebook.com/sharer/sharer.php?u=creatovert.com/album/uploads/<?php echo $singleImage['username']; ?>/<?php echo $singleImage['image']; ?>' target='_blank'><img src = 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/c2/F_icon.svg/1024px-F_icon.svg.png' style = 'width: 28px; float: right;'></a>
        </div>
      
        <div class = "text-left photo-things like-space">
        <?php
        //for number of likes in the photo
        $querya = "SELECT * FROM likes_table WHERE photo_id = ".$singleImage['id'];
        $resa = mysqli_query($conn, $querya);
        $likesNumber = mysqli_num_rows($resa);
        if ($likesNumber != 0) {
          if ($likesNumber == 1) {
            echo "<span class='likes-modal' data-toggle='modal' data-target='.bs-example-modal-sm".$singleImage['id']."'>1</span> like";
          } else {
            echo "<span class='likes-modal' data-toggle='modal' data-target='.bs-example-modal-sm".$singleImage['id']."'>".$likesNumber."</span> likes";
          }
        }
        ?>
        </div> 
        <!-- modal for the likes data -->
        <?php
        if (isset($_SESSION['user'])) {
        ?>
        <div class="modal fade bs-example-modal-sm<?php echo $singleImage['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
          <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
              <div id = "ajax-holder" style = "margin: 40px;">
              <?php 
              for ($i1 = 0; $i1 < $likesNumber; $i1 ++) {
                $x = mysqli_fetch_array($resa);
                $queryb = "SELECT * FROM users WHERE userName = '".$x['liker']."'";
                $resb = mysqli_query($conn, $queryb);
                $likerArray = mysqli_fetch_array($resb);
                if ($likerArray['profileImage'] == "") {
                  $tempImageUrl = "profilePicture.png";
                } else {
                  $tempImageUrl = "uploads/".$likerArray['userName']."/".$likerArray['profileImage'];
                }
              ?>
                <div class = 'liker-picture' style = "background-image: url('<?php echo $tempImageUrl; ?>')">
                  <span id = "liker-name"><a class = 'profile-anchor' href = '/album/<?php echo $x['liker']; ?>'><?php echo $x['liker']; ?></a></span>
                </div>
              <?php } ?>
              </div>
            </div>
          </div>
        </div> 
        <?php
        }
        ?>      
        <p class = "text-left photo-things" style = 'font-style: italic;'><?php echo dateAndTime($singleImage['dateOfUpload']); ?></p>
        <p class = "text-left photo-things" style = "font-family: georgia;"><?php echo $singleImage['caption']; ?></p>

        </div><br>
        <?php
      }
    ?>
    <nav aria-label="...">
      <ul class="pager">
        <li class="previous <?php if ($_GET['a'] == 0) { echo "disabled";} ?>"><a href="feed.php?a=<?php echo --$_GET['a']; ?>"><span aria-hidden="true">&larr;</span> Older</a></li>
        <li class="next <?php if ($grandTotal < 10 || $grandTotal1 == 0) { echo "disabled";} ?>"><a href="feed.php?a=<?php $_GET['a'] += 2; echo $_GET['a']; ?>">Newer <span aria-hidden="true">&rarr;</span></a></li>
      </ul>
    </nav>
    <button class = "btn not-logged-in-trigger" data-toggle="modal" data-target=".bs-example-modal-sm">Large modal</button>
    <div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
      <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content" style = "font-family: georgia; font-size: 20px; padding: 20px;">
          <p>Please login or register.</p>
          <div class = "row">
              <div class = "col-sm-6">
                <a href = "index.php"><button id = "login-button" class = "btn btn-success">Login</button></a>
              </div>
              <div class = "col-sm-6">
                <a href = "register.php"><button class = "btn btn-primary">Register</button></a>
              </div>
          </div>
        </div>
      </div>
    </div>
  </div>


  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
  <script type="text/javascript">
    $(document).ready(function() {
      $(".heart-icon").click(function() {
        if ($.trim($("#hidden").text()) == "") {
          $(".not-logged-in-trigger").trigger("click");
        } else {
          var idNumber = this.id;
          $.ajax({
            type: "POST",
            url: "ajax.php",
            data: {
              id: this.id,
              liker: $("#hidden").text()
            },
            success: function(response) {
              var partsArray = response.split("!");
              $("#" + idNumber + " .like-space").html(partsArray[0]);
              $("#" + idNumber + " #ajax-holder").html(partsArray[1]);

              if ($("#" + idNumber + " .heart-icon").css("color") == "rgb(223, 43, 39)") {
                $("#" + idNumber + " .heart-icon").css("color", "#000");
              } else {
                $("#" + idNumber + " .heart-icon").css("color", "#DF2B27");              
              } 
            }
          });
        }
      });
      $(".likes-modal").click(function() {
        if ($.trim($("#hidden").text()) == "") {
          $(".not-logged-in-trigger").trigger("click");
        }
      });
      $(".profile-anchor").click(function() {
        if ($.trim($("#hidden").text()) == "") {
          $(".not-logged-in-trigger").trigger("click");
        }
      });
    });
  </script>
</body>
</html>
