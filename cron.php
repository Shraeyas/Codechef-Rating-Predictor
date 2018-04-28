<?php

  include ('connect.php');
  include_once ("contests.php");
  include_once ("database.php");
  include_once ("calculate.php");


  $query = "CREATE TABLE IF NOT EXISTS `livecontests` (`id` INT NOT NULL AUTO_INCREMENT, `contestid` VARCHAR(150) , `remove` INT, PRIMARY KEY(id))";
  mysqli_query($link, $query);

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

  

  $database_ob = new Database();
  $calculate_ob = new Calculate();

  $query = "SELECT * FROM livecontests";
  $res = mysqli_query($link, $query);
  
  $GLOBALS['ncon'] = mysqli_num_rows($res);

  while($ans = mysqli_fetch_array($res))
  {
    $database_ob -> update($ans['contestid']);
    $calculate_ob -> calculaterating($ans['contestid']);
  }
  
  //$database_ob -> update('COOK93B');
  //$calculate_ob -> calculaterating('COOK93B');

  //print_r(explode("<td>", $haystack));

  /*$query = "SELECT * FROM chomu";
  $res = mysqli_query($link, $query);

  while($ans = mysqli_fetch_array($res))
  {
    $contestname = $ans['contestid'];
  }*/
  
  $query = "DELETE FROM livecontests";
  mysqli_query($link, $query);

  for($i = 0 ; $i < $size ; $i++)
  {
    if(!($contest -> isvalid($livelist[$i], $haystack)))
      continue;

    if(($contest -> isvalid($livelist[$i], $haystack)) == 2)
    {
      $query = "INSERT INTO livecontests SET contestid = '".mysqli_real_escape_string($link, $livelist[$i].'A')."'";
      mysqli_query($link, $query);

      $query = "INSERT INTO livecontests SET contestid = '".mysqli_real_escape_string($link, $livelist[$i].'B')."'";
      mysqli_query($link, $query);

      continue;
    }

    $query = "INSERT INTO livecontests SET contestid = '".mysqli_real_escape_string($link, $livelist[$i])."'";
    mysqli_query($link, $query);

  }
  
?>
