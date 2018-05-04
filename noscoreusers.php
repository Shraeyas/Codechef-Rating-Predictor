<?php

  include ('connect.php');
  include_once ('users.php');

  $query = "SELECT * FROM livecontests";
  $res = mysqli_query($link, $query);

  $user_ob = new User();

  $GLOBALS['ncon'] = mysqli_num_rows($res);

  while($ans = mysqli_fetch_array($res))
  {
    $start_time = time();

    $page = file_get_contents("https://www.codechef.com/submissions");
    //echo $page;

    $first = explode("'/users/", $page);

    for($i = 1 ; $i < count($first) ; $i++)
    {
      //echo $first[$i];
      if(substr_count($first[$i], $ans['contestid']) != 0)
      {
        $user = explode("'", $first[$i]);
        $username = $user[0];
        /*echo $user[0];
        echo "<br>";*/

        $query = "SELECT * FROM '".mysqli_real_escape_string($link, $ans['contestid'])."' WHERE username = '".mysqli_real_escape_string($link, $username)."'";
        $res_ = mysqli_query($link, $query);

        if(mysqli_num_rows($res_) == 0)
        {
          $volatility_ar = $user_ob -> volatility($username);

          $volatility = $volatility_ar[0];
          $timesplayed = $volatility_ar[1];
          $rating = $volatility_ar[2];

          $query = "INSERT INTO '".mysqli_real_escape_string($link, $ans['contestid'])."' (`username`, `volatility`, `timesplayed`, `rating`) VALUES (".mysqli_real_escape_string($link, $username).", ".mysqli_real_escape_string($link, $volatility).", ".mysqli_real_escape_string($link, $timesplayed).", ".mysqli_real_escape_string($link, $rating).")";

          mysqli_query($link, $query);
        }

        else
        {
          continue;
        }
      }

      else
      {
        continue;
      }

      if(time() - $start_time > 60/($GLOBALS['ncon']))
        break;
    }

    if(time() - $start_time > 60/($GLOBALS['ncon']))
      continue;
  }

?>
