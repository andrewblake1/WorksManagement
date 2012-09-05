<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="en" />

	<!-- blueprint CSS framework -->
	<link rel="stylesheet" type="text/css" href="/WorksManagement/css/screen.css" media="screen, projection" />
	<link rel="stylesheet" type="text/css" href="/WorksManagement/css/print.css" media="print" />
	<!--[if lt IE 8]>
	<link rel="stylesheet" type="text/css" href="/WorksManagement/css/ie.css" media="screen, projection" />
	<![endif]-->

	<link rel="stylesheet" type="text/css" href="/WorksManagement/css/main.css" />
	<link rel="stylesheet" type="text/css" href="/WorksManagement/css/form.css" />

	<link rel="stylesheet" type="text/css" href="/WorksManagement/assets/779c334c/css/bootstrap.css" />
<link rel="stylesheet" type="text/css" href="/WorksManagement/assets/779c334c/css/datepicker.css" />
<link rel="stylesheet" type="text/css" href="/WorksManagement/assets/779c334c/css/bootstrap-yii.css" />
<link rel="stylesheet" type="text/css" href="/WorksManagement/assets/5bbf12df/jquery.fancybox-1.3.4.css" />
<link rel="stylesheet" type="text/css" href="/WorksManagement/assets/6029c1d6/css/dark-hive/jquery-ui-1.8.12.custom.css" />
<link rel="stylesheet" type="text/css" href="/WorksManagement/css/client_val_form.css" media="screen" />
<script type="text/javascript" src="/WorksManagement/assets/c9795045/jquery.min.js"></script>
<script type="text/javascript" src="/WorksManagement/assets/779c334c/js/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="/WorksManagement/assets/345acb36//jquery.jstree.js"></script>
<script type="text/javascript" src="/WorksManagement/assets/5bbf12df/jquery.fancybox-1.3.4.js"></script>
<script type="text/javascript" src="/WorksManagement/assets/6029c1d6/js/jquery-ui-1.8.12.custom.min.js"></script>
<script type="text/javascript" src="/WorksManagement/js_plugins/json2/json2.js"></script>
<title>Works Management - Categorydemo</title>
</head>

<body>

<div class="container" id="page">

	<div id="header">
		<div id="logo">Works Management</div>
	</div><!-- header -->

	<div id="mainmenu">
		<ul id="yw2">
<li><a href="/WorksManagement/">Home</a></li>
<li><a href="/WorksManagement/site/page?view=about">About</a></li>
<li><a href="/WorksManagement/site/contact">Contact</a></li>
<li><a href="/WorksManagement/site/logout">Logout (username)</a></li>
</ul>	</div><!-- mainmenu -->
	
	<div class="span-19">
	<div id="content">
		<!--
 Nested Set Admin GUI
 Main View File  index.php

 @author Spiros Kabasakalis <kabasakalis@gmail.com>,myspace.com/spiroskabasakalis
 @copyright Copyright &copy; 2011 Spiros Kabasakalis
 @since 1.0
 @license The MIT License-->

<h1> Categorydemo</h1><br>
<h2>Administration</h2><br>
<ul>
     <li>If tree is empty,start by creating one or more root nodes.</li>
    <li>Right Click on a node to see available operations.</li>
    <li>Move nodes with Drag And Drop.You can move a non-root node to root position and vice versa.</li>
     <li>Root nodes cannot be reordered.Their order is fixed  by id.</li>
</ul>
<div style="margin-bottom: 70px;" >
<div style="float:left">
  <input id="reload"  type="button" style="display:block; clear: both;" value="Refresh"class="client-val-form button">
</div>
<div style="float:left">
  <input id="add_root" type="button" style="display:block; clear: both;" value="Create Root" class="client-val-form button">
</div>
</div>


<!--The tree will be rendered in this div-->

<div id="categorydemo_admin_tree" >

</div>

<script  type="text/javascript">
$(function () {
$("#categorydemo_admin_tree")
		.jstree({
                           "html_data" : {
	            "ajax" : {
                                 "type":"POST",
 	                          "url" : "/WorksManagement/categorydemo/fetchTree",
	                         "data" : function (n) {
	                          return {
                                                  id : n.attr ? n.attr("id") : 0,
                                                  "YII_CSRF_TOKEN":"3c9c03c61bf7062deedea40a2a2efb7908e621d7"
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
                     $.ajax({
                                 type: "POST",
                                 url: "/WorksManagement/categorydemo/returnForm",
                                data:{
                                          'update_id':  id,
                                           "YII_CSRF_TOKEN":"3c9c03c61bf7062deedea40a2a2efb7908e621d7"
                                              },
			       'beforeSend' : function(){
                                           $("#categorydemo_admin_tree").addClass("ajax-sending");
                                                             },
                               'complete' : function(){
                                           $("#categorydemo_admin_tree").removeClass("ajax-sending");
                                                   },
                    success: function(data){

                        $.fancybox(data,
                        {    "transitionIn"	:	"elastic",
                            "transitionOut"    :      "elastic",
                             "speedIn"		:	600,
                            "speedOut"		:	200,
                            "overlayShow"	:	false,
                            "hideOnContentClick": false,
                             "onClosed":    function(){
                                                                       } //onclosed function
                        })//fancybox

                    } //success
                });//ajax

                                                  }//action function

},//update

    "properties" : {
	"label"	: "Properties",
	"action" : function (obj) {
                                   id=obj.attr("id").replace("node_","")
                             $.ajax({
                                   type:"POST",
			           url:"/WorksManagement/categorydemo/returnView",
		                   data:   {
                                             "id" :id,
                                            "YII_CSRF_TOKEN":"3c9c03c61bf7062deedea40a2a2efb7908e621d7"
                                              },
			         beforeSend : function(){
                                               $("#categorydemo_admin_tree").addClass("ajax-sending");
                                                               },
                                complete : function(){
                                              $("#categorydemo_admin_tree").removeClass("ajax-sending");
                                                             },
                               success :  function(data){
                        $.fancybox(data,
                        {    "transitionIn"	:	"elastic",
                            "transitionOut"    :      "elastic",
                             "speedIn"		:	600,
                            "speedOut"		:	200,
                            "overlayShow"	:	false,
                            "hideOnContentClick": false,
                             "onClosed":    function(){
                                                                       } //onclosed function
                        })//fancybox

                    } //function



		});//ajax

                                                },
	"_class"			: "class",	// class is applied to the item LI node
	"separator_before"	: false,	// Insert a separator before the item
	"separator_after"	: true	// Insert a separator after the item

	},//properties

"remove" : {
	               "label"	: "Delete",
	              "action" : function (obj) {
		       $('<div title="Delete Confirmation">\n\
                     <span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>\n\
                    Categorydemo <span style="color:#FF73B4;font-weight:bold;">'+(obj).attr('rel')+'</span> and all it\'s subcategories will be deleted.Are you sure?</div>')
                       .dialog({
			resizable: false,
			height:170,
			modal: true,
			buttons: {
				       "Delete": function() {
                                        jQuery("#categorydemo_admin_tree").jstree("remove",obj);
					$( this ).dialog( "close" );
				},
				Cancel: function() {
					$( this ).dialog( "close" );
				}
			}
		});

                                                                                     }
},//remove
"create" : {
	"label"	: "Create",
	"action" : function (obj) { this.create(obj); },
        "separator_after": false
	},

//The next two context menu items,add_product and list_products are commented out because they are meaningful only if you have
// a related Product Model (Nested Model HAS MANY Product).


"add_product" : {
	"label"	: "Add Product",
	"action" : function (obj) {
                                   id=obj.attr("id").replace("node_","")
                             $.ajax({
                                    type:"POST",
			            url:"/WorksManagement/product/returnProductForm",
			           data:  {
				         "id" :id,
                                         "YII_CSRF_TOKEN":"3c9c03c61bf7062deedea40a2a2efb7908e621d7"
			            },
                                       beforeSend : function(){
                                           $("#").addClass("ajax-sending");
                                                               },
                                        complete : function(){
                                              $("#").removeClass("ajax-sending");
                                                             },

                      success: function(data){

                        $.fancybox(data,
                        {    "transitionIn"	:	"elastic",
                            "transitionOut"    :      "elastic",
                             "speedIn"		:	600,
                            "speedOut"		:	200,
                            "overlayShow"	:	false,
                            "hideOnContentClick": false,
                             "onClosed":    function(){
                                                                       } //onclosed function
                       })//fancybox

                    } //function

		});//ajax


                                                }
//	"separator_before"	: false,	// Insert a separator before the item
//	"separator_after"	: false	// Insert a separator after the item
	},//add product

   "list_products" : {
	"label"	: "List Products",
	"action" : function (obj) {
                                   id=obj.attr("id").replace("node_","")
                             $.ajax({
                                         type:"POST",
			                 url:"/WorksManagement/product/productList",
			                 data:{
				                   "id" :id,
			                           "YII_CSRF_TOKEN":"3c9c03c61bf7062deedea40a2a2efb7908e621d7"
                                              },
			                beforeSend : function(){
                                               $("#categorydemo_admin_tree").addClass("ajax-sending");
                                                               },
                                        complete : function(){
                                              $("#categorydemo_admin_tree").removeClass("ajax-sending");
                                                             },
                                       success: function(data){
                                        $.fancybox(data,
                            {  "transitionIn"	:	"elastic",
                            "transitionOut"    :      "elastic",
                             "speedIn"		:	600,
                            "speedOut"		:	200,
                            "overlayShow"	:	false,
                            "hideOnContentClick": false,
                             "onClosed":    function(){
                                                                       } //onclosed function
                      })//fancybox

                      } //function

  		});//post

                                                }

                                           }
//	"separator_before"	: false,	// Insert a separator before the item
//	"separator_after"	: false	// Insert a separator after the item
 	},//add product

   "list_products" : {
	"label"	: "List Products",
	"action" : function (obj) {
                                   id=obj.attr("id").replace("node_","")
                             $.ajax({
                                         type:"POST",
			                 url:"baseUrl/product/productList",
			                 data:{
				                   "id" :id,
			                          "YII_CSRF_TOKEN":"3c9c03c61bf7062deedea40a2a2efb7908e621d7"
                                              },
			                beforeSend : function(){
                                               $("#categorydemo_admin_tree").addClass("ajax-sending");
                                                               },
                                        complete : function(){
                                              $("#categorydemo_admin_tree").removeClass("ajax-sending");
                                                             },
                                       success: function(data){
                                        $.fancybox(data,
                            {  "transitionIn"	:	"elastic",
                            "transitionOut"    :      "elastic",
                             "speedIn"		:	600,
                            "speedOut"		:	200,
                            "overlayShow"	:	false,
                            "hideOnContentClick": false,
                             "onClosed":    function(){
                                                                       } //onclosed function
                        })//fancybox

                    } //function

		});//post

                                                }
//	"separator_before"	: false,	// Insert a separator before the item
//	"separator_after"	: true	// Insert a separator after the item
//	}//list products

                  }//items
                  },//context menu

			// the `plugins` array allows you to configure the active plugins on this instance
			"plugins" : ["themes","html_data","contextmenu","crrm","dnd"],
			// each plugin you have included can have its own config object
			"core" : { "initially_open" : [ 'node_95','node_96','node_97','node_98','node_99' ],'open_parents':true}
			// it makes sense to configure a plugin only if overriding the defaults

		})

                ///EVENTS
               .bind("rename.jstree", function (e, data) {
		$.ajax({
                           type:"POST",
			   url:"/WorksManagement/categorydemo/rename",
			   data:  {
				        "id" : data.rslt.obj.attr("id").replace("node_",""),
                                         "new_name" : data.rslt.new_name,
			                 "YII_CSRF_TOKEN":"3c9c03c61bf7062deedea40a2a2efb7908e621d7"
                                       },
                         beforeSend : function(){
                                                     $("#categorydemo_admin_tree").addClass("ajax-sending");
                                                             },
                         complete : function(){
                                                       $("#categorydemo_admin_tree").removeClass("ajax-sending");
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
			    url:"/WorksManagement/categorydemo/remove",
			    data:{
				        "id" : data.rslt.obj.attr("id").replace("node_",""),
			                "YII_CSRF_TOKEN":"3c9c03c61bf7062deedea40a2a2efb7908e621d7"
                                        },
                           beforeSend : function(){
                                                     $("#categorydemo_admin_tree").addClass("ajax-sending");
                                                             },
                          complete: function(){
                                                       $("#categorydemo_admin_tree").removeClass("ajax-sending");
                                                             },
			  success:function (r) {  response= $.parseJSON(r);
				           if(!response.success) {
					                                   $.jstree.rollback(data.rlbk);
				                                            };
			                   }
		});
	})

        .bind("create.jstree", function (e, data) {
                           newname=data.rslt.name;
                           parent_id=data.rslt.parent.attr("id").replace("node_","");
            $.ajax({
                    type: "POST",
                    url: "/WorksManagement/categorydemo/returnForm",
                      data:{   'name': newname,
                                 'parent_id':   parent_id,
                                 "YII_CSRF_TOKEN":"3c9c03c61bf7062deedea40a2a2efb7908e621d7"
                                                          },
                           beforeSend : function(){
                                                     $("#categorydemo_admin_tree").addClass("ajax-sending");
                                                             },
                           complete : function(){
                                                       $("#categorydemo_admin_tree").removeClass("ajax-sending");
                                                             },
                          success: function(data){

                        $.fancybox(data,
                        {    "transitionIn"	:	"elastic",
                            "transitionOut"    :      "elastic",
                             "speedIn"		:	600,
                            "speedOut"		:	200,
                            "overlayShow"	:	false,
                            "hideOnContentClick": false,
                             "onClosed":    function(){
                                                                       } //onclosed function
                        })//fancybox

                    } //success
                });//ajax

	})
.bind("move_node.jstree", function (e, data) {
		data.rslt.o.each(function (i) {

                //jstree provides a whole  bunch of properties for the move_node event
                //not all are needed for this view,but they are there if you need them.
                //Commented out logs  are for debugging and exploration of jstree.

                 next= jQuery.jstree._reference('#categorydemo_admin_tree')._get_next (this, true);
                 previous= jQuery.jstree._reference('#categorydemo_admin_tree')._get_prev(this,true);

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
				url: "/WorksManagement/categorydemo/moveCopy",

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
				         "YII_CSRF_TOKEN":"3c9c03c61bf7062deedea40a2a2efb7908e621d7"
                                                          },
                           beforeSend : function(){
                                                     $("#categorydemo_admin_tree").addClass("ajax-sending");
                                                             },
                          complete : function(){
                                                       $("#categorydemo_admin_tree").removeClass("ajax-sending");
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

//BINDING EVENTS FOR THE ADD ROOT AND REFRESH BUTTONS.
   $("#add_root").click(function () {
	$.ajax({
                      type: 'POST',
	              url:"/WorksManagement/categorydemo/returnForm",
		     data:	{
				    "create_root" : true,
			             "YII_CSRF_TOKEN":"3c9c03c61bf7062deedea40a2a2efb7908e621d7"
                                                          },
                                     beforeSend : function(){
                                                     $("#categorydemo_admin_tree").addClass("ajax-sending");
                                                             },
                                     complete : function(){
                                                       $("#categorydemo_admin_tree").removeClass("ajax-sending");
                                                             },
                                     success:    function(data){

                        $.fancybox(data,
                        {    "transitionIn"	:	"elastic",
                            "transitionOut"    :      "elastic",
                             "speedIn"		:	600,
                            "speedOut"		:	200,
                            "overlayShow"	:	false,
                            "hideOnContentClick": false,
                             "onClosed":    function(){
                                                                       } //onclosed function
                        })//fancybox

                    } //function

		});//post
	});//click function

              $("#reload").click(function () {
		jQuery("#categorydemo_admin_tree").jstree("refresh");
	});
});

</script>

	</div><!-- content -->
</div>
<div class="span-5 last">
	<div id="sidebar">
		</div><!-- sidebar -->
</div>

	<div class="clear"></div>

	<div id="footer">
		Copyright &copy; 2012 by My Company.<br/>
		All Rights Reserved.<br/>
		Powered by <a href="http://www.yiiframework.com/" rel="external">Yii Framework</a>.	</div><!-- footer -->

</div><!-- page -->

<script type="text/javascript">
/*<![CDATA[*/
jQuery(function($) {
jQuery('a[rel="tooltip"]').tooltip();
jQuery('a[rel="popover"]').popover();
});
/*]]>*/
</script>
</body>
</html>
