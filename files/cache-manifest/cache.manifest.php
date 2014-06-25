<?php
/*
http://www.richardleggett.co.uk/blog/index.php/dynamic-application-cache-manifest-for-php
http://jonathanstark.com/blog/2009/09/27/debugging-html-5-offline-application-cache/
https://developer.mozilla.org/en/Offline_resources_in_Firefox
http://www.html5rocks.com/en/tutorials/appcache/beginner/
http://developer.apple.com/library/safari/#documentation/iPhone/Conceptual/SafariJSDatabaseGuide/OfflineApplicationCache/OfflineApplicationCache.html

*/

error_reporting(E_ALL);
ini_set('display_errors', '1');

header("Cache-Control: max-age=0, no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: Wed, 11 Jan 1984 05:00:00 GMT");
header('Content-Type: text/cache-manifest');

$template_path = "/files";
?>
CACHE MANIFEST

CACHE:

<?php
//http://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js
$manifest_file = $_SERVER['DOCUMENT_ROOT'] . $template_path . "/cache-manifest/cache.manifest.php";
$manifest_modtime = filemtime($manifest_file);

$change_occurred = false;
$mod_date = $manifest_modtime; //set to file's mode date as a baseline

function outputCacheFiles($files, $template_path, $server_path, $cache_function)
{
	global $change_occurred, $mod_date, $manifest_file, $manifest_modtime;
		
	foreach($files as $file)
	{
		$file_path = $_SERVER['DOCUMENT_ROOT'] . $server_path . $template_path . $file;
		if(file_exists($file_path))
		{
			$file_modtime = filemtime($file_path);
			if($file_modtime > $manifest_modtime && $change_occurred == false)
			{
				$change_occurred = true;
				$mod_date = $file_modtime;
				touch($manifest_file); //update modified date on cache-manifest file
			}
			
			if($cache_function)
			{
				echo $cache_function($template_path . $file) . "\n";
			} else {
				echo $template_path . $file . "\n";	
			}
		}
	}
}

$static_files = array(
	"/images/template/header-logo.png"	
);

//Files wrapped in the PHP function update_file_cache_url()
$dynamic_files = array(
	#style sheets
	"/css/all.css", 
	
	#javascript
	"/js/site.min.js"
);

//outputCacheFiles(Array, template path, wpmu path, callback function)
outputCacheFiles($static_files, $template_path, "", false);
outputCacheFiles($dynamic_files, $template_path, "", "update_file_cache_url");

/*
$wp_modtime = returnWPModDate();

if($wp_modtime > $mod_date)
{
	$mod_date = $wp_modtime;
	touch($manifest_file);
}
*/

echo "\n#Last Updated: " . date("Y-m-d H:i:s",$mod_date) . "\n";
echo "\n#Revision: 175 \n";
?>

NETWORK:
*