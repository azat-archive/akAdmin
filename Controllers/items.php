<?

/**
 * Items controller
 * 
 * @author Azat Khuzhin <dohardgopro@gmail.com>
 * @package akAdmin
 * @licence GPLv2
 */

// create an instace of items
$GLOBALS['i'] = Items::getInstance();
// create an instace of sections
$GLOBALS['s'] = Sections::getInstance();
// create an instace of fields
$GLOBALS['f'] = Fields::getInstance();

/**
 * Add item
 */
function add($sid) {
	global $user, $d, $g, $i, $s, $f;
	
	$sid = (int)$sid;
	
	$sectionInfo = $s->details(array('id' => $sid));
	if (!$sectionInfo) {
		redirect('/default/404');
	}
	
	if (!$s->isTableExists($sectionInfo['tableName'])) {
		$d->set('error', sprintf('Table %s not exist', $sectionInfo['tableName']));
		return $d->content('default/error.php');
	}
	
	if (!$user['isAdmin'] && !$g->check($user['id'], $sid, 'insert')) {
		$d->set('error', sprintf('Your don`t have permission to add items to section %s.', $sectionInfo['title']));
		return $d->content('default/error.php');
	}
	
	$i->table = $sectionInfo['tableName'];
	$i->fieldAutoIncrement = $s->getTableAutoIncrementField($i->table);
	
	// get avaliable fields (that set up in Settings page of section-table)
	$allAvaliableFields = $f->getTranslationForSection($sid);
	// no fields is set
	if (!$allAvaliableFields) {
		$d->set('error', sprintf('No fields is avaliable. Please set it up in <a href="/section/settings/%u">settings</a>.', $sid));
		return $d->content('default/error.php');
	}
	
	// add auto_increment field to the begining of array
	$fields = array($i->fieldAutoIncrement => $i->fieldAutoIncrement);
	// type of fields, classes begining from FieldType
	$fieldTypes = array();
	foreach ($allAvaliableFields as $field) {
		$fields[$field['field']] = ($field['value'] ? $field['value'] : '&nbsp;');
		$fieldTypes[$field['field']] = $field['type'];
	}
	$i->fields = $fields;
	$i->fieldTypes = $fieldTypes;
	
	if (mb_strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
		// delete not existed fields
		foreach ($_POST as $key => &$value) {
			if (!in_array($key, array_keys($fields))) {
				unset($_POST[$key]);
			} else {
				if (isset($_FILES[$key])) {
					$value['file'] = &$_FILES[$key];
				}
			}
		}
		
		if ($id = $i->add($_POST)) {
			redirect('/section/details/' . $sid);
		}
		$d->set('error', 'Error occured while adding a new item.');
	}
	// build tree of parent sections
	$d->set('sectionTree', $s->buildTree($sectionInfo));
	$d->set('fieldAutoIncrement', $i->fieldAutoIncrement);
	$d->set('title', sprintf('Add new item to section %s', $sectionInfo['title']));
	$d->set('item', $i->getEmpty());
	
	return $d->content('items/form.php');
}

/**
 * Edit item
 */
function edit($sid, $id) {
	global $user, $d, $g, $i, $s, $f;
	
	$sid = (int)$sid;
	$id = (int)$id;
	$sectionInfo = $s->details(array('id' => $sid));
	if (!$sectionInfo) {
		redirect('/default/404');
	}
	
	if (!$s->isTableExists($sectionInfo['tableName'])) {
		$d->set('error', sprintf('Table %s not exist', $sectionInfo['tableName']));
		return $d->content('default/error.php');
	}
	
	if (!$user['isAdmin'] && !$g->check($user['id'], $sid, 'update')) {
		$d->set('error', sprintf('Your don`t have permission to update items in section %s.', $sectionInfo['title']));
		return $d->content('default/error.php');
	}
	
	$i->table = $sectionInfo['tableName'];
	$i->fieldAutoIncrement = $s->getTableAutoIncrementField($i->table);
	
	// no auto increment field
	if (!$i->fieldAutoIncrement) {
		$d->set('error', sprintf('Your don`t have auto_increment field in table %s.', $i->table));
		return $d->content('default/error.php');
	}
	
	// get avaliable fields (that set up in Settings page of section-table)
	$allAvaliableFields = $f->getTranslationForSection($sid);
	// no fields is set
	if (!$allAvaliableFields) {
		$d->set('error', sprintf('No fields is avaliable. Please set it up in <a href="/section/settings/%u">settings</a>.', $sid));
		return $d->content('default/error.php');
	}
	
	// add auto_increment field to the begining of array
	$fields = array($i->fieldAutoIncrement => $i->fieldAutoIncrement);
	// type of fields, classes begining from FieldType
	$fieldTypes = array();
	foreach ($allAvaliableFields as $field) {
		$fields[$field['field']] = ($field['value'] ? $field['value'] : '&nbsp;');
		$fieldTypes[$field['field']] = $field['type'];
	}
	// set fields
	$i->fields = $fields;
	$i->fieldTypes = $fieldTypes;
	
	// get items details
	$item = $i->details($id, true);
	if (!$item) {
		redirect('/default/404');
	}
	
	if (mb_strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
		// delete not existed fields
		foreach ($_POST as $key => &$value) {
			if (!in_array($key, array_keys($fields))) {
				unset($_POST[$key]);
			} else {
				if (isset($_FILES[$key])) {
					$value['file'] = &$_FILES[$key];
				}
			}
		}
		
		if ($i->edit($_POST, $id)) {
			redirect('/section/details/' . $sid);
		}
		$d->set('error', 'Error occured while adding a new item.');
	}
	// build tree of parent sections
	$d->set('sectionTree', $s->buildTree($sectionInfo));
	$d->set('item', $item);
	$d->set('title', sprintf('Edit item in section %s', $sectionInfo['title']));
	$d->set('fieldAutoIncrement', $i->fieldAutoIncrement);
	$d->set('fields', $fields);
	
	return $d->content('items/form.php');
}

/**
 * Erase / delete item [Multi]
 */
function erase() {
	global $user, $d, $g, $i, $s;
	
	$sid = (int)$d->getParam('sid');
	$ids = ((mb_strtolower($_SERVER['REQUEST_METHOD']) == 'post') ? $_POST['id'] : (int)$d->getParam('id'));
	
	// not all params are set
	if ($sid <= 0 || !$ids) {
		redirect('/default/error');
	}
	
	$sectionInfo = $s->details(array('id' => $sid));
	if (!$sectionInfo) {
		redirect('/default/404');
	}
	
	if (!$s->isTableExists($sectionInfo['tableName'])) {
		$d->set('error', sprintf('Table %s not exist', $sectionInfo['tableName']));
		return $d->content('default/error.php');
	}
	
	if (!$user['isAdmin'] && !$g->check($user['id'], $sid, 'delete')) {
		$d->set('error', sprintf('Your don`t have permission to delete from section %s.', $sectionInfo['title']));
		return $d->content('default/error.php');
	}
	
	// set items model properties
	$i->table = $sectionInfo['tableName'];
	$i->fieldAutoIncrement = $s->getTableAutoIncrementField($i->table);
	
	// no auto increment field
	if (!$i->fieldAutoIncrement) {
		$d->set('error', sprintf('Your don`t have auto_increment field in table %s.', $i->table));
		return $d->content('default/error.php');
	}
	
	if (!$i->erase($ids)) {
		$d->set('error', 'Error occured while deleting one of items. Please try again later.');
		return $d->content('default/error.php');
	}
	
	redirect('/section/details/' . $sid);
}

/**
 * Duplicate / copy item [Multi]
 */
function duplicate() {
	global $user, $d, $g, $i, $s, $f;
	
	$sid = (int)$d->getParam('sid');
	$ids = ((mb_strtolower($_SERVER['REQUEST_METHOD']) == 'post') ? $_POST['id'] : (int)$d->getParam('id'));
	$num = (int)$d->getParam('num');
	
	// not all params are set
	if ($sid <= 0 || !$ids || $num <= 0) {
		redirect('/default/error');
	}
	
	$sectionInfo = $s->details(array('id' => $sid));
	if (!$sectionInfo) {
		redirect('/default/404');
	}
	
	if (!$s->isTableExists($sectionInfo['tableName'])) {
		$d->set('error', sprintf('Table %s not exist', $sectionInfo['tableName']));
		return $d->content('default/error.php');
	}
	
	if (!$user['isAdmin'] && !$g->check($user['id'], $sid, array('insert', 'select'))) {
		$d->set('error', sprintf('Your don`t have permission to add to section %s or select from it.', $sectionInfo['title']));
		return $d->content('default/error.php');
	}
	
	// set items model properties
	$i->table = $sectionInfo['tableName'];
	$i->fieldAutoIncrement = $s->getTableAutoIncrementField($i->table);
	
	// no auto increment field
	if (!$i->fieldAutoIncrement) {
		$d->set('error', sprintf('Your don`t have auto_increment field in table %s.', $i->table));
		return $d->content('default/error.php');
	}
	
	// get avaliable fields (that set up in Settings page of section-table)
	$allAvaliableFields = $f->getTranslationForSection($sid);
	// no fields is set
	if (!$allAvaliableFields) {
		$d->set('error', sprintf('No fields is avaliable. Please set it up in <a href="/section/settings/%u">settings</a>.', $sid));
		return $d->content('default/error.php');
	}
	
	// add auto_increment field to the begining of array
	$fields = array($i->fieldAutoIncrement => $i->fieldAutoIncrement);
	foreach ($allAvaliableFields as $field) {
		$fields[$field['field']] = $field['value'];
	}
	// set fields
	$i->fields = $fields;
	
	// run by all items need to copy
	if (!is_array($ids)) $ids = array($ids);
	foreach ($ids as $id) {
		// get items details
		$item = $i->details($id);
		if (!$item) {
			$d->set('error', sprintf('Error item width number %u is not exist. Please try again later.', $id));
			return $d->content('default/error.php');
		}
		
		// delete auto increment field, it nums update by DB server
		unset($item[$i->fieldAutoIncrement]);
		
		// creating copies/ duplicate
		for ($inc = 1; $inc <= $num; $inc++) {
			// if in one of copy error is occured
			if (!($i->add($item))) {
				$d->set(
					'error',
					sprintf(
						'Error occured while creating %u copy of item width numer %u. Please try again later.',
						$inc, $id
					)
				);
				return $d->content('default/error.php');
			}
		}
	}
	
	redirect('/section/details/' . $sid);
}
