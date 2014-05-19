<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link type="text/css" href="/css/bootstrap.min.css" rel="stylesheet"/>
	<link type="text/css" href="/css/main.css" rel="stylesheet"/>
	<link rel="stylesheet" href="/dist/themes/default/style.min.css" />
</head>

<body>
	<nav class="navbar navbar-default" role="navigation">
	  <div class="container-fluid">
	    <!-- Brand and toggle get grouped for better mobile display -->
	    <div class="navbar-header">
	      
	      <a class="navbar-brand" href="#">Генеральный алфавитный каталог
Российской Государственной библиотеки </a>
	    </div>

	    <!-- Collect the nav links, forms, and other content for toggling -->
	    </div>
	</nav>

	<div class="row">
		<div class="col-md-4 sidebar">
			<div class="alert alert-info">.col-md-4</div>
			<div class="row">
				<div class="col-md-12">
					<button id="create" class="btn btn-success btn-sm" onclick="demo_create();" type="button">
						<i class="glyphicon glyphicon-asterisk"></i>
						Create
					</button>
					<button id="rename" class="btn btn-warning btn-sm" onclick="demo_rename();" type="button">
						<i class="glyphicon glyphicon-pencil"></i>
						Rename
					</button>
					<button id="delete" class="btn btn-danger btn-sm" onclick="demo_delete();" type="button">
						<i class="glyphicon glyphicon-remove"></i>
						Delete
					</button>
				</div>
			</div>
			<div id="separators">
				<ul>
			    	<li id="<?=$id?>">
			    		<?=$name?>
			    		<ul>
			    			<li></li>
			    		</ul>
			    	</li>
			    </ul>
			</div>
		</div>
	 	<div class="col-md-8"><div class="alert alert-info">.col-md-4</div></div>
	</div>
	<script src="js/jquery-1.11.0.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="dist/jstree.min.js"></script>
    <script src="js/main.js"></script>

    <div class="modal fade" id="createDialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
	    	<div class="modal-content">
	    		<div class="modal-header">
	        		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	        		<h4 class="modal-title" id="myModalLabel">Создание нового элемента</h4>
	      		</div>
	      		<div class="modal-body">
	      			Родительский элемент: <b><span id="parentElementCreate"></span></b><br>
	        		Тип: 
	        		<select id="typeCreate">
	        			<option>Карточка</option>
	        			<option>Каталог</option>
	        			<option>Разделитель</option>
	        			<option>Ящик</option>
	        		</select><br>
	        		Название: <input id="createName" pattern='^[A-Za-zА-Яа-яЁё0-9\s]+$'>
	      		</div>
	      		<div class="modal-footer">
	        		<button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
	        		<button id="createDialogSave" onclick="funcCreateDialogSave();" type="button" class="btn btn-primary">Добавить элемент</button>
	     		</div>
	    	</div>
		</div>
	</div>

</body>
</html>