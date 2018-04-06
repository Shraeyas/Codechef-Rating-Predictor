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

    function generate($url)
    {
      $participant = [];

      $contestname = $this -> getcontestname($url);
      $parameters = $this -> getparameters($url);

      $link = "https://codechef.com/api/rankings/".$contestname."?page=1";

      $data = file_get_contents($link);
      $contest = json_decode($data,true);

      if($contest['contest_info']['contest_code'] != $contestname)
      {
        die("Invalid Contest ID");
      }

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
          $items = $total % 50;

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
            $participant[$i++]['rank'] = $rank;

          }
        }

        else
        {
          for($item = 0 ; $item < 50 ; $item++)
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
            $participant[$i++]['rank'] = $rank;
          }
        }
      }

      return $participant;
    }
  }

  $ob = new User();

  $link = "https://www.codechef.com?rankings/LTIME55?filterBy=Institution%3DIndian%20Institute%20of%20Technology%20Kanpur&order=asc&page=2&sortBy=rank";
  print_r($ob -> generate($link));

  echo $ob -> getcontestname($link);
  echo "<br>";
  echo $ob -> getparameters($link);
?>
