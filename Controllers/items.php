<?

/*
 * This file is part of the akAdmin package.
 * (c) 2010 Azat Khuzhin <dohardgopro@gmail.com>
 *
 * For the full copyright and license information, please view http://www.gnu.org/licenses/gpl-2.0.html
 */

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
		$d->redirect('/default/404');
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
	
	if ($d->getRequestMethod() == 'post') {
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
			$d->redirect('/section/details/' . $sid);
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
		$d->redirect('/default/404');
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
		$d->redirect('/default/404');
	}
	
	if ($d->getRequestMethod() == 'post') {
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
			$d->redirect('/section/details/' . $sid);
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
	global $user, $d, $g, $i, $s, $f;
	
	$sid = (int)$d->getParam('sid');
	$ids = (($d->getRequestMethod() == 'post') ? $_POST['id'] : (int)$d->getParam('id'));
	
	// not all params are set
	if ($sid <= 0 || !$ids) {
		$d->redirect('/default/error');
	}
	
	$sectionInfo = $s->details(array('id' => $sid));
	if (!$sectionInfo) {
		$d->redirect('/default/404');
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
		$fieldTypes[$field['field']] = $field['type'];
	}
	// set fields
	$i->fields = $fields;
	$i->fieldTypes = $fieldTypes;
	
	$result = false;
	try {
		$result = $i->erase($ids);
	} catch (ItemsException $e) {}
	
	if (!$result) {
		$d->set('error', 'Error occured while deleting one of items. Please try again later.');
		return $d->content('default/error.php');
	}
	
	$d->redirect('/section/details/' . $sid);
}

/**
 * Duplicate / copy item [Multi]
 */
function duplicate() {
	global $user, $d, $g, $i, $s, $f;
	
	$sid = (int)$d->getParam('sid');
	$ids = (($d->getRequestMethod() == 'post') ? $_POST['id'] : (int)$d->getParam('id'));
	$num = (int)$d->getParam('num');
	
	// not all params are set
	if ($sid <= 0 || !$ids || $num <= 0) {
		$d->redirect('/default/error');
	}
	
	$sectionInfo = $s->details(array('id' => $sid));
	if (!$sectionInfo) {
		$d->redirect('/default/404');
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
		$fieldTypes[$field['field']] = $field['type'];
	}
	// set fields
	$i->fields = $fields;
	$i->fieldTypes = $fieldTypes;
	
	$result = false;
	try {
		$result = $i->duplicate($ids, $num);
	} catch (ItemsException $e) {
		$d->set('error', sprintf('Error occured while creating %u copy of item with numer %u. Please try again later.', $e->getMessage(), $e->getCode()));
		return $d->content('default/error.php');
	}
	if (!$result) {
		$d->set('error', 'Error occured while creating copies of items. Please try again later.');
		return $d->content('default/error.php');
	}
	
	$d->redirect('/section/details/' . $sid);
}
