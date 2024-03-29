<html>
<body>
	<?php
		if(isset($_POST['specificLetter'])){
			$specificLetter = $_POST['specificLetter'];
		
	
			echo "<form action='insertIntoDatabase.php' method='POST'>";
	
			
			$connection = buildDatabaseConnection();
			$allUnfinishedWordsWithUrls = getAllUnfinishedWords($connection, $specificLetter);

			$innovationsFound = getAllInnovationsFound($connection);
			
			echo "Grün-markierte Wörter sind bereits gefundene Innvoavtionen.<br><br>";
			
			//create table
			echo "<table border=1>
					<tr>
					<th></th>
					<th>Wort</th>
					<th>Url</th>
					</tr>";
					
				foreach($allUnfinishedWordsWithUrls as $key=>$unfinishedWordWithUrl){
					
					$rowColorAndChecked = getRowColor($innovationsFound, $unfinishedWordWithUrl[0]);
					
					//Tabellentupel pro MainURL
					echo "<tr bgcolor='".$rowColorAndChecked[0]."'>";
					echo "<td> <input type='checkbox' id='chkbx' name='checkList[]' value=\"".$unfinishedWordWithUrl[0]."\"/".$rowColorAndChecked[1]."> </td>";
					echo "<td>" .$unfinishedWordWithUrl[0]. "</td>";
					echo "<td> <a href=\"".$unfinishedWordWithUrl[1]."\" </a>" .$unfinishedWordWithUrl[1]. "</td>";
					echo "</tr>";
					
					//post array with all words of _innovation_check
					echo "<input type='hidden' id='chkbxHidden' name='checkListHidden[]' value=\"".$unfinishedWordWithUrl[0]. "\"/>";
					
					
				}
			echo "</table>
					<br>
				<input type='submit' name='btSaveWordsInDB' value='Wörter in Datenbank speichern!'/>
			</form>
			<br>
			<form action='index.html' method='POST'>
			<input type='submit' id='btCancel' value='Abbrechen und Bearbeitung aufschieben'/>
			</form>";
			
		}else{
			
	?>	
	<form action="editInnovations.php" method="POST">
		Ausstehende Wörter nach folgendem Anfangsbuchstaben filtern:
			<select name="specificLetter">
				<option value="">Alle anzeigen</option>
				
				<?php
					$alphabet = range('a','z');
					array_push($alphabet,'ä','ö','ü');
					
					foreach($alphabet as $letter){
						echo "<option value='".$letter."'>".$letter."</option>";
					}
				?>
			</select>
			<input type="submit" name="btFilter" value="Filtern!"/>
			
	</form>
	
	<?php
		}	
	?>
			
	
	<?php
		
		function getAllUnfinishedWords($connection, $specificLetter){
			
			$allUnfinishedWords = array();
			$allUnfinishedWordsWithUrls = array();
			
			$result = mysqli_query($connection,"SELECT word, url FROM _innovation_check where word like '".$specificLetter."%'");

			
			while($row = mysqli_fetch_array($result))
			{
				for($i = 0; $i <= 1; $i++){
					$allUnfinishedWords[$i] = $row[$i];
				}
				
				array_push($allUnfinishedWordsWithUrls,$allUnfinishedWords);
				
			}
			
			return $allUnfinishedWordsWithUrls;
		}
		
		function buildDatabaseConnection(){
			$hostname = "localhost"; $user = "root"; $password = ""; $db = "innovation";
			$connection = mysqli_connect($hostname, $user, $password, $db);
			mysqli_set_charset($connection,"utf8");
				
			return $connection;
		}
		
		function getRowColor($innovationsFound, $word){
				$colorAndChecked = array();
			if(in_array($word,$innovationsFound)){
				$colorAndChecked[0] = "##4EEE94";
				$colorAndChecked[1] = "checked";
			}else{
				$colorAndChecked[0] = "#FFFFFF";
				$colorAndChecked[1] = "";
			}
		
			return $colorAndChecked;
		}
		
		function getAllInnovationsFound($connection){
			
			$innovationsFound = array();
			$result = mysqli_query($connection,"SELECT word FROM _innovation_found");
			while($row = mysqli_fetch_array($result))
			{
				$innovationsFound[] = $row[0];
			}
			
			return $innovationsFound;
		}
	?>
</body>
</html>