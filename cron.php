<?php

  include ('connect.php');
  include ("valid.php");

  $query = "SELECT * FROM chomu";
  $res = mysqli_query($link, $query);

  while($ans = mysqli_fetch_array($res))
  {
    $contestname = $ans['contestid'];
  }

?>
