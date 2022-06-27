<?php
$debug2=false;
$id_room_first=2;//id that generated rooms numbers starts 
$connections_array=array();

/*
if($debug2){
	// For debug purposes
	//$file = fopen('class-worklog-lantern/worklog.txt', 'r');
	$file = fopen('worklog.txt', 'r');
	while (($line = fgetcsv($file,null,"|")) !== FALSE) {
	  //$line is an array of the csv elements
	  list($room[],$room_desc[],$villain[],$villain_desc[],$item[],$item_desc[],$item_reward[],$item_reward_desc[],$exits[]) = $line;
	  //print_r($line);echo "<hr size=10>";
	}
	fclose($file);
}

if($debug2)print_r($exits);echo "<hr size=11>";
*/
//Replaces spaces with _ and lowercase
function convert2SafeName($string){


	return strtolower(str_replace(' ', '_', $string));
}

function convert2SafeNameArray($array){
	$counter=0;
	$filesafeArray=Array();
	foreach ($array as $one) {
		$filesafeArray[$counter]=convert2SafeName($one);
		$counter++;
	}

	return $filesafeArray;
}


//print_r($room);
if($debug2)print_r(convert2SafeNameArray($room));

//Check for connections
// <directions n="255" s="255" e="255" w="255" ne="255" se="255" sw="255" nw="255" up="$up_exit_id" down="$down_exit_id" in="255" out="255" mass="0" />
//$cur_villain_name_no_space=strtolower(str_replace(' ', '_', $villain[$counter])); 
//Generates array with connections
function fill_connections_array(){
	global $exits,$room,$connections_array;
	global $id_room_first;
	global $debug2;
    //$room[]
    //$exits[]
    /////$key = array_search('green', $array); // $key = 2;
	foreach ($exits as $ex) {
		$cur_room_id = +$id_room_first+array_search($ex, $exits);

		if(strlen($ex)>5){
			//if($debug2)  echo "<BR>\n".$ex;
			$params=explode(',', convert2SafeName($ex));
 			//if($debug2)  print_r($params);
 			//Check all params (we SHOULD NOT have multiple up or E etc...)
 			foreach ($params as $param) {
 				//if($debug2)  echo "<h3>".$param."</h3>";
 				$one_exit=explode('=', convert2SafeName($param));
 				//if($debug2)  echo "<h3>".print_r($one_exit)."</h3>";
 				//eg $one_exit[0]="up"  , $one_exit[1]=Lobby 2
 				$key = array_search($one_exit[1],  convert2SafeNameArray($room)); // Search in rooms array
 				//if($debug2)  echo "<h3>KEY=".$key." , one_exit[1] =$one_exit[1] </h3>";
 				$key=$key+$id_room_first; // This is the REAL room_id (they room id1,2 were reserved)
 				//found the relater room - we need to 
 				$target_room_id=$key;
 				//$cur_room_id
 				//up,down
 				if($debug2)  echo "<h3> one_exit[0]=$one_exit[0] ,cur_room_id=$cur_room_id , target_room_id=$target_room_id </h3>\n";
 				if($one_exit[0]=='up' ) { 
 					$connections_array[$cur_room_id]['up']=$target_room_id;
 					$connections_array[$target_room_id]['down']=$cur_room_id;
                }
                if($one_exit[0]=='down' ) { 
 					$connections_array[$cur_room_id]['down']=$target_room_id;
 					$connections_array[$target_room_id]['up']=$cur_room_id;
                }
 				if($one_exit[0]=='s' ) { 
 					$connections_array[$cur_room_id]['s']=$target_room_id;
 					$connections_array[$target_room_id]['n']=$cur_room_id;
                }
                if($one_exit[0]=='n' ) { 
 					$connections_array[$cur_room_id]['n']=$target_room_id;
 					$connections_array[$target_room_id]['s']=$cur_room_id;
                }
 				if($one_exit[0]=='e' ) { 
 					$connections_array[$cur_room_id]['e']=$target_room_id;
 					$connections_array[$target_room_id]['w']=$cur_room_id;
                }
                if($one_exit[0]=='w' ) { 
 					$connections_array[$cur_room_id]['w']=$target_room_id;
 					$connections_array[$target_room_id]['e']=$cur_room_id;
                }


 				//sw,ne

 				//se,nw


 			}// END of foreach ($params as $param) {

		}


	}// END of foreach ($exits as $ex) {
		if($debug2)  echo "<hr>CCCCC Connection Array=\n";
		if($debug2)  print_r($connections_array);

}

function generate_room_direction_line($room_id)
{
	global $room,$connections_array;
	global $debug2;
	$directions_line='<directions n="255" s="255" e="255" w="255" ne="255" se="255" sw="255" nw="255" up="255" down="255" in="255" out="255" mass="0" />';
	foreach ($connections_array[$room_id] as $id =>$direction) {
		if($debug2)  echo "<h3>AAAAAAAAAAA id=$id , direction=$direction</h3>\n";
		$str2search=$id.'="255"';
		$str2replace=$id.'="'.$direction.'"';
		///echo strpos($str2search, $directions_line);
		$directions_line=str_replace($str2search, $str2replace, $directions_line);
		if($debug2)  echo "\n<h3>directions_line=$directions_line str2search=$str2search str2replace=$str2replace</h3> \n";

	}

	//if($debug2)  echo "<h3>directions_line=$directions_line</h3>\n";
	return $directions_line;
}//END of function generate_room_direction_line($room_id)


//returns text of a dummy object that is OFF
function createDummyItem($id){


$dummy_text= <<<DUMMY
      <object id="$id" holder="0" name="dummy item$id" printedname="dummy item$id">
        <description />
        <initialdescription />
        <directions n="255" s="255" e="255" w="255" ne="255" se="255" sw="255" nw="255" up="255" down="255" in="255" out="255" mass="0" />
        <flags scenery="0" portable="0" container="0" supporter="0" transparent="0" openable="0" open="0" wearable="1" emittinglight="0" locked="0" lockable="0" beingworn="0" user1="0" door="0" user2="0" user3="0" user4="0" />
        <synonyms />
        <nogo>
          <s />
          <w />
          <ne />
          <nw />
          <e />
          <se />
          <sw />
          <n />
          <up />
          <down />
          <in>You can't enter that.</in>
          <out>I don't know which way that is.</out>
        </nogo>
        <ImageId />
      </object>
DUMMY;

return $dummy_text;

}



fill_connections_array();
//generate_room_direction_line(8);
?>