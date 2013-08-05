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

class AdminController extends ModuleAdminController
{
	public function actionViewPostOpDrugs()
	{
		Audit::add('admin','list',null,false,array('module'=>'OphTrOperationnote','model'=>'PostopDrug'));

		$this->render('postopdrugs');
	}

	public function actionCreatePostOpDrug()
	{
		if (empty($_POST['name'])) {
			throw new Exception("Missing name");
		}

		if ($drug = OphTrOperationnote_PostopDrug::model()->find(array('order'=>'display_order desc'))) {
			$display_order = $drug->display_order+1;
		} else {
			$display_order = 1;
		}

		$drug = new OphTrOperationnote_PostopDrug;
		$drug->name = @$_POST['name'];
		$drug->display_order = $display_order;

		if (!$drug->save()) {
			echo json_encode(array('errors'=>$drug->getErrors()));
			return;
		}

		// TODO: this is a hack for the Orbis demo and should be removed when full site/subspecialty functionality has been implemented
		$specialty = Specialty::model()->find('code=?',array(130));
		foreach (Site::model()->findAll('institution_id=?',array(1)) as $site) {
			foreach (Subspecialty::model()->findAll('specialty_id=?',array($specialty->id)) as $subspecialty) {
				$ssd = new OphTrOperationnote_PostopSiteSubspecialtyDrug;
				$ssd->site_id = $site->id;
				$ssd->subspecialty_id = $subspecialty->id;
				$ssd->drug_id = $drug->id;
				if (!$ssd->save()) {
					echo json_encode(array('errors'=>$ssd->getErrors()));
				}
			}
		}

		Audit::add('admin','create',serialize($_POST),false,array('module'=>'OphTrOperationnote','model'=>'PostopDrug'));

		echo json_encode(array('id'=>$drug->id,'errors'=>array()));
	}

	public function actionUpdatePostOpDrug()
	{
		if (!$drug = OphTrOperationnote_PostopDrug::model()->findByPk(@$_POST['id'])) {
			throw new Exception("Drug not found: ".@$_POST['id']);
		}

		$drug->name = @$_POST['name'];
		if (!$drug->save()) {
			echo json_encode(array('errors'=>$drug->getErrors()));
			return;
		}

		Audit::add('admin','update',serialize($_POST),false,array('module'=>'OphTrOperationnote','model'=>'PostopDrug'));

		echo json_encode(array('errors'=>array()));
	}

	public function actionDeletePostOpDrug($id)
	{
		if ($drug = OphTrOperationnote_PostopDrug::model()->findByPk($id)) {
			$drug->deleted = 1;
			if ($drug->save()) {
				Audit::add('admin','delete',$id,false,array('module'=>'OphTrOperationnote','model'=>'PostopDrug'));
				echo "1";
				return;
			}
		}
		echo "0";
	}

	public function actionSortPostOpDrugs()
	{
		if (!empty($_POST['order'])) {
			foreach ($_POST['order'] as $i => $id) {
				if ($drug = OphTrOperationnote_PostopDrug::model()->findByPk($id)) {
					$drug->display_order = $i+1;
					if (!$drug->save()) {
						throw new Exception("Unable to save drug: ".print_r($drug->getErrors(),true));
					}
				}
			}
		}
	}
}