<!DOCTYPE html>
<?php
// Connecting, selecting database
$con = mysql_connect('localhost', 'user', 'YES')
   or die('Could not connect: ' . mysql_error());
mysql_select_db('Biotest') or die('Could not select database');

if($_SERVER['REQUEST_METHOD'] == 'POST'){
  $error = false;
  $error_message = "";

  // construct query
  $req_res1 = $_POST["res1"];
  $req_res2 = $_POST["res2"];

  if ($req_res1 == "" || $req_res2 == ""){
    $error = true;
    $error_message = "resource 1 and resouce 2 must both be filled in";
  } else if ($_POST["rel"] == "of a") {
    // make sure they are querying for functions 'of a' bla
    if ($req_res1 != "function" && $req_res1 != "functions") {
      $error = true;
      $error_message = "'of a' relationship is for functions";
    } else {
      // make sure that resource 2 exists
      $query = "SELECT * FROM Biotest.resources WHERE name = '" . $req_res2 . "'";
      $result = mysql_query($query) or die('Query failed: ' . mysql_error());
      if (!$line = mysql_fetch_array($result, MYSQL_ASSOC)) {
        $error = true;
        $error_message = $req_res2 . " could not be found";
      } else {
        $query = "SELECT * FROM Biotest.relationships WHERE rel = 'is a' AND res1 = '" . $req_res2 . "'";
      }
    }
  } else if ($_POST["rel"] == "located in") {
    if ($req_res1 != "molecules" && $req_res1 != "molecule") {
      $query = "SELECT * FROM Biotest.resources WHERE name = '" . $req_res1 . "'";
      $result = mysql_query($query) or die('Query failed: ' . mysql_error());
      if (!$line = mysql_fetch_array($result, MYSQL_ASSOC) || $line['type'] != "molecule") {
        $error = true;
        $error_message = "'located in' relationship is for molecules";
      }
    } else {
      $query = "SELECT * FROM Biotest.resources WHERE name = '" . $req_res2 . "'";
      $result = mysql_query($query) or die('Query failed: ' . mysql_error());

      if (!$line = mysql_fetch_array($result, MYSQL_ASSOC)) {
        $error = true;
        $error_message = $req_res2 . " could not be found";
      } else{
        $query = "SELECT * FROM Biotest.relationships WHERE rel = 'located in' AND res2 = '" . $req_res2 . "'";
      }
    }
  } else if ($_POST["rel"] == "synonymn") {
    if ($req_res1 != "function" && $req_res1 != "functions" && $req_res1 != "molecules" && $req_res1 != "molecule") {
      $error = true;
      $error_message = "'synonymn' relationship must take functions or molecules as first resource";
    } else {
      // make sure that resource 2 exists
      $query = "SELECT * FROM Biotest.resources WHERE name = '" . $req_res2 . "'";
      $result = mysql_query($query) or die('Query failed: ' . mysql_error());
      if (!$line = mysql_fetch_array($result, MYSQL_ASSOC)) {
        $error = true;
        $error_message = $req_res2 . " could not be found";
      } else {
        
        $query = "SELECT * FROM Biotest.relationships WHERE rel = 'synonymn' AND res1 = '" . $req_res2 . "' OR rel = 'synonymn' AND res2 = '" . $req_res2 . "'";
      }
    }
  } else {
    if ($req_res1 != "cells" && $req_res1 != "cells") {
      $error = true;
      $error_message = "'that have' relationship must take cells as first resource";
    } else {
      $query = "SELECT * FROM Biotest.resources WHERE name = '" . $req_res2 . "'";
      $result = mysql_query($query) or die('Query failed: ' . mysql_error());

      if (!$line = mysql_fetch_array($result, MYSQL_ASSOC)) {
        $error = true;
        $error_message = $req_res2 . " could not be found";
      } else{
        $query = "SELECT * FROM Biotest.relationships WHERE rel = 'located in' AND res1 = '" . $req_res2 . "'";
      }
    }
  }

  // query if there was no error in the submission
  if (!$error) {
    $result = mysql_query($query) or die('Query failed: ' . mysql_error());
  }
}


mysql_close($con);
?>

<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Biotagger</title>

    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
  <nav class="navbar navbar-default" role="navigation">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand brand-font" href="index.php">Biotagger</a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
        <li class="active"><a href="search.php">Search</a></li>
        <li><a href="addtag.php">Add Tag</a></li>
        <li><a href="about.php">About</a></li>
      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>

<div class="container">
  <div class="page-header">
    <h1>Search <small>find relationships between tags</small></h1>
  </div>

  <form class="form-inline" role="form" action="search.php" method="POST">
    <div class="form-group">
      <label class="sr-only" for="exampleInputEmail2">Resource 1</label>
      <input type="text" class="form-control" id="res1" name="res1" placeholder="Resource 1" <? if ($_POST["res1"]) { echo "value=\"" . $_POST["res1"] . "\""; } ?>>
    </div>
    <div class="form-group">
      <label class="sr-only" for="exampleInputPassword2">Relationship</label>
      <select class="form-control" id="rel" name="rel">
        <option <? if (!$_POST["rel"] || $_POST["rel"] == "of a") { echo "selected=\"selected\""; } ?>>of a</option> <!--functions of a molecule -->
        <option <? if ($_POST["rel"] && $_POST["rel"] == "located in") { echo "selected=\"selected\""; } ?>>located in</option> <!-- molecules located in a cell-->
        <option <? if ($_POST["rel"] && $_POST["rel"] == "synonymn") { echo "selected=\"selected\""; } ?>>synonymn</option> <!-- resource synonymn resource -->
        <option <? if ($_POST["rel"] && $_POST["rel"] == "that have") { echo "selected=\"selected\""; } ?>>that have</option>
      </select>
    </div>
    <div class="form-group">
      <label class="sr-only" for="exampleInputPassword2">Resource 2</label>
      <input type="text" class="form-control" id="res2" name="res2" placeholder="Resource 2" <? if ($_POST["res2"]) { echo "value=\"" . $_POST["res2"] . "\""; } ?>>
    </div>

    <button type="submit" class="btn btn-default Mstart-20">Submit</button>
  </form>

  <? if($_SERVER['REQUEST_METHOD'] == 'POST') { ?>
    <h1>Search Results</h1>
  <? if ($error) { ?>
    <div class="alert alert-danger"><? echo $error_message; ?></div>
  <? } else {
      echo "<table class=\"table\">\n";
      while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
         echo "\t<tr>\n";
         foreach ($line as $col_value) {
             echo "\t\t<td>$col_value</td>\n";
         }
         echo "\t</tr>\n";
      }
      echo "</table>\n";
  } } ?>
</div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
    <script>
      $( document ).ready(function() {
        $("#rel").on("change", handleForm);
        handleForm();

        function handleForm() {
          if ($("#rel").find(":selected").text() == "of a") {
            $("#res1").val("functions");
          } else if ($("#rel").find(":selected").text() == "located in") {
            $("#res1").val("molecules");
          } else if ($("#rel").find(":selected").text() == "that have") {
            $("#res1").val("cells");
          }
        }
      });
    </script>
  </body>
</html>