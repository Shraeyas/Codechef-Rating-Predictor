<?php

  class User
  {
    function getratings($username)
    {
      $content = file_get_contents("https://www.codechef.com/users/".$username);
      $first = explode("var date_versus_rating_all = [", $content);
      $second = explode("];", $first[1]);

      //echo ($second[0]);

      $third = explode('"rating":"', $second[0]);

      $ratings = array();

      for($i = 1 ; $i < count($third) ; $i++)
      {
        $val = explode('","rank":"', $third[$i]);
        array_push($ratings, $val[0]);
      }

      return $ratings;
    }

    function volatility($username)
    {
      $ratings = $this -> getratings($username);

      $mean = 0.0;
      for($i = 0 ; $i < count($ratings) ; $i++)
      {
        $mean = $mean + $ratings[$i];
      }

      $mean /= count($ratings);

      $volatility = 0.0;
      for($i = 0 ; $i < count($ratings) ; $i++)
      {
        $volatility += ($ratings[$i] - $mean) * ($ratings[$i] - $mean);
      }

      $volatility /= count($ratings);
      $volatility = sqrt($volatility);

      $volatility = max($volatility, 75);
      $volatility = min($volatility, 200);

      return $volatility;
    }
  }

  $ob = new User ();
  echo $ob -> volatility("shraeyas");

?>
