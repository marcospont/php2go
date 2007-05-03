/**
 * PHP2Go Web Development Framework
 *
 * Copyright (c) 2002-2007 Marcos Pont
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
 * @copyright 2002-2007 Marcos Pont
 * @license http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version $Id$
 */

/**
 * @fileoverview
 * Contains TabView and TabPanel classes
 */

if (!PHP2Go.included[PHP2Go.baseUrl + 'widgets/tabview.js']) {

PHP2Go.include(PHP2Go.baseUrl + 'ajax.js');

/**
 * TabView provides control over a set of tab panels
 * (tabbed views). The class contains methods to manipulate
 * these panels, as well as add and remove them
 * @param {Object} attrs Widget's attributes
 * @param {Function} func Setup function
 * @constructor
 * @base Widget
 */
function TabView(attrs, func) {
	this.Widget(attrs, func);
	/**
	 * Root element
	 * @type Object
	 */
	this.root = null;
	/**
	 * Layer that surrounds the navigation container element
	 * @type Object
	 */
	this.navScroll = null;
	/**
	 * Navigation bar's container element
	 * @type Object
	 */
	this.navContainer = null;
	/**
	 * Tabs container element
	 * @type Object
	 */
	this.contentContainer = null;
	/**
	 * Active tab
	 * @type TabPanel
	 */
	this.activeTab = null;
	/**
	 * Tab panels
	 * @type Array
	 */
	this.tabs = [];
	/**
	 * @ignore
	 */
	this.busy = false;
}
TabView.extend(Widget, 'Widget');

/**
 * Holds existent TabView instances,
 * indexed by widget ID.
 * @type Object
 */
TabView.instances = {};

/**
 * Initializes the widget
 */
TabView.prototype.setup = function() {
	this.root = $(this.attributes.id);
	this.navScroll = this.root.getElementsByClassName('tabNavigationContainer')[0];
	this.navContainer = this.root.getElementsByClassName('tabNavigation')[0];
	this.contentContainer = this.root.getElementsByClassName('tabContainer')[0];
	// initialize tabs
	var navItems = this.navContainer.getElementsByTagName('li');
	for (var i=0; i<this.attributes.tabs.length; i++) {
		var panel = new TabPanel(this.attributes.tabs[i]);
		panel.labelEl = $E(navItems[i]);
		panel.parent = this;
		panel.setup();
		this.tabs.push(panel);
		Event.addListener(navItems[i].firstChild, 'click', this._clickHandler.bind(this));
	}
	// adjust navigation height
	if (PHP2Go.browser.gecko && (this.attributes.orientation == 'left' || this.attributes.orientation == 'right'))
		this.navScroll.style.height = this.navContainer.style.height = $E(this.contentContainer.getElementsByTagName('div')[0]).getDimensions().height;
	this._initArrows();
	this.setActiveIndex(this.attributes.activeIndex);
	TabView.instances[this.attributes.id] = this;
	this.raiseEvent('init');
};

/**
 * Adds a new tab panel
 * @param {TabPanel} tab New tab
 * @param {Number} idx Index where tab must be added
 */
TabView.prototype.addTab = function(tab, idx) {
	if (tab instanceof TabPanel && tab.hasAttributes('id', 'caption')) {
		tab.parent = this;
		var before = this.getTabByIndex(idx);
		var nav = this.navContainer, cont = this.contentContainer;
		// create tab navigation item
		var li = $N('li');
		tab.labelEl = li;
		var a = $N('a', li);
		a.href = 'javascript:;';
		if (!tab.isEnabled())
			a.setAttribute('disabled', true);
		var span = $N('em', a);
		span.innerHTML = tab.attributes.caption;
		// create tab container
		var div = $N('div');
		div.id = tab.attributes.id;
		// insert before or append
		if (before) {
			nav.insertBefore(li, before.labelEl);
			cont.insertBefore(div, before.contentEl);
			this.tabs.splice(idx-1, 0, tab);
		} else {
			nav.appendChild(li);
			cont.appendChild(div);
			this.tabs.push(tab);
		}
		tab.setup();
		if (tab.isActive())
			this.setActiveTab(tab);
		else
			this._updateArrows();
		Event.addListener(a, 'click', this._clickHandler.bind(this));
	}
};

/**
 * Removes a tab panel given its index
 * @param {Number} idx Tab index
 */
TabView.prototype.removeTabByIndex = function(idx) {
	this.removeTab(this.getTabByIndex(idx));
};

/**
 * Removes a given tab panel
 * @param {TabPanel} tab Tab to remove
 */
TabView.prototype.removeTab = function(tab) {
	if (tab instanceof TabPanel && tab.parent == this) {
		if (!this.raiseEvent('beforeremove', [tab]))
			return;
		var idx = this.getTabIndex(tab);
		if (tab == this.activeTab) {
			var found = false;
			// try to activate a tab from the right side
			for (var i=this.activeIndex+1; i<=this.tabs.length; i++) {
				if (this.tabs[i-1].isEnabled()) {
					this.setActiveTab(this.tabs[i-1]);
					found = true;
					break;
				}
			}
			// try to activate a tab from the left side
			if (!found) {
				for (var i=this.activeIndex-1; i>0; i--) {
					if (this.tabs[i-1].isEnabled()) {
						this.setActiveTab(this.tabs[i-1]);
						break;
					}
				}
			}
		}
		Event.removeListener(tab.labelEl, 'click', this._clickHandler.bind(this));
		this.navContainer.removeChild(tab.labelEl);
		this.contentContainer.removeChild(tab.contentEl);
		this.tabs.splice(idx-1, 1);
		this.raiseEvent('afterremove', [tab]);
		this._updateArrows();
	}
};

/**
 * Get the index of a given tab
 * @param {TabPanel} tab Tab panel
 */
TabView.prototype.getTabIndex = function(tab) {
	for (var i=0; i<this.tabs.length; i++) {
		if (this.tabs[i] == tab)
			return (i+1);
	}
	return -1;
};

/**
 * Get a tab given its index
 * @param {Number} idx Index
 * @type TabPanel
 */
TabView.prototype.getTabByIndex = function(idx) {
	if (idx >= 1 && idx <= this.tabs.length)
		return this.tabs[idx-1];
	return null;
};

/**
 * Get a tab given its id
 * @param {String} id Tab ID
 * @type TabPanel
 */
TabView.prototype.getTabById = function(id) {
	for (var i=0; i<this.tabs.length; i++) {
		if (this.tabs[i].attributes.id == id)
			return this.tabs[i];
	}
	return null;
};

/**
 * Get active tab index
 * @type Number
 */
TabView.prototype.getActiveIndex = function() {
	return this.activeIndex;
};

/**
 * Set active tab index
 * @param {Number} idx Tab index
 */
TabView.prototype.setActiveIndex = function(idx) {
	this.setActiveTab(this.getTabByIndex(idx));
};

/**
 * Get active tab
 * @type TabPanel
 */
TabView.prototype.getActiveTab = function()  {
	return this.tabs[this.activeIndex-1];
};

/**
 * Set active tab
 * @param {TabPanel} tab Tab to activate
 */
TabView.prototype.setActiveTab = function(tab) {
	if (tab instanceof TabPanel && tab.isEnabled() && tab != this.activeTab) {
		if (!this.raiseEvent('beforechange', [this.activeTab, tab]))
			return;
		if (this.activeTab) {
			this._changeActiveState(this.activeTab, false);
		}
		if (tab.attributes.loadUri && (!tab.attributes.loaded || !this.attributes.loadCache)) {
			this._loadContents(tab);
		} else {
			this._changeActiveState(tab, true);
			this.raiseEvent('afterchange', [this.activeTab, tab]);
			this.activeIndex = this.getTabIndex(tab);
			this.activeTab = tab;
			this._updateArrows();
		}
	}
};

/**
 * Changes active state of a given tab
 * @param {TabPanel} tab Tab panel
 * @param {Boolean} state Active state
 * @access private
 */
TabView.prototype._changeActiveState = function(tab, state) {
	var nc = tab.labelEl.classNames();
	var ec = tab.contentEl.classNames();
	if (state) {
		(!nc.has('tabViewSelected')) && (nc.add('tabViewSelected'));
		(!ec.has('tabViewVisible')) && (ec.add('tabViewVisible'));
		tab.labelEl.scrollIntoView(false);
		tab.raiseEvent('activate');
	} else {
		nc.remove('tabViewSelected');
		ec.remove('tabViewVisible');
		tab.raiseEvent('deactivate');
	}
	tab.attributes.active = state;
};

/**
 * Loads content into a tab using AJAX
 * @param {TabPanel} tab Tab panel
 * @access private
 */
TabView.prototype._loadContents = function(tab) {
	var self = this;
	var request = new AjaxUpdater(tab.attributes.loadUri, {
		method: tab.attributes.loadMethod,
		params: tab.attributes.loadParams,
		container: tab.contentEl,
		async: true
	});
	tab.attributes.loaded = false;
	tab.contentEl.classNames().add('tabViewLoading');
	self.busy = true;
	self._changeActiveState(tab, true);
	self.raiseEvent('beforeload', [tab, request]);
	request.bind('onUpdate', function() {
		tab.contentEl.classNames().remove('tabViewLoading');
		tab.attributes.loaded = true;
		self.raiseEvent('afterload', [tab]);
		self.raiseEvent('afterchange', [self.activeTab, tab]);
		self.activeIndex = self.getTabIndex(tab);
		self.activeTab = tab;
		self._updateArrows();
		self.busy = false;
	});
	request.send();
};

/**
 * Handles click event on tab labels
 * @param {Event} e Event
 * @access private
 */
TabView.prototype._clickHandler = function(e) {
	if (!this.busy) {
		var elm = this.root, e = $EV(e);
		var trg = e.element(), tabs = this.tabs;
		for (var i=0; i<tabs.length; i++) {
			if (Element.isChildOf(trg, tabs[i].labelEl)) {
				this.setActiveTab(tabs[i]);
				break;
			}
		}
	}
};

/**
 * Initializes scroll arrows
 * @access private
 */
TabView.prototype._initArrows = function() {
	var ac = this.root.getElementsByClassName('tabScrollContainer')[0];
	var ar = this.arrows = ac.getElementsByClassName('tabScrollArrow');
	var self = this, ns = this.navScroll, dim = ns.getDimensions(), al = Event.addListener;
	var functions = {
		left : function() { (ns.scrollLeft > 0) && (ns.scrollLeft -= 5); self._updateArrows(); (ns.scrollLeft > 0) && (self.timeout = setTimeout(functions.left, 10)); },
		right : function() { ((ns.scrollLeft+dim.width) < ns.scrollWidth) && (ns.scrollLeft += 5); self._updateArrows(); ((ns.scrollLeft+dim.width) < ns.scrollWidth) && (self.timeout = setTimeout(functions.right, 10)); },
		top : function() { (ns.scrollTop > 0) && (ns.scrollTop -= 5); self._updateArrows(); (ns.scrollTop > 0) && (setTimeout(functions.top, 10)); },
		bottom : function() { ((ns.scrollTop+dim.height) < ns.scrollHeight) && (ns.scrollTop += 5); self._updateArrows(); ((ns.scrollTop+dim.height) < ns.scrollHeight) && (setTimeout(functions.bottom, 10)); }
	};
	if (this.attributes.orientation == 'top' || this.attributes.orientation == 'bottom') {
		ar[0].style.height = ar[1].style.height = ns.offsetHeight;
		ar[1].style.left = dim.width - 12;
		al(ar[0], 'mousedown', functions.left);
		al(ar[0], 'mouseup', function() { if (self.timeout) clearTimeout(self.timeout); });
		al(ar[1], 'mousedown', functions.right);
		al(ar[1], 'mouseup', function() { if (self.timeout) clearTimeout(self.timeout); });
	} else {
		ar[0].style.width = ar[1].style.width = ns.offsetWidth;
		ar[1].style.top = dim.height - 12;
		al(ar[0], 'mousedown', functions.top);
		al(ar[0], 'mouseup', function() { if (self.timeout) clearTimeout(self.timeout); });
		al(ar[1], 'mousedown', functions.bottom);
		al(ar[1], 'mouseup', function() { if (self.timeout) clearTimeout(self.timeout); });
	}
};

/**
 * Updates scroll arrows visibility state
 * @access private
 */
TabView.prototype._updateArrows = function() {
	var ar = this.arrows, ns = this.navScroll;
	var dim = this.navScroll.getDimensions();
	if (this.attributes.orientation == 'top' || this.attributes.orientation == 'bottom') {
		ar[0].style.visibility = (ns.scrollLeft > 0 ? 'visible' : 'hidden');
		ar[1].style.visibility = (ns.scrollWidth > (dim.width+ns.scrollLeft) ? 'visible' : 'hidden');
	} else {
		ar[0].style.display = (ns.scrollTop > 0 ? 'visible' : 'hidden');
		ar[1].style.display = (ns.scrollHeight > (dim.height+ns.scrollTop) ? 'visible' : 'hidden');
	}
};



/**
 * The TabPanel class represents a tabbed view,
 * member of a TabView widget
 * @param {Object} attrs Attributes
 * @constructor
 * @base Widget
 */
function TabPanel(attrs) {
	this.Widget(attrs, null);
	/**
	 * Label element
	 * @type Object
	 */
	this.labelEl = null;
	/**
	 * Content element
	 */
	this.contentEl = null;
	/**
	 * Parent tab view
	 */
	this.parent = null;
	this.attributes.disabled = !!this.attributes.disabled;
	this.attributes.active = !!this.attributes.active;
	this.attributes.loaded = false;
}
TabPanel.extend(Widget, 'Widget');

/**
 * Initializes the widget
 */
TabPanel.prototype.setup = function() {
	this.contentEl = $(this.attributes.id);
	this.contentEl.tabPanel = this;
};

/**
 * Get the index of this tab panel
 * @type Number
 */
TabPanel.prototype.getIndex = function() {
	return this.parent.getTabIndex(this);
};

/**
 * Activates the tab
 */
TabPanel.prototype.activate = function() {
	this.parent.setActiveTab(this);
};

/**
 * Check if this tab is active
 * @type Boolean
 */
TabPanel.prototype.isActive = function() {
	return (this.attributes.active && this.parent.activeTab == this);
};

/**
 * Enables the tab
 */
TabPanel.prototype.enable = function() {
	this.attributes.disabled = false;
	this.labelEl.firstChild.removeAttribute('disabled');
};

/**
 * Disables the tab
 */
TabPanel.prototype.disable = function() {
	this.attributes.disabled = true;
	this.labelEl.firstChild.setAttribute('disabled', true);
};

/**
 * Checks if the tab is enabled
 * @type Boolean
 */
TabPanel.prototype.isEnabled = function() {
	return (this.attributes.disabled == false);
};

/**
 * Set tab contents
 * @param {String} code Contents
 * @param {Boolean} evalScripts Eval script blocks or not
 */
TabPanel.prototype.setContent = function(code, evalScripts) {
	if (typeof(code) == 'string')
		this.contentEl.update(code, evalScripts);
};

PHP2Go.included[PHP2Go.baseUrl + 'widgets/tabview.js'] = true;

}