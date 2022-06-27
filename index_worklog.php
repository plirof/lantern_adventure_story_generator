<?php
//SET SOME OPTIONS :
//$show_logical_header=true; // for many checkboxes (poor man freeze forst column alternative)
//$show_empty_lines=false; //If disabled (false) might have problem if you have empty lines
//$add_class_to_element=true; //190319 adds class name to each element(so we can add custom js for this element )
//$sorttable_js=true; //enable sortablejs (maybe slow in BIG files)
//$show_internal_element_text_outside=true; // shows text outside teaxtare/input field (this is needed for sort to work)
$show_submit_button=true ; //shows/hide submit button 
$checkbox_show_submit=false; //activate checkbox to show submit button
//$password="1234"; //Activate & set Password  //jon 220501


//load main file
require ('worklog.php');
//<a href="generate.php?fileinput=true" target="generated">
?>
<hr>
<a href="process.php?fileinput=true" target="generated">generate lantern file xml: lant.xml</a>

<hr>
NOTES:
< directions n="11" s="255" e="6" w="4" ne="255" se="255" sw="255" nw="255" up="255" down="255" in="255" out="255" mass="0" / >
e ,w enter Location ID