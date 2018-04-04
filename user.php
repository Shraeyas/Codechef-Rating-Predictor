<?php

  class User
  {
    function volatility($username)
    {
      $content = file_get_contents("https://www.codechef.com/users/".$username);
      $first = explode("var date_versus_rating_all = [", $content);
      $second = explode("];", $first[1]);

      echo ($second[0]);

    }
  }

  $ob = new User ();
  $ob -> volatility("shraeyas");

?>
