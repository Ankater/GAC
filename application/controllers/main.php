<?PHP
	class Main extends CI_Controller {

		public function __construct()
	    {
	        parent::__construct();
	        $this->load->helper(array('form', 'url'));
			$this->load->library('form_validation');
			$this->load->model('catalog');

			$this->load->model('cardCatalog');
	    }

		function index()
		{
			$data = $this->catalog->get_tree(0);
			$data['id'] = $data[0][0];
			$data['name'] = $data[0][1];
			$this->load->view('main',$data);
		}

		function insertCards()
		{
			$maxI = 200;
			$i=1;
			$parentId = 6;
			while($i<=$maxI){
				if($i>=100){
					$num='00000';
				}else if(($i<100)&&(9<$i)){
					$num='000000';
				}else if(9>=$i){
					$num='0000000';
				}

				$faceImageCard = 'Каталог_на_Болгарском_языке/Андрей-Апостолов/' . $num . $i . '.JPG';
				$i++;
				$backImageCard = 'Каталог_на_Болгарском_языке/Андрей-Апостолов/' . $num . $i . '.JPG';
				$id=($i/2)+586;
				switch ($id) {
					case 587:
						$left= -1;
						$right= $id + 1;
						break;
					case 686:
						$left= $id - 1;
						$right= -1;
						break;
					default:
						$left= $id - 1;
						$right= $id + 1;
						break;
				}

				$this->cardCatalog->insertCards($id,$left,$right,$faceImageCard,$backImageCard,$parentId);
				$i++; 
			}
		}

		function treeNode()
		{
			if (isset($_GET['type'])){
				$type = $this->input->get('type', TRUE);
			}else{
				$type="";
			}
			
			$id = $this->input->get('id', TRUE);
			$parent = $type = $this->input->get('parent', TRUE);
			if($parent==""){
				$parent="#";
			}else{
				$parent=$id;
			}

			if ($id=='#'){
				$id=-1;
			}
			$treeArray = $this->catalog->get_tree($id);
			#print_r($treeArray);
			$page_data=array();
			for($i=0;$i<count($treeArray);$i++){
				$children = $this->catalog->issetChaild($treeArray[$i][0]);
				$children = !empty($children);
				array_push($page_data,array("id"=>$treeArray[$i][0],"parent"=>$parent,"type" => $treeArray[$i][2],"text"=>$treeArray[$i][1],"state"=>array("opened"=>false,"disabled"=>false,"selected"=>false),"children"=>$children));
			}
			echo json_encode($page_data);
		}

		function updateTree()
		{
			$currentNode = $this->input->post("currentNode",TRUE);
			$parentNode = $this->input->post("parentNode",TRUE);
			$left = $this->input->post("left",TRUE);
			$right = $this->input->post("right",TRUE);

			$this->catalog->changeOldLeftAndRightNode($currentNode);
			$this->catalog->changePlacedPosition($left,$right,$currentNode,$parentNode);
			#$this->catalog->mainNode($right, $left, $parentNode, $currentNode);
		}

		function create()
		{
			$type = $this->input->post("type", TRUE);
			$name = $this->input->post("name", TRUE);
			$parentId = $this->input->post("parentId", TRUE);

			if($type=="card"){
				$bd = "card";
			}else{
				$bd = "catalog";
			}

			$this->catalog->creeateNode($parentId, $type, $bd, $name);

		}

		function rename()
		{
			$id = $this->input->post("id",TRUE);
			$type = $this->input->post("type",TRUE);
			$name = $this->input->post("name",TRUE);

			if ($type == "card"){
				$id = substr($id,5);
				$this->catalog->renameCard($id,$name);
			}else{
				$this->catalog->renameCatalog($id,$name);
			}
		}

		function delete()
		{
			$id = $this->input->post("id",TRUE);
			$type = $this->input->post("type",TRUE);
			if ($type == "card"){
				$id = substr($id,5);
				$this->catalog->deleteCard($id);
			}else{
				$this->catalog->deleteCatalog($id);
			}
		}

		function loadCardFaceimage()
		{	
			$id = $this->input->post("id",TRUE);
			$faceImagesArray = $this->cardCatalog->getFaceIamgeCard($id);
			for($i=0;$i<count($faceImagesArray);$i++){
				$faceImagesArray[$i] = $faceImagesArray[$i][0].'!!!'.$faceImagesArray[$i][1];
			}
			echo(implode ('!!!!!!!', $faceImagesArray));
		}

		function loadCardImages()
		{
			$id = $this->input->post("id",TRUE);
			$faceImagesArray = $this->cardCatalog->getIamgeCard($id);
			//print_r($faceImagesArray[0]);
			echo $faceImagesArray[0]['faceImageCard'] . '!?!?!?!?!?!' . $faceImagesArray[0]['backImageCard']; 
		}

		function updateCardPosition()
		{
			$id = $this->input->post("id",TRUE);
			$leftId = $this->input->post("leftId",TRUE);
			$rigthId = $this->input->post("rightId",TRUE);
			$this->cardCatalog->updateCard($id,$leftId,$rigthId);
		}

		function updateSomeCardPosition()
		{
			$parent = $this->input->post("parent",TRUE);
			$draggedCards = $this->input->post("draggedCards",TRUE);
			$draggedCards = explode('!', $draggedCards);
			print_r($draggedCards);

			for($i=0;$i<count($draggedCards);$i++){
				$this->cardCatalog->changeCatalog($draggedCards[$i],$parent);
			}

			$positions = $this->input->post("positions",TRUE);
			$positions = explode('!', $positions);
			for($i=0;$i<count($positions);$i++){
				$positions[$i] = explode('?', $positions[$i]);
				$this->cardCatalog->updateCatalogAfterDraggingCards($positions[$i][0],$positions[$i][1]);
			}
			print_r($positions);
		}

		function moveCard()
		{
			$idCard = $this->input->post("idCard",TRUE);
			$idParent = $this->input->post("idParent",TRUE);
			$this->cardCatalog->moveCardToNewParent($idCard,$idParent);
		}
		
	}
?>