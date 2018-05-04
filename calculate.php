<?php

  class Calculate
  {
    //Eab
    function eab($ra, $va, $rb, $vb)
    {
      $ch = ($ra - $rb)/(sqrt($va * $va + $vb * $vb));
      return (1/(1 + pow(4, $ch)));
    }

    //Expected Rank
    function Erank($participant, $ind)
    {
      $size = count($participant);

      $ERank = 0.0;
      $ra = $participant[$ind]['rating'];
      $va = $participant[$ind]['volatility'];

      for($i = 0 ; $i < $size ; $i++)
      {

        $ERank += $this -> eab($ra, $va, $participant[$i]['rating'], $participant[$i]['volatility']);
      }

      return $ERank;
    }

    function calculaterating($contestname)
    {
      include ('connect.php');
      $query = "SELECT * FROM ".mysqli_real_escape_string($link, $contestname)." ORDER BY score desc";

      $res = mysqli_query($link, $query);


      if(!$res)
      {
        $query = "SELECT * FROM ".mysqli_real_escape_string($link, $contestname);
        $res = mysqli_query($link, $query);
      }

      $i = 0;
      $participant = [];

      $countrank = [];
      $rank = 0;
      $previousscore = 99999;
      $ratingavg = 0.0;
      $pg = 0;

      while($ans = mysqli_fetch_array($res))
      {
        $pg++;
        if($previousscore > $ans['score'])
        {
          $rank = $rank + $pg;
          $previousscore = $ans['score'];
          $pg = 0;
        }

        $participant[$i]['username'] = $ans['username'];
        $participant[$i]['rank'] = $rank;
        $ans['rank'] = $rank;
        $countrank[$ans['rank']]++;

        $participant[$i]['volatility'] = $ans['volatility'];
        //$participant[$i]['volatility'] = 125;


        /*if($participant[$i]['volatility'] < 80)
        $participant[$i]['volatility'] = 143;

        else// if($participant[$i]['volatility'] > 100 && $participant[$i]['volatility'] < 125)
        $participant[$i]['volatility'] = 93;*/


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

      $cf = sqrt(($va2/($n + $pr)) + ($rravg/($n - 1 + $pr)));

      for($i = 0 ; $i < $n ; $i++)
      {
        $erank = $this -> Erank($participant, $i);

        //$erank = abs($erank);
        $pr = $countrank[$participant[$i]['rank']];
        $pr = 1;

        if($erank != 1)
        {
          $eperf = log(($n)/($erank - 1))/(log(4));
        }

        else
        {
          $eperf = log(($n)/(1.01 - 1))/(log(4));
        }

        //var ECPerf = Math.log((N/(curr.rank - 1 + add) - 1)/(N/EPerf - 1));
        $ecperf = log(($n/($participant[$i]['rank'] - 1 + $pr) - 1)/($n/$eperf - 1));
        $ecperf /= log(4);

        if($participant[$i]['rank'] != 1)
        {
          $aperf = log(($n)/($participant[$i]['rank'] - 1))/(log(4));
        }

        else
        {
          $aperf = log(($n)/(1.01 - 1))/(log(4));
        }

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

        $query = "UPDATE ".mysqli_real_escape_string($link, $contestname)." SET newrating = '".mysqli_real_escape_string($link, $newrating)."', rank = '".mysqli_real_escape_string($link, $rank)."' WHERE username = '".mysqli_real_escape_string($link, $username)."'";

        mysqli_query($link, $query);
      }

    }
  }


?>
