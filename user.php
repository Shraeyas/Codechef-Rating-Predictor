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

    function newvolatility($timesplayed, $previousrating, $newrating, $volatility)
    {
      $volatilityweight = ((0.5 * $timesplayed + 0.8)/($timesplayed + 0.6));

      $volatility = sqrt(($volatilityweight * ($newrating - $previousrating) * ($newrating - $previousrating) + $volatility * $volatility)/($volatilityweight + 1.1));

      return $volatility;
    }

    function volatility($username)
    {
      $ratings = $this -> getratings($username);

      $timesplayed = 0;
      $rating = 1500;
      $volatility = 125;

      for($i = 0 ; $i < $timesplayed ; $i++)
      {
        $volatility = $this -> newvolatility($timesplayed, $rating, $ratings[$i], $volatility);
        $rating = $ratings[$i];

        $volatility = max(75, $volatility);
        $volatility = min(200, $volatility);
      }

      return $volatility;
    }
  }

  $ob = new User ();
  echo $ob -> volatility("shivam_iet");

?>
