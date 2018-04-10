<?php

  include ('connect.php');

  /*echo $_GET['country'];
  echo "<br>";
  echo $_GET['institution'];
  echo "<br>";
  echo $_GET['contest'];*/

  if($_GET['contest'] == 'livecontests')
  die('Error');

  $query = "SELECT 1 FROM ".mysqli_real_escape_string($link, $_GET['contest'])."";
  if(!mysqli_query($link, $query))
  die('Nothing Found for this Contest');

  $query = "SELECT * FROM ".mysqli_real_escape_string($link, $_GET['contest'])." WHERE institution LIKE '%".mysqli_real_escape_string($link, $_GET['institution'])."%' AND country LIKE '%".mysqli_real_escape_string($link, $_GET['country'])."%' AND username LIKE '%".mysqli_real_escape_string($link, $_GET['username'])."%' ORDER BY rank";

  $res = mysqli_query($link, $query);


?>



<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->

    <?php include('header.php'); ?>
    <br><br>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <title><?php echo $_GET['contest']; ?></title>


  </head>

  <body>


    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

    <div class = "container">

      <form>

        <div class="form-group">
          <label for="exampleInputEmail1">Contest</label>
          <input name="contest" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Contest" value = "<?php if(isset($_GET['contest'])) echo $_GET['contest']; ?>">
        </div>

        <div class="form-group">
          <label for="exampleInputEmail1">Country</label>
          <input name="country" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Country" value = "<?php if(isset($_GET['country'])) echo $_GET['country']; ?>">
        </div>

        <div class="form-group">
          <label for="exampleInputPassword1">Institution</label>
          <input name="institution" class="form-control" id="exampleInputPassword1" placeholder="Institution" value = "<?php if(isset($_GET['institution'])) echo $_GET['institution']; ?>">
        </div>

        <div class="form-group">
          <label for="exampleInputPassword1">Username</label>
          <input name="username" class="form-control" id="exampleInputPassword1" placeholder="Username" value = "<?php if(isset($_GET['username'])) echo $_GET['username']; ?>">
        </div>

        <button type="submit" class="btn btn-primary">Search</button>

      </form>

      <br>

      <table class="table table-striped table-dark">
        <thead>
          <tr>
            <th scope="col">#</th>
            <th scope="col">Rank</th>
            <th scope="col">Name</th>
            <th scope="col" style = "width:150px">Rating</th>
            <th scope="col" style = "width:150px">New Rating</th>
            <th scope="col" style = "width:150px">Changes</th>
            <th scope="col" style = "width:150px">Institution</th>
            <th scope="col">Country</th>

          </tr>
        </thead>
        <tbody style = "font-size:14px;font-weight:bold">

        <?php
          $i = 1;
          $pg = "";
          while($ans = mysqli_fetch_array($res))
          {
            $changes = round($ans['newrating'] - $ans['rating'], 2);

            $pg .= "<tr>";
            $pg .= "<th scope = 'row'>".$i."</th>";
            $pg .= "<td>".$ans['rank']."</td>";
            $pg .= "<td>".$ans['username']."</td>";
            $pg .= "<td>".round($ans['rating'], 2)."</td>";
            $pg .= "<td>".round($ans['newrating'], 2)."</td>";

            if($changes >= 0)
            $pg .= "<td style = 'color: #31fc40'>".$changes."</td>";

            else
            $pg .= "<td style = 'color: red'>".$changes."</td>";

            $pg .= "<td>".$ans['institution']."</td>";
            $pg .= "<td>".$ans['country']."</td>";

            $pg .= "</tr>";
            $i++;
          }
          echo $pg;
        ?>


        </tbody>
      </table>
    </div>

  </body>
</html>
<?php include ('footer.php'); ?>
