#!/usr/bin/php
<?php

$version = "7.1.0";

function version(){
	global $version;
	?>

	My Simple OLM v<?=$version?>
	
	<?php
	echo PHP_EOL;
	exit(0);
}

function usage(){
	?>
		My Simple OLM v<?=$version?>
		
		Usage :
		
		msolm.php -h
	  	msolm.php -g -p myproject
	  	msolm.php --generate --project=myproject -o outputdir
		msolm.php -g --update -p myproject --update
		msolm.php --check -p myproject
		msolm.php --clean
		
		<args>	
			-h, -?, --help :
			
					leads to this message
					
			-g, --generate :
			
					generates model files
					
					should be followed by :
					-p <project_name> : the name of a subdirectory of projects directory, containing a file description.xml
					
					optional (for model generation only) :
					-u : tells the model must be updated only (only core classes are overwritten)
					-o <destination> : output folder where model classes are generated
					
			-p, --project :
			
				The name of a subdirectory of projects directory, containing a file description.xml which describes the ldap structure for classes generation
				
			-o, --output :
			
				The output folder for model classes.
				If not specified model classes are stored in a "model" subdirectory of project folder
				
			-u, --update :
			
				If set, only core classes are overwritten (except __ldap_params.conf.php file) to preserve what has already been added by user.
				
			--check :
			
					should be followed by :
						-p <project_name> : checks if file "projects/<project_name>/description.xml" is well written.
				
			
		<project_name>
			the name of a subdirectory of projects, containing an xml file which describes the ldap objects.
	  	
	  	
	<?php
	echo "\n";
	exit(0);
}

include(__DIR__.'/generators/autoload.php');

function generateModel($project_name, $output = null, $update_only = false){
	
	if(! checkFile($project_name)){
		exit(0);
	}
	$file = "./projects/$project_name/description.xml";
	
	echo "Generating model class files from file : $file\n";
	echo "... \n";
	
	try{
		$loader = new ModelLoader($file);
		$model = $loader->getModel();
	}
	catch (ModelLoaderException $e){
		echo "Errors found in $file :\n\n";
		echo $e->getMessage();
		echo "\nModel class files generation interrupted\n\n";
		exit(0);
	}
	
	$objects = $model['objects'];
	$ldap = $model['ldap'];
		
	try{
		$output = isset($output) ? $output : "./projects/$project_name/model";
		
		$generator = new ModelGenerator($project_name, $objects, $ldap, $output, $update_only);
		//$generator->test();
		$generator->run();
	}
	catch(ModelGeneratorException $e){
		echo "Model class files generation interrupted :\n\n";
		echo $e->getMessage();
		echo "\n";
		exit(0);
	}
	
	echo "Generation complete.\n";
}



function checkFile($project_name){
	$directory = "./projects/$project_name";
	if(!is_dir($directory)){
		echo "Directory $directory not found\n";
		echo "Make sure the project's name is well written\n\n";
		return false;
	}
	
	$file = "$directory/description.xml";
	if(!is_file($file)){
		echo "Description file not found in project's directory\n";
		echo "Make sure you named your project's description file description.xml and the file is located in your project directory\n\n";
		return false;
	}
	return true;
}

function check($project_name){
	if(!checkFile($project_name)){
		exit(0);
	}	
	
	$file = "./projects/$project_name/description.xml";
	
	echo "Checking file : $file\n";
	echo "... \n";
	
	try{
		$loader = new ModelLoader($file);
		
		if(! $loader->check()){
			echo "Checking ends on errors : \n\n";
			echo $loader->getErrors();
			echo "\n";
			exit(0);
		}
	}
	catch (ModelLoaderException $e){
		echo "Checking ends on errors : \n\n";
		echo $e->getMessage();
		echo "\n";
		exit(0);
	}
	
	echo "\nChecking complete : OK\n\n";
}

function deleteDirectory($dir) {
	foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST) as $path){
		if($path->isDir() && !$path->isLink()){
			if(! rmdir($path->getPathname())){
				return false;
			}
		}
		else{
			if(!  unlink($path->getPathname())){
				return false;
			}
		}
	}
	if(! rmdir($dir)){
		return false;
	}
	return true;
}

function cleanAll(){
	if(!is_dir("./projects/")){
		echo "Directory projects/ not found\n";
		exit(0);
	}

	echo "Cleaning projects : \n";
	echo "... \n\n";
	
	$maindir = dir("./projects/");
	
	$globalsuccess = true;
	
	while($directory = $maindir->read()) {
		$project_name = $directory;
		//echo "	$project_name ...";
		
		if(is_dir("./projects/".$directory) && ($directory != ".") && ($directory != "..")){
			$success = true;
			echo "	$project_name ...";
			if(is_dir("./projects/".$directory."/model")){
				if(! deleteDirectory("./projects/".$directory."/model")){
					$success = false;
					$globalsuccess = false;
				}

			}
			if($success){
				echo " OK\n";
			}
			else{
				echo "FAILED\n";
			}
		}
	}
	$maindir->close();
	if($globalsuccess){
		echo "\nCleaning complete : OK\n\n";
	}
	else{
		echo "\nCleaning not complete\n\n";
	}
	echo "\n";
	exit(0);
}


/**
 * Parses $args command line and return them as an array
 *
 * Supports:
 * -e
 * -e <value>
 * --long-param
 * --long-param=<value>
 * --long-param <value>
 * <value>
 *
 * @param array $args
 */
function read_args($args){
	$result = array();
	
	array_shift($args);
	reset($args);
	while ($param = current($args)){
		if ($param[0] == '-') {
			$param_name = substr($param, 1);
			$value = true;
			if ($param_name[0] == '-') {
				// long-opt (--<param>)
				$param_name = substr($param_name, 1);
				if (strpos($param, '=') !== false) {
					// value specified inline (--<param>=<value>)
					list($param_name, $value) = explode('=', substr($param, 2), 2);
				}
			}
			// check if next parameter is a descriptor or a value
			$next_param = next($args);
			if ($value === true && $next_param !== false && $next_param[0] != '-'){
				$value = $next_param;
				next($args);
			}
			$result[$param_name] = $value;
		}
		else {
			// param doesn't belong to any option
			$result[] = $param;
			next($args);
		}
	}
	return $result;
}

/**************************************************************************************/
/**************************************************************************************/
/*                                                                                    */
/*                                        MAIN                                        */
/*                                                                                    */
/**************************************************************************************/
/**************************************************************************************/

$known_options = array(
	'?', 'h', 'help',
	'v', 'version',
	'check',
	'clean',
	'g', 'generate',
	'p', 'project',
	'o', 'output',
	'u', 'update');

$args = read_args($argv);


if(empty($args) || isset($args['?']) || isset($args['h']) || isset($args['help'])){
	usage();
}
elseif(isset($args['v']) || isset($args['version'])){
	version();
}
elseif(isset($args['check'])){
	$project = isset($args['p']) ? $args['p'] : (isset($args['project']) ? $args['project'] : null);
	if(isset($project)){
		check($project);
	}
	else{
		echo "Missing parameter --project (-p) when using --check".PHP_EOL;
		usage();
	}
}
elseif(isset($args['clean'])){
	cleanAll();
}
elseif(isset($args['g']) || isset($args['generate'])){
	$project = isset($args['p']) ? $args['p'] : (isset($args['project']) ? $args['project'] : null);
	if(isset($project)){
		$output = isset($args['o']) ? $args['o'] : (isset($args['output']) ? $args['output'] : null);
		$update = isset($args['u']) ? true : (isset($args['update']) ? true : false);
		generateModel($project, $output, $update);
	}
	else{
		echo "Missing parameter --project (-p) when using --generate (or -g) ".PHP_EOL;
		usage();
	}
}
else{
	foreach(array_keys($args) as $arg){
		if(!in_array($arg, $known_options)){
			echo "Unknown arg : $arg".PHP_EOL;
		}
	}
	usage();
}
?>
