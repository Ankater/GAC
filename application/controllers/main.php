<?PHP header("Content-Type: text/html; charset=utf-8");?>
<?PHP
	class Main extends CI_Controller {

		public function __construct()
	    {
	        parent::__construct();
	        $this->load->helper(array('form', 'url'));
			$this->load->library('form_validation');
			$this->load->model('catalog');
	    }

		function index()
		{
			$main_catalog = $this->catalog->get_tree(0);
			$data = $main_catalog[0];
			$this->load->view('main',$data);
		}

		function treeNode()
		{
			if (isset($_GET['type'])){
				$type = $this->input->get('type', TRUE);	
			}else{
				$type="";
			}

			$id= $this->input->get('id', TRUE);
			if ($id=='#'){
				$id=-1;
			}

			if($type == "box"){
				$treeArray = $this->catalog->get_card($id);
				for($i=0;$i<count($treeArray);$i++){
					echo "<ul>
					<li  id ='card_" . $treeArray[$i]['id'] . "'  role = 'card' class='jstree'>" . $treeArray[$i]['name'] . "</li>
					</ul>";
				}
			}else{
				$treeArray = $this->catalog->get_tree($id);
				for($i=0;$i<count($treeArray);$i++){
					$data =  `"{'disabled':true}"`;
					echo "<ul>
						<li id ='" . $treeArray[$i]['id'] . "' data-jstree=" . $data . " role = '" . $treeArray[$i]['type'] . "' class='jstree-closed'>" . $treeArray[$i]['name'] . "</li>
						</ul>";
				}
			}
		}

		function updateTree()
		{
			$currentNode = $this->input->post("currentNode",TRUE);
			$parentNode = $this->input->post("parentNode",TRUE);
			$left = $this->input->post("left",TRUE);
			$right = $this->input->post("right",TRUE);
			
			$this->catalog->leftNode($left,$currentNode);
			$this->catalog->rightNode($right,$currentNode);
			$this->catalog->mainNode($right, $left, $parentNode, $currentNode);

			echo "ok";
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
		
	}
?>