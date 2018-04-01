<?php

  class Contest
  {
    function ifcontestexists($contestname)
    {
      include('connect.php');
    }

    function isvalidcontest($contestname)
    {
      return 1;
    }

    function iftableexists($contestname)
    {
      include('connect.php');

      $query = "SHOW TABLES LIKE '".mysqli_real_escape_string($link, $contestname)."'";
      $res = mysqli_query($link, $query);

      if(mysqli_num_rows($res) == 0)
        return -1;

      return 1;
    }

    function createtable($contestname)
    {
      include('connect.php');

      $query = "CREATE TABLE IF NOT EXISTS ".mysqli_real_escape_string($link, $contestname)." (`rank` INT NOT NULL , `username` VARCHAR(50) NOT NULL , `currentrating` FLOAT NOT NULL , `predictedrating` FLOAT NOT NULL, `institution` VARCHAR(100), `country` VARCHAR(100))";

      mysqli_query($link, $query);
    }

    // Gets data in JSON format from the contest page
    function populate($contestname)
    {
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
          }
        }
      }

    }

    function update($contestname)
    {

    }
  }

?>
