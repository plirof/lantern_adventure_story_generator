
//define data array
var tabledata = [
    // Solution : kill smurf with cross,up,kill gargamel with sword,up, kill guardian1 with nail,up,kill guardian2 with helmet
    { room_name:"Entrance", room_desc:"Oli Bob room", villain:"smurf", villain_desc:"bad smurf", killitem:"cross", killitem_desc:"rusty cross", reward:"medal", reward_desc:"Oli Bob", exits:"n=Lobby,up=Lobby"},
    { room_name:"Lobby", room_desc:"Lobby room", villain:"gargamel", villain_desc:"bad garg", killitem:"sword", killitem_desc:"rusty shield", reward:"milk", reward_desc:"Oli Bob", exits:"up=Floor 1"},
    { room_name:"Floor 1", room_desc:"Floor 1room", villain:"guardian1", villain_desc:"bad smurf", killitem:"nail", killitem_desc:"rusty nail", reward:"box", reward_desc:"Oli Bob", exits:"up=Floor 2"},
    { room_name:"Floor 2", room_desc:"Floor 2 room", villain:"guardian2", villain_desc:"bad garg", killitem:"helmet", killitem_desc:"rusty helmet", reward:"watermelon", reward_desc:"Oli Bob", exits:""},

];


//Build Tabulator
var table = new Tabulator("#example-table", {
    height:"311px",
    addRowPos:"bottom",
    data:tabledata, //assign data to table
    columns:[
        {title:"Room name", field:"room_name", width:100, editor:"input"},
        {title:"Room Desc", field:"room_desc", width:100, editor:"input"},
        {title:"villain name", field:"villain", width:100, editor:"input"},
        {title:"villain Desc", field:"villain_desc", width:100, editor:"input"},
        {title:"item needed", field:"killitem", width:100, editor:"input"},
        {title:"item needed Desc", field:"killitem_desc", width:100, editor:"input"},
        {title:"reward item name", field:"reward", width:100, editor:"input"},
        {title:"reward item Desc", field:"reward_desc", width:100, editor:"input"},                
        {title:"Exits", field:"exits", width:200, editor:"input"},
    ],
    downloadConfig:{
        columnHeaders:false, //do not include column headers in downloaded table
        columnGroups:false, //do not include column groups in column headers for downloaded table
        rowGroups:false, //do not include row groups in downloaded table
        columnCalcs:false, //do not include column calcs in downloaded table
        dataTree:false, //do not include data tree in downloaded table
    },    
    downloadReady:function(fileContents, blob){
        //fileContents - the unencoded contents of the file
        //blob - the blob object for the download
        console.log(fileContents);
        //JON send request with AJAX to php and get result!
        //custom action to send blob to server could be included here
        //fetch("prosecc.php", {
        
        /*
        var res_store;
        fetch("return_request.php", {    
          method: "POST",
          headers: {'Content-Type': 'application/json'}, 
          body: JSON.stringify(fileContents)
        }).then(res => {
          //console.log("Request complete! response:", res);
           res.text().then(function (text) {
            // do something with the text response 
            console.log("TEXT! response:", text);
            });
        });
        //console.log("Request complete! response  res_store:",  res_store);
        */
        tmp=fileContents;
        tmp = tmp.replace( /\n/g, "<br>"); // replace line breaks with <br> tags
        tmp = tmp.replace( /\"/g, ""); // replace line breaks with <br> tags

        document.getElementById('mytext').value = tmp ; // Just replace <BR> with \n server side
        //return false; //Do not download Just send to textarea
        //return fileContents;
        return blob; //must return a blob to proceed with the download, return false to abort download
    },


});

//Add row on "Add Row" button click
document.getElementById("add-row").addEventListener("click", function(){
    table.addRow({});
});
/*
//Delete row on "Delete Row" button click
document.getElementById("del-row").addEventListener("click", function(){
    table.deleteRow(1);
});
*/
//Clear table on "Empty the table" button click
document.getElementById("clear").addEventListener("click", function(){
    table.clearData()
});

//Reset table contents on "Reset the table" button click
document.getElementById("reset").addEventListener("click", function(){
    table.setData(tabledata);
});
/*
//Download table contents on "download" button click
document.getElementById("download").addEventListener("click", function(){
    //table.setData(tabledata);
    //table.download("string", "data.txt");
    table.download("csv", "data.csv", {delimiter:"|"});

});
*/
//copy to textarea (id:mytext)
document.getElementById("copy-table").addEventListener("click", function(){
    //table.setData(tabledata);
    //table.download("string", "data.txt");
    console.log("COPY BUTTON");
    //console.log(table.download("csv", "data.csv", {delimiter:"|"}));
    table.download("csv", "gamedata.csv", {delimiter:"|"});
    //table.download("csv", "data.csv", {delimiter:"|"});

});


//function customCSVImporter(fileContents){
//    return fileContents;
//}
Tabulator.extendModule("import", "importers", {
    csvCustom:function(fileContents){
    	//console.log("AAAAAAAAAAAAAAAAA",fileContents);
		tmp=fileContents;
        tmp = tmp.replace( /\|/g, ','); // replace line breaks with <br> tags
        //console.log("BBBBBBBBBBBBBBBB",tmp);

    	table = new Tabulator("#example-table", {
    	    columns:[
		        {title:"Room name", field:"room_name", width:100, editor:"input"},
		        {title:"Room Desc", field:"room_desc", width:100, editor:"input"},
		        {title:"villain name", field:"villain", width:100, editor:"input"},
		        {title:"villain Desc", field:"villain_desc", width:100, editor:"input"},
		        {title:"item needed", field:"killitem", width:100, editor:"input"},
		        {title:"item needed Desc", field:"killitem_desc", width:100, editor:"input"},
		        {title:"reward item name", field:"reward", width:100, editor:"input"},
		        {title:"reward item Desc", field:"reward_desc", width:100, editor:"input"},                
		        {title:"Exits", field:"exits", width:200, editor:"input"},
		    ],
		    //data:fileContents,
		    data:tmp,
		    importFormat:"csv",
    		//autoColumns:true,
		});



		let dummytext = '{ "employees" : [' +
		'{ "firstName":"John" , "lastName":"Doe" },' +
		'{ "firstName":"Peter" , "lastName":"Jones" } ]}';
    	//table.setData(fileContents);
        //return fileContents;
        //return str_getcsv(fileContents);
        return  JSON.parse(dummytext);
        //return  "true";
    },
});


//upload-csv
document.getElementById("upload-csv").addEventListener("click", function(){
    //table.import("csv", ".csv", {delimiter:"|"});
   // for (v in this) console.log(v," = ",v.value);
    //table.import("csv", ".csv")    

    table.import("csvCustom", ".csv")    
	.then(() => {
		//for (v in this) console.log(v," = ",v.value);
		console.log("hello upload-csv THEN.......");
	    //console.log(textContent);
	    //file successfully imported
	    //console.log(fileContents);
	    //console.log("imported: ",text);
	})
	.catch(() => {
    //something went wrong
    	console.log("upload-csv CATCH...");
	})


});