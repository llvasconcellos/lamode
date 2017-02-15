/**
 * Prozoom - JQZoom Evolution on prototype&scriptaculous
 * 
 * Templates Master www.templates-master.com
 */
/*
 * JQZoom Evolution 1.0.1 - Javascript Image magnifier
 *
 * Copyright (c) Engineer Renzi Marco(www.mind-projects.it)
 *
 * $Date: 12-12-2008
 *
 * ChangeLog:
 *
 * $License : GPL,so any change to the code you should copy and paste this section,and would be nice to report this to me(renzi.mrc@gmail.com).
 */

Prozoom = function(selector, options){
    var settings = {
        zoomType: 'standard',
        zoomWidth: 200,
        zoomHeight: 200,
        xOffset: 10,
        yOffset: 0,
        position: 'right',
        observeClick: true,
        lens: true,
        lensReset: false,
        lensCursor: 'crosshair',
        lensCursorSm: 'default',
        lensOpacity: 0.5,
        imageOpacity: 0.2,
        title: true,
        alwaysOn: false,
        showEffect: 'fade',
        hideEffect: 'fade',
        showSpeed: 0.3,
        hideSpeed: 0.3,
        preloadImages: true,
        showPreload: true,
        errorMsg: 'Large image cannot be loaded',
        preloadText: 'Loading zoom'
    };
    
    options = options || {};
    Object.extend(settings, options);
    
    return $$(selector).each(function(el) {
        var a = el;
        var img = $(a).select('img')[0];
        var smallimage = new Smallimage(img);
        var smallimagedata = {};
        var btop = 0;
        var bleft = 0;
        
        var loader = null;
        loader = new Loader();
        var informer = null;
        informer = new Informer();
        
        var ZoomTitle = '';
        var ZoomTitleObj = new zoomTitle();
        
        var largeimage = [];
        largeimage[a.href] = new Largeimage(a.href);
        
        var lens = new Lens();
        var lensdata = {};
        
        var largeimageloaded = [];
        var smallimageloaded = false;
        var activatestage = [];
        var scale = [];
        var stage = [];
        var running = false;
        var mousepos = {};
        var firstime = 0;
        var preloadshow = false;

        smallimage.loadimage();
        
        if (settings.observeClick) {
            $(a).observe('click', showLargeImage);
        }
        
        $(a).hover(function(e){
            mousepos.x = e.pageX;
            mousepos.y = e.pageY;
            activate();
        }, function(){
            deactivate();
        });
        $$('a.prozoom-small-image').each(function(el){ 
            Event.observe(el, 'click', switchProductImage);
        });
        Event.observe(window, 'resize', function() {
            smallimage.updatePosition();
        });

        
        function activate()
        {
            if (!running) {
                smallimage.findborder();
                running = true;
                imageTitle = img.title;
                $(img).removeAttribute('title');
                aTitle = a.title;
                $(a).removeAttribute('title');
                
                ZoomTitle = (trim(aTitle).length > 0) ? aTitle : (trim(imageTitle).length > 0) ? imageTitle : null;
                ZoomTitleObj.updateTitle();
                
                if (!largeimage[a.href]) {
                    largeimage[a.href] = new Largeimage(a.href);
                }
                if (!largeimageloaded[a.href]) {
                    largeimage[a.href].loadimage();
                    return false;
                }
                
                if (typeof(activatestage[a.href]) != 'undefined' && activatestage[a.href] == false) {
                    a.style.cursor = settings.lensCursorSm;
                    return false;
                }
                
                if (settings.zoomType != 'innerzoom') {
                    stage[a.href] = new Stage();
                    stage[a.href].activate();
                }
                lens.activate();
                $(a).blur();
                return false;
            }
        }
        
        function deactivate()
        {
            if (!running) {
                return;
            }
            
            if (settings.zoomType == 'reverse' && !settings.alwaysOn) {
                img.setOpacity(1);
            }
            
            if (!settings.alwaysOn) {
                running = false;
                $(lens.node).stopObserving('mousemove');
                lens.remove();
                if (settings.zoomType != 'innerzoom' && stage[a.href]) {
                    stage[a.href].remove();
                }
                ZoomTitleObj.remove();
                img.writeAttribute('title', imageTitle);
                a.writeAttribute('title', aTitle);
                a.stopObserving('mousemove');
                firstime = 0;
                if ($$('.zoom_ieframe').length > 0) {
                    $$('.zoom_ieframe').invoke('remove');
                }
            } else {
                if (settings.lensReset) {
                    switch (settings.zoomType) {
                        case 'innerzoom':
                            largeimage[a.href].setcenter();
                            break;
                        default:
                            lens.center();
                            break;
                    }
                }
            }
        };
        
        function showLargeImage(e)
        {
            e.stop();
            var link = e.element();
            while (link.tagName != 'A') {
                link = link.up();
            }
            if (!largeimageloaded[link.href] || $('largeimage')) {
                return false;
            }
            var largeimage = new Element('div', {'id': 'largeimage'});
            document.body.appendChild(largeimage);
            var image = new Image();
            image.onload = function(){
                $(largeimage).setStyle({
                    left: document.viewport.getWidth() / 2 - image.width / 2 + 'px',
                    top: document.viewport.getScrollOffsets().top + 50 + 'px'
                });
                $(largeimage).setOpacity(0);
                new Effect.Fade($(largeimage), {
                    duration: 0.5,
                    from: 0,
                    to: 1
                });
                $(largeimage).select('.close').each(function(el) {
                    Event.observe(el, 'click', function(){
                        $('largeimage').remove();
                    });
                })
            }
            largeimage.innerHTML = '<img src="' + link.href + '" />';
            largeimage.innerHTML += '<a href="javascript:void(0)" class="close">Close image</a>';
            image.src = link.href;
        }
        
        function switchProductImage(e)
        {
            e.stop();
            loader.hide();
            informer.hide();
            var link = e.element();
            while (link.tagName != 'A') {
                link = link.up();
            }
            $('image').src = link.down('img.small-image').src;
            $$('.more-views-list li').invoke('removeClassName', 'active');
            link.up().addClassName('active');
            a.writeAttribute('href', link.href);
            a.writeAttribute('title', link.title);
            img.writeAttribute('title', link.down('img.small-image').title);
            
            if (settings.alwaysOn && !largeimage[link.href]) {
                largeimage[link.href] = new Largeimage(link.href);
                largeimage[link.href].loadimage();
            }
            
            if (settings.zoomType == 'innerzoom' && settings.alwaysOn) {
                function activateLens() {
                    if (!largeimageloaded[link.href]) {
                        setTimeout(activateLens, 150);
                    } else {
                        lens.activate();
                    }
                }
                activateLens();
            } else if (settings.alwaysOn) {
                lens.activate();
                if (stage[a.href]) {
                    stage[a.href].activate();
                }
            }
        }
        
        function Smallimage(image){
            this.node = image;
            
            this.loadimage = function(){
                this.node.src = image.src;
            };
            
            this.findborder = function(){
                var bordertop = '';
                bordertop = $(img).getStyle('border-top-width');
                btop = '';
                var borderleft = '';
                borderleft = $(img).getStyle('border-left-width');
                bleft = '';
                if (Prototype.Browser.IE) {
                    var temp = bordertop.split(' ');
                    
                    bordertop = temp[1];
                    var temp = borderleft.split(' ');
                    borderleft = temp[1];
                }
                
                if (bordertop) {
                    for (i = 0; i < 3; i++) {
                        var x = [];
                        x = bordertop.substr(i, 1);
                        
                        if (isNaN(x) == false) {
                            btop = btop + '' + bordertop.substr(i, 1);
                        }
                        else {
                            break;
                        }
                    }
                }
                
                if (borderleft) {
                    for (i = 0; i < 3; i++) {
                        if (!isNaN(borderleft.substr(i, 1))) {
                            bleft = bleft + borderleft.substr(i, 1)
                        }
                        else {
                            break;
                        }
                    }
                }
                btop = (btop.length > 0) ? eval(btop) : 0;
                bleft = (bleft.length > 0) ? eval(bleft) : 0;
            };
            
            this.updatePosition = function(){
                if (typeof(smallimagedata.w) == 'undefined') {
                    return;
                }
                smallimagedata.pos = $(this.node).viewportOffset();
                smallimagedata.pos.l = $(this.node).viewportOffset().left;
                smallimagedata.pos.t = $(this.node).viewportOffset().top + document.viewport.getScrollOffsets().top;
                smallimagedata.pos.r = smallimagedata.w + smallimagedata.pos.l;
                smallimagedata.pos.b = smallimagedata.h + smallimagedata.pos.t;
            };
            
            this.node.onload = function(){
                a.setStyle({
                    'cursor': settings.lensCursor,
                    'display': 'block'
                });
                
                if (a.getStyle('position') != 'absolute' && a.up().getStyle('position')) {
                    a.setStyle({
                        'cursor': settings.lensCursor,
                        'position': 'relative',
                        'display': 'block'
                    });
                }
                if (a.up().getStyle('position') != 'absolute') {
                    a.up().setStyle({
                        'position': 'relative'
                    });
                }
                if (Prototype.Browser.Webkit || Prototype.Browser.Opera) {
                    $(img).setStyle({
                        position: 'absolute',
                        top: '0px',
                        left: '0px'
                    });
                }
                
                smallimagedata.w = $(this).getWidth();
                smallimagedata.h = $(this).getHeight();
                smallimagedata.pos = $(this).viewportOffset();
                smallimagedata.pos.l = $(this).viewportOffset().left;
                smallimagedata.pos.t = $(this).viewportOffset().top + document.viewport.getScrollOffsets().top;
                smallimagedata.pos.r = smallimagedata.w + smallimagedata.pos.l;
                smallimagedata.pos.b = smallimagedata.h + smallimagedata.pos.t;
                
                $(a).setStyle({
                    'height': smallimagedata.h + 'px',
                    'width': smallimagedata.w + 'px'
                });
                
                smallimageloaded = true;
                
                if (settings.alwaysOn) {
                    activate();
                }
                if (settings.preloadImages) {
                    largeimage[a.href].loadimage();
                }
            };
            return this;
        };
        
        function Lens(){
            this.node = new Element('div', {'class': 'zoom-lens'});
            this.node.addClassName('zoom-lens'); 
            
            this.loadlens = function(){
                switch (settings.zoomType) {
                    case 'reverse':
                        this.image = new Image();
                        this.image.src = smallimage.node.src;
                        this.node.childElements().invoke('remove');
                        this.node.appendChild(this.image);
                        $(this.node).setOpacity(1);
                        break;
                    case 'innerzoom':
                        this.image = new Image();
                        this.image.src = largeimage[a.href].node.src;
                        this.node.childElements().invoke('remove');
                        this.node.appendChild(this.image);
                        $(this.node).setOpacity(1);
                        break
                    default:
                        $(this.node).setOpacity(settings.lensOpacity);
                        break;
                }
                
                switch (settings.zoomType) {
                    case 'innerzoom':
                        lensdata.w = smallimagedata.w;
                        lensdata.h = smallimagedata.h;
                        break;
                    default:
                        lensdata.w = (settings.zoomWidth) / scale[a.href].x;
                        lensdata.h = (settings.zoomHeight) / scale[a.href].y;
                        break;
                }
                
                $(this.node).setStyle({
                    'width': lensdata.w + 'px',
                    'height': lensdata.h + 'px',
                    'display': 'none'
                });
                a.insert({'top': this.node});
            }
            return this;
        };
        
        Lens.prototype.activate = function(){
            this.loadlens();
            switch (settings.zoomType) {
                case 'reverse':
                    img.setOpacity(settings.imageOpacity);
                    (settings.alwaysOn) ? lens.center() : lens.setposition(null);
                    $(a).observe('mousemove', function(e){
                        mousepos.x = e.pageX;
                        mousepos.y = e.pageY;
                        lens.setposition(e);
                    });
                    break;
                case 'innerzoom':
                    $(this.node).setStyle({top: 0 + 'px', left: 0 + 'px'}).show();
                    if (settings.title) {
                        ZoomTitleObj.loadtitle();
                    }
                    largeimage[a.href].setcenter();
                    $(a).observe('mousemove', function(e){
                        mousepos.x = e.pageX;
                        mousepos.y = e.pageY;
                        largeimage[a.href].setinner(e);
                    });
                    break;
                default:
                    (settings.alwaysOn) ? lens.center() : lens.setposition(null);
                    $(a).observe('mousemove', function(e){
                        mousepos.x = e.pageX;
                        mousepos.y = e.pageY;
                        lens.setposition(e);
                    });
                    break;
            }
            
            return this;
        };
        
        Lens.prototype.setposition = function(e){
            if (e) {
                mousepos.x = e.pageX;
                mousepos.y = e.pageY;
            }
            
            if (firstime == 0) {
                var lensleft = (smallimagedata.w) / 2 - (lensdata.w) / 2;
                var lenstop = (smallimagedata.h) / 2 - (lensdata.h) / 2;
                this.node.show();
                if (settings.lens) {
                    this.node.style.visibility = 'visible';
                } else {
                    this.node.style.visibility = 'hidden';
                    this.node.hide();
                }
                firstime = 1;
            } else {
                var lensleft = mousepos.x - smallimagedata.pos.l - (lensdata.w) / 2;
                var lenstop = mousepos.y - smallimagedata.pos.t - (lensdata.h) / 2;
            }
            
            if (overleft()) {
                lensleft = 0 + bleft;
            } else if (overright()) {
                if (Prototype.Browser.IE) {
                    lensleft = smallimagedata.w - lensdata.w + bleft + 1;
                } else {
                    lensleft = smallimagedata.w - lensdata.w + bleft - 1;
                }
            }
            
            if (overtop()) {
                lenstop = 0 + btop;
            } else if (overbottom()) {
                if (Prototype.Browser.IE) {
                    lenstop = smallimagedata.h - lensdata.h + btop + 1;
                } else {
                    lenstop = smallimagedata.h - lensdata.h - 1 + btop;
                }
            }
            lensleft = parseInt(lensleft);
            lenstop = parseInt(lenstop);
            
            this.node.setStyle({
                top: lenstop + 'px',
                left: lensleft + 'px'
            });
            
            if (settings.zoomType == 'reverse') {
                this.image.style.position = 'absolute';
                this.image.style.left = -(lensleft - bleft + 1) + 'px';
                this.image.style.top = -(lenstop - btop + 1) + 'px';
            }
            
            largeimage[a.href].setposition();
            
            function overleft(){
                return mousepos.x - (lensdata.w + 2 * 1) / 2 - bleft < smallimagedata.pos.l;
            }
            
            function overright(){
                return mousepos.x + (lensdata.w + 2 * 1) / 2 > smallimagedata.pos.r + bleft;
            }
            
            function overtop(){
                return mousepos.y - (lensdata.h + 2 * 1) / 2 - btop < smallimagedata.pos.t;
            }
            
            function overbottom(){
                return mousepos.y + (lensdata.h + 2 * 1) / 2 > smallimagedata.pos.b + btop;
            }
            
            return this;
        };
        
        Lens.prototype.center = function(){
            this.node.hide();
            var lensleft = (smallimagedata.w) / 2 - (lensdata.w) / 2;
            var lenstop = (smallimagedata.h) / 2 - (lensdata.h) / 2;
            this.node.setStyle({'top': lenstop + 'px', 'left': lensleft + 'px'});
            
            if (settings.zoomType == 'reverse') {
                this.image.setStyle({
                    'position': 'absolute',
                    'top': -(lenstop - btop + 1) + 'px',
                    'left': -(lensleft - bleft + 1) + 'px'
                });
            }
            
            largeimage[a.href].setposition();
            
            if (Prototype.Browser.IE) {
                this.node.show();
            } else {
                this.node.setOpacity(0).show();
                Effect.Fade(this.node, {
                    duration: 0.2,
                    from: 0,
                    to: (settings.zoomType == 'reverse') ? 1 : settings.lensOpacity
                });
            }
        };
        
        Lens.prototype.getoffset = function(){
            var o = {};
            o.left = parseInt(this.node.style.left);
            o.top = parseInt(this.node.style.top);
            return o;
        };
        
        Lens.prototype.remove = function(){
            if (!this.node.parentNode) {
                return false;
            }
            if (settings.zoomType == 'innerzoom') {
                Effect.Fade(this.node, {
                    duration: 0.2,
                    from: 1,
                    to: 0,
                    afterFinish: function(){
                        this.node.remove();
                    }.bind(this)
                });
            } else {
                this.node.remove(); 
            }
        };
        
        function Largeimage(url){
            this.url = url;
            this.node = new Image();
            
            this.loadimage = function(){
                if (!this.node) {
                    this.node = new Image();
                }
                this.node.style.position = 'absolute';
                this.node.style.display = 'none';
                this.node.style.left = '-5000px';
                this.node.style.top = '10px';
                
                if (!loader) {
                    loader = new Loader();
                }
                
                if (settings.showPreload && !preloadshow) {
                    loader.show();
                }
                
                document.body.appendChild(this.node);
                this.node.src = this.url; 
            }
            
            this.node.onload = function(){
                this.style.display = 'block';
                var w = Math.round($(this).getWidth());
                var h = Math.round($(this).getHeight());
                this.style.display = 'none';
                
                if (!scale[a.href]) {
                    scale[a.href] = {};
                }
                scale[a.href].x = (w / smallimagedata.w);
                scale[a.href].y = (h / smallimagedata.h);
                
                loader.hide();
                largeimageloaded[a.href] = true;
                activatestage[a.href] = scale[a.href].x > 1 || scale[a.href].y > 1;
                
                if (typeof(activatestage[a.href]) != 'undefined' && activatestage[a.href] == false) {
                    a.style.cursor = settings.lensCursorSm;
                    return false;
                }
                
                if (settings.zoomType != 'innerzoom' && running && !stage[a.href]) {
                    stage[a.href] = new Stage();
                    stage[a.href].activate();
                }
                
                if (running) {
                    lens.activate();
                }
            }
            
            this.node.onerror = function(){
                loader.hide();
                informer.show();
                informer.node.update(settings.errorMsg);
            }
            
            return this;
        }
        
        Largeimage.prototype.setposition = function(){
            this.node.style.left = Math.ceil(-scale[a.href].x * parseInt(lens.getoffset().left) + bleft) + 'px';
            this.node.style.top = Math.ceil(-scale[a.href].y * parseInt(lens.getoffset().top) + btop) + 'px';
        };
        
        Largeimage.prototype.setinner = function(e){
            var left = Math.ceil(-scale[a.href].x * Math.abs(e.pageX - smallimagedata.pos.l));
            if (Math.abs(left) > (this.node.width - smallimagedata.w)) {
                left = -(this.node.width - smallimagedata.w);
            }
            var top = Math.ceil(-scale[a.href].y * Math.abs(e.pageY - smallimagedata.pos.t));
            if (Math.abs(top) > (this.node.height - smallimagedata.h)) {
                top = -(this.node.height - smallimagedata.h);
            }
            if (settings.zoomType == 'innerzoom') {
                lens.node.down('img').setStyle({
                    'position': 'absolute',
                    'top': top + 'px',
                    'left': left + 'px'
                });
            } else {
                this.node.style.left = left + 'px';
                this.node.style.top = top + 'px';
            }
        };
        
        Largeimage.prototype.setcenter = function(){
            this.node.style.left = Math.ceil(-scale[a.href].x * Math.abs((smallimagedata.w) / 2)) + 'px';
            this.node.style.top = Math.ceil(-scale[a.href].y * Math.abs((smallimagedata.h) / 2)) + 'px';
            if (settings.zoomType == 'innerzoom') {
                lens.node.down('img').setStyle({
                    'position': 'absolute',
                    'top': this.node.style.top,
                    'left': this.node.style.left
                });
            }
        };
        
        function Stage(){
            var leftpos = smallimagedata.pos.l;
            var toppos = smallimagedata.pos.t;
            this.node = new Element('div', {'class': 'zoom-window'});
            this.node.addClassName('zoom-window'); 
            
            $(this.node).setStyle({
                position: 'absolute',
                width: Math.round(settings.zoomWidth) + 'px',
                height: Math.round(settings.zoomHeight) + 'px',
                display: 'none',
                zIndex: 10000,
                overflow: 'hidden'
            });
            
            switch (settings.position) {
                case "right":
                    leftpos = (smallimagedata.pos.r + Math.abs(settings.xOffset) + settings.zoomWidth < screen.width) ? (smallimagedata.pos.l + smallimagedata.w + Math.abs(settings.xOffset)) : (smallimagedata.pos.l - settings.zoomWidth - Math.abs(settings.xOffset));
                    topwindow = smallimagedata.pos.t + settings.yOffset + settings.zoomHeight;
                    toppos = (topwindow < screen.height && topwindow > 0) ? smallimagedata.pos.t + settings.yOffset : smallimagedata.pos.t;
                    break;
                case "left":
                    leftpos = (smallimagedata.pos.l - Math.abs(settings.xOffset) - settings.zoomWidth > 0) ? (smallimagedata.pos.l - Math.abs(settings.xOffset) - settings.zoomWidth) : (smallimagedata.pos.l + smallimagedata.w + Math.abs(settings.xOffset));
                    topwindow = smallimagedata.pos.t + settings.yOffset + settings.zoomHeight;
                    toppos = (topwindow < screen.height && topwindow > 0) ? smallimagedata.pos.t + settings.yOffset : smallimagedata.pos.t;
                    break;
                case "top":
                    toppos = (smallimagedata.pos.t - Math.abs(settings.yOffset) - settings.zoomHeight > 0) ? (smallimagedata.pos.t - Math.abs(settings.yOffset) - settings.zoomHeight) : (smallimagedata.pos.t + smallimagedata.h + Math.abs(settings.yOffset));
                    leftwindow = smallimagedata.pos.l + settings.xOffset + settings.zoomWidth;
                    leftpos = (leftwindow < screen.width && leftwindow > 0) ? smallimagedata.pos.l + settings.xOffset : smallimagedata.pos.l;
                    break;
                case "bottom":
                    toppos = (smallimagedata.pos.b + Math.abs(settings.yOffset) + settings.zoomHeight < (document.viewport.getHeight() + document.viewport.getScrollOffsets().top)) ? (smallimagedata.pos.b + Math.abs(settings.yOffset)) : (smallimagedata.pos.t - settings.zoomHeight - Math.abs(settings.yOffset));
                    leftwindow = smallimagedata.pos.l + settings.xOffset + settings.zoomWidth;
                    leftpos = (leftwindow < screen.width && leftwindow > 0) ? smallimagedata.pos.l + settings.xOffset : smallimagedata.pos.l;
                    break;
                default:
                    leftpos = (smallimagedata.pos.l + smallimagedata.w + settings.xOffset + settings.zoomWidth < screen.width) ? (smallimagedata.pos.l + smallimagedata.w + Math.abs(settings.xOffset)) : (smallimagedata.pos.l - settings.zoomWidth - Math.abs(settings.xOffset));
                    toppos = (smallimagedata.pos.b + Math.abs(settings.yOffset) + settings.zoomHeight < screen.height) ? (smallimagedata.pos.b + Math.abs(settings.yOffset)) : (smallimagedata.pos.t - settings.zoomHeight - Math.abs(settings.yOffset));
                    break;
            }
            
            this.node.style.left = leftpos + 'px';
            this.node.style.top = toppos + 'px';
            return this;
        }
        
        Stage.prototype.activate = function(){
            $$('.zoom-window').invoke('hide');
            if (!this.node.parentNode) {
                if (!this.node.firstChild) {
                    this.node.appendChild(largeimage[a.href].node);
                }
                if (settings.title) {
                    ZoomTitleObj.loadtitle();
                }
                document.body.appendChild(this.node);
            }
            
            switch (settings.showEffect) {
                case 'none':
                    $(this.node).show();
                    break;
                case 'fade':
                    $(this.node).setOpacity(0).show();
                    Effect.Fade($(this.node), {
                        from: 0,
                        to: 1,
                        duration: settings.showSpeed
                    });
                    break;
                default:
                    $(this.node).show();
                    break;
            }
            
            if (Prototype.Browser.IE && parseInt(navigator.userAgent.substring(navigator.userAgent.indexOf("MSIE") + 5)) == 6) {
                this.ieframe = new Element('iframe', {
                    'class': 'zoom_ieframe',
                    'frameborder': '0',
                    'src': '#'
                });
                $(this.ieframe).setStyle({
                    position: 'absolute',
                    left: this.node.style.left,
                    top: this.node.style.top,
                    zIndex: 99,
                    width: settings.zoomWidth,
                    height: settings.zoomHeight
                });
                $(this.node).insert({'before': this.ieframe});
            };
            if (settings.alwaysOn && lens.image) {
                lens.center();
            }
            largeimage[a.href].node.style.display = 'block';
        }
        
        Stage.prototype.remove = function(){
            switch (settings.hideEffect) {
                case 'none':
                    this.node.remove();
                    break;
                case 'fade':
                    $(this.node).setOpacity(1);
                    Effect.Fade($(this.node), {
                        from: 1,
                        to: 0,
                        duration: settings.hideSpeed,
                        afterFinish: function(){
                            this.node.remove();
                        }.bind(this)
                    });
                    break;
                default:
                    this.node.remove();
                    break;
            }
        }
        
        function zoomTitle(){
            this.node = new Element('div', {'class': 'zoom-title'}).update(ZoomTitle);
            this.node.addClassName('zoom-title'); 
            
            this.loadtitle = function(){
                if (!ZoomTitle || !ZoomTitle.length) {
                    return false;
                }
                if (settings.zoomType == 'innerzoom') {
                    $(this.node).setStyle({
                        position: 'absolute',
                        top: 0/*smallimagedata.pos.b + 3*/,
                        left: 0/* (smallimagedata.pos.l + 1)*/,
                        width: smallimagedata.w
                    });
                    lens.node.insert({'bottom': this.node});
                } else {
                    stage[a.href].node.insert({'bottom':this.node});
                }
            };
            
            this.updateTitle = function(){
                this.node.update(ZoomTitle);
            }
        }
        
        zoomTitle.prototype.remove = function(){
            if (this.node.parentNode) {
                this.node.remove();
            }
        }
        
        function Loader(){
            this.node = new Element('div', {'class': 'spinner'})
                .setStyle({'visibility': 'hidden'})
                .update(settings.preloadText);
            this.node.addClassName('spinner'); 
            document.body.appendChild(this.node);
            
            this.show = function(){
                preloadshow = true;
                loadertop = smallimagedata.pos.t + (smallimagedata.h - $(this.node).getHeight()) / 2;
                loaderleft = smallimagedata.pos.l + (smallimagedata.w - $(this.node).getWidth()) / 2;
                
                $(this.node).setStyle({
                    top: loadertop + 'px',
                    left: loaderleft + 'px',
                    position: 'absolute',
                    visibility: 'visible'
                });
            }
            
            this.hide = function(){
                preloadshow = false;
                this.node.setStyle({'visibility': 'hidden'});
            }
            
            return this;
        }
        
        function Informer(){
            this.node = new Element('div', {'class': 'informer'})
                .setStyle({'visibility': 'hidden'});
                
            this.node.addClassName('informer'); 
            
            document.body.appendChild(this.node);
            
            this.show = function(){
                informertop = smallimagedata.pos.t + (smallimagedata.h - $(this.node).getHeight()) / 2;
                informerleft = smallimagedata.pos.l + (smallimagedata.w - $(this.node).getWidth()) / 2;
                
                $(this.node).setStyle({
                    top: informertop + 'px',
                    left: informerleft + 'px',
                    position: 'absolute',
                    visibility: 'visible'
                });
            }
            
            this.hide = function(){
                this.node.setStyle({'visibility': 'hidden'});
            }
            
            return this;
        }
        
    });
};

function trim(stringa){
    while (stringa.substring(0, 1) == ' ') {
        stringa = stringa.substring(1, stringa.length);
    }
    while (stringa.substring(stringa.length - 1, stringa.length) == ' ') {
        stringa = stringa.substring(0, stringa.length - 1);
    }
    return stringa;
}


(function(){
    var withinElement = function(evt, el){
        var parent = evt.relatedTarget;
        
        while (parent && parent != el) {
            try {
                parent = parent.parentNode;
            } 
            catch (error) {
                parent = el;
            }
        }
        return parent == el;
    };
    
    Object.extend(Event, {
        mouseEnter: function(element, f, options){
            element = $(element);
            
            var fc = (options && options.enterDelay) ? (function(){
                window.setTimeout(f, options.enterDelay);
            }) : (f);
            
            if (Prototype.Browser.IE) {
                element.observe('mouseenter', fc);
            }
            else {
                element.hovered = false;
                
                element.observe('mouseover', function(evt){
                    if (!element.hovered) {
                        element.hovered = true;
                        fc(evt);
                    }
                });
            }
        },
        mouseLeave: function(element, f, options){
            element = $(element);
            
            var fc = (options && options.leaveDelay) ? (function(){
                window.setTimeout(f, options.leaveDelay);
            }) : (f);
            
            if (Prototype.Browser.IE) {
                element.observe('mouseleave', fc);
            }
            else {
                element.observe('mouseout', function(evt){
                    var target = Event.element(evt);
                    
                    if (!withinElement(evt, element)) {
                        fc(evt);
                        element.hovered = false;
                    }
                });
            }
        }
    });
    
    Element.addMethods({
        'hover': function(element, mouseEnterFunc, mouseLeaveFunc, options){
            options = Object.extend({}, options) ||
            {};
            Event.mouseEnter(element, mouseEnterFunc, options);
            Event.mouseLeave(element, mouseLeaveFunc, options);
        }
    });
    
})();
