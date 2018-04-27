<?php

  class Contests
  {
    //Get list of Live Contests
    function getlivelist()
    {
      $url = "https://codechef.com/contests";
      $data = file_get_contents($url);

      $first = explode("Present Contests", $data);

      $second = explode("Future Contests", $first[1]);
      $haystack = $second[0];

      return $haystack;
    }

    //If Contest is Live
    function isrunning($haystack, $contestname)
    {
      if(strstr($haystack, $contestname))
      {
        return 1;
      }

      else
      {
        return 0;
      }
    }

    //If Contest is Rated (For Non Proprietary Codechef Contests)
    function israted($haystack, $contestname)
    {
      $first = explode($contestname, $haystack);
      $second = explode("</tr>", $first[2]);

      if(strstr($second[0], "(Rated"))
      return 1;

      return 0;
    }

    //Codechef Proprietary Contests
    function isproprietary($contestname)
    {
      $check['LTIME'] = 1;
      $check['COOK'] = 1;

      $check["JAN"] = 1;
      $check["FEB"] = 1;
      $check["MARCH"] = 1;
      $check["APRIL"] = 1;
      $check["MAY"] = 1;
      $check["JUNE"] = 1;
      $check["JULY"] = 1;
      $check["AUG"] = 1;
      $check["SEPT"] = 1;
      $check["OCT"] = 1;
      $check["NOV"] = 1;
      $check["DEC"] = 1;

      $chk = "";
      for($i = 0 ; $i < strlen($contestname) ; $i++)
      {
        if(ctype_alpha($contestname[$i]))
        $chk .= $contestname[$i];

        else
        break;
      }

      if($chk == 'LTIME')
      return 2;

      if($check[$chk])
      return 1;

      return 0;
    }

    function isvalid($contestname, $haystack)
    {
      //$haystack = $this -> getlivelist();

      if(!($this -> isrunning($haystack, $contestname)))
      {
        return 0;
      }

      if($this -> isproprietary($contestname) == 2)
      return 1;

      if($this -> isproprietary($contestname))
      return 2;

      if($this -> israted($haystack, $contestname))
      return 1;

      return 0;
    }

  }

?>
