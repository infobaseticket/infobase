<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['phpguarddog_path']."/guard.php");
protect("","Radioplanners,BASE_MP,BASE_NPF,BSDS_viewtype","");

if($check_current_exists!=0){
	if ($check_planned_exists!="0"){  			//IF PLANNED DATA HAS ALREADY BEEN SAVED
		// GET DATA sec1
		$planneddata=get_data($_POST['band'],"","PLANNED",$viewtype,$_POST['bsdskey'],$_POST['bsdsbobrefresh'],$_POST['donor'],$lognode);
			$pl_OWNER=$planneddata['OWNER'][0];
			$pl_BRAND=$planneddata['BRAND'][0];
			$pl_RTYPE=$planneddata['TYPE'][0];
			$pl_TECHNOLOGY=$planneddata['TECHNOLOGY'][0];
			$pl_CHANNEL=$planneddata['CHANNEL'][0];
			$pl_PICKUP=$planneddata['PICKUP'][0];
			$pl_DISTRIB=$planneddata['DISTRIB'][0];
			$pl_COSP=$planneddata['COSP'][0];
			$pl_COMMENTS=$planneddata['COMMENTS'][0];

	} // END if ($check_planned_exists!="0"){
	else    //IF NO PLANNED DATA HAS BEEN SAVED YET, COPY CURRENT TO PLANNED
	{
		$pl_OWNER=$OWNER;
		$pl_BRAND=$BRAND;
		$pl_RTYPE=$RTYPE;
		$pl_TECHNOLOGY=$TECHNOLOGY;
		$pl_CHANNEL=$CHANNEL;
		$pl_PICKUP=$PICKUP;
		$pl_DISTRIB=$DISTRIB;
		$pl_COSP=$COSP;
		$pl_COMMENTS=$COMMENTS;

	}
}

?>
