<?php
	if (isset($_POST['tableName'])){
		$tableName = $_POST['tableName'];
		$headers = json_decode($_POST['headers']);
		$data = json_decode($_POST['data']);
		echo json_encode(array('Tablename'=>$tableName,'Headers'=>$headers,'Data'=>$data));
		$filename = "XC/jsons/" . str_replace(" ", "_", $tableName) . ".json";
		
		$file_contents = "var " . str_replace(" ", "_", $tableName) . " = ";
		$file_contents .= "[\n\t";
		for ($j=0;$j<count($data);$j++) {
			$file_contents .= "{";
			for ($i=0;$i<count($data[$j]);$i++) {
				$file_contents .= '"' . $headers[$i]. '":"' .$data[$j][$i].'"';
				if ($i+1<count($data[$j])){
					$file_contents .= ",";
				}
			}
			if ($j+1<count($data)){
				$file_contents .= "},\n\t";
			}else {
				$file_contents .= "}\n";
			}
		}
		$file_contents .= "\n];";
		
		file_put_contents($filename, $file_contents);
	}else {
		echo "'No POST data.'";
	}
?>