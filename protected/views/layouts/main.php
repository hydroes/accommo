<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	
	<?php
    // load css 
    foreach ( $this->excl_css as $css => $combined )
    {
    	echo '<link rel="stylesheet" type="text/css" href="'.Yii::app()->request->baseUrl.$css.'" />' . "\n\t";
    }
	
	$css_paths = array();
	foreach( $this->css as $css_url => $group )
	{
		$css_paths[] = PATH . $css_url;
		
	}
	
	// load js
	$js_paths = array();
	foreach( $this->js as $js_url => $group )
	{
		$js_paths[] = PATH . $js_url;
	}
    ?>
	<link type="text/css" rel="stylesheet" href="<?php echo Yii::app()->clientScript->minScriptCreateGroup($css_paths); ?>" />
	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
    <link rel="icon" type="image/png" href="<?php echo Yii::app()->request->baseUrl; ?>/images/favicon.ico">
    
    <script type="text/javascript">
    URL = '<?php echo Yii::app()->request->baseUrl; ?>';
    </script>
    
    
</head>

<body>

<div id="page_header">
	<div id="header_content">
    	<div id="site_name" class="float-left"><a href="<?php echo Yii::app()->request->baseUrl; ?>"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/home_icon.png" id="accom_logo" class="float-left" width="128" height="128" />Accommodation Today</a></div>
        <div id="menu_container" class="float-right">
            <ul id="navigation">
            	<li><a class="selected">Home</a></li>
                <li><a>Search</a></li>
                <li><a>Listing Owners</a></li>
                <li><a>Contact Us</a></li>
            </ul>
        </div>
    </div>
</div>

<div class="clear"></div>

<script type="text/javascript" async="async" src="<?php echo Yii::app()->clientScript->minScriptCreateGroup($js_paths); ?>"></script>

<div id="main_container">
	<?php echo $content; ?>
</div>

</body>
</html>