<?php
include_once(dirname(__FILE__).'/sql/queries/queries.php');
include_once(dirname(__FILE__).'/php/MySQLDatabase/MySQLDatabaseConn.php');
include_once(dirname(__FILE__).'/php/ini/GIVECenterIni.php');

$conn=0;
try {
    $conn = new MySQLDatabaseConn($GIVE_MYSQL_SERVER, $GIVE_MYSQL_DATABASE, $GIVE_MYSQL_UNAME, $GIVE_MYSQL_PASS);
}
catch(Exception $e){
    echo $e;
}

//to hold various error states
$error = array();

//set to 'failed' if previous login attempt yielded no matches for the uname/pass combo
if(isset($_GET['login'])) {
$error['login'] = $_GET['login'];
}
//set to 'true' if user was redirected to the login page via another page's logout button
if(isset($_GET['logout'])) {
$error['logout'] = $_GET['logout'];
}
//set to 'conn' if an exception was thrown during login connecting to the database
//set to 'query' if an exception was thrown during login querying the database
if(isset($_GET['except'])) {
$error['except'] = $_GET['except'];
}
//set to the error code resulting from an exception during login
if(isset($_GET['code'])) {
$error['code'] = $_GET['code'];
}
    
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>GIVE Center Display Banners</title>
<style type="text/css">
<!--
body {
font: 100%/1.4 Verdana, Arial, Helvetica, sans-serif;
background: #cccccc;
margin: 0;
padding: 0;
color: #000;
}
/* ~~ Element/tag selectors ~~ */
ul, ol, dl { /* Due to variations between browsers, it's best practices to zero padding and margin on lists. For consistency, you can either specify the amounts you want here, or on the list items (LI, DT, DD) they contain. Remember that what you do here will cascade to the .nav list unless you write a more specific selector. */
padding: 0;
margin: 0;
}
h1, h2, h3, h4, h5, h6, p {
margin-top: 0; /* removing the top margin gets around an issue where margins can escape from their containing div. The remaining bottom margin will hold it away from any elements that follow. */
padding-right: 15px;
padding-left: 15px; /* adding the padding to the sides of the elements within the divs, instead of the divs themselves, gets rid of any box model math. A nested div with side padding can also be used as an alternate method. */
}
a img { /* this selector removes the default blue border displayed in some browsers around an image when it is surrounded by a link */
border: none;
}
/* ~~ Styling for your site's links must remain in this order - including the group of selectors that create the hover effect. ~~ */
a:link {
color:#414958;
text-decoration: underline; /* unless you style your links to look extremely unique, it's best to provide underlines for quick visual identification */
}
a:visited {
color: #4E5869;
text-decoration: underline;
}
a:hover, a:active, a:focus { /* this group of selectors will give a keyboard navigator the same hover experience as the person using a mouse. */
text-decoration: none;
}
.header {
background: #6F7D94;
}
/* ~~ this container surrounds all other divs giving them their percentage-based width ~~ */
.container {
width: 80%;
max-width: 1260px;/* a max-width may be desirable to keep this layout from getting too wide on a large monitor. This keeps line length more readable. IE6 does not respect this declaration. */
min-width: 780px;
margin: 0 auto; /* the auto value on the sides, coupled with the width, centers the layout. It is not needed if you set the .container's width to 100%. */
overflow: hidden; /* this declaration makes the .container clear all floated columns within it. */
background-color: #cccccc;
}
/* ~~ These are the columns for the layout. ~~

1) Padding is only placed on the top and/or bottom of the divs. The elements within these divs have padding on their sides. This saves you from any "box model math". Keep in mind, if you add any side padding or border to the div itself, it will be added to the width you define to create the *total* width. You may also choose to remove the padding on the element in the div and place a second div within it with no width and the padding necessary for your design.

2) No margin has been given to the columns since they are all floated. If you must add margin, avoid placing it on the side you're floating toward (for example: a right margin on a div set to float right). Many times, padding can be used instead. For divs where this rule must be broken, you should add a "display:inline" declaration to the div's rule to tame a bug where some versions of Internet Explorer double the margin.

3) Since classes can be used multiple times in a document (and an element can also have multiple classes applied), the columns have been assigned class names instead of IDs. For example, two sidebar divs could be stacked if necessary. These can very easily be changed to IDs if that's your preference, as long as you'll only be using them once per document.

4) If you prefer your nav on the right instead of the left, simply float these columns the opposite direction (all right instead of all left) and they'll render in reverse order. There's no need to move the divs around in the HTML source.

*/
/*Right nav bar */
.sidebar1 {
float: right;
width: 12.5%;
background-color: #FFF;
}
/*Left nav bar */
.sidebar2 {
visibility:hidden;
float: left;
width: 12.5%;
padding-top: 90px;
background-color: #FF9;
background-color: #cccccc;
}
/* Center content */
.content {
position: absoulte;
width: 75%;
float: left;
background-image: url(img/gradientHORIZ.png);
height: 100%;
}
/*Quick Search hint */
input.hint {
color: grey;
}
/* ~~ This grouped selector gives the lists in the .content area space ~~ */
.content ul, .content ol {
padding: 0 15px 15px 40px; /* this padding mirrors the right padding in the headings and paragraph rule above. Padding was placed on the bottom for space between other elements on the lists and on the left to create the indention. These may be adjusted as you wish. */
}
/* ~~ The navigation list styles (can be removed if you choose to use a premade flyout menu like Spry) ~~ */
ul.nav {
list-style: none; /* this removes the list marker */
border-top: 1px solid #666; /* this creates the top border for the links - all others are placed using a bottom border on the LI *//* this creates the space between the navigation on the content below */
}
ul.nav li {
border-bottom: 1px solid #666; /* this creates the button separation */
}
ul.nav a, ul.nav a:visited { /* grouping these selectors makes sure that your links retain their button look even after being visited */
padding: 5px 5px 5px 15px;
display: block; /* this gives the link block properties causing it to fill the whole LI containing it. This causes the entire area to react to a mouse click. */
text-decoration: none;
background: #8090AB;
color: #000;
}
ul.nav a:hover, ul.nav a:active, ul.nav a:focus { /* this changes the background and text color for both mouse and keyboard navigators */
background: #6666aa;
color: #FFF;
}
/* ~~miscellaneous float/clear classes~~ */
.fltrt { /* this class can be used to float an element right in your page. The floated element must precede the element it should be next to on the page. */
float: right;
margin-left: 8px;
}
.fltlft { /* this class can be used to float an element left in your page. The floated element must precede the element it should be next to on the page. */
float: left;
margin-right: 8px;
}
.clearfloat { /* this class can be placed on a <br /> or empty div as the final element following the last floated div (within the #container) if the overflow:hidden on the .container is removed */
clear:both;
height:0;
font-size: 1px;
line-height: 0px;
}
-->
</style>
<!--[if lte IE 7]>
<style>
.content { margin-right: -1px; } /* this 1px negative margin can be placed on any of the columns in this layout with the same corrective effect. */
ul.nav a { zoom: 1; } /* the zoom property gives IE the hasLayout trigger it needs to correct extra whiltespace between the links */
</style>
<![endif]-->

<script type="text/javascript" src="js/Navigation.js"></script>
</head>

<body><div class="container" id="content">
<div align="center"></div>
<!--Insert sidebar2 to hold shape -->
<div class = "sidebar2"></div>
<!--Right nav bar -->
<div class="sidebar1">
<div align="center">
<ul class="nav">
<li><a href="Admin.php">Admin</a></li>
<li><a href="BrowseAll.php">Browse All</a></li>
<li><a href="Homepage.php">Homepage</a></li>
<li><a href="php/Session/Logout.php">Logout</a></li>

<li>
<input type="text" class="hint" value="Search..."
onfocus="if (this.className=='hint') { this.className = ''; this.value = ''; }"
onblur="if (this.value == '') { this.className = 'hint'; this.value = 'Search...'; }">
</li>
</ul>
<!-- end .sidebar1 --></div>
</div>


<!--Center content -->
<div class="content" id="content">
<div align="center">
<!--Banner here -->
<a href="#">
<img src=<?php echo get_banner_latest($conn); ?> alt="giveBanner" name="Insert_logo" width="100%" height="90" id="giveBanner" style="background: #8090AB; display:block;" />
</a>
</div>
<!--Display previous banners w/ button -->
<div align="center"><b>Change Banner: </b>
<h1 align="center">&nbsp;</h1>
<h1 align="center">Stored Banners:</h1>
<form method="POST" action="sql/update/update_banner_old.php">
<?php
            
        $banners = get_banners($conn);
      
        foreach($banners as $temp){
            echo "<a href='#'>";
            echo "<img src='".$temp['path']."' alt='giveBanner' name='Insert_logo' width='100%'
height='90' id='giveBanner' style='background: #8090AB; display:block;' />";
            echo "</a><input name='set_banner' type='submit' value='".$temp['id']."' />";
        }
        $conn->close();
        ?>
</form>
</div>
</div>
</body>
</html>

<?php

function get_banners($conn){
    
    $query = "SELECT id,path
FROM image_paths
WHERE image_type = 'banner'
ORDER BY id DESC";
    
    $conn->query($query);
    
    $banners = $conn->fetchAllAsAssoc();
    
    return $banners;
}

?>