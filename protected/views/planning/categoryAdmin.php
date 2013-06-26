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
$queryString = Yii::app()->request->queryString
	? '?'.Yii::app()->request->queryString
	: '';
$open_nodes = implode(',', $identifiers);

// holder for the modal form
echo '<div class="modal fade" id="myModal" style="display: block;"><div class="modal-body" id="form-modal"></div></div>';
// The tree will be rendered in this div-->
echo '<div id="'.  $modelName::ADMIN_TREE_CONTAINER_ID.'" ></div>';

?>

<script  type="text/javascript">
	$(function ()
	{
		// get the users rights
		writeAccessProject = <?php echo Controller::checkAccess(Controller::accessWrite, 'Project') ? 'true' : 'false'; ?>;
		writeAccessDay = <?php echo Controller::checkAccess(Controller::accessWrite, 'Day') ? 'true' : 'false'; ?>;
		writeAccessCrew = <?php echo Controller::checkAccess(Controller::accessWrite, 'Crew') ? 'true' : 'false'; ?>;
		writeAccessTask = <?php echo Controller::checkAccess(Controller::accessWrite, 'Task') ? 'true' : 'false'; ?>;
		readAccessProject = <?php echo Controller::checkAccess(Controller::accessRead, 'Project') ? 'true' : 'false'; ?>;
		readAccessDay = <?php echo Controller::checkAccess(Controller::accessRead, 'Day') ? 'true' : 'false'; ?>;
		readAccessCrew = <?php echo Controller::checkAccess(Controller::accessRead, 'Crew') ? 'true' : 'false'; ?>;
		readAccessTask = <?php echo Controller::checkAccess(Controller::accessRead, 'Task') ? 'true' : 'true'; ?>;
		isScheduler = <?php echo Yii::app()->user->checkAccess('scheduler') ? 'true' : 'false'; ?>;
		
					
		function getAction(obj)
		{
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

			if(level == 1)
			{
				writeAccess = writeAccessProject;
				readAccess = readAccessProject;
				modelName = "Project";
				reportItemsProject = <?php echo $this->getReportsMenu(Controller::reportTypeJavascript, 'Project'); ?>;
			}
			else if(level == 2)
			{
				writeAccess = writeAccessDay && isScheduler;
				readAccess = readAccessDay;
				modelName = "Day";
				reportItemsProject = <?php echo $this->getReportsMenu(Controller::reportTypeJavascript, 'Day'); ?>;
			}
			else if(level == 3)
			{
				writeAccess = writeAccessCrew && isScheduler;
				readAccess = readAccessCrew;
				modelName = "Crew";
				reportItemsProject = <?php echo $this->getReportsMenu(Controller::reportTypeJavascript, 'Crew'); ?>;
			}
			else if(level == 4)
			{
				writeAccess = writeAccessTask;
				readAccess = readAccessTask;
				modelName = "Task";
				reportItemsProject = <?php echo $this->getReportsMenu(Controller::reportTypeJavascript, 'Task'); ?>;
			}

			if(writeAccess)
			{
				action = "Update";
			}
			else if(readAccess)
			{
				action = "View";
			}
			else
			{
				action = false;
			}
		}

		$("#<?php echo $modelName::ADMIN_TREE_CONTAINER_ID; ?>").jstree(
		{
			"html_data" :
				{
				"ajax" :
					{
					"type":"POST",
					"url" : "<?php echo "$baseUrl/$modelName/fetchTree$queryString"; ?>",
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
					getAction(obj);
					if(action)
					{
						update = {
							"update" : {
								"label"	: action,
								"action" : function (obj) {

									// need to re-read modal contents first as selecting a different record
									$.ajax({
										type: "POST",
										url: "<?php echo "$baseUrl/$modelName/returnForm$queryString"; ?>",
										data:{
											"id":  id,
											"action": action,
											"model_name":  modelName,
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
											$("#form-modal").html(data);
											formAction = $("#form-modal form[action]").attr('action');
											$("#form-modal form[action]").attr('action', formAction + '<?php echo $queryString; ?>');
											// display the modal
											$("#myModal").modal('show');

										} //success
									});//ajax

								}//action function

							}
						};//update
						$.extend(contextMenu, update);

						if(writeAccess)
						{
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
												url: "<?php echo "$baseUrl/$modelName/returnForm$queryString"; ?>",
												data: ajaxdata,
												'beforeSend' : function(){
													$("#<?php echo $modelName::ADMIN_TREE_CONTAINER_ID; ?>").addClass("ajax-sending");
												},
												'complete' : function(){
													$("#<?php echo $modelName::ADMIN_TREE_CONTAINER_ID; ?>").removeClass("ajax-sending");
												},
												success: function(data){
													// change the contents
													$("#form-modal").html(data);
													// display the modal
													$("#myModal").modal('show');

												} //success
											});//ajax

										}//actions
									}
								};
								$.extend(contextMenu, create);
							}//create
						}//if(writeAccess)
					}//if(action)
					
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

		.bind("remove.jstree", function (e, data) {
			$.ajax({
				type:"POST",
				url:"<?php echo "$baseUrl/$modelName/remove$queryString"; ?>",
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
					url: "<?php echo "$baseUrl/$modelName/moveCopy$queryString"; ?>",

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
//TODO: make this a function as exact repeat of above - pass in object and return the other required variables
			// get target mode level
			getAction($(this).parent());

			if(action)
			{
				// go to the admin screen - filtering by this parent id
				window.location = encodeURI("<?php echo "$baseUrl/"; ?>" + modelName + "/" + action + "/" + id + "<?php echo $queryString; ?>");
			}
			
		});

	});

</script>
<?php

$this->newButton();

?>