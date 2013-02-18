<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
?>

<div class="element <?php echo $element->elementType->class_name?> ondemand<?php if (@$ondemand){?> hidden<?php }?>"
	data-element-type-id="<?php echo $element->elementType->id ?>"
	data-element-type-class="<?php echo $element->elementType->class_name ?>"
	data-element-type-name="<?php echo $element->elementType->name ?>"
	data-element-display-order="<?php echo $element->elementType->display_order ?>">
	<h4 class="elementTypeName"><?php echo $element->elementType->name ?></h4>

	<div class="splitElement clearfix" style="background-color: #DAE6F1;">
		<div class="left" style="width:65%;">
			<?php
			$this->widget('application.modules.eyedraw2.OEEyeDrawWidget', array(
				'doodleToolBarArray' => array(
					0 => array('PhakoIncision','SidePort','IrisHook','PCIOL','ACIOL','PI'),
					1 => array('MattressSuture','CapsularTensionRing','CornealSuture','ToricPCIOL','LimbalRelaxingIncision','Rubeosis','PosteriorSynechia'),
				),
				'onReadyCommandArray' => array(
					array('addDoodle', array('AntSeg')),
					array('addDoodle', array('PhakoIncision')),
					array('addDoodle', array('PCIOL')),
					array('deselectDoodles', array()),
				),
				'bindingArray' => array(
					'PhakoIncision' => array(
						'incisionSite' => array('id' => 'ElementCataract_incision_site_id', 'attribute' => 'data-value'),
						'incisionType' => array('id' => 'ElementCataract_incision_type_id', 'attribute' => 'data-value'),
						'incisionLength' => array('id' => 'ElementCataract_length'),
						'incisionMeridian' => array('id' => 'ElementCataract_meridian'),
					),
				),
				'listenerArray' => array(
					'sidePortController'
				),
				'idSuffix' => 'Cataract',
				'side' => $element->getSelectedEye()->getShortName(),
				'mode' => 'edit',
				'width' => 300,
				'height' => 300,
				'model' => $element,
				'attribute' => 'eyedraw',
				'offsetX' => 10,
				'offsetY' => 10,
				'template' => 'OEEyeDrawWidgetCataract',
			))?>
			<?php echo $form->hiddenInput($element, 'report2', '')?>
			<div class="halfHeight">
				<?php echo $form->dropDownList($element, 'incision_site_id', CHtml::listData(IncisionSite::model()->findAll(), 'id', 'name'),array('empty'=>'- Please select -','textAttribute'=>'data-value'))?>
				<?php echo $form->textField($element, 'length', array('size' => '10'))?>
				<?php echo $form->textField($element, 'meridian', array('size' => '10'))?>
				<?php echo $form->dropDownList($element, 'incision_type_id', CHtml::listData(IncisionType::model()->findAll(), 'id', 'name'),array('empty'=>'- Please select -','textAttribute'=>'data-value'))?>
				<?php echo $form->textArea($element, 'report', array('rows'=>6,'cols'=>35))?>
			</div>
			<?php
			$this->widget('application.modules.eyedraw2.OEEyeDrawWidget', array(
				'onReadyCommandArray' => array(
					array('addDoodle', array('OperatingTable')),
					array('addDoodle', array('Surgeon')),
					array('deselectDoodles', array()),
				),
				'syncArray' => array(
					'Cataract' => array('Surgeon' => array('PhakoIncision' => array('parameters' => array('rotation')))),
				),
				'idSuffix' => 'Position',
				'side' => $element->getSelectedEye()->getShortName(),
				'mode' => 'edit',
				'width' => 140,
				'height' => 140,
				'model' => $element,
				'attribute' => 'eyedraw2',
				'offsetX' => 10,
				'offsetY' => 10,
				'toolbar' => false,
				'template' => 'OEEyeDrawWidgetSurgeonPosition',
			))?>
		</div>
		<div class="right" style="width:35%;">
			<div class="halfHeight">
				<?php echo $form->dropDownList($element, 'iol_type_id', array(CHtml::listData($element->IOLTypes_NHS,'id','name'),CHtml::listData($element->IOLTypes_Private,'id','name')),array('empty'=>'- Please select -','divided'=>true))?>
				<?php echo $form->textField($element, 'iol_power', array('size' => '10'))?>
				<?php echo $form->dropDownList($element, 'iol_position_id', CHtml::listData(IOLPosition::model()->findAll(array('order'=>'display_order')), 'id', 'name'),array('empty'=>'- Please select -'))?>
				<?php echo $form->multiSelectList($element, 'CataractOperativeDevices', 'operative_devices', 'operative_device_id', $element->operative_device_list, $element->operative_device_defaults, array('empty' => '- Devices -', 'label' => 'Devices'))?>
				<?php echo $form->multiSelectList($element, 'CataractComplications', 'complications', 'complication_id', CHtml::listData(CataractComplications::model()->findAll(array('order'=>'display_order asc')), 'id', 'name'), array(), array('empty' => '- Complications -', 'label' => 'Complications'))?>
				<?php echo $form->textArea($element, 'complication_notes', array('rows'=>5,'cols'=>35))?>
			</div>
		</div>
	</div>
</div>
