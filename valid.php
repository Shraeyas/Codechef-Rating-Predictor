<?php

  class Valid
  {
    function ifrunning($contestname)
    {
      $url = "https://codechef.com/contests";
      $data = file_get_contents($url);

      $first = explode("Present Contests", $data);

      $second = explode("Future Contests", $first[1]);
      $haystack = $second[0];

      if(strstr($haystack, $contestname))
      {
        return 1;
      }

      else
      {
        return 0;
      }
    }
  }

  $ob = new Valid();

  $ob -> ifrunning('APRIL8');

?>
