<?php
$filename = "data.db";

$db = new SQLite3($filename);

?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body style="background-color: white;">

<!-- start of nav bar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
  <a class="navbar-brand" href="#">Group#1</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item active">
        <a class="nav-link" href="#">Theater Project<span class="sr-only">(current)</span></a>
      </li>
    </ul>
    <form class="form-inline my-2 my-lg-0">
      <a href="https://github.com/jjenki11/cua-group-project.git" class="btn btn-outline-primary">GitHub</a>
    </form>
  </div>
</nav> 
<!-- end of nav bar -->





<div id="main-container" class="container" style="position: relative; background: white; box-shadow: 0 1px 16px silver; z-index: 2;">
<div id="header" style="background: black; color: white; padding: 16px; margin-left: -15px; margin-right: -15px; margin-bottom: 15px;">
<center><h1>Theater Project</h1></center>
<br />
<center>
<div class="btn-group">
<a role="button" class="btn btn-dark" href="?type=all">Show All</a>
<a role="button" class="btn btn-dark" href="?type=York">New York City</a>
<a role="button" class="btn btn-dark" href="?type=Queens">Queens</a>
<a role="button" class="btn btn-dark" href="?type=Island">Long Island</a>
</div>
</center>
</div>
<div id="output-box" class="container-fluid" style="display: block;">
<div class="row">
<div class="col-md-12">
<div style="overflow-x: auto">
<table id="data" class="table table-condensed table-bordered table-hover table-stripped">

<tbody>
<?php

$que = $_GET['type'];
$tablesquery = $db->query("SELECT * FROM 'THEATERS';");
if ($que == "York")
{
    $tablesquery = $db->query("SELECT * FROM 'THEATERS' WHERE \"field7\" = \"New York\";");
} elseif ($que == "Queens") {
    $tablesquery = $db->query("SELECT * FROM 'THEATERS' WHERE \"field7\" = \"Queens\";");
} elseif ($que == "Island") {
    $tablesquery = $db->query("SELECT * FROM 'THEATERS' WHERE \"field7\" = \"Long Island City\";");
}  else {
    $tablesquery = $db->query("SELECT * FROM 'THEATERS';");
}
//  Make dynamic Table
while ($table = $tablesquery->fetchArray(SQLITE3_ASSOC)) {
    echo("<tr>");
    foreach ($table as $value) {
        if(empty($value))
        {
            echo("<td tabindex='1'><span title=N/A>N/A</span></td>");
        } else {
            echo("<td tabindex='1'><span title=$value>".$value."</span></td>");
        }
    }
    echo("</tr>");
}
?>
</tbody>
</table>
</div>
</div>
</div>
</div>
</div>

<footer class="page-footer font-small black">
    <div class="text-center py-3">
        <a href="about.html">Credits</a>
    </div>
</footer>

</body>
</html>


