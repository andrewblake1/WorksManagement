<!doctype html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<!-- Nb: Andrew Blake shifted this line from bottom to here as although meant to be faster to load scripts at bottom
			nested set admin gui extension needs it up here for some reason -->
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>

		<link rel="stylesheet" type="text/css" href="/WorksManagement/assets/779c334c/css/bootstrap.css" />
		<link rel="stylesheet" type="text/css" href="/WorksManagement/assets/779c334c/css/datepicker.css" />
		<link rel="stylesheet" type="text/css" href="/WorksManagement/assets/779c334c/css/bootstrap-yii.css" />
		<link rel="stylesheet" type="text/css" href="/WorksManagement/assets/c9795045/jui/css/base/jquery-ui.css" />
		<script type="text/javascript" src="/WorksManagement/assets/779c334c/js/bootstrap-datepicker.js"></script>
		<script type="text/javascript" src="/WorksManagement/assets/6b000ff5/tiny_mce/tiny_mce_gzip.js"></script>
		<script type="text/javascript" src="/WorksManagement/assets/6b000ff5/jquery/jquery.tinymce.js"></script>
		<script type="text/javascript" src="/WorksManagement/assets/6b000ff5/embedmedia/embed.js"></script>
		<script type="text/javascript">
			/*<![CDATA[*/
			tinyMCE_GZ.init({'plugins':'','themes':'simple','languages':'en','disk_cache':true,'debug':false});
			/*]]>*/
		</script>
		<title>Works Management - Admin Report</title>
		<meta name="description" content="">
		<meta name="author" content="">

		<meta name="viewport" content="width=device-width">

		<link rel="stylesheet" type="text/css" href="/WorksManagement/themes/base/css/bootstrap-and-responsive.min.css" />
		<link rel="stylesheet" type="text/css" href="/WorksManagement/themes/base/css/style.css" />
		<script src="/WorksManagement/themes/base/js/libs/modernizr-2.5.3-respond-1.1.0.min.js"></script>
	</head>
	<body>

		<div class="container">
			<div class="row">
				<header class="span12">

					<div class="navbar"><div class="navbar-inner"><div class="container"><a class="btn btn-navbar" data-toggle="collapse" data-target="#collapse_0"><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span></a><a href="#" class="brand">Works Management</a><div class="nav-collapse" id="collapse_0" /><ul id="yw2" class="nav"><li><a href="http://localhost/phpMyAdmin">Database</a></li></ul><ul class="pull-right nav" id="yw3"><li><a href="/WorksManagement/site/logout">Logout (username)</a></li></ul></div></div></div></div>			
			<!-- breadcrumbs -->
			<ul class="breadcrumbs breadcrumb"><li><a href="/WorksManagement/">Home</a><span class="divider">/</span></li><li class="active">Reports</li></ul>			

			<!-- tabs -->
			<ul id="yw4" class="nav nav-tabs"><li><a href="/WorksManagement/Client/admin">Clients</a></li><li><a href="/WorksManagement/AuthItem/admin">Roles</a></li><li><a href="/WorksManagement/DefaultValue/admin">Defaults</a></li><li><a href="/WorksManagement/Dutycategory/admin">Dutycategorys</a></li><li><a href="/WorksManagement/GenericType/admin">Custom types</a></li><li><a href="/WorksManagement/Genericprojectcategory/admin">Project categorys</a></li><li><a href="/WorksManagement/Generictaskcategory/admin">Task categorys</a></li><li class="active"><a href="/WorksManagement/Report/admin">Reports</a></li><li><a href="/WorksManagement/Resourcecategory/admin">Resource categorys</a></li><li><a href="/WorksManagement/Staff/admin">Staffs</a></li><li><a href="/WorksManagement/Supplier/admin">Suppliers</a></li></ul>				
			<h2>Reports <a class="btn btn-primary btn-small" href="/WorksManagement/Report/admin?action=download">Download Excel</a> <a data-toggle="modal" onclick="$(&quot;#myModal input:not([class=&quot;hasDatepicker&quot;]):visible:enabled:first&quot;).focus();" class="btn btn-primary btn-small" href="#myModal">New</a></h2>		</header>
	</div>

	<div class="row">
		<div class="span12">
			<div id="yw0"><div class="alert in alert-block fade alert-info"><a class="close" data-dismiss="alert">&times;</a><p><strong>To sort,</strong> click on column name.
					<p><strong>To search,</strong> enter part of any term and click elsewhere.
						/ in a column heading means you can search the different parts by seperating with /.</div></div><div id="Report-grid" class="grid-view">

				<table class="items table table-striped">
					<thead>
						<tr>
							<th id="Report-grid_c0"><a class="sort-link" href="/WorksManagement/report/admin?Report_sort=description">Description<span class="caret"></span></a></th><th class="button-column" id="Report-grid_c1">&nbsp;</th></tr>
						<tr class="filters">
							<td><div class="filter-container"><input name="Report[description]" type="text" maxlength="255" /></div></td><td>&nbsp;</td></tr>
					</thead>
					<tbody>
						<tr><td colspan="2"><span class="empty">No results found.</span></td></tr>
					</tbody>
				</table>
				<div class="keys" style="display:none" title="/WorksManagement/Report/admin"></div>
			</div>
			<div id="myModal" class="modal fade">
				<div id="form-create" class="modal-body">
					<div class="modal-header">
						<a class="close" data-dismiss="modal">&times;</a>
						<h3>Report</h3></div><div class="alert alert-block alert-error" id="Report-form_es_" style="display:none">
							<p>Please fix the following input errors:</p>
						<ul>
							<li>dummy</li>
						</ul>
					</div>
					<form class="well form-vertical" id="Report-form" action="Create" method="post">
						<label for="Report_description" class="required">Description <span class="required">*</span></label>
						<input class="span5" name="Report[description]" id="Report_description" type="text" maxlength="255" />
						<span class="help-block error" id="Report_description_em_" style="display: none"></span>
						<div>
							<textarea id="template_html" style="width:100%;height:400px;" cols="40" rows="10" name="template_html"></textarea>
							<div>
								<span>
									<a id="template_html_switch" style="color:navy;font-family:sans-serif;font-size:8pt;background-color:#F0F0EE;border-style:solid;border-width:0px 1px 1px 1px;border-color:#CCCCCC;text-decoration:none;padding:1px 3px 3px 3px;margin:2px 0 0 0;" href="javascript:%24%28%22%23template_html%22%29.toggleModeTinyMCE%28%5B%27Text%20mode%27%2C%27HTML%20mode%27%5D%29">Text mode</a>
								</span>
							</div>
						</div>
						<label for="Report_context">Context</label>
						<input name="Report[context]" id="Report_context" type="hidden" />
						<input id="Report_context_save" type="hidden" value="" name="Report_context_save" />
						<input class="ui-autocomplete-input" autocomplete="off" role="textbox" aria-autocomplete="list" aria-haspopup="true" size="50" maxLength="50" onblur="$(this).val($(&#039;#Report_context_save&#039;).val());" id="Report_context_lookup" name="Report_context_lookup" value="" type="text" maxlength="64" />
						<span class="help-block error" id="Report_context_em_" style="display: none"></span>
						<input name="Report[staff_id]" id="Report_staff_id" type="hidden" />
						<div class="modal-footer">
							<input class="form-button btn btn-primary btn-large" type="submit" name="yt0" value="Create" />
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

</div><!-- container -->
<script>window.jQuery || document.write('<script src="/WorksManagement/themes/base/js/libs/jquery-1.7.2.min.js"><\/script>')</script>
<!-- container -->
<script src="/WorksManagement/themes/base/js/libs/bootstrap/bootstrap.min.js"></script>

<script src="/WorksManagement/themes/base/js/script.js"></script>
<script type="text/javascript" src="/WorksManagement/assets/c9795045/jquery.ba-bbq.js"></script>
<script type="text/javascript" src="/WorksManagement/assets/c9795045/jquery.yiiactiveform.js"></script>
<script type="text/javascript" src="/WorksManagement/assets/86d502a4/gridview/jquery.yiigridview.js"></script>
<script type="text/javascript" src="/WorksManagement/assets/c9795045/jui/js/jquery-ui.min.js"></script>
<script type="text/javascript">
	/*<![CDATA[*/
	jQuery(function($) {
		jQuery('a[rel="tooltip"]').tooltip();
		jQuery('a[rel="popover"]').popover();
		jQuery('#yw0 .alert').alert();
		jQuery('#Report-grid a.delete').live('click',function() {
			if(!confirm('Are you sure you want to delete this item?')) return false;
			var th=this;
			var afterDelete=function(link,success,data) {
				if(success)
				{
					$("#yw0").html(data);
				}
			};
			$.fn.yiiGridView.update('Report-grid', {
				type:'POST',
				url:$(this).attr('href'),
				success:function(data) {
					$.fn.yiiGridView.update('Report-grid');
					afterDelete(th,true,data);
				},
				error:function(XHR) {
					return afterDelete(th,false,XHR);
				}
			});
			return false;
		});
		jQuery('#Report-grid').yiiGridView({'ajaxUpdate':['Report-grid'],'ajaxVar':'ajax','pagerClass':'pagination','loadingClass':'grid-view-loading','filterClass':'filters','tableClass':'items table table-striped','selectableRows':1,'pageVar':'Report_page','afterAjaxUpdate':function() {
				jQuery('.popover').remove();
				jQuery('a[rel="popover"]').popover();
				jQuery('.tooltip').remove();
				jQuery('a[rel="tooltip"]').tooltip();
			}});
		jQuery('#Report_context_lookup').autocomplete({'minLength':1,'maxHeight':'100','select':function(event, ui){$('#Report_context').val(ui.item.id);$('#Report_context_save').val(ui.item.value);},'source':'/WorksManagement/AuthItem/autocomplete?model=Report&attribute=context'});
		$('#Report-form').yiiactiveform({'validateOnSubmit':true,'validateOnChange':false,'afterValidate': function(form, data, hasError)
			{
				// If adding/editing multiple models as a result of what appears visually to be a single model form
				// then their are errors returned in the json data object but hasError is false as it hasnt detected errors matching inputs
				// on the form as they dont exist. This function puts those erros into the error block at the top and stops the form being submitted
				var $lis = "";

				// if afterValidate is being told there are no errors what it really means is no form inputs have errors
				if(!hasError)
				{
			
					// loop thru json object which is 2 dimensional array
					$.each(data, function()
					{
						$.each(this, function(k, v)
						{
							$lis = $lis + "<li>" + v + "</li>";
						});
					});

					// if there are errors with the models but not on the form inputs
					if($lis != "")
					{
						$errorhtml = '<div id="-form_es_" class="alert alert-block alert-error" style="">\
					<p>Please fix the following input errors:</p><ul>' + $lis + '</ul></div>';
					
											$("[id*=-form_es_]").replaceWith($errorhtml);
					
											return false;
										}
									}

									return true;
								},'attributes':[{'id':'Report_description','inputID':'Report_description','errorID':'Report_description_em_','model':'Report','name':'Report[description]','enableAjaxValidation':true,'inputContainer':'div.control-group'},{'id':'Report_context','inputID':'Report_context','errorID':'Report_context_em_','model':'Report','name':'Report[context]','enableAjaxValidation':true,'inputContainer':'div.control-group'}],'summaryID':'Report-form_es_','focus':'input:not([class=\"hasDatepicker\"]):visible:enabled:first'});
							jQuery('#myModal').modal({'show':false});
							jQuery('#collapse_0').collapse({'parent':false,'toggle':false});
						});
						jQuery(window).load(function() {
							jQuery("#template_html").tinymce({'mode':'exact','elements':'template_html','language':'en','readonly':false,'relative_urls':false,'remove_script_host':false,'convert_fonts_to_spans':true,'fullscreen_new_window':true,'media_use_script':true,'theme':'simple'}, 'html', true);
						});
						/*]]>*/
</script>
</body>
</html>