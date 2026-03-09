<?php
fwrite($file, "<?php\n");
fwrite($file, "/**\n");
fwrite($file, " * File generated with MySimpleOLM v$msolm_version\n");
fwrite($file, " * \n");
fwrite($file, " * You should not modify this file.\n");
fwrite($file, " * If you need to add or modify some functionalities offered by this file,\n");
fwrite($file, " * you should see and modify the child class corresponding to this file.\n");
fwrite($file, " */\n");
fwrite($file, "\n");
fwrite($file, "namespace $nsp"."core;\n");
fwrite($file, "\n");
fwrite($file, "use \\$nsp"."exceptions\\$classname"."Exception;\n");
fwrite($file, "\n");
fwrite($file, "/**\n");
fwrite($file, " * @class $core_classname.\n");
fwrite($file, " * Like all other Core classes, the class is the base class for objects instances in ldap directory.\n");
fwrite($file, " * You should not use this class directly but the subclass $classname which inherits all the methods of this class.\n");
fwrite($file, " * Ex :\n");
fwrite($file, " * \$manager = $manager_classname::getInstance();\n");
fwrite($file, " * \$object = new $classname();\n");
fwrite($file, " * ... // set object properties\n");
fwrite($file, " * \$manager->add(\$object);\n");
fwrite($file, " */\n");
fwrite($file, "class $core_classname {\n");
fwrite($file, "\n");
fwrite($file, "	// Class attributes for object management\n");
fwrite($file, "	protected \$_new = true;\n");
fwrite($file, "	protected \$_modified = false;\n");
fwrite($file, "	protected \$_deleted = false;\n");
fwrite($file, "	protected \$_renamed = false;\n");
fwrite($file, "\n");
fwrite($file, "	// Attributes relatives to ldap entry\n");
fwrite($file, "	protected \$dn = null;\n");
foreach($datas['attributes'] as $attribute => $infos){
if(($infos['type'] == 'value') || ($infos['type'] == 'refValue')){
if($infos['multi']){
if($infos['datatype']['type'] == 'constant'){
fwrite($file, "	protected \$".$infos['attribute_pf_name']." = array(");
$first = true;
foreach(explode('/',$infos['datatype']['values']) as $value){
if(! $first){
fwrite($file, ", ");
}
fwrite($file, "\"$value\"");
$first = false;
}
fwrite($file, ");\n");
}
else{
fwrite($file, "	protected \$".$infos['attribute_pf_name']." = array();\n");
}
}
else{
if($infos['datatype']['type'] == 'constant'){
fwrite($file, "	protected \$".$attribute." = \"".$infos['datatype']['value']."\";				\n");
}
else{
fwrite($file, "	protected \$".$attribute." = null;\n");
}
}
}
}
fwrite($file, "	protected static \$attributesList = array(");
$first = true;
foreach($datas['attributes'] as $attribute => $infos){
if(($infos['type'] == "value") || ($infos['type'] == "refValue")){
if(! $first){
fwrite($file, ", ");
}
if($infos['multi']){
fwrite($file, "\"".$infos['attribute_pf_name']."\"");
}
else{
fwrite($file, "\"$attribute\"");
}
$first = false;
}
}
fwrite($file, ");\n");
fwrite($file, "\n");
$rdn = $datas['rdn'];
$rdn_infos = $datas['attributes'][$rdn];
fwrite($file, "	protected \$_originalDn = null;\n");
fwrite($file, "\n");
fwrite($file, "\n");
fwrite($file, "	/**\n");
fwrite($file, "	 * Constructor\n");
fwrite($file, "	 * @params \$datas array : should not be used. Only object manager can send \$datas to the constructor \n");
fwrite($file, "	 * to initialize the object. If you want initialize object avoiding to use setters, you have to instanciate\n");
fwrite($file, "	 * an empty object then use update method on it.\n");
fwrite($file, "	 */\n");
fwrite($file, "	public function __construct(array \$datas = null){			\n");
fwrite($file, "		if(! empty(\$datas)){\n");
fwrite($file, "			\$trace = defined('DEBUG_BACKTRACE_IGNORE_ARGS') ? debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3) : debug_backtrace();\n");
fwrite($file, "			if((isset(\$trace[1]['class']) && isset(\$trace[2]['class']) && (\$trace[1]['class'] == '$nsp$classname') && (\$trace[2]['class'] == '$nsp$manager_classname' || \$trace[2]['class'] == '$namespace\core\\$manager_core_classname'))\n");
fwrite($file, "				|| (isset(\$trace[1]['class']) && (\$trace[1]['class'] == '$nsp$manager_classname' || \$trace[1]['class'] == '$namespace\core\\$manager_core_classname'))) {\n");
fwrite($file, "				foreach (\$datas as \$key => \$value) {\n");
fwrite($file, "					\$this->\$key = \$value;\n");
fwrite($file, "				}\n");
fwrite($file, "				\$this->_new = false;\n");
fwrite($file, "			}\n");
fwrite($file, "			else{\n");
fwrite($file, "				\$this->setDatas(\$datas);\n");
fwrite($file, "			}\n");
fwrite($file, "		}\n");
fwrite($file, "	}\n");
fwrite($file, "\n");
fwrite($file, "	/**\n");
fwrite($file, "	 * Set datas to the object according datas provided.\n");
fwrite($file, "	 * @params \$datas array : a set of key,value to initialize the object. Can be used on a new object to initialize it avoiding to use setters,\n");
fwrite($file, "	 * when a lot of attributes.\n");
fwrite($file, "	 */\n");
fwrite($file, "	public function setDatas(array \$datas){\n");
fwrite($file, "		foreach (\$datas as \$key => \$value) {\n");
fwrite($file, "			if(in_array(\$key, self::\$attributesList)){\n");
fwrite($file, "				\$method = \"set\".ucfirst(\$key);\n");
fwrite($file, "				\$this->\$method(\$value);\n");
fwrite($file, "			}\n");
fwrite($file, "			elseif(\$key == 'dn'){\n");
fwrite($file, "				\$this->setDn(\$value);\n");
fwrite($file, "			}\n");
fwrite($file, "		}\n");
fwrite($file, "	}\n");
fwrite($file, "\n");
fwrite($file, "	/**\n");
fwrite($file, "	 * @return array all object's attributes in a set of key,value (faster then call any getter)\n");
fwrite($file, "	 */\n");
fwrite($file, "	public function getDatas(){\n");
fwrite($file, "		\$datas = array();\n");
fwrite($file, "		foreach (self::\$attributesList as \$attribute) {\n");
fwrite($file, "			\$datas[\$attribute] = \$this->\$attribute;\n");
fwrite($file, "		}\n");
fwrite($file, "		return \$datas;\n");
fwrite($file, "	}\n");
fwrite($file, "\n");
fwrite($file, "	/**\n");
fwrite($file, "	 * Tells if the object is a new one or already exists in ldap directory.\n");
fwrite($file, "	 * This method is basically used by manager. You may not have to use it.\n");
fwrite($file, "	 * @return boolean true if the object is a new one, false otherwise.\n");
fwrite($file, "	 */\n");
fwrite($file, "	final public function _isNew(){\n");
fwrite($file, "		return \$this->_new;\n");
fwrite($file, "	}\n");
fwrite($file, "\n");
fwrite($file, "	/**\n");
fwrite($file, "	 * Due to limitations of php the method is public but can only be used from $nsp$classname class itself or corresponding manager class\n");
fwrite($file, "	 * You should not use this method.\n");
fwrite($file, "	 * @param \$var boolean\n");
fwrite($file, "	 */\n");
fwrite($file, "	final public function _setNew(\$var){\n");
fwrite($file, "		//\$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);\n");
fwrite($file, "		\$trace = defined('DEBUG_BACKTRACE_IGNORE_ARGS') ? debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2) : debug_backtrace();\n");
fwrite($file, "		if (! (\$trace[1]['class'] == '$nsp$classname' || \$trace[1]['class'] == '$nsp$manager_classname' || \$trace[1]['class'] == '$namespace\core\\$manager_core_classname')) {\n");
fwrite($file, "			\$message  = \"Due to limitations of php the method is public but can only be used from $nsp$manager_classname class\\n\";\n");
fwrite($file, "			\$message .= \"You should not use this method.\";\n");
fwrite($file, "			throw new ".$classname."Exception(\$message);\n");
fwrite($file, "		}\n");
fwrite($file, "		\$this->_new = \$var;\n");
fwrite($file, "	}\n");
fwrite($file, "\n");
fwrite($file, "	/**\n");
fwrite($file, "	 * Tells if the object has been modified or not.\n");
fwrite($file, "	 * This method is basically used by manager. You may not have to use it.\n");
fwrite($file, "	 * @return boolean true if the object has been modified, false otherwise.\n");
fwrite($file, "	 */\n");
fwrite($file, "	final public function _isModified(){\n");
fwrite($file, "		return \$this->_modified;\n");
fwrite($file, "	}\n");
fwrite($file, "\n");
fwrite($file, "	/**\n");
fwrite($file, "	 * Due to limitations of php the method is public but can only be used from $nsp$classname class itself or corresponding manager class\n");
fwrite($file, "	 * You should not use this method.\n");
fwrite($file, "	 * @param \$var boolean\n");
fwrite($file, "	 */\n");
fwrite($file, "	final public function _setModified(\$var){\n");
fwrite($file, "		//\$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);\n");
fwrite($file, "		\$trace = defined('DEBUG_BACKTRACE_IGNORE_ARGS') ? debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2) : debug_backtrace();\n");
fwrite($file, "		if (! (\$trace[1]['class'] == '$nsp$classname' || \$trace[1]['class'] == '$nsp$manager_classname' || \$trace[1]['class'] == '$namespace\core\\$manager_core_classname')) {\n");
fwrite($file, "			\$message  = \"Due to limitations of php the method is public but can only be used from $nsp$manager_classname class\\n\";\n");
fwrite($file, "			\$message .= \"You should not use this method.\";\n");
fwrite($file, "			throw new ".$classname."Exception(\$message);\n");
fwrite($file, "		}\n");
fwrite($file, "		\$this->_modified = \$var;\n");
fwrite($file, "	}\n");
fwrite($file, "\n");
fwrite($file, "	/** Tells if the object has been deleted from  ldap directory or not.\n");
fwrite($file, "	 * This method is basically used by manager. You may not have to use it.\n");
fwrite($file, "	 * @return boolean true if the object has been deleted, false otherwise.\n");
fwrite($file, "	 */\n");
fwrite($file, "	final public function _isDeleted(){\n");
fwrite($file, "		return \$this->_deleted;\n");
fwrite($file, "	}\n");
fwrite($file, "\n");
fwrite($file, "	/**\n");
fwrite($file, "	 * Due to limitations of php the method is public but can only be used from $nsp$classname class itself or corresponding manager class\n");
fwrite($file, "	 * You should not use this method.\n");
fwrite($file, "	 * @param \$var boolean\n");
fwrite($file, "	 */\n");
fwrite($file, "	final public function _setDeleted(\$var){\n");
fwrite($file, "		//\$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);\n");
fwrite($file, "		\$trace = defined('DEBUG_BACKTRACE_IGNORE_ARGS') ? debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2) : debug_backtrace();\n");
fwrite($file, "		if (! (\$trace[1]['class'] == '$nsp$classname' || \$trace[1]['class'] == '$nsp$manager_classname' || \$trace[1]['class'] == '$namespace\core\\$manager_core_classname')) {\n");
fwrite($file, "			\$message  = \"Due to limitations of php the method is public but can only be used from $nsp$manager_classname class\\n\";\n");
fwrite($file, "			\$message .= \"You should not use this method.\";\n");
fwrite($file, "			throw new ".$classname."Exception(\$message);\n");
fwrite($file, "		}\n");
fwrite($file, "		\$this->_deleted = \$var;\n");
fwrite($file, "	}\n");
fwrite($file, "\n");
fwrite($file, "	/** Tells if the object has been renamed or not.\n");
fwrite($file, "	 * This method is basically used by manager. You may not have to use it.\n");
fwrite($file, "	 * @return boolean true if the object has been renamed, false otherwise.\n");
fwrite($file, "	 */\n");
fwrite($file, "	final public function _isRenamed(){\n");
fwrite($file, "		return \$this->_renamed;\n");
fwrite($file, "	}\n");
fwrite($file, "\n");
fwrite($file, "	/**\n");
fwrite($file, "	 * Due to limitations of php the method is public but can only be used from $nsp$classname class itself or corresponding manager class\n");
fwrite($file, "	 * You should not use this method.\n");
fwrite($file, "	 * @param \$var boolean\n");
fwrite($file, "	 */\n");
fwrite($file, "	final public function _setRenamed(\$var){\n");
fwrite($file, "		//\$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);\n");
fwrite($file, "		\$trace = defined('DEBUG_BACKTRACE_IGNORE_ARGS') ? debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2) : debug_backtrace();\n");
fwrite($file, "		if (! (\$trace[1]['class'] == '$nsp$classname' || \$trace[1]['class'] == '$nsp$manager_classname' || \$trace[1]['class'] == '$namespace\core\\$manager_core_classname')) {\n");
fwrite($file, "			\$message  = \"Due to limitations of php the method is public but can only be used from $nsp$manager_classname class\\n\";\n");
fwrite($file, "			\$message .= \"You should not use this method.\";\n");
fwrite($file, "			throw new ".$classname."Exception(\$message);\n");
fwrite($file, "		}\n");
fwrite($file, "		\$this->_renamed = \$var;\n");
fwrite($file, "	}\n");
fwrite($file, "\n");
fwrite($file, "	//---------------------------------------------------\n");
fwrite($file, "	// Getters and setters\n");
fwrite($file, "	//---------------------------------------------------\n");
fwrite($file, "	public function getDn(){\n");
fwrite($file, "		return \$this->dn;\n");
fwrite($file, "	}\n");
fwrite($file, "\n");
fwrite($file, "	public function getOriginalDn(){\n");
fwrite($file, "		return \$this->_originalDn;\n");
fwrite($file, "	}\n");
fwrite($file, "\n");
fwrite($file, "	public function setDn(\$value){\n");
fwrite($file, "		if(\$value != \$this->dn){\n");
fwrite($file, "			if(! \$this->_new){\n");
fwrite($file, "				\$this->originalDn = \$this->dn;\n");
fwrite($file, "				\$this->dn = \$value;\n");
fwrite($file, "				\$newRdn = substr(\$value, strpos(\$value, '=')+1, strpos(\$value, ','));\n");
fwrite($file, "				if(\$newRdn !== \$this->$rdn){\n");
fwrite($file, "					\$this->$rdn = \$newRdn;\n");
fwrite($file, "				}\n");
fwrite($file, "				\$this->_modified = true;\n");
fwrite($file, "				\$this->_renamed = true;\n");
fwrite($file, "			}\n");
fwrite($file, "			else{\n");
fwrite($file, "				\$this->dn = \$value;\n");
fwrite($file, "				\$newRdn = substr(\$value, strpos(\$value, '=')+1, strpos(\$value, ','));\n");
fwrite($file, "				if(\$newRdn !== \$this->$rdn){\n");
fwrite($file, "					\$this->$rdn = \$newRdn;\n");
fwrite($file, "				}\n");
fwrite($file, "			}\n");
fwrite($file, "		}\n");
fwrite($file, "	}\n");
fwrite($file, "\n");
foreach($datas['attributes'] as $attribute => $infos){
if($infos['type'] == 'value'){
if($infos['multi']){
fwrite($file, "	public function get".$this->camelize($infos['attribute_pf_name'])."(){\n");
fwrite($file, "		return \$this->".$infos['attribute_pf_name'].";\n");
fwrite($file, "	}\n");
fwrite($file, "\n");
if($infos['datatype']['type'] != 'constant'){
fwrite($file, "	public function set".$this->camelize($infos['attribute_pf_name'])."(\$values){\n");
fwrite($file, "		if(is_array(\$values)){\n");
fwrite($file, "			\$shouldReplace = false;\n");
fwrite($file, "			foreach(\$values as \$v){\n");
fwrite($file, "				if(! in_array(\$v, \$this->".$infos['attribute_pf_name'].")){\n");
fwrite($file, "					\$shouldReplace = true;\n");
fwrite($file, "					break;\n");
fwrite($file, "				}\n");
fwrite($file, "			}\n");
fwrite($file, "			if(! \$shouldReplace){\n");
fwrite($file, "				foreach(\$this->".$infos['attribute_pf_name']." as \$v){\n");
fwrite($file, "					if(! in_array(\$v, \$values)){\n");
fwrite($file, "						\$shouldReplace = true;\n");
fwrite($file, "						break;\n");
fwrite($file, "					}\n");
fwrite($file, "				}\n");
fwrite($file, "			}\n");
fwrite($file, "			if(\$shouldReplace){\n");
fwrite($file, "				\$this->".$infos['attribute_pf_name']." = \$values;\n");
fwrite($file, "				\$this->_modified = true;\n");
fwrite($file, "			}\n");
fwrite($file, "		}\n");
fwrite($file, "		else{\n");
fwrite($file, "			if(! ((count(\$this->".$infos['attribute_pf_name'].") == 1) && (\$this->".$infos['attribute_pf_name']."[0] === \$values))){\n");
fwrite($file, "				\$this->".$infos['attribute_pf_name']." = array(\$values);\n");
fwrite($file, "				\$this->_modified = true;\n");
fwrite($file, "			}\n");
fwrite($file, "		}\n");
fwrite($file, "	}\n");
fwrite($file, "\n");
fwrite($file, "	public function add".$this->camelize($attribute)."(\$value){\n");
fwrite($file, "		if(! in_array(\$value, \$this->".$infos['attribute_pf_name'].")){\n");
fwrite($file, "			\$this->".$infos['attribute_pf_name']."[] = \$value;\n");
fwrite($file, "			\$this->_modified = true;\n");
fwrite($file, "		}\n");
fwrite($file, "	}\n");
fwrite($file, "\n");
fwrite($file, "	public function delete".$this->camelize($attribute)."(\$value){\n");
fwrite($file, "		if((\$index = array_search(\$value, \$this->".$infos['attribute_pf_name'].")) !== false){\n");
fwrite($file, "			array_splice(\$this->".$infos['attribute_pf_name'].", \$index, 1);\n");
fwrite($file, "			\$this->_modified = true;\n");
fwrite($file, "		}\n");
fwrite($file, "	}\n");
fwrite($file, "\n");
}
}
else{
fwrite($file, "	public function get".$this->camelize($attribute)."(){\n");
fwrite($file, "		return \$this->$attribute;\n");
fwrite($file, "	}\n");
fwrite($file, "\n");
if($attribute == $rdn){
fwrite($file, "	public function set".$this->camelize($attribute)."(\$value){\n");
fwrite($file, "		if(\$value != \$this->$attribute){\n");
fwrite($file, "			\$this->$attribute = \$value;\n");
fwrite($file, "			if(! \$this->_new){\n");
fwrite($file, "				\$this->_originalDn = \$this->dn;\n");
fwrite($file, "				\$this->dn = substr(\$this->dn, 0, strpos(\$this->dn, '=')+1).\$value.substr(\$this->dn, strpos(\$this->dn, ','));\n");
fwrite($file, "				\$this->_modified = true;\n");
fwrite($file, "				\$this->_renamed = true;\n");
fwrite($file, "			}\n");
fwrite($file, "			else{\n");
fwrite($file, "				if(isset(\$this->dn)){\n");
fwrite($file, "					\$this->dn = substr(\$this->dn, 0, strpos(\$this->dn, '=')+1).\$value.substr(\$this->dn, strpos(\$this->dn, ','));\n");
fwrite($file, "				}\n");
fwrite($file, "				else{\n");
fwrite($file, "					\$manager = \\$nsp$manager_classname::getInstance();\n");
fwrite($file, "					\$this->dn = \$manager->getRdnAttribute().\"=\".\$this->$rdn.\",\".\$manager->getBase();\n");
fwrite($file, "				}\n");
fwrite($file, "			}\n");
fwrite($file, "		}\n");
fwrite($file, "	}\n");
fwrite($file, "\n");
}
elseif($infos['datatype']['type'] != 'constant'){
fwrite($file, "	public function set".$this->camelize($attribute)."(\$value){\n");
fwrite($file, "		if(\$value !== \$this->$attribute){\n");
fwrite($file, "			\$this->$attribute = \$value;\n");
fwrite($file, "			\$this->_modified = true;\n");
fwrite($file, "		}\n");
fwrite($file, "	}\n");
fwrite($file, "\n");
}
}
}
}
fwrite($file, "}\n");
fwrite($file, "?>");
?>