<?php

$modelName = $this->modelName;

//create an array open_nodes with the ids of the nodes that we want to be initially open
//when the tree is loaded.Modify this to suit your needs.Here,we open all nodes on load.
$identifiers=array();
foreach($modelName::model()->findAll(array('order'=>'lft')) as $n=>$category)
{
	$identifiers[]="'".'node_'.$category->id."'";
}

$dataProvider = new CActiveDataProvider($modelName);
$baseUrl = Yii::app()->baseUrl;
$open_nodes = implode(',', $identifiers);

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

// as handling multiple models i.e. tasks, crews and days and possibly projects in future, need a modal for each
//$projectController = new ProjectController(null);
//$projectController->actionCreate('myModalProject');

$dayController = new DayController(null);
$dayController->actionCreate('myModalDay');

$crewController = new CrewController(null);
$crewController->actionCreate('myModalCrew');

$taskController = new TaskController(null);
$taskController->actionCreate('myModalTask');
// as using boostrap modal for create the html for the modal needs to be on
// the calling page i.e. this admin page
//$this->actionCreate();



echo '

	<!--The tree will be rendered in this div-->

	<div id="'.  $modelName::ADMIN_TREE_CONTAINER_ID.'" >

	</div>';

?>


<script  type="text/javascript">
	$(function ()
	{
		$("#<?php echo $modelName::ADMIN_TREE_CONTAINER_ID; ?>")
		.jstree(
		{
			"html_data" :
				{
				"ajax" :
					{
					"type":"POST",
					"url" : "<?php echo "$baseUrl/$modelName/fetchTree"; ?>",
					"data" : function (n)
					{
						return{
							id : n.attr ? n.attr("id") : 0,
							"YII_CSRF_TOKEN":"<?php echo Yii::app()->request->csrfToken; ?>"
						};
					}
				}
			},

			"contextmenu":  { 'items': function(obj){
					contextMenu = {};
					
					// get target mode level
					id=obj.attr("id").replace("node_","");

					if(obj.is("DIV"))
					{
						level = 1;
					}
					else
					{
						level = obj.find('[class^="level"]').first().attr("class").replace("level","");
					}

					// get the users rights
					accessProject = <?php echo Controller::checkAccess(Controller::accessWrite, "Project"); ?>;
					accessDay = <?php echo Controller::checkAccess(Controller::accessWrite, "Day"); ?>;
					accessCrew = <?php echo Controller::checkAccess(Controller::accessWrite, "Crew"); ?>;
					accessTask = <?php echo Controller::checkAccess(Controller::accessWrite, "Task"); ?>;

					if(level == 1)
					{
						writeAccess = accessProject;
						modelName = "Project";
						reportItemsProject = <?php echo $this->getReportsMenu(Controller::reportTypeJavascript, 'Project'); ?>;
					}
					else if(level == 2)
					{
						writeAccess = accessDay;
						modelName = "Day";
						reportItemsProject = <?php echo $this->getReportsMenu(Controller::reportTypeJavascript, 'Day'); ?>;
					}
					else if(level == 3)
					{
						writeAccess = accessCrew;
						modelName = "Crew";
						reportItemsProject = <?php echo $this->getReportsMenu(Controller::reportTypeJavascript, 'Crew'); ?>;
					}
					else if(level == 4)
					{
						writeAccess = accessTask;
						modelName = "Task";
						reportItemsProject = <?php echo $this->getReportsMenu(Controller::reportTypeJavascript, 'Task'); ?>;
					}

					if(writeAccess)
					{
						rename = {
							"rename" : {
								"label" : "Rename",
								"action" : function (obj) { this.rename(obj); }
							}
						};//rename
						$.extend(contextMenu, rename);

						update = {
							"update" : {
								"label"	: "Update",
								"action"	: function (obj) {

									// need to re-read modal contents first as selecting a different record
									$.ajax({
										type: "POST",
										url: "<?php echo "$baseUrl/$modelName/returnForm"; ?>",
										data:{
											'update_id':  id,
											'model_name':  modelName,
											"YII_CSRF_TOKEN":"<?php echo Yii::app()->request->csrfToken; ?>"
										},
										'beforeSend' : function(){
											$("#<?php echo $modelName::ADMIN_TREE_CONTAINER_ID; ?>").addClass("ajax-sending");
										},
										'complete' : function(){
											$("#<?php echo $modelName::ADMIN_TREE_CONTAINER_ID; ?>").removeClass("ajax-sending");
										},
										success: function(data){
											// change the contents
											$("#myModal" + modelName + " form").replaceWith(data);
											// set the parent id in the hidden modal
			//								$('[name="parent_id"]').val(obj.attr("id").replace("node_",""));
											// display the modal
											$("#myModal" + modelName).modal('show');

										} //success
									});//ajax

								}//action function

							}
						};//update
						$.extend(contextMenu, update);

						remove = {
							"remove" : {
								"label"	: "Delete",
								"action" : function (obj) {

									if(confirm('Are you sure you want to remove ' + (obj).attr('rel') + 'and any sub categories'))
									{
										jQuery("#<?php echo $modelName::ADMIN_TREE_CONTAINER_ID; ?>").jstree("remove",obj);
									}
								}
							}
						};//remove
						$.extend(contextMenu, remove);

						if(level != 4)
						{

							create = {
								"create" : {
									"label"	: "Create",
									"action" : function (obj) {
										ajaxdata = { "YII_CSRF_TOKEN":"<?php echo Yii::app()->request->csrfToken; ?>" };

										// get the target model name
										if(level == 1)
										{
											targetName = "Day";
											$.extend(ajaxdata, {project_id : id});
										}
										else if(level == 2)
										{
											targetName = "Crew";
											$.extend(ajaxdata, {day_id : id});
										}
										else if(level == 3)
										{
											targetName = "Task";
											$.extend(ajaxdata, {crew_id : id});
										}

										$.extend(ajaxdata, {model_name : targetName});

										// need to re-read modal contents first as selecting a different record
										$.ajax({
											type: "POST",
											url: "<?php echo "$baseUrl/$modelName/returnForm"; ?>",
											data: ajaxdata,
											'beforeSend' : function(){
												$("#<?php echo $modelName::ADMIN_TREE_CONTAINER_ID; ?>").addClass("ajax-sending");
											},
											'complete' : function(){
												$("#<?php echo $modelName::ADMIN_TREE_CONTAINER_ID; ?>").removeClass("ajax-sending");
											},
											success: function(data){
												// change the contents
												$("#myModal" + targetName + " form").replaceWith(data);
												// set the parent id in the hidden modal
				//								$('[name="parent_id"]').val(obj.attr("id").replace("node_",""));
												// display the modal
												$("#myModal" + targetName).modal('show');

											} //success
										});//ajax

									}//actions
								}
							};//create
						$.extend(contextMenu, create);
						}
					}
					
					// add any reports
					if(reportItemsProject !== null)
					{
						$.extend(contextMenu, reportItemsProject);
					}
					
					return contextMenu;
				}//items funtcion
			},//context menu

			// the `plugins` array allows you to configure the active plugins on this instance
			"plugins" : ["themes","html_data","contextmenu","crrm","dnd"],
			// each plugin you have included can have its own config object
			"core" : { "initially_open" : [ <?php echo $open_nodes ?> ],'open_parents':true},
			// it makes sense to configure a plugin only if overriding the defaults

			// restrict to moving only to correct levels
			"crrm" : {
				move : {
					check_move : function (m) {
						target = m.o.find('[class^="level"]').first().attr("class").replace("level","");

						if(m.np.is("DIV"))
						{
							destination = 1;
						}
						else
						{
							destination = m.np.find('[class^="level"]').first().attr("class").replace("level","");
						}

						return (target - destination) == 1;
					}
				}
			}
		})

		///EVENTS
		.bind("rename.jstree", function (e, data) {
			$.ajax({
				type:"POST",
				url:"<?php echo "$baseUrl/$modelName/rename"; ?>",
				data:  {
					"id" : data.rslt.obj.attr("id").replace("node_",""),
					"new_name" : data.rslt.new_name,
					"YII_CSRF_TOKEN":"<?php echo Yii::app()->request->csrfToken; ?>"
				},
				beforeSend : function(){
					$("#<?php echo $modelName::ADMIN_TREE_CONTAINER_ID; ?>").addClass("ajax-sending");
				},
				complete : function(){
					$("#<?php echo $modelName::ADMIN_TREE_CONTAINER_ID; ?>").removeClass("ajax-sending");
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
				url:"<?php echo "$baseUrl/$modelName/remove"; ?>",
				data:{
					"id" : data.rslt.obj.attr("id").replace("node_",""),
					"YII_CSRF_TOKEN":"<?php echo Yii::app()->request->csrfToken; ?>"
				},
				beforeSend : function(){
					$("#<?php echo $modelName::ADMIN_TREE_CONTAINER_ID; ?>").addClass("ajax-sending");
				},
				complete: function(){
					$("#<?php echo $modelName::ADMIN_TREE_CONTAINER_ID; ?>").removeClass("ajax-sending");
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

				next= jQuery.jstree._reference('#<?php echo $modelName::ADMIN_TREE_CONTAINER_ID; ?>')._get_next (this, true);
				previous= jQuery.jstree._reference('#<?php echo $modelName::ADMIN_TREE_CONTAINER_ID; ?>')._get_prev(this,true);

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
					url: "<?php echo "$baseUrl/$modelName/moveCopy"; ?>",

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
						$("#<?php echo $modelName::ADMIN_TREE_CONTAINER_ID; ?>").addClass("ajax-sending");
					},
					complete : function(){
						$("#<?php echo $modelName::ADMIN_TREE_CONTAINER_ID; ?>").removeClass("ajax-sending");
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
							// refresh entire tree to auto update runtime derived lables
							jQuery("#<?php echo $modelName::ADMIN_TREE_CONTAINER_ID; ?>").jstree("refresh");
							//  console.log('OK');
						}

					}
				}); //ajax

			});//each function
		});   //bind move event

		;//JSTREE FINALLY ENDS (PHEW!)

		$("#reload").click(function ()
		{
			jQuery("#<?php echo $modelName::ADMIN_TREE_CONTAINER_ID; ?>").jstree("refresh");
		});

		$("#<?php echo $modelName::ADMIN_TREE_CONTAINER_ID; ?>").delegate("a","click", function(e) {
			// get the id of the clicked node
//			id = $(this).parent().attr("id").split("_")[1];
			obj = $(this).parent();
			
			// get target node level
			id=obj.attr("id").replace("node_","");

			if(obj.is("DIV"))
			{
				level = 1;
			}
			else
			{
				level = obj.find('[class^="level"]').first().attr("class").replace("level","");
			}
			
			// get the users rights
			accessProject = <?php echo Controller::checkAccess(Controller::accessRead, "Project"); ?>;
			accessDay = <?php echo Controller::checkAccess(Controller::accessRead, "Day"); ?>;
			accessCrew = <?php echo Controller::checkAccess(Controller::accessRead, "Crew"); ?>;
			accessTask = <?php echo Controller::checkAccess(Controller::accessRead, "Task"); ?>;
			readAccess = null;

			if(level == 1)
			{
				readAccess = accessProject;
				modelName = "Project";
			}
			else if(level == 2)
			{
				readAccess = accessDay;
				modelName = "Day";
			}
			else if(level == 3)
			{
				readAccess = accessCrew;
				modelName = "Crew";
			}
			else if(level == 4)
			{
				readAccess = accessTask;
				modelName = "Task";
			}
			
			if(readAccess !== null)
			{
				// go to the admin screen - filtering by this parent id
				window.location = encodeURI("<?php echo "$baseUrl/"; ?>" + modelName + "/update/" + id);
			}
			
		});

	});

</script>
