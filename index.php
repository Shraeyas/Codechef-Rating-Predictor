<?php

  include ('header.php');

  class Redirect
  {
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
        /*echo $type[0];
        echo "<br>";
        echo urldecode($type[1]);*/

        return [$type[0], $type[1]];
      }
    }

    function redirect_call($url)
    {
      $par = $this -> getparameters($url);
      $contestname = $this -> getcontestname($url);

      if($contestname == "")
      return -1;

      if($par[0] == "Country")
      header("Location: leaderboard.php?contest=".$contestname."&country=".$par[1]);

      else if($par[0] == "Institution")
      header("Location: leaderboard.php?contest=".$contestname."&institution=".$par[1]);

      else
      header("Location: leaderboard.php?contest=".$contestname);
    }
  }

  $ob = new Redirect();

  $url = $_SERVER['REQUEST_URI'];

  $ob -> redirect_call($url);

  //print_r ($ob -> getparameters($url));




  //$url = "https://www.codechef.com?rankings/APRIL18A?filterBy=Country%3DIndia&order=asc&sortBy=rank";
  /*$url = "https://www.codechef.com?rankings/APRIL18A?filterBy=Institution%3DIndian%20Institute%20of%20Technology%20Delhi&order=asc&sortBy=rank";

  print_r ($ob -> getparameters($url));
  echo $ob -> getcontestname($url);

  //https://www.codechef.com/rankings/APRIL18A?filterBy=Institution%3DIndian%20Institute%20of%20Technology%20Delhi&order=asc&sortBy=rank
  */
?>


<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <title>Rating Predictor</title>
  </head>
  <body>


    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
  </body>


  <div class = "container">

    <table class="table table-striped table-dark">
      <thead>
        <tr>
          <th scope="col">#</th>
          <th scope="col">ContestID</th>
        </tr>

      </thead>
      <tbody style = "font-size:14px;font-weight:bold;">

        <?php
          include ('connect.php');
          $query = "SELECT * FROM livecontests";
          $res = mysqli_query($link, $query);

          $i = 0;
          while($ans = mysqli_fetch_array($res))
          {
            echo "<tr><td scope = 'row'>".++$i."</td>";
            echo "<td  scope = 'row'><a style = 'color:#31fc40' href = 'leaderboard.php?contest=".$ans['contestid']."'>".$ans['contestid']."</a></td></tr>";
          }

        ?>

      </tbody>
    </table>
  </div>

</html>

<?php include('footer.php'); ?>
