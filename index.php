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
		<table id="newContent"></table>
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
				$.ajax({
					type: "POST",
					data: {action: "addNewContent", table: $("#tables").val()},
					url: "requete.php",
					dataType: "json",
					success: function(content){
						contenu="<tr>";
						$.each(content, function(key, value){
							contenu+="<th>";
							if(!$.isArray(value))
							{
								contenu+=key;
							}
							else{
								//Faire la liste déroulante pour les array
								contenu+="<select id='"+key+"' class='fk_choix'>"
								objet=value[0];
								$.each(objet, function(cle, valeur){
									contenu+="<option value='"+cle+"'>"+cle+"</options>";
								});
								contenu+="</select>";
							}
							contenu+="</th>";
						});

						contenu+="</tr>";
						$("#newContent").append(contenu);

						contenu="<tr>";

						$.each(content, function(key, value){
							contenu+="<td id='fk_"+key+"'>";
							if(!$.isArray(value))
							{
								contenu+="<input placeholder='"+key+"' type='";
								if(value.match("int"))
								{
									contenu+="number'";
								}
								else if(value.match("varchar"))
								{
									contenu+="text'";
								}
								contenu+="maxlength='"+value.substring(value.indexOf('(')+1,value.indexOf(')'))+"'>"
							}
							else{
								contenu+=foreignKeyContent(value, key );
							}
							contenu+="</td>";
						});
						contenu+="</tr>";
						$("#newContent").append(contenu);
					}
				});
			}
		});

		$("#newContent").on("change",".fk_choix",function(){
			key=$(this).val();
			champ=$(this).attr("id");
			$.ajax({
				type: "POST",
				data: {action: "addNewContentChoix", table: $("#tables").val(), key: key, champ: champ},
				url: "requete.php",
				dataType: "json",
				success: function(content){
					contenu=foreignKeyContent(content, champ);

					$("#fk_"+champ).empty();
					$("#fk_"+champ).append(contenu);
				}
			});

		});

		function foreignKeyContent(value, key){
			contenu="<select>";
			$.each(value, function(k,objet){
				contenu+="<option value='"+objet[key]+"'>";
				$.each(objet, function(cle, valeur){
					if($("#"+key).val().match(cle))
					{
						contenu+=valeur;
					}
				});
			});
			contenu+="</select>"

			return contenu;
		}

		function callTableContent()
		{
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

					$("#contenu").empty();
					$("#newContent").empty();
					$("#contenu").append(contenu);
				}
			});
		}
	</script>
</html>