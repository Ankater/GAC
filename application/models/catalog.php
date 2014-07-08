<?PHP
	class catalog extends CI_Model{
		
		function __construct()
		{
			parent::__construct();
		}

		function get_tree($parentId)
		{
			/*
			$query = "SELECT id,name,type FROM `catalog` WHERE `parentId` like " . $parentId . " ORDER by `left`";
			$query_data = $this->db->query($query);

			return $query_data->result_array();
			*/
			$left=-1;
			$catalogArray= array();
			
			$query = "SELECT `id`, `name`, `type`, `right` FROM `catalog` WHERE `parentId` like " . $parentId . " AND `left` like " . $left . "";

			$query_data = $this->db->query($query);
			$query_data = $query_data->result_array();
			if(!empty($query_data)){
				$stack = array($query_data[0]['id'],$query_data[0]['name'],$query_data[0]['type']);
				array_push($catalogArray, $stack);
				$right = $query_data[0]['right'];
				if($right!=-1){
					while($right!=-1){
						$query = "SELECT `id`,`name`,`type`,`right` FROM `catalog` WHERE `id` = " . $right;
						//$query = "SELECT `faceImageCard`,`id`,`right` FROM `card` WHERE `id` like " . $right . "";
						$query_data = $this->db->query($query);
						$query_data = $query_data->result_array();
						$stack = array($query_data[0]['id'],$query_data[0]['name'],$query_data[0]['type']);
						array_push($catalogArray, $stack);
						$right = $query_data[0]['right'];
					}
				}
			}
			#print_r($catalogArray);
			return $catalogArray;
		}

		function issetChaild($parentId)
		{
			$query = "SELECT * FROM `catalog` WHERE `parentId` like " . $parentId . "";
			$query_data = $this->db->query($query);
			return $query_data->result_array();	
		}
		function changeOldLeftAndRightNode($currentNode){
			

			$query = "SELECT `left`,`right` FROM `catalog` WHERE `id` = " . $currentNode;
			$query_data = $this->db->query($query);
			$side = $query_data->result_array();
			print_r($side);

			if($side[0]['left']=="-1"){
				echo "left";
				$query = "UPDATE `catalog` SET `left`= -1 WHERE `id` = " . $side[0]['right'];
				$query_data = $this->db->query($query);
			}elseif($side[0]['right']=="-1"){
				echo "right";
				$query = "UPDATE `catalog` SET `right`= -1 WHERE `id` = " . $side[0]['left'];
				$query_data = $this->db->query($query);
			}else{
				echo "nothing";
				#echo "UPDATE `card` SET `left`= ".$side[0]['left']." WHERE `left` = " . $idCard;
				$query = "UPDATE `catalog` SET `left`= ".$side[0]['left']." WHERE `left` = " . $currentNode;
				$query_data = $this->db->query($query);
				#echo "UPDATE `card` SET `right`= ".$side[0]['right']." WHERE `right` = " . $idCard;
				$query = "UPDATE `catalog` SET `right`= ".$side[0]['right']." WHERE `right` = " . $currentNode;
				$query_data = $this->db->query($query);
			}

		}

		function changePlacedPosition($leftNode, $rightNode, $mainNode, $parentNode){
			//$query = "UPDATE `catalog` SET `left`=" . $mainNode . " WHERE `id` like " . $rightNode;
			
			$query = "UPDATE `catalog` SET `right`=" . $mainNode .  " WHERE `id` = " . $leftNode;
			$query_data = $this->db->query($query);

			$query = "UPDATE `catalog` SET `right`=" . $rightNode .  ", `left`=" . $leftNode .  ", `parentId` = " . $parentNode . " WHERE `id` = " . $mainNode;
			$query_data = $this->db->query($query);

			$query = "UPDATE `catalog` SET `left`=" . $mainNode .  " WHERE `id` = " . $rightNode;
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

		/*
		function renameCard($id, $name){
			$query = "UPDATE `card` SET `name`='" . $name . "' WHERE `id` LIKE " . $id;
			$query_data = $this->db->query($query);
		}
		*/

		function renameCatalog($id, $name){
			$query = "UPDATE `catalog` SET `name`='" . $name . "' WHERE `id` LIKE " . $id;
			$query_data = $this->db->query($query);
		}

		/*
		function deleteCard($id){
			
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
		*/

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