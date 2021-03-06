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

<section class="element">
	<h3 class="element-title highlight"><?php echo $element->elementType->name ?></h3>
	<div class="element-data">
		<div class="row">
			<div class="large-6 column">
				<div class="row data-row">
					<div class="large-4 column">
						<div class="data-label">
							<?php echo CHtml::encode($element->getAttributeLabel('gauge_id'))?>
						</div>
					</div>
					<div class="large-8 column">
						<div class="data-value">
							<?php echo $element->gauge->value?>
						</div>
					</div>
				</div>
				<div class="row data-row">
					<div class="large-4 column">
						<div class="data-label">
							<?php echo CHtml::encode($element->getAttributeLabel('pvd_induced'))?>
						</div>
					</div>
					<div class="large-8 column">
						<div class="data-value">
							<?php echo $element->pvd_induced ? 'Yes' : 'No'; ?>
						</div>
					</div>
				</div>
				<div class="row data-row">
					<div class="large-4 column">
						<div class="data-label">
							<?php echo CHtml::encode($element->getAttributeLabel('comments'))?>
						</div>
					</div>
					<div class="large-8 column">
						<div class="data-value">
							<?php echo Yii::app()->format->Ntext($element->comments)?>
						</div>
					</div>
				</div>
			</div>
			<div class="large-6 column">
				<?php
					$this->widget('application.modules.eyedraw.OEEyeDrawWidget', array(
					'side'=>$element->eye->getShortName(),
					'mode'=>'view',
					'width'=>200,
					'height'=>200,
					'model'=>$element,
					'attribute'=>'eyedraw',
					));
				?>
			</div>
		</div>
	</div>
</section>
