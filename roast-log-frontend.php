<?php /* Template Name: Portal - Roast Log 
 */
get_header();
date_default_timezone_set('America/Los_Angeles');
$timeNow = gmdate('H:i:s', time() - 943945200);
$dateNow = date('Y-m-d');
global $wpdb;
$date = date('Y-m-d');

if ($_SERVER["REQUEST_METHOD"] == "POST") {      
$totalTime = $_POST['finishingTime'];
if ($_POST["roastExit"] == 1 AND strlen($totalTime) == 5) {
	$totalTime = "00:" . $totalTime;
} else if ($_POST["roastExit"] == 1 AND strlen($totalTime) == 4){
	$totalTime = "00:0" . $totalTime;
} else if ($_POST["roastExit"] == 2 AND strlen($totalTime) ==5) {
	$totalTime = "00:" . $totalTime;
} else if ($_POST["roastExit"] == 2 AND strlen($totalTime) ==4) {
	$totalTime = "00:" . substr($totalTime,0,2).':'.substr($totalTime,2,2);
} else if ($_POST["roastExit"] == 2 AND strlen($totalTime) ==3) {
	$totalTime = "00:0" . substr($totalTime,0,1).':'.substr($totalTime,1,2);
}
echo "<input style='display: none;' id='isLoggedIn' name='isLoggedIn' value='" . $_POST['selectedUser'] . "'>";
if ($_POST['intensity'] != "") {
$lotNumber = $wpdb->get_var("SELECT Lot_Number FROM " . $wpdb->prefix . "roast_details WHERE Country_Name = '". $_POST['origin'] . "' AND Active = 1");
$wpdb->insert($wpdb->prefix . 'roast_db', array(	'user' 				=> $_POST["selectedUser"],
													'roastDate' 		=> $date,
										            'coffeeChoice' 		=> $_POST['origin'],
										            'roastChoice' 		=> $_POST['intensity'],
										            'greenCoffee' 		=> $_POST['coffeeWeight'],
										            'roastLength' 		=> $totalTime,
										            'roastedCoffee' 	=> (.82 * $_POST['coffeeWeight']),
													'roastComments' 	=> $_POST['comment'],
													'roastStopType' 	=> $_POST['roastExit'],
													'roastStart' 		=> $_POST['startTime'],
													'roastStop'			=> $_POST['endTime'],
													'lotNumber'			=> $lotNumber,
													'roastTime' 		=> $_POST['startTime']));
}}
?>
<script type="text/javascript">
	jQuery(document).ready(function() {
    jQuery(".breadcrumb-trail").hide();
    jQuery(".breadcrumb").hide();
    jQuery(".widget").hide();
    var hasBeenSet = 0;
    var button;
    var selectedUser = 0;
    var intensity;
    var weight;
    var hasBeenClicked = 0;
    var roastComments;
    var finishingTime;
    userSelection();

    function userSelection() {
        if (jQuery("#isLoggedIn").length) {
            selectedUser = jQuery("#isLoggedIn").attr("value");
            roastSelection();
        } else {
            //This function gets the user information from the human.
            jQuery("#userNames").show();
            jQuery(".users").click(function() {
                if (typeof jQuery(this).attr("data-user") != 'undefined') {
                    selectedUser = jQuery(this).attr("data-user");
                    jQuery("#userNames").hide();
                    roastSelection();
                    //Below are the statements executed in the event that the other button is selected.
                }
                if (jQuery(this).attr("data-other") == 'other') {
                    jQuery("#userNames").fadeOut(0);
                    jQuery("#enterUser").fadeIn();
                    jQuery('#userName').keyup(function() {
                        jQuery('#submitUser').attr('disabled', false)
                    });
                    jQuery("#submitUser").click(function() {
                        selectedUser = jQuery("#userName").attr("value");
                        jQuery("#enterUser").hide();
                        roastSelection();
                    })
                }
            })
        }
    }


    function roastSelection() {
        jQuery("#selectedUser").val(selectedUser);
        jQuery(".roastedToday").show();
        jQuery("#roastSelection").show();
        jQuery(".button").click(function() {
            // Checks to see if any button has already been pressed.
            if (hasBeenSet == 1 && typeof button != 'undefined' && typeof jQuery(this).attr("data-origin") != 'undefined' && button != jQuery(this).attr("data-origin")) {
                jQuery("." + button + "-div").slideUp();
                button = jQuery(this).attr("data-origin");
                jQuery("." + button + "-div").slideDown();
            } else if (hasBeenSet == 1 && typeof button != 'undefined' && typeof jQuery(this).attr("data-origin") != 'undefined' && button == jQuery(this).attr("data-origin")) {
                jQuery("." + button + "-div").slideUp();
                hasBeenSet = 0;
            } else if (hasBeenSet == 1 && typeof jQuery(this).attr("data-origin") == 'undefined' && jQuery("#roastSelection").attr("style") != 'display: none;') {
                intensity = jQuery(this).attr("value");
                weight = jQuery("#lbs").attr("value");
                jQuery("#roastSelection").fadeOut(0);
                jQuery("#startRoast").show();
                jQuery("." + button + "-div").fadeOut();
                roastCompletion(weight);
            } else if (hasBeenSet == 0 && typeof jQuery(this).attr("data-origin") != 'undefined') {
                // Fades in all of the buttons of the class of the origin button.
                button = jQuery(this).attr("data-origin");
                jQuery("." + button + "-div").slideDown();
                hasBeenSet = 1;
            }
        })
    }
    var callback = function(event) {
        event.preventDefault();
        finishingTime = jQuery('#timeField').attr("value");
        roastComment = jQuery("#roastComment").attr("value");
        jQuery("#roastExit").val('2');
        var time = new Date();
        var endTime = (
            ("0" + time.getHours()).slice(-2) + ":" +
            ("0" + time.getMinutes()).slice(-2) + ":" +
            ("0" + time.getSeconds()).slice(-2));
        jQuery('#endTime').val(endTime);
        submitInfo(finishingTime);
    }

    function roastCompletion() {
        var totalSeconds;
        var hasBeenClicked = 0;
        var beforeColon = 0;
        finishingTime = 0;
        //this is the brains behind the stopwatch and associated time math and string concatenation
        jQuery(".stopWatch").click(function(event) {
            //this if loop checks to see if the clock has been clicked
            if (hasBeenClicked == 0) {
                event.preventDefault();
                hasBeenClicked = 1;
                var time = new Date();
                var startTime = (
                    ("0" + time.getHours()).slice(-2) + ":" +
                    ("0" + time.getMinutes()).slice(-2) + ":" +
                    ("0" + time.getSeconds()).slice(-2));
                jQuery("#startTime").val(startTime);
                var start = new Date;
                setInterval(function() {
                    totalSeconds = Math.round((new Date - start) / 1000);
                    if (totalSeconds < 10) {
                        afterColon = "0" + totalSeconds;
                    } else if (totalSeconds <= 59) {
                        var afterColon = totalSeconds;
                    } else if (totalSeconds >= 60) {
                        totalSeconds = 0;
                        var afterColon = "00";
                        beforeColon = beforeColon + 1;
                        start = new Date;
                    }
                    jQuery('#timeDisplay').text(beforeColon + ":" + afterColon);
                }, 1000);
            } else if (hasBeenClicked == 1 && jQuery("#timeField").attr("value") != '') {
                jQuery('a').click(function(event) {
                	callback();
                	});
                finishingTime = jQuery("#timeField").attr("value");
                roastComments = jQuery("#roastComments").attr("value");
                jQuery("#roastExit").val('2');
                var time = new Date();
                var endTime = (
                    ("0" + time.getHours()).slice(-2) + ":" +
                    ("0" + time.getMinutes()).slice(-2) + ":" +
                    ("0" + time.getSeconds()).slice(-2));
                jQuery('#endTime').val(endTime);
                submitInfo(finishingTime);
            } else if (hasBeenClicked == 1 && jQuery("#timeField").attr("value") == '') {
                jQuery('a').click(function(event) {
                    event.preventDefault();
                    finishingTime = jQuery('#timeDisplay').text();
                    roastComment = jQuery("#roastComment").attr("value");
                    jQuery("#roastExit").val('1');
                    var time = new Date();
                    var endTime = (
                        ("0" + time.getHours()).slice(-2) + ":" +
                        ("0" + time.getMinutes()).slice(-2) + ":" +
                        ("0" + time.getSeconds()).slice(-2));
                    jQuery('#endTime').val(endTime);
                    submitInfo(finishingTime);
                })
                finishingTime = jQuery('#timeDisplay').text();
                jQuery("#roastExit").val('1');
                var time = new Date();
                var endTime = (
                    ("0" + time.getHours()).slice(-2) + ":" +
                    ("0" + time.getMinutes()).slice(-2) + ":" +
                    ("0" + time.getSeconds()).slice(-2));
                jQuery('#endTime').val(endTime);
                submitInfo(finishingTime);
            }
        })
        jQuery("#timeField").keypress(function(event) {
            if (event.which == 13) {
                callback(event);
            };
        });
    }


    function submitInfo(finishingTime) {
        jQuery("#origin").val(button);
        jQuery("#roastWeight").val(weight);
        jQuery("#finishingTime").val(finishingTime);
        jQuery("#intensity").val(intensity);
        jQuery("#comment").val(jQuery("#roastComments").attr("value"));
        jQuery("#timeDisplay").closest("form").trigger('submit');
    };
});
</script>
<?php	global $wpdb;	$origins = $wpdb->get_col("SELECT DISTINCT(Country_Name) FROM ". $wpdb->prefix . "roast_details ORDER BY Country_Name");?>
	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">
			<?php woo_content_before(); ?>
			    <div id="content" class="col-full">
			    	<div id="main-sidebar-container">    
			            <!-- #main Starts -->
			            <?php woo_main_before(); ?>
			            <?php woo_loop_before(); ?>
			            <section id="userNames" style='display:none'>
			            	<h1 style='text-align: center;'>Select a Roaster:<br><br></h1>
			            		<form method="POST" action="">
			            			<table border=0><th><tr style="width: 100%">
			            					<td style="width:33%"><input type="button" class='button users' style='text-align: center; font-size: 150%; width: 100%;' data-user='Rob' name='Rob' value='Rob'></td>
			            					<td style="width:850px"><input type="button" class='button users' style='text-align: center; font-size: 150%; width: 100%;' data-user='Tanner' name='Tanner' value='Tanner'></td>
			            					<td style="width:33%"><input type="button" class='button users' style='text-align: center; font-size: 150%; width: 100%;' data-other='other' name='Other' value='other'></td>
			            			</tr></th></table>
			            		</form>
			            </section>
			            <section id='enterUser' style='display:none'>
			            	<h1 style='text-align: center;'>Enter a username:</h1>
			            			<table>
			            				<th style='width: 100%; padding: 15px'><tr style='width: 100%; padding: 15px'>
			            					<td><input id='userName'style="width:100%" type='text' name="userName" placeholder="Lord Farquad"></td>
			            					<td><input type ="button" id='submitUser' class='button' name='submitUser' value='submit-eth' disabled="true"></td>
			            					</tr></th>
			            			</table>
					            </section>
					            <section id="roastSelection" style='display: none;'>
										<table style="width: 100%"><th><tr>
										<td style="text-align: left; width: 75%"><h1>Choose an Origin and Roast Size</h1></td>
										<td style="content-align: right;"><a href="" class="submit" style="text-align: right; background-color: #523D26;">Log Out</a></td></tr><tr style="height: 100px">
				            			<td style="display: table-cell; vertical-align: middle; left: 0px; "><input class='styleRange' style=' width: 53%; display: inline-block; ' id='weight' type='range' name='coffee weight' min="0" max="50" step="5" value="50" oninput="outputUpdate(value)"></td>
				            			<td style="vertical-align: middle;"><output for='weight' style="font-size: 3em" id='lbs'>50</output></td>
				            			</tr></th></table>
				            			<script>
				            				function outputUpdate(lbs) {
				            				document.querySelector('#lbs').value = lbs;
				            				}
				            			</script>
									<table border="0">
										<?php
											// This Foreach will populate the Origin Buttons and the nested Foreach displays the available roast intensities.
											$buttonCount = 0;
											foreach($origins as $origin) {
												$intensitiesSerialized = $wpdb->get_var("SELECT Roasts_Available FROM ". $wpdb->prefix . "roast_details WHERE Country_Name = '". $origin . "' ORDER BY Roasts_Available");
												$intensities = unserialize($intensitiesSerialized);
												$originNoSpaces = preg_replace("/[\s_]/", "", $origin);
												if ($buttonCount %3 == 0) {$startRow = "</tr><tr data-row='" . (($buttonCount/3)+1) . " style='width: 100%''>";
													} else if ($buttonCount %3 == 0 AND $buttonCount /3 == 0){$startRow = "<tr 'width: 850px' data-row='1'>";
													} else {$startRow = "";}
												$buttonCount ++;
												echo $startRow . "<td style='width: 300px; padding: 10px'><input class='button' style='width:100%; padding: 10px 30px; font-size: 2em;' data-origin='" . $originNoSpaces . "' type='button' name='country' value='" . $origin . "'>";
												// This Foreach populates the available roast intensities.
												foreach($intensities as $intensity) {
													echo "<div class='". $originNoSpaces . "-div" . "' style='display: none; margin: 0 15px;'><input class='button' style='padding: 10px; font-size: 2em; background-color: #523D26;' type='button' name='roasts' value='" . $intensity . "'></div>";
													}
													echo "</td>";
												}
										?>
									</table>
									</section>
									<section id='startRoast' style="display: none;">
										<form method="POST" action="">
										<table><tr><td><h1 style="font-size: 2.5em; text-align: center; text-align: center;">Start and stop the roast with the stopwatch</h1></td><td style=" vertical-align: middle"><input style="display: center; margin: 0 15px;" type="submit" value="Start Over"></td></tr></table>
										<table>
											<tbody>
												<tr><td></td><td style="text-align: center; padding: 30px 0 0 0;"><input id="timeField" type="tel" class='text' placeholder="Enter Time Manually" style="display: center; text-align: center; padding: 10px; width: 85%;" value=""></td></tr>
												<tr><td style="text-align: center; width: 50%" ><input type="image" class="stopWatch" id="stopWatch" align="bottom" src="<?php echo get_option('siteurl') ?>/wp-content/uploads/2015/03/Timer-Brown.png" style="border: none; width: 50%; padding: 5%; position: relative;">
													<a href=""><h1 class="stopWatch" id="timeDisplay" style="position: absolute; top: 59%; font-weight: 30px; color: white; text-align: center; width: 50%;">0:00</h1></a>
														</td>
													<td style="text-align: center" align="left"><textarea  id="roastComments" placeholder="Enter any special notes!" style="width: 85%; height: 340px; vertical-align: bottom; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; box-sizing: border-box;"></textarea></td>
												</tr>
											</tbody>
										</table>
											<input type='hidden' id="selectedUser" name="selectedUser" value="">
											<input type='hidden' id="origin" name="origin" value="">
											<input type='hidden' id="roastWeight" name="coffeeWeight" value="">
											<input type='hidden' id="intensity" name="intensity" value="">
											<input type='hidden' id='comment' name='comment' value="">
											<input type='hidden' id='roastExit' name='roastExit' value=''>
											<input type='hidden' id='finishingTime' name='finishingTime' value=''>
											<input type='hidden' id='startTime' name='startTime' value="">
											<input type='hidden' id='endTime' name='endTime' value="">
										</form>
									</section>
									<?php
									$roastedToday = $wpdb->get_var("SELECT SUM(greenCoffee) FROM ". $wpdb->prefix . "roast_db WHERE roastDate = CURDATE()");
									?>
									<h1 class='roastedToday' style='text-align: center; display: none;'><?php echo $roastedToday; ?>LBS Roasted Today so Far</h1>
									
											<style type="text/css">
												input[type='range']::-webkit-slider-thumb {
												    -webkit-appearance: none !important;
												    background: url('<?php echo get_option('siteurl') ?>/wp-content/uploads/2015/03/images-e1426632763847.png') no-repeat center center;
												    height:80px;
												    width:75px;
												    border-top-right-radius:80px;
												    border-top-left-radius:15px;
												    border-bottom-right-radius:15px;
												    border-bottom-left-radius:80px;
												}
												input[type=range]:focus {
												    outline: none;
												}
											</style>

							<?php woo_loop_after(); ?>
					</div>
					<?php get_sidebar('alt'); ?>
			    </div>
			<?php woo_content_after(); ?>
		</main>
	</div><?php get_footer();?>