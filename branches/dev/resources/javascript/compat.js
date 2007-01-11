/**
 * PHP2Go Web Development Framework
 *
 * Copyright (c) 2002-2006 Marcos Pont
 *
 * LICENSE:
 *
 * This library is free software; you can redistribute it
 * and/or modify it under the terms of the GNU Lesser General
 * Public License as published by the Free Software Foundation;
 * either version 2.1 of the License, or (at your option) any
 * later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @copyright 2002-2006 Marcos Pont
 * @license http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version $Id$
 */

/**
 * @fileoverview
 * This library contains the old PHP2Go Javascript functions, so that we
 * can keep backwards compatibility in running applications. These functions
 * were choosen by their relevance. All other functions were dropped - if you
 * must use them, you should rewrite and place them in the application's
 * source tree
 */

if (!PHP2Go.included[PHP2Go.baseUrl + 'compat.js']) {

function setCookie() {
	Cookie.set.apply(null, setCookie.arguments);
}
function getCookie(name) {
	return Cookie.get(name);
}
function deleteCookie(name) {
	Cookie.remove(name);
}
function getDivLeft(elm) {
	elm = $(elm);
	return (elm ? elm.getPosition().x : null);
}
function getDivTop(elm) {
	elm = $(elm);
	return (elm ? elm.getPosition().y : null);
}
function getDivWidth(elm) {
	elm = $(elm);
	return (elm ? elm.getDimensions().width : null);
}
function getDivHeight(elm) {
	elm = $(elm);
	return (elm ? elm.getDimensions().height : null);
}
function moveDivTo(elm, x, y) {
	if (elm = $(elm)) {
		elm.moveTo(x, y);
	}
}
function getILayerWidth(elm) {
	return IFrame.size(elm).width;
}
function getILayerHeight(elm) {
	return IFrame.size(elm).height;
}
function scrollILayerXTo(elm, x) {
	IFrame.scrollXTo(elm, x);
}
function scrollILayerYTo(elm, y) {
	IFrame.scrollYTo(elm, y);
}
function changeILayerUrl(elm, url) {
	IFrame.setUrl(elm, url);
}
function showHideLayer() {
	var elm, vis = null;
	for (var i=0; i<(arguments.length-1); i+=2) {
		if ((elm=$(arguments[i])) != null) {
			vis = arguments[i+1] || '';
			(vis == 'show') && (vis = 'visible');
			(vis == 'hide') && (vis = 'hidden');
			elm.setStyle('visibility', vis);
		}
	}
}
function setDivVisibility(elm, visible) {
	if (elm = $(elm)) {
		elm.setStyle('visibility', (visible?'visible':'hidden'));
	}
}
function setDivVisibilities(elms, visible) {
	$A(elms).walk(function(item, idx) {
		setDivVisibility(item, visible);
	});
}
function writeToDiv(elm, op, cl) {
	elm = $(elm), op = !!op;
	if (elm) {
		(op && elm.clear());
		elm.insertHTML($A(arguments).slice(3).join(''), 'bottom');
	}
}
function getFormObj(frm) {
	return document.forms[frm];
}
function getFormFieldObj(frm, fld) {
	var fld = $FF(frm, fld);
	if (fld)
		return fld.getFormElement();
	return null;
}
function getFormFieldValue(frm, fld) {
	var fld = $FF(frm, fld);
	if (fld)
		return fld.getValue();
	return null;
}
function getRadioOptions(frm, fld) {
	return getFormFieldObj(frm, fld);
}
function getSelectedRadioOption(frm, fld) {
	return $V(frm, fld);
}
function getCheckboxValue(frm, fld) {
	return $V(frm, fld);
}
function enableField(frm, fld) {
	var fld = $FF(frm, fld);
	(fld) && (fld.enable());
}
function enableFieldList(frm, list) {
	var args = [frm].concat(list);
	Form.enable.apply(null, args);
}
function disableField(frm, fld) {
	var fld = $FF(frm, fld);
	(fld) && (fld.disable());
}
function disableFieldList(frm, list) {
	var args = [frm].concat(list);
	Form.disable.apply(null, args);
}
function requestFocus(frm, fld) {
	var fld = $FF(frm, fld);
	(fld) && (fld.focus());
}
function isEmpty(frm, fld) {
	var fld = $FF(frm, fld);
	return (fld ? fld.isEmpty() : true);
}
function clearForm(frm, editor, readonly, subset) {
	Form.clear(frm, subset);
}
function clearOptions(frm, fld) {
	var fld = $FF(frm, fld);
	(fld) && (fld.clearOptions());
}
function addOption(frm, fld, value, text, pos) {
	var fld = $FF(frm, fld);
	(fld) && (fld.addOption(value, text, pos));
}
function createOptionsFromString(str, frm, fld, ls, cs, idx) {
	var fld = $FF(frm, fld);
	(fld) && (fld.importOptions(str, ls, cs, idx));
}
function selectOptionByCaption(frm, fld, text) {
	var fld = $FF(frm, fld);
	(fld) && (fld.selectByText(text));
}
function getDocumentObject(n, d) {
	return $(n);
}
function getAbsolutePos(el) {
	return Element.getPosition(el);
}
function addEvent(el, name, func) {
	Event.addListener($(el), name, func);
}
function stopEvent(e) {
	$EV(e).stop();
}
function getStyleAttribute(el, name) {
	return Element.getStyle(el, name);
}
function setStyleAttribute(el, name, value) {
	Element.setStyle(el, name, value);
}
function setBackgroundColor(el, color) {
	Element.setStyle(el, 'background-color', (color || 'transparent'));
}
function trim(val) {
	return String(val).trim();
}
function stringReplace() {
	if (arguments.length > 1)
		return String(arguments[0]).assignAll.apply(null, $A(arguments).slice(1));
	return (arguments[0] || '');
}
function capitalizeWords(val) {
	return String(val).capitalize();
}
function createWindow(url, wid, hei, x, y, tit, type, evt, ret) {
	ret = !!ret;
	if (ret)
		return Window.open(url, wid, hei, x, y, type, tit, ret);
	Window.open(url, wid, hei, x, y, type, tit, false);
}

PHP2Go.included[PHP2Go.baseUrl + 'compat.js'] = true;

}