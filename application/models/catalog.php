<?PHP
	class catalog extends CI_Model{
		
		function __construct()
		{
			parent::__construct();
		}

		function get_tree($parentId)
		{
			$query = "SELECT id,name,type FROM `catalog` WHERE `parentId` like " . $parentId . " ORDER by `left`";
			$query_data = $this->db->query($query);
			
			return $query_data->result_array();
		}

		function get_card($parentId)
		{
			$query = "SELECT id,name FROM `card` WHERE `parentId` like " . $parentId . " ORDER by `left`";
			$query_data = $this->db->query($query);
			
			return $query_data->result_array();
		}

		function leftNode($leftNode, $mainNode){
			$query = "UPDATE `catalog` SET `right`=" . $mainNode .  " WHERE `id` like " . $leftNode;
			$query_data = $this->db->query($query);
		}

		function rightNode($rightNode, $mainNode){
			$query = "UPDATE `catalog` SET `left`=" . $mainNode . " WHERE `id` like " . $rightNode;
			$query_data = $this->db->query($query);
		}

		function creeateNode($parentId, $type, $bd, $name){
			$query = "SELECT id FROM `" . $bd . "` WHERE `parentId` = " . $parentId . " AND `left` = -1";
			$query = $this->db->query($query);
			$rightId = $query->result_array();
			if(empty($rightId)){
				if($bd == "catalog"){
					$query = "INSERT INTO `catalog`(`parentId`, `type`, `name`, `left`, `right`) VALUES (" . $parentId . ", '" . $type . "', '" . $name . "', -1,-1)";
					$query = $this->db->query($query);
				}else{
					$query = "INSERT INTO `card`(`parentId`, `name`, `left`, `right`) VALUES (" . $parentId . ", '" . $name . "', -1, -1)";
					$query = $this->db->query($query);
				}				
			}else{
				$rightId = $rightId[0]['id'];

				if($bd == "catalog"){
					$query = "INSERT INTO `catalog`(`parentId`, `type`, `name`, `left`, `right`) VALUES (" . $parentId . ", '" . $type . "', '" . $name . "', -1, " . $rightId . ")";
					$query = $this->db->query($query);
				}else{
					$query = "INSERT INTO `card`(`parentId`, `name`, `left`, `right`) VALUES (" . $parentId . ", '" . $name . "', -1, " . $rightId . ")";
					$query = $this->db->query($query);
				}

				$insertId = "SELECT id FROM `" . $bd . "` WHERE `parentId` = " . $parentId . " AND `left` = -1";
				$insertId = $this->db->query($insertId);
				$insertId = $insertId->result_array();
				$insertId = $insertId[0]['id'];

				$query = "UPDATE `" . $bd . "` SET `left`=" . $insertId . " WHERE `id` like " . $rightId;
				$query_data = $this->db->query($query);
			}


			//"INSERT INTO `catalog`(`parentId`, `type`, `name`, `left`, `right`) VALUES (" . $parentId . ", `" . $type . "`, `" . $name . "`, -1, " . $rightId . ")"
		}

		function mainNode($rightNode, $leftNode, $parentNode, $mainNode){
			$query = "UPDATE `catalog` SET `left`=" . $leftNode . ", `right`= " . $rightNode . ", `parentId` = " . $parentNode . " WHERE  `id` like " . $mainNode;
			$query_data = $this->db->query($query);
		}

		function renameCard($id, $name){
			$query = "UPDATE `card` SET `name`='" . $name . "' WHERE `id` LIKE " . $id;
			$query_data = $this->db->query($query);
		}

		function renameCatalog($id, $name){
			$query = "UPDATE `catalog` SET `name`='" . $name . "' WHERE `id` LIKE " . $id;
			$query_data = $this->db->query($query);
		}

		function deleteCard($id){
			/*
			1)Соседние элементы
			2)удаление
			*/
			$leftRightId = "SELECT `left`, `right` FROM `card` WHERE `id` LIKE '" . $id . "'";
			$leftRightId = $this->db->query($leftRightId);
			$leftRightId = $leftRightId->result_array();
			$left = "UPDATE `card` SET `right`=" . $leftRightId[0]['right'] . " WHERE `id` LIKE '" . $leftRightId[0]['left'] . "'";
			$left = $this->db->query($left);

			$right = "UPDATE `card` SET `left`=" . $leftRightId[0]['left'] . " WHERE `id` LIKE '" . $leftRightId[0]['right'] . "'";
			$right = $this->db->query($right); 

			$delete = "DELETE FROM `card` WHERE `id` LIKE '" . $id . "'"; 
			$delete = $this->db->query($delete);
		}

		function deleteCatalog($id){
			$leftRightId = "SELECT `left`, `right` FROM `catalog` WHERE `id` LIKE '" . $id . "'";
			$leftRightId = $this->db->query($leftRightId);
			$leftRightId = $leftRightId->result_array();

			$left = "UPDATE `catalog` SET `right`=" . $leftRightId[0]['right'] . " WHERE `id` LIKE '" . $leftRightId[0]['left'] . "'";
			$left = $this->db->query($left);
			$right = "UPDATE `catalog` SET `left`=" . $leftRightId[0]['left'] . " WHERE `id` LIKE '" . $leftRightId[0]['right'] . "'";
			$right = $this->db->query($right); 

			$delete = "DELETE FROM `catalog` WHERE `id` LIKE '" . $id . "'"; 
			$delete = $this->db->query($delete);

			$children = "SELECT `id` FROM `catalog` WHERE `parentId` LIKE '" . $id . "'";
			$children = $this->db->query($children);
			$children = $children->result_array();
			
			for($i=0;$i<count($children);$i++){
				$delete = "DELETE FROM `catalog` WHERE `id` LIKE '" . $children[$i]['id'] . "'"; 
				$delete = $this->db->query($delete);
			}

			$middleArray = $children;

			while(!empty($middleArray)){
				$newMiddleArray = array();
				for($i=0;$i<count($middleArray);$i++){
					$children = $this->db->query("SELECT `id` FROM `catalog` WHERE `parentId` LIKE '" . $middleArray[$i]['id'] . "'");
					$children = $children->result_array();
					if(!empty($children)){
						for ($j=0; $j < count($children); $j++) {
							if($children[$j]['type']=="box"){
								$deleteCard = "DELETE FROM `card` WHERE `parentId` LIKE '" . $children[$j]['id'] . "'"; 
								$deleteCard = $this->db->query($deleteCard);
							}
							$deleteCatalog = "DELETE FROM `card` WHERE `id` LIKE '" . $children[$j]['id'] . "'";
							$deleteCatalog = $this->db->query($deleteCatalog);
							array_push($newMiddleArray, $children[$j]['id']);
						}
					}
				}
				$middleArray = $newMiddleArray;
			}
			
		}
	}
?>