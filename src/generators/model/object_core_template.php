##<?php
##/**
## * File generated with MySimpleOLM v$msolm_version
## * 
## * You should not modify this file.
## * If you need to add or modify some functionalities offered by this file,
## * you should see and modify the child class corresponding to this file.
## */
##
##namespace $nsp"."core;
##
##use \\$nsp"."exceptions\\$classname"."Exception;
##
##/**
## * @class $core_classname.
## * Like all other Core classes, the class is the base class for objects instances in ldap directory.
## * You should not use this class directly but the subclass $classname which inherits all the methods of this class.
## * Ex :
## * \$manager = $manager_classname::getInstance();
## * \$object = new $classname();
## * ... // set object properties
## * \$manager->add(\$object);
## */
##class $core_classname {
##
##	// Class attributes for object management
##	protected \$_new = true;
##	protected \$_deleted = false;
##	protected \$_renamed = false;
##	protected \$_modified = false;
##	protected \$_modifiedAttributes = array();
##
##	// Attributes relatives to ldap entry
##	protected \$dn = null;
/** premier parcours, on en profite pour initiliser des variables */
foreach($datas['attributes'] as $attribute => $infos){
	if(($infos['type'] == 'value') || ($infos['type'] == 'refValue')){
		if($infos['multi']){
			if($infos['datatype']['type'] == 'constant'){
#	protected \$".$infos['attribute_pf_name']." = array(
				$first = true;
				foreach(explode('/',$infos['datatype']['values']) as $value){
					if(! $first){
						#, 
					}
					#\"$value\"
					$first = false;
				}
				##);
			}
			else{
##	protected \$".$infos['attribute_pf_name']." = array();
			}
		}
		else{
			if($infos['datatype']['type'] == 'constant'){
##	protected \$".$attribute." = \"".$infos['datatype']['value']."\";				
			}
			else{
##	protected \$".$attribute." = null;
			}
		}
	}
}
#	protected static \$attributesList = array(
$first = true;
foreach($datas['attributes'] as $attribute => $infos){
	if(($infos['type'] == "value") || ($infos['type'] == "refValue")){
		if(! $first){
			#, 
		}
		if($infos['multi']){
			#\"".$infos['attribute_pf_name']."\"
		}
		else{
			#\"$attribute\"
		}
		$first = false;
	}
}
##);
##
$rdn = $datas['rdn'];
$rdn_infos = $datas['attributes'][$rdn];
##	protected \$_originalDn = null;
##
##
##	/**
##	 * Constructor
##	 * @params \$datas array : should not be used. Only object manager can send \$datas to the constructor 
##	 * to initialize the object. If you want initialize object avoiding to use setters, you have to instanciate
##	 * an empty object then use update method on it.
##	 */
##	public function __construct(array \$datas = null){			
##		if(! empty(\$datas)){
##			\$trace = defined('DEBUG_BACKTRACE_IGNORE_ARGS') ? debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3) : debug_backtrace();
##			if((isset(\$trace[1]['class']) && isset(\$trace[2]['class']) && (\$trace[1]['class'] == '$nsp$classname') && (\$trace[2]['class'] == '$nsp$manager_classname' || \$trace[2]['class'] == '$namespace\core\\$manager_core_classname'))
##				|| (isset(\$trace[1]['class']) && (\$trace[1]['class'] == '$nsp$manager_classname' || \$trace[1]['class'] == '$namespace\core\\$manager_core_classname'))) {
##				foreach (\$datas as \$key => \$value) {
##					\$this->\$key = \$value;
##				}
##				\$this->_new = false;
##			}
##			else{
##				\$this->setDatas(\$datas);
##			}
##		}
##	}
##
##	/**
##	 * Set datas to the object according datas provided.
##	 * @params \$datas array : a set of key,value to initialize the object. Can be used on a new object to initialize it avoiding to use setters,
##	 * when a lot of attributes.
##	 */
##	public function setDatas(array \$datas){
##		foreach (\$datas as \$key => \$value) {
##			if(in_array(\$key, self::\$attributesList)){
##				\$method = \"set\".ucfirst(\$key);
##				\$this->\$method(\$value);
##			}
##			elseif(\$key == 'dn'){
##				\$this->setDn(\$value);
##			}
##		}
##	}
##
##	/**
##	 * @return array all object's attributes in a set of key,value (faster then call any getter)
##	 */
##	public function getDatas(){
##		\$datas = array();
##		foreach (self::\$attributesList as \$attribute) {
##			\$datas[\$attribute] = \$this->\$attribute;
##		}
##		return \$datas;
##	}
##
##	/**
##	 * Tells if the object is a new one or already exists in ldap directory.
##	 * This method is basically used by manager. You may not have to use it.
##	 * @return boolean true if the object is a new one, false otherwise.
##	 */
##	final public function _isNew(){
##		return \$this->_new;
##	}
##
##	/**
##	 * Due to limitations of php the method is public but can only be used from $nsp$classname class itself or corresponding manager class
##	 * You should not use this method.
##	 */
##	final public function _unsetNew(){
##		//\$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
##		\$trace = defined('DEBUG_BACKTRACE_IGNORE_ARGS') ? debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2) : debug_backtrace();
##		if (! (\$trace[1]['class'] == '$nsp$classname' || \$trace[1]['class'] == '$nsp$manager_classname' || \$trace[1]['class'] == '$namespace\core\\$manager_core_classname')) {
##			\$message  = \"Due to limitations of php the method is public but can only be used from $nsp$manager_classname class\\n\";
##			\$message .= \"You should not use this method.\";
##			throw new ".$classname."Exception(\$message);
##		}
##		\$this->_new = false;
##	}
##
##	/**
##	 * Tells if the object has been modified or not.
##	 * This method is basically used by manager. You may not have to use it.
##	 * @return boolean true if the object has been modified, false otherwise.
##	 */
##	final public function _isModified(){
##		return \$this->_modified;
##	}
##
##
##	/**
##	 * Returns the list of modified attributes.
##	 * @return array the list of modified attributes.
##	 */
##	final public function _getModifiedAttributes(){
##		return \$this->_modifiedAttributes;
##	}
##
##
##	/**
##	 * Due to limitations of php the method is public but can only be used from $nsp$classname class itself or corresponding manager class
##	 * You should not use this method.
##	 */
##	final public function _resetModified(){
##		//\$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
##		\$trace = defined('DEBUG_BACKTRACE_IGNORE_ARGS') ? debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2) : debug_backtrace();
##		if (! (\$trace[1]['class'] == '$nsp$classname' || \$trace[1]['class'] == '$nsp$manager_classname' || \$trace[1]['class'] == '$namespace\core\\$manager_core_classname')) {
##			\$message  = \"Due to limitations of php the method is public but can only be used from $nsp$manager_classname class\\n\";
##			\$message .= \"You should not use this method.\";
##			throw new ".$classname."Exception(\$message);
##		}
##		\$this->_modified = false;
##		\$this->_modifiedAttributes = array();
##		\$this->_renamed = false;
##	}
##
##	/** Tells if the object has been deleted from  ldap directory or not.
##	 * This method is basically used by manager. You may not have to use it.
##	 * @return boolean true if the object has been deleted, false otherwise.
##	 */
##	final public function _isDeleted(){
##		return \$this->_deleted;
##	}
##
##	/**
##	 * Due to limitations of php the method is public but can only be used from $nsp$classname class itself or corresponding manager class
##	 * You should not use this method.
##	 */
##	final public function _setDeleted(){
##		//\$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
##		\$trace = defined('DEBUG_BACKTRACE_IGNORE_ARGS') ? debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2) : debug_backtrace();
##		if (! (\$trace[1]['class'] == '$nsp$classname' || \$trace[1]['class'] == '$nsp$manager_classname' || \$trace[1]['class'] == '$namespace\core\\$manager_core_classname')) {
##			\$message  = \"Due to limitations of php the method is public but can only be used from $nsp$manager_classname class\\n\";
##			\$message .= \"You should not use this method.\";
##			throw new ".$classname."Exception(\$message);
##		}
##		\$this->_deleted = true;
##	}
##
##	/** Tells if the object has been renamed or not.
##	 * This method is basically used by manager. You may not have to use it.
##	 * @return boolean true if the object has been renamed, false otherwise.
##	 */
##	final public function _isRenamed(){
##		return \$this->_renamed;
##	}
##
##	//---------------------------------------------------
##	// Getters and setters
##	//---------------------------------------------------
##	public function getDn(){
##		return \$this->dn;
##	}
##
##	public function getOriginalDn(){
##		return \$this->_originalDn;
##	}
##
##	public function setDn(\$value){
##		if(\$value != \$this->dn){
##			if(! \$this->_new){
##				\$this->originalDn = \$this->dn;
##				\$this->dn = \$value;
##				\$newRdn = substr(\$value, strpos(\$value, '=')+1, strpos(\$value, ','));
##				if(\$newRdn !== \$this->$rdn){
##					\$this->$rdn = \$newRdn;
##				}
##				\$this->_modified = true;
##				\$this->_renamed = true;
##			}
##			else{
##				\$this->dn = \$value;
##				\$newRdn = substr(\$value, strpos(\$value, '=')+1, strpos(\$value, ','));
##				if(\$newRdn !== \$this->$rdn){
##					\$this->$rdn = \$newRdn;
##				}
##			}
##		}
##	}
##
foreach($datas['attributes'] as $attribute => $infos){
	if($infos['type'] == 'value'){
		if($infos['multi']){
##	public function get".$this->camelize($infos['attribute_pf_name'])."(){
##		return \$this->".$infos['attribute_pf_name'].";
##	}
##
			if($infos['datatype']['type'] != 'constant'){
##	public function set".$this->camelize($infos['attribute_pf_name'])."(\$values){
##		if(is_array(\$values)){
##			\$shouldReplace = false;
##			foreach(\$values as \$v){
##				if(! in_array(\$v, \$this->".$infos['attribute_pf_name'].")){
##					\$shouldReplace = true;
##					break;
##				}
##			}
##			if(! \$shouldReplace){
##				foreach(\$this->".$infos['attribute_pf_name']." as \$v){
##					if(! in_array(\$v, \$values)){
##						\$shouldReplace = true;
##						break;
##					}
##				}
##			}
##			if(\$shouldReplace){
##				\$this->".$infos['attribute_pf_name']." = \$values;
##				\$this->_modified = true;
##				\$this->_modifiedAttributes[] = '".$infos['attribute_pf_name']."';
##			}
##		}
##		else{
##			if(! ((count(\$this->".$infos['attribute_pf_name'].") == 1) && (\$this->".$infos['attribute_pf_name']."[0] === \$values))){
##				\$this->".$infos['attribute_pf_name']." = array(\$values);
##				\$this->_modified = true;
##				\$this->_modifiedAttributes[] = '".$infos['attribute_pf_name']."';
##			}
##		}
##	}
##
##	public function add".$this->camelize($attribute)."(\$value){
##		if(! in_array(\$value, \$this->".$infos['attribute_pf_name'].")){
##			\$this->".$infos['attribute_pf_name']."[] = \$value;
##			\$this->_modified = true;
##			\$this->_modifiedAttributes[] = '".$infos['attribute_pf_name']."';
##		}
##	}
##
##	public function delete".$this->camelize($attribute)."(\$value){
##		if((\$index = array_search(\$value, \$this->".$infos['attribute_pf_name'].")) !== false){
##			array_splice(\$this->".$infos['attribute_pf_name'].", \$index, 1);
##			\$this->_modified = true;
##			\$this->_modifiedAttributes[] = '".$infos['attribute_pf_name']."';
##		}
##	}
##
			}
		}
		else{
##	public function get".$this->camelize($attribute)."(){
##		return \$this->$attribute;
##	}
##
			if($attribute == $rdn){
##	public function set".$this->camelize($attribute)."(\$value){
##		if(\$value != \$this->$attribute){
##			\$this->$attribute = \$value;
##			if(! \$this->_new){
##				\$this->_originalDn = \$this->dn;
##				\$this->dn = substr(\$this->dn, 0, strpos(\$this->dn, '=')+1).\$value.substr(\$this->dn, strpos(\$this->dn, ','));
##				\$this->_modified = true;
##				\$this->_renamed = true;
##			}
##			else{
##				if(isset(\$this->dn)){
##					\$this->dn = substr(\$this->dn, 0, strpos(\$this->dn, '=')+1).\$value.substr(\$this->dn, strpos(\$this->dn, ','));
##				}
##				else{
##					\$manager = \\$nsp$manager_classname::getInstance();
##					\$this->dn = \$manager->getRdnAttribute().\"=\".\$this->$rdn.\",\".\$manager->getBase();
##				}
##			}
##		}
##	}
##
			}
			elseif($infos['datatype']['type'] != 'constant'){
##	public function set".$this->camelize($attribute)."(\$value){
##		if(\$value !== \$this->$attribute){
##			\$this->$attribute = \$value;
##			\$this->_modified = true;
##			\$this->_modifiedAttributes[] = '".$infos['attribute_pf_name']."';
##		}
##	}
##
			}
		}
	}
}
##}
#?>