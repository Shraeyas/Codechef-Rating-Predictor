<?php

  class Calculate
  {
    //Eab
    function eab($ra, $va, $rb, $vb)
    {
      return (1/(1 + ($ra - $rb)/(4 * sqrt($va * $va + $vb * $vb))));
    }

    //Expected Rank
    function Erank($participant, $ind)
    {
      $size = count($participant);

      $ERank = 0.1;
      $ra = $participant[$ind]['rating'];
      $va = $participant[$ind]['volatility'];

      for($i = 0 ; $i < $size ; $i++)
      {
        if($i == $ind)
        continue;

        $ERank += $this -> eab($ra, $va, $participant[$i]['rating'], $participant[$i]['volatility']);
      }

      return $ERank;
    }

    function calculaterating($contestname)
    {
      include ('connect.php');
      $query = "SELECT * FROM ".mysqli_real_escape_string($link, $contestname);
      $res = mysqli_query($link, $query);
      $i = 0;
      $participant = [];

      $ratingavg = 0.0;

      while($ans = mysqli_fetch_array($res))
      {
        $participant[$i]['username'] = $ans['username'];
        $participant[$i]['rank'] = $ans['rank'] + 0.1;
        $participant[$i]['volatility'] = $ans['volatility'];
        $participant[$i]['rating'] = $ans['rating'];
        $participant[$i]['newrating'] = $ans['newrating'];
        $participant[$i]['timesplayed'] = $ans['timesplayed'];

        $ratingavg += $participant[$i]['rating'];
        $i++;
      }

      $n = count($participant);   //Total Participants
      $ratingavg /= $n;           //Average Rating
      $cf = 0.0;                  //Conpetition Factor

      $va2 = 0.0;
      $rravg = 0.0;

      for($i = 0 ; $i < $n ; $i++)
      {
        $va2 += $participant[$i]['volatility'] * $participant[$i]['volatility'];
        $rravg += ($participant[$i]['rating'] - $ratingavg) * ($participant[$i]['rating'] - $ratingavg);
      }

      $cf = sqrt(($va2/$n) + ($rravg/($n - 1)));

      for($i = 0 ; $i < $n ; $i++)
      {
        $erank = $this -> Erank($participant, $i);

        $erank = abs($erank);
        $eperf = log(($n)/($erank - 1))/(log(4));

        $aperf = log(($n)/($participant[$i]['rank'] - 1))/(log(4));

        $timesplayed = $participant[$i]['timesplayed'];
        $rating = $participant[$i]['rating'];
        $username = $participant[$i]['username'];

        $rwa = (0.4 * $timesplayed + 0.2)/(0.7 * $timesplayed + 0.6);

        $newrating = $rating + ($aperf - $eperf) * $cf * $rwa;

        $maxchange = 100 + (75/($timesplayed + 1)) + (100 * 500)/(abs($rating - 1500) + 500);

        if(abs($rating - $newrating) > $maxchange)
        {
          if($newrating > $rating)
          {
            $newrating = $rating + $maxchange;
          }

          else if($newrating < $rating)
          {
            $newrating = $rating - $maxchange;
          }
        }

        $query = "UPDATE ".mysqli_real_escape_string($link, $contestname)." SET newrating = '".mysqli_real_escape_string($link, $newrating)."' WHERE username = '".mysqli_real_escape_string($link, $username)."'";

        mysqli_query($link, $query);
      }

    }
  }

  /*$ob = new Calculate();
  $contest = "COOK92A";
  $ob -> calculaterating($contest);*/

?>
