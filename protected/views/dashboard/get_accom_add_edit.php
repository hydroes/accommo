  <div id="accommodation_wizard_carousel" class="carousel slide">
	<!-- Carousel items -->
	<div class="carousel-inner">
	  <div class="active item">
              <?php $form = $this->beginWidget('CActiveForm', array('action' => $this->createUrl('dashboard/OpAddAccomDetails'), 'id' => 'accommodation_details', 'htmlOptions' => array('class' => 'form-horizontal well'))); ?>
                <fieldset>
            	<a class="btn" id="accommodation_save_details">Next&nbsp;<i class="icon-arrow-right"></i></a>
            	<h2>Accommodation details</h2>&nbsp;
                
                <input type="hidden" value="<?php echo $op; ?>" id="add_edit_op" />
                
                <div class="control-group">
					<?php echo $form->label($accommodationModel, 'accommodation_name', array('class' => 'control-label')); ?>
					<div class="controls">
						<?php echo $form->textField($accommodationModel, 'accommodation_name', array('class' => 'input-xlarge')) ?>
					</div>
				</div>
                
                <div class="control-group">
					<?php echo $form->label($accommodationModel, 'accommodation_description', array('class' => 'control-label')); ?>
					<div class="controls">
						<?php echo $form->textArea($accommodationModel, 'accommodation_description', array('class' => 'input-xlarge')) ?>
					</div>
				</div>
				
				<div class="control-group">
					<?php
                    $accom_types = array('none'=>'Please select a type', 'Backpacker'=>'Backpacker', 'Bed and Breakfast'=>'Bed and Breakfast', 'Boutique Hotel'=>'Boutique Hotel', 'Camping and Caravanning'=>'Camping and Caravanning', 'Country House'=>'Country House', 'Guest House'=>'Guest House', 'Hotel'=>'Hotel', 'Houseboat'=>'Houseboat', 'Lodge'=>'Lodge', 'Mobile Camp'=>'Mobile Camp','Private Home'=>'Private Home', 'Resort'=>'Resort', 'Safari Lodge'=>'Safari Lodge', 'Self-catering'=>'Self-catering', 'Tented Camp'=> 'Tented Camp');
                    ?>
                    <?php echo $form->label($accommodationModel, 'accommodation_type', array('class' => 'control-label')); ?>
					<div class="controls">
						<?php echo $form->dropDownList($accommodationModel, 'accommodation_type', $accom_types, array('class' => 'input-xlarge')); ?>
					</div>
				</div>

                <?php echo $form->hiddenField($accommodationModel, 'accommodation_id', array('class' => 'accommodation_id')); ?>
                </fieldset>
                <?php $this->endWidget(); ?>
	  </div>
	  <div class="item">
		<?php $form = $this->beginWidget('CActiveForm', array('action' => $this->createUrl('dashboard/OpAddAccomLocationDetails'), 'id' => 'accommodation_location', 'htmlOptions' => array('class' => 'form-horizontal well'))); ?>
		<fieldset>

		<a class="btn previous-wizard"><i class="icon-arrow-left"></i>&nbsp;Previous</a>
		<a class="btn" id="accommodation_save_location">Next&nbsp;<i class="icon-arrow-right"></i></a>
		<h2>Location</h2>

		<input name="Accommodation_accommodation_address" id="Accommodation_accommodation_address" type="text" class="input-xlarge ignore-enter" /><br /><br />

		<?php echo $form->hiddenField($accommodationModel, 'accommodation_lat'); ?>
		<?php echo $form->hiddenField($accommodationModel, 'accommodation_lng'); ?>
		<?php echo $form->hiddenField($accommodationModel, 'accommodation_zoom'); ?>
		<div id="map_data"></div>

		<div id="map_error" class="hide">Please place a marker on the map by clicking the mouse over your location.</div>
		<div id="map_canvas" class="float-left"></div>
		<div class="clear"></div>
		</fieldset>
		<?php $this->endWidget(); ?>
	  </div>
	  <div class="item">
			<?php $form = $this->beginWidget('CActiveForm', array('action' => $this->createUrl('dashboard/OpAddAccommFeatures'), 'id' => 'accommodation_features', 'htmlOptions' => array('class' => 'form-horizontal well'))); ?>
			<fieldset>
			<a class="btn previous-wizard"><i class="icon-arrow-left"></i>&nbsp;Previous</a>
			<a class="btn" id="accommodation_save_features">Next&nbsp;<i class="icon-arrow-right"></i></a>
			<h2>What features do you have?</h2>

			<?php		
			foreach( $features as $feature ):
			?>
				 <div class="control-group">
					<label class="control-label" for="features_<?php echo $feature["feature_id"]; ?>"><?php echo $feature['feature_name'] ?></label>
					<div class="controls">
					  <label class="checkbox">
						<?php 
						$checked = ( isset($assigned_features[$feature['feature_id']]) === true ) ? true : false;
						echo CHtml::checkBox('features['.$feature["feature_id"].']', $checked); 
						?>
						<!-- Option one is this and that—be sure to include why it's great -->
					  </label>
					</div>
				  </div>
			<?php
			endforeach;
			?>
			</fieldset>
			<?php $this->endWidget(); ?>
	  </div>
	  <div class="item">
		<?php $form = $this->beginWidget('CActiveForm', array('id' => 'accommodation_images', 'htmlOptions' => array('class' => 'form-horizontal well'))); ?>
		<fieldset>
		<a class="btn previous-wizard"><i class="icon-arrow-left"></i>&nbsp;Previous</a>
		<a class="btn" href="<?php echo $this->createUrl('dashboard/GetAccomOverview') ?>" id="accommodation_save_images"><i class="icon-ok-sign"></i>&nbsp;Finish</a>
		<h2>Upload images</h2>
		<div id="uploaded_images">
			<?php
			$uploaded_images = 0;
			foreach( $images as $image ):

				if ( ($uploaded_images % 3) === 0 ):
				?>
				<div class="clear">&nbsp;</div>
				<?php
				endif;
				?>
				<div class="listing_preview_container">
					<a href="<?php echo $this->createUrl('dashboard/OpDeleteImages', array('ai_id' => $image['ai_id'])) ?>" class="delete_image_btn show">&nbsp;</a>
					<img src="<?php echo URL; ?>/listing_images/<?php echo $image['ai_thumb_name']; ?>" name="image_<?php echo $uploaded_images;?>" alt="image" class="listing_preview" width="<?php echo $image['ai_thumb_width']; ?>" height="<?php echo $image['ai_thumb_height']; ?>" />
				</div>
				<?php
					$uploaded_images++;
			endforeach;
			?>

			<?php
			// output empty image holders
			for ( $uploaded_images; $uploaded_images <= 5; $uploaded_images++ ):
			?>
			<?php
				if ( ($uploaded_images % 3) === 0 ):
			?>
			<div class="clear">&nbsp;</div>
			<?php
				endif;
			?>
			<div class="listing_preview_container empty">
				<a href="#" class="delete_image_btn hide">&nbsp;</a>
			</div>
			<?php
			endfor;
			?>

		</div>

			<div class="clear">&nbsp;</div>
			<div id="uploading_animation">Uploading images, please be patient...<br /><img src="<?php echo URL; ?>/images/layout/ajax-loader.gif" alt="Uploading" width="43" height="11" /></div>
				<input name="accommodation_image_upload_url" id="accommodation_image_upload_url" type="hidden" value="<?php echo $this->createAbsoluteUrl('dashboard/OpAddImages'); ?>" />
				<input name="ms" id="ms" type="hidden" value="<?php echo SID; ?>" />
				<div id="uploader">
					<p>A problem occurred with our the uploader. please <a href="mailto:photos@accommodationtoday.co.za">email your photos</a> to us and we will upload them for you.</p>
				</div>
				<div class="clear">&nbsp;</div>
			</div>
			</fieldset>
			<?php $this->endWidget(); ?>
	  </div>
	  <div class="item">
		<?php if ( $op == 'add' ): ?>
		<div class="hero-unit well add_rooms">
		  <h1>Congratulations</h1>
		  <p>You have successfully added your establishment. Whats next you ask? Add rooms to your establishment.<br />
		  Press the button below to begin adding rooms.
		  </p>
		  <p>
			<a class="btn btn-primary btn-large add_rooms_assist">
			  Add rooms
			</a>
		  </p>
		</div>
		<?php else: ?>
		<div class="alert alert-success">
		  <h4 class="alert-heading">Success!</h4>
		  You have successfully edited your establishment.<br />
		</div>
		<?php endif; ?>
	  </div>
	</div>
</div>

<script>

jQuery(document).ready(function()
{
    $('#accommodation_wizard_carousel').carousel({
		interval: false
	});

	Accommodation.Uploader.initialize();

	// if the accom is being edited then center map on its location
  <?php if( empty($accommodationModel->accommodation_lat) === false
    && empty($accommodationModel->accommodation_lng) === false ): ?>
  var locationData =
  {
    zoom: parseInt(<?php echo $accommodationModel->accommodation_zoom; ?>),
    center: null,
    lat: parseFloat(<?php echo $accommodationModel->accommodation_lat; ?>),
    lng: parseFloat(<?php echo $accommodationModel->accommodation_lng; ?>)
  };
  Accommodation.Map.initialize(locationData);
  <?php else: ?>
	Accommodation.Map.initialize();
  <?php endif; ?>

});

</script>