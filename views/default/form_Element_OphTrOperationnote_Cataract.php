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
		<div class="alert-box alert">This element is missing and needs to be completed</div>
	<?php }?>
	<header class="sub-element-header">
		<h4 class="sub-element-title"><?php  echo $element->elementType->name; ?></h4>
	</header>

	<?php
	$layoutColumns=$form->layoutColumns;
	$form->layoutColumns=array('label'=>3,'field'=>9);
	?>
	<div class="element-fields">
		<div class="row cataract">
			<div class="fixed column">
				<?php
					$this->widget('application.modules.OphTrOperationnote.widgets.OEEyeDrawWidgetCataract', array(
							'doodleToolBarArray' => array(
								0 => array('PhakoIncision','SidePort','IrisHook','PCIOL','ACIOL','PI'),
								1 => array('MattressSuture','CapsularTensionRing','CornealSuture','ToricPCIOL','LimbalRelaxingIncision'),
							),
							'onReadyCommandArray' => array(
								array('addDoodle', array('AntSeg')),
								array('addDoodle', array('PhakoIncision')),
								array('addDoodle', array('PCIOL')),
								array('deselectDoodles', array()),
							),
							'bindingArray' => array(
								'PhakoIncision' => array(
									'incisionSite' => array('id' => 'Element_OphTrOperationnote_Cataract_incision_site_id', 'attribute' => 'data-value'),
									'incisionType' => array('id' => 'Element_OphTrOperationnote_Cataract_incision_type_id', 'attribute' => 'data-value'),
									'incisionLength' => array('id' => 'Element_OphTrOperationnote_Cataract_length'),
									'incisionMeridian' => array('id' => 'Element_OphTrOperationnote_Cataract_meridian'),
								),
							),
							'listenerArray' => array(
								'sidePortController'
							),
							'idSuffix' => 'Cataract',
							'side' => $this->selectedEyeForEyedraw->shortName,
							'mode' => 'edit',
							'width' => 300,
							'height' => 300,
							'model' => $element,
							'attribute' => 'eyedraw',
							'offsetX' => 10,
							'offsetY' => 10,
							'template' => 'OEEyeDrawWidgetCataract',
						))?>
					<?php echo $form->hiddenInput($element, 'report2', $element->report2)?>
					<?php
							$this->widget('application.modules.OphTrOperationnote.widgets.OEEyeDrawWidgetCataract', array(
								'onReadyCommandArray' => array(
									array('addDoodle', array('OperatingTable')),
									array('addDoodle', array('Surgeon')),
									array('deselectDoodles', array()),
								),
								'syncArray' => array(
									'Cataract' => array('Surgeon' => array('PhakoIncision' => array('parameters' => array('rotation')))),
								),
								'idSuffix' => 'Position',
								'side' => $this->selectedEyeForEyedraw->shortName,
								'mode' => 'edit',
								'width' => 140,
								'height' => 140,
								'model' => $element,
								'attribute' => 'eyedraw2',
								'offsetX' => 10,
								'offsetY' => 10,
								'toolbar' => false,
								'template' => 'OEEyeDrawWidgetSurgeonPosition',
							))
						?>
			</div>
			<div class="fluid column">
				<div class="row">
					<div class="large-12 column">
						<?php echo $form->dropDownList($element, 'incision_site_id', CHtml::listData(OphTrOperationnote_IncisionSite::model()->findAll(), 'id', 'name'),array('empty'=>'- Please select -','textAttribute'=>'data-value'),false,array('field'=>4))?>
						<?php echo $form->textField($element, 'length', array(),array(),array_merge($form->layoutColumns,array('field'=>2)))?>
						<?php echo $form->textField($element, 'meridian', array(),array(),array_merge($form->layoutColumns,array('field'=>2)))?>
						<?php echo $form->dropDownList($element, 'incision_type_id', CHtml::listData(OphTrOperationnote_IncisionType::model()->findAll(), 'id', 'name'),array('empty'=>'- Please select -','textAttribute'=>'data-value'),false,array('field'=>4))?>
						<?php echo $form->textArea($element, 'report',array(),false,array('rows'=>6))?>
						<?php echo $form->dropDownList($element, 'iol_type_id', array(CHtml::listData($element->OphTrOperationnote_IOLTypes_NHS,'id','name'),CHtml::listData($element->OphTrOperationnote_IOLTypes_Private,'id','name')),array('empty'=>'- Please select -','divided'=>true),$element->iol_hidden,array('field'=>4))?>
						<?php echo $form->textField($element, 'predicted_refraction',array(),array(),array_merge($form->layoutColumns,array('field'=>2)))?>
						<?php echo $form->textField($element, 'iol_power', array('hide' => $element->iol_hidden),array(),array_merge($form->layoutColumns,array('field'=>2)))?>
						<?php echo $form->dropDownList($element, 'iol_position_id', CHtml::listData(OphTrOperationnote_IOLPosition::model()->findAll(array('order'=>'display_order')), 'id', 'name'),array('empty'=>'- Please select -'),$element->iol_hidden,array('field'=>4))?>
						<?php echo $form->multiSelectList($element, 'OphTrOperationnote_CataractOperativeDevices', 'operative_devices', 'id', $this->getOperativeDeviceList($element), $this->getOperativeDeviceDefaults(), array('empty' => '- Devices -', 'label' => 'Devices'),false,false,null,false,false,array('field'=>4))?>
						<?php echo $form->multiSelectList($element, 'OphTrOperationnote_CataractComplications', 'complications', 'id', CHtml::listData(OphTrOperationnote_CataractComplications::model()->findAll(array('order'=>'display_order asc')), 'id', 'name'), null, array('empty' => '- Complications -', 'label' => 'Complications'),false,false,null,false,false,array('field'=>4))?>
						<?php echo $form->textArea($element, 'complication_notes',array(),false,array('rows'=>6))?>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<?php $form->layoutColumns=$layoutColumns;?>
