<?php

  class Contest
  {

    function ifcontestexists($contestname)
    {
      include('connect.php');

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
    }
  }

?>
