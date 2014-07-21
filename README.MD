# JsonDataBehavior

Adds beforeSave and afterFind callbacks to model to json_encode and json_decode data on certain fields.

## Setup 

On every model that has a field that should be encoded you have to configure whitch fields will be Json. 

For example, a User Model that have `settings`:

```
class User extends AppModel {
	
	public $actsAs		= array(
		'JsonData.JsonData' => [
			'fields' 	=> ['settings'],
		],
	);

	public $belongsTo	= array(
		'Group'
	);
}
```

On every associated Model you should also add the behavior without fields (unless it actualy have Json fields). This way, each time this associated model does a find and retrieves associated data, this data will also be decoded.

Example on Group Model:

```
class Group extends AppModel {
	
	public $actsAs		= array(
		'JsonData.JsonData',
	);

	public $hasMany		= array(
		'User',
	);
}
```

This way each time you call `$groups = $this->Group->find('all', array('contain' => array('User'))));`, `$groups[{n}]['User']['settings']` will be an array. 

# Methods
## JsonData::decode(&$data, $modelName = null)

`$data` is an array with data retrieved from a Model::find().  
`$modelName`is the Model::alias. It's optional because the method is recursive and will check every $data to tell if it has a Json field (based on the associated models).

Does not return anything as $data is referenced.