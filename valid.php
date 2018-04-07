<?php

  class Valid
  {
    function valid($contestname)
    {
      $url = "https://codechef.com/contests";
      $data = file_get_contents($url);

      $first = explode("Present Contests", $data);
      $second = $first[1];

      $third = explode("Future Contests", $second);
      echo $third[0];
    }
  }

  $ob = new Valid();
  $contestname = 'APRIL18';
  $ob -> valid($contestname);

?>
