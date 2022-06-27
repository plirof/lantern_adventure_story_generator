var debug1;
debug1 = false;
var counter;
counter = 0;
var room_counter;
room_counter = 2;
var write_to_lant_xml;
write_to_lant_xml = false;
var direct_download;
direct_download = false;

    var title, _REQUEST;
    title = "This is the game title (from input text)"; 
    var result;
    result = "_REQUEST_MYCONTENT"; //grab textarea
    // if($debug1) {print_r($result);echo "=RESULT<hr size=10>";}
    var result_array;
    //result_array = explode("<br>", result); //PHP
    result_array = result.split("<BR>");
    //if($debug1) {print_r($result_array);echo "=RESULT+ARRAY<hr size=10>";}
    var _key_;
    __loop1:
        for (_key_ in result_array) {
            line = result_array[_key_];
            var line2;
            //line2 = explode("|", line); //PHP
            line2 = line.split("|");
            //list($room[],$room_desc[],$villain[],$villain_desc[],$item[],$item_desc[],$item_reward[],$item_reward_desc[],$exits[]) = $line2; //PHP @@@@@@@@@
            __LIST_VALUES__ = line2;
            //if($debug1) {print_r($line);echo "=LINE<hr size=10>";}
            if (debug1) {
                print_r(line2);
                console.log("=LINE2 LOOP<hr size=10>");
            }
        }


//++++++++++++++++++++++++++
var debug2;
debug2 = false;
var id_room_first;
id_room_first = 2;
//id that generated rooms numbers starts
var connections_array;
connections_array = {};

//Replaces spaces with _ and lowercase
function convert2SafeName(string) {
    return strtolower(str_replace(" ", "_", string));
}
function convert2SafeNameArray(array) {
    var counter;
    counter = 0;
    var filesafeArray;
    filesafeArray = {};
    var _key_;
    __loop1:
        for (_key_ in array) {
            var one;
            one = array[_key_];
            filesafeArray[counter] = convert2SafeName(one);
            counter++;
        }
    return filesafeArray;
}
//print_r($room);
if (debug2) {
    print_r(convert2SafeNameArray(room));
}
//Check for connections
// <directions n="255" s="255" e="255" w="255" ne="255" se="255" sw="255" nw="255" up="$up_exit_id" down="$down_exit_id" in="255" out="255" mass="0" />
//$cur_villain_name_no_space=strtolower(str_replace(' ', '_', $villain[$counter]));
//Generates array with connections
function fill_connections_array() {
    //$room[]
    //$exits[]
    /////$key = array_search('green', $array); // $key = 2;
    var _key_;
    __loop1:
        for (_key_ in exits) {
            var ex;
            ex = exits[_key_];
            var cur_room_id;
            cur_room_id = +id_room_first + array_search(ex, exits);
            if (strlen(ex) > 5) {
                //if($debug2)  echo "<BR>.n".$ex;
                var params;
                //params = explode(",", convert2SafeName(ex));//PHP
                params = convert2SafeName(ex).split("|");
                //if($debug2)  print_r($params);
                //Check all params (we SHOULD NOT have multiple up or E etc...)
                __loop2:
                    for (_key_ in params) {
                        var param;
                        param = params[_key_];
                        //if($debug2)  echo "<h3>".$param."</h3>";
                        var one_exit;
                        //one_exit = explode("=", convert2SafeName(param));//PHP
                        one_exit = convert2SafeName(ex).split("=");
                        //if($debug2)  echo "<h3>".print_r($one_exit)."</h3>";
                        //eg $one_exit[0]="up"  , $one_exit[1]=Lobby 2
                        var key;
                        key = array_search(one_exit[1], convert2SafeNameArray(room));
                        // Search in rooms array
                        //if($debug2)  echo "<h3>KEY=".$key." , one_exit[1] =$one_exit[1] </h3>";
                        key = key + id_room_first;
                        // This is the REAL room_id (they room id1,2 were reserved)
                        //found the relater room - we need to
                        var target_room_id;
                        target_room_id = key;
                        //$cur_room_id
                        //up,down
                        if (debug2) {
                            console.log("<h3> one_exit[0]=" + one_exit[0] + " ,cur_room_id=" + cur_room_id + " , target_room_id=" + target_room_id + " </h3> ");
                        }
                        if (one_exit[0] == "up") {
                            connections_array[cur_room_id]["up"] = target_room_id;
                            connections_array[target_room_id]["down"] = cur_room_id;
                        }
                        if (one_exit[0] == "down") {
                            connections_array[cur_room_id]["down"] = target_room_id;
                            connections_array[target_room_id]["up"] = cur_room_id;
                        }
                        if (one_exit[0] == "s") {
                            connections_array[cur_room_id]["s"] = target_room_id;
                            connections_array[target_room_id]["n"] = cur_room_id;
                        }
                        if (one_exit[0] == "n") {
                            connections_array[cur_room_id]["n"] = target_room_id;
                            connections_array[target_room_id]["s"] = cur_room_id;
                        }
                        if (one_exit[0] == "e") {
                            connections_array[cur_room_id]["e"] = target_room_id;
                            connections_array[target_room_id]["w"] = cur_room_id;
                        }
                        if (one_exit[0] == "w") {
                            connections_array[cur_room_id]["w"] = target_room_id;
                            connections_array[target_room_id]["e"] = cur_room_id;
                        }
                        if (one_exit[0] == "ne") {
                            connections_array[cur_room_id]["ne"] = target_room_id;
                            connections_array[target_room_id]["sw"] = cur_room_id;
                        }
                        if (one_exit[0] == "sw") {
                            connections_array[cur_room_id]["sw"] = target_room_id;
                            connections_array[target_room_id]["ne"] = cur_room_id;
                        }
                        if (one_exit[0] == "se") {
                            connections_array[cur_room_id]["se"] = target_room_id;
                            connections_array[target_room_id]["nw"] = cur_room_id;
                        }
                        if (one_exit[0] == "nw") {
                            connections_array[cur_room_id]["nw"] = target_room_id;
                            connections_array[target_room_id]["se"] = cur_room_id;
                        }
                        //sw,ne
                        //se,nw
                    }
                // END of foreach ($params as $param) {
            }
        }
    // END of foreach ($exits as $ex) {

    if (debug2) {
        print_r(connections_array);
    }
}
function generate_room_direction_line(room_id) {
    var directions_line;
    directions_line = "<directions n=\"255\" s=\"255\" e=\"255\" w=\"255\" ne=\"255\" se=\"255\" sw=\"255\" nw=\"255\" up=\"255\" down=\"255\" in=\"255\" out=\"255\" mass=\"0\" />";
    var id;
    __loop1:
        for (id in connections_array[room_id]) {
            var direction;
            direction = connections_array[room_id][id];
            if (debug2) {
                console.log("<h3>AAAAAAAAAAA id=" + id + " , direction=" + direction + "</h3> "  );
            }
            var str2search;
            str2search = id + "=\"255\"";
            var str2replace;
            str2replace = id + "=\"" + direction + "\"";
            ///echo strpos($str2search, $directions_line);
            directions_line = str_replace(str2search, str2replace, directions_line);

        }
    //if($debug2)  echo "<h3>directions_line=$directions_line</h3>.n";
    return directions_line;
}
//END of function generate_room_direction_line($room_id)
//returns text of a dummy object that is OFF
function createDummyItem(id) {
    var dummy_text;
    dummy_text = "DUMMY-HEREDOC@@@@@@@@@@@@@@@@@@@@@@@@@@";
    return dummy_text;
}
fill_connections_array();


//generate_room_direction_line(8);
//-----------------------------------------------
var rooms_total;
rooms_total = sizeof(room);
var item_reward_safenamearray;
item_reward_safenamearray = convert2SafeNameArray(item_reward);
var items_not_created_list;
items_not_created_list = {};
//MIGHT NOT NEEDED CHECK!! hold here the items ids NOT created so we will link to the ID of the reward items
//$a=["luigi","mantis","jaws","zangief","ivan_drago","joker","t_800_model_101","van_damne","chuck_norris"];
var objects_locations;
objects_locations = "";
var objects_enemies;
objects_enemies = "";
var objects_items;
objects_items = "";
var sentences_text;
sentences_text = "";
var usercheckblocks_text;
usercheckblocks_text = "";
var checks_text;
checks_text = "";
var routines_text;
routines_text = "";
var event_text;
event_text = "";
var objects_items_reward;
objects_items_reward = "";
// PARSE ALL THE ROOMS (= file/table rows)
__loop1:
    for (_key_ in room) {
        var r;
        r = room[_key_];
        var cur_room_name;
        cur_room_name = room[counter];
        var cur_villain_name_no_space;
        cur_villain_name_no_space = strtolower(str_replace(" ", "_", villain[counter]));
        var cur_item_name_no_space;
        cur_item_name_no_space = strtolower(str_replace(" ", "_", item[counter]));
        var cur_item_reward_name_no_space;
        cur_item_reward_name_no_space = strtolower(str_replace(" ", "_", item_reward[counter]));
        var kill_item_defined;
        kill_item_defined = true;
        if (strlen(item[counter]) < 2) {
            kill_item_defined = false;
        }
        var reward_item_defined;
        reward_item_defined = true;
        if (strlen(item_reward[counter]) < 2) {
            reward_item_defined = false;
        }
        var villain_defined;
        villain_defined = true;
        if (strlen(villain[counter]) < 2) {
            villain_defined = false;
            /*$kill_item_defined=false;*/
        }
        //if no villain igoner kill/needed item
        ///$key = array_search('green', $array); // $key = 2;
        var up_exit_id;
        up_exit_id = room_counter + 1;
        var down_exit_id;
        down_exit_id = room_counter - 1;
        if (up_exit_id > rooms_total + 1) {
            up_exit_id = 255;
        }
        if (down_exit_id < 2) {
            down_exit_id = 255;
        }
        //--------------------------//Locations--------------------------------------
        //$directions_line ===<directions n="255" s="255" e="255" w="255" ne="255" se="255" sw="255" nw="255" up="$up_exit_id" down="$down_exit_id" in="255" out="255" mass="0" />
        var directions_line;
        directions_line = generate_room_direction_line(room_counter);
        var loc1;
        loc1 = "LOC-HEREDOC@@@@@@@@@@@@@@@@@@@@@@@@@@";
        objects_locations = objects_locations + "\n\
" + loc1 + "";
        //-------------$objects_enemies---------------------------------------------------
        var id_villains;
        id_villains = room_counter + rooms_total;
        // increments by one each loop
        var vil1;
        vil1 = "VILLAINS-HEREDOC@@@@@@@@@@@@@@@@@@@@@@@@@@";
        if (!villain_defined) {
            vil1 = createDummyItem(id_villains);
        }
        //if villain NOT defined create dummy entry
        objects_enemies = objects_enemies + "\n\
" + vil1 + "";
        //---------$objects_items (kill items)-------------------------------------------------------
        //$objects_items
        var id_item;
        id_item = room_counter + rooms_total * 2;
        var found_item_again_check;
        found_item_again_check = array_search(convert2SafeName(item[counter]), item_reward_safenamearray);
        //echo "<h1> ".convert2SafeName($item[$counter])." </h1>"; print_r($item_reward_safenamearray);
        //if ($found_item_again_check>-1)  echo "<h1>FOUND ".convert2SafeName($item[$counter])." found_item_again_check=$found_item_again_check</h1>";
        //$items_not_created_list[]=$id_item; //MIGHT NOT NEEDED
        if (!(found_item_again_check > -1) && kill_item_defined) {
            // if item NOT found in reward items THEN create it OR item
            var items1;
            items1 = "ITEMS-HEREDOC@@@@@@@@@@@@@@@@@@@@@@@@@@";
        } else {
            items1 = createDummyItem(id_item);
        }
        objects_items = objects_items + "\n\
" + items1 + "";
        //---------$objects_items_reward-------------------------------------------------------
        //$objects_items
        var id_item_reward;
        id_item_reward = room_counter + rooms_total * 3;
        var items_reward1;
        items_reward1 = "ITEMS2-HEREDOC@@@@@@@@@@@@@@@@@@@@@@@@@@";
        if (!reward_item_defined) {
            items_reward1 = createDummyItem(id_item_reward);
        }
        //if reward NOT defined crete dummy entry
        objects_items_reward = objects_items_reward + "\n\
" + items_reward1 + "";
        //-------$checks_text---------------------------------------------------------
        if (villain_defined && kill_item_defined) {
            //add more exits
            //echo "<h1>room_counter:$room_counter : connections_array=$connections_array[$room_counter] </H1>"; print_r($connections_array[$room_counter]);
            var key;
            __loop2:
                for (key in connections_array[room_counter]) {
                    var value;
                    value = connections_array[room_counter][key];
                    //echo "<h1>room_counter:$room_counter : $key ";
                    var checks1;
                    checks1 = "<check verb=\"" + key + "\" check=\"check" + counter + "_" + cur_villain_name_no_space + "\" /> ";
                    checks_text = checks_text + "\n\
" + checks1 + "";
                }
        }
        //--------$sentences_text--------------------------------------------------------
        if (villain_defined && kill_item_defined) {
            ///
            var sentence1;
            sentence1 = "SENT-HEREDOC@@@@@@@@@@@@@@@@@@@@@@@@@@";
            sentences_text = sentences_text + "\n\
" + sentence1 + "";
        }
        //END of if($kill_item_defined) {
        //-----$usercheckblocks_text-----------------------------------------------------------
        if (villain_defined && kill_item_defined) {
            //if villain and kill items filled then block (if villain and killitem not filled then do NOT block access)
            var checkblock1;
            checkblock1 = "\n\
    <UserCheck name=\"check" + counter + "_" + cur_villain_name_no_space + "\">if ( " + cur_villain_name_no_space + ".location==player.location )\n\
    {\n\
         println(\"" + villain[counter] + " blocks that exit! \");\n\
         stop();\n\
    }\n\
    </UserCheck>\n\
  ";
            usercheckblocks_text = usercheckblocks_text + "\n\
" + checkblock1 + "";
        }
        //-----$routines_text-----------------------------------------------------------
        if (kill_item_defined) {
            var routine1;
            routine1 = "<routine name=\"kill_" + cur_villain_name_no_space + "\">println(\"You kill " + villain[counter] + " \");\n\
     " + cur_villain_name_no_space + ".location= offscreen;\n\
     " + cur_item_name_no_space + ".location= offscreen;" + "\n\
";
            if (reward_item_defined) {
                routine1 = routine1.cur_item_reward_name_no_space + ".location= player;";
            }
            routine1 = routine1 + "   </routine>";
            routines_text = routines_text + "\n\
" + routine1 + "";
        }
        //END of if($kill_item_defined) {
        //----EVENTS (show villain in room------------------------------------------------------------
        if (villain_defined) {
            var event1, villain_desc;
            event1 = "      <event name=\"same_room_" + cur_villain_name_no_space + "\">if(" + cur_villain_name_no_space + ".location==player.location) println(\"You see " + villain[counter] + " in the room! " + villain_desc[counter] + " \") ;\n\
  </event> ";
            event_text = event_text + "\n\
" + event1 + "";
        }
        //----------------------------------------------------------------
        room_counter++;
        counter++;
    }
// END of foreach ($room as $r) {
// Last check
var win_event;
win_event = "      <event name=\"win_condition_" + cur_villain_name_no_space + "\">if(" + cur_villain_name_no_space + ".location==offscreen) println(\"You killed " + villain[counter - 1] + "!!! You WON!\") ;\n\
</event> ";
event_text = event_text + "\n\
" + win_event + "";
if (debug1) {
    console.log("================================================");
    console.log("" + objects_locations + "");
    console.log("" + objects_enemies + "");
    console.log("" + objects_items + "");
    console.log(checks_text);
    console.log(sentences_text);
    console.log(usercheckblocks_text);
    console.log("================================================");
    //exit(0);
}
/*
NOTES:
<directions n="11" s="255" e="6" w="4" ne="255" se="255" sw="255" nw="255" up="255" down="255" in="255" out="255" mass="0" />
e ,w enter Location ID
*/
var objects_text;
objects_text = objects_locations.objects_enemies.objects_items.objects_items_reward;
if (true) {
    var text;
    text = "EOT-HEREDOC@@@@@@@@@@@@@@@@@@@@@@@@@@";
}


console.log(text);