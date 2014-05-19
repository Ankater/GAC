var oldParentId;

$.jstree.defaults.dnd.copy = false;
$(document).ready(function(){
	$('#separators')
		.jstree({
			'core' : {
				"check_callback" : true,
				'data' : {
					'url' : '/main/treeNode',
				    'data' : function (node) {
				    	if(node.li_attr==undefined){
				        	return { 'id' : node.id};
				    	}else{
				    		console.log(node.li_attr);
				    		return { 'id' : node.id, 'type': node.li_attr.role};
				    	}
				    }
				}
			},
			"plugins" : ["contextmenu", "dnd", "search", "types", "wholerow"]
		});
});
$(document).on('dnd_start.vakata',function(e,data){
	target = $(data.event.target).closest("li[id]");
	oldParentId = $('#separators').jstree(true).get_parent(target);
})
$(document).on('dnd_stop.vakata', function (e, data) {
	target = $(data.event.target).closest("li[id]");
	$('#separators').jstree(true).open_node(target);
	elementId = $(data.element).closest("li[id]").attr("id");


	if(confirm("Вы точно хотеите перенести данный элемент?")){
		//console.log($(data.element).closest("li[id]").parent().parent().attr("id"));
		setTimeout(function(){
			parentOfElement = $('#separators').jstree(true).get_parent(elementId);
			parentOfTarget = $('#separators').jstree(true).get_parent(target);

			left = $('#'+elementId).prev().attr("id");
			if(left==undefined){left=0};
			
			right = $('#'+elementId).next().attr("id");
			if(right==undefined){right=0};

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
	/*
	if(elementId!=targetId){}
	$('#separators').jstree(true).open_node($(data.event.target).closest("li[id]").attr("id"));
	console.log($(data.element).closest("li[id]").attr("id"));
	console.log($('#'+$(data.element).closest("li[id]").attr("id")+''));
	console.log($(data.event.target).closest("li[id]").attr("id"));
	*/
});


function demo_create() {
	/*
	var ref = $('#separators').jstree(true),
		sel = ref.get_selected();
	if(!sel.length) { return false; }
	sel = sel[0];
	sel = ref.create_node(sel, {"type":"file"});
	if(sel) {
		ref.edit(sel);
	}
	*/
	
	var tree = $('#separators').jstree(true),
		branch = tree.get_selected();
	if(!branch.length){return false;}
	branch = branch[0];
	$("#typeCreate").empty();
	role = $("#"+branch).attr("role");
	switch(role){
		case "catalog":
			$("#typeCreate").append("<option>Разделитель</option><option>Ящик</option>")
			break
		case "separator":
			$("#typeCreate").append("<option>Ящик</option><option>Карточка</option>")
			break
		case "box":
			$("#typeCreate").append("<option>Разделитель</option><option>Карточка</option>")
			break
		case "card":
			$("#create").popover({
				title:"В карточке нельзя создавать элементы"
			})
			$("#create").popover("show");
			setTimeout(function(){
				$("#create").popover("destroy");
				console.log("destroy");
			},3000)
			break
	}

	if(role!="card"){
		$('#createDialog').modal('show');
		branchName = $('#' + branch).text();
		if(branchName.substring(1).match(/(\S+)\s/) != null){
			branchName = branchName.substring(1).match(/(\S+)\s/)[0];
		}
		$("#parentElementCreate").text(branchName);
		console.log(branchName);
	}
};

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

	console.log($("#"+parentId).attr("role"));
	$.post("/main/create",{'parentId':parentId,'name':name,'type':type});
	$('#createDialog').modal('hide');
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
			type = branchElement.attr("role");
			id = branchElement.attr("id");
			$.post("/main/rename",{'id':id,'name':branchName,'type':type})
		})
/*
		$("#separators").on('set_text.jstree',function(){
			setTimeout(function(){
				var ref = $('#separators').jstree(true),
					sel = ref.get_selected();
				if(!sel.length) { return false; }
				sel = sel[0];
				console.log(ref.get_text(sel));
				console.log("2!!!!!!!")
			},500)
		})
		//$('.jstree-rename-input').unbind('keydown');
		/*
		$('.jstree-rename-input').on("keydown",function(e){
			console.log(e.keyCode);
			if(e.keyCode===13){
				ref = $('#separators').jstree(true);
				console.log("!!!!!!!!!!!!!!!!!!");
				ref.trigger('rename_node.jstree');
			}
		})
		/*
		if(i!=0){
			id = sel[0];
			if(($("#"+id).text().substring(1)).match(/(\S+)\s/) != null){
				name = ($("#"+id).text().substring(1)).match(/(\S+)\s/)[0];
			}else{
				name = $("#"+id).text();
			}
			type = $('#'+id).attr('role');
			$.post("/main/rename",{'id':id,'name':name,'type':type})
			$(document).unbind('click');
			statusRename = true;
		}else if(statusRename == true ){
			$(document).unbind('click');
			$('.jstree-rename-input').unbind('keypress');
		}else{
			i++;
		}

		$(document).click(function(){
			if(i!=0){
				id = sel[0];
				if(($("#"+id).text().substring(1)).match(/(\S+)\s/) != null){
					name = ($("#"+id).text().substring(1)).match(/(\S+)\s/)[0];
				}else{
					name = $("#"+id).text();
				}
				type = $('#'+id).attr('role');
				$.post("/main/rename",{'id':id,'name':name,'type':type})
				$(document).unbind('click');
				statusRename = true;
			}else if(statusRename == true ){
				$(document).unbind('click');
				$('.jstree-rename-input').unbind('keypress');
			}else{
				i++;
			}
		})
		$('.jstree-rename-input').on('keypress',function(e){
			if(e.keyCode === 13){
				console.log($('.jstree-rename-input').val()	)
				if(i!=0){
					id = sel[0];
					if(($("#"+id).text().substring(1)).match(/(\S+)\s/) != null){
						name = ($("#"+id).text().substring(1)).match(/(\S+)\s/)[0];
					}else{
						name = $("#"+id).text();
					}
					type = $('#'+id).attr('role');
					$.post("/main/rename",{'id':id,'name':name,'type':type})
					$(document).unbind('click');
					statusRename = true;
				}else if(statusRename == true ){
					$(document).unbind('click');
					$('.jstree-rename-input').unbind('keypress');
				}else{
					i++;
				}
			}
		})
		*/
	}
};


function demo_delete() {
	if(confirm("Вы уверены, что хотите удалить этот элемент?")){
		var ref = $('#separators').jstree(true),
			sel = ref.get_selected();
		if(!sel.length) { return false; }
		id = sel[0];
		name = $("#"+id).text().substring(1);
		type = $('#'+id).attr('role');
		$.post("/main/delete",{'id':id,'name':name,'type':type})
		ref.delete_node(sel);
	}
}; 