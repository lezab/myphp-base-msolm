##<?php
##\$ldap_base = \"$ou\";
##\$ldap_filter = \"$filter\";
##\$ldap_attributes_mapping = array(
$first = true;
foreach($datas['attributes'] as $attribute => $infos){
	if(($infos['type'] == "value") || ($infos['type'] == "refValue")){
		if(! $first){
			##,
		}
		if($infos['multi']){
			#	\"".$infos['attribute_pf_name']."\" => \"".$infos['ldap_attribute']."\"
		}
		else{
			#	\"$attribute\" => \"".$infos['ldap_attribute']."\"
		}
		$first = false;
	}
}
##);
#?>