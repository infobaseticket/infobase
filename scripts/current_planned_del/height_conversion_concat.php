<?
//echo $_POST['pl_ANTHEIGHT1_'.$n.'_t']."<br>";
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