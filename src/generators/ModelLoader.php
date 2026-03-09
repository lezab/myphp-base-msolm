<?php
class ModelLoader{
	
	public $schema;
	public $model;
	public $error;
		
	public function __construct($file){
		libxml_use_internal_errors(true);
		
		$schema = new DomDocument();
		if(! $schema->load($file)){
			throw new ModelLoaderException(self::libxml_get_errors(), 0);
		}
		$this->schema = $schema;
	}
	
	public function getErrors(){
		return $this->error;
	}
	
	public function getModel(){
		if(! $this->check()){
			throw new ModelLoaderException($this->getErrors());
		}
		return $this->model;
	}
	
	
	public function check(){

		$model = array();
		
		$model_xml = $this->schema;
		if (! $model_xml->schemaValidate(__DIR__.'/msolm_schema.xsd')) {
			$this->error = self::libxml_get_errors();
			return false;
		}
		
		$model['ldap'] = array();
		$db = $model_xml->getElementsByTagName("ldap");
		$model['ldap']['host'] = $db->item(0)->getAttribute('host');
		$model['ldap']['port'] = $db->item(0)->getAttribute('port');
		$model['ldap']['bind_dn'] = $db->item(0)->getAttribute('bind_dn');
		$model['ldap']['bind_password'] = $db->item(0)->getAttribute('bind_password');
		$model['ldap']['phpNamespace'] = $db->item(0)->getAttribute('phpNamespace');
		
		
		$ldapmodel = array();
		$objects = $model_xml->getElementsByTagName("object");
		
		// Loading all objects in a first pass
		foreach($objects as $object){
		
			$object_name = $object->getAttribute("phpObjectName");
            if($object_name == ""){
				$this->error = "Object definition should specify at least phpObjectName attribute";
				return false;
			}
         
			if(isset($ldapmodel[$object_name])){
				$this->error = "Cannot define $object_name twice. Object name should be unique in the schema";
				return false;
			}
		
			$ldapmodel[$object_name] = array();
			
			$ldapmodel[$object_name]['object_pf_name'] = $object->getAttribute("phpObjectPFName");
			if($ldapmodel[$object_name]['object_pf_name'] == ""){
				$ldapmodel[$object_name]['object_pf_name'] = $object_name."s";
			}
			$ldapmodel[$object_name]['manager_name'] = $object->getAttribute("phpManagerName");
			if($ldapmodel[$object_name]['manager_name'] == ""){
				$ldapmodel[$object_name]['manager_name'] = $ldapmodel[$object_name]['object_pf_name']."Manager";
			}  
			

			$ldapmodel[$object_name]['ou'] = $object->getAttribute("ou");
			$ldapmodel[$object_name]['filter'] = $object->getAttribute("filter");
                        
                        
			$rdn_defined = false;
			$ldapmodel[$object_name]['attributes'] = array();
			$ldap_attribute_names = array();
			
			foreach($object->getElementsByTagName("attribute") as $attribute){
				
				$attribute_name = $attribute->getAttribute("phpAttributeName");
				$ldap_attribute_name = $attribute->getAttribute("name");
				if($attribute_name == ""){
					$attribute_name = $ldap_attribute_name;
				}
				
				if(isset($ldapmodel[$object_name]['attributes'][$attribute_name])){
					$this->error = "Cannot define $attribute_name twice. Attribute name should be unique in an object (in object $object_name)";
					return false;
				}
				if(in_array($ldap_attribute_name, $ldap_attribute_names)){
					$this->error = "Cannot define attributes on same ldap attribute ($ldap_attribute_name) more than once (In object $object_name)";
					return false;
				}
				$ldap_attribute_names[] = $ldap_attribute_name;
				
				$ldapmodel[$object_name]['attributes'][$attribute_name] = array();
				
				$ldapmodel[$object_name]['attributes'][$attribute_name]['multi'] = $attribute->getAttribute("multi") == "true" ? true : false;
				
				$ldapmodel[$object_name]['attributes'][$attribute_name]['ldap_attribute'] = $ldap_attribute_name;
				
				if($ldapmodel[$object_name]['attributes'][$attribute_name]['multi']){
					$ldapmodel[$object_name]['attributes'][$attribute_name]['attribute_pf_name'] = $attribute->getAttribute("phpAttributePFName");
					if($ldapmodel[$object_name]['attributes'][$attribute_name]['attribute_pf_name'] == ""){
						$ldapmodel[$object_name]['attributes'][$attribute_name]['attribute_pf_name'] = $attribute_name."s";
					}
				}
				
				
				$attribute_type = array();
				$attribute_type['type'] = $attribute->getAttribute("type");
				
				$attribute_type['size'] = $attribute->getAttribute("size");
				// Peut être défini si "type" est string seulement
				if(! ($attribute_type['type'] == "string")){
					if($attribute_type['size'] != ""){
						$this->error = "size attribute should not be defined for ".$attribute_type['type']." attribute (attribute $attribute_name in object $object_name)";
						return false;
					}
				}
				
                                
				$attribute_type['values'] = $attribute->getAttribute("values");
				if($attribute_type['type'] == "enum" || ($attribute_type['type'] == "constant" && $ldapmodel[$object_name]['attributes'][$attribute_name]['multi'])){
					if($attribute_type['values'] == ""){
						$multicomment = $attribute_type['type'] == "constant" ? " (multi)" : "";
						$this->error = "values attribute should be defined for ".$attribute_type['type']."$multicomment attribute (attribute $attribute_name in object $object_name)";
						return false;
					}
				}
				if(! ($attribute_type['type'] == "enum" || ($attribute_type['type'] == "constant" && $ldapmodel[$object_name]['attributes'][$attribute_name]['multi']))){
					if($attribute_type['values'] != ""){
						$multicomment = $attribute_type['type'] == "constant" ? " (not multi)" : "";
						$this->error = "values attribute should not be defined for ".$attribute_type['type']."$multicomment attribute (attribute $attribute_name in object $object_name)";
						return false;
					}
				}
				
				$attribute_type['value'] = $attribute->getAttribute("value");
				if($attribute_type['type'] == "constant" && (! $ldapmodel[$object_name]['attributes'][$attribute_name]['multi'])){
					if($attribute_type['value'] == ""){
						$this->error = "value attribute should be defined for constant (not multi) attribute (attribute $attribute_name in object $object_name)";
						return false;
					}
				}
				if(! ($attribute_type['type'] == "constant" && (! $ldapmodel[$object_name]['attributes'][$attribute_name]['multi']))){
					if($attribute_type['value'] != ""){
						$this->error = "value attribute should not be defined for ".$attribute_type['type']." attribute (attribute $attribute_name in object $object_name)";
						return false;
					}
				}

				/*$attribute_type['auto_increment'] = $attribute->getAttribute("autoIncrement") == "true" ? true : false;
				if($attribute_type['auto_increment']){
					// Ne peut etre défini que si "type" est int, integer ...
					if(! ($attribute_type['type'] == "integer")){
						$this->error = "auto_increment attribute can only be set for integer attribute (attribute $attribute_name in object $object_name)";
						return false;
					}
				}*/
				
				/*$attribute_type['unsigned'] = $attribute->getAttribute("unsigned") == "true" ? true : false;
				if($attribute_type['unsigned']){
					// Ne peut etre défini que si "type" est int, integer ...
					if(! ($attribute_type['type'] == "integer")){
						$this->error = "unsigned attribute can only be set for integer attribute (attribute $attribute_name in object $object_name)";
						return false;
					}
				}*/
				
				$ldapmodel[$object_name]['attributes'][$attribute_name]['data'] = $attribute_type;
		
				if($attribute->hasChildNodes()){
					$reference = $attribute->getElementsByTagName("reference")->item(0);
					$attribute_reference = array('object' => $reference->getAttribute('object'), 'attribute' => $reference->getAttribute('attribute'));
					$ldapmodel[$object_name]['attributes'][$attribute_name]['reference'] = $attribute_reference;
				}
                                
				if($attribute->getAttribute("rdn") == "true"){
					if(! $rdn_defined){
						$ldapmodel[$object_name]['attributes'][$attribute_name]['rdn'] = true;
						$rdn_defined = true;
					}
					else{
						$this->error = "Only one attribute can be defined as rdn (attribute $attribute_name in object $object_name)";
						return false;
					}
				}
				else{
					$ldapmodel[$object_name]['attributes'][$attribute_name]['rdn'] = false;
				}


				$ldapmodel[$object_name]['attributes'][$attribute_name]['unique'] = $attribute->getAttribute("unique") == "true" ? true : false;

				if($ldapmodel[$object_name]['attributes'][$attribute_name]['unique'] && $ldapmodel[$object_name]['attributes'][$attribute_name]['rdn']){
					$this->error = "An attribute cannot be rdn and unique (attribute $attribute_name in object $object_name)";
					return false;
				}

				$ldapmodel[$object_name]['attributes'][$attribute_name]['required'] = $attribute->getAttribute("required") == "true" ? true : ($attribute->getAttribute("required") == "false" ? false : null);

				if(isset($ldapmodel[$object_name]['attributes'][$attribute_name]['required'])){
					if($ldapmodel[$object_name]['attributes'][$attribute_name]['required'] && $attribute_type['auto_increment']){
						$this->error = "An attribute cannot be required and auto_increment (attribute $attribute_name in object $object_name)";
						return false;
					}
					if(! $ldapmodel[$object_name]['attributes'][$attribute_name]['required'] && $ldapmodel[$object_name]['attributes'][$attribute_name]['rdn']){
						$this->error = "A rdn cannot be set 'required=false' (attribute $attribute_name in object $object_name)";
						return false;
					}
				}
				else{
					/*if($attribute_type['auto_increment']){
						$ldapmodel[$object_name]['attributes'][$attribute_name]['required'] = false;
					}
					else*/
					if($ldapmodel[$object_name]['attributes'][$attribute_name]['rdn']){
						$ldapmodel[$object_name]['attributes'][$attribute_name]['required'] = true;
					}
					else{
						$ldapmodel[$object_name]['attributes'][$attribute_name]['required'] = false;
					}
				}
			}

			if(! $rdn_defined){
				$this->error = "No attribute is defined as rdn. Please specify one (in object $object_name)";
				return false;
			}
		}
		
		// A second pass to check references
		foreach($objects as $object){
			$object_name = $object->getAttribute("name");
			foreach($object->getElementsByTagName("attribute") as $attribute){
				$attribute_name = $attribute->getAttribute("name");
				if(isset($ldapmodel[$object_name]['attributes'][$attribute_name]['reference'])){
					$reference = $ldapmodel[$object_name]['attributes'][$attribute_name]['reference'];
					if(! isset($ldapmodel[$reference['object']])){
						$this->error = "Attribute $attribute_name in $object_name references an object which is not defined (".$reference['object'].")";
						return false;
					}
					if($reference['attribute'] != 'dn'){
						if(! isset($ldapmodel[$reference['object']]['attributes'][$reference['attribute']])){
								$this->error = "Attribute $attribute_name in $object_name references an attribute which is not defined (".$reference['object']."(".$reference['attribute']."))";
								return false;
						}

						if(! ($ldapmodel[$reference['object']]['attributes'][$reference['attribute']]['rdn']
								|| $ldapmodel[$reference['object']]['attributes'][$reference['attribute']]['unique'])){
								$this->error = "Attribute $attribute_name in $object_name cannot reference an attribute which is not rdn or unique (".$reference['object']."(".$reference['attribute']."))";
								return false;
						}
					}
				}
			}
		}
		
		$model['objects'] = $ldapmodel;
		$this->model = $model;
		return true;
	}
	
	public function checkLight(){
		$model_xml = $this->schema;
		if (! $model_xml->schemaValidate(__DIR__.'/msolm_schema.xsd')) {
			$this->error = self::libxml_get_errors();
			return false;
		}
		return true;
	}

	private static function libxml_get_error($error) {
		$return = "\n";
		switch ($error->level) {
			case LIBXML_ERR_WARNING:
				$return .= "Warning $error->code ";
				break;
			case LIBXML_ERR_ERROR:
				$return .= "Error $error->code ";
				break;
			case LIBXML_ERR_FATAL:
				$return .= "Fatal Error $error->code ";
				break;
		}
		$return .= "on line $error->line : ";
		$return .= trim($error->message);
		$return .= "\n";
		return $return;
	}
	
	private static function libxml_get_errors() {
		$message = "";
		$errors = libxml_get_errors();
		foreach ($errors as $error) {
			$message.= self::libxml_get_error($error);
		}
		libxml_clear_errors();
		return $message;
	}
}

class ModelLoaderException extends Exception {
	public function ModelLoaderException($message = '', $code = 0, $e = null) {
		parent::__construct($message, $code, $e);
	}
}
?>