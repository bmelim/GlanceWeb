<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require("phpMQTT.php");

$dbsettings=array();
$dbmessages=array();

//Read the values from the database
$db = new SQLite3('clp.db') or die('Unable to open database');

$db->busyTimeout(1000); // 1 second

$results = $db->query("SELECT setting,value FROM settings");
while ($row = $results->fetchArray())
        $dbsettings[$row['setting']]=trim($row['value']);

$results2 = $db->query("select s.setting, s.value, (m.host || '/' || m.name || '/' || m.value) as message from settings s, messages m where s.value == m.id and m.action == 1");
while ($row2 = $results2->fetchArray())
        $dbmessages[$row2['setting']]=trim($row2['message']);
?>

<!DOCTYPE html>
	<html lang="en">
		<head>
		    <meta charset="utf-8">
		    <meta http-equiv="X-UA-Compatible" content="IE=edge">
		    <meta name="viewport" content="width=device-width, initial-scale=1">
		    <meta name="description" content="">
		    <meta name="author" content="">
		    <link rel="icon" href="../../favicon.ico">


		    <!-- Bootstrap core CSS -->
		    <link href="css/bootstrap.min.css" rel="stylesheet">

		    <!-- Custom styles for this template -->
		    <link href="css/starter-template.css" rel="stylesheet">

		    <script src="../../assets/js/ie-emulation-modes-warning.js"></script>

		    <!-- Bootstrap -->
		    <link href = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css" rel = "stylesheet">

		    <!-- Custom styles for this template -->
		    <link href="css/grid.css" rel="stylesheet">

        	    <title>Glance Settings</title>



		    <script>
		    	function autoSubmit()
			{
		    		var formObject = document.forms['settingsform'];
				formObject.submit();
			}

		    </script>

		</head>

  		<body>
			<form action = '' method = 'post' name='settingsform' id='settingsform'>

    			<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
				<div class="container">
        				<div class="navbar-header">
          					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
					        	<span class="sr-only">Toggle navigation</span>
	            					<span class="icon-bar"></span>
        	    					<span class="icon-bar"></span>
            						<span class="icon-bar"></span>
            						<span class="icon-bar"></span>
          					</button>
          					<a class="navbar-brand" href="#"></a>
        				</div>

        				<div class="collapse navbar-collapse">
				      		<ul class="nav navbar-nav">
				        		<li class="active"><a href="#">Control</a></li>
            						<li><a href="displayandbuttons.php">Display and Buttons</a></li>
				        		<li><a href="eventsandactions.php">Events and Actions</a></li>
				        		<li><a href="config.php">Configuration</a></li>
				          	</ul>
        				</div><!--/.nav-collapse -->
      				</div>
    			</div>

			<?php

    			echo "<div class='container'>";
      				echo "<div class='starter-template'>";
        				echo "<p class='lead'><br></p>";
					if (!empty($_POST)) //check if form was submitted
					{
					        $pressed_button = "";
					        foreach ($_POST as $key => $value)
					        {
					                foreach($value as $k => $v)
					                {
					                        $pressed_button = $k;
					                }
					        }

					        switch ($pressed_button) 
						{
					            case "0":
						    	$button = "button0" . "on-";
					                break;
						    case "1":
					                $button = "button0" . "off-";
					                break;
					            case "2":
					                $button = "button1" . "on-";
					                break;
					            case "3":
					                $button = "button1" . "off-";
					                break;
					            case "4":
					                $button = "button2" . "on-";
					                break;
					            case "5":
					                $button = "button2" . "off-";
					                break;
					            case "6":
					                $button = "button3" . "on-";
					                break;
					            case "7":
					                $button = "button3" . "off-";
					                break;
					            case "8":
						        $button = "button4" . "on-";
					                break;
					            case "9":
						        $button = "button4" . "off-";
					                break;
					        }

        					$mqtt = new phpMQTT($dbsettings['mosbrokeraddress'], $dbsettings['mosbrokerport'], $dbsettings['name']);
					        if ($mqtt->connect(true, NULL, $dbsettings['mosusername'], $dbsettings['mospassword']))
        					{
					                $message = "";
					                $i = 0;
					                while($i < 5)
					                {
					                        $value = $dbsettings[$button . $i];
					                        if ($value=="-")
					                                break;

					                        $message = $dbmessages[$button . $i];
					                        $mqtt->publish($dbsettings['mostopic'], $message);
					                        $i++;
					                }
					                $mqtt->close();
					        }
					        else
					        {
					            echo "Time out!\n";
					        }

					} //If the form was submitted. 

					echo "<div class='container'>";


					        if (strcmp($dbsettings['webcolour'], "green")==0)
					                echo "<div class='col-md-4' style = 'background-color:rgba(0,255,0,.2)'>";
					        elseif (strcmp($dbsettings['webcolour'], "red")==0)
					                echo "<div class='col-md-4' style = 'background-color:rgba(255,0,0,.5)'>";
					        elseif (strcmp($dbsettings['webcolour'], "orange")==0)
					                echo "<div class='col-md-4' style = 'background-color:rgba(255,165,0,.1)'>";

					        		echo "<h2>Control</h2>";

								echo "<table>";
						                        $x = 0;
						                        $button_counter = 0;
				                		        while ($x < 10) 
									{
						                                $buttonxalias = "button" . $button_counter  . "alias";
                                						$buttonxtype = $dbsettings["button" . $button_counter  . "type"];

				                		                if (strlen($dbsettings[$buttonxalias])==0)
                                						        break;

						                                echo "<tr>";
							                                echo "<td align='center' colspan='2'><B>";
							                                echo $dbsettings[$buttonxalias];
				                		        	        echo "</B></td>";
				                        		        echo "</tr>";

						                                echo "<tr>";
                			                				if($buttonxtype=="togglebutton")
				        			                        {
                                							        echo "<td>";
						                                                echo "<input type='submit' name='" .  "button[" . $x  . "]' value='&nbsp &nbsp on &nbsp &nbsp'>";
                			                				        echo "</td>";
								                        }
				                                			else
			                                				{
							                                        echo "<td colspan='2'>";
                                							                echo "<input type='submit' name='" .  "button[" . $x  . "]' value='&nbsp &nbsp Do it &nbsp &nbsp' style='width:100%'>";
				                                        			echo "</td>";
			                                				}

							                                $x++;

						        	                        echo "<td>";

			                        				        	if($buttonxtype=="togglebutton")
							                                        	echo "<input type='submit' name='" .  "button[" . $x . "]' value=' &nbsp &nbsp off &nbsp &nbsp'>";
                                							echo "</td>";

							                                $x++;

		                                				echo "</tr>";

						                                echo "<tr><td>&nbsp</td><td>&nbsp</td></tr>";

                                						$button_counter++;
			                        			} //while

								echo "</table>";

       							echo "</div>"; //col-md-4

						echo "</div>"; //container


      					echo "</div>"; //.starter template -->

				echo "</div>"; //container 

			echo "</form>";

		echo "</body>";


    //Bootstrap core JavaScript
    //================================================== -->
    //Placed at the end of the document so the pages load faster -->
    echo "<script src='https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js'></script>";

    //Include all compiled plugins (below), or include individual files as needed -->
    echo "<script src = '//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js'></script>";

echo "</html>";

?>
