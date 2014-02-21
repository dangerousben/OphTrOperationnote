<h1 class="badge">Reports</h1>
<div class="row">
	<div class="large-11 small-11 small-centered large-centered column">
		<div class="panel">
			<h2>Operation Report</h2>
			<form>
				<div class="row field-row">
					<div class="large-2 column">
						<?php echo CHtml::label('Surgeon', 'surgeon_id') ?>
					</div>
					<div class="large-4 column end">
						<?php echo CHtml::dropDownList('surgeon_id', '', $surgeons) ?>
					</div>
				</div>
				<div class="row field-row">
					<div class="large-2 column">
						<?php echo CHtml::label('Date From', 'date_from') ?>
					</div>
					<div class="large-4 column end">
						<?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
								'name'=>'date_from',
								'id'=>'date_from',
								'options'=>array(
									'showAnim'=>'fold',
									'dateFormat'=>Helper::NHS_DATE_FORMAT_JS,
									'maxDate'=> 0,
									'defaultDate' => "-1y"
								),
								'value'=>@$_GET['date_from']
							))?>
					</div>
				</div>
				<div class="row field-row">
					<div class="large-2 column">
						<?php echo CHtml::label('Date To', 'date_to') ?>
					</div>
					<div class="large-4 column end">
						<?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
								'name'=>'date_to',
								'id'=>'date_to',
								'options'=>array(
									'showAnim'=>'fold',
									'dateFormat'=>Helper::NHS_DATE_FORMAT_JS,
									'maxDate'=> 0,
									'defaultDate' => 0
								),
								'value'=>@$_GET['date_to']
							))?>
					</div>
				</div>
				<div class="row field-row">
					<div class="large-2 column">
						&nbsp;
					</div>
					<div class="large-4 column end">
						<?php echo CHtml::submitButton('Generate Report') ?>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>