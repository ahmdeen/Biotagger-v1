<!DOCTYPE html>
<?php
// Connecting, selecting database
$con = mysql_connect('localhost', 'user', 'YES')
   or die('Could not connect: ' . mysql_error());
mysql_select_db('Biotest') or die('Could not select database');

$query = "SELECT * FROM Biotest.resources";
$res_result = mysql_query($query) or die('Query failed: ' . mysql_error());

$query = "SELECT * FROM Biotest.relationships";
$rel_result = mysql_query($query) or die('Query failed: ' . mysql_error());

if($_SERVER['REQUEST_METHOD'] == 'POST'){
  // if adding a resource
  if ($_POST["submission"] == "resource") {
    $res_error = false;
    $res_error_message = "";
    $req_name = $_POST["name"];
    $req_type = $_POST["type"];
    // make sure name does not exist
    if ($req_name == "") {
      $res_error = true;
      $res_error_message = "Name field is missing";
    } else {
      $query = "SELECT * FROM Biotest.resources WHERE name = '" . $req_name . "'";
      $result = mysql_query($query) or die('Query failed: ' . mysql_error());
      if ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
        $res_error = true;
        $res_error_message = $req_name . " is already listed in the resources table";
      } else {
        $query = "INSERT INTO Biotest.resources (name, type) VALUES ('$req_name', '$req_type') ";
        $result = mysql_query($query) or die('Query failed: ' . mysql_error());
        $res_success = "Added $req_name with type $req_type to the database";
      }
    }
  // if adding a relationship
  } else {
    $rel_error = false;
    $rel_error_message = "";
    $req_res1 = $_POST["res1"];
    $req_rel = $_POST["rel"];
    $req_res2 = $_POST["res2"];

    if ($req_res1 == "" || $req_res2 == "") {
      $rel_error = true;
      $rel_error_message = "Resource 1 and resource 2 must both be filled in";
    } else {
      // check that res1 and res2 exist
      $query = "SELECT * FROM Biotest.resources WHERE name = '" . $req_res1 . "'";
      $result = mysql_query($query) or die('Query failed: ' . mysql_error());
      $query2 = "SELECT * FROM Biotest.resources WHERE name = '" . $req_res2 . "'";
      $result2 = mysql_query($query2) or die('Query failed: ' . mysql_error());
      if (!$line = mysql_fetch_array($result, MYSQL_ASSOC)) {
        $rel_error = true;
        $rel_error_message = $req_res1 . " is not a valid resource";
      } else if (!$line2 = mysql_fetch_array($result2, MYSQL_ASSOC)) {
        $rel_error = true;
        $rel_error_message = $req_res2 . " is not a valid resource";
      } else {
        $query = "INSERT INTO Biotest.relationships (res1, rel, res2) VALUES ('$req_res1', '$req_rel', '$req_res2') ";
        $result = mysql_query($query) or die('Query failed: ' . mysql_error());
        $rel_success = "Added relationship $req_res1 $req_rel $req_res2";
      }
    }
  }
}
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
        <li><a href="search.php">Search</a></li>
        <li class="active"><a href="addtag.php">Add Tag</a></li>
        <li><a href="about.php">About</a></li>
      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>

  <div class="container">
    <div class="page-header">
      <h1>Add Tag <small>define new resources</small></h1>
    </div>
    <h2>Resource</h2>
    <form class="form-inline" role="form" action="addtag.php" method="POST">
      <div class="form-group">
        <label class="sr-only" for="exampleInputEmail2">Name</label>
        <input type="text" class="form-control" id="name" name="name" placeholder="Name" <? if ($res_error && $_POST["name"]) { echo "value=\"" . $_POST["name"] . "\""; } ?>>
      </div>
      <div class="form-group">
        <label class="sr-only" for="exampleInputPassword2">Type</label>
        <select class="form-control" id="type" name="type">
          <option <? if (!$_POST["type"] || $res_error && $_POST["type"] == "cell") { echo "selected=\"selected\""; } ?>>cell</option> <!--functions of a molecule -->
          <option <? if ($res_error && $_POST["type"] && $_POST["type"] == "molecule") { echo "selected=\"selected\""; } ?>>molecule</option> <!-- molecules located in a cell-->
          <option <? if ($res_error && $_POST["type"] && $_POST["type"] == "function") { echo "selected=\"selected\""; } ?>>function</option> <!-- resource synonymn resource -->
        </select>
      </div>
      <input type="hidden" name="submission" value="resource">
      <button type="submit" class="btn btn-default Mstart-20">Add</button>
    </form>

    <? if ($res_error) { ?>
      <p><div class="alert alert-danger"><? echo $res_error_message; ?></div></p>
    <?
      } else if ($res_success) {
    ?>
         <p><div class="alert alert-success"><? echo $res_success; ?></div></p>
    <?}?>

    <h2>Relationship</h2>
    <form class="form-inline" role="form" action="addtag.php" method="POST">
      <div class="form-group">
        <label class="sr-only" for="exampleInputEmail2">Resource 1</label>
        <input type="text" class="form-control" id="res1" name="res1" placeholder="Resource 1" <? if ($rel_error && $_POST["res1"]) { echo "value=\"" . $_POST["res1"] . "\""; } ?>>
      </div>
      <div class="form-group">
        <label class="sr-only" for="exampleInputPassword2">Relationship</label>
        <select class="form-control" id="rel" name="rel">
          <option <? if (!$_POST["rel"] || $rel_error && $_POST["rel"] == "is a") { echo "selected=\"selected\""; } ?>>is a</option> <!--functions of a molecule -->
          <option <? if ($rel_error && $_POST["rel"] && $_POST["rel"] == "located in") { echo "selected=\"selected\""; } ?>>located in</option> <!-- molecules located in a cell-->
          <option <? if ($rel_error && $_POST["rel"] && $_POST["rel"] == "synonymn") { echo "selected=\"selected\""; } ?>>synonymn</option> <!-- resource synonymn resource -->
        </select>
      </div>
      <div class="form-group">
        <label class="sr-only" for="exampleInputPassword2">Resource 2</label>
        <input type="text" class="form-control" id="res2" name="res2" placeholder="Resource 2" <? if ($rel_error && $_POST["res2"]) { echo "value=\"" . $_POST["res2"] . "\""; } ?>>
      </div>
      <button type="submit" class="btn btn-default Mstart-20">Add</button>
    </form>

    <? if ($rel_error) { ?>
        <p><div class="alert alert-danger"><? echo $rel_error_message; ?></div></p>
      <?
        } else if ($rel_success) {
      ?>
          <p><div class="alert alert-success"><? echo $rel_success; ?></div></p>
      <?
        }
      ?>
  </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
  </body>
</html>