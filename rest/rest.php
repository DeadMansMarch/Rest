<?
/* Liam Pierce, 4/2/18 */

header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Credentials: true");

if (!isset($_REQUEST['resource'])){
	header("HTTP/1.1 403 FORBIDDEN");
}

$resource = preg_replace("/^rest/","",$_REQUEST['resource']);
$method = $_SERVER['REQUEST_METHOD'];

$restfulStack = array(
);

if ($resource === ""){
	header("HTTP/1.1 400 FORBIDDEN");
	die();
}

foreach ($restfulStack as $match=>$onMatch){
	if (preg_match("/^".preg_replace("/\//","\/",$match)."\/?$"."/",$resource,$matchData)){
		if (!isset($onMatch[strtolower($method)])){
			header("HTTP/1.1 400 BAD REQUEST");
			exit();
		}else{
			
			if (count($onMatch["assign"]) != count($matchData) - 1) {
				header("HTTP/1.1 400 BAD REQUEST");
				exit();
			}
			
			foreach(array_slice($matchData,1) as $index=>$assign){
				global $$onMatch["assign"][$index];
				$$onMatch["assign"][$index] = $assign;
			}
			
			if (isset($onMatch[strtolower($method)."-request"])){
				foreach($onMatch[strtolower($method)."-request"] as $k=>$v){
					if (!isset($_REQUEST[$v])){
						header("HTTP/1.1 400 BAD REQUEST");
					}else{
						global $$v;
						$$v = $_REQUEST[$v];
					}
				}
			}
			
			$onMatch[strtolower($method)]();
			flush();
			exit();
		}
	}
}

header("HTTP/1.1 400 BAD REQUEST");
exit();

?>
