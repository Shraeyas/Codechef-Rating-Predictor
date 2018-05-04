<?php

  class Database
  {
    function create($contestname)
    {
      include ('connect.php');

      $query = "CREATE TABLE IF NOT EXISTS `".mysqli_real_escape_string($link, $contestname)."` (`id` INT NOT NULL AUTO_INCREMENT, `rank` INT , `username` VARCHAR(150) , `institution` VARCHAR(150) , `country` VARCHAR(150) , `organisation` VARCHAR(150)  , `volatility` DOUBLE , `rating` DOUBLE , `newrating` DOUBLE , `timesplayed` INT, PRIMARY KEY(id), UNIQUE (username))";

      mysqli_query($link, $query);
    }

    function ifexists($contestname)
    {
      include ('connect.php');
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

    }
  }

?>
