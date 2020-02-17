<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<?php 
			$dir = array_slice(scandir("XC/jsons/"),2);
			foreach ($dir as $file) {
				echo "<script src='XC/jsons/$file'></script>\n";
			}
			$stringArray = json_encode($dir);
			echo "<script> var fileList = $stringArray;</script>";
		?>
		
		<title id="title"></title>
	</head>
	<body>
		<div>
			<select id="fileselect" onchange="showTable(this.value)">
				<option selected disabled>Choose Table</option>
			</select>
			<button onclick="newTable()">Create New Table</button>
		</div>
		<div id="table"></div>
	</body>
	<script>
		window.onload = function () {
			var select = document.getElementById("fileselect");
			for (var file of fileList) {
				var option = document.createElement("option");
				var filename = file.split('.').slice(0,-1).join('.');
				option.text = filename.replace(/_/g, " ");
				option.value = filename;
				select.add(option);
			}
		}
		function showTable(table) {
			document.getElementById("title").innerHTML = table.replace(/_/g, " ");
			var div = document.getElementById("table");
			let data = window[table];
			//console.log(data);
			var keys = Object.keys(data[data.length-1]);
			var table = "<table border='1'><tr>";
			for (var x of keys){
				table += "<th>"+x+"</th>";
			}
			table += "</tr>";
			for (var y in data){
				table += "<tr>";
				for (var z of keys){
					table += "<td>"+data[y][z]+"</td>";
				}
				table += "</tr>\n";
			}
			
			table += "</table>";
			div.innerHTML = table;
		}
		
		function newTable() {
			var body = document.getElementsByTagName("body")[0];
			let form = "<a href='.' onclick='return confirm(\"Are you sure?\")'>Back</a>";
			form += "<form id='form' target='_blank' action='/saveTable.php' method='post'>";
				form += "<div id='tableNameRow'><input id='tableName' placeholder='Table Name' onkeyup='this.style.outlineStyle=\"none\";displayNewTable()'></div>";
				form += "<div id='header'><input name='header1' onkeyup='updatePlaceholder(this.name,this.value);this.style.outlineStyle=\"none\";displayNewTable();' placeholder='Header 1' autocomplete='off' class='headerInput'><button type='button' id='headerButton' onclick='addHeader()'>Add Header</button></div>";
				form += "<div id='1' name='entry'><input id='entry11' placeholder='Entry 1' autocomplete='off' class='1 entryInput' onkeyup='displayNewTable()'><button type='button' id='entryButton' onclick='addEntry()'>Add Entry</button></div>"
			form += "</form><button id='submit' type='button' onclick='if(validateForm()){if (confirm(\"Are you sure you want to submit?\")){submitForm();}}'>Submit</button><div id='newTable'></div>";
			body.innerHTML = form;
		}
		
		function addHeader() {
			var headerButton = document.getElementById("headerButton");
			var newHeader = document.createElement("input");
			var count = document.getElementById("header").childNodes.length;
			newHeader.name = "header"+count;
			newHeader.className = "headerInput";
			newHeader.placeholder = "Header "+count;
			newHeader.setAttribute("onkeyup","updatePlaceholder(this.name,this.value);this.style.outlineStyle=\"none\";form.firstChild.firstChild.value;displayNewTable()");
			newHeader.setAttribute("autocomplete","off");
			document.getElementById("header").insertBefore(newHeader, headerButton);
			addCol(count);
			var removeButton = document.createElement("button");
			removeButton.id = "header"+count;
			var width = newHeader.clientWidth;
			removeButton.setAttribute("style", "width: "+width+"px");
			removeButton.type = "button";
			removeButton.innerHTML = "Remove Header "+count;
			removeButton.setAttribute("onclick", "removeHeader(this.id);displayNewTable()");
			document.getElementById("tableNameRow").insertBefore(removeButton, null);//document.getElementById("tableName"));
		}
		function addCol(count) {
			var divs = document.getElementsByName("entry");
			var entryButton = document.getElementById("entryButton");
			//console.log(divs,entryButton);
			var entryNum = 1;
			entryButton.parentNode.removeChild(entryButton);
			for (var i of divs){
				var newinput = document.createElement("input");
				newinput.id = "entry"+entryNum+count;
				newinput.placeholder = "Entry "+count;
				newinput.setAttribute("autocomplete","off");
				newinput.className = count;
				newinput.className += " entryInput";
				newinput.setAttribute("onkeyup", "displayNewTable()");
				if (entryNum>1) {
					var removeEntryButton = i.getElementsByTagName("button")[0];
					i.insertBefore(newinput, removeEntryButton);
				}else {
					i.insertBefore(newinput, null);
				}
				entryNum++;
			}
			divs[divs.length-1].insertBefore(entryButton, null);
		}
		function addEntry() {
			var form = document.getElementById("form");
			var entryButton = document.getElementById("entryButton");
			//console.log(form);
			var newEntry = document.createElement("div");
			var count = document.getElementsByName("entry").length + 1;
			newEntry.id = count;
			newEntry.setAttribute("name", "entry");
			
			var headers = document.getElementById("header").childNodes;
			var inputNum = 1;
			for (var header of headers){
				if (header.nodeName != "BUTTON"){
					var newinput = document.createElement("input");
					newinput.id = "entry"+count+inputNum;
					newinput.placeholder = document.getElementById("entry1"+inputNum).placeholder;
					newinput.setAttribute("autocomplete","off");
					newinput.className = inputNum;
					newinput.className += " entryInput";
					newinput.setAttribute("onkeyup", "displayNewTable()");
					inputNum++;
					newEntry.insertBefore(newinput, null);
				}
			}
			entryButton.parentNode.removeChild(entryButton);
			//Create - button
				var removeButton = document.createElement("button");
				removeButton.type = "button";
				removeButton.setAttribute("onclick", "removeEntry(this.parentNode.id);displayNewTable()");
				removeButton.innerHTML = "-";
				
			newEntry.insertBefore(removeButton, null);
			newEntry.insertBefore(entryButton, null);
			
			form.insertBefore(newEntry, null);
		}
		function updatePlaceholder(hName,val) {
			var inputs = document.getElementsByClassName(hName.charAt(hName.length-1));
			if (hName!="header1"){
				document.getElementById(hName).innerHTML = "Remove "+val;
			}
			for (var i of inputs) {
				i.placeholder = val;
			}
		}
		function removeEntry(divID) {
			var entryButton = document.getElementById("entryButton");
			var div = document.getElementById(divID);
			if (entryButton.parentNode == div){
				var entries = document.getElementsByName("entry");
				var oneBefore = entries[entries.length-2];
				oneBefore.insertBefore(entryButton, null);
			}
			div.parentNode.removeChild(div);
		}
		function removeHeader(hName) {
			var entries = document.getElementsByName("entry");
			var header = document.getElementsByName(hName)[0];
			var headerButton = document.getElementById(hName);
			headerButton.parentNode.removeChild(headerButton);
			header.parentNode.removeChild(header);
			for (var i of entries) {
				i.removeChild(document.getElementById("entry"+i.id+hName.charAt(hName.length-1)));
			}
		}
		function validateForm() {
			var filledout = true;
			var tablename = document.getElementById("tableName");
			if (tablename.value === "") {
				tablename.style.outlineStyle = "solid";
				tablename.style.outlineColor = "#FF0000";
				filledout = false;
			}
			var headers = document.getElementById("header").children;
			for (var h of headers){
				if (h.value === "" && h.tagName=="INPUT") {
					h.style.outlineStyle = "solid";
					h.style.outlineColor = "#FF0000";
					filledout = false;
				}
			}
			return filledout;
		}
		function submitForm() {
			var headers = document.getElementsByClassName("headerInput");
			var entries = document.getElementsByName("entry");
			var tableName = document.getElementById("tableName").value;
			var strHeader = "";
			var arrEntries = [];
			for (var h of headers) {
				strHeader += h.value+"|";
			}
			strHeader = strHeader.substring(0,strHeader.length-1);
			var arrHeader = strHeader.split('|');
			entries.forEach(function (curr, idx, entries){
					var inputs = curr.children;
					var entry = "";
					for (var i of inputs){
						if (i.tagName=="INPUT"){
							//console.log(idx,i.value);
							if (i.value==""){
								entry += "null"+"|";
							}else{
								entry +=  i.value +"|";
							}
						}
					}
					entry = entry.substring(0,entry.length-1);
					arrEntry = entry.split('|');
					arrEntries.push(arrEntry);
			});
			console.log(arrHeader, JSON.stringify(arrEntries));
			fetch('saveTable.php',{
				method: 'post',
				headers: {'Content-Type': "application/x-www-form-urlencoded; charset=UTF-8"},
				body:	'tableName='+tableName + '&'
						+'headers='+JSON.stringify(arrHeader) + '&'
						+'data='+JSON.stringify(arrEntries)
				})
				.then(function (response) {
						//Check if server response was OK
						if (response.status !== 200) {
							console.log("There was an error with the server. Status Code: " + response.status);
							return;
						}
						//Process resonse
						response.json().then(function (data) {
							console.log('Success',data);
						}, function (err) {
							console.log("Response was not in valid JSON format.",err);
						})
					}
				).catch(function (err) {
					console.log("Error with Fetch: -S",err);
				});
		}
		
		function displayNewTable() {
			var tableDiv = document.getElementById("newTable");
			var form = document.getElementById("form");
			tableDiv.innerHTML = "<h2>"+form.getElementsByTagName("input").namedItem("tableName").value+"</h2>";
			
			var liveTable = document.createElement("table");
			liveTable.border = "1px";
			var tbody = document.createElement("tbody");
			
			//add headers to tbody
			var tr = document.createElement("tr");
			for (var h of form.getElementsByClassName("headerInput")){
				var th = document.createElement("th");
				th.innerHTML = h.value;
				tr.appendChild(th);
			}
			tbody.appendChild(tr);
			
			//add data to tbody
			for (var div of document.getElementsByName("entry")){
				tr = document.createElement("tr");
				for (var i of div.getElementsByTagName("input")){
					var td = document.createElement("td");
					td.innerHTML = i.value;
					tr.appendChild(td);
				}
				tbody.appendChild(tr);
			}
			liveTable.appendChild(tbody);
			tableDiv.appendChild(liveTable);
		}
		
	</script>
</html>