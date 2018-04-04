This rest API, when placed within a document root, can be accessed both through a virtual subdomain or the local site.
The URL matching system for use within a local site is made to use a folder named "rest", so that folder is included on this git.

The restfulStack array must be filled with the following formatted data for each entry.

$restfulStack = array(
  "users/([0-9]+)"=>array( //Key to each element is entered into regex - URL variables are defined by the "assign" array in order of capture.
      "assign"=>array("id"), //Assign captured
      "get"=>function(){ //Function response to get method.
        global $id; //Will be defined by assign data.

        doSomeWork();
      },
      
      "post-request"=>array("email","password"),
      "post"=>function(){ 
        global $id; //Will be defined by assign data.
        global $email; //Will be defined based on post data. 
        global $password; //Will be defined based on post data. 
      },
      
      strtolower($method)."-request"=>array("email","password"), //"lowercase(method)-request"
      strtolower($method)=>function(){ 
        global $id; //Will be defined by assign data.
        global $email; //Will be defined based on strtolower($method) data. 
        global $password; //Will be defined based on strtolower($method) data. 
        
      }
  ) 
)
