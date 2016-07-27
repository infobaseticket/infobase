<?php
$popup=$popup+1;
$pop_data.="
<div id='".$BSDSKEY.$_POST['candidate'].$_POST['upgnr'].$popup."' class='modal fade' role='dialog' aria-labelleby='myModelLabel' aria-hidden='true'>
	<div class='modal-dialog'>
    	<div class='modal-content'>
		<div class='modal-header'>
			<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>×</button>
			<h3 id='myModalLabel ".$class."'>.:: BSDS INFO ::.</h3>
		</div>
		<div class='modal-body'>
		<table class='table table-striped'>
			<tr>
			<td><b>Initially created:</b></td>
			<td>".$ORIGIN_DATE."<br>";
			if ($fullname){
			$pop_data.="$fullname";
			}
			$pop_data.=" ($DESIGNER_CREATE)<br>";
			if ($mobile){
			$pop_data.=$mobile."<br>";
			}
			if ($email){
			$pop_data.=$email."<br>";
			}
			$pop_data.="</td>";
			if ($technologies){
				$pop_data.="<tr><td><b>Technologies funded:</b></td>
				<td>".$technologies."</td></tr>";
			}

			
			$userdetails=getuserdata($DESIGNER_UPDATE);
			$email_update=trim($userdetails['email']);
			$fullname_update=trim($userdetails['fullname']);
			$mobile_update=trim($userdetails['mobile']);

			if ($fullname_update || $mobile_update || $email_update){
				$pop_data.="<tr><td valign=top><b>Latest update PRE: </b></td><td>".$CHANGE_DATE."<br>";
			}

			if ($fullname_update){
			$pop_data.="$fullname_update<br>";
			}
			if ($mobile_update){
			$pop_data.="$mobile_update<br>";
			}
			if ($email_update){
			$pop_data.="$email_update<br>";
			}

			$userdetails=getuserdata($UPDATE_BY_AFTER_COPY);
			$email_update_AFTER_COPY=trim($userdetails['email']);
			$fullname_update_AFTER_COPY=trim($userdetails['fullname']);
			$mobile_update_AFTER_COPY=trim($userdetails['mobile']);

			$pop_data.="<tr><td valign=top>";
			if ($fullname_update_AFTER_COPY){
			$pop_data.=$fullname_update_AFTER_COPY."<br>";
			}
			if ($mobile_update_AFTER_COPY){
			$pop_data.=$mobile_update_AFTER_COPY."<br>";
			}
			if ($email_update_AFTER_COPY){
			$pop_data.=$email_update_AFTER_COPY."<br>";
			}

			$pop_data.="</td></tr>";

			$userdetails=getuserdata($STATUS_BY);
			$email_air_approval=trim($userdetails['email']);
			$fullname_air_approval=trim($userdetails['fullname']);
			$mobile_air_approval=trim($userdetails['mobile']);

			if ($status!="BSDS AS BUILD"){
				if ($fullname_air_approval || $mobile_air_approval || $email_air_approval){
					$pop_data.="<tr><td valign=top><b>".$TEAML_FUNDED ." by Partner:</b></td><td>On $STATUS_DATE<br>";
				}
				if ($fullname_air_approval){
				$pop_data.=$fullname_air_approval."<br>";
				}
				if ($mobile_air_approval){
				$pop_data.=$mobile_air_approval."<br>";
				}
				if ($email_air_approval){
				$pop_data.=$email_air_approval."<br>";
				}

				$userdetails=getuserdata($QA_STATUS_BY);
				$email_QA_approval=trim($userdetails['email']);
				$fullname_QA_approval=trim($userdetails['fullname']);
				$mobile_QA_approval=trim($userdetails['mobile']);

				if ($fullname_QA_approval || $mobile_QA_approval || $email_QA_approval || $QA_STATUS_BY=="BASE"){
					$pop_data.="<tr><td valign=top><b>".$QA_STATUS." by Q&A:</b></td><td>On $QA_STATUS_DATE ($QA_STATUS_BY)<br>";
				}
				if ($fullname_QA_approval){
				$pop_data.=$fullname_QA_approval."<br>";
				}
				if ($mobile_QA_approval){
				$pop_data.=$mobile_QA_approval."<br>";
				}
				if ($email_QA_approval){
				$pop_data.=$email_QA_approval."<br>";
				}
			}
			$pop_data.="</td></tr><tr><td>Comments: </td>
			<td>".$COMMENTS."</td></tr>
			</table>
			</div>
		</div>
	</div>
</div>";
?>