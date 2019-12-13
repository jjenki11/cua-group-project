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
    <!-- nav bar -->
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
    <!-- nav bar -->

    <div id="main-container" class="container" style="position: relative; background: white; box-shadow: 0 1px 16px silver; z-index: 2;">
      <div id="header" style="background: black; color: white; padding: 16px; margin-left: -15px; margin-right: -15px; margin-bottom: 15px;">
        <center>
          <h1>Theater Project</h1>
        </center>
        <br />
        <center>
        <h3>Map Filters</h3>
        <form id="filter_form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">  
          Search: <input type="text" id="searchString" name="search" value="">
          <br><br>
          City:
          <input id="AllRadio" type="radio" name="city" value="All" checked>All
          <input id="YorkRadio" type="radio" name="city" value="York">New York City
          <input id="QueensRadio" type="radio" name="city" value="Queens">Queens
          <input id="IslandRadio" type="radio" name="city" value="Island">Long Island
          <br><br>
          Zip Code: <input type="text" id="zipBox" name="zip" value="">
          <br><br>
          <input type="submit" name="submit" value="Submit">  
        </form>
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

                    $search = $_GET['search'];
                    $que = $_GET['city'];
                    $zip = $_GET['zip'];

                    $had_city = true;
                    $had_search  = true;
                    
                    $search_string = "SELECT * FROM 'THEATERS'";
                    if ($que == "York") {
                      $search_string.=" WHERE \"field7\" = \"New York\"";
                    } elseif ($que == "Queens") {
                      $search_string.=" WHERE \"field7\" = \"Queens\"";
                    } elseif ($que == "Island") {
                      $search_string.=" WHERE \"field7\" = \"Long Island City\"";
                    }  else {
                      $had_city = false;
                    }

                    if($search) {
                      if($had_city == false) {
                        $search_string.=" WHERE ";
                      } else {
                        $search_string.=" AND ";
                      }
                      $search_string.="\"field2\" LIKE \"%$search%\";";
                    } else {
                      $had_search = false;
                    }

                    if($zip) {
                      if(($had_city == false) && ($had_search == false)) {
                        $search_string.=" WHERE ";
                      } else {
                        $search_string.=" AND ";
                      }
                      $search_string.="\"field8\" = \"$zip\";";
                    }


                    $tablesquery = $db->query($search_string);

                    //else {                      
                      // BAD FILTER TYPE - redirect to all filter
                    //  echo('<script>window.location.assign("http://li1923-168.members.linode.com/group1/index.php?type=all&filt=city")</script>');
                    //}

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

      // handle form stuff
      var form = document.getElementById("filter_form"); 
      function handleForm(event) { 
        event.preventDefault();

        let query = "http://li1923-168.members.linode.com/group1/index.php?";        

        if(document.getElementById("AllRadio").checked){
          query = `${query}city=All`;
        } else if(document.getElementById("YorkRadio").checked){
          query = `${query}city=York`;
        } else if(document.getElementById("QueensRadio").checked){
          query = `${query}city=Queens`;
        } else if(document.getElementById("IslandRadio").checked){
          query = `${query}city=Island`;
        } else {
          query = `${query}city=All`;
        }

        if (document.getElementById("searchString").value != ""){
          query = `${query}&search=${document.getElementById("searchString").value}`
        }
        if (document.getElementById("zipBox").value != ""){
          query = `${query}&zip=${document.getElementById("zipBox").value}`
        }

        window.location.assign(query);
      } 
      form.addEventListener('submit', handleForm);

      function setInputFilter(textbox, inputFilter) {
        ["input", "keydown", "keyup", "mousedown", "mouseup", "select", "contextmenu", "drop"].forEach(function(event) {
          textbox.addEventListener(event, function() {
            if (inputFilter(this.value)) {
              this.oldValue = this.value;
              this.oldSelectionStart = this.selectionStart;
              this.oldSelectionEnd = this.selectionEnd;
            } else if (this.hasOwnProperty("oldValue")) {
              this.value = this.oldValue;
              this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
            } else {
              this.value = "";
            }
          });
        });
      }
      // Allow digits and '.' only, using a RegExp
      setInputFilter(document.getElementById("zipBox"), (value) => /^\d*\.?\d*$/.test(value));

      // set value of form widgets equal to the url query params
      var params = getUrlVars(window.location.href);
      let keys = Object.keys(params);
      if(keys.length > 0){
        document.getElementById("zipBox").value = (keys.indexOf("zip") != -1) ? params["zip"] : "";        
        document.getElementById("searchString").value = (keys.indexOf("search") != -1) ? params["search"] : "";
        if(keys.indexOf("city") != -1){
          document.getElementById(`${params["city"]}Radio`).checked = true;
        }
      }
      // extract jsonified version of url query params
      function getUrlVars(url) {
          var hash;
          var myJson = {};
          var hashes = url.slice(url.indexOf('?') + 1).split('&');
          for (var i = 0; i < hashes.length; i++) {
              hash = hashes[i].split('=');
              myJson[hash[0]] = hash[1];
          }
          return myJson;
      }
      </script>
      <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAxT5l-Cdxyp4z0IvNCabKrZoTVgcdDtIs&callback=initMap"></script>
    <footer class="page-footer font-small black">
      <div class="text-center py-3">
        <a href="about.html">About</a>
      </div>
    </footer>
  </body>
</html>