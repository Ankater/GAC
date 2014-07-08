<?PHP
	class cardCatalog extends CI_Model{
		
		function __construct()
		{
			parent::__construct();
		}

		
		function insertCards($id,$left,$right,$faceImageCard,$backImageCard,$parentId)
		{
				
			$query = "INSERT INTO `card`(`id`,`left`, `right`, `faceImageCard`, `backImageCard`, `parentId`) VALUES (".$id.",".$left.",".$right.",'".$faceImageCard."','".$backImageCard."',".$parentId.")";
			$this->db->query($query);
		}
		
		function getFaceIamgeCard($parentId)
		{
			$right = 0;
			$left=-1;
			$faceImageArray= array();
			
			$query = "SELECT `faceImageCard`,`id`,`right` FROM `card` WHERE `parentId` like " . $parentId. " AND `left` like " . $left . "";
			$query_data = $this->db->query($query);
			$query_data = $query_data->result_array();
			if(!empty($query_data)){
				$stack = array($query_data[0]['faceImageCard'],$query_data[0]['id']);
				array_push($faceImageArray, $stack);
				$right = $query_data[0]['right'];
				if($right!=-1){
					while($right!=-1){
						$query = "SELECT `faceImageCard`,`id`,`right` FROM `card` WHERE `id` like " . $right . "";
						$query_data = $this->db->query($query);
						$query_data = $query_data->result_array();
						$stack = array($query_data[0]['faceImageCard'],$query_data[0]['id']);
						array_push($faceImageArray, $stack);
						$right = $query_data[0]['right'];
					}
				}
			}
			//print_r($faceImageArray);
			return $faceImageArray;
		}

		function getIamgeCard($id){
			$imageArray= array();
			$query = "SELECT `faceImageCard`,`backImageCard` FROM `card` WHERE `id` like " . $id . "";
			$query_data = $this->db->query($query);
			return $query_data->result_array();
		}

		function changeCatalog($id,$parentId){
			$query = "SELECT `id` FROM `card` WHERE `left` = -1 AND `parentId` = " . $parentId;
			$query_data = $this->db->query($query);
			$rightId = $query_data->result_array();
			if(empty($rightId)==False){
				$rightId = $rightId[0]['id'];
			}else{
				$rightId = -1;
			}
			
			if($rightId!=-1){
				$query = "UPDATE `card` SET `left`='" . $id . "' WHERE `id` = " . $rightId;
				$query_data = $this->db->query($query);
			}
			
			$query = "UPDATE `card` SET `left`= -1, `right`=".$rightId.", `parentId`=" . $parentId . " WHERE `id` = " . $id;
			$query_data = $this->db->query($query);
		}

		function updateCatalogAfterDraggingCards($left,$right){
			print($left);
			print($right);

			echo ($left=="-1")&&($right=="-1");
			echo ($left=="-1");
			echo ($right=="-1");
			echo "!!!!!!!!!!!!!!!!!";
			if((($left=="-1")&&($right=="-1"))==False){
				if($left=="-1"){
					echo "один";
					$query = "UPDATE `card` SET `left`='" . $left . "' WHERE `id` = " . $right;
					$this->db->query($query);
				}elseif ($right=="-1") {
					echo "два";
					$query = "UPDATE `card` SET `right`='" . $right . "' WHERE `id` = " . $left;
					$this->db->query($query);
				}else{
					echo "три";
					$query = "UPDATE `card` SET `right`='" . $right . "' WHERE `id` = " . $left;
					$this->db->query($query);
					$query = "UPDATE `card` SET `left`='" . $left . "' WHERE `id` = " . $right;
					$this->db->query($query);
				}
			}
		}

		function updateCard($id, $leftId, $rightId)
		{
			$query = "SELECT `left`,`right` FROM `card` WHERE `id` = " . $id;
			$query_data = $this->db->query($query);
			$side = $query_data->result_array();
			print_r($side);

			if($side[0]['left']=="-1"){
				$query = "UPDATE `card` SET `left`= -1 WHERE `id` = " . $side[0]['right'];
				$query_data = $this->db->query($query);
			}elseif($side[0]['right']=="-1"){
				$query = "UPDATE `card` SET `right`= -1 WHERE `id` = " . $side[0]['left'];
				$query_data = $this->db->query($query);
			}else{
				#echo "UPDATE `card` SET `left`= ".$side[0]['left']." WHERE `left` = " . $idCard;
				$query = "UPDATE `card` SET `left`= ".$side[0]['left']." WHERE `left` = " . $id;
				$query_data = $this->db->query($query);
				#echo "UPDATE `card` SET `right`= ".$side[0]['right']." WHERE `right` = " . $idCard;
				$query = "UPDATE `card` SET `right`= ".$side[0]['right']." WHERE `right` = " . $id;
				$query_data = $this->db->query($query);
			}
			
			$query = "UPDATE `card` SET `right`=" . $id .  " WHERE `id` = " . $leftId;
			$query_data = $this->db->query($query);

			$query = "UPDATE `card` SET `right`=" . $rightId .  ", `left`=" . $leftId .  " WHERE `id` = " . $id;
			$query_data = $this->db->query($query);

			$query = "UPDATE `card` SET `left`=" . $id .  " WHERE `id` = " . $rightId;
			$query_data = $this->db->query($query);
		}

		function moveCardToNewParent($idCard,$idParent){
			$query = "SELECT `left`,`right`,`parentId` FROM `card` WHERE `id` = " . $idCard;
			$query_data = $this->db->query($query);
			$side = $query_data->result_array();
			if($side[0]['parentId']==$idParent){
				echo "sameParent";
			}else{
				if((($side[0]['left']=='-1')&&($side[0]['right']=='-1'))==False){
					if($side[0]['left']=='-1'){
						#echo "UPDATE `card` SET `left`= -1 WHERE `left` = " . $idCard;
						$query = "UPDATE `card` SET `left`= -1 WHERE `left` = " . $idCard;
						$query_data = $this->db->query($query);
					}else if($side[0]['right']=='-1'){
						#echo "UPDATE `card` SET `right`= -1 WHERE `right` = " . $idCard;
						$query = "UPDATE `card` SET `right`= -1 WHERE `right` = " . $idCard;
						$query_data = $this->db->query($query);
					}else{
						#echo "UPDATE `card` SET `left`= ".$side[0]['left']." WHERE `left` = " . $idCard;
						$query = "UPDATE `card` SET `left`= ".$side[0]['left']." WHERE `left` = " . $idCard;
						$query_data = $this->db->query($query);
						#echo "UPDATE `card` SET `right`= ".$side[0]['right']." WHERE `right` = " . $idCard;
						$query = "UPDATE `card` SET `right`= ".$side[0]['right']." WHERE `right` = " . $idCard;
						$query_data = $this->db->query($query);
					}
				}
				#echo "SELECT `id` FROM `card` WHERE `parentId` = " . $idParent . " AND `left` = -1";
				$query = "SELECT `id` FROM `card` WHERE `parentId` = " . $idParent . " AND `left` = -1";
				$query_data = $this->db->query($query);
				$right = $query_data->result_array();
				if(count($right)!=0){
					$right = $right[0]['id'];
					#echo "UPDATE `card` SET `left`= " . $idCard . " WHERE `parentId` = " . $idParent . " AND `left` = -1";
					$query = "UPDATE `card` SET `left`= " . $idCard . " WHERE `parentId` = " . $idParent . " AND `left` = -1";
					$query_data = $this->db->query($query);
					#echo "UPDATE `card` SET `left`= -1 , `right`= ".$right.", `parentId`=" . $idParent . "  WHERE `id` = " . $idCard;
					$query = "UPDATE `card` SET `left`= -1 , `right`= ".$right.", `parentId`=" . $idParent . "  WHERE `id` = " . $idCard;
					$query_data = $this->db->query($query);
				}else{
					$query = "UPDATE `card` SET `left`= -1 , `right`= -1, `parentId`=" . $idParent . "  WHERE `id` = " . $idCard;
					$query_data = $this->db->query($query);
				}
			}
		}
	}
?>