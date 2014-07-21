<?php
class JsonDataBehavior extends ModelBehavior {

	public function setup(Model $model, $settings = array()) {
		if (!isset($this->settings[$model->alias])) {
			$this->settings[$model->alias] = [
				'fields'	=> [],
			];
		}
		$this->settings[$model->alias] = array_merge($this->settings[$model->alias], (array)$settings);

		$this->settings[$model->alias]['fields'] = (array)$this->settings[$model->alias]['fields'];
	}

	public function afterFind(Model $model, $results, $primary = false) {
		$this->decode($results);
		return $results;
	}

	protected function decode(&$data, $modelName = null) {
		if(Hash::numeric(array_keys($data))) {
			foreach($data as &$da) {
				$this->decode($da, $modelName);
			}
			return;
		}

		if($modelName !== null) {
			if(!array_key_exists($modelName, $this->settings)) return;

			foreach($this->settings[$modelName]['fields'] as $field) {
				if(isset($data[$field])) $data[$field] = json_decode($data[$field], true);
			}
		}

		foreach(array_keys($data) as $associatedModelName) {
			if(array_key_exists($associatedModelName, $this->settings)) $this->decode($data[$associatedModelName], $associatedModelName);
		}
	}

	public function beforeSave(Model $model, $options = array()) {
		foreach($this->settings[$model->alias]['fields'] as $field) {
			if(isset($model->data[$model->alias][$field])) $model->data[$model->alias][$field] = json_encode($model->data[$model->alias][$field]);
		}
		return true;
	}
}
