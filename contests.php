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

    

  }

?>
