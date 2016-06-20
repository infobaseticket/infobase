<?
/*
The copyright for all material provided on this site ("Infobase") is held by Frederick Eyland (the original creator of the material) 
and partially by Base. Except as stated herein, none of the material may be copied, reproduced, imitated, distributed, republished, 
downloaded, displayed, posted or transmitted in any form or by any means, including, but not limited to, electronic, mechanical, 
photocopying, recording, or otherwise, without the prior written permission of Frederick Eyland. 
Permission is granted to display, copy, distribute and download the materials on this Site for personal, non-commercial use only provided 
you do not modify the materials and that you retain all copyright and other proprietary notices contained in the materials. 
You also may not, without Frederick Eyland's permission, "mirror" any material contained on this Site on any other server. This 
permission terminates automatically if you breach any of these terms or conditions. Upon termination, you will immediately destroy any 
downloaded and printed materials. Any unauthorized use of any material contained on this Site may violate copyright laws, trademark laws, the laws of privacy and publicity, and communications regulations and statutes.
If you see the contents of our pages on another Web Site, please notify me of the URL or company of that Web site so that I may confirm whether written permission was granted. 
frederick@eyland.be
*/
?>
<html>
<body bgcolor="ffffff">
<?
      include("../include/config.php");
      echo "<center><font color='#3399FF' size='4' face='Arial, Helvetica, sans-serif'><u><b>Neighbours for cell = $cell:</u></font><br><br>";
      echo "<table width=50%>";
	  $k=1; 
       $db = mysql_connect("$dbserver", "$dbuser", "$dbpasswd");
       mysql_select_db("$dbname1",$db) or die ("could not connect to $dbname1");
	   $result80=mysql_query("select * from data_switch_neigh WHERE sc='$cell' order by sc Asc",$db) or die( "Unable to select table data_switch_neigh");		
          while ($row80 = mysql_fetch_array($result80))  {
		    for ($i = 3; $i <= 35; $i++) {
			 if ($row80[$i]!='NULL' AND $cell!=$row80[$i]){
        	 echo (" <td align='center' width=10%><b>$row80[$i]</b>");
			 	if ($k==2){
	             echo "<tr>";
	             $k=0;
	            }
	            $k++;
			 }
			}
         }
	 echo "</table>";
	 
?>
</body>
</html>