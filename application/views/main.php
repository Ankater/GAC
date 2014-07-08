<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link type="text/css" href="/css/bootstrap.min.css" rel="stylesheet"/>
	<link rel="stylesheet" href="/dist/themes/default/style.min.css"/>
	<link rel="stylesheet" href="/css/jquery-ui-1.10.4.custom.min.css"/>
	<link type="text/css" href="/css/main.css" rel="stylesheet"/>
</head>

<body>
	<nav id="header" class="navbar navbar-default" role="navigation">
	  <div class="container-fluid">
	    <!-- Brand and toggle get grouped for better mobile display -->
	    <div class="navbar-header">
	      
	      <a class="navbar-brand" href="#">Генеральный алфавитный каталог
Российской Государственной библиотеки </a>
	    </div>

	    <!-- Collect the nav links, forms, and other content for toggling -->
	    </div>
	</nav>

	<div class="row mainRow">
		<div class="col-md-3 leftsidebar">
			<div class="alert alert-info">.col-md-4</div>
			<ul class="nav nav-tabs" role="tablist" id="tablist">
			  <li class="active"><a href="#tree" role="tab" data-toggle="tab">Древо</a></li>
			  <li><a href="#search" role="tab" data-toggle="tab">Поиск</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="tree">
					<div>
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
				<div class="tab-pane" id="search">
					<div class="input-group">
					    <input type="text" id="searchInput" class="form-control" placeholder="Найти">
					    <span class="input-group-btn">
					    	<button class="btn btn-primary" type="button">Найти</button>
					    </span>
				    </div>
				</div>
			</div>
		</div>
	 	<div class="col-md-9 rigthsidebar">
			<div id="treePath" class="alert alert-info">Just Another Sidebar</div>
			<ul id="sortable" class="site-wrapper-inner"></ul>
		 	<div class="container">
				<p id="backToTheGallery" class="col-md-offset-4 col-md-6" style="display: none;"><a href="">Все карточки</a>&#187;<span id='side'>Лицевая сторона</span> карточки №<span id='numCard'>1</span></p>
		 	</div>
		 	<div class="container" id="flipboxContainer" style="display: none;">
		 		<div class="col-md-1 container prevCardContainer">
		 			<button type="button" style = "margin-left: 16px; margin-right=16px;" class="btn btn-default" id="prevCard">
		 				<span class="glyphicon glyphicon-chevron-left"></span>
		 			</button>
		 		</div>
		 		<div class="col-md-10" style="padding:0;">
			 		<div id='flipbox'></div>
		 		</div>
		 		<div class="col-md-1 container nextCardContainer">
		 			<button type="button" style = "margin-left: 16px; margin-right: 16px;"class="btn btn-default" id="nextCard">
		 				<span class="glyphicon glyphicon-chevron-right"></span>
		 			</button>
		 		</div>
		 	</div>
	 	</div>
	</div>
	<script	type='application/javascript' src="js/jquery-1.11.0.min.js"></script>
    <script	type='application/javascript' src="js/bootstrap.min.js"></script>
    <script	type='application/javascript' src="dist/jstree.min.js"></script>
    <script	type='application/javascript' src="js/jquery-ui-1.10.4.custom.min.js"></script>
    <script	type='application/javascript' src="js/jquery.flip.min.js"></script>
    <script	type='application/javascript' src="js/main.js"></script>

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