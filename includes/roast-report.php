<?php 

if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
date_default_timezone_set('America/Los_Angeles');



class Roast_Table extends WP_List_Table {

     
    function __construct(){
        global $status, $page;
                
        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'Roast',     //singular name of the listed records
            'plural'    => 'Roasts',    //plural name of the listed records
            'ajax'      => true        //does this table support ajax?
        ) );
        
    }

    
    /** ************************************************************************
     * Recommended. This method is called when the parent class can't find a method
     * specifically build for a given column. Generally, it's recommended to include
     * one method for each column you want to render, keeping your package class
     * neat and organized. For example, if the class needs to process a column
     * named 'title', it would first see if a method named $this->column_title() 
     * exists - if it does, that method will be used. If it doesn't, this one will
     * be used. Generally, you should try to use custom column methods as much as 
     * possible. 
     * 
     * Since we have defined a column_title() method later on, this method doesn't
     * need to concern itself with any column with a name of 'title'. Instead, it
     * needs to handle everything else.
     * 
     * For more detailed insight into how columns are handled, take a look at 
     * WP_List_Table::single_row_columns()
     * 
     * @param array $item A singular item (one full row's worth of data)
     * @param array $column_name The name/slug of the column to be processed
     * @return string Text or HTML to be placed inside the column <td>
     **************************************************************************/
    function column_default($item, $column_name){
        switch($column_name){
            case 'roastDate':
            case 'roastTime':
            case 'coffeeChoice':
            case 'roastChoice':
            case 'roastLength':
            case 'greenCoffee':
			case 'lotNumber':
            case 'user':
                return $item[$column_name];
            default:
                return print_r($item,true); //Show the whole array for troubleshooting purposes
        }
    }
    
    
    
    
    
    /*
    For inputting data before and/or after the table is displayed.
    */
    function extra_tablenav( $which ) {
        global $dateResult, $dateResult2;
        if ( $which == "top" ){
            //The code that goes before the table is here
            print "From: <input type=\"text\" name=\"dateResult\" value=\"". $dateResult . "\" size=\"9\" />&nbsp;";
            print "To: <input type=\"text\" name=\"dateResult2\" value=\"". $dateResult2 . "\" size=\"9\" />&nbsp;&nbsp;&nbsp;";
            print "<input type=\"submit\" class=\"button action\" value=\"Submit\" />&nbsp;&nbsp;&nbsp;";
			print "<input type=\"submit\" class=\"button-primary\" name=\"weekly_roast_report\" value=\"Generate Weekly Report\" />&nbsp;&nbsp;&nbsp;";
			print "<input type=\"submit\" class=\"button-primary\" name=\"monthly_roast_report\" value=\"Monthly Roasted Report\" />";
        }
        if ( $which == "bottom" ){
            //The code that goes after the table is there

        }
    }
    
        
    /** ************************************************************************
     * Recommended. This is a custom column method and is responsible for what
     * is rendered in any column with a name/slug of 'title'. Every time the class
     * needs to render a column, it first looks for a method named 
     * column_{$column_title} - if it exists, that method is run. If it doesn't
     * exist, column_default() is called instead.
     * 
     * This example also illustrates how to implement rollover actions. Actions
     * should be an associative array formatted as 'slug'=>'link html' - and you
     * will need to generate the URLs yourself. You could even ensure the links
     * 
     * 
     * @see WP_List_Table::::single_row_columns()
     * @param array $item A singular item (one full row's worth of data)
     * @return string Text to be placed inside the column <td> (movie title only)
     **************************************************************************/
    function column_title($item){
        
        //Build row actions
        $actions = array(
            'edit'      => sprintf('<a href="?page=%s&action=%s&roast=%s">Edit</a>',$_REQUEST['page'],'edit',$item['roastDate']),
            'delete'    => sprintf('<a href="?page=%s&action=%s&roast=%s">Delete</a>',$_REQUEST['page'],'delete',$item['roastDate']),
        );
        
        //Return the title contents
        return sprintf('%1$s <span style="color:silver">(%2$s)</span>%3$s',
            /*$1%s*/ $item['coffeeChoice'],
            /*$2%s*/ $item['greenCoffee'],
            /*$3%s*/ $this->row_actions($actions)
            
            
        );
        
    }
    
    /** ************************************************************************
     * REQUIRED if displaying checkboxes or using bulk actions! The 'cb' column
     * is given special treatment when columns are processed. It ALWAYS needs to
     * have it's own method.
     * 
     * @see WP_List_Table::::single_row_columns()
     * @param array $item A singular item (one full row's worth of data)
     * @return string Text to be placed inside the column <td> (movie title only)
     **************************************************************************/
    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label ("movie")
            /*$2%s*/ $item['id']                //The value of the checkbox should be the record's id
        );
    }
    
    
    /** ************************************************************************
     * REQUIRED! This method dictates the table's columns and titles. This should
     * return an array where the key is the column slug (and class) and the value 
     * is the column's title text. If you need a checkbox for bulk actions, refer
     * to the $columns array below.
     * 
     * The 'cb' column is treated differently than the rest. If including a checkbox
     * column in your table you must create a column_cb() method. If you don't need
     * bulk actions or checkboxes, simply leave the 'cb' entry out of your array.
     * 
     * @see WP_List_Table::::single_row_columns()
     * @return array An associative array containing column information: 'slugs'=>'Visible Titles'
     **************************************************************************/
    function get_columns(){
        $columns = array(
        // Uncomment line below to include checkbox for Bulk Actions
            //'cb'                => '<input type="checkbox" />', //Render a checkbox instead of text
            'roastDate'         => 'Date',
            'roastTime'         => 'Start Time',
            'coffeeChoice'      => 'Coffee',
            'roastChoice'       => 'Roast',
            'roastLength'       => 'Length',
            'greenCoffee'       => 'Green Weight (lbs)',
            'lotNumber'         => 'Lot Number',
            'user'              => 'Roaster'
        );
        
        return $columns;
    }
    
    /** ************************************************************************
     * Optional. If you want one or more columns to be sortable (ASC/DESC toggle), 
     * you will need to register it here. This should return an array where the 
     * key is the column that needs to be sortable, and the value is db column to 
     * sort by. Often, the key and value will be the same, but this is not always
     * the case (as the value is a column name from the database, not the list table).
     * 
     * This method merely defines which columns should be sortable and makes them
     * clickable - it does not handle the actual sorting. You still need to detect
     * the ORDERBY and ORDER querystring variables within prepare_items() and sort
     * your data accordingly (usually by modifying your query).
     * 
     * @return array An associative array containing all the columns that should be sortable: 'slugs'=>array('data_values',bool)
     **************************************************************************/
    function get_sortable_columns() {
        $sortable_columns = array(
            'roastDate'      => array('roastDate',true),     //true means it's already sorted
            'roastTime'      => array('roastTime',true),
            'coffeeChoice'   => array('coffeeChoice',false),
            'roastChoice'    => array('roastChoice',false),
            'roastLength'    => array('roastLength',false),
            'greenCoffee'    => array('greenCoffee',false),
            'lotNumber'    => array('lotNumber',false),
            'user'           => array('user',false)
        );
        return $sortable_columns;
    }
    
    
    /** ************************************************************************
     * Optional. If you need to include bulk actions in your list table, this is
     * the place to define them. Bulk actions are an associative array in the format
     * 'slug'=>'Visible Title'
     * 
     * If this method returns an empty value, no bulk action will be rendered. If
     * you specify any bulk actions, the bulk actions box will be rendered with
     * the table automatically on display().
     * 
     * Also note that list tables are not automatically wrapped in <form> elements,
     * so you will need to create those manually in order for bulk actions to function.
     * 
     * @return array An associative array containing all the bulk actions: 'slugs'=>'Visible Titles'
     **************************************************************************/
    //function get_bulk_actions() {
    //    $actions = array(
    //        'delete'    => 'Delete',
    //        'hide'      => 'Hide'
    //    );
    //    return $actions;
    //}
    
    
    /** ************************************************************************
     * Optional. You can handle your bulk actions anywhere or anyhow you prefer.
     * For this example package, we will handle it in the class to keep things
     * clean and organized.
     * 
     * @see $this->prepare_items()
     **************************************************************************/
    function process_bulk_action() {
        
        //Detect when a bulk action is being triggered...
        if( 'delete'===$this->current_action() ) {
            wp_die('Items deleted (or they would be if we had items to delete)!');
        }
        
    }
    
    
    /** ************************************************************************
     * REQUIRED! This is where you prepare your data for display. This method will
     * usually be used to query the database, sort and filter the data, and generally
     * get it ready to be displayed. At a minimum, we should set $this->items and
     * $this->set_pagination_args(), although the following properties and methods
     * are frequently interacted with here...
     * 
     * @global WPDB $wpdb
     * @uses $this->_column_headers
     * @uses $this->items
     * @uses $this->get_columns()
     * @uses $this->get_sortable_columns()
     * @uses $this->get_pagenum()
     * @uses $this->set_pagination_args()
     **************************************************************************/
    function prepare_items() {
        global $wpdb, $dateResult, $dateResult2; 
        
        //This is used only if making any database queries
        
        
        /**
         * First, lets decide how many records per page to show
         */
        $per_page = 200;
        
        
        
        /**
         * REQUIRED. Now we need to define our column headers. This includes a complete
         * array of columns to be displayed (slugs & titles), a list of columns
         * to keep hidden, and a list of columns that are sortable. Each of these
         * can be defined in another method (as we've done here) before being
         * used to build the value for our _column_headers property.
         */
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        
        
        /**
         * REQUIRED. Finally, we build an array to be used by the class for column 
         * headers. The $this->_column_headers property takes an array which contains
         * 3 other arrays. One for all columns, one for hidden columns, and one
         * for sortable columns.
         */
        $this->_column_headers = array($columns, $hidden, $sortable);
        
        
        /**
         * Optional. You can handle your bulk actions however you see fit. In this
         * case, we'll handle them within our package just to keep things clean.
         */
        $this->process_bulk_action();
        
        
        /**
         * Instead of querying a database, we're going to fetch the example data
         * property we created for use in this plugin. This makes this example 
         * package slightly different than one you might build on your own. In 
         * this example, we'll be using array manipulation to sort and paginate 
         * our data. In a real-world implementation, you will probably want to 
         * use sort and pagination data to build a custom query instead, as you'll
         * be able to use your precisely-queried data immediately.
         */
         $roast_query = $wpdb->get_results("SELECT id,roastDate,roastTime,coffeeChoice,roastChoice,roastLength,greenCoffee,user,lotNumber FROM " . $wpdb->prefix . "roast_db WHERE roastDate BETWEEN '$dateResult' AND '$dateResult2' ORDER BY roastDate DESC, roastTime DESC", ARRAY_A);

        $data = $roast_query;
        
        /**
         * This checks for sorting input and sorts the data in our array accordingly.
         * 
         * In a real-world situation involving a database, you would probably want 
         * to handle sorting by passing the 'orderby' and 'order' values directly 
         * to a custom query. The returned data will be pre-sorted, and this array
         * sorting technique would be unnecessary.
         */
        function usort_reorder($a,$b){
             $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'roastDate'; //If no sort, default to title
             $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'desc'; //If no order, default to asc
             $result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
             return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
         }
         usort($data, 'usort_reorder');
        
        
        
            
        /***********************************************************************
         * ---------------------------------------------------------------------
         * vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv
         * 
         * In a real-world situation, this is where you would place your query.
         * 
         * ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
         * ---------------------------------------------------------------------
         **********************************************************************/
  
        /**
         * REQUIRED for pagination. Let's figure out what page the user is currently 
         * looking at. We'll need this later, so you should always include it in 
         * your own package classes.
         */
        $current_page = $this->get_pagenum();
        
        /**
         * REQUIRED for pagination. Let's check how many items are in our data array. 
         * In real-world use, this would be the total number of items in your database, 
         * without filtering. We'll need this later, so you should always include it 
         * in your own package classes.
         */
        $total_items = count($data);
        
        
        /**
         * The WP_List_Table class does not handle pagination for us, so we need
         * to ensure that the data is trimmed to only the current page. We can use
         * array_slice() to 
         */
        $data = array_slice($data,(($current_page-1)*$per_page),$per_page);
        
        
        
        /**
         * REQUIRED. Now we can add our *sorted* data to the items property, where 
         * it can be used by the rest of the class.
         */
        $this->items = $data;
        
        
        /**
         * REQUIRED. We also have to register our pagination options & calculations.
         */
        $this->set_pagination_args( array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
        ) );
    }
    
}



function render_roast_page(){
    global $dateResult, $dateResult2;
    //Create an instance of our package class...
    $roastTable = new Roast_Table();
    //Fetch, prepare, sort, and filter our data...
    $roastTable->prepare_items();
    
    //$roastTable->months_dropdown('page');
    
    ?>
    <div class="wrap">
        
        <div id="icon-plugins" class="icon32"><br/></div>
        <h2>Roast Report</h2>

        
        <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
        <form id="movies-filter" method="GET">
            <!-- For plugins, we also need to ensure that the form posts back to our current page -->
            <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
            
            <?php
            
            //print "From: <input type=\"text\" name=\"dateResult\" value=\"". $dateResult . "\" size=\"9\" />&nbsp;";
            //print "To: <input type=\"text\" name=\"dateResult2\" value=\"". $dateResult2 . "\" size=\"9\" />&nbsp;&nbsp;&nbsp;";
            //print "<input type=\"submit\" class=\"button action\" value=\"Submit\" />"; ?>
            
            <!-- Now we can render the completed list table -->
            <!-- <?php $roastTable->search_box($text, $input_id ); ?> -->
            
            <?php $roastTable->display() ?>
        </form>
    </div>
    <?php
}

global $dateResult, $dateResult2, $wpdb;;
        if (!empty($_GET['dateResult'])) {
                
               $dateResult = $_GET['dateResult'];
            }
            else {
                $dateResult = date('Y-m-d');    
            }
            
        if (!empty($_GET['dateResult2'])) {
                    
                $dateResult2 = $_GET['dateResult2'];
            }
                
            else {
               $dateResult2 = date('Y-m-d');
            }




render_roast_page();

if (($_GET['weekly_roast_report'])) {

	echo "<table class=\"widefat\">";
	echo "<tbody>";
	echo "<tr cellpadding=\"4\">";	
		$day = 4;
			while ($day >= 0) {
			
			echo "<td><strong>";
				if (date("l") != "Friday") { 
					$date = date("Y-m-d", strtotime("Last Friday -" . $day ." day"));
					echo date("l, F jS", strtotime("Last Friday -" . $day ." day"));
						
				}else {
						
					$date = date("Y-m-d", strtotime("-" . $day ." day"));
					echo date("l, F jS", strtotime("-" . $day ." day"));
					
				}
			echo "</strong>";
				$daily_total = 0;
				$daily_coffee = $wpdb->get_results("SELECT roastDate, coffeeChoice, roastChoice, user, SUM(greenCoffee), SUM(roastedCoffee) FROM `cicr_roast_db` where roastDate = '$date' GROUP BY coffeeChoice, roastChoice", ARRAY_N );
				
			echo "<table>";
			echo "<tbody>";
			foreach ($daily_coffee as $sum_coffee) :
				
				$daily_total += $sum_coffee[4];
				
				echo "<tr>";
				echo "<td>";
						
				if (count($sum_coffee) > 0) {
					echo $sum_coffee[1] . " " . $sum_coffee[2] . ": " . $sum_coffee[4];
				}					
				else{				
					echo "No roasts today";
				}
					
					echo "</td>";
					echo "</tr>";
				
			endforeach;
			
			if ($daily_total > 0 ) {
			echo "<tr><td><hr /><b>Total Today: ";
			echo $daily_total ."lbs";
			echo "</b></td></tr>";
			}
			
			echo "</tbody>";
			echo "</table>"; 
			
				
			echo "</td>";
			
			
		$day--;
		
		}
echo "</tr>";
echo "</tbody>";		
echo "</table>";
}

if (($_GET['monthly_roast_report'])) {

				$daily_total = 0;
				$daily_coffee = $wpdb->get_results("SELECT roastDate, coffeeChoice, roastChoice, user, SUM(greenCoffee), SUM(roastedCoffee) FROM `cicr_roast_db` where roastDate BETWEEN '2014-07-01' AND '2014-07-31' GROUP BY coffeeChoice", ARRAY_N );
				
			echo "<table class=\"widefat\" width=\"30%\">";
			echo "<thead>";
			echo "<tr>
			<b><td style=\"border-bottom:2px solid #F1F1F1;\"><b>Coffee</b></td><td style=\"border-bottom:2px solid #F1F1F1;\"><b>Pounds</b></td></b></tr></thead>";
			
			
			echo "<tbody>";
			foreach ($daily_coffee as $sum_coffee) :
				
				$daily_total += $sum_coffee[4];
				
				echo "<tr>";
				
						
				if (count($sum_coffee) > 0) {
					echo "<td style=\"border-bottom: 1px dotted black;\">", $sum_coffee[1], "</td>"; 
					echo "<td>", $sum_coffee[4], "</td>";
				}					
				else{				
					echo "No roasts today";
				}
					
					echo "</tr>";
				
			endforeach;
			
			if ($daily_total > 0 ) {
			echo "<tr><td colspan=\"2\" style=\"border-top:2px solid #F1F1F1;\"><b>Total for this period: ";
			echo $daily_total ." lbs";
			echo "</b></td></tr>";
			echo "</tbody>";
			echo "</table>";
			}

}

?>


