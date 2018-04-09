<?php

  class User
  {
    // Gets data in JSON format from the contest page

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

      $timesplayed = count($ratings);
      $rating = 1500;
      $volatility = 125;

      for($i = 0 ; $i < $timesplayed ; $i++)
      {
        $volatility = $this -> newvolatility($timesplayed, $rating, $ratings[$i], $volatility);
        $rating = $ratings[$i];

        $volatility = max(75, $volatility);
        $volatility = min(200, $volatility);
      }

      return [$volatility, $timesplayed, $ratings[$timesplayed - 1]];
    }

    function generate($contestname)
    {
      include ('connect.php');

      $participant = [];

      $url = "https://codechef.com/api/rankings/".$contestname."?page=1";

      $data = file_get_contents($url);
      $contest = json_decode($data,true);

      $pages = $contest['availablePages'];
      $total = $contest['totalItems'];
      $perpage = 50;
      $i = 0;

      for($page = 1 ; $page <= $pages ; $page++)
      {
        $url = "https://codechef.com/api/rankings/".$contestname."?page=".$page;
        $data = file_get_contents($url);
        $contest = json_decode($data,true);

        if($page == $pages)
        {
          $items = $total % $perpage;

          for($item = 0 ; $item < $items ; $item++)
          {
            $username = $contest['list'][$item]['user_handle'];
            $country = $contest['list'][$item]['country'];
            $institution = $contest['list'][$item]['institution'];
            $rating = $contest['list'][$item]['rating'];
            $rank = $contest['list'][$item]['rank'];

            $query = "SELECT * FROM ".mysqli_real_escape_string($link, $contestname)." WHERE username = '".mysqli_real_escape_string($link, $username)."'";
            $res = mysqli_query($link, $query);

            if(mysqli_num_rows($res) != 0)
            {
              $query_cc = "UPDATE ".mysqli_real_escape_string($link, $contestname)." SET `rank` = '".mysqli_real_escape_string($link, $rank)."' WHERE username = '".mysqli_real_escape_string($link, $username)."'";

              mysqli_query($link, $query_cc);

              continue;
            }


            $participant[$i]['username'] = $username;
            $participant[$i]['country'] = $country;
            $participant[$i]['institution'] = $institution;
            $participant[$i]['rating'] = $rating;


            $pg = $this -> volatility($username);
            $participant[$i]['volatility'] = $pg[0];
            $participant[$i]['timesplayed'] = $pg[1];
            $participant[$i]['rating'] = $pg[2];

            if($participant[$i]['timesplayed'] == 0)
            {
              $participant[$i]['rating'] = 1500;
            }
            $participant[$i]['rank'] = $rank;

            $query = "SELECT * FROM ".mysqli_real_escape_string($link, $contestname)." WHERE username = '".mysqli_real_escape_string($link, $participant[$i]['username'])."'";
            $res = mysqli_query($link, $query);

            if(mysqli_num_rows($res) == 0)
            {
              $query = "INSERT INTO ".mysqli_real_escape_string($link, $contestname)." (`rank`, `username`, `institution`, `country`, `volatility`, `rating`, `newrating`, `timesplayed`) VALUES ('".mysqli_real_escape_string($link, $participant[$i]['rank'])."', '".mysqli_real_escape_string($link, $participant[$i]['username'])."', '".mysqli_real_escape_string($link, $participant[$i]['institution'])."', '".mysqli_real_escape_string($link, $participant[$i]['country'])."', '".mysqli_real_escape_string($link, $participant[$i]['volatility'])."', '".mysqli_real_escape_string($link, $participant[$i]['rating'])."', '".mysqli_real_escape_string($link, $participant[$i]['rating'])."', '".mysqli_real_escape_string($link, $participant[$i]['timesplayed'])."')";

              mysqli_query($link, $query);
            }

            else
            {
              $query = "UPDATE ".mysqli_real_escape_string($link, $contestname)." SET `rank` = '".mysqli_real_escape_string($link, $participant[$i]['rank'])."', `institution` = '".mysqli_real_escape_string($link, $participant[$i]['institution'])."', `country` = '".mysqli_real_escape_string($link, $participant[$i]['country'])."', `volatility` = '".mysqli_real_escape_string($link, $participant[$i]['volatility'])."', `rating` = '".mysqli_real_escape_string($link, $participant[$i]['rating'])."', `timesplayed` = '".mysqli_real_escape_string($link, $participant[$i]['timesplayed'])."' WHERE username = '".mysqli_real_escape_string($link, $participant[$i]['username'])."'";

              mysqli_query($link, $query);
            }

            $i++;
          }
        }

        else
        {
          for($item = 0 ; $item < $perpage ; $item++)
          {
            $username = $contest['list'][$item]['user_handle'];
            $country = $contest['list'][$item]['country'];
            $institution = $contest['list'][$item]['institution'];
            $rating = $contest['list'][$item]['rating'];
            $rank = $contest['list'][$item]['rank'];

            $query = "SELECT * FROM ".mysqli_real_escape_string($link, $contestname)." WHERE username = '".mysqli_real_escape_string($link, $username)."'";
            $res = mysqli_query($link, $query);

            if(mysqli_num_rows($res) != 0)
            {
              $query_cc = "UPDATE ".mysqli_real_escape_string($link, $contestname)." SET `rank` = '".mysqli_real_escape_string($link, $rank)."' WHERE username = '".mysqli_real_escape_string($link, $username)."'";

              mysqli_query($link, $query_cc);

              continue;
            }

            $participant[$i]['username'] = $username;
            $participant[$i]['country'] = $country;
            $participant[$i]['institution'] = $institution;
            $participant[$i]['rating'] = $rating;

            $pg = $this -> volatility($username);
            $participant[$i]['volatility'] = $pg[0];
            $participant[$i]['timesplayed'] = $pg[1];
            $participant[$i]['rating'] = $pg[2];

            if($participant[$i]['timesplayed'] == 0)
            {
              $participant[$i]['rating'] = 1500;
            }

            $participant[$i]['rank'] = $rank;

            $query = "SELECT * FROM ".mysqli_real_escape_string($link, $contestname)." WHERE username = '".mysqli_real_escape_string($link, $participant[$i]['username'])."'";
            $res = mysqli_query($link, $query);

            if(mysqli_num_rows($res) == 0)
            {
              $query = "INSERT INTO ".mysqli_real_escape_string($link, $contestname)." (`rank`, `username`, `institution`, `country`, `volatility`, `rating`, `newrating`, `timesplayed`) VALUES ('".mysqli_real_escape_string($link, $participant[$i]['rank'])."', '".mysqli_real_escape_string($link, $participant[$i]['username'])."', '".mysqli_real_escape_string($link, $participant[$i]['institution'])."', '".mysqli_real_escape_string($link, $participant[$i]['country'])."', '".mysqli_real_escape_string($link, $participant[$i]['volatility'])."', '".mysqli_real_escape_string($link, $participant[$i]['rating'])."', '".mysqli_real_escape_string($link, $participant[$i]['rating'])."', '".mysqli_real_escape_string($link, $participant[$i]['timesplayed'])."')";

              mysqli_query($link, $query);
            }

            else
            {
              $query = "UPDATE ".mysqli_real_escape_string($link, $contestname)." SET `rank` = '".mysqli_real_escape_string($link, $participant[$i]['rank'])."', `institution` = '".mysqli_real_escape_string($link, $participant[$i]['institution'])."', `country` = '".mysqli_real_escape_string($link, $participant[$i]['country'])."', `volatility` = '".mysqli_real_escape_string($link, $participant[$i]['volatility'])."', `rating` = '".mysqli_real_escape_string($link, $participant[$i]['rating'])."', `timesplayed` = '".mysqli_real_escape_string($link, $participant[$i]['timesplayed'])."' WHERE username = '".mysqli_real_escape_string($link, $participant[$i]['username'])."'";

              mysqli_query($link, $query);
            }

            $i++;

          }
        }
      }

      return $participant;
    }
  }

?>
