<?
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['phpguarddog_path']."/guard.php");
protect("","Radioplanners,BASE_MP,BASE_NPF,BSDS_view","");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");


$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);


function generatebutton($siteupgnr,$checktype,$title,$status,$filedate,$filefullpath,$filename,$ran,$reason,$rafid){
	global $config,$guard_groups;
	
	$out="<tr>
		<td>".$title."</td>";
		if ($status==1){
		$out.="<td class='success' id='fileVal_".$siteupgnr.$checktype."'>OK ".$ran."</td>";
		}else if ($status==7){
		$out.="<td class='success' id='fileVal_".$siteupgn.$checktype."'><a class='tippy' title='".$reason."'>OVERRULED</a></td>";
		}else if ($status==5){
		$out.="<td class='success' id='fileVal_".$siteupgnr.$checktype."'>".$filename."</td>";
		}else if ($status==6){
		$out.="<td class='danger' id='fileVal_".$siteupgnr.$checktype."'>".$filename."</td>";
		}else if ($status==4){
		$out.="<td class='warning' id='fileVal_".$siteupgn.$checktype."'>WRONG DATE FORMAT:<br>".$filedate."</td>";
		}else if ($status==2){
		$out.="<td class='warning' id='fileVal_".$siteupgnr.$checktype."'>OLD FILE (FUNDING DATE)!</td>";
		}else if ($status==3){
		$out.="<td class='warning' id='fileVal_".$siteupgnr.$checktype."'>WRONG FILENAME! MISSING PART!</td>";
		}else{
		$out.="<td class='danger' id='fileVal_".$siteupgnr.$checktype."'>NOT FOUND</td>";
		}
		$out.="<td>";
		
		if ($status==1){ 
			if ($ran=='BENCHMARK_RAN'){
			  $ranloc=$config['ranfolderBENCH'];
			}else if ($ran=='RAN_ARCHIVE'){
			  $ranloc=$config['ranfolderARCHIVE'];
			}else if ($ran=='RAN-ALU'){
			  $ranloc=$config['ranfolder'];
			}
			
			$dir=rawurlencode(str_replace($ranloc,'', dirname($filefullpath)));
			//echo $dir."<br>";
			$ranurl=$config['sitepath_url'].'/bsds/scripts/liveranbrowser/liveranbrowser.php?dir='.$dir."&ran=".$ran;
			$out.="<div class='btn-toolbar' role='toolbar'>
	          <div class='btn-group'>
	          	<a class='btn btn-default btn-xs filedownload tippy' target='_new' title='Download file ".$filename." href='scripts/filebrowser/filedownload.php?file=".urlencode($filefullpath)."&name=".$filename."'><span class='glyphicon glyphicon-download'></span></a>
	           	<a class='btn btn-default btn-xs liveran tippy' title='Open containing folder' target='_blank' style='target-new: tab;' id='filefolder' data-ranurl='".$ranurl."'><span class='glyphicon glyphicon-circle-arrow-right'></span></a>";
	         
	  	}else if ($status!=7){
	  			if (substr_count($guard_groups, 'Administrators')==1 or substr_count($guard_groups, 'Base_delivery')==1 ){
	           	$out.="<a class='btn btn-default btn-xs overruleVali tippy'  data-type='FILE' data-checktype='".$checktype."' id='".$siteupgnr.$checktype."' data-siteupgnr='".$siteupgnr."' data-rafid='".$rafid."' title='Overrule ".$title."' target='_blank' style='target-new: tab;'><span class='glyphicon glyphicon-ok'></span></a>";
	         	} 
	          } 
	           $out.="</div>
		    </div>
	  	</td>
	</tr>";

	return $out;
}

function generateMilestone2($siteupgnr,$checktype,$MS,$descr,$date,$reason,$rafid){
	global $guard_groups;
	$out="<tr>
		<td>".$MS."</td>
		<td>".$descr."</td>";
		if ($reason!=''){ 
		$out.="<td class='success'><a class='tippy' title='".$reason."'>OVERRULED</a></td>";
		}else if ($date!=''){ 
		$out.="<td class='success'>".$date."</td>";
		}else{
		$out.="<td class='danger' id='fileVal_".$siteupgnr.$checktype."'>NOT OK</td>";
		}
	$out.="<td><div class='btn-toolbar' role='toolbar'>
	          <div class='btn-group'>";
		if ((($reason=='' && $date=='') or ($reason!='' && $date!='')) && (substr_count($guard_groups, 'Administrators')==1 or substr_count($guard_groups, 'Base_delivery')==1)){
       	$out.="<a class='btn btn-default btn-xs overruleVali tippy' data-type='MS' data-checktype='".$checktype."' id='".$siteupgnr.$MS."' data-siteupgnr='".$siteupgnr."' data-rafid='".$rafid."' title='Overrule ".$siteupgnr.": ".$descr."' target='_blank' style='target-new: tab;'><span class='glyphicon glyphicon-ok'></span></a>";
     	} 
	$out.="</div>
		    </div>
	  	</td>
	</tr>";
	return $out;
}
