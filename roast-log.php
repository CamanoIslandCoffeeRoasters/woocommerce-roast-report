<?php
/*
Plugin Name: Woocommerce Roast Log
Plugin URI: http://camanoislandcoffee.com
Description: Roast information for logging and accounting purposes.
Version: 1.0
Author: Tanner Legasse	
Author URI: 
License: GNU
*/
if (!defined('RL_URL'))
    define('RL_URL', plugins_url() . '/' . basename(dirname(__FILE__)) . '/');

if (!defined('ROAST_REPORT_FRONTEND'))
    define('ROAST_REPORT_FRONTEND', untrailingslashit( plugin_dir_path(__FILE__)));

if (!defined('RL_PATH'))
    define('RL_PATH', plugin_dir_path(__FILE__));

register_activation_hook( __FILE__, 'activationTables' );
if (isset($_POST)) {
	//var_dump($_POST);
}
function roastMenu()
{
     add_menu_page("Roast Log", "Roast Report", "manage_options", "roast-options", 'roast_options_callback', '/wp-content/plugins/woocommerce-roast-report/favicon.ico', 75);
     add_submenu_page('roast-options', 'Roast Statistics', 'Roast Statistics', 'manage_options', 'roast_dashboard', 'roast_options');
     add_submenu_page('roast-options', 'Add Roast', 'Add Roast', 'manage_options', 'add-roast', 'add_roast_callback');
}

add_action('admin_menu', 'roastMenu');

function checkForCountry($country) {
	global $wpdb;
	$countryCheck = $wpdb->get_col("SELECT Country_Name FROM ". $wpdb->prefix . "roast_details WHERE Country_Name = '" . $country . "' AND Active = 1");
    if (isset ($countryCheck)) {
    	return true;
    } else {
    	return false;
    };
}

function roast_options() {
	global $wpdb;
    global $current_user;
	$roastDetails = $wpdb->prefix . "roast_details";
    if (isset($_POST['submit'])) {
        $date = date('Y-m-d');
        get_currentuserinfo();
        $roastsAbailable = serialize($_POST['roasts']);
		if (checkForCountry($_POST['country'])) {
			$date = date("Y/m/d");
			$wpdb->update($roastDetails, array('Active' => '0', 'End_Date' => $date), array('Active' => '1', 'Country_Name' => $_POST['country']));
		}
        $wpdb->insert($roastDetails, array(
            'User_Name' => $current_user->display_name,
            'Entry_Date' => $date,
            'Country_Name' => $_POST['country'],
            'Lot_Number' => $_POST['lotnum'],
            'Roasts_Available' => $roastsAbailable));
          echo "<p style='background-color: #4FFF5E;'>Thanks! The new coffee has been added.</p> ";
		
		$country = $_POST['country'];
		$args = array(	'post_type'    =>   array('product', 'product_variation'),
	                    'post_status'  =>   'publish',
	                    'product_cat'  =>   $country);
		$lotNumber = $_POST['lotnum'];
		$query = new WP_Query($args);
		$posts = $query->posts;
		foreach ($posts as $post) {
		  	update_post_meta($post->ID, 'lot_number', $lotNumber );
		}
	};
?>

	<form action="" method="POST">
		<p><b>Enter a new roast!</b></p>
		<select id="country" name="country">
		<option value="Brazil">Brazil</option>
		<option value="Cascadia">Cascadia</option>
		<option value="Chile">Chile</option>
		<option value="Colombia">Colombia</option>
		<option value="Estonia">Estonia</option>
		<option value="Ethiopia">Ethiopia</option>
		<option value="Guatemala">Guatemala</option>
		<option value="Guinea">Guinea</option>
		<option value="Honduras">Honduras</option>
		<option value="Indonesia">Indonesia</option>
		<option value="Mexico">Mexico</option>
		<option value="PNG">Papua New Guinea</option>
		<option value="Peru">Peru</option>
		<option value="Sumatra">Sumatra</option>
		<option value="Uganda">Uganda</option>
		</select><br>
		<input Type="text" name="lotnum" placeholder="Lot Number" /> <br>
		<label><input Type="checkbox" name="roasts[]" value="Light"/> Light</label><br>
		<label><input Type="checkbox" name="roasts[]" value="Medium"/> Medium</label><br>
		<label><input Type="checkbox" name="roasts[]" value="Dark"/> Dark</label><br>
		<label><input Type="checkbox" name="roasts[]" value="Reserve"/> Reserve</label><br>
		<label><input Type="checkbox" name="roasts[]" value="Decaf"/> Decaf</label><br>
		<input Type="submit" action= "" name="submit" value="Submit New Origin"/>
	</form>

<?php
	global $wpdb;	
	$origins = $wpdb->get_col("SELECT Country_Name FROM ". $wpdb->prefix . "roast_details WHERE Active = '1' ORDER BY Country_Name");
	echo "<table style ='border: 1px black; width: 100%'>";
	echo "<tr style='background-color: #FFF; width: 100%; '><td>Origin</td><td>Light</td><td>Medium</td><td>Dark</td><td>Reserve</td><td>Decaf</td><td>Lot Number</td><td>Date Entered</td></tr>";
	$rowNumber = 1;
	foreach($origins as $origin) {
		$intensitiesSerialized = $wpdb->get_var("SELECT Roasts_Available FROM ". $wpdb->prefix . "roast_details WHERE Country_Name = '". $origin . "' ORDER BY Roasts_Available");
		$lotNumber = $wpdb->get_var("SELECT Lot_Number FROM ". $wpdb->prefix . "roast_details WHERE Country_Name = '". $origin . "' AND Active = 1");
		$startDate = $wpdb->get_var("SELECT Entry_Date FROM ". $wpdb->prefix . "roast_details WHERE Country_Name = '". $origin . "' AND Active = 1");
		$intensities = unserialize($intensitiesSerialized);
		$originNoSpaces = preg_replace("/[\s_]/", "", $origin);
		if ($rowNumber %2 == 0) {$color = "#FFF";} else {$color = "";}
		echo "<tr style='background-color: " . $color . ";'><td style='padding: 0 10px;'><p data-origin='" . $originNoSpaces . "'>" . $origin . "</p></td>";
		// This Foreach populates the available roast intensities.
		if (in_array("Light" , $intensities)) {echo "<td style='background-color: #DEB887; width: 11%'><p> </p></td>";} else {echo "<td style='width: 11%'><p> </p></td>";}
		if (in_array("Medium" , $intensities)) {echo "<td style='background-color: #DEB887; width: 11%'><p> </p></td>";} else {echo "<td style='width: 11%'><p> </p></td>";}
		if (in_array("Dark" , $intensities)) {echo "<td style='background-color: #DEB887; width: 11%'><p> </p></td>";} else {echo "<td style='width: 11%'><p> </p></td>";}
		if (in_array("Reserve" , $intensities)) {echo "<td style='background-color: #DEB887; width: 11%'><p> </p></td>";} else {echo "<td style='width: 11%'><p> </p></td>";}
		if (in_array("Decaf" , $intensities)) {echo "<td style='background-color: #DEB887; width: 11%'><p> </p></td>";} else {echo "<td style='width: 11%'><p> </p></td>";}
		echo "<td><p class='LotNumber'" . " name='lotNumber'>" . $lotNumber . "</p></td>";
		echo "<td><p class='startDate'" . " name='startDate'>" . $startDate . "</p></td>";
		$rowNumber ++;
		echo "<br></tr>";
	}
	echo "</table>";
}

function add_roast_callback() {
     echo '<div class="wrap">';
          include RL_PATH . 'includes/add-roast.php';
     echo '</div>';
}

function activationTables() {
    global $wpdb;
    $roast_table = $wpdb->prefix . "roast_db";
    $create_table =   "CREATE TABLE IF NOT EXISTS " . $roast_table . " (
                      id int(11) NOT NULL AUTO_INCREMENT,
                      roastDate date NOT NULL,
                      roastTime time NOT NULL,
                      coffeeChoice text COLLATE utf8_unicode_ci NOT NULL,
                      roastChoice text COLLATE utf8_unicode_ci NOT NULL,
                      greenCoffee int(11) NOT NULL,
                      roastedCoffee int(11) NOT NULL,
                      user text COLLATE utf8_unicode_ci NOT NULL,
                      roastStart time NOT NULL,
                      roastStop time NOT NULL,
                      roastLength time NOT NULL,
                      roastComments varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                      roastStopType int(11) NOT NULL,
                      PRIMARY KEY  (id),
                      UNIQUE KEY id (id)
                      ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
    $detailsTable = $wpdb->prefix . "roast_details";
    $createDetailsTable = "CREATE TABLE IF NOT EXISTS " . $detailsTable . "(
  					  id int(11) NOT NULL AUTO_INCREMENT,
  					  Entry_Date date NOT NULL,
					  End_Date date NOT NULL,
					  Country_Name varchar(50) CHARACTER SET utf8 NOT NULL,
					  Lot_Number varchar(50) CHARACTER SET utf8 NOT NULL,
					  Roasts_Available varchar(255) NOT NULL,
					  User_Name varchar(50) CHARACTER SET utf8 NOT NULL,
					  Active tinyint(1) DEFAULT '1',
					  PRIMARY KEY  (ID)
					  ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $create_table );
    dbDelta( $createDetailsTable );
    }
function roast_options_callback() {
    echo '<div class="wrap">';
     include RL_PATH . 'includes/roast-report.php';
    echo '</div>';
}
?>