<?
require_once($_SERVER['DOCUMENT_ROOT']."/include/config.php");
require_once($config['phpguarddog_path']."/guard.php");

require_once($config['sitepath_url']."/include/PHP/oci8_funcs.php");
include("../procedures/cur_plan_procedures.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>

<head>

	<script type="text/javascript" src="<?=$config['sitepath_url']?>/include/javascripts/jquery/jquery.js"></script>
	<script type="text/javascript" src="<?=$config['sitepath_url']?>/include/javascripts/jquery/jquery.form.js"></script>
	<title>Untitled 3</title>
	
	<script type="text/javascript">	
		// prepare the form when the DOM is ready 
	$(document).ready(function() { 
	    var options = { 
	        target:        '#output1',   // target element(s) to be updated with server response 
	        beforeSubmit:  validate   // pre-submit callback 
	        //success:       showResponse  // post-submit callback 
	 
	        // other available options: 
	        //url:       url         // override for form's 'action' attribute 
	        //type:      type        // 'get' or 'post', override for form's 'method' attribute 
	        //dataType:  null        // 'xml', 'script', or 'json' (expected server response type) 
	        //clearForm: true        // clear all form fields after successful submit 
	        //resetForm: true        // reset the form after successful submit 
	 
	        // $.ajax options can be used here too, for example: 
	        //timeout:   3000 
	    }; 
	 
	    // bind form using 'ajaxForm' 
	    $('#searchForm').ajaxForm(options); 
	}); 
	 
	// pre-submit callback 
	function validate (formData, jqForm, options) { 
	    // formData is an array; here we use $.param to convert it to a string to display it 
	    // but the form plugin does this for you automatically when it submits the data 
	    var queryString = $.param(formData); 
	 
	    // jqForm is a jQuery object encapsulating the form element.  To access the 
	    // DOM element for the form do this: 
	    // var formElement = jqForm[0]; 
	    <?
	    $par="pl_CABTYPE";
	    ?>
	  var cabtype = $('select[@name=<?=$par?>]').fieldValue();
	  var cabtype = $('select[@name=<?=$par?>]').fieldValue();
	  alert (cabtype);
	 	
	 	if (cabtype == '2206')
		{
			alert('About to submit: \n\n' +  "--" + cabtype); //queryString
			return false; 
		}else{
			return true; 
		}
	} 
	 
	// post-submit callback 
	function showResponse(responseText, statusText)  { 
	    // for normal html responses, the first argument to the success callback 
	    // is the XMLHttpRequest object's responseText property 
	 
	    // if the ajaxForm method was passed an Options Object with the dataType 
	    // property set to 'xml' then the first argument to the success callback 
	    // is the XMLHttpRequest object's responseXML property 
	 
	    // if the ajaxForm method was passed an Options Object with the dataType 
	    // property set to 'json' then the first argument to the success callback 
	    // is the json data object returned by the server 
	 /*+ responseText + */
	    //alert('status: ' + statusText + '\n\nresponseText: \n\n\nThe output div should have already been updated with the responseText.'); 
	    alert ("hallo");
	} 
	</script>
</head>

<body>

<form action="<?=$_SERVER['PHP_SELF']?>" method="POST" id="searchForm">
<input type="hidden" name="action" value="save">
	
<table cellpadding="0" cellspacing="0"  border="0" width="100%">
<tr valign="top" align="center">
  <td align='center'>	
	 <table border="0" bordercolor="lighblue" cellpadding="0" cellspacing="1" align="center" width="98%">
	 <tr>
	 	<td colspan="8" bgcolor="Black" height="2px">&nbsp;</td>
	 </tr>
	 <tr>
		<td class="table_head"><select name="type"><option>GSM900</option><option>GSM1800</option></select></td>
		<td colspan="3">
	 	    <table width="100%" height="100%" border="0" bordercolor="lighblue" cellpadding="0" align="center" cellspacing="1">
		 	<tr>
				<td class="parameter_name">Cabinettype</td>
				<td class="<?=$color[4][1]?>">
                	<SELECT NAME="pl_CABTYPE" class="<?=$color[4][2]?>">
						<? echo get_select_CABTYPE($pl_CABTYPE); ?>
                    </select>
				</td>
				<td class="parameter_name"># cabinet <?=$type?></td>
				<td class="<?=$color[4][1]?>"><SELECT NAME="pl_NR_OF_CAB" class="<?=$color[4][2]?>" id="pl_NR_OF_CAB">
											<? get_select_numbers($pl_NR_OF_CAB,0,3,1,'no');?></SELECT></td>
			 </tr>
			 <tr>
				<td class="parameter_name">CDU Type</td>
				<TD class="<?=$color[4][1]?>"><SELECT NAME="pl_CDUTYPE" class="<?=$color[4][2]?>">
								<? 	echo get_select_CDU($pl_CDUTYPE); 	?></SELECT>
				</td>
				<td class="parameter_name"><font color="blue" size=1><b>Battery Backup Sys</td>
				<TD class="<?=$color[4][1]?>"><SELECT NAME="pl_BBS" class="<?=$color[4][2]?>"><? echo get_select_BBS($pl_BBS);  ?></SELECT>
				</td>
			</tr>
			<tr id="cur_DXU1">
				<td class="parameter_name"><font color="blue" size=1><b>DXU CAB 1</td>
				<TD class="<?=$color[4][1]?>"><SELECT NAME="pl_DXUTYPE1" class="<?=$color[4][2]?>">
								  	<? echo get_select_DXU($pl_DXUTYPE1); ?></SELECT>
				</td>
				<td class="parameter_name"></td>
				<TD class="PLANNED_SAME"></td>
			</tr>
			<tr id="cur_DXU2">
				<td class="parameter_name"><font color="blue" size=1><b>DXU CAB 2</td>
				<TD class="<?=$color[4][1]?>"><SELECT NAME="pl_DXUTYPE2" class="<?=$color[4][2]?>">
									<? echo get_select_DXU($pl_DXUTYPE2); ?></SELECT>
				</td>
				<td class="parameter_name"></td>
				<TD class="PLANNED_SAME"></td>
			</tr>
			<tr id="cur_DXU3">
				<td class="parameter_name"><font color="blue" size=1><b>DXU CAB 3</td>
				<TD class="<?=$color[4][1]?>"><SELECT NAME="pl_DXUTYPE3" class="<?=$color[4][2]?>">
									<? echo get_select_DXU($pl_DXUTYPE3); ?></SELECT>
				</td>
				<td class="parameter_name"></td>
				<TD class="PLANNED_SAME"></td>
			</tr>
			</table>
		</td>
	 </tr>
	 <tr>
		 <td width="1px" bgcolor="black">&nbsp;</td>
		 <td bgcolor="black"><font color="White" size="2"><b>Sector 1</b></font></td>
		 <td bgcolor="black"><font color="White" size="2"><b>Sector 2</b></font></td>
		 <td bgcolor="black"><font color="White" size="2"><b>Sector 3</b></font></td>
		<? if ($CONFIG_4){ ?>
		 <td class="table_head"><font color="White" size="2"><b>Sector <?=$sec4?></b></font></td>
		 <? } ?>
	  </tr>
	  <tr>
		 <td class="parameter_name" width="120px">State</td>
 		 <td class="PLANNED_SAME"><?=$STATE_1?></td>
		 <td class="PLANNED_SAME"><?=$STATE_2?></td>
		 <td class="PLANNED_SAME"><?=$STATE_3?></td>
		 <? if ($CONFIG_4){ ?>
		 <td class="PLANNED_SAME"><?=$STATE_4?></td>
		 <? } ?>

	  </tr>
 	  <tr>
		 <td class="parameter_name">Config</td>
 		 <td class="<?=$color[4][1]?>">
         	<SELECT NAME="pl_CONFIG_1" class="<?=$color[4][2]?>">
				<?
					get_select_celleq($pl_CONFIG_1);
				?>
            </select>
         </td>
	 	 <td class="<?=$color[5][1]?>">
         	<SELECT NAME="pl_CONFIG_2" class="<?=$color[5][2]?>">
		 		<?
					get_select_celleq($pl_CONFIG_2);
				?>
         	</select>
         </td>
	 	 <td class="<?=$color[6][1]?>">
         	<SELECT NAME="pl_CONFIG_3" class="<?=$color[6][2]?>">
				<?
					get_select_celleq($pl_CONFIG_3);
				?>
            </select>
         </td>
		 <? if ($CONFIG_4){ ?>
		 <td class="<?=$color[8][1]?>">
         	<SELECT NAME="pl_CONFIG_4" class="<?=$color[8][2]?>">
				<?
					get_select_celleq($pl_CONFIG_4);
				?>
            </select>
         </td>
		 <? } ?>
	  </tr>
 	  <tr>
		 <td class="parameter_name"><font color="blue"><b>TMA</font></td>
     	 <td class="<?=$color[4][1]?>"><SELECT NAME="pl_TMA_1" class="<?=$color[4][2]?>"><? echo get_select_TMA($pl_TMA_1);?></SELECT></td>
     	 <td class="<?=$color[5][1]?>"><SELECT NAME="pl_TMA_2" class="<?=$color[5][2]?>"><? echo get_select_TMA($pl_TMA_2);?></SELECT></td>
     	 <td class="<?=$color[6][1]?>"><SELECT NAME="pl_TMA_3" class="<?=$color[6][2]?>"><? echo get_select_TMA($pl_TMA_3);?></SELECT></td>
		 <? if ($CONFIG_4){ ?>
		 <td class="<?=$color[8][1]?>"><SELECT NAME="pl_TMA_4" class="<?=$color[8][2]?>"><? echo get_select_TMA($pl_TMA_4);?></SELECT></td>
		 <? } ?>
	  </tr>
 	  <tr id="FEQ_ACTIVE1">
		 <td class="parameter_name"><b>FREQ active network CAB1</td>
 	 	 <td class="<?=$color[4][1]?>"><SELECT NAME="pl_FREQ_ACTIVE1_1" class="<?=$color[4][2]?>"><? get_select_numbers($pl_FREQ_ACTIVE1_1,0,12,1,'no');?> </td>
	 	 <td class="<?=$color[5][1]?>"><SELECT NAME="pl_FREQ_ACTIVE1_2" class="<?=$color[5][2]?>"><? get_select_numbers($pl_FREQ_ACTIVE1_2,0,12,1,'no');?></td>
	 	 <td class="<?=$color[6][1]?>"><SELECT NAME="pl_FREQ_ACTIVE1_3" class="<?=$color[6][2]?>"><? get_select_numbers($pl_FREQ_ACTIVE1_3,0,12,1,'no');?></td>
		 <? if ($CONFIG_4){ ?>
		 <td class="<?=$color[8][1]?>"><SELECT NAME="pl_FREQ_ACTIVE1_4" class="<?=$color[8][2]?>"><? get_select_numbers($pl_FREQ_ACTIVE1_4,0,12,1,'no');?></td>
		 <? } ?>
	  </tr>
 	  <tr id="FEQ_ACTIVE2">
		 <td class="parameter_name"><b>FREQ active network CAB2</td>
 	 	 <td class="<?=$color[4][1]?>"><SELECT NAME="pl_FREQ_ACTIVE2_1" class="<?=$color[4][2]?>"><? get_select_numbers($pl_FREQ_ACTIVE2_1,0,12,1,'no');?> </td>
	 	 <td class="<?=$color[5][1]?>"><SELECT NAME="pl_FREQ_ACTIVE2_2" class="<?=$color[5][2]?>"><? get_select_numbers($pl_FREQ_ACTIVE2_2,0,12,1,'no');?></td>
	 	 <td class="<?=$color[6][1]?>"><SELECT NAME="pl_FREQ_ACTIVE2_3" class="<?=$color[6][2]?>"><? get_select_numbers($pl_FREQ_ACTIVE2_3,0,12,1,'no');?></td>
		 <? if ($CONFIG_4){ ?>
		 <td class="<?=$color[8][1]?>"><SELECT NAME="pl_FREQ_ACTIVE2_4" class="<?=$color[8][2]?>"><? get_select_numbers($pl_FREQ_ACTIVE2_4,0,12,1,'no');?></td>
		 <? } ?>
	  </tr>
 	  <tr id="FEQ_ACTIVE3">
		 <td class="parameter_name"><b>FREQ active network CAB3</td>
 	 	 <td class="<?=$color[4][1]?>"><SELECT NAME="pl_FREQ_ACTIVE3_1" class="<?=$color[4][2]?>"><? get_select_numbers($pl_FREQ_ACTIVE3_1,0,12,1,'no');?> </td>
	 	 <td class="<?=$color[5][1]?>"><SELECT NAME="pl_FREQ_ACTIVE3_2" class="<?=$color[5][2]?>"><? get_select_numbers($pl_FREQ_ACTIVE3_2,0,12,1,'no');?></td>
	 	 <td class="<?=$color[6][1]?>"><SELECT NAME="pl_FREQ_ACTIVE3_3" class="<?=$color[6][2]?>"><? get_select_numbers($pl_FREQ_ACTIVE3_3,0,12,1,'no');?></td>
		 <? if ($CONFIG_4){ ?>
		 <td class="<?=$color[8][1]?>"><SELECT NAME="pl_FREQ_ACTIVE3_4" class="<?=$color[8][2]?>"><? get_select_numbers($pl_FREQ_ACTIVE3_4,0,12,1,'no');?></td>
		 <? } ?>
	  </tr>
 	  <tr id="TRU_CAB1">
		 <td class="parameter_name"><b>TRU installed CAB1</b></td>
    	 <td class="<?=$color[4][1]?>">
    	 <SELECT NAME="pl_TRU_INST1_1_1" CLASS="<?=$color[4][2]?>"><? get_select_numbers($pl_TRU_INST1_1_1,0,12,1,'no');?>
     	 <SELECT NAME="pl_TRU_TYPE1_1_1" CLASS="<?=$color2[4][2]?>"><? echo get_select_TRU($pl_TRU_TYPE1_1_1);?></select><br>
     	 <SELECT NAME="pl_TRU_INST1_2_1" CLASS="<?=$color[4][2]?>"><? get_select_numbers($pl_TRU_INST1_2_1,0,12,1,'no');?>
     	 <SELECT NAME="pl_TRU_TYPE1_2_1" CLASS="<?=$color2[4][2]?>"><?echo get_select_TRU($pl_TRU_TYPE1_2_1);?></select>
     	 </td>
     	 <td class="<?=$color[5][1]?>">
    	 <SELECT NAME="pl_TRU_INST1_1_2" CLASS="<?=$color[5][2]?>"><? get_select_numbers($pl_TRU_INST1_1_2,0,12,1,'no');?>
     	 <SELECT NAME="pl_TRU_TYPE1_1_2" CLASS="<?=$color2[5][2]?>"><? echo get_select_TRU($pl_TRU_TYPE1_1_2);?></select><br>
     	 <SELECT NAME="pl_TRU_INST1_2_2" CLASS="<?=$color[5][2]?>"><? get_select_numbers($pl_TRU_INST1_2_2,0,12,1,'no');?>
     	 <SELECT NAME="pl_TRU_TYPE1_2_2" CLASS="<?=$color2[5][2]?>"><? echo get_select_TRU($pl_TRU_TYPE1_2_2);?></select>
     	 </td>
         <td class="<?=$color[6][1]?>">
    	 <SELECT NAME="pl_TRU_INST1_1_3" CLASS="<?=$color[6][2]?>"><? get_select_numbers($pl_TRU_INST1_1_3,0,12,1,'no');?>
     	 <SELECT NAME="pl_TRU_TYPE1_1_3" CLASS="<?=$color2[6][2]?>"><? echo get_select_TRU($pl_TRU_TYPE1_1_3);?></select><br>
     	 <SELECT NAME="pl_TRU_INST1_2_3" CLASS="<?=$color[6][2]?>"><? get_select_numbers($pl_TRU_INST1_2_3,0,12,1,'no');?>
     	 <SELECT NAME="pl_TRU_TYPE1_2_3" CLASS="<?=$color2[6][2]?>"><? echo get_select_TRU($pl_TRU_TYPE1_2_3);?></select>
	     </td>
		 <? if ($CONFIG_4){ ?>
		 <td class="<?=$color[8][1]?>">
    	 <SELECT NAME="pl_TRU_INST1_1_4" CLASS="<?=$color[8][2]?>"><? get_select_numbers($pl_TRU_INST1_1_4,0,12,1,'no');?>
     	 <SELECT NAME="pl_TRU_TYPE1_1_4" CLASS="<?=$color2[8][2]?>"><? echo get_select_TRU($pl_TRU_TYPE1_1_4);?></select><br>
     	 <SELECT NAME="pl_TRU_INST1_2_4" CLASS="<?=$color[8][2]?>"><? get_select_numbers($pl_TRU_INST1_2_4,0,12,1,'no');?>
     	 <SELECT NAME="pl_TRU_TYPE1_2_4" CLASS="<?=$color2[8][2]?>"><? echo get_select_TRU($pl_TRU_TYPE1_2_4);?></select>
	     </td>
		 <? } ?>
	  </tr>
	  <tr id="TRU_CAB2">
 	     <td class="parameter_name" width="120px"><b>TRU installed CAB2</b></td>
    	 <td class="<?=$color[4][1]?>">
    	 <SELECT NAME="pl_TRU_INST2_1_1" CLASS="<?=$color[4][2]?>"><? get_select_numbers($pl_TRU_INST2_1_1,0,12,1,'no');?>
     	 <SELECT NAME="pl_TRU_TYPE2_1_1" CLASS="<?=$color2[4][2]?>"><? echo get_select_TRU($pl_TRU_TYPE2_1_1);?></select><br>
     	 <SELECT NAME="pl_TRU_INST2_2_1" CLASS="<?=$color[4][2]?>"><? get_select_numbers($pl_TRU_INST2_2_1,0,12,1,'no');?>
     	 <SELECT NAME="pl_TRU_TYPE2_2_1" CLASS="<?=$color2[4][2]?>"><? echo get_select_TRU($pl_TRU_TYPE2_2_1);?></select>
     	 </td>
     	 <td class="<?=$color[5][1]?>">
    	 <SELECT NAME="pl_TRU_INST2_1_2" CLASS="<?=$color[5][2]?>"><? get_select_numbers($pl_TRU_INST2_1_2,0,12,1,'no');?>
     	 <SELECT NAME="pl_TRU_TYPE2_1_2" CLASS="<?=$color2[5][2]?>"><? echo get_select_TRU($pl_TRU_TYPE2_1_2);?></select><br>
     	 <SELECT NAME="pl_TRU_INST2_2_2" CLASS="<?=$color[5][2]?>"><? get_select_numbers($pl_TRU_INST2_2_2,0,12,1,'no');?>
     	 <SELECT NAME="pl_TRU_TYPE2_2_2" CLASS="<?=$color2[5][2]?>"><? echo get_select_TRU($pl_TRU_TYPE2_2_2);?></select>
     	 </td>
         <td class="<?=$color[6][1]?>">
    	 <SELECT NAME="pl_TRU_INST2_1_3" CLASS="<?=$color[6][2]?>"><? get_select_numbers($pl_TRU_INST2_1_3,0,12,1,'no');?>
     	 <SELECT NAME="pl_TRU_TYPE2_1_3" CLASS="<?=$color2[6][2]?>"><? echo get_select_TRU($pl_TRU_TYPE2_1_3);?></select><br>
     	 <SELECT NAME="pl_TRU_INST2_2_3" CLASS="<?=$color[6][2]?>"><? get_select_numbers($pl_TRU_INST2_2_3,0,12,1,'no');?>
     	 <SELECT NAME="pl_TRU_TYPE2_2_3" CLASS="<?=$color2[6][2]?>"><? echo get_select_TRU($pl_TRU_TYPE2_2_3);?></select>
	     </td>
		 <? if ($CONFIG_4){ ?>
		 <td class="<?=$color[8][1]?>">
    	 <SELECT NAME="pl_TRU_INST2_1_4" CLASS="<?=$color[8][2]?>"><? get_select_numbers($pl_TRU_INST2_1_4,0,12,1,'no');?>
     	 <SELECT NAME="pl_TRU_TYPE2_1_4" CLASS="<?=$color2[8][2]?>"><? echo get_select_TRU($pl_TRU_TYPE2_1_4);?></select><br>
     	 <SELECT NAME="pl_TRU_INST2_2_4" CLASS="<?=$color[8][2]?>"><? get_select_numbers($pl_TRU_INST2_2_4,0,12,1,'no');?>
     	 <SELECT NAME="pl_TRU_TYPE2_2_4" CLASS="<?=$color2[8][2]?>"><? echo get_select_TRU($pl_TRU_TYPE2_2_4);?></select>
	     </td>
		 <? } ?>
	  </tr>
 	  <tr id="TRU_CAB3">
		 <td class="parameter_name"><b>TRU installed CAB3</b></td>
    	 <td class="<?=$color[4][1]?>">
    	 <SELECT NAME="pl_TRU_INST3_1_1" CLASS="<?=$color[4][2]?>"><? get_select_numbers($pl_TRU_INST3_1_1,0,12,1,'no');?>
     	 <SELECT NAME="pl_TRU_TYPE3_1_1" CLASS="<?=$color2[4][2]?>"><? echo get_select_TRU($pl_TRU_TYPE3_1_1);?></select><br>
     	 <SELECT NAME="pl_TRU_INST3_2_1" CLASS="<?=$color[4][2]?>"><? get_select_numbers($pl_TRU_INST3_2_1,0,12,1,'no');?>
     	 <SELECT NAME="pl_TRU_TYPE3_2_1" CLASS="<?=$color2[4][2]?>"><? echo get_select_TRU($pl_TRU_TYPE3_2_1);?></select>
     	 </td>
     	 <td class="<?=$color[5][1]?>">
    	 <SELECT NAME="pl_TRU_INST3_1_2" CLASS="<?=$color[5][2]?>"><? get_select_numbers($pl_TRU_INST3_1_2,0,12,1,'no');?>
     	 <SELECT NAME="pl_TRU_TYPE3_1_2" CLASS="<?=$color2[5][2]?>"><? echo get_select_TRU($pl_TRU_TYPE3_1_2);?></select><br>
     	 <SELECT NAME="pl_TRU_INST3_2_2" CLASS="<?=$color[5][2]?>"><? get_select_numbers($pl_TRU_INST3_2_2,0,12,1,'no');?>
     	 <SELECT NAME="pl_TRU_TYPE3_2_2" CLASS="<?=$color2[5][2]?>"><? echo get_select_TRU($pl_TRU_TYPE3_2_2);?></select>
     	 </td>
         <td class="<?=$color[6][1]?>">
    	 <SELECT NAME="pl_TRU_INST3_1_3" CLASS="<?=$color[6][2]?>"><? get_select_numbers($pl_TRU_INST3_1_3,0,12,1,'no');?>
     	 <SELECT NAME="pl_TRU_TYPE3_1_3" CLASS="<?=$color2[6][2]?>"><? echo get_select_TRU($pl_TRU_TYPE3_1_3);?></select><br>
     	 <SELECT NAME="pl_TRU_INST3_2_3" CLASS="<?=$color[6][2]?>"><? get_select_numbers($pl_TRU_INST3_2_3,0,12,1,'no');?>
     	 <SELECT NAME="pl_TRU_TYPE3_2_3" CLASS="<?=$color2[6][2]?>"><? echo get_select_TRU($pl_TRU_TYPE3_2_3);?></select>
	     </td>
		 <? if ($CONFIG_4){ ?>
		 <td class="<?=$color[8][1]?>">
    	 <SELECT NAME="pl_TRU_INST3_1_4" CLASS="<?=$color[8][2]?>"><? get_select_numbers($pl_TRU_INST3_1_4,0,12,1,'no');?>
     	 <SELECT NAME="pl_TRU_TYPE3_1_4" CLASS="<?=$color2[8][2]?>"><? echo get_select_TRU($pl_TRU_TYPE3_1_4);?></select><br>
     	 <SELECT NAME="pl_TRU_INST3_2_4" CLASS="<?=$color[8][2]?>"><? get_select_numbers($pl_TRU_INST3_2_4,0,12,1,'no');?>
     	 <SELECT NAME="pl_TRU_TYPE3_2_4" CLASS="<?=$color2[8][2]?>"><? echo get_select_TRU($pl_TRU_TYPE3_2_4);?></select>
	     </td>
		 <? } ?>
	  </tr>
 	  <tr>
		 <td class="parameter_name">Antenna Type 1</td>
		 <td class="<?=$color[4][1]?>">
         	<SELECT NAME="pl_ANTTYPE1_1" class="<?=$color[4][2]?>">
				<? 	echo get_select_anttype($pl_ANTTYPE1_1);	?>
            </select>
         </td>
	 	 <td class="<?=$color[5][1]?>">
         	<SELECT NAME="pl_ANTTYPE1_2" class="<?=$color[5][2]?>">
				<? 	echo get_select_anttype($pl_ANTTYPE1_2);	?>
            </select>
         </td>
	 	 <td class="<?=$color[6][1]?>">
         	<SELECT NAME="pl_ANTTYPE1_3" class="<?=$color[6][2]?>">
				<? echo get_select_anttype($pl_ANTTYPE1_3); ?>
            </select>
         </td>
		 <? if ($CONFIG_4){ ?>
		  <td class="<?=$color[8][1]?>">
         	<SELECT NAME="pl_ANTTYPE1_4" class="<?=$color[8][2]?>">
				<? echo get_select_anttype($pl_ANTTYPE1_4);	?>
            </select>
         </td>
		 <? } ?>
	  </tr>
	  <tr>
		 <td class="parameter_name">Elektrical downtilt 1</td>
		 	 <td class="<?=$color[4][1]?>"><SELECT NAME="pl_ELECTILT1_1" class="<?=$color[4][2]?>"><? get_select_numbers($pl_ELECTILT1_1,0,15,1,'no');?></td>
	 	 <td class="<?=$color[5][1]?>"><SELECT NAME="pl_ELECTILT1_2" class="<?=$color[5][2]?>"><? get_select_numbers($pl_ELECTILT1_2,0,15,1,'no');?></td>
	 	 <td class="<?=$color[6][1]?>"><SELECT NAME="pl_ELECTILT1_3" class="<?=$color[6][2]?>"><? get_select_numbers($pl_ELECTILT1_3,0,15,1,'no');?></td>
		 <? if ($CONFIG_4){ ?>
		 <td class="<?=$color[8][1]?>"><SELECT NAME="pl_ELECTILT1_4" class="<?=$color[8][2]?>"><? get_select_numbers($pl_ELECTILT1_4,0,15,1,'no');?></td>
		 <? } ?>
	  </tr>
	  <tr>
		 <td class="parameter_name">Mechanical tilt 1</td>
	 	 <td class="<?=$color[4][1]?>"><SELECT NAME="pl_MECHTILT1_1" class="<?=$color[4][2]?>"><? get_select_numbers($pl_MECHTILT1_1,0,15,1,'no');?>&nbsp;
	 	 <SELECT NAME='pl_MECHTILT_DIR1_1' CLASS='<?=$color2[4][2]?>'><option SELECTED><?=$pl_MECHTILT_DIR1_1?><option>DOWNTILT<option>UPTILT</SELECT></td>
	 	 <td class="<?=$color[5][1]?>"><SELECT NAME="pl_MECHTILT1_2" class="<?=$color[5][2]?>"><? get_select_numbers($pl_MECHTILT1_2,0,15,1,'no');?>&nbsp;
	 	 <SELECT NAME='pl_MECHTILT_DIR1_2' CLASS='<?=$color2[5][2]?>'><option SELECTED><?=$pl_MECHTILT_DIR1_2?><option>DOWNTILT<option>UPTILT</SELECT></td>
	 	 <td class="<?=$color[6][1]?>"><SELECT NAME="pl_MECHTILT1_3" class="<?=$color[6][2]?>"><? get_select_numbers($pl_MECHTILT1_3,0,15,1,'no');?>&nbsp;
	 	 <SELECT NAME='pl_MECHTILT_DIR1_3' CLASS='<?=$color2[6][2]?>'><option SELECTED><?=$pl_MECHTILT_DIR1_3?><option>DOWNTILT<option>UPTILT</SELECT></td>
		 <? if ($CONFIG_4){ ?>
		 <td class="<?=$color[8][1]?>"><SELECT NAME="pl_MECHTILT1_4" class="<?=$color[6][2]?>"><? get_select_numbers($pl_MECHTILT1_4,0,15,1,'no');?>&nbsp;
	 	 <SELECT NAME='pl_MECHTILT_DIR1_4' CLASS='<?=$color2[8][2]?>'><option SELECTED><?=$pl_MECHTILT_DIR1_4?><option>DOWNTILT<option>UPTILT</SELECT></td>
		 <? } ?>
	  </tr>
  	  <tr>
		 <td class="parameter_name">Antenna Height 1</td>
	 	 <td class="<?=$color[4][1]?>"><SELECT NAME="pl_ANTHEIGHT1_1" class="<?=$color[4][2]?>"><? get_select_numbers($pl_ANTHEIGHT1_1,-5,200,1,'no');?>m<SELECT NAME="pl_ANTHEIGHT1_1_t" class="<?=$color[4][2]?>"><? get_select_numbers($pl_ANTHEIGHT1_1_t,0,99,1,'yes');?></td>
	 	 <td class="<?=$color[5][1]?>"><SELECT NAME="pl_ANTHEIGHT1_2" class="<?=$color[5][2]?>"><? get_select_numbers($pl_ANTHEIGHT1_2,-5,200,1,'no');?>m<SELECT NAME="pl_ANTHEIGHT1_2_t" class="<?=$color[5][2]?>"><? get_select_numbers($pl_ANTHEIGHT1_2_t,0,99,1,'yes');?></td>
	 	 <td class="<?=$color[6][1]?>"><SELECT NAME="pl_ANTHEIGHT1_3" class="<?=$color[6][2]?>"><? get_select_numbers($pl_ANTHEIGHT1_3,-5,200,1,'no');?>m<SELECT NAME="pl_ANTHEIGHT1_3_t" class="<?=$color[6][2]?>"><? get_select_numbers($pl_ANTHEIGHT1_3_t,0,99,1,'yes');?></td>
		 <? if ($CONFIG_4){ ?>
		 <td class="<?=$color[8][1]?>"><SELECT NAME="pl_ANTHEIGHT1_4" class="<?=$color[8][2]?>"><? get_select_numbers($pl_ANTHEIGHT1_4,-5,200,1,'no');?>m<SELECT NAME="pl_ANTHEIGHT1_4_t" class="<?=$color[8][2]?>"><? get_select_numbers($pl_ANTHEIGHT1_4_t,0,99,1,'yes');?></td>
		 <? } ?>
	  </tr>
	  <tr>
		 <td class="parameter_name">Antenna Type 2</td>
 	 	 <td class="<?=$color[4][1]?>"><SELECT NAME="pl_ANTTYPE2_1" class="<?=$color[4][2]?>"><? echo get_select_anttype($pl_ANTTYPE2_1);?></select></td>
	 	 <td class="<?=$color[5][1]?>"><SELECT NAME="pl_ANTTYPE2_2" class="<?=$color[5][2]?>"><? echo get_select_anttype($pl_ANTTYPE2_2);?></select></td>
	 	 <td class="<?=$color[6][1]?>"><SELECT NAME="pl_ANTTYPE2_3" class="<?=$color[6][2]?>"><? echo get_select_anttype($pl_ANTTYPE2_3); ?></select></td>
		 <? if ($CONFIG_4){ ?>
		 <td class="<?=$color[8][1]?>"><SELECT NAME="pl_ANTTYPE2_4" class="<?=$color[8][2]?>"><? echo get_select_anttype($pl_ANTTYPE2_4);?></select></td>
		 <? } ?>
	  </tr>
 <tr>
		 <td class="parameter_name">Elektrical downtilt 2</td>
	 	 <td class="<?=$color[4][1]?>"><SELECT NAME="pl_ELECTILT2_1" class="<?=$color[4][2]?>"><? get_select_numbers($pl_ELECTILT2_1,0,15,1,'no');?></td>
	 	 <td class="<?=$color[5][1]?>"><SELECT NAME="pl_ELECTILT2_2" class="<?=$color[5][2]?>"><? get_select_numbers($pl_ELECTILT2_2,0,15,1,'no');?></td>
	 	 <td class="<?=$color[6][1]?>"><SELECT NAME="pl_ELECTILT2_3" class="<?=$color[6][2]?>"><? get_select_numbers($pl_ELECTILT2_3,0,15,1,'no');?></td>
		 <? if ($CONFIG_4){ ?>
		<td class="<?=$color[8][1]?>"><SELECT NAME="pl_ELECTILT2_4" class="<?=$color[8][2]?>"><? get_select_numbers($pl_ELECTILT2_4,0,15,1,'no');?></td>
		 <? } ?>
	  </tr>
 	  <tr>
		 <td class="parameter_name">Mechanical tilt 2</td>
 	 	 <td class="<?=$color[4][1]?>"><SELECT NAME="pl_MECHTILT2_1" class="<?=$color[4][2]?>"><? get_select_numbers($pl_MECHTILT2_1,0,15,1,'no');?>&nbsp;
	 	 <SELECT NAME='pl_MECHTILT_DIR2_1' CLASS='<?=$color2[4][2]?>'><option SELECTED><?=$pl_MECHTILT_DIR2_1?><option>DOWNTILT<option>UPTILT</SELECT></td>
	 	 <td class="<?=$color[5][1]?>"><SELECT NAME="pl_MECHTILT2_2" class="<?=$color[5][2]?>"><? get_select_numbers($pl_MECHTILT2_2,0,15,1,'no');?>&nbsp;
	 	 <SELECT NAME='pl_MECHTILT_DIR2_2' CLASS='<?=$color2[5][2]?>'><option SELECTED><?=$pl_MECHTILT_DIR2_2?><option>DOWNTILT<option>UPTILT</SELECT></td>
	 	 <td class="<?=$color[6][1]?>"><SELECT NAME="pl_MECHTILT2_3" class="<?=$color[6][2]?>"><? get_select_numbers($pl_MECHTILT2_3,0,15,1,'no');?>&nbsp;
	 	 <SELECT NAME='pl_MECHTILT_DIR2_3' CLASS='<?=$color2[6][2]?>'><option SELECTED><?=$pl_MECHTILT_DIR2_3?><option>DOWNTILT<option>UPTILT</SELECT></td>
		 <? if ($CONFIG_4){ ?>
		 <td class="<?=$color[8][1]?>"><SELECT NAME="pl_MECHTILT2_4" class="<?=$color[8][2]?>"><? get_select_numbers($pl_MECHTILT2_4,0,15,1,'no');?>&nbsp;
	 	 <SELECT NAME='pl_MECHTILT_DIR2_4' CLASS='<?=$color2[8][2]?>'><option SELECTED><?=$pl_MECHTILT_DIR2_4?><option>DOWNTILT<option>UPTILT</SELECT></td>
		 <? } ?>
	  </tr>
  	  <tr>
		 <td class="parameter_name">Antenna Height 2</td>
	     <td class="<?=$color[4][1]?>"><SELECT NAME="pl_ANTHEIGHT2_1" class="<?=$color[4][2]?>"><? get_select_numbers($pl_ANTHEIGHT2_1,-5,200,1,'no');?>m<SELECT NAME="pl_ANTHEIGHT2_1_t" class="<?=$color2[4][2]?>"><? get_select_numbers($pl_ANTHEIGHT2_1_t,0,99,1,'yes');?></td>
	  	 <td class="<?=$color[5][1]?>"><SELECT NAME="pl_ANTHEIGHT2_2" class="<?=$color[5][2]?>"><? get_select_numbers($pl_ANTHEIGHT2_2,-5,200,1,'no');?>m<SELECT NAME="pl_ANTHEIGHT2_2_t" class="<?=$color2[5][2]?>"><? get_select_numbers($pl_ANTHEIGHT2_2_t,0,99,1,'yes');?></td>
	 	 <td class="<?=$color[6][1]?>"><SELECT NAME="pl_ANTHEIGHT2_3" class="<?=$color[6][2]?>"><? get_select_numbers($pl_ANTHEIGHT2_3,-5,200,1,'no');?>m<SELECT NAME="pl_ANTHEIGHT2_3_t" class="<?=$color2[6][2]?>"><? get_select_numbers($pl_ANTHEIGHT2_3_t,0,99,1,'yes');?></td>
		 <? if ($CONFIG_4){ ?>
		 <td class="<?=$color[8][1]?>"><SELECT NAME="pl_ANTHEIGHT2_4" class="<?=$color[8][2]?>"><? get_select_numbers($pl_ANTHEIGHT2_4,-5,200,1,'no');?>m<SELECT NAME="pl_ANTHEIGHT2_4_t" class="<?=$color2[8][2]?>"><? get_select_numbers($pl_ANTHEIGHT2_4_t,0,99,1,'yes');?></td>
		 <? } ?>
	  </tr>
  	  <tr>
		 <td class="parameter_name">Azimuth</td>
 	 	 <td class="<?=$color[4][1]?>"><SELECT NAME="pl_AZI_1" class="<?=$color[4][2]?>"><? get_select_azi($pl_AZI_1);?></td>
	 	 <td class="<?=$color[5][1]?>"><SELECT NAME="pl_AZI_2" class="<?=$color[5][2]?>"><? get_select_azi($pl_AZI_2);?></td>
	 	 <td class="<?=$color[6][1]?>"><SELECT NAME="pl_AZI_3" class="<?=$color[6][2]?>"><? get_select_azi($pl_AZI_3);?></td>
		 <? if ($CONFIG_4){ ?>
		 <td class="<?=$color[8][1]?>"><SELECT NAME="pl_AZI_4" class="<?=$color[8][2]?>"><? get_select_azi($pl_AZI_4);?></td>
		 <? } ?>
	  </tr> 	  <tr>
		 <td class="parameter_name">Feeder type <?=$updatable?></td>
 	 	 <td class="<?=$color[4][1]?>">
         	<SELECT NAME="pl_FEEDER_1" class="<?=$color[4][2]?>">
				<?
					get_select_feeder($pl_FEEDER_1);
				?>
         </td>
	 	 <td class="<?=$color[5][1]?>">
         	<SELECT NAME="pl_FEEDER_2" class="<?=$color[5][2]?>">
				<?
					get_select_feeder($pl_FEEDER_2);
				?>
         </td>
	 	 <td class="<?=$color[6][1]?>">
         	<SELECT NAME="pl_FEEDER_3" class="<?=$color[6][2]?>">
				<?
					get_select_feeder($pl_FEEDER_3);
				?>
         </td>
		 <? if ($CONFIG_4){ ?>
		 <td class="<?=$color[8][1]?>">
         	<SELECT NAME="pl_FEEDER_4" class="<?=$color[8][2]?>">
				<?
					get_select_feeder($pl_FEEDER_4);
				?>
         </td>
		 <? } ?>
	  </tr>
  	  <tr>
		 <td class="parameter_name">Feeder length <?=$updatable?></td>
 	 	 <td class="<?=$color[4][1]?>"><SELECT NAME="pl_FEEDERLEN_1" class="<?=$color[4][2]?>"><? get_select_numbers($pl_FEEDERLEN_1,0,200,1,'no');?>m<SELECT NAME="pl_FEEDERLEN_1_t" class="<?=$color2[4][2]?>"><? get_select_numbers($pl_FEEDERLEN_1_t,0,99,5,'yes');?></td>
	 	 <td class="<?=$color[5][1]?>"><SELECT NAME="pl_FEEDERLEN_2" class="<?=$color[5][2]?>"><? get_select_numbers($pl_FEEDERLEN_2,0,200,1,'no');?>m<SELECT NAME="pl_FEEDERLEN_2_t" class="<?=$color2[5][2]?>"><? get_select_numbers($pl_FEEDERLEN_2_t,0,99,5,'yes');?></td>
	 	 <td class="<?=$color[6][1]?>"><SELECT NAME="pl_FEEDERLEN_3" class="<?=$color[6][2]?>"><? get_select_numbers($pl_FEEDERLEN_3,0,200,1,'no');?>m<SELECT NAME="pl_FEEDERLEN_3_t" class="<?=$color2[6][2]?>"><? get_select_numbers($pl_FEEDERLEN_3_t,0,99,5,'yes');?></td>
		 <? if ($CONFIG_4){ ?>
		 <td class="<?=$color[8][1]?>"><SELECT NAME="pl_FEEDERLEN_4" class="<?=$color[8][2]?>"><? get_select_numbers($pl_FEEDERLEN_4,0,200,1,'no');?>m<SELECT NAME="pl_FEEDERLEN_4_t" class="<?=$color2[8][2]?>"><? get_select_numbers($pl_FEEDERLEN_4_t,0,99,5,'yes');?></td>
		 <? } ?>
	</tr>
	<tr>
		 <td class="parameter_name"><font color="blue">Feeder sharing</font></td>
	 	 <td class="<?=$color[4][1]?>"><SELECT NAME="pl_FEEDERSHARE_1" class="<?=$color[4][2]?>"><? get_select_feedershare($pl_FEEDERSHARE_1);?></SELECT></td>
		 <td class="<?=$color[5][1]?>"><SELECT NAME="pl_FEEDERSHARE_2" class="<?=$color[5][2]?>"><? get_select_feedershare($pl_FEEDERSHARE_2);?></SELECT></td>
		 <td class="<?=$color[6][1]?>"><SELECT NAME="pl_FEEDERSHARE_3" class="<?=$color[6][2]?>"><? get_select_feedershare($pl_FEEDERSHARE_3);?></SELECT></td>
		 <? if ($CONFIG_4){ ?>
		 <td class="<?=$color[8][1]?>"><SELECT NAME="pl_FEEDERSHARE_4" class="<?=$color[8][2]?>"><? get_select_feedershare($pl_FEEDERSHARE_4);?></SELECT></td>
		 <? } ?>
	</tr>
	<tr>
		 <td class="parameter_name"><font color="blue">DC block</font></td>
	 	 <td class="<?=$color[4][1]?>"><SELECT NAME="pl_DCBLOCK_1" class="<?=$color[4][2]?>"><option SELECTED><?=$pl_DCBLOCK_1?><option>YES<option>NO</SELECT></td>
	 	 <td class="<?=$color[5][1]?>"><SELECT NAME="pl_DCBLOCK_2" class="<?=$color[5][2]?>"><option SELECTED><?=$pl_DCBLOCK_2?><option>YES<option>NO</SELECT></td>
	 	 <td class="<?=$color[6][1]?>"><SELECT NAME="pl_DCBLOCK_3" class="<?=$color[6][2]?>"><option SELECTED><?=$pl_DCBLOCK_3?><option>YES<option>NO</SELECT></td>
		 <? if ($CONFIG_4){ ?>
		 <td class="<?=$color[8][1]?>"><SELECT NAME="pl_DCBLOCK_4" class="<?=$color[8][2]?>"><option SELECTED><?=$pl_DCBLOCK_4?><option>YES<option>NO</SELECT></td>
		 <? } ?>
	 </tr>
	<tr>
		 <td class="parameter_name">HR active upon integration</td>
	 	 <td class="<?=$color[4][1]?>"><SELECT NAME="pl_HRACTIVE_1" class="<?=$color[4][2]?>"><? get_select_HRACTIVE($pl_HRACTIVE_1);?></SELECT></td>
	 	 <td class="<?=$color[5][1]?>"><SELECT NAME="pl_HRACTIVE_2" class="<?=$color[5][2]?>"><? get_select_HRACTIVE($pl_HRACTIVE_2);?></SELECT></td>
	 	 <td class="<?=$color[6][1]?>"><SELECT NAME="pl_HRACTIVE_3" class="<?=$color[6][2]?>"><? get_select_HRACTIVE($pl_HRACTIVE_3);?></SELECT></td>
		 <? if ($CONFIG_4){ ?>
		 <td class="<?=$color[8][1]?>"><SELECT NAME="pl_HRACTIVE_4" class="<?=$color[8][2]?>"><? get_select_HRACTIVE($pl_HRACTIVE_4);?></SELECT></td>
		 <? } ?>
	 </tr>
	 </table>
	</td>
</tr>
</table>
<input type="submit">
</form>
                <h1>Output Div (#output1):</h1>
                <div id="output1">AJAX response will replace this content.</div>
            </div>
</body>
</html>