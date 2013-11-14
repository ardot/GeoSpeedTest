<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="en-US">
<head>
 <script src="http://speedof.me/api/api.js" type="text/javascript"></script>
<script src="js/jquery.min.js" type="text/javascript"></script>
<link rel="stylesheet" href="source/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" />
<script type="text/javascript" src="source/jquery.fancybox.pack.js?v=2.1.5"></script>
<script src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script type="text/javascript" src="js/constants.js"></script>
<link rel="shortcut icon" href="http://sstatic.net/so/favicon.ico">
<link rel="stylesheet" type="text/css" href="css/style.css" title="Style sheet!"/>
<title>RedRover Connection Speed Test - Cornell University</title>
<style>a.menu_html5_geolocation{font-weight:bold;}</style>
<meta charset="windows-1252">
<meta name="viewport" content="width=device-width">
<meta name="Keywords" content="internet, speed, connection, test, cornell, university, SQL,colors,tutorial,programming,development,training,learning,quiz,primer,lessons,reference,examples,source code,demos,tips,color table,w3c,cascading style sheets,active server pages,Web building,Webmaster">
<meta name="Description" content="A crowd-sourced mapping of internet connection speed around Cornell University!">
<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
<body>
<div id="mapholder"></div>


<div id="outer" style="
  position:fixed;
  left:50%;
  top:100px;
  background-color: rgba(100,100,100, .5);
  height:100px;
  width:415px;
  margin-left:-207px;
  margin-top:-75px;
  padding-left:10px;
  border-radius:8px;
">
<!--button id="btnStart" type="button" onclick="btnStartClick()"><h3>Start Test</h3></button-->
<div id="msg">
  <h2>
    Searching for your current location...
  </h2>
  <hr>
  <h3>
    <span class="secondary">
      Please allow the browser to access your location
    </span>
  </h3>
  <img
    style="margin-left:190px;top:50px"
    src="ajax-loader.gif"
    alt="Loading animation"/>
</div>
</div>

<div title="What do these numbers mean?"  id="help">
  <h1 style="font-size:240%" title="What do these numbers mean?">
    <a class="fancybox fancybox.iframe" href="iframe.html"> [?] </a>
  </h1>
</div>

<div id="key">
  <img src="speedtestkey.png" width="400" height="60" alt="Speed Test Key"/>
</div>

<script>

$(document).ready(function() {

  /* Apply fancybox to multiple items */

  console.log("Here");
  $("a.fancybox").fancybox({
    'transitionIn'  : 'elastic',
    'transitionOut' : 'elastic',
    'speedIn'   : 600,
    'speedOut'    : 200,
    'width'     : 510,
    'height'    : 340,
    'overlayShow' : false
  });

});

</script>

<script>
var map;
var marker;
var latlon;
var points;
var atCornell = true;

SomApi.account = "SOM5281a0b30c276"; //your API Key here
SomApi.domainName = "timlenardo.com"; //your domain or sub-domain here
SomApi.config.sustainTime = 8;
SomApi.onTestCompleted = onTestCompleted;
SomApi.onError = onError;
var msgDiv = document.getElementById("msg");

function btnStartClick() {
  msgDiv.innerHTML = "<h2>Speed test in progress. Please wait...</h2><hr>" +
    "<h3><span class=\"secondary\" id=\"whichtest\"> Testing Download Speed: </span></h3>" +
    "<div id=\"progressBarDown\"><div></div></div>";
  setTimeout(progressBarRecursive, 150, 0, 1);
  SomApi.startTest();


}


function progressBarRecursive(percent, down) {
  if (percent > 100) {
    if (down == 1) {
      $('#whichtest')[0].innerHTML = "Testing Upload Speed: ";
      setTimeout(progressBarRecursive, 150, 0, 0);
      return;
    } else {
      msgDiv.innerHTML = "<h2>Speed test in progress. Please wait...</h2><hr>" +
        "<h3><span id=\"whichtest\"> Finishing Up! </span></h3>" +
        "<img style=\"margin-left:190px;top:50px\" src=\"ajax-loader.gif\" alt=\"Loading animation\"/>";
      return;
    }
  }
  progress(percent, $('#progressBarDown'));
  setTimeout(progressBarRecursive, 150, (percent + 1), down)
}

function progress(percent, $element) {
  var progressBarWidth = percent * $element.width() / 100;
  $element.find('div').animate({ width: progressBarWidth }, 90).html(percent + "%&nbsp;");
}

function onTestCompleted(testResult) {

  var outer = document.getElementById('outer');
  document.body.removeChild(outer);


  // Stores the upload and download colors
  var up_color = getColor(testResult.upload);
  var down_color = getColor(testResult.download);

  // use the
  var today = new Date();
  var time = today.getTime();
  var time_report = "Just now";

  console.log(atCornell);
  if (atCornell == true) {
    var url = "addData.php";
    url = url.concat("?lat=" + lat);
    url = url.concat("&long=" + lon);
    url = url.concat("&up=" + testResult.upload);
    url = url.concat("&down=" + testResult.download);
    url = url.concat("&time=" + time);

    $.ajax(url);
  }
  var contentString = "<h2> Your Result! </h2><hr>";

  contentString = contentString + getMessage(
    down_color,
    up_color,
    testResult.download,
    testResult.upload,
    time_report);

   var infowindow = new google.maps.InfoWindow({
    content: contentString,
    maxWidth: 600
  });

  attachMessage(marker, contentString);
  infowindow.open(map,marker);

}

function getMessage(down_color, up_color, down, up, time) {
  var to_ret =
    '<div class=\"marker\"> <h3>' +
    'Download: <span class=\"result\" style=\"font-weight:600;font-size:120%;color:#' +
        down_color + '\">' + down + ' Mbps </span><br/>' +
    'Upload: <span class=\"result\" style=\"font-weight:600;font-size:120%;color:#' +
        up_color + '\">' + up + ' Mbps </span> <br/>' +
     '<span class=\"time\">' + time + '</span></h3></div>';
  return to_ret;
}

function getTime(time) {

  var today = new Date();
  var cur_time = today.getTime();
  var diff = cur_time - time;

  days = Math.floor(diff / 86400000);
  hours = Math.floor(diff / 3600000);
  minutes = Math.floor(diff / 60000);
  seconds = Math.floor(diff / 1000);

  if (days > 0) {
    to_ret = days + " days ago";
  } else if (hours > 0) {
    to_ret = hours + " hours ago";
  } else if (minutes > 0) {
    to_ret = minutes + " minutes ago";
  } else {
    to_ret = seconds + " seconds ago";
  }

  return to_ret;
}

function onError(error) {

  msgDiv.innerHTML = "Error "+ error.code + ": Speed test failied with the error message: "+error.message;
}

function getLocation()
  {
  if (navigator.geolocation)
    {
    navigator.geolocation.getCurrentPosition(getPosition,showError);
    }
  else{msgDiv.innerHTML="Geolocation is not supported by this browser.";}
  }

function getPosition(position)
{
  lat=position.coords.latitude;
  lon=position.coords.longitude;
  latlon=new google.maps.LatLng(lat, lon);
  showPosition(lat, lon, latlon);
}

function showPosition(lat, lon, latlong) {

  mapholder=document.getElementById('mapholder')
  mapholder.style.height='600px';
  mapholder.style.width='100%';



  if (!ALLOWED_BOUNDS.contains(latlon)) {
    msgDiv.innerHTML= "<h2>Error: Outside University Bounds </h2><hr>" +
    "<h3><span class=\"secondary\" id=\"whichtest\"> We've detected that you are outside the" +
    " bounds of Cornell University! <a onclick=\"btnStartClick()\"> Test my speed anyway </a></span></h3>";
    lat = LAT_CENTER;
    lon = LNG_CENTER;
    latlon = new google.maps.LatLng(lat,lon);
    atCornell = false;
  }

  var styles = [
    {
      stylers: [
        { hue: "#C33FFCC"},
        { saturation: 20 }
      ]
    },{
      featureType: "road",
      elementType: "geometry",
      stylers: [
        { lightness: 100 },
        { visibility: "simplified" }
      ]
    },{
      featureType: "road",
      elementType: "labels",
      stylers: [
        { visibility: "off" }
      ]
    }
  ];

  var styledMap = new google.maps.StyledMapType(styles,
    {name: "Styled Map"});

  var myOptions={
    center:latlon,zoom:18,
    //mapTypeId:google.maps.MapTypeId.ROADMAP,
    //mapTypeControl:false,
    mapTypeControlOptions: {
      mapTypeIds: [google.maps.MapTypeId.ROADMAP, 'map_style']
    }
  };

  map=new google.maps.Map(document.getElementById("mapholder"),myOptions);


  map.mapTypes.set('map_style', styledMap);
  map.setMapTypeId('map_style');

  var iconBase = 'https://maps.google.com/mapfiles/kml/shapes/';

  marker=new google.maps.Marker(
    {position:latlon,
    map:map,
    animation: google.maps.Animation.DROP,
    zIndex: 10,
    icon: iconBase + 'flag.png'
  });

  if (atCornell) {
    btnStartClick();
  }

  // Print out the points at which markers should live
  <?php
    $db = mysql_connect("localhost", DB_USERNAME, DB_PASSWORD);

    if (!$db){
      echo "Could not connect to database" . mysql_error();
      exit();
    }

    $db_name = "speedtest";
    if (!mysql_select_db($db_name, $db)){
      die ("Could not select database") . mysql_error();
    }

    $query = "SELECT * FROM Results";
    $sql = mysql_query($query);
    $first = true;

    print("points = [");
    while($row=mysql_fetch_assoc($sql)){
      $lat = $row['lat'];
      $long = $row['long'];
      $down = $row['down'];
      $up = $row['up'];
      $time = $row['time'];
      if ($first) {
        print("[$lat,$long,$down,$up,$time]");
        $first = false;
      } else {
        print(",[$lat,$long,$down,$up,$time]");
      }
    }
    print("];");
  ?>

  // Drop points one by one
  dropOneByOne();

  /*for (var i = 0; i < points.length; i++) {

    var up_color = getColor(points[i][3]);
    var down_color = getColor(points[i][2]);

    var pinColor_local = down_color;

    var pinImage_local = new google.maps.MarkerImage("http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|" + pinColor_local,
      new google.maps.Size(21, 34),
      new google.maps.Point(0,0),
      new google.maps.Point(10, 34));

    var pinShadow_local = new google.maps.MarkerImage("http://chart.apis.google.com/chart?chst=d_map_pin_shadow",
      new google.maps.Size(40, 37),
      new google.maps.Point(0, 0),
      new google.maps.Point(12, 35));

   /* var marker_local = new google.maps.Marker({
      position: new google.maps.LatLng(points[i][0],points[i][1]),
      map: map,
      icon: pinImage_local,
      shadow: pinShadow_local,
      animation: google.maps.Animation.DROP
    });*/

    /*var time = getTime(points[i][4]);
    var lat = points[i][0];
    var lon = points[i][1];

    var contentString_local = getMessage(
      down_color,
      up_color,
      points[i][2],
      points[i][3],
      time);

    window.setTimeout(
      dropEm,
      1000,
      lat,
      lon,
      pinImage_local,
      pinShadow_local,
      contentString_local
    );

    //attachMessage(marker_local, contentString_local);
  }*/

}

function dropOneByOne() {
  if (points.length == 0) {
    return;
  }

  point = points.pop();
  var up_color = getColor(point[3]);
  var down_color = getColor(point[2]);

  var pinColor_local = down_color;

  var pinImage_local = new google.maps.MarkerImage("http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|" + pinColor_local,
    new google.maps.Size(21, 34),
    new google.maps.Point(0,0),
    new google.maps.Point(10, 34));

  var pinShadow_local = new google.maps.MarkerImage("http://chart.apis.google.com/chart?chst=d_map_pin_shadow",
    new google.maps.Size(40, 37),
    new google.maps.Point(0, 0),
    new google.maps.Point(12, 35));

  var time = getTime(point[4]);
  var lat = point[0];
  var lon = point[1];

  var contentString_local = getMessage(
    down_color,
    up_color,
    point[2],
    point[3],
    time);

  dropEm(
    lat,
    lon,
    pinImage_local,
    pinShadow_local,
    contentString_local
  );

  setTimeout(
    dropOneByOne,
    DROP_TIMING,
    points);
}

function dropEm(lat, lon, pinImage, pinShadow, content) {
  var marker = dropPin(lat, lon, pinImage, pinShadow);
  attachMessage(marker, content);
}

function dropPin(lat, lon, pinImage, pinShadow){

  var marker_local = new google.maps.Marker({
    position: new google.maps.LatLng(lat,lon),
    map: map,
    icon: pinImage,
    shadow: pinShadow,
    zIndex: 1,
    animation: google.maps.Animation.DROP
  });

  return marker_local;
}


function attachMessage(marker, message) {
  var infowindow_local = new google.maps.InfoWindow({
    content: message,
    maxWidth: 400
  });

  google.maps.event.addListener(marker, 'mouseover', function() {
    infowindow_local.open(map, this);
  });

  google.maps.event.addListener(marker, 'mouseout', function() {
    infowindow_local.close(map, this);
  });
}

function showError(error)
  {
  var header = "";
  var content = "";
  switch(error.code)
    {
    case error.PERMISSION_DENIED:
      header = "Geolocation Denied";
      content = "You have denied the ability to use your geolocation, so we " +
        "can't add you to the map! ";
      break;
    case error.POSITION_UNAVAILABLE:
      header = "Location Unavailable";
      content = "We cannot seem to access your geolocation. Try re-loading the"+
        " page! ";
      break;
    case error.TIMEOUT:
      header = "Location Timeout";
      content = "The browser's request to use your geolocation timed out. Try " +
        "and make sure you answer the prompt! ";
      break;
    case error.UNKNOWN_ERROR:
      header = "Unknown Error";
      content = "An unknown error occurred. Try re-loading the page! "
      break;
    }

    msgDiv.innerHTML= "<h2>Error: " + header +"</h2><hr>" +
    "<h3><span class=\"secondary\" id=\"whichtest\"> " + content +
    "<a onclick=\"btnStartClick()\"> Test my speed anyway </a></span></h3>";
    lat = LAT_CENTER;
    lon = LNG_CENTER;
    latlon = new google.maps.LatLng(lat,lon);
    atCornell = false;
    showPosition(lat, lon, latlon);
  }

// Returns the appropriate point color, given a download speed
function getColor(down) {
  if (down <= 2.0) {
    return "FF0000";
  } else if (down <= 4.0) {
    return "FF3232";
  } else if (down <= 6.0) {
    return "FF6464";
  } else if (down <= 8.0) {
    return "FF9696";
  } else if (down <= 10.0) {
    return "FFC8C8";
  } else if (down <= 12.0) {
    return "C8FFC8";
  } else if (down <= 14.0) {
    return "96FF96";
  } else if (down <= 16.0) {
    return "64FF64";
  } else if (down <= 18.0) {
    return "32FF32";
  } else {
    return "00FF00";
  }
}

getLocation();

</script>

</body>
</html>
