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

/**
 * This is the model class for table "et_ophtroperationnote_postop_drugs".
 *
 * The followings are the available columns in table 'et_ophtroperationnote_postop_drugs':
 * @property integer $id
 * @property integer $event_id
 *
 * The followings are the available model relations:
 * @property Event $event
 * @property OperationDrug[] $drugs
 */
class ElementPostOpDrugs extends BaseEventTypeElement
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return ElementPostOpDrugs the static model class
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'et_ophtroperationnote_postop_drugs';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('event_id', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
		);
	}
	
	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
			'drugs' => array(self::HAS_MANY, 'OperationDrug', 'et_ophtroperationnote_postop_drugs_id'),
			'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
			'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id, true);
		$criteria->compare('event_id', $this->event_id, true);
		
		return new CActiveDataProvider(get_class($this), array(
				'criteria' => $criteria,
			));
	}

	/**
	 * Set default values for forms on create
	 */
	public function setDefaultOptions()
	{
	}

	/**
	 * Need to delete associated records
	 * @see CActiveRecord::beforeDelete()
	 */
	protected function beforeDelete() {
		OperationDrug::model()->deleteAllByAttributes(array('et_ophtroperationnote_postop_drugs_id' => $this->id));
		return parent::beforeDelete();
	}
	
	protected function beforeSave()
	{
		return parent::beforeSave();
	}

	protected function afterSave()
	{
		$existing_drug_ids = array();

		foreach (OperationDrug::model()->findAll('et_ophtroperationnote_postop_drugs_id = :drugsId', array(':drugsId' => $this->id)) as $od) {
			$existing_drug_ids[] = $od->drug_id;
		}

		if (isset($_POST['Drug'])) {
			foreach ($_POST['Drug'] as $id) {
				if (!in_array($id,$existing_drug_ids)) {
					$drug = new OperationDrug;
					$drug->et_ophtroperationnote_postop_drugs_id = $this->id;
					$drug->drug_id = $id;

					if (!$drug->save()) {
						throw new Exception('Unable to save drug: '.print_r($drug->getErrors(),true));
					}
				}
			}
		}

		foreach ($existing_drug_ids as $id) {
			if (!isset($_POST['Drug']) || !in_array($id,$_POST['Drug'])) {
				$od = OperationDrug::model()->find('et_ophtroperationnote_postop_drugs_id = :drugsId and drug_id = :drugId',array(':drugsId' => $this->id, ':drugId' => $id));
				if (!$od->delete()) {
					throw new Exception('Unable to delete drug: '.print_r($od->getErrors(),true));
				}
			}
		}

		return parent::afterSave();
	}

	protected function beforeValidate()
	{
		return parent::beforeValidate();
	}

	public function getDrug_list() {
		return $this->getDrugsBySiteAndSubspecialty();
	}

	public function getDrugsBySiteAndSubspecialty($default=false) {
		$firm = Firm::model()->findByPk(Yii::app()->session['selected_firm_id']);
		$subspecialty_id = $firm->serviceSubspecialtyAssignment->subspecialty_id;
		$site_id = Yii::app()->request->cookies['site_id']->value;

		$params = array(':subSpecialtyId'=>$subspecialty_id,':siteId'=>$site_id);

		if ($default) {
			$where = ' AND et_ophtroperationnote_postop_site_subspecialty_drug.default = :default ';
			$params[':default'] = 1;
		}

		return CHtml::listData(Yii::app()->db->createCommand()
			->select('et_ophtroperationnote_postop_drug.id, et_ophtroperationnote_postop_drug.name')
			->from('et_ophtroperationnote_postop_drug')
			->join('et_ophtroperationnote_postop_site_subspecialty_drug','et_ophtroperationnote_postop_site_subspecialty_drug.drug_id = et_ophtroperationnote_postop_drug.id')
			->where('et_ophtroperationnote_postop_site_subspecialty_drug.subspecialty_id = :subSpecialtyId and et_ophtroperationnote_postop_site_subspecialty_drug.site_id = :siteId'.@$where, $params)
			->order('et_ophtroperationnote_postop_drug.name asc')
			->queryAll(), 'id', 'name');
	}

	public function getDrug_defaults() {
		$ids = array();
		foreach ($this->getDrugsBySiteAndSubspecialty(true) as $id => $drug) {
			$ids[] = $id;
		}
		return $ids;
	}

	public function wrap() {
		return parent::wrap(array(
			'OperationDrug' => 'et_ophtroperationnote_postop_drugs_id',
		));
	}
}
