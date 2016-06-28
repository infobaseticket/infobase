<?php
//echo $_POST['pl_ANTHEIGHT1_'.$n.'_t']."<br>";
if ($_POST['ANTHEIGHT1_'.$n]!="" && $_POST['ANTHEIGHT1_'.$n]!="-"){
		if ($_POST['ANTHEIGHT1_'.$n.'_t']==""){
			$_POST['ANTHEIGHT1_'.$n.'_t']="0";
		}
		$_POST['ANTHEIGHT1_'.$n]=$_POST['ANTHEIGHT1_'.$n].".".$_POST['ANTHEIGHT1_'.$n.'_t'];
}
if ($_POST['ANTHEIGHT2_'.$n]!="" && $_POST['ANTHEIGHT2_'.$n]!="-"){
		if ($_POST['ANTHEIGHT2_'.$n.'_t']==""){
			$_POST['ANTHEIGHT2_'.$n.'_t']="0";
		}
		$_POST['ANTHEIGHT2_'.$n]=$_POST['ANTHEIGHT2_'.$n].".".$_POST['ANTHEIGHT2_'.$n.'_t'];
}
if ($_POST['FEEDERLEN_'.$n]!="" && $_POST['FEEDERLEN_'.$n]!="-"){
		if ($_POST['FEEDERLEN_'.$n.'_t']==""){
			$_POST['FEEDERLEN_'.$n.'_t']="0";
		}
		$_POST['FEEDERLEN_'.$n]=$_POST['FEEDERLEN_'.$n].".".$_POST['FEEDERLEN_'.$n.'_t'];
}

if ($_POST['pl_ANTHEIGHT1_'.$n]!="" && $_POST['pl_ANTHEIGHT1_'.$n]!="-"){
		if ($_POST['pl_ANTHEIGHT1_'.$n.'_t']==""){
			$_POST['pl_ANTHEIGHT1_'.$n.'_t']="0";
		}
		$_POST['pl_ANTHEIGHT1_'.$n]=$_POST['pl_ANTHEIGHT1_'.$n].".".$_POST['pl_ANTHEIGHT1_'.$n.'_t'];
}
if ($_POST['pl_ANTHEIGHT2_'.$n]!="" && $_POST['pl_ANTHEIGHT2_'.$n]!="-"){
		if ($_POST['pl_ANTHEIGHT2_'.$n.'_t']==""){
			$_POST['pl_ANTHEIGHT2_'.$n.'_t']="0";
		}
		$_POST['pl_ANTHEIGHT2_'.$n]=$_POST['pl_ANTHEIGHT2_'.$n].".".$_POST['pl_ANTHEIGHT2_'.$n.'_t'];
}
if ($_POST['pl_FEEDERLEN_'.$n]!="" && $_POST['pl_FEEDERLEN_'.$n]!="-"){
		if ($_POST['pl_FEEDERLEN_'.$n.'_t']==""){
			$_POST['pl_FEEDERLEN_'.$n.'_t']="0";
		}
		$_POST['pl_FEEDERLEN_'.$n]=$_POST['pl_FEEDERLEN_'.$n].".".$_POST['pl_FEEDERLEN_'.$n.'_t'];
}

?>