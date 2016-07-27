<?

$page = $_POST['page'];
$rp = $_POST['rp'];
$sortname = $_POST['sortname'];
$sortorder = $_POST['sortorder'];

if (!$sortname) $sortname = 'name';
if (!$sortorder) $sortorder = 'desc';

$sort = "ORDER BY $sortname $sortorder";

if (!$page) $page = 1;
if (!$rp) $rp = 10;
$start = (($page-1) * $rp);

$limit = "LIMIT $start, $rp";

$sql = "SELECT iso,name,printable_name,iso3,numcode FROM country $sort $limit";

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT" );
header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
header("Cache-Control: no-cache, must-revalidate" );
header("Pragma: no-cache" );
header("Content-type: text/xml");
$xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
$xml .= "<rows>";
$xml .= "<page>1</page>";
$xml .= "<total>1</total>";
//while ($row = mysql_fetch_array($result)) {
$xml .= "<row id='".$row['iso']."'>";
$xml .= "<cell><![CDATA[1]]></cell>";
$xml .= "<cell><![CDATA[2]]></cell>";
$xml .= "<cell><![CDATA[3]]></cell>";
$xml .= "<cell><![CDATA[".utf8_encode($row['iso3'])."]]></cell>";
$xml .= "<cell><![CDATA[5]]></cell>";
$xml .= "</row>";
//}

$xml .= "</rows>";
echo $xml;
?>