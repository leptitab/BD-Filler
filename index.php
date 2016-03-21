<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
	</head>
	<body>
		<select id="tables">
		</select>
		<button id="add">add new</button>
		<div id="newContent"></div>
		<table id="contenu"></table>
	</body>
	<script>
		$(document).ready(function(){
			$.ajax({
				type: "POST",
				data: {action: "getTable"},
				url: "requete.php",
				dataType: "json",
				success: function(content){
					$.each(content, function(key, value) {
						$('#tables')
						.append($("<option></option>")
						.attr("value",value["TABLE_NAME"])
						.text(value["TABLE_NAME"])); 
					});
					callTableContent();
				}
			});
		})
		
		$("#tables").change(function(){
			callTableContent();
		})

		$("#add").click(function(){
			if( $('#newContent').is(':empty') ) {
				contenu="";
				$.ajax({
					type: "POST",
					data: {action: "addNewContent", table: $("#tables").val()},
					url: "requete.php",
					dataType: "json",
					success: function(content){
						$.each(content, function(key, value){
							if(!$.isArray(value))
							{
								contenu+="<input placeholder='"+key+"' type='";
								if(value.match("int"))
								{
									contenu+="number' ";
								}
								else if(value.match("varchar"))
								{
									contenu+="text' ";
								}
								contenu+="maxlength='"+value.substring(value.indexOf('(')+1,value.indexOf(')'))+"'>"
							}
							else{
								//Faire la liste déroulante pour les array
							}
						});
						$("#newContent").append(contenu);
					}
				});
			}
		})

		function callTableContent()
		{
			$("#contenu").empty();
			$("#newContent").empty();
			contenu="";
			$.ajax({
				type: "POST",
				data: {action: "getTableContent", table: $("#tables").val()},
				url: "requete.php",
				dataType: "json",
				success: function(content){
					contenu+="<tr class='title'>";
					$.each(content[0], function(key, value) {
						contenu+="<th>"+key+"</th>";
					});
					contenu+="</tr>";
					$.each(content, function(key, value) {
						contenu+="<tr>";
						$.each(value, function(cle, valeur) {
							contenu+="<td>"+valeur+"</td>";
						});
						contenu+="</tr>";
					});
					$("#contenu").append(contenu);
				}
			});
		}
	</script>
</html>