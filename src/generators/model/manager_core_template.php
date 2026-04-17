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
##use \\$nsp"."$classname;
##use \\$nsp"."exceptions\\$manager_classname"."Exception;
##
##/**
## * @class $manager_core_classname : a class for managing $classname objects.
## * Like all other CoreManager classes, the class is a singleton an should not be instanciate.
## * The right way to deal with this class is to call the getInstance method on the subclass $core_classname.
## * Ex :
## * \$manager = $manager_classname::getInstance();
## * \$objects = \$manager->getList();
## * ...
## */
##class $manager_core_classname {
##	
##	// Class attributes for object management
##	protected static \$instance;
##	protected \$ldap;
##	protected static \$base;
##	protected static \$filter;
##	protected static \$ldapAttributesList;
##	protected static \$attributesMapping;
##	protected static \$singleAttributesIndex;
##	protected static \$multiAttributesIndex;
##	protected static \$requiredAttributesList;
##
##	/**
##	 * Constructor
##	 * This constructor should not be used.
##	 * Use getInstance method instead.
##	 * @see getInstance()
##	 */
##	protected function __construct(){
##		\$this->ldap = LdapConnectionProvider::getInstance();
##
##		include(__DIR__.\"/$classname.conf.php\");
##		self::\$base = \$ldap_base;
##		self::\$filter = \$ldap_filter;
##		self::\$ldapAttributesList = array_values(\$ldap_attributes_mapping);
##		self::\$attributesMapping = \$ldap_attributes_mapping;
$rdn = $datas['rdn'];
$rdn_infos = $datas['attributes'][$rdn];
##		self::\$singleAttributesIndex = array(strtolower(\$ldap_attributes_mapping['$rdn']) => \"$rdn\");
foreach($datas['attributes'] as $attribute => $infos){
	if(($attribute != $rdn) && (($infos['type'] == "value") || ($infos['type'] == "refValue")) && (! $infos['multi'])){
		##		if(isset(\$ldap_attributes_mapping['$attribute']) && \$ldap_attributes_mapping['$attribute'] != \"\"){
		##			self::\$singleAttributesIndex[strtolower(\$ldap_attributes_mapping['$attribute'])] = \"$attribute\";
		##		}
	}
}
##		self::\$multiAttributesIndex = array();
foreach($datas['attributes'] as $attribute => $infos){
	if((($infos['type'] == "value") || ($infos['type'] == "refValue")) && $infos['multi']){
		##		if(isset(\$ldap_attributes_mapping['".$infos['attribute_pf_name']."']) && \$ldap_attributes_mapping['".$infos['attribute_pf_name']."'] != \"\"){
		##			self::\$multiAttributesIndex[strtolower(\$ldap_attributes_mapping['".$infos['attribute_pf_name']."'])] = \"".$infos['attribute_pf_name']."\";
		##		}
	}
}
#		self::\$requiredAttributesList = array(
$first = true;
foreach($datas['attributes'] as $attribute => $infos){
	if(($infos['type'] == 'value') && $infos['required']){
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
##	}
##
##	/**
##	 * Use this method to get the instance of the manager
##	 * @return \\$nsp"."$manager_classname
##	 */
##	public static function getInstance(){
##		if(! isset(static::\$instance)) {
##			static::\$instance = new static;
##		}
##		return static::\$instance;
##	}
##
##	public function getBase(){
##		return self::\$base;
##	}
##
##	public function getRdnAttribute(){
##		return self::\$attributesMapping['$rdn'];
##	}
##
/** ***************************************************************** */
/**                                                                   */
/** GET AND EXISTS OBJECT                                             */
/**                                                                   */
/** ***************************************************************** */
##	/**
##	 * Use this method to get a $classname extracted from ldap
##	 * @param \$id the object identifier (rdn)
##	 * @return \\$nsp"."$classname
##	 */
##	public function get(\$id){
##		try{
##			return \$this->getBy".$this->camelize($rdn)."(\$id);
##		}
##		catch(\\Exception \$e){
##			throw new ".$manager_classname."Exception(\$e->getMessage(), 2, \$e);
##		}
##	}
##
##	/**
##	 * Use this method to check if a $classname exists in the ldap.
##	 * Using this method is faster than get method then check if value is returned.
##	 * @param \$id the object identifier (rdn)
##	 * @return boolean true if a corresponding entry is found, false otherwise
##	 */
##	public function exists(\$id){
##		try{
##			return \$this->existsBy".$this->camelize($rdn)."(\$id);
##		}
##		catch(\\Exception \$e){
##			throw new ".$manager_classname."Exception(\$e->getMessage(), 2, \$e);
##		}
##	}
##
/** ***************************************************************** */
/**                                                                   */
/** GET OBJECT BY UNIQUE ATTRIBUTE                                    */
/**                                                                   */
/** ***************************************************************** */
foreach($datas['attributes'] as $attribute => $infos){
	if((($infos['type'] == 'value') || ($infos['type'] == "refValue")) && ($infos['unique'] || $infos['rdn'])){
##	/**
##	 * getBy".$this->camelize($attribute)." method
##	 * @param \$value a value of $attribute (defined as unique or rdn)
##	 * @return \\$nsp"."$classname object
##	 */
##	public function getBy".$this->camelize($attribute)."(\$value){
##		if(! (isset(self::\$attributesMapping['$attribute']) && self::\$attributesMapping['$attribute'] != \"\")){
##			throw new ".$manager_classname."Exception(\"No ldap attribute mapping defined for $attribute. Could not proceed\");
##		}
##		try{
##			\$datas = \$this->ldap->search(\"(&(\".self::\$attributesMapping['$attribute'].\"=\$value)\".self::\$filter.\")\", self::\$ldapAttributesList, self::\$base);
##			if(\$datas){
##				if(\$datas['count'] > 1){
##					throw new ".$manager_classname."Exception(\"Multiple entries found for $attribute=\$value while attribute is defined as unique\");
##				}
##				\$cdatas = array();
##				foreach(\$datas[0] as \$key => \$values){
##					if(isset(self::\$singleAttributesIndex[\$key])){
##						\$cdatas[self::\$singleAttributesIndex[\$key]] = \$values[0];
##					}
##					elseif(isset(self::\$multiAttributesIndex[\$key])){
##						\$cdatas[self::\$multiAttributesIndex[\$key]] = array();
##						for(\$i=0; \$i < \$values['count']; \$i++){
##							\$cdatas[self::\$multiAttributesIndex[\$key]][] = \$values[\$i];
##						}
##					}
##				}
##				\$cdatas['dn'] = \$datas[0]['dn'];
##				return new $classname(\$cdatas);
##			}
##			return null;
##		}
##		catch(\\Exception \$e){
##			throw new ".$manager_classname."Exception(\$e->getMessage(), 2, \$e);
##		}
##	}
##
/** ***************************************************************** */
/**                                                                   */
/** EXISTS BY UNIQUE ATTRIBUTE                                        */
/**                                                                   */
/** ***************************************************************** */
##	/**
##	 * existsBy".$this->camelize($attribute)." method
##	 * Using this method is faster than using corresponding get method then check if a value is returned.
##	 * @param \$value a value of $attribute (defined as unique or primary key)
##	 * @return boolean true if a corresponding entry is found, false otherwise
##	 */
##	public function existsBy".$this->camelize($attribute)."(\$value){
##		if(! (isset(self::\$attributesMapping['$attribute']) && self::\$attributesMapping['$attribute'] != \"\")){
##			throw new ".$manager_classname."Exception(\"No ldap attribute mapping defined for $attribute. Could not proceed\");
##		}
##		try{
##			\$datas = \$this->ldap->search(\"(&(\".self::\$attributesMapping['$attribute'].\"=\$value)\".self::\$filter.\")\", self::\$ldapAttributesList, self::\$base);
##			if(\$datas){
##				if(\$datas['count'] > 1){
##					throw new ".$manager_classname."Exception(\"Multiple entries found for $attribute=\$value while attribute is defined as unique\");
##				}
##				return true;
##			}
##			return false;
##		}
##		catch(\\Exception \$e){
##			throw new ".$manager_classname."Exception(\$e->getMessage(), 2, \$e);
##		}
##	}
##
	}
}
/** ***************************************************************** */
/**                                                                   */
/** ADD OBJECT                                                        */
/**                                                                   */
/** ***************************************************************** */
##	/**
##	 * Inserts a new object in the ldap directory.
##	 * Related objects are added or modified according the object attributes
##	 * @param \$object a $classname object
##	 * @param \$cascade this parameter should not be used. It exists for internal purpose only.
##	 * @return ".$rdn_infos['datatype']['type']." object identifier (rdn)
##	 */
##	public function add($classname \$object){
##		if(! \$object->_isNew()){
##			throw new ".$manager_classname."Exception('This object is extracted from ldap or has already been save. Perhaps you should use update method instead', 1);
##		}
##		if(\$object->_isDeleted()){
##			throw new ".$manager_classname."Exception('This object has been deleted. You cannot add it again', 1);
##		}
##
##		try{
##			\$datas = \$object->getDatas();
##			\$ldap_datas = array();
##			foreach(\$datas as \$key => \$value){
##				if(! ((is_array(\$value) && empty(\$value)) || (\$value == null || \$value == \"\"))){
##					if(isset(self::\$attributesMapping[\$key]) && self::\$attributesMapping[\$key] != \"\"){
##						\$ldap_datas[self::\$attributesMapping[\$key]] = \$value;
##					}
##				}
##			}
##			\$this->ldap->add(\$object->getDn(), \$ldap_datas);
##			\$oid = \$object->get".$this->camelize($rdn)."();
##			\$object->_unsetNew();
##			\$object->_resetModified();
##		}
##		catch(\\Exception \$e){
##			throw new ".$manager_classname."Exception(\$e->getMessage(), 2, \$e);
##		}
##		return \$oid;
##	}
##
/** ***************************************************************** */
/**                                                                   */
/** UPDATE OBJECT                                                     */
/**                                                                   */
/** ***************************************************************** */
##	/**
##	 * Updates an existing object in ldap according the modifications done on it.
##	 * Related objects are added or modified according the object attributes.
##	 * @param \$object a $classname object
##	 * @param \$cascade this parameter should not be used. It exists for internal purpose only.
##	 */
##	public function update($classname \$object){
##		if(\$object->_isNew()){
##			throw new ".$manager_classname."Exception('This object is a new object. Perhaps you should use add method instead', 1);
##		}
##		if(\$object->_isDeleted()){
##			throw new ".$manager_classname."Exception('This object has been deleted. You cannot update it', 1);
##		}
##
##		if(\$object->_isModified()){
##			try{
##				if(\$object->_isRenamed()){
##					\$this->ldap->rename(\$object->getOriginalDn(), \$object->getDn());
##				}
##				\$datas = \$object->getDatas();
##				// Active Directory does not accept the rdn is part of the attributes passed to modify method even if is equal to the original rdn
##				// In any case, it has been processed before so we can remove it
##				unset(\$datas['$rdn']);
##				// Idem but faster than : foreach(\$datas as \$key => \$data){ if(\$data == null)	\$datas[\$key] = array(); }
##				\$datas = array_replace(\$datas, array_fill_keys(array_keys(\$datas, null), array()));
##				\$ldap_datas = array();
##				\$modifiedAttributes = \$object->_getModifiedAttributes();
##				foreach(\$datas as \$key => \$value){
##					if(in_array(\$key, \$modifiedAttributes)){
##						// Only process attributes that have a mapping
##						if(isset(self::\$attributesMapping[\$key]) && self::\$attributesMapping[\$key] != \"\"){
##							\$ldap_datas[self::\$attributesMapping[\$key]] = \$value;
##						}
##					}
##				}
##				\$this->ldap->modify(\$object->getDn(), \$ldap_datas);
##			}
##			catch(\\Exception \$e){
##				throw new ".$manager_classname."Exception(\$e->getMessage(), 2, \$e);
##			}
##			\$object->_resetModified();
##		}
##	}
##
/** ***************************************************************** */
/**                                                                   */
/** DELETE OBJECT                                                     */
/**                                                                   */
/** ***************************************************************** */
##	/**
##	 * Deletes an existing object in ldap.
##	 * Related objects are modified according this deletion, but are not deleted.
##	 * @param \$object a $classname object
##	 * @param \$cascade this parameter should not be used. It exists for internal purpose only.
##	 */
##	public function delete($classname \$object){
##		if(\$object->_isNew()){
##			throw new ".$manager_classname."Exception('This object is a new object. Cannot delete it', 1);
##		}
##		if(\$object->_isDeleted()){
##			throw new ".$manager_classname."Exception('This object has already been deleted. You cannot delete it again', 1);
##		}
##
##		try{
##			\$this->ldap->delete(\$object->getDn());
##		}
##		catch(\\Exception \$e){
##			throw new ".$manager_classname."Exception(\$e->getMessage(), 2, \$e);
##		}
##
##		\$object->_setDeleted();
##	}
##
/** ***************************************************************** */
/**                                                                   */
/** COUNT OBJECTS                                                     */
/**                                                                   */
/** ***************************************************************** */
##	/**
##	 * Get the number of objects in ldap.
##	 * @return integer - the number of $classname objects.
##	 */
##	public function count(){
##		try{
##			\$datas = \$this->ldap->search(self::\$filter, array(), self::\$base);
##			if(\$datas){
##				return \$datas['count'];
##			}
##			return 0;
##		}
##		catch(\\Exception \$e){
##			throw new ".$manager_classname."Exception(\$e->getMessage(), 2, \$e);
##		}
##	}
##
/** ***************************************************************** */
/**                                                                   */
/** LIST ALL OBJECTS                                                  */
/**                                                                   */
/** ***************************************************************** */
##	/**
##	 * Get a set of existing objects in the  ldap directory.
##	 * @param \$sortAttributes (optional) could be a single string or an array of string corresponding to the attributes names Could be null. Default null.
##	 * @param \$asArray (optional) boolean : if true the return is a 2 dimensions array containing datas in the corresponding entries ($classname). Default false.
##	 * @return \\$nsp"."$classname"."[] - an array of $classname : the objects found in storage according \$offset and \$limit parameters passed. If \$asArray is set to true, returns a 2 dimensions array containing datas of these objects.
##	 */
##	public function getList(\$sortAttributes = null, \$asArray = false){
##		\$objects = array();
##
##		try{
##			\$datas = \$this->ldap->search(self::\$filter, self::\$ldapAttributesList, self::\$base, \$sortAttributes);
##			if(\$datas){
##				unset(\$datas['count']);
##				if(\$asArray){
##					foreach(\$datas as \$i => \$entry){
##						\$cdatas = array();
##						foreach(\$entry as \$key => \$values){
##							if(isset(self::\$singleAttributesIndex[\$key])){
##								\$cdatas[self::\$singleAttributesIndex[\$key]] = \$values[0];
##							}
##							elseif(isset(self::\$multiAttributesIndex[\$key])){
##								\$cdatas[self::\$multiAttributesIndex[\$key]] = array();
##								for(\$i=0; \$i < \$values['count']; \$i++){
##									\$cdatas[self::\$multiAttributesIndex[\$key]][] = \$values[\$i];
##								}
##							}
##						}
##						\$cdatas['dn'] = \$entry['dn'];
##						\$objects[] = \$cdatas;
##					}
##				}
##				else{
##					foreach(\$datas as \$i => \$entry){
##						\$cdatas = array();
##						foreach(\$entry as \$key => \$values){
##							if(isset(self::\$singleAttributesIndex[\$key])){
##								\$cdatas[self::\$singleAttributesIndex[\$key]] = \$values[0];
##							}
##							elseif(isset(self::\$multiAttributesIndex[\$key])){
##								\$cdatas[self::\$multiAttributesIndex[\$key]] = array();
##								for(\$i=0; \$i < \$values['count']; \$i++){
##									\$cdatas[self::\$multiAttributesIndex[\$key]][] = \$values[\$i];
##								}
##							}
##						}
##						\$cdatas['dn'] = \$entry['dn'];
##						\$objects[] = new $classname(\$cdatas);
##					}
##					
##				}
##				return \$objects;
##			}
##			return null;
##		}
##		catch(\\Exception \$e){
##			throw new ".$manager_classname."Exception(\$e->getMessage(), 2, \$e);
##		}
##	}
/** ***************************************************************** */
/**                                                                   */
/** LIST OBJECTS WITH FILTER                                          */
/**                                                                   */
/** ***************************************************************** */
##	/**
##	 * Get a set of existing objects in the  ldap directory according a filter.
##	 * @param \$filter a filter to select only some entries among the entries defined by the object class
##	 * @param \$sortAttributes (optional) could be a single string or an array of string corresponding to the attributes names. Could be null. Default null.
##	 * @param \$asArray (optional) boolean : if true the return is a 2 dimensions array containing datas in the corresponding entries ($classname). Default false.
##	 * @return \\$nsp"."$classname"."[] - an array of $classname : the objects found in storage according \$offset and \$limit parameters passed. If \$asArray is set to true, returns a 2 dimensions array containing datas of these objects.
##	 */
##	public function getFilteredList(\$filter, \$sortAttributes = null, \$asArray = false){
##		\$objects = array();
##
##		try{
##			\$datas = \$this->ldap->search(\"(&\$filter\".self::\$filter.\")\", self::\$ldapAttributesList, self::\$base, \$sortAttributes);
##			if(\$datas){
##				unset(\$datas['count']);
##				if(\$asArray){
##					foreach(\$datas as \$i => \$entry){
##						\$cdatas = array();
##						foreach(\$entry as \$key => \$values){
##							if(isset(self::\$singleAttributesIndex[\$key])){
##								\$cdatas[self::\$singleAttributesIndex[\$key]] = \$values[0];
##							}
##							elseif(isset(self::\$multiAttributesIndex[\$key])){
##								\$cdatas[self::\$multiAttributesIndex[\$key]] = array();
##								for(\$i=0; \$i < \$values['count']; \$i++){
##									\$cdatas[self::\$multiAttributesIndex[\$key]][] = \$values[\$i];
##								}
##							}
##						}
##						\$cdatas['dn'] = \$entry['dn'];
##						\$objects[] = \$cdatas;
##					}
##				}
##				else{
##					foreach(\$datas as \$i => \$entry){
##						\$cdatas = array();
##						foreach(\$entry as \$key => \$values){
##							if(isset(self::\$singleAttributesIndex[\$key])){
##								\$cdatas[self::\$singleAttributesIndex[\$key]] = \$values[0];
##							}
##							elseif(isset(self::\$multiAttributesIndex[\$key])){
##								\$cdatas[self::\$multiAttributesIndex[\$key]] = array();
##								for(\$i=0; \$i < \$values['count']; \$i++){
##									\$cdatas[self::\$multiAttributesIndex[\$key]][] = \$values[\$i];
##								}
##							}
##						}
##						\$cdatas['dn'] = \$entry['dn'];
##						\$objects[] = new $classname(\$cdatas);
##					}
##					
##				}
##				return \$objects;
##			}
##			return null;
##		}
##		catch(\\Exception \$e){
##			throw new ".$manager_classname."Exception(\$e->getMessage(), 2, \$e);
##		}
##	}
##}
#?>