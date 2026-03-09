<?php
fwrite($file, "<?php\n");
fwrite($file, "\$ldap_base = \"$ou\";\n");
fwrite($file, "\$ldap_filter = \"$filter\";\n");
fwrite($file, "\$ldap_attributes_mapping = array(\n");
$first = true;
foreach($datas['attributes'] as $attribute => $infos){
if(($infos['type'] == "value") || ($infos['type'] == "refValue")){
if(! $first){
fwrite($file, ",\n");
}
if($infos['multi']){
fwrite($file, "	\"".$infos['attribute_pf_name']."\" => \"".$infos['ldap_attribute']."\"");
}
else{
fwrite($file, "	\"$attribute\" => \"".$infos['ldap_attribute']."\"");
}
$first = false;
}
}
fwrite($file, ");\n");
fwrite($file, "?>");
?>