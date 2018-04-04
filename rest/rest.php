<?
/* Liam Pierce, 4/2/18 */

header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Credentials: true");

include_once("/var/www/cook/api/api.php");


if (!isset($_REQUEST['resource'])){
	header("HTTP/1.1 403 FORBIDDEN");
}

$resource = preg_replace("/^rest/","",$_REQUEST['resource']);
$method = $_SERVER['REQUEST_METHOD'];

$restfulStack = array(
	"recipes/([0-9]+)"=>array(
		"assign"=>array("id"),
		"get"=>function(){
			global $id;
			
			echo json_encode(array("recipe"=>getRecipe($id)));
		}
	),
	
	"recipes"=>array(
		"assign"=>array(),
		"post-request"=>array("name"),
		"post"=>function(){
			global $name;
			
			echo json_encode(array("id"=>createRecipe($name,$_SESSION["userId"])));
		},
		"get"=>function(){
			echo json_encode(getRecipes());
		}
	),
	
	"users/([0-9]+)/recipes"=>array(
		"assign"=>array("uid"),
		"get"=>function(){
			global $uid;
	
			echo json_encode(getRecipes($uid));
		}
	),
	
	"users"=>array(
		"assign"=>array(),
		"post-request"=>array("email","password"),
		"post"=>function(){
			global $email;
			global $password;
		
			echo json_encode(array("success"=>generateUser($email,$password)));
		}
	),
	
	"users/auth"=>array(
		"assign"=>array(),
		"post-request"=>array("email","password"),
		"post"=>function(){
			global $email;
			global $password;
			echo json_encode(array("success"=>auth($email,$password),"uid"=>$_SESSION["userId"]));
		}, 
	),
	
	"users/emails"=>array(
		"assign"=>array(),
		"get-request"=>array("email"),
		"get"=>function(){
			global $email;
	
			echo json_encode(array("exists"=>userEmailExists($email)));
		}, 
	),
	
);

if ($resource === ""){
	header("HTTP/1.1 403 FORBIDDEN");
	die();
}

foreach ($restfulStack as $match=>$onMatch){
	if (preg_match("/^".preg_replace("/\//","\/",$match)."\/?$"."/",$resource,$matchData)){
		if (!isset($onMatch[strtolower($method)])){
			//var_dump($onMatch);
			header("HTTP/1.1 401 BAD REQUEST");
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

header("HTTP/1.1 405 BAD REQUEST");
exit();

?>