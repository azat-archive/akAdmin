/*
 * This file is part of the akAdmin package.
 * (c) 2010 Azat Khuzhin <dohardgopro@gmail.com>
 *
 * For the full copyright and license information, please view http://www.gnu.org/licenses/gpl-2.0.html
 */

/**
 * @author Azat Khuzhin
 * @package akAdmin
 * @licence GPLv2
 * 
 * Main js models
 */

/*
 * hide main error
 * 
 * Hide main error after five second
 * Main error - is a small popup in top of page
 */
var mainError = {
	selector: 'div#mainError',
	init: function() {
		$(mainError.selector + ' span.close').click(function(e) {e.preventDefault(); return mainError.close();});
		setTimeout(mainError.close, 5000);
	},
	close: function() {
		$(mainError.selector).slideUp('slow', function() {$(mainError.selector).remove();});
	}
}
/*hide main error*/

/*
 * autocomplete section list
 * 
 * Autocomplete dialog using jquery.autocomplete
 */
var autoComplete = {
	nameSelector: 'ul.form li input[name=parentTitle]',
	idSelector: 'ul.form li input[name=pid]',
	url: '/ajax/sections/',
	init: function() {
		$(autoComplete.nameSelector).
		click(function() {$(this).val('')}).
		autocomplete(
			autoComplete.url,
			{
				width: 300,
				onItemSelect: function(row) {
					$(autoComplete.idSelector).val(row.extra);
				},
				formatItem: function(row, index, num) {
					return row.title;
				}
			}
		);
	}
}
/*autocomplete section list*/

/*
 * deleteing projects and tables
 * 
 * Deleting comfirm dialog
 * Using jConfirm
 */
var deletingConfirm = {
	selector: 'a.actionConfirm',
	init: function() {
		$(deletingConfirm.selector).click(function(e) {
			e.preventDefault(this);
			
			jConfirm('Are your shure your want to continue?', 'Confirm', function(r) {
				if (r) window.location = e.target.href;
			});
		});
	}
}
/*\deleteing projects and tables*/

/*
 * fullTree
 * 
 * Full tree - is a tree of users
 * That append some buttons with avaliable grants to select, when your clicking by user,
 * and delete all buttons and input assigned with it on clicking to already clicked user,
 * when your clicking by some of grants it adds input element
 * 
 * And Autoselect button "all" if all is selected
 * 
 * And there is an attr for already existed users grants (when page is loading)
 */
var fullTree = {
	selector: 'ul#fullTree li',
	formSelector: 'form[name=grants]',
	maxGrantsItems: 6,
	init: function() {
		$(fullTree.selector).click(function(e) {return fullTree.click(e);});
		// append onclick, to already existed span's
		$(fullTree.selector + ' span:not(.all).exist').parents('li').children('span.grants').children('span').click(function() { fullTree.createGrantsForm(this); });
		// add to form user grants, which already exist
		$(fullTree.selector + ' span:not(.all).exist').each(function() { return fullTree.createGrantsForm(this, true); });
		return true;
	},
	/**
	 * Click on LI element
	 * Add buttons with grants
	 */
	click: function(e, exist) {
		var el = e.target;
		// if this is a span children -> do nothing
		if (/span/i.test(el.tagName)) return true;
		
		if ($(el).children('span.grants').length > 0) {
			// click on all already selected elements (to delete if from form)
			$(el).children('span.grants').children('span:not(.all).selected').click();
			$(el).children('span.grants').remove();
			return true;
		} else {
			$(el).html($(el).html() + '\
				<span class="grants">\
					<span class="all" onkeydown="return \'all\';">all</span>\
					<span onkeydown="return \'select\';">select</span>\
					<span onkeydown="return \'insert\';">insert</span>\
					<span onkeydown="return \'delete\';">delete</span>\
					<span onkeydown="return \'update\';">update</span>\
					<span onkeydown="return \'alter\';">alter</span>\
					<span onkeydown="return \'drop\';">drop</span>\
				</span>'
			);
			
			$('span.grants span', el).click(function() { fullTree.createGrantsForm(this, exist); });
			return true;
		}
	},
	/**
	 * Create input with grants
	 * And chage class in grant button (SPAN)
	 */
	createGrantsForm: function(el, exist) {
		// if non-selected existed users grants
		if (exist && !$(el).hasClass('selected')) return true;
		
		var input = $(
			fullTree.formSelector +
			sprintf(' [name="id\\[%u\\]\\[%s\\]"]', $(el).parents('li').get(0).onkeyup(), el.onkeydown())
		);
		
		// already selected
		if ($(el).hasClass('selected') && !exist) {
			if (el.onkeydown() != 'all') {
				// change statis of input
				input.val(false);
			} else {
				$(el).parents('li').children('span.grants').children('span:not(.all).selected').click();
			}
			$(el).removeClass('selected');
			fullTree.updateExisted(el);
		}
		// not selected
		else {
			if (el.onkeydown() != 'all') {
				// append input
				if (exist) {
					$(fullTree.formSelector).html(
						$(fullTree.formSelector).html() + 
						sprintf('<input type="hidden" name="id[%u][%s]" value="exist" />',
						$(el).parents('li').get(0).onkeyup(), el.onkeydown())
					);
				} else {
					if (input.length) {
						input.val(true);
					} else {
						$(fullTree.formSelector).html(
							$(fullTree.formSelector).html() + 
							sprintf('<input type="hidden" name="id[%u][%s]" value="true" />',
							$(el).parents('li').get(0).onkeyup(), el.onkeydown())
						);
					}
				}
			} else {
				$(el).parents('li').children('span.grants').children('span:not(.all):not(.selected)').click();
			}
			// add class that selected
			$(el).addClass('selected');
			if (!exist) fullTree.updateExisted(el);
			fullTree.changeAllIfNeed(el);
		}
	},
	/**
	 * If changing status in one of grants in one project
	 * Than delete class that it exists
	 * 
	 * And update inputs
	 */
	updateExisted: function(el) {
		if (el.onkeydown() == 'all') return;
		
		$(el).parent().children('span:not(.all).exist').each(function() {
			var input = $(
				fullTree.formSelector +
				sprintf(' [name="id\\[%u\\]\\[%s\\]"]', $(this).parents('li').get(0).onkeyup(), this.onkeydown())
			);
			
			// update input from exist to true
			if ($(this).hasClass('selected')) {
				input.val(true);
			} else {
				input.val(false);
			}
			
			$(this).removeClass('exist');
		});
	},
	/**
	 * Change button "all"
	 * Select or diselect
	 */
	changeAllIfNeed: function(el) {
		// if all already selected, append class "selected" to "all" item
		grantsItems = 0;
		$(el).parent().children('span:not(.all).selected').each(function() { if ($(this).hasClass('selected')) grantsItems++; });
		if (grantsItems == fullTree.maxGrantsItems) {
			$(el).parent().children('span.all').addClass('selected');
		} else {
			$(el).parent().children('span.all').removeClass('selected');
		}
	}
}
/*fullTree*/

/*
 * search
 * 
 * This very simple object, that append "Search.. " if input is empty, and add gray color
 * And delete gray color when inpput is not empty
 */
var search = {
	selector: 'div#searchPanel',
	defaultValue: 'Search...',
	init: function() {
		$(search.selector + ' form').submit(function() { return search.submit(this); });
		
		$(search.selector + ' input[name=q]').
			blur(function() { search.updateInput($(this)); }).
			focus(function() { search.updateInput($(this)); }).
			click(function() { search.click($(this)); }).
			each(function() { search.updateInput($(this)); });
	},
	updateInput: function(jEl) {
		if (jEl.val() == '') {
			jEl.val(search.defaultValue);
			jEl.css({color: '#999999'});
		} else if (jEl.val() != search.defaultValue) {
			jEl.css({color: '#000000'});
		}
	},
	click: function(jEl) {
		if (jEl.val() == search.defaultValue) {
			jEl.val('');
			jEl.css({color: '#000000'});
		}
	},
	submit: function(el) {
		var searchVal = $('input[name=q]', el).val();
		
		if ((search.defaultValue != searchVal) && searchVal) {
			location.href = el.action + searchVal;
		}
		
		return false;
	}
}
/*\search*/

/*
 * duplicate
 * 
 * Duplicate prompt dialog
 * Using jPrompt
 */
var duplicate = {
	click: function(url) {
		if (!url) return false;
		
		var e = e || window.event;
		e.preventDefault();
		
		jPrompt('How much copies your want?', 1, 'Prompt', function(r) {
			if (!r) return false;
			
			var int = parseInt(r);
			if (int > 0) {
				window.location = url + int;
				return true;
			}
			jAlert('Value must be more then "0"');
		});
	}
}
/*\duplicate*/

/*
 * multiActions
 * 
 * It used on multi delete or mulri duplicate of items
 * of some table
 */
var multiActions = {
	inputSelector: '.itemMultiActions',
	actionsSelector: '.actionSelect',
	allSelector: '.allSelectDeselect',
	urlErase: '', // form param "action" of inputs by inputSelector, will be change to this value, on click to "Erase"
	urlDuplicate: '', // form param "action" of inputs by inputSelector, will be change to this value, on click to "Duplicate"
	init: function() {
		$(multiActions.inputSelector).click(function() { multiActions.appendActions(); });
		$(multiActions.allSelector).click(function() { multiActions.allSelectDeselect(); });
	},
	allSelectDeselect: function() {
		var e = e || window.event;
		e.preventDefault();
		
		$(multiActions.inputSelector).each(function() {
			if (!this.checked) this.checked = true;
			else this.checked = false;
		});
		
		multiActions.appendActions();
	},
	appendActions: function() {
		// not items
		if ($(multiActions.inputSelector + ':checked').length > 0) {
			$(multiActions.actionsSelector).html(
				'<a href="#" onclick="multiActions.erase();">Delete</a>\
				<a href="#" onclick="multiActions.duplicate();">Copy</a>'
			);
		} else {
			$(multiActions.actionsSelector).html('');
		}
	},
	erase: function() {
		var e = e || window.event;
		e.preventDefault();
		
		// not items or url for erase is not set
		if ($(multiActions.inputSelector + ':checked').length <= 0 || !multiActions.urlErase) return false;
		
		// confirm
		jConfirm('Are your shure your want to delete all selected items?', 'Confirm', function(r) {
			if (!r) return false;
			
			// replace "action" param of form, and submit it
			$(multiActions.inputSelector).parents('form').attr('action', multiActions.urlErase).submit();
		});
	},
	duplicate: function() {
		var e = e || window.event;
		e.preventDefault();
		
		// not items or url for duplicate is not set
		if ($(multiActions.inputSelector + ':checked').length <= 0 || !multiActions.urlDuplicate) return false;
		
		jPrompt('How much copies of selected items your want?', 1, 'Prompt', function(r) {
			if (!r) return false;
			
			var int = parseInt(r);
			if (int > 0) {
				// replace "action" param of form, and submit it
				$(multiActions.inputSelector).parents('form').attr('action', multiActions.urlDuplicate + int).submit();
				
				return true;
			}
			jAlert('Value must be more then "0"');
		});
	}
}
/*\multiActions*/

/*onload
 * 
 * Assign onload event
 */
$(document).ready(function() {
	mainError.init();
	autoComplete.init();
	deletingConfirm.init();
	fullTree.init();
	search.init();
	multiActions.init();
	$('.fancyboxImage').fancybox();
	$('.wysiwyg').wysiwyg();
});
/*\onload*/