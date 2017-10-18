<?php
ob_start();
session_start();
require_once 'dbconnect1.php';
if (isset($_GET['profile'])) {
  $query = "SELECT * FROM users WHERE userName = '".$_GET['profile']."'";
  $res = mysqli_query($conn, $query);
  $row = mysqli_fetch_array($res);
  if (mysqli_num_rows($res) == 0) {
    header("Location: /album/home.php?noProfile=yes");
  }
  if ($row['userId'] == $_SESSION['user']) {
    header("Location: home.php");
  } else {
    $myProfile = 0;
  }  
}

 //dataAndTime user defined function
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
 /*//if get variable a is invalid one
 if (isset($_GET['a']) && !(is_int($_GET['a']) && intval($_GET['a'] > '0'))) {
  header("Location: home.php");
 } */
 // if session is not set this will redirect to login page
 if( !isset($_SESSION['user']) ) {
  header("Location: index.php");
  exit;
 }
 // select loggedin users detail
 $res=mysqli_query($conn, "SELECT * FROM users WHERE userId=".$_SESSION['user']);
 $userRow=mysqli_fetch_array($res);
?>

<!--upload script -->
<?php
$uploadOk = "";
$messageCounter = 0;
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['bio'])) {
  $bio = $_POST['bio'];
  $query = "UPDATE users SET bio = '".$bio."' WHERE userId = ".$_SESSION['user'];
  $res = mysqli_query($conn, $query);
  header("Location: home.php");
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['fileToUpload']['name'])) {
  if ($_FILES['fileToUpload']['name'] !== "") {
    $target_dir = "/album/uploads/".$userRow['userName']."/";
    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
    $uploadOk = 1;
    $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
    // Check if image file is a actual image or fake image
    if(isset($_POST["submit"])) {
        $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
        if($check !== false) {
            $uploadOk = 1;
        } else {
            $message = "File is not an image.";
            $uploadOk = 0;
        }
    }
    // Check if file already exists
    if (file_exists($target_file) && $uploadOk == 1) {
        $message = "Sorry, file already exists.";
        $uploadOk = 0;
    }
    // Check file size
    if ($_FILES["fileToUpload"]["size"] > 500000 && $uploadOk == 1) {
        $message = "Sorry, your file is too large.";
        $uploadOk = 0;
    }
    // Allow certain file formats
    if($imageFileType != "JPG" && $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    && $imageFileType != "gif"  && $uploadOk == 1) {
        $message = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }
    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk !== 0) {
      if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
          $message = "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been successfully uploaded.";
          $timeAmountArray = preg_split('/ /', date('Y m d g i a'));
          $timeAmount = ($timeAmountArray[0]-2017)*365*24*60*60 + $timeAmountArray[1]*30*24*60*60+$timeAmountArray[2]*24*60*60+$timeAmountArray[3]*60*60+$timeAmountArray[4]*60;
          $query = "INSERT INTO photos_collection (username, image, caption, dateOfUpload, timeAmount) VALUES ('".$userRow['userName']."','".basename( $_FILES["fileToUpload"]["name"])."','".$_POST['caption']."','".date('Y m d g i a F l')."','".$timeAmount."');";
          $res = mysqli_query($conn, $query);


      } else {
          $message = "Sorry, there was an error uploading your file.";
      }
    }
  }
  
} 
//for profile picture uploading
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['fileToUpload1']['name'])) {
  if ($_FILES['fileToUpload1']['name'] !== "") {
    $target_dir = "uploads/".$userRow['userName']."/";
    $target_file = $target_dir . basename($_FILES["fileToUpload1"]["name"]);
    $uploadOk = 1;
    $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
    // Check if image file is a actual image or fake image
    if(isset($_POST["submit"])) {
        $check = getimagesize($_FILES["fileToUpload1"]["tmp_name"]);
        if($check !== false) {
            $uploadOk = 1;
        } else {
            $message = "File is not an image.";
            $uploadOk = 0;
        }
    }
    // Check file size
    if ($_FILES["fileToUpload1"]["size"] > 1000000 && $uploadOk == 1) {
        $message = "Sorry, your file is too large.";
        $uploadOk = 0;
    }
    // Allow certain file formats
    if($imageFileType != "JPG" && $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    && $imageFileType != "gif"  && $uploadOk == 1) {
        $message = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }
    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk !== 0) {
      if (move_uploaded_file($_FILES["fileToUpload1"]["tmp_name"], $target_file)) {
          $message = "The file ". basename( $_FILES["fileToUpload1"]["name"]). " has been successfully updated as the profile picture.";
          $query = "UPDATE users SET profileImage ='".$_FILES['fileToUpload1']['name']."' WHERE userId =".$userRow['userId'];
          $res = mysqli_query($conn, $query);
      } else {
          $message = "Sorry, there was an error updating your profile picture.";
      }
    }
  } 
}
?>
<!DOCTYPE html>
<html>
<head>
<meta property="og:image" content="http://creatovert.com/album/display.jpg" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta charset="utf-8"/>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Welcome - <?php echo $userRow['userName']; ?></title>
<link rel="icon" href="/album/logo.png">
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<link rel="stylesheet" href="/album/style.php" type="text/css" />
<link rel="stylesheet" type="text/css" href="lightbox.css">
<style type = "text/css">
  .modal-content {
    font-family: georgia;
    font-size: 1.25em !important;
  }
  .bio-form {
    display: none;
  }
  /*.message {
    margin: 14px auto -8px auto;
    display: inline-block;
    padding: 6px !important;
    margin-right: 5px;
  }*/
  .image-container li {
    list-style: none;
    display: inline-block;
  }
  .image {
    min-width: 270px;
    min-height: 300px;
    background-repeat: no-repeat;
    background-size: cover;
    background-position: center;
    margin-bottom: 5px;
  }
  textarea {
    resize: none;
  }
  .bio-display {
    font-size: 1.2em;
    font-family: georgia;    
  }
  .photo-panel {
    margin-top: 20px;
  }
  .no-pictures {
    font-family: Georgia;
    font-size: 1.25em;
    cursor: pointer;
  }
  .page-header {
    margin: 20px 0px 20px;
    padding-bottom: 0px;
    border: none;
  }
  .photo-div {
    overflow: hidden;
  }
  @media screen and (min-width: 990px) {
    .two-photos-wrapper {
      padding-left: 0px;
      overflow: hidden;
    }
    .first-two-images-wrapper-in-a-row {
      padding-right: 0px;
    }
  }
  * {
    box-sizing: border-box;
  }
  @media screen and (max-width: 600px) {
    .page-header {
      text-align: center;
      margin: 10px 0px 0px 0px;
    }
    .page-header button {
      float: none !important;
      text-align: center;
      display: block;
      margin: 10px auto;
    }
  }
  .disabled, .pagination .active {
    pointer-events: none;
  }
  .profile-row {
    padding-top: 20px;
    background-color: #fff;
    margin-left: 0px;
    margin-right: 0px;
  }
  @media screen and (min-width: 988px) {
    .sub-div {
      width: 80%;
    }
  }
  .profile-picture {
    width: 150px;
    height: 150px;
    background-repeat: no-repeat;
    background-size: cover !important;
    background-position: center !important;
    border-radius: 50%;
    margin: auto;
    margin-bottom: 20px;
  }
  .updateProPic:hover {
    text-decoration: underline;
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
  .impfortant-class {
    margin-bottom: 0px !important;
  }
  .x {
    margin-top: 20px;
  }
  .wrapper {
    position: absolute;
    top: 0px;
  }
  
  .navbar-default .navbar-nav>.open>a, .navbar-default .navbar-nav>.open>a:focus, .navbar-default .navbar-nav>.open>a:hover {
    color: #B92B27;
    font-weight: bold;
    background-color: #FBFBFB;
  }
  .dropdown-menu>li>a:focus, .dropdown-menu>li>a:hover {
    color: #FBFBFB;
    text-decoration: none;
    background-color: #B92B27;
  }
  .inline-div {
    display: inline-block;
  }
  .gotit-background {
     background-color: #E2E9EE;
  }
</style>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
<script src = "lightbox.js"></script>

<script>
$(document).ready(function() {

  if ($.trim($(".message").text()).length != 0) {
    $(".profile-row").addClass("important-class");
    $(".message").fadeOut(4000);
  }        

  $(".upload-anchor").click(function() {
    $(".big-button").trigger("click");
  });
  $(".bio-update").click(function() {
    $(".bio-display").css("display", "none");
    $(".bio-form").css("display", "block");
  });
});
</script>
</head>
<body>

 <?php include "header.php"; ?> 
  
  <div id="wrapper">
  <?php 
  if (!isset($_GET['noProfile'])) {
  ?>
    <div class = "container x">
      <?php if (isset($message)) { ?>
        <div class = "text-center message alert alert-<?php if ($uploadOk === 1) { echo "success";} else if ($uploadOk === 0) { echo "danger";} ?>"><?php echo $message;?></div>
      <?php } ?>
    </div>
    <div class="container">
      <div class = "row text-center profile-row">
        <div class = "col-sm-4 text-center">
          <?php 
          $res=mysqli_query($conn, "SELECT * FROM users WHERE userId=".$_SESSION['user']);
          $userRow=mysqli_fetch_array($res);
          if (isset($myProfile)) {
            $userRow['userName'] = $row['userName'];
            $userRow['profileImage'] = $row['profileImage'];
          }
          if ($userRow['profileImage'] == NULL) { $proPicTemp = "/album/profilePicture.png";} else {$proPicTemp = "/album/uploads/".$userRow['userName']."/".$userRow['profileImage'];}
          ?>
          <div class = 'profile-picture thumbnail' style = 'background: url("<?php echo $proPicTemp; ?>");'>
          </div>
          <?php if (!isset($myProfile)) { ?>
          <p class = "updateProPic" style = "color: #4080ff; cursor: pointer;" data-toggle="modal" data-target=".bs1-example-modal-sm">Update New Profile Picture</p>
          <?php } ?>
          <div id = "upload-modal" class="modal fade bs1-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" style = "padding-right: 0px;">
            <div class="modal-dialog modal-sm" role="document">
              <div class="modal-content text-center" style = "color: black; padding: 20px; font-size: 15px;"> 
                <strong>Choose</strong> a image and update it as the <span style = "font-style: italic;">profile picture.</span><br><br>
                <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" enctype="multipart/form-data">
                  <input type = "file" class = "btn" name = "fileToUpload1" id = "fileToUpload1"><br>
                  <input type = "submit" name = "submit" value = "Upload" class = "btn btn-success pro-pic">      
                </form>
              </div>
            </div>
          </div>
        </div>
        <div class = "col-sm-8 text-left">
          <div class = "sub-div">
            <p style = 'font-size: 20px; margin-bottom: 0px;'>
            <?php 
            if (isset($myProfile)) {
              echo $row['userName'];
            } else {
              echo "About Me";
            } 
            ?>
            </p>
            <?php
            if (!isset($myProfile)) {
            ?>
            <a class = "bio-update" style = "cursor: pointer;"><p style = "color: #4080ff; margin-bottom: 10px;"><?php if ($userRow['bio'] == NULL) { echo "Write Short Introductory Bio";} else { echo "Edit";}?></p></a>
            <form class = "bio-form" method = "POST" action = "<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
              <div class = "form-group"><textarea rows = "3" cols = "20" class = "form-control" name = 'bio' maxlength="200" autofocus><?php echo $userRow['bio']; ?></textarea></div>
              <div class = "form-group"><input type = "submit" value = "Update" class = "btn btn-info"></div>
            </form>
            <?php } ?>
            <p class = "bio-display"><?php if (isset($myProfile)) { echo $row['bio']; } else { echo $userRow['bio']; } ?></p>
          </div>
        </div>
      </div>
      <div class="panel panel-success photo-panel">

        <div class="panel-heading">

          <!-- Small modal -->
          <div class="page-header">
            <h2 style = "display: inline;">Photos Collection</h2>
            <button type="button" class="btn btn-info big-button" data-toggle="modal" data-target=".bs-example-modal-sm" style = "float: right;">Upload a New Picture</button> 
            <div class = "clearfix"></div>    
          </div>
          

          <div id = "upload-modal" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" style = "padding-right: 0px;">
            <div class="modal-dialog modal-sm" role="document">
              <div class="modal-content text-center" style = "color: black; padding: 20px; font-size: 15px;"> 
                Click on <strong>"Choose File"</strong> button and choose a photo to upload.  Then, click on <strong>"Upload"</strong> to upload it.<br><br>
                <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" enctype="multipart/form-data">
                  <input type = "file" class = "btn" name = "fileToUpload" id = "fileToUpload"><br>
                  <span style = "text-align: left;">Caption</span>
                  <textarea style = "margin-bottom: 10px;" class="form-control" rows="3" name = "caption"></textarea>
                  <input type = "submit" name = "submit" value = "Upload" class = "btn btn-success upload-button">      
                </form>
              </div>
            </div>
          </div>
        </div>

        <div class="panel-body panel-body-gallery">
          <div class="row image-container">
            <div class="col-lg-12">
              <?php
              $groupNumber = 8;
              $start = 0;
              if (isset($_GET['a'])) {
                $start = $start + $groupNumber*($_GET['a']-1); 
              }
              $query = "SELECT * FROM photos_collection WHERE username = '".$userRow['userName']."' ORDER BY id DESC LIMIT ".$start.",".$groupNumber;
              $res = mysqli_query($conn, $query);
              $imageTotal = mysqli_num_rows($res);
              //$array = mysqli_fetch_array($res);
              //var_dump($array);

              $query1 = "SELECT image, caption, dateOfUpload FROM photos_collection WHERE username = '".$userRow['userName']."' ORDER BY id DESC LIMIT ".($start+$groupNumber).",".$groupNumber;
              $res1 = mysqli_query($conn, $query1);
              $imageTotal1 = mysqli_num_rows($res1);

              $query2 = "SELECT image, caption, dateOfUpload FROM photos_collection WHERE username = '".$userRow['userName']."' ORDER BY id DESC LIMIT ".($start+2*$groupNumber).",".$groupNumber;
              $res2 = mysqli_query($conn, $query2);
              $imageTotal2 = mysqli_num_rows($res2);

              $query3 = "SELECT image, caption, dateOfUpload FROM photos_collection WHERE username = '".$userRow['userName']."' ORDER BY id DESC LIMIT ".($start+3*$groupNumber).",".$groupNumber;
              $res3 = mysqli_query($conn, $query3);
              $imageTotal3 = mysqli_num_rows($res3);

              $query4 = "SELECT image, caption, dateOfUpload FROM photos_collection WHERE username = '".$userRow['userName']."' ORDER BY id DESC LIMIT ".($start+4*$groupNumber).",".$groupNumber;
              $res4 = mysqli_query($conn, $query4);
              $imageTotal4 = mysqli_num_rows($res4);

              $query5 = "SELECT image, caption, dateOfUpload FROM photos_collection WHERE username = '".$userRow['userName']."' ORDER BY id DESC LIMIT ".($start+5*$groupNumber).",".$groupNumber;
              $res5 = mysqli_query($conn, $query5);
              $imageTotal5 = mysqli_num_rows($res5);

              if ($imageTotal == 0) {
                echo "<p class = 'no-pictures'>There are no pictures to show.  <a class = 'upload-anchor'>Upload</a> the first today!</p>";
                } 
              ?>
              
              <ul>
                  <?php
                  $i = 0;
                  $counter = 0;
                  while ($i < ceil($imageTotal/4)) {  
                    echo "<div class = 'row photo-row'>";
                    $j = 0;
                    for ($j = 0; $j < 4; $j++) {
                      if ($j == 0 || $j == 2) {
                        echo "<div class = 'col-md-6 two-photos-wrapper";
                        if ($j == 0) {echo " first-two-images-wrapper-in-a-row";};
                        echo "'>";
                      }
                      echo "<div style ='margin-bottom: 15px;' class = 'col-sm-6 photo-div'>";
                      $userPhotos = mysqli_fetch_array($res);
                      echo "<a href = 'uploads/".$userRow['userName']."/".$userPhotos['image']."' data-lightbox = 'gallery'>";
                      $url = '"/album/uploads/'.$userRow['userName'].'/'.$userPhotos['image'].'"';
                      echo "<div class = 'image thumbnail' style = 'background-image: url($url);'>";
                      echo "</div>";
                      echo "</a>";
                      ?>

                      <div>
                        <div class = 'inline-div'>
                        <span style = 'font-size: 12px; font-style: italic;'>
                          <?php echo dateAndTime($userPhotos['dateOfUpload']); ?>
                        </span>
                        </div>
                        <div style = 'float: right;' class = 'inline-div'>
                          <?php if (!isset($myProfile)) { ?>
                          <button data-toggle='modal' data-target='.bs-example-modal-lg-del-<?php echo $userPhotos['id']; ?>' style = 'max-height: 20px; padding: 0px 3px; font-style: normal;' class = 'btn btn-danger <?php echo $userPhotos['id']; ?>'>Delete</button>
                          <?php } ?>
                        </div>
                        <div><?php echo $userPhotos['caption']; ?></div>
                        </div>
                      <?php 
                      echo "<div class='modal fade bs-example-modal-lg-del-".$userPhotos['id']."' tabindex='-1' role='dialog' aria-labelledby='myLargeModalLabel'><div class='modal-dialog modal-lg-del-".$userPhotos['id']."' role='document'><div class='modal-content text-center' style = 'padding: 20px;'>";
                      echo "<p>Are you sure you want to delete the photo?</p>";
                      echo "<div><button style = 'margin-right: 10px;' class = 'btn btn-primary delete-yes' id = '".$userPhotos['id']."'>Yes</button><button style = 'margin-left: 10px;' class = 'btn btn-default' data-dismiss = 'modal'>No</button></div>";
                      echo "</div></div></div>";
                      echo "</div>";
                      $counter++;
                      if ($counter >= $imageTotal) {
                        break;
                      }
                      if ($j == 1 || $j == 3) {
                        echo "</div>";
                      }
                    }
                    echo "</div>";
                    $i++;
                  }
                  ?>
              </ul> 
            </div>
          </div>

          <?php if ($imageTotal != 0) { ?>
          <div class = 'pagination'>
            <nav aria-label="Page navigation">
              <ul class="pagination pagination-lg">
                <li>
                  <a class = '<?php if (!isset($_GET['a']) || $_GET['a'] == 1 ) { echo "disabled"; } ?>' href=" 
                  home.php?a=<?php 
                  if (isset($_GET['a'])) {
                    echo $_GET['a'] - 1;
                  }
                  if (isset($_GET['profile'])) { echo "&profile=".$_GET['profile']; } ?>" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                  </a>
                </li>
                <?php 
                  if (!isset($_GET['a'])) {
                    $i = 0;
                  } else {
                    $i = $_GET['a'] - 1;
                  }
                  $counter = 0;
                  $counter1 = 0;
                  $counter2 = 0;
                  $counter3 = 0;
                  $counter4 = 0;
                  for ($k = 0; $k < 5; $i++) {
                    echo "<li class = '";
                    if ((isset($_GET['a']) && $_GET['a'] == ($i+1)) || (!isset($_GET['a']) && $i == 0)) {
                      echo "active";
                    }
                    echo "'><a href='home.php?";
                    if (isset($_GET['profile'])) {
                    echo "profile=".$_GET['profile']."&";
                    }
                    echo "a=".($i+1)."'>".($i+1)."</a></li>";
                    $k++;
                    if ($imageTotal < $groupNumber || $imageTotal1 == 0) {
                      break;
                    }
                    if ($imageTotal1 < $groupNumber || $imageTotal2 == 0) {
                      if ($counter == 1) {
                        break;
                      }
                      $counter++;
                    }
                    if ($imageTotal2 < $groupNumber || $imageTotal3 == 0 ) {
                      if ($counter1 == 2) {
                        break;
                      }
                      $counter1++;
                    }
                    if ($imageTotal3 < $groupNumber || $imageTotal4 == 0 ) {
                      if ($counter2 == 3) {
                        break;
                      }
                      $counter2++;
                    }
                    if ($imageTotal4 < $groupNumber || $imageTotal5 == 0 ) {
                      if ($counter3 == 4) {
                        break;
                      }
                      $counter3++;
                    }
                  }
                ?>
                <li>
                  <a  class = '<?php if ($imageTotal < $groupNumber || $imageTotal1 == 0) { echo "disabled"; } ?>' href="
                  <?php 
                  if (!isset($_GET['a'])) {
                    $_GET['a'] = 1;
                  }
                  echo htmlspecialchars($_SERVER['PHP_SELF']).'?a=';
                  if (isset($_GET['a'])) {
                    echo $_GET['a'] + 1;
                  } 
                  if (isset($_GET['profile'])) { echo "&profile=".$_GET['profile']; }
                  ?>
                  " aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                  </a>
                </li>
              </ul>
            </nav>
          </div>
          <?php } ?>
          
        </div>

      </div>
    </div>
    <?php
    } else {
      echo "<p class = 'alert alert-danger text-center' style = 'font-size: 1.6em; font-family: georgia;'>The profile does not exist.</p>";
    } 
    ?>
  </div>
  <?php $uploadOk = "" ?>
</body>
<script type="text/javascript">
  $(document).ready(function() {
    $(".delete-yes").click(function() {
      idNumber = this.id;
      $.ajax({
        type: "POST",
        url: "ajax.php",
        data: {deleteId: idNumber},
        success: function(response) {
          location.reload();
        }
      });
    });
  });
</script>
</html>
<?php ob_end_flush(); ?>