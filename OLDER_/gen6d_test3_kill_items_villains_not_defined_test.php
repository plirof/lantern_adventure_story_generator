<?php
/*

220624b - Dummy Kill & reward items & checks
220623a - Generated version 
220622 - initial version

ToDo:
+ ok: Connect rooms :Initially connect automatically rooms with Up and Down (by order) (Don)
+ ok: (missing ne,nw,se,sw)Connect rooms : Then parse the connection string for n=XX,e,w,s,up,down
+ ok: Using flatfile/CSV (seperated by |) HTML form table  :
Room  |  villain | villain desc  |  item needed | item desc | item dropped |item dropped desc |

item , villain should be created as objects first and place inside room/inventory (or maybe dropped by villain)


CSV import

*/
//Solution kill Conchita with ball attack,n,kill Luigi with pipe ,u,kill Jaws with dental floss  ,u,Kill Joker with Full House Attack ,u,Kill Dhalsim with Chun Li Attack ,u,Kill Sauron With Ring Attack ,u,kill thanos with infinity attack ,u,kill t 800 with emp attack ,u,Kill Van Damme with Blind Attack ,u,kill Chuck Norris with God Attack
//Solution kill Conchita with ball attack,u,kill Luigi with pipe ,u,kill thanos with infinity attack ,u,kill Jaws with dental floss ,u,Kill Zangief with Chun Li Attack ,u,Kill Sauron With Ring Attack ,u,Kill Joker with Full House Attack ,u,kill t 800 with emp attack ,u,Kill Van Damne with Blind Attack ,u,kill Chuck Norris with God Attack


// Videos attack : https://www.youtube.com/watch?v=BYLVxlLFuq0&list=PL8nDWbKG9fKbvgEJYp-nSPjX2c3L61rDw
// NPC blocker https://www.youtube.com/watch?v=LOFsWgJuywo&list=PL8nDWbKG9fKbvgEJYp-nSPjX2c3L61rDw
$debug1=false;
$counter=0;
$room_counter=2;
$write_to_lant_xml=true;

//$file = fopen('class-worklog-lantern/worklog.txt', 'r');
$file = fopen('worklog.txt', 'r');
while (($line = fgetcsv($file,null,"|")) !== FALSE) {
  //$line is an array of the csv elements
  list($room[],$room_desc[],$villain[],$villain_desc[],$item[],$item_desc[],$item_reward[],$item_reward_desc[],$exits[]) = $line;
  //print_r($line);echo "<hr size=10>";
}
fclose($file);

include "functions.php";

$rooms_total=sizeof($room);
$item_reward_safenamearray=convert2SafeNameArray($item_reward);
$items_not_created_list=array();//MIGHT NOT NEEDED CHECK!! hold here the items ids NOT created so we will link to the ID of the reward items


//$a=["luigi","mantis","jaws","zangief","ivan_drago","joker","t_800_model_101","van_damne","chuck_norris"];

$objects_locations="";
$objects_enemies="";
$objects_items="";
$sentences_text="";
$usercheckblocks_text="";
$checks_text="";
$routines_text="";
$event_text="";
$objects_items_reward="";

// PARSE ALL THE ROOMS (= file/table rows)
foreach ($room as $r) {

  $cur_room_name=$room[$counter];
  $cur_villain_name_no_space=strtolower(str_replace(' ', '_', $villain[$counter]));  
  $cur_item_name_no_space=strtolower(str_replace(' ', '_', $item[$counter]));  
  $cur_item_reward_name_no_space=strtolower(str_replace(' ', '_', $item_reward[$counter]));

  $kill_item_defined=true;
  if(strlen($item[$counter])<2)$kill_item_defined=false;
  $reward_item_defined=true;
  if(strlen($item_reward[$counter])<2)$reward_item_defined=false;
  $villain_defined=true;
  if(strlen($villain[$counter])<2) { $villain_defined=false;/*$kill_item_defined=false;*/ } //if no villain igoner kill/needed item



  ///$key = array_search('green', $array); // $key = 2;
  $up_exit_id=$room_counter+1;
  $down_exit_id=$room_counter-1;
  if($up_exit_id>$rooms_total+1) {$up_exit_id=255;}
  if($down_exit_id<2) {$down_exit_id=255;};

  //--------------------------//Locations--------------------------------------
  //$directions_line ===<directions n="255" s="255" e="255" w="255" ne="255" se="255" sw="255" nw="255" up="$up_exit_id" down="$down_exit_id" in="255" out="255" mass="0" />
  $directions_line=generate_room_direction_line($room_counter);
  $loc1 = <<<LOC
        <object id="$room_counter" holder="0" name="$cur_room_name" printedname="$cur_room_name">
        <description>$room_desc[$counter]</description>
        <initialdescription />
        $directions_line
        <flags scenery="0" portable="0" container="0" supporter="0" open="0" wearable="0" emittinglight="1" locked="0" lockable="0" beingworn="0" user1="0" door="0" user2="0" user3="0" user4="0" />
        <synonyms names="" />
        <nogo>
          <e />
          <in>You can't enter that.</in>
          <out>I don't know which way that is.</out>
        </nogo>
        <ImageId />
      </object>      
LOC ;

//  echo '      <routine name="kill_'.$name.'">println("You kill '.$name.'");luigi.location= offscreen;</routine>';
  //echo '<routine name="kill_'.$name.'">println("You kill '.$name.'"); luigi.location= offscreen;</routine> '."\n";
  $objects_locations=$objects_locations."\n".$loc1 ."";

//-------------$objects_enemies---------------------------------------------------
$id_villains=$room_counter+$rooms_total; // increments by one each loop


$vil1 = <<<VILLAINS
      <object id="$id_villains" holder="$room_counter" name="$cur_villain_name_no_space" printedname="$villain[$counter]">
        <description>$villain_desc[$counter]</description>
        <initialdescription />
        <directions n="255" s="255" e="255" w="255" ne="255" se="255" sw="255" nw="255" up="255" down="255" in="255" out="255" mass="0" />
        <flags scenery="1" portable="0" container="0" supporter="0" open="0" wearable="0" emittinglight="0" locked="0" lockable="0" beingworn="0" user1="0" door="0" user2="0" user3="0" user4="0" />
        <synonyms names="" />
        <nogo>
          <w />
          <e />
          <n />
          <in>You can't enter that.</in>
          <out>I don't know which way that is.</out>
        </nogo>
        <ImageId />
      </object>
VILLAINS;

if(!$villain_defined) $vil1=createDummyItem($id_villains); //if villain NOT defined create dummy entry

$objects_enemies=$objects_enemies."\n".$vil1 ."";

//---------$objects_items (kill items)-------------------------------------------------------
//$objects_items
$id_item=$room_counter+$rooms_total*2;


$found_item_again_check = array_search(convert2SafeName($item[$counter]),  $item_reward_safenamearray);
//echo "<h1> ".convert2SafeName($item[$counter])." </h1>"; print_r($item_reward_safenamearray);
//if ($found_item_again_check>-1)  echo "<h1>FOUND ".convert2SafeName($item[$counter])." found_item_again_check=$found_item_again_check</h1>";
//$items_not_created_list[]=$id_item; //MIGHT NOT NEEDED
if (!($found_item_again_check>-1) && $kill_item_defined ) { // if item NOT found in reward items THEN create it OR item
$items1 = <<<ITEMS
      <object id="$id_item" holder="1" name="$item[$counter]" printedname="$item[$counter]">
        <description>$item_desc[$counter]</description>
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
ITEMS;

} // END of if (!$found_item_again_check) {
else
{
  $items1=createDummyItem($id_item);
}
$objects_items=$objects_items."\n".$items1 ."";

//---------$objects_items_reward-------------------------------------------------------
//$objects_items
$id_item_reward=$room_counter+$rooms_total*3;

$items_reward1 = <<<ITEMS
      <object id="$id_item_reward" holder="0" name="$item_reward[$counter]" printedname="$item_reward[$counter]">
        <description>$item_reward_desc[$counter]</description>
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
ITEMS;

if(!$reward_item_defined) $items_reward1=createDummyItem($id_item_reward); //if reward NOT defined crete dummy entry

$objects_items_reward=$objects_items_reward."\n".$items_reward1 ."";

//-------$checks_text---------------------------------------------------------
//$checks_text=' <check verb="e" check="check1_mantis" />';
//$checks_text=' <check verb="w" check="check1_mantis" />';
//$checks_text=' <check verb="s" check="check1_mantis" />';
//$checks_text=' <check verb="n" check="check1_mantis" />';

if($villain_defined && $kill_item_defined) { //add more exits 
  //echo "<h1>room_counter:$room_counter : connections_array=$connections_array[$room_counter] </H1>"; print_r($connections_array[$room_counter]);

  foreach($connections_array[$room_counter] as $key=>$value){
    //echo "<h1>room_counter:$room_counter : $key ";    
    $checks1 = '<check verb="'.$key.'" check="check'.$counter.'_'.$cur_villain_name_no_space.'" /> ';
    $checks_text=$checks_text."\n".$checks1 ."";
  }
  /*
  $checks1 = '
  <check verb="up" check="check'.$counter.'_'.$cur_villain_name_no_space.'" /> \n
  <check verb="down" check="check'.$counter.'_'.$cur_villain_name_no_space.'" />  ;
  ';
  $checks_text=$checks_text."\n".$checks1 ."";
  */
}

//--------$sentences_text--------------------------------------------------------  
/*
$sentence1=<<<SENT

      <sentence verb="kill" do="$villain[$counter]" prep="" io="" type="instead" sub="kill_$cur_villain_name_no_space" /> \n
      <sentence verb="kill" do="$villain[$counter]" prep="with" io="$item[$counter]" type="instead" sub="kill_$cur_villain_name_no_space" />\n
      <sentence verb="kill" do="$villain[$counter]" prep="with" io="fist" type="instead" sub="kill_$cur_villain_name_no_space" />\n
      <sentence verb="kill" do="$villain[$counter]" prep="with" io="kick" type="instead" sub="kill_$cur_villain_name_no_space" />
SENT;
*/
if($villain_defined && $kill_item_defined) {  /// 
$sentence1=<<<SENT

      <sentence verb="kill" do="$villain[$counter]" prep="with" io="$item[$counter]" type="instead" sub="kill_$cur_villain_name_no_space" />\n
SENT;

$sentences_text=$sentences_text."\n".$sentence1."";
} //END of if($kill_item_defined) {

//-----$usercheckblocks_text-----------------------------------------------------------
if($villain_defined && $kill_item_defined){  //if villain and kill items filled then block (if villain and killitem not filled then do NOT block access)
  $checkblock1='
    <UserCheck name="check'.$counter.'_'.$cur_villain_name_no_space.'">if ( '.$cur_villain_name_no_space.'.location==player.location )
    {
         println("'.$villain[$counter].' blocks that exit! ");
         stop();
    }
    </UserCheck>
  ';
  $usercheckblocks_text=$usercheckblocks_text."\n".$checkblock1."";
}

//-----$routines_text-----------------------------------------------------------  

if($kill_item_defined) {

  $routine1='<routine name="kill_'.$cur_villain_name_no_space.'">println("You kill '.$villain[$counter].' ");
     '.$cur_villain_name_no_space.'.location= offscreen;
     '.$cur_item_name_no_space.'.location= offscreen;'."\n";

  if($reward_item_defined)$routine1=$routine1.$cur_item_reward_name_no_space.'.location= player;';
  $routine1=$routine1.'   </routine>';

  $routines_text=$routines_text."\n".$routine1."";
} //END of if($kill_item_defined) {

//----EVENTS (show villain in room------------------------------------------------------------
if($villain_defined){
  $event1='      <event name="same_room_'.$cur_villain_name_no_space.'">if('.$cur_villain_name_no_space.'.location==player.location) println("You see '.$villain[$counter].' in the room! '.$villain_desc[$counter].' ") ;
  </event> ';

  $event_text=$event_text."\n".$event1."";
}
//----------------------------------------------------------------  

  $room_counter++;
  $counter++;


}; // END of foreach ($room as $r) {
// Last check
$win_event='      <event name="win_condition_'.$cur_villain_name_no_space.'">if('.$cur_villain_name_no_space.'.location==offscreen) println("You killed '.$villain[$counter-1].'!!! You WON!") ;
</event> ';
$event_text=$event_text."\n".$win_event."";




if($debug1) {
  echo "$objects_locations";
  echo "$objects_enemies";
  echo "$objects_items";
  echo $checks_text;
  echo $sentences_text;
  echo $usercheckblocks_text;
  exit(0);
}
/*

NOTES:
<directions n="11" s="255" e="6" w="4" ne="255" se="255" sw="255" nw="255" up="255" down="255" in="255" out="255" mass="0" />
e ,w enter Location ID




*/


//ob_start();                      // start capturing output
//include('lantern.xml');   // execute the file
//$content = ob_get_contents();    // get the contents from the buffer
//ob_end_clean();                  // stop buffering and discard contents
//echo "<hr>".$content ;
//$text = <<<EOT



$objects_text=$objects_locations.$objects_enemies.$objects_items.$objects_items_reward;










if (true) {
$text = <<<EOT
<?xml version="1.0" encoding="utf-8"?>
<xml xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
  <project>
    <projname>DeathTower</projname>
    <welcome>Welcome To Tower Of Death!</welcome>
    <author>Firelord Quality Games</author>
    <language>English</language>
    <version>Version 1.0</version>
    <output>adventure</output>
    <preps>
      <prep>in</prep>
      <prep>on</prep>
      <prep>at</prep>
      <prep>under</prep>
      <prep>into</prep>
      <prep>inside</prep>
      <prep>through</prep>
      <prep>out</prep>
      <prep>behind</prep>
      <prep>off</prep>
      <prep>up</prep>
      <prep>with</prep>
      <prep>to</prep>
      <prep>off</prep>
      <prep>north</prep>
      <prep>south</prep>
      <prep>east</prep>
      <prep>west</prep>
      <prep>northeast</prep>
      <prep>southeast</prep>
      <prep>northwest</prep>
      <prep>southwest</prep>
      <prep>up</prep>
      <prep>down</prep>
      <prep>about</prep>
      <prep>over</prep>
      <prep>across</prep>
      <prep>for</prep>
    </preps>
    <verbs>
      <builtinverbs>
        <verb>n,go north,north</verb>
        <verb>s,go south,south</verb>
        <verb>e,go east,east</verb>
        <verb>w,go west,west</verb>
        <verb>ne,go northeast,northeast</verb>
        <verb>se,go southeast,southeast</verb>
        <verb>sw,go southwest,southwest</verb>
        <verb>nw,go northwest,northwest</verb>
        <verb>up,go up,u</verb>
        <verb>down,go down,d</verb>
        <verb>enter,go in,go inside,get in</verb>
        <verb>out</verb>
        <verb>go</verb>
        <verb>get,take,grab,pick up</verb>
        <verb>give</verb>
        <verb>inventory,i</verb>
        <verb>kill,attack</verb>
        <verb>drop</verb>
        <verb>light</verb>
        <verb>look,l</verb>
        <verb>examine,x,look at</verb>
        <verb>look in</verb>
        <verb>search</verb>
        <verb>open</verb>
        <verb>lock</verb>
        <verb>unlock</verb>
        <verb>close,shut</verb>
        <verb>eat</verb>
        <verb>drink</verb>
        <verb>put,place</verb>
        <verb>quit</verb>
        <verb>smell,sniff</verb>
        <verb>listen</verb>
        <verb>wait</verb>
        <verb>climb</verb>
        <verb>yell,scream,shout</verb>
        <verb>jump</verb>
        <verb>talk to</verb>
        <verb>turn on</verb>
        <verb>turn off</verb>
        <verb>wear,put on</verb>
        <verb>save</verb>
        <verb>restore</verb>
        <verb>push,press</verb>
        <verb>read</verb>
        <verb>use</verb>
        <verb>again</verb>
      </builtinverbs>
      <userverbs />
    </verbs>
    <objects>
      <object id="0" holder="0" name="Offscreen" printedname="Offscreen">
        <description>Offscreen.  Move objects here to remove them from the world.</description>
        <initialdescription />
        <directions n="255" s="255" e="255" w="255" ne="255" se="255" sw="255" nw="255" up="255" down="255" in="255" out="255" mass="0" />
        <flags scenery="0" portable="0" container="0" supporter="0" transparent="0" openable="0" open="0" wearable="0" emittinglight="0" locked="0" lockable="0" beingworn="0" user1="0" door="0" user2="0" user3="0" user4="0" />
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
          <in />
          <out />
        </nogo>
        <ImageId />
      </object>
      <object id="1" holder="2" name="player" printedname="player">
        <description>You're a wonderful person. You shouldn't care what you look like.</description>
        <initialdescription />
        <directions n="255" s="255" e="255" w="255" ne="255" se="255" sw="255" nw="255" up="255" down="255" in="255" out="255" mass="0" />
        <flags scenery="0" portable="0" container="0" supporter="0" transparent="0" openable="0" open="0" wearable="0" emittinglight="0" locked="0" lockable="0" beingworn="0" user1="0" door="0" user2="0" user3="0" user4="0" />
        <synonyms names="me,self" />
        <nogo>
          <w />
          <e />
          <in>You can't enter that.</in>
          <out>I don't know which way that is.</out>
        </nogo>
        <ImageId />
      </object>
      $objects_text
    </objects>
    <checks>
      <check verb="n" check="check_move" />
      <check verb="s" check="check_move" />
      <check verb="e" check="check_move" />
      <check verb="w" check="check_move" />
      <check verb="u" check="check_move" />
      <check verb="d" check="check_move" />
      <check verb="ne" check="check_move" />
      <check verb="nw" check="check_move" />
      <check verb="se" check="check_move" />
      <check verb="sw" check="check_move" />
      <check verb="close" check="check_dobj_supplied" />
      <check verb="close" check="check_dobj_opnable" />
      <check verb="close" check="check_dobj_open" />
      <check verb="drink" check="check_dobj_supplied" />
      <check verb="drink" check="check_have_dobj" />
      <check verb="drop" check="check_dobj_supplied" />
      <check verb="drop" check="check_have_dobj" />
      <check verb="eat" check="check_dobj_supplied" />
      <check verb="eat" check="check_have_dobj" />
      <check verb="enter" check="check_dobj_supplied" />
      <check verb="enter" check="check_see_dobj" />
      <check verb="enter" check="check_move" />
      <check verb="out" check="check_move" />
      <check verb="examine" check="check_dobj_supplied" />
      <check verb="examine" check="check_see_dobj" />
      <check verb="get" check="check_dobj_supplied" />
      <check verb="get" check="check_see_dobj" />
      <check verb="get" check="check_dont_have_dobj" />
      <check verb="get" check="check_dobj_portable" />
      <check verb="get" check="check_weight" />
      <check verb="kill" check="check_dobj_supplied" />
      <check verb="kill" check="check_see_dobj" />
      <check verb="light" check="check_dobj_supplied" />
      <check verb="light" check="check_have_dobj" />
      <check verb="light" check="check_see_dobj" />
      <check verb="open" check="check_dobj_supplied" />
      <check verb="open" check="check_see_dobj" />
      <check verb="open" check="check_dobj_opnable" />
      <check verb="open" check="check_dobj_unlocked" />
      <check verb="put" check="check_dobj_supplied" />
      <check verb="put" check="check_prep_supplied" />
      <check verb="put" check="check_iobj_supplied" />
      <check verb="put" check="check_not_self_or_child" />
      <check verb="put" check="check_have_dobj" />
      <check verb="put" check="check_see_iobj" />
      <check verb="put" check="check_put" />
      <check verb="talk to" check="check_dobj_supplied" />
      <check verb="talk to" check="check_see_dobj" />
      <check verb="turn on" check="check_dobj_supplied" />
      <check verb="turn on" check="check_see_dobj" />
      <check verb="lock" check="check_dobj_supplied" />
      <check verb="lock" check="check_see_dobj" />
      <check verb="lock" check="check_dobj_lockable" />
      <check verb="unlock" check="check_dobj_supplied" />
      <check verb="unlock" check="check_see_dobj" />
      <check verb="unlock" check="check_dobj_lockable" />
      <check verb="look in" check="check_dobj_supplied" />
      <check verb="look in" check="check_see_dobj" />
      <check verb="wear" check="check_see_dobj" />
      <check verb="use" check="check_have_dobj" />
      <check verb="wear" check="check_have_dobj" />
      <check verb="wear" check="check_dobj_wearable" />
      <check verb="give" check="check_dobj_supplied" />
      <check verb="give" check="check_have_dobj" />
      <check verb="give" check="check_iobj_supplied" />
      <check verb="give" check="check_see_iobj" />
      <check verb="push" check="check_dobj_supplied" />
      <check verb="push" check="check_see_dobj" />
      <check verb="read" check="check_dobj_supplied" />
      <check verb="read" check="check_see_dobj" />
      $checks_text
    </checks>
    <sentences>
      <sentence verb="wear" do="*" prep="" io="" type="before" sub="get_portable" />
      <sentence verb="take" do="PLAYER" prep="" io="" type="instead" sub="not_possible" />
      <sentence verb="kill" do="PLAYER" prep="" io="" type="instead" sub="kill_self" />
      <sentence verb="kill" do="*" prep="" io="" type="instead" sub="default_kill" />
      <sentence verb="talk to" do="PLAYER" prep="" io="" type="instead" sub="talk_to_self" />
      <sentence verb="talk to" do="*" prep="" io="" type="instead" sub="default_talk" />
      <sentence verb="listen" do="" prep="" io="" type="instead" sub="listen" />
      <sentence verb="wait" do="" prep="" io="" type="instead" sub="wait" />
      <sentence verb="yell" do="" prep="" io="" type="instead" sub="yell" />
      <sentence verb="jump" do="" prep="" io="" type="instead" sub="jump" />
      <sentence verb="eat" do="*" prep="" io="" type="instead" sub="default_eat" />
      <sentence verb="drink" do="*" prep="" io="" type="instead" sub="default_drink" />
      <sentence verb="smell" do="*" prep="" io="" type="instead" sub="smell" />
      <sentence verb="take" do="*" prep="" io="" type="after" sub="report_take" />
      <sentence verb="drop" do="*" prep="" io="" type="after" sub="report_drop" />
      <sentence verb="close" do="*" prep="" io="" type="after" sub="report_closed" />
      <sentence verb="wear" do="*" prep="" io="" type="after" sub="report_wear" />
      $sentences_text
    </sentences>
    <routines>
      <routine name="game_start">//put any startup code or messages here
</routine>
      <routine name="reset">//put reset code here
</routine>
      <routine name="not_possible">if (dobj == player) { println("Not physically possible.");  } </routine>
      <routine name="get_portable">if (dobj.portable == 1) { if (dobj.holder != player) { println("(Taken)"); dobj.holder = player;}  } </routine>
      <routine name="kill_self">println("If you are experiencing suicidal thoughts you should seek psychiatric help.");</routine>
      <routine name="default_kill">println("Perhaps you should count to 3 and calm down.");</routine>
      <routine name="kill_player">println("***YOU HAVE DIED***.");
reset();
</routine>
      <routine name="talk_to_self">println("Talking to yourself is a sign of impending mental collapse.");</routine>
      <routine name="default_talk">println("That fails to produce an exciting conversation.");</routine>
      <routine name="listen">println("You hear nothing unexpected.");</routine>
      <routine name="smell">println("You smell nothing unexpected.");</routine>
      <routine name="wait">println("Time passes...");</routine>
      <routine name="yell">println("AAAAAAAAAAAAARRRRGGGGGG!");</routine>
      <routine name="jump">println("WHEEEEEE!");</routine>
      <routine name="default_eat">println("That's not part of a healthy diet.");</routine>
      <routine name="default_drink">println("You can't drink that.");</routine>
      <routine name="quit_sub">println("[Note: you can't quit the test client]");</routine>
      <routine name="report_take">println("Taken.");</routine>
      <routine name="report_drop">println("Dropped.");</routine>
      <routine name="report_closed">println("Closed.");</routine>
      <routine name="report_wear">print("You put on the ");
printname(dobj);
println(".");</routine>
$routines_text
    </routines>
    <CheckFunctions>
    $usercheckblocks_text
    </CheckFunctions>
    <events>
$event_text
    </events>
    <variables>
      <builtin>
        <var name="dobj" addr="dobjId" value="0" />
        <var name="iobj" addr="iobjId" value="0" />
        <var name="score" addr="score" value="0" />
        <var name="moves" addr="moves" value="0" />
        <var name="health" addr="health" value="100" />
        <var name="turnsWithoutLight" addr="turnsWithoutLight" value="0" />
        <var name="gameOver" addr="gameOver" value="0" />
        <var name="answer" addr="answer" value="0" />
        <var name="maxWeight" addr="maxWeight" value="10" />
        <var name="invWeight" addr="invWeight" value="0" />
      </builtin>
      <user />
    </variables>
    <Arrays />
    <walkthrough>kill Conchita with ball attack,n,kill Luigi with pipe ,u,kill Jaws with dental floss  ,u,Kill Joker with Full House Attack ,u,Kill Zangief with Chun Li Attack ,u,Kill Sauron With Ring Attack ,u,kill thanos with infinity attack ,u,kill t 800 with emp attack ,u,Kill Van Damme with Blind Attack ,u,kill Chuck Norris with God Attack</walkthrough>
    <BuildSettings>
      <SpectrumBorder>0</SpectrumBorder>
      <SpectrumPen>7</SpectrumPen>
      <SpectrumPaper>0</SpectrumPaper>
      <SpectrumFont />
      <BrightPalette>true</BrightPalette>
      <EnableGraphics>true</EnableGraphics>
      <C64LoadScreen />
      <CPMPostBuildScript />
      <C64Border>0</C64Border>
      <C64BG>2</C64BG>
      <C64FG>3</C64FG>
      <StatusLine>true</StatusLine>
    </BuildSettings>
    <StringDefs>
      <Strings>
        <StringDef>
          <StringName>LeadingA</StringName>
          <Z80name>leadinga</Z80name>
          <SF02Name>leadingA</SF02Name>
          <Value>A </Value>
        </StringDef>
        <StringDef>
          <StringName>The</StringName>
          <Z80name>the</Z80name>
          <SF02Name>the</SF02Name>
          <Value>The </Value>
        </StringDef>
        <StringDef>
          <StringName>Pardon</StringName>
          <Z80name>pardonstr</Z80name>
          <SF02Name>pardon</SF02Name>
          <Value>Pardon?</Value>
        </StringDef>
        <StringDef>
          <StringName>DontUnderstand</StringName>
          <Z80name>confused</Z80name>
          <SF02Name>confused</SF02Name>
          <Value>I don't follow you.</Value>
        </StringDef>
        <StringDef>
          <StringName>Done</StringName>
          <Z80name>done</Z80name>
          <SF02Name>done</SF02Name>
          <Value>Done.</Value>
        </StringDef>
        <StringDef>
          <StringName>Is</StringName>
          <Z80name>is</Z80name>
          <SF02Name>is</SF02Name>
          <Value> is...</Value>
        </StringDef>
        <StringDef>
          <StringName>InThe</StringName>
          <Z80name>inthe </Z80name>
          <SF02Name>inthe </SF02Name>
          <Value>In the </Value>
        </StringDef>
        <StringDef>
          <StringName>Onthe</StringName>
          <Z80name>onthe</Z80name>
          <SF02Name>onthe</SF02Name>
          <Value>On the </Value>
        </StringDef>
        <StringDef>
          <StringName>NotOpenable</StringName>
          <Z80name>notopenable</Z80name>
          <SF02Name>notOpenable</SF02Name>
          <Value>That's not openable.</Value>
        </StringDef>
        <StringDef>
          <StringName>NotCloseable</StringName>
          <Z80name>notcloseable</Z80name>
          <SF02Name>notcloseable</SF02Name>
          <Value>That's not closeable.</Value>
        </StringDef>
        <StringDef>
          <StringName>AlreadyOpen</StringName>
          <Z80name>alreadyopen</Z80name>
          <SF02Name>alreadyOpen</SF02Name>
          <Value>It's already open.</Value>
        </StringDef>
        <StringDef>
          <StringName>AlreadyClosed</StringName>
          <Z80name>alreadyclosed</Z80name>
          <SF02Name>alreadyClosed</SF02Name>
          <Value>It's already closed.</Value>
        </StringDef>
        <StringDef>
          <StringName>AlreadyHave</StringName>
          <Z80name>alreadyhave</Z80name>
          <SF02Name>alreadyHave</SF02Name>
          <Value>You already have that.</Value>
        </StringDef>
        <StringDef>
          <StringName>CantTake</StringName>
          <Z80name>notportable</Z80name>
          <SF02Name>notPortable</SF02Name>
          <Value>You can't take that.</Value>
        </StringDef>
        <StringDef>
          <StringName>CantDo</StringName>
          <Z80name>cantDoThat</Z80name>
          <SF02Name>cantDoThat</SF02Name>
          <Value>You can't do that.</Value>
        </StringDef>
        <StringDef>
          <StringName>Carrying</StringName>
          <Z80name>carrying</Z80name>
          <SF02Name>carrying</SF02Name>
          <Value>You are carrying...</Value>
        </StringDef>
        <StringDef>
          <StringName>TooHeavy</StringName>
          <Z80name>tooheavystr</Z80name>
          <SF02Name>tooHeavy</SF02Name>
          <Value>You can't carry any more.</Value>
        </StringDef>
        <StringDef>
          <StringName>HaveNothing</StringName>
          <Z80name>noitems</Z80name>
          <SF02Name>emptyhanded</SF02Name>
          <Value>You are empty handed.</Value>
        </StringDef>
        <StringDef>
          <StringName>CantSee</StringName>
          <Z80name>pitchdark</Z80name>
          <SF02Name>noLight</SF02Name>
          <Value>It is pitch dark.</Value>
        </StringDef>
        <StringDef>
          <StringName>DontHave</StringName>
          <Z80name>donthave</Z80name>
          <SF02Name>dontHave</SF02Name>
          <Value>You don't have that.</Value>
        </StringDef>
        <StringDef>
          <StringName>DontSee</StringName>
          <Z80name>dontseestr</Z80name>
          <SF02Name>dontsee</SF02Name>
          <Value>You don't see that here.</Value>
        </StringDef>
        <StringDef>
          <StringName>CantSeeIn</StringName>
          <Z80name>cantlook</Z80name>
          <SF02Name>noPeek</SF02Name>
          <Value>You can't see inside that.</Value>
        </StringDef>
        <StringDef>
          <StringName>FindNothing</StringName>
          <Z80name>nothing</Z80name>
          <SF02Name>itsEmpty</SF02Name>
          <Value>You find nothing.</Value>
        </StringDef>
        <StringDef>
          <StringName>CantOpen</StringName>
          <Z80name>cantopen</Z80name>
          <SF02Name>cantopen</SF02Name>
          <Value>You can't open that.</Value>
        </StringDef>
        <StringDef>
          <StringName>CantEnter</StringName>
          <Z80name>noenter</Z80name>
          <SF02Name>noenter</SF02Name>
          <Value>You can't enter that.</Value>
        </StringDef>
        <StringDef>
          <StringName>NotContainer</StringName>
          <Z80name>notcontainer</Z80name>
          <SF02Name>notContainer</SF02Name>
          <Value>You can't put things in that.</Value>
        </StringDef>
        <StringDef>
          <StringName>NotSupporter</StringName>
          <Z80name>notsupporter</Z80name>
          <SF02Name>nosurface</SF02Name>
          <Value>You find no suitable surface.</Value>
        </StringDef>
        <StringDef>
          <StringName>CantWear</StringName>
          <Z80name>notwearable</Z80name>
          <SF02Name>notwearable</SF02Name>
          <Value>You can't wear that.</Value>
        </StringDef>
        <StringDef>
          <StringName>AlreadyWearing</StringName>
          <Z80name>alreadyworn</Z80name>
          <SF02Name>alreadyWorn</SF02Name>
          <Value>You're already wearing that.</Value>
        </StringDef>
        <StringDef>
          <StringName>Opening</StringName>
          <Z80name>openingThe</Z80name>
          <SF02Name>openningThe</SF02Name>
          <Value>Opening the </Value>
        </StringDef>
        <StringDef>
          <StringName>Reveals</StringName>
          <Z80name>reveals</Z80name>
          <SF02Name>reveals</SF02Name>
          <Value> reveals...</Value>
        </StringDef>
        <StringDef>
          <StringName>ItsClosed</StringName>
          <Z80name>closed</Z80name>
          <SF02Name>itsClosed</SF02Name>
          <Value>It's closed.</Value>
        </StringDef>
        <StringDef>
          <StringName>IsClosed</StringName>
          <Z80name>isclosed</Z80name>
          <SF02Name>isclosed</SF02Name>
          <Value> is closed.</Value>
        </StringDef>
        <StringDef>
          <StringName>IsEmpty</StringName>
          <Z80name>isempty</Z80name>
          <SF02Name>isempty</SF02Name>
          <Value> is empty.</Value>
        </StringDef>
        <StringDef>
          <StringName>IsLocked</StringName>
          <Z80name>isLocked</Z80name>
          <SF02Name>isLocked</SF02Name>
          <Value> is locked.</Value>
        </StringDef>
        <StringDef>
          <StringName>ItsLocked</StringName>
          <Z80name>itslocked</Z80name>
          <SF02Name>itslocked</SF02Name>
          <Value>It's locked.</Value>
        </StringDef>
        <StringDef>
          <StringName>DoorClosed</StringName>
          <Z80name>doorclosed</Z80name>
          <SF02Name>doorclosed</SF02Name>
          <Value>The door is closed.</Value>
        </StringDef>
        <StringDef>
          <StringName>ThereIsA</StringName>
          <Z80name>thereisa</Z80name>
          <SF02Name>thereisa</SF02Name>
          <Value>There is a </Value>
        </StringDef>
        <StringDef>
          <StringName>Here</StringName>
          <Z80name>here</Z80name>
          <SF02Name>here</SF02Name>
          <Value> here.</Value>
        </StringDef>
        <StringDef>
          <StringName>Contains</StringName>
          <Z80name>contains</Z80name>
          <SF02Name>contains</SF02Name>
          <Value> contains...</Value>
        </StringDef>
        <StringDef>
          <StringName>ItsNotLocked</StringName>
          <Z80name>notlocked</Z80name>
          <SF02Name>notlocked</SF02Name>
          <Value>It's not locked.</Value>
        </StringDef>
        <StringDef>
          <StringName>NotLockable</StringName>
          <Z80name>notLockable</Z80name>
          <SF02Name>notLockable</SF02Name>
          <Value>It's not locked.</Value>
        </StringDef>
        <StringDef>
          <StringName>AlreadyLocked</StringName>
          <Z80name>alreadyLocked</Z80name>
          <SF02Name>alreadyLocked</SF02Name>
          <Value>It's already locked.</Value>
        </StringDef>
        <StringDef>
          <StringName>AlreadyUnlocked</StringName>
          <Z80name>alreadyUnlocked</Z80name>
          <SF02Name>alreadyUnlocked</SF02Name>
          <Value>It's already unlocked.</Value>
        </StringDef>
        <StringDef>
          <StringName>NotPossible</StringName>
          <Z80name>impossible</Z80name>
          <SF02Name>impossible</SF02Name>
          <Value>That's not possible.</Value>
        </StringDef>
        <StringDef>
          <StringName>WhichWay</StringName>
          <Z80name />
          <SF02Name />
          <Value>I don't know which way that is.</Value>
        </StringDef>
        <StringDef>
          <StringName>BadPut</StringName>
          <Z80name>badput</Z80name>
          <SF02Name>badput</SF02Name>
          <Value>That would violate the laws of physics.</Value>
        </StringDef>
        <StringDef>
          <StringName>ProvidingLight</StringName>
          <Z80name>providingLight</Z80name>
          <SF02Name>providingLight</SF02Name>
          <Value> (providing light)</Value>
        </StringDef>
        <StringDef>
          <StringName>BeingWorn</StringName>
          <Z80name>beingworn</Z80name>
          <SF02Name>beingWorn</SF02Name>
          <Value> (being worn)</Value>
        </StringDef>
        <StringDef>
          <StringName>MissingNoun</StringName>
          <Z80name>missingnoun</Z80name>
          <SF02Name>missingDobj</SF02Name>
          <Value>Missing noun.</Value>
        </StringDef>
        <StringDef>
          <StringName>MissingPrep</StringName>
          <Z80name>missingprep</Z80name>
          <SF02Name>missingPrep</SF02Name>
          <Value>Missing preposition.</Value>
        </StringDef>
        <StringDef>
          <StringName>MissingNoun2</StringName>
          <Z80name>missing_io</Z80name>
          <SF02Name>missing_io</SF02Name>
          <Value>Missing second noun.</Value>
        </StringDef>
        <StringDef>
          <StringName>BadWord</StringName>
          <Z80name>dontknowstr</Z80name>
          <SF02Name>badword</SF02Name>
          <Value>I don't know the word '</Value>
        </StringDef>
        <StringDef>
          <StringName>BadVerb</StringName>
          <Z80name>badverbstr</Z80name>
          <SF02Name>badverb</SF02Name>
          <Value>I don't know the verb '</Value>
        </StringDef>
        <StringDef>
          <StringName>Ambiguous</StringName>
          <Z80name>ambigstr</Z80name>
          <SF02Name>ambig</SF02Name>
          <Value>I don't know which one you mean.</Value>
        </StringDef>
        <StringDef>
          <StringName>Bye</StringName>
          <Z80name>bye</Z80name>
          <SF02Name>bye</SF02Name>
          <Value>Bye.</Value>
        </StringDef>
      </Strings>
    </StringDefs>
    <YouCantGoThatWay>You can't go that way.</YouCantGoThatWay>
    <DefaultDescription>You notice nothing unexpected.</DefaultDescription>
  </project>
</xml>
EOT;
}



echo $text;
if($write_to_lant_xml) {
  $myfile = fopen("lant.xml", "w") or die("Unable to open file!");
  fwrite($myfile, $text);
  fclose($myfile);
}


//echo $text;

?>
