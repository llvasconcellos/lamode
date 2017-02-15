/**
 * Magento
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Academic Free License (AFL 3.0) that is
 * bundled with this package in the file LICENSE_AFL.txt. It is also available
 * through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php If you did not receive a copy of
 * the license and are unable to obtain it through the world-wide-web, please
 * send an email to license@magentocommerce.com so we can send you a copy
 * immediately.
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your needs
 * please refer to http://www.magentocommerce.com for more information.
 * 
 * @category Gr
 * @package Gr_Deepzoom
 * @copyright Copyright (c) 2010 groupeReflect (http://www.groupereflect.net)
 * @license http://opensource.org/licenses/afl-3.0.php Academic Free License
 *          (AFL 3.0)
 */
var GrSeadragonUi = Class.create();
GrSeadragonUi.prototype = {
	initialize : function(b, a) {
		this._position = 0;
		this.area = b;
		this.options = a;
		this.onClick = this.switchTo.bind(this);
		this.onControlNextClick = this.controlNextClick.bind(this);
		this.onControlPreviousClick = this.controlPreviousClick.bind(this);
		$$("a[rel^='seadragon']").each(function(c) {
			c.observe("click", this.onClick)
		}.bind(this))
	},
	startViewer : function() {
		if ($(this.area)) {
			this.setConfiguration();
			this.setTooltips();
			this.viewer = new Seadragon.Viewer(this.area)
		}
	},
	setConfiguration : function() {
		Seadragon.Config.immediateRender = true;
		if (this.options.Config != null) {
			var a = $H(Seadragon.Config);
			var b = a.merge(this.options.Config);
			Seadragon.Config = b.toObject()
		}
	},
	setTooltips : function() {
		if (this.options.Tooltips != null) {
			if (this.options.Tooltips.ZoomIn != null) {
				Seadragon.Strings.setString("Tooltips.ZoomIn",
						this.options.Tooltips.ZoomIn)
			}
			if (this.options.Tooltips.ZoomOut != null) {
				Seadragon.Strings.setString("Tooltips.ZoomOut",
						this.options.Tooltips.ZoomOut)
			}
			if (this.options.Tooltips.Home != null) {
				Seadragon.Strings.setString("Tooltips.Home",
						this.options.Tooltips.Home)
			}
			if (this.options.Tooltips.FullPage != null) {
				Seadragon.Strings.setString("Tooltips.FullPage",
						this.options.Tooltips.FullPage)
			}
		}
	},
	activePrevNext : function() {
		if ($(this.area)) {
			this.addControl()
		}
	},
	applyContainerStyles : function() {
		if (this.options.containerStyles != null) {
			$(this.area).setStyle(this.options.containerStyles)
		}
	},
	hideNoSelectionArea : function() {
		if (this.options.noSelection != null) {
			$$(this.options.noSelection).invoke("hide")
		}
	},
	open : function(a) {
		if (this.viewer == null) {
			this.startViewer()
		}
		this._url = a;
		this.applyContainerStyles();
		this.viewer.openDzi(a)
	},
	switchTo : function(b) {
		var a = Event.element(b);
		if (a.rel == "seadragon") {
			dzi = a.href
		} else {
			dzi = a.parentNode.href
		}
		if (this.viewer == null) {
			this.startViewer()
		}
		if (dzi) {
			this.hideNoSelectionArea();
			this.open(dzi)
		} else {
			this.viewer.close()
		}
		this.removeControl();
		this.refreshPosition();
		this.addControl();
		Seadragon.Utils.cancelEvent(b)
	},
	addControl : function() {
		if (this._position < this._collection.length - 1) {
			this.viewer.addControl(this.makeNextControl(this._position),
					Seadragon.ControlAnchor.TOP_RIGHT)
		}
		if (this._position != 0) {
			this.viewer.addControl(this.makePreviousControl(this._position),
					Seadragon.ControlAnchor.TOP_LEFT)
		}
	},
	makeNextControl : function(a) {
		var c = document.createElement("a");
		var b = document.createTextNode(" ");
		c.id = this.area + "-btn-next";
		c.href = "#";
		c.className = "control c-nxt";
		c.rel = ++a;
		c.appendChild(b);
		Seadragon.Utils.addEvent(c, "click", this.onControlNextClick);
		return c
	},
	controlNextClick : function(b) {
		var a = Event.element(b);
		Event.stop(b);
		if (this._collection[a.rel] != null
				&& this._collection[a.rel].url != null) {
			this.open(this._collection[a.rel].url)
		}
		this.removeControl();
		this.refreshPosition();
		this.addControl()
	},
	makePreviousControl : function(a) {
		var c = document.createElement("a");
		var b = document.createTextNode(" ");
		c.id = this.area + "-btn-prev";
		c.href = "#";
		c.className = "control c-prev";
		c.rel = --a;
		c.appendChild(b);
		Seadragon.Utils.addEvent(c, "click", this.onControlPreviousClick);
		return c
	},
	controlPreviousClick : function(b) {
		var a = Event.element(b);
		Event.stop(b);
		if (this._collection[a.rel] != null
				&& this._collection[a.rel].url != null) {
			this.open(this._collection[a.rel].url)
		}
		this.removeControl();
		this.refreshPosition();
		this.addControl()
	},
	removeControl : function() {
		this.viewer.removeControl(this.area + "-btn-prev");
		this.viewer.removeControl(this.area + "-btn-next")
	},
	loadGallery : function(a) {
		this._collection = a;
		this.refreshPosition();
		this.addControl()
	},
	refreshPosition : function() {
		for ( var a in this._collection) {
			if (this._collection[a].url == this._url) {
				this._position = a
			}
		}
	}
};
