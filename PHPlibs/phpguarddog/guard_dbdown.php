<?php
print "<"."html".">";
print "<"."body".">";
include('guard.php');
include('guard_extend.php');


print_custom_styles();
print_include("top");
print_custom_menu();

?>

<br>
<font class=title>System message</font>
<br>

<div class=errorfield>
DATABASE ERROR<br>

	<br>
The PHP Guard Dog database is currently unavailable.
<br><br><br>Please try again later.
</div>


<?php


print_include("bottom");

print "<"."/body".">";
print "<"."/html".">";
?>
