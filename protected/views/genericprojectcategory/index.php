<?php

Yii::app()->user->setFlash('info','
	<ul>
		<li>If tree is empty,start by creating one or more root nodes.</li>
		<li>Right Click on a node to see available operations.</li>
		<li>Move nodes with Drag And Drop.You can move a non-root node to root position and vice versa.</li>
		<li>Root nodes cannot be reordered.Their order is fixed  by id.</li>
	</ul>');

$this->widget('bootstrap.widgets.TbAlert');

// as using boostrap modal for create the html for the modal needs to be on
// the calling page i.e. this admin page
$this->actionCreate();

echo '

	<!--The tree will be rendered in this div-->

	<div id="'.Genericprojectcategory::ADMIN_TREE_CONTAINER_ID.'" >

	</div>';

?>


<script  type="text/javascript">
	$(function ()
	{
		$("#<?php echo Genericprojectcategory::ADMIN_TREE_CONTAINER_ID; ?>")
		.jstree(
		{
			"html_data" :
				{
				"ajax" :
					{
					"type":"POST",
					"url" : "<?php echo $baseUrl; ?>/genericprojectcategory/fetchTree",
					"data" : function (n)
					{
						return{
							id : n.attr ? n.attr("id") : 0,
							"YII_CSRF_TOKEN":"<?php echo Yii::app()->request->csrfToken; ?>"
						};
					}
				}
			},

			"contextmenu":  { 'items': {

					"rename" : {
						"label" : "Rename",
						"action" : function (obj) { this.rename(obj); }
					},
					"update" : {
						"label"	: "Update",
						"action"	: function (obj) {
							id=obj.attr("id").replace("node_","");
							// need to re-read modal contents first as selecting a different record
							$.ajax({
								type: "POST",
								url: "<?php echo $baseUrl; ?>/genericprojectcategory/returnForm",
								data:{
									'update_id':  id,
									"YII_CSRF_TOKEN":"<?php echo Yii::app()->request->csrfToken; ?>"
								},
								'beforeSend' : function(){
									$("#<?php echo Genericprojectcategory::ADMIN_TREE_CONTAINER_ID; ?>").addClass("ajax-sending");
								},
								'complete' : function(){
									$("#<?php echo Genericprojectcategory::ADMIN_TREE_CONTAINER_ID; ?>").removeClass("ajax-sending");
								},
								success: function(data){

									// change the contents
									$("#myModal form").replaceWith(data);
									// set the parent id in the hidden modal
	//								$('[name="parent_id"]').val(obj.attr("id").replace("node_",""));
									// display the modal
									$("#myModal").modal('show');

								} //success
							});//ajax

						}//action function

					},//update

					"remove" : {
						"label"	: "Delete",
						"action" : function (obj) {
						
							if(confirm('Are you sure you want to remove ' + (obj).attr('rel') + 'and any sub categories'))
							{
								jQuery("#<?php echo Genericprojectcategory::ADMIN_TREE_CONTAINER_ID; ?>").jstree("remove",obj);
							}
						}
					},//remove
					"create" : {
						"label"	: "Create",
						"action" : function (obj) {
							// set the parent id in the hidden modal
							$('[name="parent_id"]').val(obj.attr("id").replace("node_",""));
							// display the modal
							$("#myModal").modal('show');
							
						},
						"separator_after": false
					}


				}//items
			},//context menu

			// the `plugins` array allows you to configure the active plugins on this instance
			"plugins" : ["themes","html_data","contextmenu","crrm","dnd"],
			// each plugin you have included can have its own config object
			"core" : { "initially_open" : [ <?php echo $open_nodes ?> ],'open_parents':true}
			// it makes sense to configure a plugin only if overriding the defaults

		})

		///EVENTS
		.bind("rename.jstree", function (e, data) {
			$.ajax({
				type:"POST",
				url:"<?php echo $baseUrl; ?>/genericprojectcategory/rename",
				data:  {
					"id" : data.rslt.obj.attr("id").replace("node_",""),
					"new_name" : data.rslt.new_name,
					"YII_CSRF_TOKEN":"<?php echo Yii::app()->request->csrfToken; ?>"
				},
				beforeSend : function(){
					$("#<?php echo Genericprojectcategory::ADMIN_TREE_CONTAINER_ID; ?>").addClass("ajax-sending");
				},
				complete : function(){
					$("#<?php echo Genericprojectcategory::ADMIN_TREE_CONTAINER_ID; ?>").removeClass("ajax-sending");
				},
				success:function (r) {  response= $.parseJSON(r);
					if(!response.success) {
						$.jstree.rollback(data.rlbk);
					}else{
						data.rslt.obj.attr("rel",data.rslt.new_name);
					};
				}
			});
		})

		.bind("remove.jstree", function (e, data) {
			$.ajax({
				type:"POST",
				url:"<?php echo $baseUrl; ?>/genericprojectcategory/remove",
				data:{
					"id" : data.rslt.obj.attr("id").replace("node_",""),
					"YII_CSRF_TOKEN":"<?php echo Yii::app()->request->csrfToken; ?>"
				},
				beforeSend : function(){
					$("#<?php echo Genericprojectcategory::ADMIN_TREE_CONTAINER_ID; ?>").addClass("ajax-sending");
				},
				complete: function(){
					$("#<?php echo Genericprojectcategory::ADMIN_TREE_CONTAINER_ID; ?>").removeClass("ajax-sending");
				},
				success:function (r) {  response= $.parseJSON(r);
					if(!response.success) {
						$.jstree.rollback(data.rlbk);
					};
				}
			});
		})

		.bind("move_node.jstree", function (e, data) {
			data.rslt.o.each(function (i) {

				//jstree provides a whole  bunch of properties for the move_node event
				//not all are needed for this view,but they are there if you need them.
				//Commented out logs  are for debugging and exploration of jstree.

				next= jQuery.jstree._reference('#<?php echo Genericprojectcategory::ADMIN_TREE_CONTAINER_ID; ?>')._get_next (this, true);
				previous= jQuery.jstree._reference('#<?php echo Genericprojectcategory::ADMIN_TREE_CONTAINER_ID; ?>')._get_prev(this,true);

				pos=data.rslt.cp;
				moved_node=$(this).attr('id').replace("node_","");
				next_node=next!=false?$(next).attr('id').replace("node_",""):false;
				previous_node= previous!=false?$(previous).attr('id').replace("node_",""):false;
				new_parent=$(data.rslt.np).attr('id').replace("node_","");
				old_parent=$(data.rslt.op).attr('id').replace("node_","");
				ref_node=$(data.rslt.r).attr('id').replace("node_","");
				ot=data.rslt.ot;
				rt=data.rslt.rt;
				copy= typeof data.rslt.cy!='undefined'?data.rslt.cy:false;
				copied_node= (typeof $(data.rslt.oc).attr('id') !='undefined')? $(data.rslt.oc).attr('id').replace("node_",""):'UNDEFINED';
				new_parent_root=data.rslt.cr!=-1?$(data.rslt.cr).attr('id').replace("node_",""):'root';
				replaced_node= (typeof $(data.rslt.or).attr('id') !='undefined')? $(data.rslt.or).attr('id').replace("node_",""):'UNDEFINED';


				//                      console.log(data.rslt);
				//                      console.log(pos,'POS');
				//                      console.log(previous_node,'PREVIOUS NODE');
				//                      console.log(moved_node,'MOVED_NODE');
				//                      console.log(next_node,'NEXT_NODE');
				//                      console.log(new_parent,'NEW PARENT');
				//                      console.log(old_parent,'OLD PARENT');
				//                      console.log(ref_node,'REFERENCE NODE');
				//                      console.log(ot,'ORIGINAL TREE');
				//                      console.log(rt,'REFERENCE TREE');
				//                      console.log(copy,'IS IT A COPY');
				//                      console.log( copied_node,'COPIED NODE');
				//                      console.log( new_parent_root,'NEW PARENT INCLUDING ROOT');
				//                      console.log(replaced_node,'REPLACED NODE');


				$.ajax({
					async : false,
					type: 'POST',
					url: "<?php echo $baseUrl; ?>/genericprojectcategory/moveCopy",

					data : {
						"moved_node" : moved_node,
						"new_parent":new_parent,
						"new_parent_root":new_parent_root,
						"old_parent":old_parent,
						"pos" : pos,
						"previous_node":previous_node,
						"next_node":next_node,
						"copy" : copy,
						"copied_node":copied_node,
						"replaced_node":replaced_node,
						"YII_CSRF_TOKEN":"<?php echo Yii::app()->request->csrfToken; ?>"
					},
					beforeSend : function(){
						$("#<?php echo Genericprojectcategory::ADMIN_TREE_CONTAINER_ID; ?>").addClass("ajax-sending");
					},
					complete : function(){
						$("#<?php echo Genericprojectcategory::ADMIN_TREE_CONTAINER_ID; ?>").removeClass("ajax-sending");
					},
					success : function (r) {
						response=$.parseJSON(r);
						if(!response.success) {
							$.jstree.rollback(data.rlbk);
							alert(response.message);
						}
						else {
							//if it's a copy
							if  (data.rslt.cy){
								$(data.rslt.oc).attr("id", "node_" + response.id);                         
								if(data.rslt.cy && $(data.rslt.oc).children("UL").length) {
									data.inst.refresh(data.inst._get_parent(data.rslt.oc));
								}
							}
							//  console.log('OK');
						}

					}
				}); //ajax



			});//each function
		});   //bind move event

		;//JSTREE FINALLY ENDS (PHEW!)

		$("#reload").click(function ()
		{
			jQuery("#<?php echo Genericprojectcategory::ADMIN_TREE_CONTAINER_ID; ?>").jstree("refresh");
		});
	});

</script>

