<?php

  class User
  {
    // Gets data in JSON format from the contest page

    function getcontestname($url)
    {
        $get = $url;
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

        return $contestname;
    }

    function getparameters($url)
    {
      $get = $url;
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

      $parameter = $cn2[1];

      if(strstr($parameter, "filterBy="))
      {
        $ss = explode("filterBy=", $parameter);
        $pg = explode("&", $ss[1]);

        $type = explode("%3D", $pg[0]);
        echo $type[0];
        echo "<br>";
        echo urldecode($type[1]);
      }
    }

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

      for($i = 0 ; $i < $timesplayed - 1 ; $i++)
      {
        $volatility = $this -> newvolatility($timesplayed, $rating, $ratings[$i], $volatility);
        $rating = $ratings[$i];

        $volatility = max(75, $volatility);
        $volatility = min(200, $volatility);
      }

      return [$volatility, $timesplayed];
    }

    function generate($contestname)
    {
      $participant = [];

      $link = "https://codechef.com/api/rankings/".$contestname."?page=1";

      $data = file_get_contents($link);
      $contest = json_decode($data,true);

      $pages = $contest['availablePages'];
      $total = $contest['totalItems'];
      $perpage = 50;
      $i = 0;

      for($page = 1 ; $page <= $pages ; $page++)
      {
        $link = "https://codechef.com/api/rankings/".$contestname."?page=".$page;
        $data = file_get_contents($link);
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

            $participant[$i]['username'] = $username;
            $participant[$i]['country'] = $country;
            $participant[$i]['institution'] = $institution;
            $participant[$i]['rating'] = $rating;

            $pg = $this -> volatility($username);
            $participant[$i]['volatility'] = $pg[0];
            $participant[$i]['timesplayed'] = $pg[1];

            if($participant[$i]['timesplayed'] == 0)
            {
              $participant[$i]['rating'] = 1500;
            }
            $participant[$i]['rank'] = $rank;

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

            $participant[$i]['username'] = $username;
            $participant[$i]['country'] = $country;
            $participant[$i]['institution'] = $institution;
            $participant[$i]['rating'] = $rating;

            $pg = $this -> volatility($username);
            $participant[$i]['volatility'] = $pg[0];
            $participant[$i]['timesplayed'] = $pg[1];

            if($participant[$i]['timesplayed'] == 0)
            {
              $participant[$i]['rating'] = 1500;
            }

            $participant[$i]['rank'] = $rank;

            $i++;

          }
        }
      }
      return $participant;
    }
  }

  /*$ob = new User();
  $ob -> generate("LTIME57");*/
  /*$url = "https://www.codechef.com?rankings/LTIME55?filterBy=Institution%3DIndian%20Institute%20of%20Technology%20Kanpur&order=asc&page=2&sortBy=rank";
  print_r($ob -> generate($url));

  echo $ob -> getcontestname($url);
  echo "<br>";
  echo $ob -> getparameters($url);*/
?>
