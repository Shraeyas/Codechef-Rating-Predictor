<?php

  include ('connect.php');
  include_once ("contests.php");

  $contest = new Contests();
  $haystack = $contest -> getlivelist();

  $livelist = array();

  $first = explode("<tbody>", $haystack);
  $second = explode('href="/', $first[1]);

  $size = count($second);

  for($i = 1 ; $i < $size ; $i++)
  {
    $pg = explode('">', $second[$i]);
    array_push($livelist, $pg[0]);
  }

  $size = count($livelist);

  for($i = 0 ; $i < $size ; $i++)
  {
    if(!($contest -> isvalid($livelist[$i], $haystack)))
      continue;

    $query = "SELECT * FROM livecontests WHERE contestid = '".mysqli_real_escape_string($link, $livelist[$i])."'";
    $res = mysqli_query($link, $query);

    if(mysqli_num_rows($res) == 0)
    {
      $query = "INSERT INTO livecontests SET contestid = '".mysqli_real_escape_string($link, $livelist[$i])."'";
      mysqli_query($link, $query);
    }
  }


  //print_r(explode("<td>", $haystack));

  /*$query = "SELECT * FROM chomu";
  $res = mysqli_query($link, $query);

  while($ans = mysqli_fetch_array($res))
  {
    $contestname = $ans['contestid'];
  }*/

?>
