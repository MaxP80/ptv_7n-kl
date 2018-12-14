<?php
	include("DataBase.php");
	if (isset($_POST['id_groupes']) && isset($_POST['subject']) && isset($_POST['msg'])){
		echo ("Tout va bien jusque là");
		$idGroupes = decodeIdGroupes($_POST['id_groupes']);
		$trameRequestAdh = 'SELECT user_id, profil_id from membres_groupe WHERE id_groupe IN "'.$idGroupes.'"';
		echo ($trameRequestAdh);
	}

	function decodeIdGroupes ($string){
		$arrayID = explode("||", $string);
		$res = '()';
		for ($i = 0; $i<sizeof($arrayID); $i++){
			if ($i==0){
				echo 'DEB';
				$res = "(";
			}
			$res += $arrayID[$i];
			if ($i == sizeof($arrayID)-1){
				echo "FIN";
				$res+=")";
			} else {
				echo "Mil";
				$res +=",";
			}
		}
		echo $res;
		return $res;
	}
?>