<!doctype html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title><?php echo CHtml::encode($this->pageTitle); ?></title>

<?php
// Suppress Yii's autoload of JQuery
// We're loading it at bottom of page (best practice)
// from Google's CDN with fallback to local version 


// TODO: this here because sometimes swapping to an autotext in jquery so not there to begin with
// tidy up so included only in those views
// This shifted here from main view file due to ajax requests returning script files to automatically bind new
// elements - with ajax request may not get to main view include


		// Load Yii's generated javascript at bottom of page
		// instead of the 'head', ensuring it loads after JQuery
//		Yii::app()->getClientScript()->coreScriptPosition = CClientScript::POS_END;
	?>
	<meta name="description" content="">
	<meta name="author" content="">
	<meta name="viewport" content="width=device-width">
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/style.css" />
	<link rel="icon" href="<?php echo Yii::app()->baseUrl; ?>/favicon.ico" type="image/x-icon">
	<link rel="shortcut icon" href="<?php echo Yii::app()->baseUrl; ?>/favicon.ico" type="image/x-icon">

</head>
<body>

<div class="container">
	<div class="row">
		<header class="span12">

			<?php $this->navbar() ?>
			
			<!-- breadcrumbs -->
			<?php if(isset($this->breadcrumbs)):?>
				<?php $this->widget('WMTbBreadcrumbs', array(
					'links'=>$this->breadcrumbs,
				)); ?>
			<?php endif?>

			<!-- tabs -->
			<?php
			$tabs = $this->tabs;
			if($tabs)
			{
				// tabs is multi-dimensional i.e. multiple layers
				foreach($tabs as $tabMenu)
				{
					$this->widget('WMTbTabs', array(
						'type'=>'tabs', // '', 'tabs', 'pills' (or 'list')
						'stacked'=>false, // whether this is a stacked menu
						'tabs'=>$tabMenu,
					));
				}
			}
			?>
				
			<?php
			if(!empty($this->heading))
			{
				echo '<h2>'.CHtml::encode($this->heading).'</h2>';
			}
			?>
		</header>
	</div>
	
	<div class="row">
		<div class="span12">
			<?php echo $content; ?>
		</div>
	</div>

</div>
	
</body>
</html>