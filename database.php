<?php

  class Database
  {
    function create($contestname)
    {
      include ('connect.php');

      $query = "CREATE TABLE IF NOT EXISTS `".mysqli_real_escape_string($link, $contestname)."` (`id` INT NOT NULL AUTO_INCREMENT, `rank` INT , `username` VARCHAR(150) , `institution` VARCHAR(150) , `country` VARCHAR(150) , `organisation` VARCHAR(150)  , `volatility` DOUBLE , `rating` DOUBLE , `newrating` DOUBLE , `timesplayed` INT, PRIMARY KEY(id))";

      mysqli_query($link, $query);
    }

    function ifexists($contestname)
    {
      include('connect.php');
      $query = "SHOW TABLES LIKE '".mysqli_real_escape_string($link, $contestname)."'";
      $res = mysqli_query($link, $query);

      if(mysqli_num_rows($res) == 0)
        return 0;

      return 1;
    }

    //Adds Users in the Database
    function update($contestname)
    {
      include ('connect.php');
      include_once ('users.php');

      if(!($this -> ifexists($contestname)))
      {
        $this -> create($contestname);
      }

      $user = new User();
      $contestant = $user -> generate($contestname);

      $size = count($contestant);

      for($i = 0 ; $i < $size ; $i++)
      {
        $query = "SELECT * FROM ".mysqli_real_escape_string($link, $contestname)." WHERE username = '".mysqli_real_escape_string($link, $contestant[$i]['username'])."'";
        $res = mysqli_query($link, $query);

        if(mysqli_num_rows($res) == 0)
        {
          $query = "INSERT INTO ".mysqli_real_escape_string($link, $contestname)." (`rank`, `username`, `institution`, `country`, `volatility`, `rating`, `newrating`, `timesplayed`) VALUES ('".mysqli_real_escape_string($link, $contestant[$i]['rank'])."', '".mysqli_real_escape_string($link, $contestant[$i]['username'])."', '".mysqli_real_escape_string($link, $contestant[$i]['institution'])."', '".mysqli_real_escape_string($link, $contestant[$i]['country'])."', '".mysqli_real_escape_string($link, $contestant[$i]['volatility'])."', '".mysqli_real_escape_string($link, $contestant[$i]['rating'])."', '".mysqli_real_escape_string($link, $contestant[$i]['rating'])."', '".mysqli_real_escape_string($link, $contestant[$i]['timesplayed'])."')";

          mysqli_query($link, $query);
        }

        else
        {
          $query = "UPDATE ".mysqli_real_escape_string($link, $contestname)." SET `rank` = '".mysqli_real_escape_string($link, $contestant[$i]['rank'])."', `institution` = '".mysqli_real_escape_string($link, $contestant[$i]['institution'])."', `country` = '".mysqli_real_escape_string($link, $contestant[$i]['country'])."', `volatility` = '".mysqli_real_escape_string($link, $contestant[$i]['volatility'])."', `rating` = '".mysqli_real_escape_string($link, $contestant[$i]['rating'])."', `timesplayed` = '".mysqli_real_escape_string($link, $contestant[$i]['timesplayed'])."' WHERE username = '".mysqli_real_escape_string($link, $contestant[$i]['username'])."'";

          mysqli_query($link, $query);
        }

        /*print_r($contestant[$i]);
        echo "<br>";*/
      }
    }
  }

  $ob = new Database();
  $ob -> update("COOK92A");

?>
