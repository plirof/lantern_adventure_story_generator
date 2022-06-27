<?php
date_default_timezone_set('Europe/Athens'); //added to avoid PHP warning for date 160920
#####################################################################################
# Flat File Database Manager 1.2jmod11-200109_isset_checks
#
# changes:
# 1.2jmod11-220501a - added password option to ACCEPT submit
# 1.2jmod11-201028b_checkbox_show_submit show only when you check a checkbox
# 1.2jmod11-200109_isset_checks_instead_of_from_empty() to isset()
# 1.2jmod11-200108_submit_button option , edit.php , index.php=only view)
# 1.2jmod10-190410_sorting fix TEXTAREA show outside element if column name =Week (HARDCODED)
# 1.2jmod10-190409e_LISTWEEKSSCH
# 1.2jmod10-190408_$show_logical_header shows text in checkboxes (alt to freeze 1st row)
# 1.2jmod09-190320_sortablejs_sprintf
# 1.2jmod08-190319_shows_selected_&_class_name on list text
# 1.2jmod07-LISTQUARTER 190312
# ver1.2jmod06-RTfilter 190305
# ver1.2jmod05-Reat time client filter 190224a
# ver1.2jmod04-link object type 190223a :
#   - Added red color to DELETE/MARK checkbox
# ver1.2jmod03-link object type 170716a
#####################################################################################
# Visit http://www.zubrag.com/scripts/ for updates
#####################################################################################
# Expects:
# NOTE : need to add to php.ini max_input_vars=3000;
# Database file name, i.e. file with real data
# --> $data_file
#####################################################################################
# Database definition file. You have to describe database format in this file.
# Each row describes one field
# Allowed data types: 
# STRING:  Rendered as regular input field. Row format:
#          title,STRING,length
# TEXT:    Rendered as text area. Row format:
#          title,TEXT,columns,rows
# LOGICAL: Rendered as check box (flag). Row format:
#          title,LOGICAL,1,value for Yes,value for No
# LIST:    Rendered as list box or combo box. Row format:
#          title,LIST,number of rows visible at a time,colon ":" separated allowed values
# LINK:  Rendered as regular input field. Row format:  //JONMOD
#          title,LINK,length#
# DATE:  Rendered as regular input field. Row format:    Added by jon
#          title,STRING,length
# LISTQUARTER:    Rendered as list box or combo box with monthQuarters (not much usefull atm). Row format:
#          title,LIST,number of rows visible at a time added by Jon 190312
# LISTWEEKSSCH:    Rendered as list box or combo box. Row format:
#          title,LIST,rows visible,colon ":" sep. values  (parama are ignored select options are auto filled $first_week_sept10=37, $last_week_of_year=53 )      
# Sample data definition file contents:
# City,LIST,3,City1:City2:City3:City4:City5
# State,LIST,1,NY:CA:LA
# Zip,STRING,8
# Active,LOGICAL,1,Y:N
# Comments,TEXT,30:2
# title,LOGICAL,1,YES:NO
# website,LINK,30
# week_school_list,LISTWEEKSSCH,1,5:0:1:2
# --> $structure_file
######################################################################################
# Fields delimiter
# --> $delimiter
#####################################################################################

// strip slashes
if (get_magic_quotes_gpc()) {
  function stripslashes_deep($value) {
    $value = is_array($value) ? array_map('stripslashes_deep', $value) : (isset($value) ? stripslashes($value) : null);
    return $value;
  }

  $_POST = stripslashes_deep($_POST);
  $_GET = stripslashes_deep($_GET);
  //$_COOKIE = stripslashes_deep($_COOKIE);
}

//Set these in INDEX.PHP
if(!isset($show_empty_lines))$show_empty_lines=false; //If disabled(false) might have problem if you have empty lines
if(!isset($add_class_to_element))$add_class_to_element=true; //190319 adds class name to each element(so we can add custom js for this element )
if(!isset($show_internal_element_text_outside))$show_internal_element_text_outside=false; //Show element text outside (Helps sorting!!)
if(!isset($sorttable_js))$sorttable_js=true; // might need $show_internal_element_text_outside=true
if(!isset($show_logical_header))$show_logical_header=false; //If disabled(false) might have problem if you have empty lines
if(!isset($show_submit_button))$show_submit_button=true;  //200108 index.php:false-read-only / edit.php :true shows submit
if(!isset($checkbox_show_submit))$checkbox_show_submit=true;  //201028 $checkbox_show_submit show only when you check a checkbox
$password_activated=false ; 
if(isset($password))$password_activated=true;  //220501 $password_activated (True will need password to ACCEPT submit ) will need PASSWORD textfield too . If password is wrong then it willjust ignore submit



$structure_tmp = file($structure_file);
$structure = array();
foreach($structure_tmp as $key=>$tmp) {
  /*if(strpos($tmp,':') === 0) {
    $code = explode(':',$tmp);
    ${$code[1]} = trim($code[2]);
    continue;
  }*/
  $line = explode(',',$tmp);
  $name_will_be = str_replace(' ','',trim($line[0]));
  foreach($structure as $key1=>$value1) {
    if ($value1['name'] == $name_will_be)
      die("Few columns have the similar name (not counting spaces): '{$line[0]}'. Please rename.");
  }
  $structure[$key]['name_original'] = trim($line[0]);
  $structure[$key]['name'] = str_replace(' ','',$structure[$key]['name_original']);
  $structure[$key]['type'] = trim($line[1]);
  if (isset($line[2])) $structure[$key]['format'] = trim($line[2]);
  if (isset($line[3])) {
    $values = explode(':',$line[3]);
    foreach($values as $item) {
      $structure[$key]['values'][] = trim($item);
    }
  }
}

// Save data (Submit button pressed)
if (isset($_POST['submit'])) {
  /////////////////////////////////////////////////
  if($password_activated) {  //220501 $password_activated
    echo "<h1>Password activated</h1>";
    
    if($password!=$_POST['password']) {
        echo "<h1>Wrong Password</h1>";
        exit(1);
    }
  }
  if ($skip_lines > 0) {
    // read header lines
    $tmp_data = file($data_file);
  }

  $f = fopen($data_file,'w+');
  if ($f) {

    // save header back to file
    if ($skip_lines > 0) {
      for($i=0; $i < $skip_lines; $i++) {
        fputs($f,$tmp_data[$i]);
      }
    }

    for( $i=0; $i < count($_POST[$structure[0]['name']]); $i++ ) {
      // do not save records marked for delete
      if (isset($_POST['d_e_l_e_t_e'][$i])) continue;

      $s = '';
      $isfirst = true;
      foreach($structure as $key => $field) {
        $n1 = isset($_POST[$structure[$key]['name']]) ? $_POST[$structure[$key]['name']] : '';
        $v1 = isset($n1[$i]) ? $n1[$i] : $structure[$key]['values'][1];
        // remove new line characters as each new line represents new database row
        $v1 = str_replace(array("\r\n","\n","\r"),'',$v1);
        $s = $s . ($isfirst ? '' : $delimiter) . $v1;
        $isfirst = false;
      }
      // do not save empty lines
      if (trim(str_replace($delimiter,'',$s)) == '') if($show_empty_lines)continue;

      // save database record to file
      fputs($f,$s."\n");
    } // for
    fclose($f);
  } // if
 ////// header("location: index.php");   //added jon 161112 by jon to avoid resubmissions  (if you restore session will always do a normal load)  
}

$data = file($data_file);

// skip header lines
if ($skip_lines > 0) $data = array_slice($data, $skip_lines);

// add "new line" holder
$data[] = str_repeat($delimiter,count($structure)-1);
//array_unshift($data, str_repeat($delimiter,count($structure)-1));//added by jon 160217


echo '<html>';
echo "<head><title>$data_file</title>
<script>
  function autoScrolling() { window.scrollTo(0,document.body.scrollHeight); }
  //setInterval(autoScrolling, 1000); //added by jon 160218 autoscroll bottom of page
</script>
<style>
/* filter table stuff */
.myRTFilterInput {
  width: 100%; /* Full-width */
  font-size: 16px; /* Increase font-size */
  padding: 12px 20px 12px 40px; /* Add some padding */
  border: 1px solid #ddd; /* Add a grey border */
  margin-bottom: 12px; /* Add some space below the input */
}


</style>";

if($sorttable_js) echo'<script src="sorttable.js"></script>'."\n";

echo "</head>";
echo "<body><h1>$data_file</h1>
";

echo '<form method="post">';
$table_class='';
if($sorttable_js) $table_class=' class="sortable" ';
echo "\n".'<table id="myTable" '.$table_class.' border=1 >'."\n";

// output header
echo '<tr >';
foreach ($structure as $key=>$line) {
  echo "<th>{$line['name_original']}</th>";
}
echo '<th>Mark</th>';
echo '</tr>'."\n";

// output data
foreach($data as $datakey => $line) {

  // skip empty rows
  if (trim($line) == '') if($show_empty_lines)continue;

  echo '<tr style="background: #'.($datakey % 2 == 0 ? 'F0F0F0' : 'FAFAFA').'">';

  $items = explode($delimiter,$line);

  // any fields not defined? add empty
  while (count($items) < count($structure))
    $items[] = '';

  foreach ($items as $key => $item) {
    $item = htmlspecialchars(trim($item));
    $name = $structure[$key]['name'];
    echo "\n".'  <td >';
  //echo "<h1>$item</h1>";
    $class_name='';
    if($add_class_to_element) $class_name='class="'.$name.'"';
    switch ($structure[$key]['type']) {
# STRING:  Rendered as regular input field. Row format:
#          title,STRING,length  
      case 'STRING':
        echo '<input onchange="cdf('.$datakey.')" name="'.$name.'['.$datakey.']" value="'.$item.'" '.$class_name.' size="'.$structure[$key]['format'].'" />';
        //echo "$item";
        break;
# DATE:  Rendered as regular input field. Row format:    Added by jon
#          title,STRING,length      
      case 'DATE':   // added by jon 20141208
    
    if ($item==null) $item=date("Ymd");
        echo '<input onchange="cdf('.$datakey.')" name="'.$name.'['.$datakey.']" value="'.$item.'" '.$class_name.' size="'.$structure[$key]['format'].'" />';
        //echo "$item";
        break;
# TEXT:    Rendered as text area. Row format:
#          title,TEXT,columns,rows

      case 'TEXT':
        if($show_internal_element_text_outside && (strpos($structure[$key]['name'], 'Week') !== false)  )echo ''.substr($item, 0, 8).'<BR>'; //HARDCODED Show ONLY if column name contains 'Week Suggest' in name
        $rc = explode(':',$structure[$key]['format']);
        $cols = trim($rc[0]);
        $rows = trim($rc[1]);
        echo '<textarea onchange="cdf('.$datakey.')" name="'.$name.'['.$datakey.']" rows="'.$rows.'" cols="'.$cols.'">'.$item.'</textarea>';
        break;
# LOGICAL: Rendered as check box (flag). Row format:
#          title,LOGICAL,1,value for Yes,value for No

    case 'LOGICAL':
        $val_yes = trim($structure[$key]['values'][0]);
        echo '<input onchange="cdf('.$datakey.')" name="'.$name.'['.$datakey.']" type="checkbox" '.(($item == $val_yes) ? 'checked' : '').' value="'.$val_yes.'" />';
        if($show_logical_header) echo $structure[$key]['name'];// show header column name (alternative to freeze first row)
        break;
// +++++++++++++++++++++++++ added by john to show a link (adds HTTP:// ) 20160428+++++++++++++++++++++++        
# LINK:  Rendered as regular input field (like STRING) but creates a link. Row format:
#          title,LINK,length  
      case 'LINK':
        $item = preg_replace("/^http:\/\//i", "", $item); //remove http from saved text NOTE might have to add http or https eventually jon 170716a
        $item = preg_replace("/^https:\/\//i", "", $item);//remove http from saved text NOTE might have to add http or https eventually jon 170716a
#        quicknotes_worklog_/flatfile.inc.php on line 203
        echo '<input onchange="cdf('.$datakey.')" name="'.$name.'['.$datakey.']" value="'.$item.'" size="'.$structure[$key]['format'].'" />';
//      echo '<BR><a href="http://'.$item.'" >'.$item.'</a>';
        if($show_internal_element_text_outside)echo '<BR><a href="http://'.$item.'" >LINK</a>';        
        break;        
// ------------------------ added by john to show a link (adds HTTP://)----------------------
# LIST:    Rendered as list box or combo box. Row format:
#          title,LIST,number of rows visible at a time,colon ":" separated allowed values   
      case 'LIST':
        if($show_internal_element_text_outside)echo '<b>'.sprintf("%02d", $item).'</b><BR>'; 
        echo '<select onchange="cdf('.$datakey.')" name="'.$name.'['.$datakey.']" '.$class_name.' size="'.$structure[$key]['format'].'">';
        foreach($structure[$key]['values'] as $value) {
          echo '<option value="'.$value.'" '.($value == $item ? 'selected' : '').'>'.$value.'</option>';
        }
        echo '</select>';
        
        break;
# LISTQUARTER:    Rendered as list box or combo box with monthQuarters (not much usefull atm). Row format:
#          title,LIST,number of rows visible at a time,colon ":" separated allowed values   
      case 'LISTQUARTER':
        if($show_internal_element_text_outside)echo '<b>'.sprintf("%02d", $item).'</b><BR>';
      	$month_weeks=array("---");
      	array_push($month_weeks,"SepA","SepB","SepC","SepD","OctA","OctB","OctC","OctD","NovA","NovB","NovC","NovD","DecA","DecB","DecC","DecD","JanB","JanC","JanD","FebA","FebB","FebC","FebD","MarA","MarB","MarC","MarD","AprA","AprB","AprC","AprD","MayA","MayB","MayC","MayD","JunA","JunB","JunC","JunD");
        echo '<select onchange="cdf('.$datakey.')" name="'.$name.'['.$datakey.']" '.$class_name.' size="'.$structure[$key]['format'].'">';
        foreach($month_weeks as $value) {
          //echo '<option value="'.$value.'" '.($value == $item ? 'selected' : '').'>'.$value.'</option>';
        	echo '<option value="'.$value.'" '.($value == $item ? 'selected' : '').'>'.$value.'</option>';
        }
        echo '</select>';
        break;
# LISTWEEKSSCH:    Rendered as list box or combo box with monthQuarters (not much usefull atm). Row format:
#          title,LIST,number of rows visible at a time,colon ":" separated allowed values  (VALUES are Ignored in LISTWEEKSSCH)  
      case 'LISTWEEKSSCH':
        if($show_internal_element_text_outside)echo '<b>'.sprintf("%02d", $item).'</b><BR>';
        if(empty($first_week_sept10)) $first_week_sept10=37; //year 2019  week_num of sept 10
        if(empty($last_week_of_year)) $last_week_of_year=53; //year 2019  last week of year

        $week_year2week_sch=array();
        $week_sch2week_year=array();

        $week_year2week_sch["99"]="99"; //means unsorted
        $week_sch2week_year["99"]="99"; //means unsorted

        $counter=$first_week_sept10;
        for($i=0;$i<38;$i++)
        {
          //echo '["'.sprintf("%02d", $counter).'"]=>"'.sprintf("%02d", $i).'", ';
          $week_year2week_sch[sprintf("%02d", $counter)]=sprintf("%02d", $i);
          $week_sch2week_year[sprintf("%02d", $i)]=sprintf("%02d", $counter);
          $counter++;
          if($counter>$last_week_of_year) $counter=01;
        }
        
        $week_year2week_sch["77"]="77"; //means to check
        $week_sch2week_year["77"]="77"; //means to check

        echo '<select onchange="cdf('.$datakey.')" name="'.$name.'['.$datakey.']" '.$class_name.' size="'.$structure[$key]['format'].'">';
        foreach($week_sch2week_year as $key=>$value) {
          //echo '<option value="'.$value.'" '.($value == $item ? 'selected' : '').'>'.$value.'</option>';
          echo '<option value="'.sprintf("%02d", $key).'" '.(sprintf("%02d", $key) == sprintf("%02d", $item) ? 'selected' : '').'>'.$key ."(".$week_sch2week_year[$key].")".'</option>';
        }
        echo '</select>';
        //echo "hello";
        break;                 
    }
    echo '</td>';
  }  // end of   foreach ($items as $key => $item) {
  
  // Mark for delete if last record (i.e. Add option). In this way we'll skip adding empty records
  
  echo "\n  <td><div style='background-color: red;'><input id='d_e_l_e_t_e[{$datakey}]'  name='d_e_l_e_t_e[{$datakey}]' type='checkbox' ".($datakey == count($data)-1 ? 'checked' : '')." /></div></td>";
  echo "\n</tr>\n";

}

echo '<tr><td colspan=255 align=center></td></tr>';
print '<input type="text" id="myRTFilterInput" onkeyup="myRTFilterFunction()" placeholder="Search for names..">';
echo '</table>';



if ($show_submit_button) {echo '<center><input type="submit" name="submit" id="submit_button_id" value="Save Changes and Delete marked" style="border:1px solid red"></center>';} else {echo '<center>READ ONLY MODE</center>';}//200108 show/hide submit button'
if($password_activated) {
  echo '<input type="password" name="password" id="password" value="" style="border:1px solid red">';

}

if ($checkbox_show_submit) {
  echo '
<script>
var btn_submit = document.getElementById("submit_button_id");
btn_submit.style.display = "none";
function checkbox_show_submit_Function() {
  // Get the checkbox
  var checkBox = document.getElementById("showSubmitBtnCheck");
  // Get the output text
  var btn_submit = document.getElementById("submit_button_id");

  // If the checkbox is checked, display the output text
  if (checkBox.checked == true){
    btn_submit.style.display = "block";
  } else {
    btn_submit.style.display = "none";
  }
}
</script>

show submit: <input type="checkbox" id="showSubmitBtnCheck" onclick="checkbox_show_submit_Function()"><BR>

';

}//201028_checkbox_show_submit show only when you check a checkbox

echo "</form>

<script>
function cdf(theid) {
document.getElementById('d_e_l_e_t_e['+theid+']').checked = false;
}
</script>";

//include('show_filter.php'); //added 181020a jon to display a list of options to filter rows   


echo '
<script>
autoScrolling();

function myRTFilterFunction() {
  // Declare variables 
  var input, filter, table, tr, td, i, txtValue;
  var counted_columns='.count($structure).';
  input = document.getElementById("myRTFilterInput");
  filter = input.value.toUpperCase();
  table = document.getElementById("myTable");
  tr = table.getElementsByTagName("tr");

  // Loop through all table rows, and hide those who dont match the search query
  for (i = 0; i < tr.length; i++) {


	var found_in_input_elem=false;
    for (col = 0; col < counted_columns; col++) {
    	
	 	var x;
	 	td = tr[i].getElementsByTagName("td")[col];
	    if (td) {
	      if(td.getElementsByTagName("input")[0]){
	      	x = td.getElementsByTagName("input")[0].value;
	      	
	      
	      }

	      txtValue = td.textContent || td.innerText || x ;
	      if(txtValue==null) txtValue="ZXCXZCCXZC";
	      if (txtValue.toUpperCase().indexOf(filter) > -1 || found_in_input_elem) {
	      	//console.log("x found="+x+"  txtValue="+txtValue    +"          count(structure)="+counted_columns);
	        tr[i].style.display = "";
	        found_in_input_elem=true;
	      } else {
	        tr[i].style.display = "none";
	      }
    	}
    }// END of for (col = 0; col < counted_columns; col++) {


  }
}

</script>
';

echo '</body>';
echo '</html>';

?>
