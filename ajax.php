<?php
require_once 'dbconnect1.php';
if (isset($_POST['liker']) && isset($_POST['id'])) {
	$query1 = "SELECT * FROM users WHERE userId = ".$_POST['liker'].";";
	$res1 = mysqli_query($conn, $query1);
	$b = mysqli_fetch_array($res1);

	$query = "SELECT * FROM likes_table WHERE photo_id = ".$_POST['id']." AND liker = '".$b['userName']."';";
	$res = mysqli_query($conn, $query);
	$numberOfLikes = mysqli_num_rows($res);
	if ($numberOfLikes == 0) {
		$query = "SELECT * FROM photos_collection WHERE id = '".$_POST['id']."';";
		$res = mysqli_query($conn, $query);
		$a = mysqli_fetch_array($res);

		$query = "INSERT INTO likes_table (photo_id, owner, liker) VALUES ('".$_POST['id']."','".$a['username']."','".$b['userName']."');";
		$res = mysqli_query($conn, $query);

		$query = "SELECT * FROM likes_table WHERE photo_id = ".$_POST['id'];
		$res = mysqli_query($conn, $query);
		if (mysqli_num_rows($res) == 1) {
			$text = "<span class='likes-modal' data-toggle='modal' data-target='.bs-example-modal-sm".$_POST['id']."'>1</span> like";
		} else {
			$text = "<span class='likes-modal' data-toggle='modal' data-target='.bs-example-modal-sm".$_POST['id']."'>".mysqli_num_rows($res)."</span> likes";
		}

	} else {
		$query = "DELETE FROM likes_table WHERE photo_id = ".$_POST['id']." AND liker ='".$b['userName']."';";
		$res = mysqli_query($conn, $query);

		$query = "SELECT * FROM likes_table WHERE photo_id = ".$_POST['id'];
		$res = mysqli_query($conn, $query);
		if (mysqli_num_rows($res) == 1) {
			$text = "<span class='likes-modal' data-toggle='modal' data-target='.bs-example-modal-sm".$_POST['id']."'>1</span> like";
		} else if (mysqli_num_rows($res) != 0) {
			$text = "<span class='likes-modal' data-toggle='modal' data-target='.bs-example-modal-sm".$_POST['id']."'>".mysqli_num_rows($res)."</span> likes";
		} else {
			$text = "";
		}
	} 
	echo $text."!";
	$queryc = "SELECT * FROM likes_table WHERE photo_id = ".$_POST['id'];
	$resc = mysqli_query($conn, $queryc);
	$likesNumber1 = mysqli_num_rows($resc);

	for ($i1 = 0; $i1 < $likesNumber1; $i1 ++) {
	$x = mysqli_fetch_array($resc);
	$queryb = "SELECT * FROM users WHERE userName = '".$x['liker']."'";
	$resb = mysqli_query($conn, $queryb);
	$likerArray = mysqli_fetch_array($resb);
	if ($likerArray['profileImage'] == "") {
	  $tempImageUrl = "profilePicture.png";
	} else {
	  $tempImageUrl = "uploads/".$likerArray['userName']."/".$likerArray['profileImage'];
	}
?>
	<div class = 'liker-picture' style = 'background-image: url("<?php echo $tempImageUrl; ?>")'>
	  <span id = "liker-name"><a class = 'profile-anchor' href = 'home.php?profile=<?php echo $x['liker']; ?>'><?php echo $x['liker']; ?></a></span>
	</div>
<?php
	} 
} 

if (isset($_POST['deleteId'])) {
	$query = "SELECT * FROM photos_collection WHERE id = ".$_POST['deleteId'];
	$res = mysqli_query($conn, $query);
	$row = mysqli_fetch_array($res);
	unlink("uploads/".$row['username']."/".$row['image']);

	$query = "DELETE FROM photos_collection WHERE id = ".$_POST['deleteId'];
	$res = mysqli_query($conn, $query);
	
}
?>
