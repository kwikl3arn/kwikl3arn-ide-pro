<?php
//error_reporting(0);

header("X-XSS-Protection: 0");
## if the preview button is pressed then it will execute the command below ##
if (!empty($_POST['preview'])) {	

	## if the preview button == Execute it will run PHP code ##

    if ($_POST['preview'] == "Execute") {
        if (!empty($_POST['code'])) {
            $code = $_POST['code'];
            eval("?> $code ");
        }
    } 

	## or if the preview button == Preview it will run HTML, CSS and JS code ##

	else if ($_POST['preview'] == "Preview") {
		
		## Unify CSS, HTML and JS code in one file
        $code = '';
		
		## If the CDN input is not empty then
        if (!empty($_POST['cdn_url'])) {
            $cdn = $_POST['cdn_url'];
			
			# Obtain all CDN urls
			foreach($cdn as $cdn_url) {
				
				## Check the extension file on the CDN url.
				if(parseurl($cdn_url) == 'css'){
					
					## If the CDN extension file is == css then
					$code .= '<link rel="stylesheet" href="'.$cdn_url.'">';
				}
				else if(parseurl($cdn_url) == 'js'){
					
				## If the CDN extension file is == js then
					$code .= '<script type="text/javascript" src="'.$cdn_url.'"></script>';
				}
			}
        }
		
		## Enter the CSS code in the textarea into <style> {CSS Code} </ style> above the HTML code

        if (!empty($_POST['css'])) {
            $code .= '<style>' . $_POST['css'] . '</style>';
        }
		
		
## Enter HTML code in the textarea under the CSS code and above the JS code
        if (!empty($_POST['html'])) {
            $code .= $_POST['html'];
        }
		
		
## Enter the JS code in the textarea into the <script> {JS Code} </ script> under the CSS and HTML code
        if (!empty($_POST['js'])) {
            $code .= '<script>' . $_POST['js'] . '</script>';
        }
		
		## Displays the CSS, HTML and JS codes that have been put together

        echo $code;
    } 


## or if the preview button == Go will run the MySQL syntax ##
	else if ($_POST['preview'] == "Go") {
		
		## Host and username suggestions should not be blank.

        if (!empty($_POST['host']) && !empty($_POST['username']) && isset($_POST['password'])) {
			
			## CMD style ^^
			$style = "<link href='//fonts.googleapis.com/css?' rel='stylesheet' type='text/css'>";
			$style .= "<style>body{background-color:black;font-family: VT323;color:#f2f2f2}table {border-collapse: collapse;}table, td, th {border: 1px solid white;color:white;padding:5px;}</style>";
			echo $style;
            
            $mysqlhost = $_POST['host'];            
            $mysqlusr = $_POST['username'];            
            $mysqlpass = $_POST['password'];
			  //$mysqlpass = '';
            
			## Connection to mysql
           $con= mysqli_connect($mysqlhost, $mysqlusr, $mysqlpass);            
            
            if (!empty($_POST['query'])) {
                
                if (get_magic_quotes_gpc())
                    $_POST['query'] = stripslashes($_POST['query']);
                
				## Specifies the database to be queried
                mysqli_select_db($con,$_POST['dbname']);
                
				## Running a query

                $result = mysqli_query($con,$_POST['query']);
                
                if ($result) {
					
					## Displays the results of the query table
                    if (@mysqli_num_rows($result)) {
                        
?>

            <table>  
                <thead>  
                    <tr>  
                    
<?php
                        
						## Displays all fields in the table

                        // for ($i = 0; $i < mysqli_num_fields($result); $i++) {                            
                            // //echo ('<th>' . mysqli_fetch_field($i,$result). '</th>');    
							// //echo ('<th>' .mysqli_fetch_field_direct($result,$i). '</th>');	
							// echo ('<th>' .mysqli_fetch_assoc($result). '</th>');							
                        // }
						// foreach($result as $val){
							// // echo ('<th>' .$val. '</th>');
							// echo ('<th>' .mysqli_fetch_assoc($result). '</th>');
							
						// }
						
                      
    /* Get field information for all columns */
			$mysqli_data_type_hash = array(
    1=>'tinyint',
    2=>'smallint',
    3=>'int',
    4=>'float',
    5=>'double',
    7=>'timestamp',
    8=>'bigint',
    9=>'mediumint',
    10=>'date',
    11=>'time',
    12=>'datetime',
    13=>'year',
    16=>'bit',
    //252 is currently mapped to all text and blob types (MySQL 5.0.51a)
	252=>'blob',
    253=>'varchar',
    254=>'char',
    246=>'decimal'
);
	
	$count = 0; 	 
    while ($finfo = $result->fetch_field()) {

		
 echo ('<th>' .$finfo->name.'&nbsp;'.$mysqli_data_type_hash[$finfo->type].'('.$finfo->length. ')</th>');
        // printf("Name:     %s\n", $finfo->name);
        // printf("Table:    %s\n", $finfo->table);
        // printf("max. Len: %d\n", $finfo->max_length);
        // printf("Flags:    %d\n", $finfo->flags);
        // // printf("Type:     %d\n\n",$finfo->type);  
   // printf("Type:     %s\n\n",$mysqli_data_type_hash[$finfo->type]);
   
   $count++;
    }
	

    // $result->close();

?>

                    </tr>  
                </thead>                
                <tbody>  
                
<?php
                        
						
## Displays all the contents of the fields in the table
                        while ($row = mysqli_fetch_row($result)) {                            
                            echo ('<tr>');                            
                            for ($i = 0; $i < mysqli_num_fields($result); $i++) {                                
                                echo ('<td>' . htmlentities($row[$i], ENT_QUOTES) . '</td>');                                
                            }                            
                            echo ('</tr>');                            
                        }
                        
?>  

                </tbody> 
            </table>
            
<?php
                     
					
## Displays the query success message in addition to the table. For example, create database
                    } else {                        
                        echo ('Query OK: ' . mysqli_affected_rows() . ' rows affected.');                        
                    }                    
                } 
				
				## Displays an error message because the sql command is wrong

				else {                    
                    echo ('Query Failed: ' . mysqli_error());                    
                }
                
            } else if (empty($_POST['dbname']) && empty($_POST['query'])) {
?>    

			<table>  
                <thead>  
                    <tr>                      
						<th>db_name</th>
                    </tr>  
                </thead>                
                <tbody>  

<?php
                
                $dbs = mysql_list_dbs();   
					
				
## Displays all database names on the server
                for ($i = 0; $i < mysqli_num_rows($dbs); $i++) {                    
                    $dbname = mysqli_select_db($dbs, $i);                    
                    echo ('<tr><td>' . $dbname . '</td></tr>');                    
                }
                
?>

				</tbody> 
			</table>

<?php
            }
            
        }
    }   ## else if the preview button == RUN will run the PGSQL syntax ##
		else if ($_POST['preview'] == "RUN") {
		
		## Host and username suggestions should not be blank.

        if (!empty($_POST['host']) && !empty($_POST['username']) && isset($_POST['password'])) {
			
			## CMD style ^^
			$style = "<link href='//fonts.googleapis.com/css?' rel='stylesheet' type='text/css'>";
			$style .= "<style>body{background-color:#a05555;font-family: VT323;color:#f2f2f2}table {border-collapse: collapse;}table, td, th {border: 1px solid white;color:white;padding:7px;}</style>";
			echo $style;
            
            $pgsqlhost = "host=".$_POST['host'];
			$port="port=5432";
			$db="dbname=".$_POST['dbname'];
            $pgsqlusr = "user=".$_POST['username'];            
            $pgsqlpass = "password=".$_POST['password'];
			  
            
			## Connection to postgres
			
			$con= pg_connect("$pgsqlhost $port $db $pgsqlusr $pgsqlpass");  
			 
          
            if (!empty($_POST['query'])) {
                
                if (get_magic_quotes_gpc())
                    $_POST['query'] = stripslashes($_POST['query']);
                
				## Specifies the database to be queried
               // pg_select($con,$_POST['dbname']);
                
				## Running a query

                $result = pg_query($con,$_POST['query']);
                
                if ($result) {
					
					## Displays the results of the query table
                    if (@pg_num_rows($result)) {
                        
?>

            <table>  
                <thead>  
                    <tr>  
                    
<?php
                        
						## Displays all fields in the table

                        $i = 0;

while ($i < pg_num_fields($result))
{
	$fieldName = pg_field_name($result, $i);
	$fieldtype=pg_field_type($result, $i);
	$length=pg_field_prtlen($result, $fieldName);
	if($length=="")
	{
		echo '<td>' . $fieldName .'</td>';
	}else{
	echo '<td>' . $fieldName ." ".$fieldtype."(".$length. ')</td>';
	}
	$i = $i + 1;
}
						
						
                      
    
    
?>

                    </tr>  
                </thead>                
                <tbody>  
                
<?php
                        
						
## Displays all the contents of the fields in the table
                        while ($row = pg_fetch_row($result)) {                            
                            echo ('<tr>');                            
                            for ($i = 0; $i < pg_num_fields($result); $i++) {                                
                                echo ('<td>' . htmlentities($row[$i], ENT_QUOTES) . '</td>');                                
                            }                            
                            echo ('</tr>');                            
                        }
                        
?>  

                </tbody> 
            </table>
            
<?php
                     
					
## Displays the query success message in addition to the table. For example, create database
                    } else {                        
                       echo ('Query OK: ' . pg_affected_rows() . ' rows affected.');                        
                    }                    
                } 
				
				## Displays an error message because the sql command is wrong

				else {                    
                    echo ('Query Failed: ' . pg_last_error());                    
                }
                
            } else if (empty($_POST['dbname']) && empty($_POST['query'])) {
?>    

			<table>  
                <thead>  
                    <tr>                      
						<th>db_name</th>
                    </tr>  
                </thead>                
                <tbody>  

<?php
                
               // $dbs = mysql_list_dbs();   
					
				
## Displays all database names on the server
                // for ($i = 0; $i < pg_num_rows($dbs); $i++) {                    
                    // $dbname = pg_select($dbs, $i);                    
                    // echo ('<tr><td>' . $dbname . '</td></tr>');                    
                // }
                
?>

				</tbody> 
			</table>

<?php
            }
            
        }
    }
}

## Function to check url CDN whether CSS or JS ##
function parseurl($url) {
    return preg_replace("#(.+)?\.(\w+)(\?.+)?#", "$2", $url);
}

?> 
