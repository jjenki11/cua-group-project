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
      <a class="navbar-brand" href="#">Group #1</a>
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
        <center>
          <h1>Theater Project</h1>
        </center>
        <br />
        <center>
        <!--
          <div id="filter_form">
            <form id="form_92285" method="post" action="/group1/?type=Island&filt=city">
              <div class="form_description">
                <h2>Untitled Form</h2>
                <p>This is your form description. Click here to edit.</p>
              </div>						
                <ul >
                
                    <li id="li_1" >
              <label class="description" for="element_1">Search by name </label>
              <div>
                <input id="element_1" name="element_1" class="element text large" type="text" maxlength="255" value=""/> 
              </div> 
              </li>		<li id="li_3" >
              <label class="description" for="element_3">Drop Down </label>
              <div>
              <select class="element select medium" id="element_3" name="element_3"> 
                <option value="1" selected="selected">New York City</option>
          <option value="2" >Queens</option>
          <option value="3" >Long Island</option>

              </select>
              </div> 
              </li>		<li id="li_2" >
              <label class="description" for="element_2">Phone </label>
              <span>
                <input id="element_2_1" name="element_2_1" class="element text" size="3" maxlength="3" value="" type="text"> -
                <label for="element_2_1">(###)</label>
              </span>
              <span>
                <input id="element_2_2" name="element_2_2" class="element text" size="3" maxlength="3" value="" type="text"> -
                <label for="element_2_2">###</label>
              </span>
              <span>
                <input id="element_2_3" name="element_2_3" class="element text" size="4" maxlength="4" value="" type="text">
                <label for="element_2_3">####</label>
              </span>
              
              </li>
                    <li class="buttons">
                    <input type="hidden" name="form_id" value="92285" />
                    
                  <input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
              </li>
                </ul>
              </form>	
          </div>
        -->
          <div class="btn-group">
            <a role="button" class="btn btn-dark" href="?type=all&filt=city">Show All</a>
            <a role="button" class="btn btn-dark" href="?type=York&filt=city">New York City</a>
            <a role="button" class="btn btn-dark" href="?type=Queens&filt=city">Queens</a>
            <a role="button" class="btn btn-dark" href="?type=Island&filt=city">Long Island</a>
          </div>
        </center>
      </div>

      <div id="output-box" class="container-fluid" style="display: block;">
        <div class="row">
          <div class="col-md-12">
            <div style="overflow-x: auto">
              <table id="data" class="table table-condensed table-bordered table-hover table-stripped">
                <div id="map"></div>
                
                <tbody>
                  <?php
                    /*
                    $queryPost = $_POST['query'];
                    if($queryPost !== null) {
                      echo("console.log('ok...');");
                    }
                    */

                    $filt = $_GET['filt'];
                    $que = $_GET['type'];
                    
                    if ($filt == "city") {
                      if ($que == "York") {
                        $tablesquery = $db->query("SELECT * FROM 'THEATERS' WHERE \"field7\" = \"New York\";");
                      } elseif ($que == "Queens") {
                        $tablesquery = $db->query("SELECT * FROM 'THEATERS' WHERE \"field7\" = \"Queens\";");
                      } elseif ($que == "Island") {
                        $tablesquery = $db->query("SELECT * FROM 'THEATERS' WHERE \"field7\" = \"Long Island City\";");
                      }  else {
                        $tablesquery = $db->query("SELECT * FROM 'THEATERS';");
                      }
                    } elseif ($filt == "Distance") {
                      echo("alert('distance placeholder')");
                    } elseif ($filt == "Search") {
                      echo("alert('search placeholder')");
                    } else {                      
                      // BAD FILTER TYPE - redirect to all filter
                      echo('<script>window.location.assign("http://li1923-168.members.linode.com/group1/index.php?type=all&filt=city")</script>');
                    }

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
                    $results= $tablesquery;
                    //Create array to keep all results
                    $data = array();
                    // Fetch Associated Array (1 for SQLITE3_ASSOC)
                    while ($res= $results->fetchArray(1)) {
                      array_push($data, $res);
                    }
                    $jdata = json_encode($data);
                    $mapPoints = array();
                    $mapData = array();
                    foreach (json_decode($jdata, true) as $d){
                      $x = str_replace("POINT ", "", $d['field1']);
                      array_push($mapPoints, $x);
                      $name = $d['field2'];
                      $phone = $d['field3'];
                      $url = $d['field4'];
                      $metadata = "{$name}|{$phone}|{$url}";
                      array_push($mapData, $metadata);
                    }
                  ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <script>
      // Initialize and add the map
      function initMap() {
        var map = new google.maps.Map(document.getElementById('map'), {zoom: 11, center: {lat: 40.730610, lng: -73.935242}});
        var current_points = "<?php echo $mapPoints; ?>";
        var pts=<?php echo json_encode($mapPoints); ?>;
        var metadata=<?php echo json_encode($mapData); ?>;

        function makeInfoWindowEvent(map, infowindow, contentString, marker) {
          google.maps.event.addListener(marker, 'click', function() {
            infowindow.setContent(contentString);
            infowindow.open(map, marker);
          });
        }

        let lats = [];
        let lons = [];
        // should be same length as metadata array
        for (let i = 0; i < pts.length; i++){
          if(pts[i].indexOf("geom") == -1){
            // format point string into lat/lon json
            let ll = pts[i].replace('(', '').replace(')', '').split(' ');
            let a_pt = {lat: parseFloat(ll[1]), lng: parseFloat(ll[0])};
            lats.push(parseFloat(ll[1]));
            lons.push(parseFloat(ll[0]));
            // add each point
            let m = new google.maps.Marker({position: a_pt, map: map});
            var infowindow = new google.maps.InfoWindow();

            // format the metadata
            let md_array = metadata[i].split("|");
            let md = `Name: <a target='_blank' href='${md_array[2]}'>${md_array[0]}</a><br>Phone: ${md_array[1]}<br>`;
            makeInfoWindowEvent(map, infowindow, md, m);
          }
        }
        // average lats and lons then set map center to that
        let lat_sum = 0; let lon_sum = 0;
        for(let x = 0; x < lats.length; x++){
          lat_sum = lat_sum + lats[x]; lon_sum = lon_sum + lons[x];
        }
        map.setCenter({lat: (lat_sum/lats.length), lng: (lon_sum/lons.length)});
      }
      </script>
      <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAxT5l-Cdxyp4z0IvNCabKrZoTVgcdDtIs&callback=initMap">
    </script>
    <footer class="page-footer font-small black">
      <div class="text-center py-3">
        <a href="about.html">Credits</a>
      </div>
    </footer>
  </body>
</html>