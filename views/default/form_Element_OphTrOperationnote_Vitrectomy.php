<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
?>

<section class="sub-element <?php echo $element->elementType->class_name?> on-demand<?php if (@$ondemand) {?> hidden<?php }?><?php if ($this->action->id == 'update' && !$element->event_id) {?> missing<?php }?>"
		 data-element-type-id="<?php echo $element->elementType->id ?>"
		 data-element-type-class="<?php echo $element->elementType->class_name ?>"
		 data-element-type-name="<?php echo $element->elementType->name ?>"
		 data-element-display-order="<?php echo $element->elementType->display_order ?>">
	<?php if ($this->action->id == 'update' && !$element->event_id) {?>
		<div class="alert-box alert with-icon">This element is missing and needs to be completed</div>
	<?php }?>

	<header class="sub-element-header">
		<h4 class="sub-element-title"><?php  echo $element->elementType->name; ?></h4>
	</header>

	<?php
	$layoutColumns=$form->layoutColumns;
	$form->layoutColumns=array('label'=>3,'field'=>9);
	?>
	<div class="element-fields">
		<div class="row eyedraw-row vitrectomy">
			<div class="fixed column">
				<?php
				$this->widget('application.modules.eyedraw.OEEyeDrawWidget', array(
					'doodleToolBarArray' => array(
						0 => array('RRD','UTear','RoundHole','Dialysis','GRT','MacularHole','StarFold','AntPVR','Cryo','LaserCircle'),
					),
					'onReadyCommandArray' => array(
						array('addDoodle', array('Fundus')),
						array('deselectDoodles', array()),
					),
					'side'=>$this->selectedEyeForEyedraw->shortName,
					'mode'=>'edit',
					'width'=>300,
					'height'=>300,
					'model'=>$element,
					'attribute'=>'eyedraw',
					'offsetX' => 10,
					'offsetY' => 10,
				));
				?>
			</div>
			<div class="fluid column">
				<?php echo $form->dropDownList($element, 'gauge_id', CHtml::listData(OphTrOperationnote_VitrectomyGauge::model()->findAll(),'id','value'),array('empty'=>'- Please select -'),false,array('field'=>3))?>
				<?php echo $form->radioBoolean($element, 'pvd_induced',array())?>
				<?php echo $form->textArea($element, 'comments', array('rows' => 4))?>
			</div>
		</div>
	</div>
</section>

<?php $form->layoutColumns=$layoutColumns;?>
