<?php

class DefaultController extends BaseEventTypeController {
	public function actionCreate() {
		parent::actionCreate();
	}

	public function actionUpdate($id) {
		parent::actionUpdate($id);
	}

	public function actionView($id) {
		parent::actionView($id);
	}

	public function actionPrint($id) {
		return parent::actionPrint($id);
	}

	public function getDefaultElements($action, $event_type_id=false, $event=false) {
		$elements = parent::getDefaultElements($action, $event_type_id, $event);

		// If we're loading the create form and there are procedures pulled from the booking which map to elements
		// then we need to include them in the form
		if ($action == 'create' && empty($_POST)) {
			$proclist = new ElementProcedureList;
			$extra_elements = array();

			$new_elements = array(array_shift($elements));

			foreach ($proclist->selected_procedures as $procedure) {
				$criteria = new CDbCriteria;
				$criteria->compare('procedure_id',$procedure->id);
				$criteria->order = 'display_order asc';

				foreach (ProcedureListOperationElement::model()->findAll($criteria) as $element) {
					$element = new $element->element_type->class_name;

					if (!in_array(get_class($element),$extra_elements)) {
						$extra_elements[] = get_class($element);
						$new_elements[] = $element;
					}
				}
			}

			$elements = array_merge($new_elements, $elements);
		}

		/* If an opnote was saved with a procedure in the procedure list but the associated element wasn't saved, include it here */
		if ($action == 'update' && empty($_POST)) {
			$extra_elements = array();
			$new_elements = array(array_shift($elements));

			foreach (ElementProcedureList::model()->find('event_id = :event_id',array(':event_id' => $this->event->id))->selected_procedures as $procedure) {
				$criteria = new CDbCriteria;
				$criteria->compare('procedure_id',$procedure->id);
				$criteria->order = 'display_order asc';

				foreach (ProcedureListOperationElement::model()->findAll($criteria) as $element) {
					$class = $element->element_type->class_name;
					$element = new $element->element_type->class_name;

					if (!$class::model()->find('event_id=?',array($this->event->id))) {
						$extra_elements[] = get_class($element);
						$new_elements[] = $element;
					}
				}
			}

			$elements = array_merge($new_elements, $elements);
		}

		// Procedure list elements need to be shown in the order they were selected, not the default sort order from the element_type
		// TODO: This probably needs replacing with a some better code
			
		// Get correct order for procedure elements
		if($this->event) {
			$procedure_list = ElementProcedureList::model()->find(
					'event_id = :event_id',
					array(':event_id' => $this->event->id)
			);
			$procedure_classes = array();
			foreach($procedure_list->procedure_assignments as $procedure_assignment) {
				$procedure_classes[] = ProcedureListOperationElement::model()->find('procedure_id = ?', array($procedure_assignment->proc_id))->element_type->class_name;
			}
			
			// Resort procedure elements
			// This code assumes that the elements are grouped into three distinct blocks, with the procedures in the middle
			$sorted_elements = array();
			$index = 0;
			$section = 'top';
			foreach($elements as $element) {
				if(in_array(get_class($element), $procedure_classes)) {
					$section = 'procedure';
					$index = 1000 + array_search(get_class($element), $procedure_classes);
				} else if($section == 'procedure') {
					$section = 'bottom';
					$index = 2000;
				} else {
					$index++;
				}
				$sorted_elements[$index] = $element;
			}
			ksort($sorted_elements);
			$elements = $sorted_elements;
		}

		return $elements;
	}

	public function actionLoadElementByProcedure() {
		if (!$proc = Procedure::model()->findByPk((integer)@$_GET['procedure_id'])) {
			throw new SystemException('Procedure not found: '.@$_GET['procedure_id']);
		}

		$form = new BaseEventTypeCActiveForm;

		foreach ($this->getProcedureSpecificElements($proc->id) as $element) {
			$class_name = $element->element_type->class_name;

			$element = new $class_name;

			if (in_array($class_name,array('ElementCataract','ElementVitrectomy','ElementBuckle'))) {
				if (!in_array(@$_GET['eye'],array(1,2))) {
					echo "must-select-eye";
					return;
				}
			}

			$this->renderPartial(
				'create' . '_' . $element->create_view,
				array('element' => $element, 'data' => array(), 'form' => $form, 'ondemand' => true),
				false, true
			);
		}
	}

	public function actionGetElementsToDelete() {
		if (!$proc = Procedure::model()->findByPk((integer)@$_POST['procedure_id'])) {
			throw new SystemException('Procedure not found: '.@$_POST['procedure_id']);
		}

		$procedures = @$_POST['remaining_procedures'] ? explode(',',$_POST['remaining_procedures']) : array();

		$elements = array();

		foreach ($this->getProcedureSpecificElements($proc->id) as $element) {
			if (empty($procedures) || !ProcedureListOperationElement::model()->find('procedure_id in ('.implode(',',$procedures).') and element_type_id = '.$element->element_type->id)) {
				$elements[] = $element->element_type->class_name;
			}
		}

		die(json_encode($elements));
	}

	public function getProcedureSpecificElements($procedure_id) {
		$criteria = new CDbCriteria;
		$criteria->compare('procedure_id',$procedure_id);
		$criteria->order = 'display_order asc';

		return ProcedureListOperationElement::model()->findAll($criteria);
	}
	
	public function getAllProcedureElements($action){
		$elements = $this->getDefaultElements($action);
		$current_procedure_elements = array();
		
		foreach($elements as $element){
			$element_type = ElementType::model()->find('class_name = ?', array(get_class($element)));
			$procedure_elements = ProcedureListOperationElement::model()->find('element_type_id = ?', array($element_type->id));
			if($procedure_elements){
				$current_procedure_elements[] = $element;
			}
		}
		
		return $current_procedure_elements;
	}
	
	public function renderAllProcedureElements($action, $form=false, $data=false) {
		$elements = $this->getAllProcedureElements($action);
		$count = count($elements);
		$i = 0;
		$last = false;
		foreach ($elements as $element) {
			if($count == ($i + 1)){
				$last = true;
			}
			$this->renderPartial(
				$action . '_' . $element->{$action.'_view'},
				array('element' => $element, 'data' => $data, 'form' => $form, 'last' => $last),
				false, false
			);
			$i++;
		}
	}

	public function actionGetIOLType() {
		$iol = OphTrOperationnoteCataractIOL::model()->find('barcode = :barcode', array(':barcode' => @$_GET['barcode']));
		if($iol) {
			echo CJavaScript::jsonEncode(array(
					'type_id' => $iol->type_id,
					'power' => $iol->power,
					'type' => $iol->type->name
			));
		} else {
			throw new CHttpException(404, 'Unknown IOL');
		}
	}
	
	public function actionVerifyprocedure() {
		$list = Yii::app()->session['Procedures'];
		$found = false;

		if (!isset($_GET['short_version'])) {
			$_GET['short_version'] = true;
		}

		if (!empty($_GET['name'])) {
			if (!empty($list)) {
				foreach ($list as $id => $procedure) {
					if ($procedure['term'] == $_GET['name']) {
						if ($this->procedure_requires_eye($id)) {
							echo "no";
						} else {
							echo "yes";
						}
						return;
					}
				}
			}

			// if not in the session, check in the db
			if (!$found) {
				$procedure = Yii::app()->db->createCommand()
					->select('*')
					->from('proc')
					->where('term=:term', array(':term'=>$_GET['name']))
					->queryRow();
				if (!empty($procedure)) {
					if ($this->procedure_requires_eye($procedure['id'])) {
						echo "no";
					} else {
						echo "yes";
					}
					return;
				}
			}
		} else {
			$i = 0;
			$result = true;
			$procs = array();
			while (isset($_GET['proc'.$i])) {
				if ($this->procedure_requires_eye($_GET['proc'.$i])) {
					$result = false;
					$procs[] = Procedure::model()->findByPk($_GET['proc'.$i])->term;
				}
				$i++;
			}
			if ($result) {
				echo "yes";
			} else {
				echo implode("\n",$procs);
			}
		}
	}

	// returns true if the passed procedure id requires the selection of 'left' or 'right' eye
	function procedure_requires_eye($procedure_id) {
		foreach (ProcedureListOperationElement::model()->findAll('procedure_id=?',array($procedure_id)) as $plpa) {
			$element_type = ElementType::model()->findByPk($plpa->element_type_id);

			if (in_array($element_type->class_name,array('ElementCataract','ElementBuckle','ElementVitrectomy'))) {
				return true;
			}
		}

		return false;
	}
}
