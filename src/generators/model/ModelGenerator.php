<?php
class ModelGenerator extends MGenerator{
	
	public $version = '7.1.0';
	
	public $model_directory;
	public $model_core_directory;
	public $model_exceptions_directory;
	
	public $update;
	
	public $ldapmodel;
	public $database;
	public $namespace;	
	
	private $objectModel = null;
	
	private $templates = array("object_conf", "object_core", "object_exception", "object", "manager_core", "manager_exception", "manager", "connection_provider", "connection_provider_exception", 'msolm_object');
	
	public function __construct($project_name, $ldapmodel, $database, $output, $update_only){
		
		$namespace = $database['phpNamespace'] != "" ? rtrim($database['phpNamespace'], "\\") : $project_name;
		
		$directory = $output."/".preg_replace("#\\\#", DIRECTORY_SEPARATOR, $namespace)."/";

		$this->model_directory = $directory;
		if(! file_exists($directory)){
			mkdir($directory, 0755, true);
			mkdir($directory."core", 0755, true);
			mkdir($directory."exceptions", 0755, true);
		}
		else{
			if(! file_exists($directory."core")){
				mkdir($directory."core", 0755, true);
			}
			if(! file_exists($directory."exceptions")){
				mkdir($directory."exceptions", 0755, true);
			}
		}
		$this->model_core_directory = $directory."core/";
		$this->model_exceptions_directory = $directory."exceptions/";
		$this->ldapmodel = $ldapmodel;
		$this->database = $database;
		$this->namespace = $namespace;
		$this->update = $update_only;
	}
	
	public function run(){
		
		echo "Cleaning output directory\n";
		echo "... \n";
		
		$success = true;
		if($this->update){
			$core_dir = dir($this->model_directory."core");
			while($entry = $core_dir->read()) {
				if(is_file($this->model_directory."core/".$entry) && (substr_compare($entry, ".conf.php", -9) !== 0)){
					if(! unlink($this->model_directory."core/".$entry)){
						echo "Fail to delete file ".$this->model_directory."core/".$entry."\n\n";
						$success = false;
					}
				}
			}
			$exceptions_dir = dir($this->model_directory."exceptions");
			while($entry = $exceptions_dir->read()) {
				if(is_file($this->model_directory."exceptions/".$entry)){
					if(! unlink($this->model_directory."exceptions/".$entry)){
						echo "Fail to delete file ".$this->model_directory."exceptions/".$entry."\n\n";
						$success = false;
					}
				}
			}
			$main_dir = dir($this->model_directory);
			$present_model_files = array();
			foreach($this->getObjectModel() as $object_name => $datas){
				$present_model_files[] = $object_name.".php";
				$present_model_files[] = $datas['manager_name'].".php";
			}
			while($entry = $main_dir->read()) {
				if(is_file($this->model_directory.$entry)
					&& (! in_array($entry, $present_model_files))
					&& (substr_compare($entry, ".bak", -4) !== 0)){
					if(! rename($this->model_directory.$entry, $this->model_directory.$entry.".bak")){
						echo "Fail to rename file ".$this->model_directory.$entry." as ".$this->model_directory.$entry.".bak\n\n";
						$success = false;
					}
				}
			}
		}
		else{
			foreach(array("", "core", "exceptions") as $dir_name){
				$directory = dir($this->model_directory.$dir_name);
				while($entry = $directory->read()) {
					if(is_file($this->model_directory.$dir_name."/".$entry)){
						if(! unlink($this->model_directory.$dir_name."/".$entry)){
							echo "Fail to delete file ".$this->model_directory.$dir_name."/".$entry."\n\n";
							$success = false;
						}
					}
				}
			}
		}
		
		if($success){
			echo " OK\n";
		}
		else{
			echo "FAILED\n";
			echo "Terminating process\n";
			exit(0);
		}
		echo "\n";
		
		
		echo "Generating model files\n";
		echo "... \n";
		
		self::compileTemplates("model", $this->templates);
		
		$database = $this->database;
		
		$model = $this->getObjectModel();
		//print_r($model);
		
		$namespace = $this->namespace;
		$nsp = $namespace.'\\';
		
		
		$msolm_version = $this->version;

		
		//* ******************************************
		//* LdapConnectionProvider                ****
		//* ******************************************
		$file = fopen($this->model_core_directory."LdapConnectionProvider.php", "w+");
		include(__DIR__.'/connection_provider_generator.php');
		fclose($file);
		$file = fopen($this->model_exceptions_directory."LdapConnectionProviderException.php", "w+");
		include(__DIR__.'/connection_provider_exception_generator.php');
		fclose($file);
		
		if((! $this->update) || (! file_exists($this->model_core_directory."__ldap_params.conf.php"))){
			$file = fopen($this->model_core_directory."__ldap_params.conf.php", "w+");
			fwrite($file, "<?php\n");
			fwrite($file, "\$ldap_address=\"".$database['host']."\";\n");
			fwrite($file, "\$ldap_port=\"".$database['port']."\";\n");
			fwrite($file, "\$ldap_bind_dn=\"".$database['bind_dn']."\";\n");
			fwrite($file, "\$ldap_bind_password=\"".$database['bind_password']."\";\n");
			fwrite($file, "?>");
			fclose($file);
		}

		/** ******************************************/
		/** MSOLM object                          ****/
		/** ******************************************/
		$file = fopen($this->model_core_directory."MSOLM.php", "w+");
		include(__DIR__.'/msolm_object_generator.php');
		fclose($file);
		
		
		/** ******************************************/
		/** ldap objects                        ****/
		/** ******************************************/
		foreach($model as $objectname => $datas){
			
			$classname = $objectname;
			$core_classname = $objectname."Core";
			$exception_classname = $objectname."Exception";
			$manager_classname = $datas['manager_name'];
			$manager_core_classname = $datas['manager_name']."Core";
			$manager_exception_classname = $datas['manager_name']."Exception";
			$ou = $datas['ou'];
			$filter = $datas['filter'];
			
			echo "\nProcessing : $classname :\n";
			
			//* ******************************************
			//* Object configuration file             ****
			//* ******************************************
			// création du fichier
			$filename = $objectname.".conf.php";
			if((! $this->update) || (! file_exists($this->model_core_directory.$filename))){
			echo "	generating : $filename\n";
			$file = fopen($this->model_core_directory.$filename, "w+");
			include(__DIR__.'/object_conf_generator.php');
			fclose($file);
			}
			else{
				echo "	keep existing $filename file when processing in update mode\n";
			}
			
			//* ******************************************
			//* Classe objectCore                     ****
			//* ******************************************
			// création du fichier
			$filename = $core_classname.".php";
			echo "	generating : $filename\n";
			$file = fopen($this->model_core_directory.$filename, "w+");
			include(__DIR__.'/object_core_generator.php');
			fclose($file);
			
			/** ******************************************/
			/** Classe objectException                ****/
			/** ******************************************/
			// création du fichier
			$filename = $exception_classname.".php";
			echo "	generating : $filename\n";
			$file = fopen($this->model_exceptions_directory.$filename, "w+");
			include(__DIR__.'/object_exception_generator.php');
			fclose($file);
			
			//* ******************************************
			//* Classe object                         ****
			//* ******************************************
			$filename = $classname.".php";
			if((! $this->update) || (! file_exists($this->model_directory.$filename))){
				// création du fichier
				echo "	generating : $filename\n";
				$file = fopen($this->model_directory.$filename, "w+");
				include(__DIR__.'/object_generator.php');
				fclose($file);
			}
			
			
			//* ******************************************
			//* Classe objectManagerCore              ****
			//* ******************************************
			// création du fichier
			$filename = $manager_core_classname.".php";
			echo "	generating : $filename\n";
			$file = fopen($this->model_core_directory.$filename, "w+");
			include(__DIR__.'/manager_core_generator.php');
			fclose($file);
			
			/** ******************************************/
			/** Classe objectManagerException         ****/
			/** ******************************************/
			// création du fichier
			$filename = $manager_exception_classname.".php";
			echo "	generating : $filename\n";
			$file = fopen($this->model_exceptions_directory.$filename, "w+");
			include(__DIR__.'/manager_exception_generator.php');
			fclose($file);
			
			//* ******************************************
			//* Classe objectManager                  ****
			//* ******************************************
			$filename = $manager_classname.".php";
			if((! $this->update) || (! file_exists($this->model_directory.$filename))){
				// création du fichier
				echo "	generating : $filename\n";
				$file = fopen($this->model_directory.$filename, "w+");
				include(__DIR__.'/manager_generator.php');
				fclose($file);
			}
		}
		
		echo "OK.\n\n";
	}
	
	
	public function getObjectModel(){
		if(isset($this->objectModel)){
			return $this->objectModel;
		}
		$ldapModel = $this->ldapmodel;
		//print_r($ldapModel);
		$objectModel = array();
		
		foreach($ldapModel as $objectname => $datas){

			$objectModel[$objectname]['manager_name'] = $datas['manager_name'];
			$objectModel[$objectname]['ou'] = $datas['ou'];
			$objectModel[$objectname]['filter'] = $datas['filter'];

			foreach($datas['attributes'] as $attribute_name => $infos){
				// A étudier en fonction de multi ou pas
				/*if(!isset($infos['reference'])){
						// Les attributs simples
						$objectModel[$objectname]['attributes'][$attribute_name]['type'] = 'value';
				}
				else{
						$objectModel[$objectname]['attributes'][$attribute_name]['type'] = 'refValue';
				}*/
				// Pour le moment :
				$objectModel[$objectname]['attributes'][$attribute_name]['type'] = 'value';


				$objectModel[$objectname]['attributes'][$attribute_name]['datatype'] = $infos['data'];
				$objectModel[$objectname]['attributes'][$attribute_name]['required'] = $infos['required'];
				$objectModel[$objectname]['attributes'][$attribute_name]['unique'] = $infos['unique'];
				$objectModel[$objectname]['attributes'][$attribute_name]['rdn'] = $infos['rdn'];
				$objectModel[$objectname]['attributes'][$attribute_name]['multi'] = $infos['multi'];
				$objectModel[$objectname]['attributes'][$attribute_name]['ldap_attribute'] = $infos['ldap_attribute'];
				if($infos['multi']){
					$objectModel[$objectname]['attributes'][$attribute_name]['attribute_pf_name'] = $infos['attribute_pf_name'];
				}

				if($infos['rdn']){
					$objectModel[$objectname]['rdn'] = $attribute_name;
				}
			}
		}
		
		$this->objectModel = $objectModel;
		return $objectModel;
	}
	
	
	public function test(){
		print_r($this->ldapmodel);
		$model = $this->getObjectModel();
		print_r($model);
	}
}

class ModelGeneratorException extends Exception {
	public function ModelGeneratorException($message = '', $code = 0, $e = null) {
		parent::__construct($message, $code, $e);
	}
}
?>
