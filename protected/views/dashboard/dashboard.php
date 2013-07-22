<?php 
$count = 0;
?>
<div id="tabs">
	<ul>
		<li><a href="#overview">Overview</a></li>

		<?php foreach ($accommodation as $accommo): ?>
		<li><a href="<?php echo $this->createUrl('dashboard/getAccomOverview', array('accommodation_id' => $accommo['accommodation_id']));?>"><span><?php echo $accommo['accommodation_name']; ?></span></a>
		
		<?php
		 endforeach;?>
		<li><a id="overview_add_est_wiz" href="#add_establishment_wizard" data-url="<?php echo $this->createUrl('dashboard/getAccomAddEdit'); ?>" data-load_into="#add_establishment_wizard">Add Establishment</a></li>
	</ul>
	<div id="overview">
        <div>
        	Not sure what to put here yet..... <br />
			Probably put over all stats and booking notifications etc.
        </div>
	</div>
	<div id="add_establishment_wizard"></div>
</div>
