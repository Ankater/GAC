var oldParentId;
var imageArray;
var currentNumImg;
var statusDragingCard = true;
var statusDragInTree = false;
var currentDraggableElement;
var hoveredElement;//node element
var selectableOn = true;
var statusDragSomeCards;

$.jstree.defaults.dnd.copy = false;
$(document).ready(function(){
	$('#tablist a:first').tab('show');
$(window).resize( setHeiHeight );
chooseMouseUp(true);
$("#sortable").sortable({
	dropOnEmpty: false,
	cursorAt: { left:-10, top:-10 },
	handle:".cardImg",
	start: function(event, ui) {
		/*
		arraySelectedElements = [];
		console.log(ui['item'].attr("class"));
		$('[id].ui-selected').each(function(index,element){
			arraySelectedElements.push($(element).attr("id"));
		})
		//console.log(arraySelectedElements);
		
		ui['item'].children().children().hide();
		ui['item'].children().append("<p id='numSelectedCards'>"+arraySelectedElements.length+"</p>");
		*/
	},
	sort: function (event, ui){},
    stop: function (event, ui){
		ui['item'].children().children("#numSelectedCards").remove().show();
        ui['item'].children().children().show();
        statusDragInTree = false;
		statusDragingCard = false;
		currentElement = ui['item'];
		id = currentElement.attr('id');
		console.log('id= ' + id);

		left = currentElement.prev().attr("id");
		if(left==undefined){left='-1'};
					
		right = currentElement.next().attr("id");
		if(right==undefined){right='-1'};
		console.log(left);
		console.log(right);

		$.post("/main/updateCardPosition",{'id':id,'leftId':left,'rightId':right});
		setTimeout(function(){
			statusDragingCard = true;
		},1000)
    },

   	out: function(event,ui){
		statusDragInTree = true;
		currentDraggableElement = ui['item'];
		console.log(currentDraggableElement)
	},
	over:function(){
		statusDragInTree = false;
		currentDraggableElement = undefined;
	}
});

//$("#sortable").disableSelection();
$("#sortable").selectable({
	start: function(event,ui){
		//console.log(ui);
	},
	stop: function( event, ui ) {
		//console.log($("li.ui-selected"));
	},
	selected:function( event, ui ) {
		$("#sortable").sortable("disable");
		selectableOn = false;
	},
	unselected:function( event, ui ) {
		$("#sortable").sortable("enable");
		selectableOn = true;
	}
});



$('#separators')
		.jstree({
			"plugins" : ["dnd","state", "types", "wholerow"],
			'core' : {
				"check_callback" : true,
				'data' : {
					'url' : '/main/treeNode',
					'dataType': 'JSON',
				    'data' : function (node) {
				    	if(node.li_attr==undefined){
				        	return { 'id' : node.id, 'parent':node.parent};
				    	}else{
				    		return { 'id' : node.id, 'type': node.type, 'parent':node.parent};
				    	}
				    }
				}
			},
			"types" : {
				"#":{
					"valid_children" : ["main"]
				},
			    "main" : {
			      "valid_children" : ["catalog"]
			    },
			    "separator" : {
			    	"icon" : "/img/treeSeparator.png",
			      	"valid_children" : ["box","card"]
			    },
			    "catalog" : {
			    	"icon" : "/img/treeCatalog.jpg",
			      	"valid_children" : ["separator","box"]
			    },
			    "box" : {
			    	"icon" : "/img/treeBox.png",
			    	"valid_children" : ["card","separator"]
			    }
			}
		});

	$('#separators').on('select_node.jstree',function(e,data){
		if(statusDragInTree==false){
			$("#backToTheGallery").hide()
			$("#sortable").empty();
			$("#sortable").show();
			$("#flipboxContainer").hide();
			$("#flipbox").empty();
			var ref = $('#separators').jstree(true),
			sel = ref.get_selected();
			$("#treePath").text(ref.get_path(sel,"/"));
			type = ref.get_type(sel);
			if(type=="box"||type=="separator"){
				id = $("#"+sel).attr("id");
				$.ajax({
					type: "POST",
					url:"/main/loadCardFaceimage",
					data:'id='+id,
					beforeSend:function(){
						$("#sortable").append("<img src='img/loading.gif'>");
					},
					success: function(result){
						$("#sortable").empty();
						if(result!=""){
							var faceImageArray = result.split ('!!!!!!!');
							$('#sortable').removeAttr("style");
							for(i=0;i<faceImageArray.length;i++){
								faceImageArray[i] = faceImageArray[i].split('!!!')
								$("#sortable").append('<li id="'+faceImageArray[i][1]+'" class="imageCard"><div class="cardDiv"><a href="#" class="thumbnail cardMin"><img class="cardImg" data-src="holder.js/100%x180" src="'+faceImageArray[i][0]+'"></a></div></li>');
							}
							setHeiHeight();
						}
					}
				})
			}
		}
	});

	$('#separators').on("hover_node.jstree",function(e,data){
		if(statusDragInTree||(selectableOn==false)){
			hoveredElement = data['node'];
		}
	});

});
$("#backToTheGallery").children("a").click(function(){
	$("#backToTheGallery").hide();
	$("#sortable").show();
	$("#flipboxContainer").hide();
	return false;
})

$("body").on("click","img.cardImg",function(){

	if(statusDragingCard){
		console.log($(this).closest('li'));
		currentNumImg=$(this).closest('.imageCard').index();
		$('#numCard').text(currentNumImg+1);
		$.post("/main/loadCardImages",{'id':$(this).closest('.imageCard').attr('id')},function(result){
			imageArray = result.split('!?!?!?!?!?!');
			$("#flipbox").flip({
				direction:'tb',
				content:"<img style='width: "+(0.495*$(window).width())+"px;' src="+ imageArray[0] +">"
			})
			$("#backToTheGallery").show();
			$("#sortable").hide();
			$("#flipboxContainer").show();
		});

		$("#nextCard").removeAttr("disabled");
		$("#prevCard").removeAttr("disabled");
		console.log(currentNumImg);
		if((currentNumImg==0)&&($("#sortable").children("li").eq(currentNumImg).attr('id')==$("#sortable").children("li").eq(-1).attr('id'))){
			$("#prevCard").attr("disabled","disabled");
			$("#nextCard").attr("disabled","disabled");
		}else if(currentNumImg==0){
			$("#prevCard").attr("disabled","disabled");
		}else if($("#sortable").children("li").eq(currentNumImg).attr('id')==$("#sortable").children("li").eq(-1).attr('id')){
			$("#nextCard").attr("disabled","disabled");
		}
	}
	
})

$('#sortable').on('mousedown','img.cardImg.ui-selected',function(event){
	if(selectableOn==false){
		$("#sortable").selectable("disable");
		numChoosenElements=$("li.ui-selected").length;

		$(".rigthsidebar").append('<p id="numDraggedItems" style="">' + numChoosenElements + '</p>');
		chooseMouseUp(false);
	}
})

$("#flipbox").click(function(){
	if(imageArray!=undefined){
		if($("#flipbox").children("img").attr("src")==imageArray[0]){
			$("#side").text("Обратная сторона сторона");
			img = imageArray[1];
		}else{
			$("#side").text("Лицевая сторона");
			img = imageArray[0];
		}
		$("#flipbox").flip({
			direction:'lr',
			content:"<img style='width: "+(0.495*$(window).width())+"px;' src="+ img +">"
		})
	}
})

$("#prevCard").click(function(){
	$("#side").text("Лицевая сторона");
	currentNumImg = currentNumImg - 1;
	$("#numCard").text(currentNumImg+1);
	$.post("/main/loadCardImages",{'id':$("#sortable").children("li").eq(currentNumImg).attr("id")},function(result){
		imageArray = result.split('!?!?!?!?!?!');
		$("#flipbox").flip({
			direction:'rl',
			content:"<img style='width: "+(0.495*$(window).width())+"px;' src=" + imageArray[0] + ">"
		})
	});
	
	$("#prevCard").removeAttr("disabled");
	$("#nextCard").removeAttr("disabled");
	if((currentNumImg==0)&&($("#sortable").children("li").eq(currentNumImg).attr('id')==$("#sortable").children("li").eq(-1).attr('id'))){
		$("#prevCard").attr("disabled","disabled");
		$("#nextCard").attr("disabled","disabled");
	}else if(currentNumImg==0){
		$("#prevCard").attr("disabled","disabled");
	}else if($("#sortable").children("li").eq(currentNumImg).attr('id') == $("#sortable").children("li").eq(-1).attr('id')){
		$("#nextCard").attr("disabled","disabled");
	}
})

$("#nextCard").click(function(){
	$("#side").text("Лицевая сторона");
	currentNumImg = currentNumImg + 1;
	$("#numCard").text(currentNumImg+1);
	$.post("/main/loadCardImages",{'id':$("#sortable").children("li").eq(currentNumImg).attr("id")},function(result){
		imageArray = result.split('!?!?!?!?!?!');
		$("#flipbox").flip({
			direction:'lr',
			content:"<img style='width: "+(0.495*$(window).width())+"px;' src=" + imageArray[0] + ">"
		})
	});
	
	$("#prevCard").removeAttr("disabled");
	$("#nextCard").removeAttr("disabled");
	if((currentNumImg==0)&&($("#sortable").children("li").eq(currentNumImg).attr('id')==$("#sortable").children("li").eq(-1).attr('id'))){
		$("#prevCard").attr("disabled","disabled");
		$("#nextCard").attr("disabled","disabled");
	}else if(currentNumImg==0){
		$("#prevCard").attr("disabled","disabled");
	}else if($("#sortable").children("li").eq(currentNumImg).attr('id') == $("#sortable").children("li").eq(-1).attr('id')){
		$("#nextCard").attr("disabled","disabled");
	}
})

$(document).on('dnd_start.vakata',function(e,data){
	target = $(data.event.target).closest("li[id]");
	oldParentId = $('#separators').jstree(true).get_parent(target);
})

$(document).on('dnd_stop.vakata', function (e, data) {
	target = $(data.event.target).closest("li[id]");


	$('#separators').jstree(true).open_node(target);
	elementId = $(data.element).closest("li[id]").attr("id");
	elementType = $(data.element).closest("li[id]").attr("type");


	//if(elementId!=target.attr('id')&&$("#"+elementId).parent().parent().attr('id')!=target.attr('id')){
	if(confirm("Вы точно хотеите перенести данный элемент?")){
		targetType = target.attr('type');
		elementType = $(data.element).closest("li[id]").attr("type");
		//console.log($(data.element).closest("li[id]").parent().parent().attr("id"));
		setTimeout(function(){
			parentOfElement = $('#separators').jstree(true).get_parent(elementId);
			parentOfTarget = $('#separators').jstree(true).get_parent(target);
			left = $('#'+elementId).prev().attr("id");
			if(left==undefined){left=-1};
				
				right = $('#'+elementId).next().attr("id");
				if(right==undefined){right=-1};
				if((parentOfElement!=parentOfTarget)||((parentOfElement == parentOfTarget)&&(target!=elementId))){
					$.ajax({
						type: "POST",
						url: "/main/updateTree",
						data: "currentNode="+elementId+"&parentNode="+parentOfElement+"&left="+left+"&right="+right,
					});
				}
				//console.log($('#'+elementId).prev().attr("id"));
				//console.log($('#'+elementId).next().attr("id"));
			console.log("elementId = " + elementId);
			console.log("targetId = " + target);
			console.log(parentOfElement);
			console.log(parentOfTarget);
		},100)
	}else{
		$("#"+elementId).remove();
		$('#separators').jstree(true).refresh_node($("#"+oldParentId));
		$('#separators').jstree(true).open_node(oldParentId);
	}
	
});

$('body').on('hover','.jstree-wholerow',function(){
	console.log("dragover!");
});

function chooseMouseUp(numCardStatus){
	if(numCardStatus){
		$("body").off('mousemove');
		$("body").off('mouseup');
		$("body").on('mouseup',function(){
			if(statusDragInTree&&(hoveredElement!=undefined)){
				var ref = $('#separators').jstree(true),
				sel = ref.get_selected();
				console.log(sel[0]);
				if(sel[0]!=hoveredElement['id']){
					console.log(currentDraggableElement);
					if((hoveredElement['type']!='box')&&(hoveredElement['type']!='separator')){
						alert("Карточки могут храниться только в ящиках и разделителях!")
					}else{
						idCard = currentDraggableElement.attr('id');
						idParent = hoveredElement['id']
						if(confirm("Вы точно хотеите перенести данный элемент?")){
							console.log(currentDraggableElement);
							

							$.post("/main/moveCard",{'idParent':idParent,'idCard':idCard});
							
							statusDragInTree = false;
							currentDraggableElement = undefined;
							$('body').find(".ui-sortable-placeholder").remove();
							$('#sortable').find(currentDraggableElement).remove();
							
						}
					}
				}else{
					alert("Данные карточки уже содержатся в этом каталоге");
				}
			}
			statusDragInTree = false;
			currentDraggableElement = undefined;
		})
	}else{
		$("body").on('mousemove',function(e){
			$("#numDraggedItems").offset({top:e.pageY+10, left:e.pageX+10});
		});
		$("body").on('mouseup',function(e){
			if(hoveredElement!=undefined){
				var ref = $('#separators').jstree(true),
				sel = ref.get_selected();
				parentId = hoveredElement['id'];
				console.log(hoveredElement);
				console.log(sel[0]);
				if(sel[0]!=parentId){
					if((hoveredElement!=false)||(hoveredElement!=undefined)){
						if((hoveredElement['type']!='box')&&(hoveredElement['type']!='separator')){
							alert("Карточки могут храниться только в ящиках и разделителях!")
						}else{
							if(confirm("Вы точно хотеите перенести данный элемент?")){
								draggedCards=[];
								$('[id].ui-selected').each(function(index,element){
									draggedCards.push($(element).attr("id"));
								})

								numChoosenElements=$("li.ui-selected").length-1;
								dragArraySomeElements = [];
								statusUpdating = true;
								i=numChoosenElements;
								while(statusUpdating){
									currentElement = $("li.ui-selected").eq(i);
									stepArray =[-1,-1];
									if(currentElement.next().length!=0){
										stepArray[1] = currentElement.next().attr('id');
									}

									statusFindPrevElement = true;
									while(statusFindPrevElement){
										currentElement = $("li.ui-selected").eq(i);
										if(currentElement.prev().length!=0){
											if(currentElement.prev().hasClass('ui-selected')){				
												if(i>0){
													i--;
												}else{
													statusFindPrevElement = false;
													statusUpdating = false;
												}
											}else{
												stepArray[0] = currentElement.prev().attr('id');
												if(i>0){
													i--;
												}else{
													statusFindPrevElement = false;
													statusUpdating = false;
												}
												dragArraySomeElements.push(stepArray);
												statusFindPrevElement = false;
											}
										}else{
											dragArraySomeElements.push(stepArray);
											statusFindPrevElement = false;
											statusUpdating = false;
										}
									}
								}
								
								console.log(dragArraySomeElements);

								for(i=0;i<dragArraySomeElements.length;i++){
									dragArraySomeElements[i]=dragArraySomeElements[i].join('?');
								}
								draggedCards = draggedCards.reverse();	
								dragArraySomeElements=dragArraySomeElements.join('!');
								for(i=0;i<draggedCards.length;i++){
									$("#"+draggedCards[i]+".ui-selected").remove();
								}
								draggedCards=draggedCards.join('!');
								$.post("/main/updateSomeCardPosition",{'draggedCards':draggedCards,'positions':dragArraySomeElements,'parent':parentId});
								console.log(dragArraySomeElements);
								console.log(draggedCards);
							}
						}
					}
				}else{
					alert("Данные карточки уже содержатся в этом каталоге");
				} 
			}
			selectableOn = true;
			hoveredElement=undefined;
			$("#numDraggedItems").remove();
			$("#sortable").selectable("enable");
			$(".ui-selected").removeClass("ui-selected");
			chooseMouseUp(true);
		});
	}
}

function funcCreateDialogSave(){
	type = $("#typeCreate").val();
	switch(type){
		case "Карточка":
			type = "card"
			break
		case "Каталог":
			type = "catalog"
			break
		case "Разделитель":
			type = "separator"
			break
		case "Ящик":
			type = "box"
			break
	}
	name = $("#createName").val();
	
	var tree = $('#separators').jstree(true),
		branch = tree.get_selected();
	if(!branch.length){return false;}
	parentId = branch[0];
	
	$.post("/main/create",{'parentId':parentId,'name':name,'type':type})
	.success(function() { 
		console.log("complete");
		tree.refresh();
	})
	
	$('#createDialog').modal('hide');
};

function demo_create() {
	
	var tree = $('#separators').jstree(true),
		branch = tree.get_selected();
	if(!branch.length){return false;}
	branch = branch[0];
	$("#typeCreate").empty();
	//console.log(tree.get_type($("#"+branch)));
	type = tree.get_type($("#"+branch));
	//console.log(type);
	switch(type){
		case "catalog":
			$("#typeCreate").append("<option>Разделитель</option><option>Ящик</option>")
			break
		case "separator":
			$("#typeCreate").append("<option>Ящик</option>")
			break
		case "box":
			$("#typeCreate").append("<option>Разделитель</option>")
			break
	}



	if(type=='main'){
		alert("в главном каталоге нельзя создавать элементы");
	}else{
		$('#createDialog').modal('show');
		branchName = $('#' + branch).text();
		if(branchName.substring(1).match(/(\S+)\s/) != null){
			branchName = branchName.substring(1).match(/(\S+)\s/)[0];
		}

		$("#parentElementCreate").text(branchName);
		console.log(branchName);
		
	}
};

function demo_rename() {
	if(confirm("Вы уверены, что хотите переименовать этот элемент?")){
		$('.jstree-rename-input').off("keypress");
		var ref = $('#separators').jstree(true),
			sel = ref.get_selected();
		if(!sel.length) { return false; }
		sel = sel[0];
		ref.edit(sel);
		i=0;
		statusRename = false;
		console.log($('.jstree-rename-input'));
		
		$("#separators").on('rename_node.jstree',function(){
			var ref = $('#separators').jstree(true),
				sel = ref.get_selected();
			if(!sel.length) { return false; }
			sel = sel[0];

			branchName = ref.get_text(sel);
			branchElement = ref.get_node(sel,true);
			type = branchElement.attr("type");
			id = branchElement.attr("id");
			$.post("/main/rename",{'id':id,'name':branchName,'type':type})
		})
	}
};

function demo_delete() {
	
	var ref = $('#separators').jstree(true),
		sel = ref.get_selected();
	if(!sel.length) { return false; }
	id = sel[0];
	name = $("#"+id).text().substring(1);
	type = ref.get_type($("#"+sel))
	if(type=='main'){
		alert('Вы не можете удалить главный каталог');
	}else if(type=='catalog'){
		alert('Вы не можете удалить каталог');
	}else{
		if(confirm("Вы уверены, что хотите удалить этот элемент?")){
			$.post("/main/delete",{'id':id,'name':name,'type':type})
			ref.delete_node(sel);
		}
	}
	
}; 

function setHeiHeight() { 
	console.log($('#sortable').height());
	console.log($(window).height());
	if($('#sortable').height()<($(window).height()-150)){
		console.log("go");
		$('#sortable').css({
			height: ($(window).height()-150) + 'px' 
		}); 
	}

} 
setHeiHeight();