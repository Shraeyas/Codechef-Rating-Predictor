<?php

  $get = $_SERVER['REQUEST_URI'];
  $exp = explode('?', $get);

  $link = "https://www.codechef.com/";

  for($i = 1 ; $i < count($exp) ; $i++)
  {
    if($i != 1)
    {
      $link = $link.'?';
    }

    $link .= $exp[$i];
  }

  $cn = explode('/', $link);
  $cn1 = $cn[count($cn) - 1];

  $cn2 = explode('?', $cn1);
  $contestname = $cn2[0];

?>
