<?

/*
 * This file is part of the akAdmin package.
 * (c) 2010 Azat Khuzhin <dohardgopro@gmail.com>
 *
 * For the full copyright and license information, please view http://www.gnu.org/licenses/gpl-2.0.html
 */

/**
 * Sections controller
 * 
 * @author Azat Khuzhin <dohardgopro@gmail.com>
 * @package akAdmin
 * @licence GPLv2
 */

// create an instace of sections
$GLOBALS['s'] = Sections::getInstance();
// create an instace of fields
$GLOBALS['f'] = Fields::getInstance();

/**
 * Show all sections [AJAX]
 */
function ajaxAll() {
	global $d, $s, $l;
	
	$q = urldecode($d->getParam('q'));
	$excludeId = (int)$d->getParam('excludeId');
	
	// do not write log at the end
	$l->registerAsShutdown = false;
	
	$d->set('data', $s->simpleList(array('title LIKE' => $q. '%', 'tableName' => '', 'id !=' => $excludeId), 0, 20));
	return $d->content('default/ajaxJSON.php');
}

/**
 * Show all sections
 */
function all() {
	global $d, $s;
	
	$d->set('headTitle', 'Sections');
	$d->set('sections', $s->all(array('pid' => 0)));
	return $d->content('sections/details.php');
}

/**
 * Add a sections
 */
function add() {
	global $user, $d, $s, $g;
	
	if (mb_strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
		if ($_POST['title']) $_POST['title'] = strip_tags($_POST['title']);
		if ($_POST['description']) $_POST['description'] = strip_tags($_POST['description']);
		if ($_POST['tableName']) $_POST['tableName'] = strip_tags($_POST['tableName']);
		if ($_POST['pid']) $_POST['pid'] = (int)$_POST['pid'];
		
		if (!$_POST['title']) {
			$d->set('error', 'Please fill field "Title"');
		} elseif ($_POST['pid'] > 0 && (!$user['isAdmin'] && !$g->check($user['id'], $_POST['pid'], 'insert'))) {
			$d->set('error', sprintf('Your don`t have permission to add to section %s.', $_POST['parentTitle']));
			return $d->content('default/error.php');
		} else {
			$sectionId = $s->add(array('title' => $_POST['title'], 'description' => $_POST['description'], 'uid' => $user['id'], 'pid' => $_POST['pid'], 'tableName' => $_POST['tableName']));
			
			// if all ok while adding a new section
			if ($sectionId) {
				if (!$g->update($user['id'], $sectionId)) {
					$d->set('error', 'Error occured while adding grants for a new section.');
				} else {
					$d->redirect('/section/details/' . $sectionId);
				}
			} else {
				$d->set('error', 'Error occured while adding a new section. Please try again later.');
			}
		}
	}
	
	$d->set('title', 'Add new section');
	return $d->content('sections/add.php');
}

/**
 * Edit a section
 */
function edit($sid) {
	global $user, $d, $s, $g;
	
	$sid = (int)$sid;
	// no such section
	$sectionInfo = $s->details(array('id' => $sid));
	if (!$sectionInfo) $d->redirect('/default/404');
	
	if ($sectionInfo['pid']) {
		$parentInfo = $s->simpleList(array('id' => $sectionInfo['pid']), 0, 1);
		$sectionInfo['parentTitle'] = $parentInfo['title'];
	}
	
	// check grants
	if (!$user['isAdmin'] && !$g->check($user['id'], $sid, 'alter')) {
		$d->set('error', sprintf('Your don`t have permission to %s.', $sectionInfo['title']));
		return $d->content('default/error.php');
	}
	
	if (mb_strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
		if ($_POST['title']) $_POST['title'] = strip_tags($_POST['title']);
		if ($_POST['description']) $_POST['description'] = strip_tags($_POST['description']);
		if ($_POST['tableName']) $_POST['tableName'] = strip_tags($_POST['tableName']);
		if ($_POST['pid']) $_POST['pid'] = (int)$_POST['pid'];
		
		if (!$_POST['title']) {
			$d->set('error', 'Please fill field "Title"');
		} elseif ($_POST['pid'] > 0 && (!$user['isAdmin'] && !$g->check($user['id'], $_POST['pid'], 'insert'))) {
			$d->set('error', sprintf('Your don`t have permission to add to section %s.', $_POST['parentTitle']));
			return $d->content('default/error.php');
		} elseif ($s->edit(array('title' => $_POST['title'], 'description' => $_POST['description'], 'pid' => $_POST['pid'], 'tableName' => $_POST['tableName']), $sid)) {
			$d->redirect('/section/details/' . $sid);
		} else {
			$d->set('error', sprintf('Error occured while editing section %s. Please try again later.', $sectionInfo['title']));
		}
	}
	
	// build tree of parent sections
	$d->set('sectionTree', $s->buildTree($sectionInfo));
	$d->set('title', 'Edit section ' . $sectionInfo['title']);
	$d->set('section', $sectionInfo);
	return $d->content('sections/edit.php');
}

/**
 * Erase / delete section
 */
function erase($sid) {
	global $d, $s, $user, $g;
	
	$sid = (int)$sid;
	// no such section
	$sectionInfo = $s->details(array('id' => $sid));
	if (!$sectionInfo) $d->redirect('/default/404');
	
	// check grants
	if (!$user['isAdmin'] && !$g->check($user['id'], $sid, 'drop')) {
		$d->set('error', sprintf('Your don`t have permission to %s.', $sectionInfo['title']));
		return $d->content('default/error.php');
	}
	
	$haveAny = $s->simpleList(array('pid' => $sid), 0, 1);
	if ($haveAny) {
		$d->set('error', sprintf('Section %s have some sub sections or tables. Delete is first.', $sectionInfo['title']));
		return $d->content('default/error.php');
	}
	
	// error occured
	if (!$s->erase($sid)) {
		$d->set('error', sprintf('Error occured while deleting section %s. Please try again later.', $sectionInfo['title']));
		return $d->content('default/error.php');
	}
	// success delete, redirect to parent section if it exists, or to list of sections
	if ($sectionInfo['pid']) $d->redirect('/section/details/' . $sectionInfo['pid']);
	else $d->redirect('/sections');
}

/**
 * Details of section
 */
function details($sid) {
	global $d, $s, $g, $user;
	
	$sid = (int)$sid;
	// current selected section
	$currentSection = $s->details(array('id' => $sid), 0, 1);
	if (!$currentSection) $d->redirect('/default/404');
	
	// check grants
	if (!$user['isAdmin'] && !$g->check($user['id'], $sid, 'select')) {
		$d->set('error', sprintf('Your don`t have permission to %s.', $currentSection['title']));
		return $d->content('default/error.php');
	}
	
	$d->set('headTitle', sprintf('Section %s', $currentSection['title']));
	// build tree of parent sections
	$d->set('sectionTree', $s->buildTree($currentSection));
	
	$d->set('currentSection', $currentSection);
	if (!$currentSection['tableName']) {
		$d->set('sections', $s->all(array('pid' => $sid)));
		return $d->content('sections/details.php');
	} else {
		return detailsTable($currentSection);
	}
}

/**
 * Details of section-table
 */
function detailsTable(array &$info) {
	global $d, $s, $g, $user, $f, $i;
	
	if (!$s->isTableExists($info['tableName'])) {
		$d->set('error', sprintf('Table %s not exist', $info['tableName']));
		return $d->content('default/error.php');
	}
	
	// get auto_increment field from table
	$fieldAutoIncrement = $s->getTableAutoIncrementField($info['tableName']);
	if (!$fieldAutoIncrement) {
		$d->set('error', 'In this table no auto_increment field, Such actions like "Edit" or "Delete" or "Copy" is not avaliable!');
	}
	$d->set('fieldAutoIncrement', $fieldAutoIncrement);
	
	// paginator
	$page = (int)$d->getParam('page');
	if ($page <= 0) $page = 1;
	$offset = 0;
	$limit = itemsPerPage;
	// if page is selected
	if ($page) {
		$offset = ($limit * ($page - 1));
	}
	// sorting
	$sort = ($d->getParam('sort') ? urldecode($d->getParam('sort')) : null);
	$d->set('sort', $sort);
	// search
	$searchQuery = urldecode($d->getParam('q'));
	$d->set('searchQuery', $searchQuery);
	
	// get avaliable fields (that set up in Settings page of section-table)
	$allAvaliableFields = $f->getTranslationForSection($d->getParam('sid'));
	// no fields is set
	if (!$allAvaliableFields) {
		$d->set('error', sprintf('No fields is avaliable. Please set it up in <a href="/section/settings/%u">settings</a>.', $d->getParam('sid')));
		return $d->content('default/error.php');
	}
	
	// add auto_increment field to the begining of array
	$fields = array($fieldAutoIncrement => $fieldAutoIncrement);
	// type of fields, classes begining from FieldType
	$fieldTypes = array();
	foreach ($allAvaliableFields as $field) {
		$fields[$field['field']] = $field['value'];
		$fieldTypes[$field['field']] = $field['type'];
	}
	// instance of Items
	$i = Items::getInstance($info['tableName'], $fieldAutoIncrement, $fields, $fieldTypes);
	// num of results
	$resultsNum = $i->getNum($searchQuery);
	// paginator
	$d->set('pages', ceil($resultsNum / $limit));
	$d->set('page', $page);
	
	$d->set('fields', $fields);
	$d->set('items', $i->get($offset, $limit, $sort, $searchQuery));
	
	return $d->content('sections/detailsTable.php');
}

/**
 * Settings of table
 */
function settings($sid) {
	global $d, $s, $g, $user, $f, $i;
	
	$sid = (int)$sid;
	// current selected section
	$currentSection = $s->details(array('id' => $sid), 0, 1);
	if (!$currentSection) $d->redirect('/default/404');
	
	// check grants
	if (!$user['isAdmin'] && !$g->check($user['id'], $sid, 'alter')) {
		$d->set('error', sprintf('Your don`t have permission to %s.', $currentSection['title']));
		return $d->content('default/error.php');
	}
	
	if (!$currentSection['tableName'] || !$s->isTableExists($currentSection['tableName'])) {
		$d->set('error', sprintf('Table %s not exist', $currentSection['tableName']));
		return $d->content('default/error.php');
	}
	
	// get fields list for table
	$fieldsFromTable = $s->getTableFields($currentSection['tableName'], true);
	// get avaliable fields
	$allAvaliableFields = $f->getTranslationForSection($sid, true);
	// join fields from table (DESCRIBE table query) & avaliable fields with translation (that set up in Settings page of section-table)
	$fields = array();
	foreach ($fieldsFromTable as &$field) {
		// standart
		$fields[$field['Field']] = array(
			'value' => $field['Field'],
			'hidden' => false,
			'type' => 'html',
			'DBType' => $field['DBType'],
			'sequence' => 0,
		);
		if (!$allAvaliableFields) continue;
		
		foreach ($allAvaliableFields as &$val) {
			if ($val['field'] == $field['Field']) {
				$fields[$field['Field']]['value'] = $val['value'];
				$fields[$field['Field']]['hidden'] = $val['hidden'];
				$fields[$field['Field']]['sequence'] = $val['sequence'];
				$fields[$field['Field']]['type'] = $val['type'];
			}
		}
	}
	
	if (mb_strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
		// delete not existed fields in the table
		foreach ($_POST as $k => &$v) {
			if (!isset($fields[$k])) unset($_POST[$k]);
		}
		// update existed translation
		foreach ($_POST as $fieldName => &$field) {
			$fieldsData = array(
				'value' => $field['value'],
				'hidden' => isset($field['hidden']),
				'sequence' => (int)$field['sequence'],
				'type' => $field['type'],
			);
			
			if (!$f->updateTranslation($sid, $fieldName, null, $fieldsData)) {
				$d->set('error', sprintf('Error occured while adding translation for field %s in section %s.', $fieldName, $currentSection['title']));
				return $d->content('default/error.php');
			}
		}
		
		$d->redirect('/section/details/' . $sid);
	}
	
	$d->set('DBTypes', Fields::$avaliableTypesByDBTypes);
	$d->set('headTitle', sprintf('Settings of section %s', $currentSection['title']));
	// build tree of parent sections
	$d->set('sectionTree', $s->buildTree($currentSection));
	$d->set('currentSection', $currentSection);
	$d->set('fields', $fields);
	
	return $d->content('sections/settings.php');
}
