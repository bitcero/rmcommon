const modals = {
  /**
   * Base template used for all modals
   */
  template: function (options) {
    // Extract vars
    const {message, content, title, icon, width, size, id, closeButton, helpButton, color} = options;

    let modal = document.createElement('div');
    modal.className = 'modal fade ';

    if (id) {
      modal.id = id;
    }

    const final_content = content || message;
    const final_width = () => {
      switch (width) {
        case 'small':
          return 'modal-sm';
        case 'large':
          return 'modal-lg';
        case 'extra-large':
          return 'modal-xl';
        default:
          return '';
      }
    }

    modal.setAttribute('tabindex', '-1');
    modal.setAttribute('role', 'dialog');
    modal.setAttribute('aria-labelledby', 'modal-title');
    modal.setAttribute('aria-hidden', 'true');

    modal.innerHTML = `<div class="modal-dialog ${final_width()}" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        ${title ? `<h5 class="modal-title" id="modal-title">${title}</h5>` : ''}
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        ${final_content}
                    </div>
                    <div class="modal-footer d-flex align-items-center justify-content-end justify-content-between">
                        ${closeButton ? `<button type="button" class="btn btn-link" data-bs-dismiss="modal">Close</button>` : ''}
                    </div>
                </div>
            </div>`;

    return modal;
  },

  show: function (options) {
    const modal = this.template(options);
    document.body.appendChild(modal);
    let bsModal = new bootstrap.Modal(modal);
    bsModal.show();
    modal.addEventListener('hidden.bs.modal', event => {
      modal.remove();
    });
    return modal;
  },
};

modals.dialog = modals.show;

(function () {
  // Detach all modals with class attach-to-body and attach them to body
  let modals = document.querySelectorAll('.modal.attach-to-body');
  modals.forEach(modal => {
    document.body.appendChild(modal);
  });

  modals = document.querySelectorAll('.modal[data-cu-body-attach]');
  modals.forEach(modal => {
    document.body.appendChild(modal);
  });

  // Show all modal elements with attribute data-cu-show
  let show = document.querySelectorAll('.modal[data-cu-show]');
  show.forEach(modal => {
    let bsModal = new bootstrap.Modal(modal);
    bsModal.show();
  });
})();
/*!
 XOOPS Spinner Plugin
 Author: Eduardo Cortés (AKA bitcero)
 Copyright: © 2015 The Xoops Project (http://xoops.org)
 License: GPL 2 or later
 */
(function ($) {

    $.fn.cuSpinner = function (options) {

        /**
         * icon can be any XOOPS SVG icon or other
         * type must be spin or pulse
         * spinner can be show or hide
         */
        var settings = $.extend({
            icon: 'svg-rmcommon-spinner-06',
            type: 'spin',
            hide: 'cu-icon',
            class: '',
            steps: 12, // Only useful when type equal to 'pulse'
            speed: 3    // 1 to 5 - only valid when type equal to spin
        }, options);

        settings.type = settings.type != 'spin' && settings.type != 'pulse' ? 'spin' : settings.type;

        var style = settings.type == 'pulse' ? 'animation: cu-spin 1s infinite steps('+settings.steps+')' : 'animation: cu-spin '+((2/6) * settings.speed)+'s infinite linear;'

        /**
         * Container must have a child with class xo-icon-sv
         * other wise this plugin could cause conflicts
         */
        return this.each(function () {

            var el = $(this);
            var theIcon = cuHandler.getIcon(settings.icon);

            if (!theIcon) {
                theIcon = cuHandler.getIcon('svg-rmcommon-spinner-06');
            }

            /**
             * Verify if spinner is pressent. If yes we need to remove it
             * and show the other icon
             */
            var exists = $(this).find('.cu-spinner');
            if (exists.length > 0) {
                $(exists).remove();
                $(this).find('.cu-spinner-hide').removeClass('cu-spinner-hide').show();
                return true;
            }

            /**
             * Add the spinner
             */
            var spinner = $("<span />", {class: 'cu-icon cu-spinner' + (settings.class != '' ? ' ' + settings.class : '')});
            spinner.load(theIcon, function () {
                // Hide the required class
                var toHide = $(el).find('.' + settings.hide);
                if(toHide.length > 0){
                    toHide.addClass('cu-spinner-hide').hide();
                    $(toHide[0]).after(spinner);
                } else {
                    $(el).prepend(spinner);
                }
            });

            return true;

        });

    }

}(jQuery));
/*
PNotify 2.1.0 sciactive.com/pnotify/
(C) 2015 Hunter Perrin
license GPL/LGPL/MPL
*/
/*
 * ====== PNotify ======
 *
 * http://sciactive.com/pnotify/
 *
 * Copyright 2009-2015 Hunter Perrin
 *
 * Triple licensed under the GPL, LGPL, and MPL.
 * 	http://gnu.org/licenses/gpl.html
 * 	http://gnu.org/licenses/lgpl.html
 * 	http://mozilla.org/MPL/MPL-1.1.html
 */

(function (factory) {
    if (typeof exports === 'object' && typeof module !== 'undefined') {
        // CommonJS
        module.exports = factory(require('jquery'));
    } else if (typeof define === 'function' && define.amd) {
        // AMD. Register as a module.
        define('pnotify', ['jquery'], factory);
    } else {
        // Browser globals
        factory(jQuery);
    }
}(function($){
    var default_stack = {
        dir1: "down",
        dir2: "left",
        push: "bottom",
        spacing1: 5,
        spacing2: 5,
        context: $("body")
    };
    var posTimer, // Position all timer.
        body,
        jwindow = $(window);
    // Set global variables.
    var do_when_ready = function(){
        body = $("body");
        PNotify.prototype.options.stack.context = body;
        jwindow = $(window);
        // Reposition the notices when the window resizes.
        jwindow.bind('resize', function(){
            if (posTimer) {
                clearTimeout(posTimer);
            }
            posTimer = setTimeout(function(){
                PNotify.positionAll(true);
            }, 10);
        });
    };
    PNotify = function(options){
        this.parseOptions(options);
        this.init();
    };
    $.extend(PNotify.prototype, {
        // The current version of PNotify.
        version: "2.1.0",

        // === Options ===

        // Options defaults.
        options: {
            // The notice's title.
            title: false,
            // Whether to escape the content of the title. (Not allow HTML.)
            title_escape: false,
            // The notice's text.
            text: false,
            // Whether to escape the content of the text. (Not allow HTML.)
            text_escape: false,
            // What styling classes to use. (Can be either "brighttheme", "jqueryui", "bootstrap2", "bootstrap3", or "fontawesome".)
            styling: "brighttheme",
            // Additional classes to be added to the notice. (For custom styling.)
            addclass: "",
            // Class to be added to the notice for corner styling.
            cornerclass: "",
            // Display the notice when it is created.
            auto_display: true,
            // Width of the notice.
            width: "300px",
            // Minimum height of the notice. It will expand to fit content.
            min_height: "16px",
            // Type of the notice. "notice", "info", "success", or "error".
            type: "notice",
            // Set icon to true to use the default icon for the selected
            // style/type, false for no icon, or a string for your own icon class.
            icon: true,
            // Opacity of the notice.
            opacity: 1,
            // The animation to use when displaying and hiding the notice. "none",
            // "show", "fade", and "slide" are built in to jQuery. Others require jQuery
            // UI. Use an object with effect_in and effect_out to use different effects.
            animation: "fade",
            // Speed at which the notice animates in and out. "slow", "def" or "normal",
            // "fast" or number of milliseconds.
            animate_speed: "slow",
            // Specify a specific duration of position animation
            position_animate_speed: 500,
            // Display a drop shadow.
            shadow: true,
            // After a delay, remove the notice.
            hide: true,
            // Delay in milliseconds before the notice is removed.
            delay: 8000,
            // Reset the hide timer if the mouse moves over the notice.
            mouse_reset: true,
            // Remove the notice's elements from the DOM after it is removed.
            remove: true,
            // Change new lines to br tags.
            insert_brs: true,
            // Whether to remove notices from the global array.
            destroy: true,
            // The stack on which the notices will be placed. Also controls the
            // direction the notices stack.
            stack: default_stack
        },

        // === Modules ===

        // This object holds all the PNotify modules. They are used to provide
        // additional functionality.
        modules: {},
        // This runs an event on all the modules.
        runModules: function(event, arg){
            var curArg;
            for (var module in this.modules) {
                curArg = ((typeof arg === "object" && module in arg) ? arg[module] : arg);
                if (typeof this.modules[module][event] === 'function') {
                    this.modules[module][event](this, typeof this.options[module] === 'object' ? this.options[module] : {}, curArg);
                }
            }
        },

        // === Class Variables ===

        state: "initializing", // The state can be "initializing", "opening", "open", "closing", and "closed".
        timer: null, // Auto close timer.
        styles: null,
        elem: null,
        container: null,
        title_container: null,
        text_container: null,
        animating: false, // Stores what is currently being animated (in or out).
        timerHide: false, // Stores whether the notice was hidden by a timer.

        // === Events ===

        init: function(){
            var that = this;

            // First and foremost, we don't want our module objects all referencing the prototype.
            this.modules = {};
            $.extend(true, this.modules, PNotify.prototype.modules);

            // Get our styling object.
            if (typeof this.options.styling === "object") {
                this.styles = this.options.styling;
            } else {
                this.styles = PNotify.styling[this.options.styling];
            }

            // Create our widget.
            // Stop animation, reset the removal timer when the user mouses over.
            this.elem = $("<div />", {
                "class": "ui-pnotify "+this.options.addclass,
                "css": {"display": "none"},
                "aria-live": "assertive",
                "mouseenter": function(e){
                    if (that.options.mouse_reset && that.animating === "out") {
                        if (!that.timerHide) {
                            return;
                        }
                        that.cancelRemove();
                    }
                    // Stop the close timer.
                    if (that.options.hide && that.options.mouse_reset) {
                        that.cancelRemove();
                    }
                },
                "mouseleave": function(e){
                    // Start the close timer.
                    if (that.options.hide && that.options.mouse_reset && that.animating !== "out") {
                        that.queueRemove();
                    }
                    PNotify.positionAll();
                }
            });
            // Create a container for the notice contents.
            this.container = $("<div />", {
                "class": 'alert ' + this.options.type,//this.styles.container+" ui-pnotify-container "+(this.options.type === "error" ? this.styles.error : (this.options.type === "info" ? this.styles.info : (this.options.type === "success" ? this.styles.success : this.styles.notice))),
                "role": "alert"
            }).appendTo(this.elem);
            if (this.options.cornerclass !== "") {
                this.container.removeClass("ui-corner-all").addClass(this.options.cornerclass);
            }
            // Create a drop shadow.
            if (this.options.shadow) {
                this.container.addClass("ui-pnotify-shadow");
            }


            // Add the appropriate icon.
            if (this.options.icon !== false) {

                if(typeof this.options.icon == 'string'){

                    var c = $("<div />", {"class": "ui-pnotify-icon"});
                    cuHandler.loadIcon(this.options.icon, c);
                    c.prependTo(this.container);

                } else {
                    // Build the new icon.
                    $("<div />", {"class": "ui-pnotify-icon"})
                        .append($("<span />", {"class": this.options.icon === true ? (this.options.type === "error" ? this.styles.error_icon : (this.options.type === "info" ? this.styles.info_icon : (this.options.type === "success" ? this.styles.success_icon : this.styles.notice_icon))) : this.options.icon}))
                        .prependTo(this.container);
                }

            }

            // Add a title.
            this.title_container = $("<h4 />", {
                "class": "ui-pnotify-title"
            })
            .appendTo(this.container);
            if (this.options.title === false) {
                this.title_container.hide();
            } else if (this.options.title_escape) {
                this.title_container.text(this.options.title);
            } else {
                this.title_container.html(this.options.title);
            }

            // Add text.
            this.text_container = $("<div />", {
                "class": "ui-pnotify-text"
            })
            .appendTo(this.container);
            if (this.options.text === false) {
                this.text_container.hide();
            } else if (this.options.text_escape) {
                this.text_container.text(this.options.text);
            } else {
                this.text_container.html(this.options.insert_brs ? String(this.options.text).replace(/\n/g, "<br />") : this.options.text);
            }

            // Set width and min height.
            /*if (typeof this.options.width === "string") {
                this.elem.css("width", this.options.width);
            }*/
            if (typeof this.options.min_height === "string") {
                this.container.css("min-height", this.options.min_height);
            }


            // Add the notice to the notice array.
            if (this.options.stack.push === "top") {
                PNotify.notices = $.merge([this], PNotify.notices);
            } else {
                PNotify.notices = $.merge(PNotify.notices, [this]);
            }
            // Now position all the notices if they are to push to the top.
            if (this.options.stack.push === "top") {
                this.queuePosition(false, 1);
            }




            // Mark the stack so it won't animate the new notice.
            this.options.stack.animation = false;

            // Run the modules.
            this.runModules('init');

            // Display the notice.
            if (this.options.auto_display) {
                this.open();
            }
            return this;
        },

        // This function is for updating the notice.
        update: function(options){
            // Save old options.
            var oldOpts = this.options;
            // Then update to the new options.
            this.parseOptions(oldOpts, options);
            // Update the corner class.
            if (this.options.cornerclass !== oldOpts.cornerclass) {
                this.container.removeClass("ui-corner-all "+oldOpts.cornerclass).addClass(this.options.cornerclass);
            }
            // Update the shadow.
            if (this.options.shadow !== oldOpts.shadow) {
                if (this.options.shadow) {
                    this.container.addClass("ui-pnotify-shadow");
                } else {
                    this.container.removeClass("ui-pnotify-shadow");
                }
            }
            // Update the additional classes.
            if (this.options.addclass === false) {
                this.elem.removeClass(oldOpts.addclass);
            } else if (this.options.addclass !== oldOpts.addclass) {
                this.elem.removeClass(oldOpts.addclass).addClass(this.options.addclass);
            }
            // Update the title.
            if (this.options.title === false) {
                this.title_container.slideUp("fast");
            } else if (this.options.title !== oldOpts.title) {
                if (this.options.title_escape) {
                    this.title_container.text(this.options.title);
                } else {
                    this.title_container.html(this.options.title);
                }
                if (oldOpts.title === false) {
                    this.title_container.slideDown(200)
                }
            }
            // Update the text.
            if (this.options.text === false) {
                this.text_container.slideUp("fast");
            } else if (this.options.text !== oldOpts.text) {
                if (this.options.text_escape) {
                    this.text_container.text(this.options.text);
                } else {
                    this.text_container.html(this.options.insert_brs ? String(this.options.text).replace(/\n/g, "<br />") : this.options.text);
                }
                if (oldOpts.text === false) {
                    this.text_container.slideDown(200)
                }
            }
            // Change the notice type.
            if (this.options.type !== oldOpts.type)
                this.container.removeClass(
                    this.styles.error+" "+this.styles.notice+" "+this.styles.success+" "+this.styles.info
                ).addClass(this.options.type);
            if (this.options.icon !== oldOpts.icon || (this.options.icon === true && this.options.type !== oldOpts.type)) {
                // Remove any old icon.
                this.container.find("div.ui-pnotify-icon").remove();
                if (this.options.icon !== false) {

                    if(typeof this.options.icon == 'string'){
                        // The icon is a svg icon

                        var c = $("<div />", {"class": "ui-pnotify-icon"});
                        cuHandler.loadIcon(this.options.icon, c);
                        c.prependTo(this.container);

                    } else {
                        // Build the new icon.
                        $("<div />", {"class": "ui-pnotify-icon"})
                            .append($("<span />", {"class": this.options.icon === true ? (this.options.type === "error" ? this.styles.error_icon : (this.options.type === "info" ? this.styles.info_icon : (this.options.type === "success" ? this.styles.success_icon : this.styles.notice_icon))) : this.options.icon}))
                            .prependTo(this.container);
                    }
                }
            }
            // Update the width.
            /*if (this.options.width !== oldOpts.width) {
                this.elem.animate({width: this.options.width});
            }*/
            // Update the minimum height.
            if (this.options.min_height !== oldOpts.min_height) {
                this.container.animate({minHeight: this.options.min_height});
            }
            // Update the opacity.
            if (this.options.opacity !== oldOpts.opacity) {
                this.elem.fadeTo(this.options.animate_speed, this.options.opacity);
            }
            // Update the timed hiding.
            if (!this.options.hide) {
                this.cancelRemove();
            } else if (!oldOpts.hide) {
                this.queueRemove();
            }
            this.queuePosition(true);

            // Run the modules.
            this.runModules('update', oldOpts);
            return this;
        },

        // Display the notice.
        open: function(){
            this.state = "opening";
            // Run the modules.
            this.runModules('beforeOpen');

            var that = this;
            // If the notice is not in the DOM, append it.
            if (!this.elem.parent().length) {
                this.elem.appendTo(this.options.stack.context ? this.options.stack.context : body);
            }
            // Try to put it in the right position.
            if (this.options.stack.push !== "top") {
                this.position(true);
            }
            // First show it, then set its opacity, then hide it.
            if (this.options.animation === "fade" || this.options.animation.effect_in === "fade") {
                // If it's fading in, it should start at 0.
                this.elem.show().fadeTo(0, 0).hide();
            } else {
                // Or else it should be set to the opacity.
                if (this.options.opacity !== 1) {
                    this.elem.show().fadeTo(0, this.options.opacity).hide();
                }
            }
            this.animateIn(function(){
                that.queuePosition(true);

                // Now set it to hide.
                if (that.options.hide) {
                    that.queueRemove();
                }

                that.state = "open";

                // Run the modules.
                that.runModules('afterOpen');
            });

            return this;
        },

        // Remove the notice.
        remove: function(timer_hide) {
            this.state = "closing";
            this.timerHide = !!timer_hide; // Make sure it's a boolean.
            // Run the modules.
            this.runModules('beforeClose');

            var that = this;
            if (this.timer) {
                window.clearTimeout(this.timer);
                this.timer = null;
            }
            this.animateOut(function(){
                that.state = "closed";
                // Run the modules.
                that.runModules('afterClose');
                that.queuePosition(true);
                // If we're supposed to remove the notice from the DOM, do it.
                if (that.options.remove)
                    that.elem.detach();
                // Run the modules.
                that.runModules('beforeDestroy');
                // Remove object from PNotify.notices to prevent memory leak (issue #49)
                // unless destroy is off
                if (that.options.destroy) {
                    if (PNotify.notices !== null) {
                        var idx = $.inArray(that,PNotify.notices);
                        if (idx !== -1) {
                            PNotify.notices.splice(idx,1);
                        }
                    }
                }
                // Run the modules.
                that.runModules('afterDestroy');
            });

            return this;
        },

        // === Class Methods ===

        // Get the DOM element.
        get: function(){
            return this.elem;
        },

        // Put all the options in the right places.
        parseOptions: function(options, moreOptions){
            this.options = $.extend(true, {}, PNotify.prototype.options);
            // This is the only thing that *should* be copied by reference.
            this.options.stack = PNotify.prototype.options.stack;
            var optArray = [options, moreOptions], curOpts;
            for (var curIndex=0; curIndex < optArray.length; curIndex++) {
                curOpts = optArray[curIndex];
                if (typeof curOpts == "undefined") {
                    break;
                }
                if (typeof curOpts !== 'object') {
                    this.options.text = curOpts;
                } else {
                    for (var option in curOpts) {
                        if (this.modules[option]) {
                            // Avoid overwriting module defaults.
                            $.extend(true, this.options[option], curOpts[option]);
                        } else {
                            this.options[option] = curOpts[option];
                        }
                    }
                }
            }
        },

        // Animate the notice in.
        animateIn: function(callback){
            // Declare that the notice is animating in. (Or has completed animating in.)
            this.animating = "in";
            var animation;
            if (typeof this.options.animation.effect_in !== "undefined") {
                animation = this.options.animation.effect_in;
            } else {
                animation = this.options.animation;
            }
            if (animation === "none") {
                this.elem.show();
                callback();
            } else if (animation === "show") {
                this.elem.show(this.options.animate_speed, callback);
            } else if (animation === "fade") {
                this.elem.show().fadeTo(this.options.animate_speed, this.options.opacity, callback);
            } else if (animation === "slide") {
                this.elem.slideDown(this.options.animate_speed, callback);
            } else if (typeof animation === "function") {
                animation("in", callback, this.elem);
            } else {
                this.elem.show(animation, (typeof this.options.animation.options_in === "object" ? this.options.animation.options_in : {}), this.options.animate_speed, callback);
            }
            if (this.elem.parent().hasClass('ui-effects-wrapper')) {
                this.elem.parent().css({
                    "position": "fixed",
                    "overflow": "visible"
                });
            }
            if (animation !== "slide") {
                this.elem.css("overflow", "visible");
            }
            this.container.css("overflow", "hidden");
        },

        // Animate the notice out.
        animateOut: function(callback){
            // Declare that the notice is animating out. (Or has completed animating out.)
            this.animating = "out";
            var animation;
            if (typeof this.options.animation.effect_out !== "undefined") {
                animation = this.options.animation.effect_out;
            } else {
                animation = this.options.animation;
            }
            if (animation === "none") {
                this.elem.hide();
                callback();
            } else if (animation === "show") {
                this.elem.hide(this.options.animate_speed, callback);
            } else if (animation === "fade") {
                this.elem.fadeOut(this.options.animate_speed, callback);
            } else if (animation === "slide") {
                this.elem.slideUp(this.options.animate_speed, callback);
            } else if (typeof animation === "function") {
                animation("out", callback, this.elem);
            } else {
                this.elem.hide(animation, (typeof this.options.animation.options_out === "object" ? this.options.animation.options_out : {}), this.options.animate_speed, callback);
            }
            if (this.elem.parent().hasClass('ui-effects-wrapper')) {
                this.elem.parent().css({
                    "position": "fixed",
                    "overflow": "visible"
                });
            }
            if (animation !== "slide") {
                this.elem.css("overflow", "visible");
            }
            this.container.css("overflow", "hidden");
        },

        // Position the notice. dont_skip_hidden causes the notice to
        // position even if it's not visible.
        position: function(dontSkipHidden){
            // Get the notice's stack.
            var s = this.options.stack,
                e = this.elem;
            if (e.parent().hasClass('ui-effects-wrapper')) {
                e = this.elem.css({
                    "left": "0",
                    "top": "0",
                    "right": "0",
                    "bottom": "0"
                }).parent();
            }
            if (typeof s.context === "undefined") {
                s.context = body;
            }
            if (!s) {
                return;
            }
            if (typeof s.nextpos1 !== "number") {
                s.nextpos1 = s.firstpos1;
            }
            if (typeof s.nextpos2 !== "number") {
                s.nextpos2 = s.firstpos2;
            }
            if (typeof s.addpos2 !== "number") {
                s.addpos2 = 0;
            }
            var hidden = e.css("display") === "none";
            // Skip this notice if it's not shown.
            if (!hidden || dontSkipHidden) {
                var curpos1, curpos2;
                // Store what will need to be animated.
                var animate = {};
                // Calculate the current pos1 value.
                var csspos1;
                switch (s.dir1) {
                    case "down":
                        csspos1 = "top";
                        break;
                    case "up":
                        csspos1 = "bottom";
                        break;
                    case "left":
                        csspos1 = "right";
                        break;
                    case "right":
                        csspos1 = "left";
                        break;
                }
                curpos1 = parseInt(e.css(csspos1).replace(/(?:\..*|[^0-9.])/g, ''));
                if (isNaN(curpos1)) {
                    curpos1 = 0;
                }
                // Remember the first pos1, so the first visible notice goes there.
                if (typeof s.firstpos1 === "undefined" && !hidden) {
                    s.firstpos1 = curpos1;
                    s.nextpos1 = s.firstpos1;
                }
                // Calculate the current pos2 value.
                var csspos2;
                switch (s.dir2) {
                    case "down":
                        csspos2 = "top";
                        break;
                    case "up":
                        csspos2 = "bottom";
                        break;
                    case "left":
                        csspos2 = "right";
                        break;
                    case "right":
                        csspos2 = "left";
                        break;
                }
                curpos2 = parseInt(e.css(csspos2).replace(/(?:\..*|[^0-9.])/g, ''));
                if (isNaN(curpos2)) {
                    curpos2 = 0;
                }
                // Remember the first pos2, so the first visible notice goes there.
                if (typeof s.firstpos2 === "undefined" && !hidden) {
                    s.firstpos2 = curpos2;
                    s.nextpos2 = s.firstpos2;
                }
                // Check that it's not beyond the viewport edge.
                if ((s.dir1 === "down" && s.nextpos1 + e.height() > (s.context.is(body) ? jwindow.height() : s.context.prop('scrollHeight')) ) ||
                    (s.dir1 === "up" && s.nextpos1 + e.height() > (s.context.is(body) ? jwindow.height() : s.context.prop('scrollHeight')) ) ||
                    (s.dir1 === "left" && s.nextpos1 + e.width() > (s.context.is(body) ? jwindow.width() : s.context.prop('scrollWidth')) ) ||
                    (s.dir1 === "right" && s.nextpos1 + e.width() > (s.context.is(body) ? jwindow.width() : s.context.prop('scrollWidth')) ) ) {
                    // If it is, it needs to go back to the first pos1, and over on pos2.
                    s.nextpos1 = s.firstpos1;
                    s.nextpos2 += s.addpos2 + (typeof s.spacing2 === "undefined" ? 25 : s.spacing2);
                    s.addpos2 = 0;
                }
                // Animate if we're moving on dir2.
                if (s.animation && s.nextpos2 < curpos2) {
                    switch (s.dir2) {
                        case "down":
                            animate.top = s.nextpos2+"px";
                            break;
                        case "up":
                            animate.bottom = s.nextpos2+"px";
                            break;
                        case "left":
                            animate.right = s.nextpos2+"px";
                            break;
                        case "right":
                            animate.left = s.nextpos2+"px";
                            break;
                    }
                } else {
                    if (typeof s.nextpos2 === "number") {
                        e.css(csspos2, s.nextpos2+"px");
                    }
                }
                // Keep track of the widest/tallest notice in the column/row, so we can push the next column/row.
                switch (s.dir2) {
                    case "down":
                    case "up":
                        if (e.outerHeight(true) > s.addpos2) {
                            s.addpos2 = e.height();
                        }
                        break;
                    case "left":
                    case "right":
                        if (e.outerWidth(true) > s.addpos2) {
                            s.addpos2 = e.width();
                        }
                        break;
                }
                // Move the notice on dir1.
                if (typeof s.nextpos1 === "number") {
                    // Animate if we're moving toward the first pos.
                    if (s.animation && (curpos1 > s.nextpos1 || animate.top || animate.bottom || animate.right || animate.left)) {
                        switch (s.dir1) {
                            case "down":
                                animate.top = s.nextpos1+"px";
                                break;
                            case "up":
                                animate.bottom = s.nextpos1+"px";
                                break;
                            case "left":
                                animate.right = s.nextpos1+"px";
                                break;
                            case "right":
                                animate.left = s.nextpos1+"px";
                                break;
                        }
                    } else {
                        e.css(csspos1, s.nextpos1+"px");
                    }
                }
                // Run the animation.
                if (animate.top || animate.bottom || animate.right || animate.left) {
                    e.animate(animate, {
                        duration: this.options.position_animate_speed,
                        queue: false
                    });
                }
                // Calculate the next dir1 position.
                switch (s.dir1) {
                    case "down":
                    case "up":
                        s.nextpos1 += e.height() + (typeof s.spacing1 === "undefined" ? 5 : s.spacing1);
                        break;
                    case "left":
                    case "right":
                        s.nextpos1 += e.width() + (typeof s.spacing1 === "undefined" ? 5 : s.spacing1);
                        break;
                }
            }

            return this;
        },
        // Queue the position all function so it doesn't run repeatedly and
        // use up resources.
        queuePosition: function(animate, milliseconds){
            if (posTimer) {
                clearTimeout(posTimer);
            }
            if (!milliseconds) {
                milliseconds = 10;
            }
            posTimer = setTimeout(function(){
                PNotify.positionAll(animate);
            }, milliseconds);
            return this;
        },


        // Cancel any pending removal timer.
        cancelRemove: function(){
            if (this.timer) {
                window.clearTimeout(this.timer);
            }
            if (this.state === "closing") {
                // If it's animating out, animate back in really quickly.
                this.elem.stop(true);
                this.state = "open";
                this.animating = "in";
                this.elem.css("height", "auto").animate({
                    "width": this.options.width,
                    "opacity": this.options.opacity
                }, "fast");
            }
            return this;
        },
        // Queue a removal timer.
        queueRemove: function(){
            var that = this;
            // Cancel any current removal timer.
            this.cancelRemove();
            this.timer = window.setTimeout(function(){
                that.remove(true);
            }, (isNaN(this.options.delay) ? 0 : this.options.delay));
            return this;
        }
    });
    // These functions affect all notices.
    $.extend(PNotify, {
        // This holds all the notices.
        notices: [],
        removeAll: function () {
            $.each(PNotify.notices, function(){
                if (this.remove) {
                    this.remove(false);
                }
            });
        },
        positionAll: function (animate) {
            // This timer is used for queueing this function so it doesn't run
            // repeatedly.
            if (posTimer) {
                clearTimeout(posTimer);
            }
            posTimer = null;
            // Reset the next position data.
            if (PNotify.notices && PNotify.notices.length) {
                $.each(PNotify.notices, function(){
                    var s = this.options.stack;
                    if (!s) {
                        return;
                    }
                    s.nextpos1 = s.firstpos1;
                    s.nextpos2 = s.firstpos2;
                    s.addpos2 = 0;
                    s.animation = animate;
                });
                $.each(PNotify.notices, function(){
                    this.position();
                });
            } else {
                var s = PNotify.prototype.options.stack;
                if (s) {
                    delete s.nextpos1;
                    delete s.nextpos2;
                }
            }
        },
        styling: {
            brighttheme: {
                // Bright Theme doesn't require any UI libraries.
                container: "brighttheme",
                notice: "brighttheme-notice",
                notice_icon: "brighttheme-icon-notice",
                info: "brighttheme-info",
                info_icon: "brighttheme-icon-info",
                success: "brighttheme-success",
                success_icon: "brighttheme-icon-success",
                error: "brighttheme-error",
                error_icon: "brighttheme-icon-error"
            },
            jqueryui: {
                container: "ui-widget ui-widget-content ui-corner-all",
                notice: "ui-state-highlight",
                // (The actual jQUI notice icon looks terrible.)
                notice_icon: "ui-icon ui-icon-info",
                info: "",
                info_icon: "ui-icon ui-icon-info",
                success: "ui-state-default",
                success_icon: "ui-icon ui-icon-circle-check",
                error: "ui-state-error",
                error_icon: "ui-icon ui-icon-alert"
            },
            bootstrap2: {
                container: "alert",
                notice: "",
                notice_icon: "icon-exclamation-sign",
                info: "alert-info",
                info_icon: "icon-info-sign",
                success: "alert-success",
                success_icon: "icon-ok-sign",
                error: "alert-error",
                error_icon: "icon-warning-sign"
            },
            bootstrap3: {
                container: "alert",
                notice: "alert-warning",
                notice_icon: "glyphicon glyphicon-exclamation-sign",
                info: "alert-info",
                info_icon: "glyphicon glyphicon-info-sign",
                success: "alert-success",
                success_icon: "glyphicon glyphicon-ok-sign",
                error: "alert-danger",
                error_icon: "glyphicon glyphicon-warning-sign"
            }
        }
    });
    /*
     * uses icons from http://fontawesome.io/
     * version 4.0.3
     */
    PNotify.styling.fontawesome = $.extend({}, PNotify.styling.bootstrap3);
    $.extend(PNotify.styling.fontawesome, {
        notice_icon: "fa fa-exclamation-circle",
        info_icon: "fa fa-info",
        success_icon: "fa fa-check",
        error_icon: "fa fa-warning"
    });

    if (document.body) {
        do_when_ready();
    } else {
        $(do_when_ready);
    }
    return PNotify;
}));
// Buttons
// Uses AMD or browser globals for jQuery.
(function (factory) {
    if (typeof exports === 'object' && typeof module !== 'undefined') {
        // CommonJS
        module.exports = factory(require('jquery'), require('pnotify'));
    } else if (typeof define === 'function' && define.amd) {
        // AMD. Register as a module.
        define('pnotify.buttons', ['jquery', 'pnotify'], factory);
    } else {
        // Browser globals
        factory(jQuery, PNotify);
    }
}(function($, PNotify){
    PNotify.prototype.options.buttons = {
        // Provide a button for the user to manually close the notice.
        closer: true,
        // Only show the closer button on hover.
        closer_hover: true,
        // Provide a button for the user to manually stick the notice.
        sticker: true,
        // Only show the sticker button on hover.
        sticker_hover: true,
        // Show the buttons even when the nonblock module is in use.
        show_on_nonblock: false,
        // The various displayed text, helps facilitating internationalization.
        labels: {
            close: "Close",
            stick: "Stick"
        }
    };
    PNotify.prototype.modules.buttons = {
        // This lets us update the options available in the closures.
        myOptions: null,

        closer: null,
        sticker: null,

        init: function(notice, options){
            var that = this;
            this.myOptions = options;
            notice.elem.on({
                "mouseenter": function(e){
                    // Show the buttons.
                    if (that.myOptions.sticker && (!(notice.options.nonblock && notice.options.nonblock.nonblock) || that.myOptions.show_on_nonblock)) {
                        that.sticker.trigger("pnotify_icon").css("visibility", "visible");
                    }
                    if (that.myOptions.closer && (!(notice.options.nonblock && notice.options.nonblock.nonblock) || that.myOptions.show_on_nonblock)) {
                        that.closer.css("visibility", "visible");
                    }
                },
                "mouseleave": function(e){
                    // Hide the buttons.
                    if (that.myOptions.sticker_hover) {
                        that.sticker.css("visibility", "hidden");
                    }
                    if (that.myOptions.closer_hover) {
                        that.closer.css("visibility", "hidden");
                    }
                }
            });

            // Provide a button to stick the notice.
            this.sticker = $("<div />", {
                "class": "ui-pnotify-sticker",
                "css": {
                    "cursor": "pointer",
                    "visibility": options.sticker_hover ? "hidden" : "visible"
                },
                "click": function(){
                    notice.options.hide = !notice.options.hide;
                    if (notice.options.hide) {
                        notice.queueRemove();
                    } else {
                        notice.cancelRemove();
                    }
                    $(this).trigger("pnotify_icon");
                }
            })
            .bind("pnotify_icon", function(){
                    $(this).children().removeClass(notice.styles.pin_up+" "+notice.styles.pin_down).addClass(notice.options.hide ? notice.styles.pin_up : notice.styles.pin_down);
                    $(this).children().load(cuHandler.url("/modules/rmcommon/icons/" + (notice.options.hide ? 'pause.svg' : 'play.svg')))
            })
            .append($("<span />", {
                    "class": notice.styles.pin_up,
                    "title": options.labels.stick
                })
                    .load(cuHandler.url("/modules/rmcommon/icons/pause.svg")))
            .prependTo(notice.container);
            if (!options.sticker || (notice.options.nonblock && notice.options.nonblock.nonblock && !options.show_on_nonblock)) {
                this.sticker.css("display", "none");
            }

            // Provide a button to close the notice.
            this.closer = $("<div />", {
                "class": "ui-pnotify-closer",
                "css": {"cursor": "pointer", "visibility": options.closer_hover ? "hidden" : "visible"},
                "click": function(){
                    notice.remove(false);
                    that.sticker.css("visibility", "hidden");
                    that.closer.css("visibility", "hidden");
                }
            })
            .append($("<span />", {"class": notice.styles.closer, "title": options.labels.close})
                    .load(cuHandler.url('/modules/rmcommon/icons/close.svg')))
            .prependTo(notice.container);
            if (!options.closer || (notice.options.nonblock && notice.options.nonblock.nonblock && !options.show_on_nonblock)) {
                this.closer.css("display", "none");
            }
        },
        update: function(notice, options){
            this.myOptions = options;
            // Update the sticker and closer buttons.
            if (!options.closer || (notice.options.nonblock && notice.options.nonblock.nonblock && !options.show_on_nonblock)) {
                this.closer.css("display", "none");
            } else if (options.closer) {
                this.closer.css("display", "block");
            }
            if (!options.sticker || (notice.options.nonblock && notice.options.nonblock.nonblock && !options.show_on_nonblock)) {
                this.sticker.css("display", "none");
            } else if (options.sticker) {
                this.sticker.css("display", "block");
            }
            // Update the sticker icon.
            this.sticker.trigger("pnotify_icon");
            // Update the hover status of the buttons.
            if (options.sticker_hover) {
                this.sticker.css("visibility", "hidden");
            } else if (!(notice.options.nonblock && notice.options.nonblock.nonblock && !options.show_on_nonblock)) {
                this.sticker.css("visibility", "visible");
            }
            if (options.closer_hover) {
                this.closer.css("visibility", "hidden");
            } else if (!(notice.options.nonblock && notice.options.nonblock.nonblock && !options.show_on_nonblock)) {
                this.closer.css("visibility", "visible");
            }
        }
    };
    $.extend(PNotify.styling.brighttheme, {
        closer: "cu-icon",//"brighttheme-icon-closer",
        pin_up: "cu-icon",
        pin_down: "cu-icon"
    });
    $.extend(PNotify.styling.jqueryui, {
        closer: "ui-icon ui-icon-close",
        pin_up: "ui-icon ui-icon-pin-w",
        pin_down: "ui-icon ui-icon-pin-s"
    });
    $.extend(PNotify.styling.bootstrap2, {
        closer: "icon-remove",
        pin_up: "icon-pause",
        pin_down: "icon-play"
    });
    $.extend(PNotify.styling.bootstrap3, {
        closer: "glyphicon glyphicon-remove",
        pin_up: "glyphicon glyphicon-pause",
        pin_down: "glyphicon glyphicon-play"
    });
    $.extend(PNotify.styling.fontawesome, {
        closer: "fa fa-times",
        pin_up: "fa fa-pause",
        pin_down: "fa fa-play"
    });
}));
// Callbacks
(function (factory) {
    if (typeof exports === 'object' && typeof module !== 'undefined') {
        // CommonJS
        module.exports = factory(require('jquery'), require('pnotify'));
    } else if (typeof define === 'function' && define.amd) {
        // AMD. Register as a module.
        define('pnotify.callbacks', ['jquery', 'pnotify'], factory);
    } else {
        // Browser globals
        factory(jQuery, PNotify);
    }
}(function($, PNotify){
    var _init   = PNotify.prototype.init,
        _open   = PNotify.prototype.open,
        _remove = PNotify.prototype.remove;
    PNotify.prototype.init = function(){
        if (this.options.before_init) {
            this.options.before_init(this.options);
        }
        _init.apply(this, arguments);
        if (this.options.after_init) {
            this.options.after_init(this);
        }
    };
    PNotify.prototype.open = function(){
        var ret;
        if (this.options.before_open) {
            ret = this.options.before_open(this);
        }
        if (ret !== false) {
            _open.apply(this, arguments);
            if (this.options.after_open) {
                this.options.after_open(this);
            }
        }
    };
    PNotify.prototype.remove = function(timer_hide){
        var ret;
        if (this.options.before_close) {
            ret = this.options.before_close(this, timer_hide);
        }
        if (ret !== false) {
            _remove.apply(this, arguments);
            if (this.options.after_close) {
                this.options.after_close(this, timer_hide);
            }
        }
    };
}));
// Confirm
(function (factory) {
    if (typeof exports === 'object' && typeof module !== 'undefined') {
        // CommonJS
        module.exports = factory(require('jquery'), require('pnotify'));
    } else if (typeof define === 'function' && define.amd) {
        // AMD. Register as a module.
        define('pnotify.confirm', ['jquery', 'pnotify'], factory);
    } else {
        // Browser globals
        factory(jQuery, PNotify);
    }
}(function($, PNotify){
    PNotify.prototype.options.confirm = {
        // Make a confirmation box.
        confirm: false,
        // Make a prompt.
        prompt: false,
        // Classes to add to the input element of the prompt.
        prompt_class: "",
        // The default value of the prompt.
        prompt_default: "",
        // Whether the prompt should accept multiple lines of text.
        prompt_multi_line: false,
        // Where to align the buttons. (right, center, left, justify)
        align: "right",
        // The buttons to display, and their callbacks.
        buttons: [
            {
                text: "Ok",
                addClass: "",
                // Whether to trigger this button when the user hits enter in a single line prompt.
                promptTrigger: true,
                click: function(notice, value){
                    notice.remove();
                    notice.get().trigger("pnotify.confirm", [notice, value]);
                }
            },
            {
                text: "Cancel",
                addClass: "",
                click: function(notice){
                    notice.remove();
                    notice.get().trigger("pnotify.cancel", notice);
                }
            }
        ]
    };
    PNotify.prototype.modules.confirm = {
        // The div that contains the buttons.
        container: null,
        // The input element of a prompt.
        prompt: null,

        init: function(notice, options){
            this.container = $('<div style="margin-top:5px;clear:both;" />').css('text-align', options.align).appendTo(notice.container);

            if (options.confirm || options.prompt)
                this.makeDialog(notice, options);
            else
                this.container.hide();
        },

        update: function(notice, options){
            if (options.confirm) {
                this.makeDialog(notice, options);
                this.container.show();
            } else {
                this.container.hide().empty();
            }
        },

        afterOpen: function(notice, options){
            if (options.prompt)
                this.prompt.focus();
        },

        makeDialog: function(notice, options) {
            var already = false, that = this, btn, elem;
            this.container.empty();
            if (options.prompt) {
                this.prompt = $('<'+(options.prompt_multi_line ? 'textarea rows="5"' : 'input type="text"')+' style="margin-bottom:5px;clear:both;" />')
                .addClass(notice.styles.input+' '+options.prompt_class)
                .val(options.prompt_default)
                .appendTo(this.container);
            }
            for (var i in options.buttons) {
                btn = options.buttons[i];
                if (already)
                    this.container.append(' ');
                else
                    already = true;
                elem = $('<button type="button" />')
                .addClass(notice.styles.btn+' '+btn.addClass)
                .text(btn.text)
                .appendTo(this.container)
                .on("click", (function(btn){ return function(){
                    if (typeof btn.click == "function") {
                        btn.click(notice, options.prompt ? that.prompt.val() : null);
                    }
                }})(btn));
                if (options.prompt && !options.prompt_multi_line && btn.promptTrigger)
                    this.prompt.keypress((function(elem){ return function(e){
                        if (e.keyCode == 13)
                            elem.click();
                    }})(elem));
                if (notice.styles.text) {
                    elem.wrapInner('<span class="'+notice.styles.text+'"></span>');
                }
                if (notice.styles.btnhover) {
                    elem.hover((function(elem){ return function(){
                        elem.addClass(notice.styles.btnhover);
                    }})(elem), (function(elem){ return function(){
                        elem.removeClass(notice.styles.btnhover);
                    }})(elem));
                }
                if (notice.styles.btnactive) {
                    elem.on("mousedown", (function(elem){ return function(){
                        elem.addClass(notice.styles.btnactive);
                    }})(elem)).on("mouseup", (function(elem){ return function(){
                        elem.removeClass(notice.styles.btnactive);
                    }})(elem));
                }
                if (notice.styles.btnfocus) {
                    elem.on("focus", (function(elem){ return function(){
                        elem.addClass(notice.styles.btnfocus);
                    }})(elem)).on("blur", (function(elem){ return function(){
                        elem.removeClass(notice.styles.btnfocus);
                    }})(elem));
                }
            }
        }
    };
    $.extend(PNotify.styling.jqueryui, {
        btn: "ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only",
        btnhover: "ui-state-hover",
        btnactive: "ui-state-active",
        btnfocus: "ui-state-focus",
        input: "",
        text: "ui-button-text"
    });
    $.extend(PNotify.styling.bootstrap2, {
        btn: "btn",
        input: ""
    });
    $.extend(PNotify.styling.bootstrap3, {
        btn: "btn btn-default",
        input: "form-control"
    });
    $.extend(PNotify.styling.fontawesome, {
        btn: "btn btn-default",
        input: "form-control"
    });
}));
// Desktop
(function (factory) {
    if (typeof exports === 'object' && typeof module !== 'undefined') {
        // CommonJS
        module.exports = factory(require('jquery'), require('pnotify'));
    } else if (typeof define === 'function' && define.amd) {
        // AMD. Register as a module.
        define('pnotify.desktop', ['jquery', 'pnotify'], factory);
    } else {
        // Browser globals
        factory(jQuery, PNotify);
    }
}(function($, PNotify){
    var permission;
    var notify = function(title, options){
        // Memoize based on feature detection.
        if ("Notification" in window) {
            notify = function (title, options) {
                return new Notification(title, options);
            };
        } else if ("mozNotification" in navigator) {
            notify = function (title, options) {
                // Gecko < 22
                return navigator.mozNotification
                    .createNotification(title, options.body, options.icon)
                    .show();
            };
        } else if ("webkitNotifications" in window) {
            notify = function (title, options) {
                return window.webkitNotifications.createNotification(
                    options.icon,
                    title,
                    options.body
                );
            };
        } else {
            notify = function (title, options) {
                return null;
            };
        }
        return notify(title, options);
    };


    PNotify.prototype.options.desktop = {
        // Display the notification as a desktop notification.
        desktop: false,
        // If desktop notifications are not supported or allowed, fall back to a regular notice.
        fallback: true,
        // The URL of the icon to display. If false, no icon will show. If null, a default icon will show.
        icon: null,
        // Using a tag lets you update an existing notice, or keep from duplicating notices between tabs.
        // If you leave tag null, one will be generated, facilitating the "update" function.
        // see: http://www.w3.org/TR/notifications/#tags-example
        tag: null
    };
    PNotify.prototype.modules.desktop = {
        tag: null,
        icon: null,
        genNotice: function(notice, options){
            if (options.icon === null) {
                this.icon = "http://sciactive.com/pnotify/includes/desktop/"+notice.options.type+".png";
            } else if (options.icon === false) {
                this.icon = null;
            } else {
                this.icon = options.icon;
            }
            if (this.tag === null || options.tag !== null) {
                this.tag = options.tag === null ? "PNotify-"+Math.round(Math.random() * 1000000) : options.tag;
            }
            notice.desktop = notify(notice.options.title, {
                icon: this.icon,
                body: notice.options.text,
                tag: this.tag
            });
            if (!("close" in notice.desktop) && ("cancel" in notice.desktop)) {
                notice.desktop.close = function(){
                    notice.desktop.cancel();
                };
            }
            notice.desktop.onclick = function(){
                notice.elem.trigger("click");
            };
            notice.desktop.onclose = function(){
                if (notice.state !== "closing" && notice.state !== "closed") {
                    notice.remove();
                }
            };
        },
        init: function(notice, options){
            if (!options.desktop)
                return;
            permission = PNotify.desktop.checkPermission();
            if (permission !== 0) {
                // Keep the notice from opening if fallback is false.
                if (!options.fallback) {
                    notice.options.auto_display = false;
                }
                return;
            }
            this.genNotice(notice, options);
        },
        update: function(notice, options, oldOpts){
            if ((permission !== 0 && options.fallback) || !options.desktop)
                return;
            this.genNotice(notice, options);
        },
        beforeOpen: function(notice, options){
            if ((permission !== 0 && options.fallback) || !options.desktop)
                return;
            notice.elem.css({'left': '-10000px', 'display': 'none'});
        },
        afterOpen: function(notice, options){
            if ((permission !== 0 && options.fallback) || !options.desktop)
                return;
            notice.elem.css({'left': '-10000px', 'display': 'none'});
            if ("show" in notice.desktop) {
                notice.desktop.show();
            }
        },
        beforeClose: function(notice, options){
            if ((permission !== 0 && options.fallback) || !options.desktop)
                return;
            notice.elem.css({'left': '-10000px', 'display': 'none'});
        },
        afterClose: function(notice, options){
            if ((permission !== 0 && options.fallback) || !options.desktop)
                return;
            notice.elem.css({'left': '-10000px', 'display': 'none'});
            if ("close" in notice.desktop) {
                notice.desktop.close();
            }
        }
    };
    PNotify.desktop = {
        permission: function(){
            if (typeof Notification !== "undefined" && "requestPermission" in Notification) {
                Notification.requestPermission();
            } else if ("webkitNotifications" in window) {
                window.webkitNotifications.requestPermission();
            }
        },
        checkPermission: function(){
            if (typeof Notification !== "undefined" && "permission" in Notification) {
                return (Notification.permission === "granted" ? 0 : 1);
            } else if ("webkitNotifications" in window) {
                return window.webkitNotifications.checkPermission() == 0 ? 0 : 1;
            } else {
                return 1;
            }
        }
    };
    permission = PNotify.desktop.checkPermission();
}));
// Nonblock
(function (factory) {
    if (typeof exports === 'object' && typeof module !== 'undefined') {
        // CommonJS
        module.exports = factory(require('jquery'), require('pnotify'));
    } else if (typeof define === 'function' && define.amd) {
        // AMD. Register as a module.
        define('pnotify.nonblock', ['jquery', 'pnotify'], factory);
    } else {
        // Browser globals
        factory(jQuery, PNotify);
    }
}(function($, PNotify){
    // Some useful regexes.
    var re_on = /^on/,
        re_mouse_events = /^(dbl)?click$|^mouse(move|down|up|over|out|enter|leave)$|^contextmenu$/,
        re_ui_events = /^(focus|blur|select|change|reset)$|^key(press|down|up)$/,
        re_html_events = /^(scroll|resize|(un)?load|abort|error)$/;
    // Fire a DOM event.
    var dom_event = function(e, orig_e){
        var event_object;
        e = e.toLowerCase();
        if (document.createEvent && this.dispatchEvent) {
            // FireFox, Opera, Safari, Chrome
            e = e.replace(re_on, '');
            if (e.match(re_mouse_events)) {
                // This allows the click event to fire on the notice. There is
                // probably a much better way to do it.
                $(this).offset();
                event_object = document.createEvent("MouseEvents");
                event_object.initMouseEvent(
                    e, orig_e.bubbles, orig_e.cancelable, orig_e.view, orig_e.detail,
                    orig_e.screenX, orig_e.screenY, orig_e.clientX, orig_e.clientY,
                    orig_e.ctrlKey, orig_e.altKey, orig_e.shiftKey, orig_e.metaKey, orig_e.button, orig_e.relatedTarget
                );
            } else if (e.match(re_ui_events)) {
                event_object = document.createEvent("UIEvents");
                event_object.initUIEvent(e, orig_e.bubbles, orig_e.cancelable, orig_e.view, orig_e.detail);
            } else if (e.match(re_html_events)) {
                event_object = document.createEvent("HTMLEvents");
                event_object.initEvent(e, orig_e.bubbles, orig_e.cancelable);
            }
            if (!event_object) return;
            this.dispatchEvent(event_object);
        } else {
            // Internet Explorer
            if (!e.match(re_on)) e = "on"+e;
            event_object = document.createEventObject(orig_e);
            this.fireEvent(e, event_object);
        }
    };


    // This keeps track of the last element the mouse was over, so
    // mouseleave, mouseenter, etc can be called.
    var nonblock_last_elem;
    // This is used to pass events through the notice if it is non-blocking.
    var nonblock_pass = function(notice, e, e_name){
        notice.elem.css("display", "none");
        var element_below = document.elementFromPoint(e.clientX, e.clientY);
        notice.elem.css("display", "block");
        var jelement_below = $(element_below);
        var cursor_style = jelement_below.css("cursor");
        if (cursor_style === "auto" && element_below.tagName === "A") {
            cursor_style = "pointer";
        }
        notice.elem.css("cursor", cursor_style !== "auto" ? cursor_style : "default");
        // If the element changed, call mouseenter, mouseleave, etc.
        if (!nonblock_last_elem || nonblock_last_elem.get(0) != element_below) {
            if (nonblock_last_elem) {
                dom_event.call(nonblock_last_elem.get(0), "mouseleave", e.originalEvent);
                dom_event.call(nonblock_last_elem.get(0), "mouseout", e.originalEvent);
            }
            dom_event.call(element_below, "mouseenter", e.originalEvent);
            dom_event.call(element_below, "mouseover", e.originalEvent);
        }
        dom_event.call(element_below, e_name, e.originalEvent);
        // Remember the latest element the mouse was over.
        nonblock_last_elem = jelement_below;
    };


    PNotify.prototype.options.nonblock = {
        // Create a non-blocking notice. It lets the user click elements underneath it.
        nonblock: false,
        // The opacity of the notice (if it's non-blocking) when the mouse is over it.
        nonblock_opacity: .2
    };
    PNotify.prototype.modules.nonblock = {
        // This lets us update the options available in the closures.
        myOptions: null,

        init: function(notice, options){
            var that = this;
            this.myOptions = options;
            notice.elem.on({
                "mouseenter": function(e){
                    if (that.myOptions.nonblock) e.stopPropagation();
                    if (that.myOptions.nonblock) {
                        // If it's non-blocking, animate to the other opacity.
                        notice.elem.stop().animate({"opacity": that.myOptions.nonblock_opacity}, "fast");
                    }
                },
                "mouseleave": function(e){
                    if (that.myOptions.nonblock) e.stopPropagation();
                    nonblock_last_elem = null;
                    notice.elem.css("cursor", "auto");
                    // Animate back to the normal opacity.
                    if (that.myOptions.nonblock && notice.animating !== "out")
                        notice.elem.stop().animate({"opacity": notice.options.opacity}, "fast");
                },
                "mouseover": function(e){
                    if (that.myOptions.nonblock) e.stopPropagation();
                },
                "mouseout": function(e){
                    if (that.myOptions.nonblock) e.stopPropagation();
                },
                "mousemove": function(e){
                    if (that.myOptions.nonblock) {
                        e.stopPropagation();
                        nonblock_pass(notice, e, "onmousemove");
                    }
                },
                "mousedown": function(e){
                    if (that.myOptions.nonblock) {
                        e.stopPropagation();
                        e.preventDefault();
                        nonblock_pass(notice, e, "onmousedown");
                    }
                },
                "mouseup": function(e){
                    if (that.myOptions.nonblock) {
                        e.stopPropagation();
                        e.preventDefault();
                        nonblock_pass(notice, e, "onmouseup");
                    }
                },
                "click": function(e){
                    if (that.myOptions.nonblock) {
                        e.stopPropagation();
                        nonblock_pass(notice, e, "onclick");
                    }
                },
                "dblclick": function(e){
                    if (that.myOptions.nonblock) {
                        e.stopPropagation();
                        nonblock_pass(notice, e, "ondblclick");
                    }
                }
            });
        },
        update: function(notice, options){
            this.myOptions = options;
        }
    };
}));

/*! JsRender v0.9.78 (Beta): http://jsviews.com/#jsrender */
/*! **VERSION FOR WEB** (For NODE.JS see http://jsviews.com/download/jsrender-node.js) */
!function(e,t){var n=t.jQuery;"object"==typeof exports?module.exports=n?e(t,n):function(n){if(n&&!n.fn)throw"Provide jQuery or null";return e(t,n)}:"function"==typeof define&&define.amd?define(function(){return e(t)}):e(t,!1)}(function(e,t){"use strict";function n(e,t){return function(){var n,r=this,i=r.base;return r.base=e,n=t.apply(r,arguments),r.base=i,n}}function r(e,t){return te(t)&&(t=n(e?e._d?e:n(s,e):s,t),t._d=1),t}function i(e,t){for(var n in t.props)Re.test(n)&&(e[n]=r(e[n],t.props[n]))}function o(e){return e}function s(){return""}function a(e){try{throw console.log("JsRender dbg breakpoint: "+e),"dbg breakpoint"}catch(t){}return this.base?this.baseApply(arguments):e}function d(e){this.name=(t.link?"JsViews":"JsRender")+" Error",this.message=e||this.name}function u(e,t){for(var n in t)e[n]=t[n];return e}function l(e,t,n){return e?(de.delimiters=[e,t,ve=n?n.charAt(0):ve],pe=e.charAt(0),ce=e.charAt(1),fe=t.charAt(0),ge=t.charAt(1),e="\\"+pe+"(\\"+ve+")?\\"+ce,t="\\"+fe+"\\"+ge,G="(?:(\\w+(?=[\\/\\s\\"+fe+"]))|(\\w+)?(:)|(>)|(\\*))\\s*((?:[^\\"+fe+"]|\\"+fe+"(?!\\"+ge+"))*?)",ae.rTag="(?:"+G+")",G=new RegExp("(?:"+e+G+"(\\/)?|\\"+pe+"(\\"+ve+")?\\"+ce+"(?:(?:\\/(\\w+))\\s*|!--[\\s\\S]*?--))"+t,"g"),W=new RegExp("<.*>|([^\\\\]|^)[{}]|"+e+".*"+t),le):de.delimiters}function p(e,t){t||e===!0||(t=e,e=void 0);var n,r,i,o,s=this,a=!t||"root"===t;if(e){if(o=t&&s.type===t&&s,!o)if(n=s.views,s._.useKey){for(r in n)if(o=t?n[r].get(e,t):n[r])break}else for(r=0,i=n.length;!o&&i>r;r++)o=t?n[r].get(e,t):n[r]}else if(a)for(;s.parent;)o=s,s=s.parent;else for(;s&&!o;)o=s.type===t?s:void 0,s=s.parent;return o}function c(){var e=this.get("item");return e?e.index:void 0}function f(){return this.index}function g(t){var n,r=this,i=r.linkCtx,o=(r.ctx||{})[t];return void 0===o&&i&&i.ctx&&(o=i.ctx[t]),void 0===o&&(o=oe[t]),o&&te(o)&&!o._wrp&&(n=function(){return o.apply(this&&this!==e?this:r,arguments)},n._wrp=r,u(n,o)),n||o}function v(e){return e&&(e.fn?e:this.getRsc("templates",e)||re(e))}function h(e,t,n,r){var o,s,a="number"==typeof n&&t.tmpl.bnds[n-1],d=t.linkCtx;return void 0!==r?n=r={props:{},args:[r]}:a&&(n=a(t.data,t,ae)),s=n.args[0],(e||a)&&(o=d&&d.tag,o||(o=u(new ae._tg,{_:{inline:!d,bnd:a,unlinked:!0},tagName:":",cvt:e,flow:!0,tagCtx:n}),d&&(d.tag=o,o.linkCtx=d),n.ctx=L(n.ctx,(d?d.view:t).ctx)),o._er=r&&s,i(o,n),n.view=t,o.ctx=n.ctx||o.ctx||{},n.ctx=void 0,s=o.cvtArgs("true"!==e&&e)[0],s=a&&t._.onRender?t._.onRender(s,t,o):s),void 0!=s?s:""}function m(e){var t=this,n=t.tagCtx,r=n.view,i=n.args;return e=e||t.convert,e=e&&(""+e===e?r.getRsc("converters",e)||S("Unknown converter: '"+e+"'"):e),i=i.length||n.index?e?i.slice():i:[r.data],e&&(e.depends&&(t.depends=ae.getDeps(t.depends,t,e.depends,e)),i[0]=e.apply(t,i)),i}function w(e,t){for(var n,r,i=this;void 0===n&&i;)r=i.tmpl&&i.tmpl[e],n=r&&r[t],i=i.parent;return n||Y[e][t]}function x(e,t,n,r,o,s){t=t||X;var a,d,u,l,p,c,f,g,v,h,m,w,x,b,_,y,k,j,C,A="",T=t.linkCtx||0,V=t.ctx,R=n||t.tmpl,M="number"==typeof r&&t.tmpl.bnds[r-1];for("tag"===e._is?(a=e,e=a.tagName,r=a.tagCtxs,u=a.template):(d=t.getRsc("tags",e)||S("Unknown tag: {{"+e+"}} "),u=d.template),void 0!==s?(A+=s,r=s=[{props:{},args:[]}]):M&&(r=M(t.data,t,ae)),g=r.length,f=0;g>f;f++)h=r[f],(!T||!T.tag||f&&!T.tag._.inline||a._er)&&((w=R.tmpls&&h.tmpl)&&(w=h.content=R.tmpls[w-1]),h.index=f,h.tmpl=w,h.render=N,h.view=t,h.ctx=L(h.ctx,V)),(n=h.props.tmpl)&&(h.tmpl=t.getTmpl(n)),a||(a=new d._ctr,x=!!a.init,a.parent=c=V&&V.tag,a.tagCtxs=r,C=a.dataMap,T&&(a._.inline=!1,T.tag=a,a.linkCtx=T),(a._.bnd=M||T.fn)?a._.arrVws={}:a.dataBoundOnly&&S("{^{"+e+"}} tag must be data-bound")),r=a.tagCtxs,C=a.dataMap,h.tag=a,C&&r&&(h.map=r[f].map),a.flow||(m=h.ctx=h.ctx||{},l=a.parents=m.parentTags=V&&L(m.parentTags,V.parentTags)||{},c&&(l[c.tagName]=c),l[a.tagName]=m.tag=a);if(!(a._er=s)){for(i(a,r[0]),a.rendering={},f=0;g>f;f++)h=a.tagCtx=r[f],k=h.props,y=a.cvtArgs(),(b=k.dataMap||C)&&(y.length||k.dataMap)&&(_=h.map,_&&_.src===y[0]&&!o||(_&&_.src&&_.unmap(),_=h.map=b.map(y[0],k,void 0,!a._.bnd)),y=[_.tgt]),a.ctx=h.ctx,f||(x&&(j=a.template,a.init(h,T,a.ctx),x=void 0),T&&(T.attr=a.attr=T.attr||a.attr),p=a.attr,a._.noVws=p&&p!==Ee),v=void 0,a.render&&(v=a.render.apply(a,y)),y.length||(y=[t]),void 0===v&&(v=h.render(y[0],!0)||(o?void 0:"")),A=A?A+(v||""):v;a.rendering=void 0}return a.tagCtx=r[0],a.ctx=a.tagCtx.ctx,a._.noVws&&a._.inline&&(A="text"===p?ie.html(A):""),M&&t._.onRender?t._.onRender(A,t,a):A}function b(e,t,n,r,i,o,s,a){var d,u,l,p=this,f="array"===t;p.content=a,p.views=f?[]:{},p.parent=n,p.type=t||"top",p.data=r,p.tmpl=i,l=p._={key:0,useKey:f?0:1,id:""+$e++,onRender:s,bnds:{}},p.linked=!!s,n?(d=n.views,u=n._,u.useKey?(d[l.key="_"+u.useKey++]=p,p.index=Ue,p.getIndex=c):d.length===(l.key=p.index=o)?d.push(p):d.splice(o,0,p),p.ctx=e||n.ctx):p.ctx=e}function _(e){var t,n,r,i,o,s,a;for(t in Oe)if(o=Oe[t],(s=o.compile)&&(n=e[t+"s"]))for(r in n)i=n[r]=s(r,n[r],e,0),i._is=t,i&&(a=ae.onStore[t])&&a(r,i,s)}function y(e,t,n){function i(){var t=this;t._={inline:!0,unlinked:!0},t.tagName=e}var o,s,a,d=new ae._tg;if(te(t)?t={depends:t.depends,render:t}:""+t===t&&(t={template:t}),s=t.baseTag){t.flow=!!t.flow,t.baseTag=s=""+s===s?n&&n.tags[s]||se[s]:s,d=u(d,s);for(a in t)d[a]=r(s[a],t[a])}else d=u(d,t);return void 0!==(o=d.template)&&(d.template=""+o===o?re[o]||re(o):o),d.init!==!1&&((i.prototype=d).constructor=d._ctr=i),n&&(d._parentTmpl=n),d}function k(e){return this.base.apply(this,e)}function j(e,n,r,i){function o(n){var o,a;if(""+n===n||n.nodeType>0&&(s=n)){if(!s)if(/^\.\/[^\\:*?"<>]*$/.test(n))(a=re[e=e||n])?n=a:s=document.getElementById(n);else if(t.fn&&!W.test(n))try{s=t(document).find(n)[0]}catch(d){}s&&(i?n=s.innerHTML:(o=s.getAttribute(Se),o?o!==Ie?(n=re[o],delete re[o]):t.fn&&(n=t.data(s)[Ie]):(e=e||(t.fn?Ie:n),n=j(e,s.innerHTML,r,i)),n.tmplName=e=e||o,e!==Ie&&(re[e]=n),s.setAttribute(Se,e),t.fn&&t.data(s,Ie,n))),s=void 0}else n.fn||(n=void 0);return n}var s,a,d=n=n||"";return 0===i&&(i=void 0,d=o(d)),i=i||(n.markup?n:{}),i.tmplName=e,r&&(i._parentTmpl=r),!d&&n.markup&&(d=o(n.markup))&&d.fn&&(d=d.markup),void 0!==d?(d.fn||n.fn?d.fn&&(a=d):(n=V(d,i),U(d.replace(ke,"\\$&"),n)),a||(_(i),a=u(function(){return n.render.apply(n,arguments)},n)),e&&!r&&e!==Ie&&(qe[e]=a),a):void 0}function C(e,n){return t.isFunction(e)?e.call(n):e}function A(e){var t,n=[],r=e.length;for(t=0;r>t;t++)n.push(e[t].unmap());return n}function T(e,n){function r(e){l.apply(this,e)}function i(){return new r(arguments)}function o(e,t){var n,r,i,o,s,a=c.length;for(n=0;a>n;n++)o=c[n],r=void 0,o+""!==o&&(r=o,o=r.getter),void 0===(s=e[o])&&r&&void 0!==(i=r.defaultVal)&&(s=C(i,e)),t(s,r&&p[r.type],o)}function s(n){n=n+""===n?JSON.parse(n):n;var r,i,s,u=n,l=[];if(t.isArray(n)){for(n=n||[],i=n.length,r=0;i>r;r++)l.push(this.map(n[r]));return l._is=e,l.unmap=d,l.merge=a,l}if(n){o(n,function(e,t){t&&(e=t.map(e)),l.push(e)}),u=this.apply(this,l);for(s in n)s===ee||b[s]||(u[s]=n[s])}return u}function a(e){e=e+""===e?JSON.parse(e):e;var n,r,s,a,d,u,l,p,c,f,v=this;if(t.isArray(v)){for(p={},f=[],s=e.length,a=v.length,n=0;s>n;n++){for(c=e[n],l=!1,r=0;a>r&&!l;r++)p[r]||(u=v[r],g&&(p[r]=l=g+""===g?c[g]&&(b[g]?u[g]():u[g])===c[g]:g(u,c)));l?(u.merge(c),f.push(u)):f.push(i.map(c))}return void(x?x(v).refresh(f,!0):v.splice.apply(v,[0,v.length].concat(f)))}o(e,function(e,t,n){t?v[n]().merge(e):v[n](e)});for(d in e)d===ee||b[d]||(v[d]=e[d])}function d(){var e,n,r,i,o,s,a=this;if(t.isArray(a))return A(a);for(e={},i=c.length,r=0;i>r;r++)n=c[r],o=void 0,n+""!==n&&(o=n,n=o.getter),s=a[n](),e[n]=o&&s&&p[o.type]?t.isArray(s)?A(s):s.unmap():s;for(n in a)"_is"===n||b[n]||n===ee||"_"===n.charAt(0)&&b[n.slice(1)]||t.isFunction(a[n])||(e[n]=a[n]);return e}var u,l,p=this,c=n.getters,f=n.extend,g=n.id,v=t.extend({_is:e||"unnamed",unmap:d,merge:a},f),h="",m="",w=c?c.length:0,x=t.observable,b={};for(r.prototype=v,u=0;w>u;u++)!function(e){e=e.getter||e,b[e]=u+1;var t="_"+e;h+=(h?",":"")+e,m+="this."+t+" = "+e+";\n",v[e]=v[e]||function(n){return arguments.length?void(x?x(this).setProperty(e,n):this[t]=n):this[t]},x&&(v[e].set=v[e].set||function(e){this[t]=e})}(c[u]);return l=new Function(h,m.slice(0,-1)),l.prototype=v,v.constructor=l,i.map=s,i.getters=c,i.extend=f,i.id=g,i}function V(e,n){var r,i=ue._wm||{},o=u({tmpls:[],links:{},bnds:[],_is:"template",render:N},n);return o.markup=e,n.htmlTag||(r=Ae.exec(e),o.htmlTag=r?r[1].toLowerCase():""),r=i[o.htmlTag],r&&r!==i.div&&(o.markup=t.trim(o.markup)),o}function R(e,t){function n(i,o,s){var a,d,u,l;if(i&&typeof i===Fe&&!i.nodeType&&!i.markup&&!i.getTgt&&!("viewModel"===e&&i.getters||i.extend)){for(u in i)n(u,i[u],o);return o||Y}return void 0===o&&(o=i,i=void 0),i&&""+i!==i&&(s=o,o=i,i=void 0),l=s?"viewModel"===e?s:s[r]=s[r]||{}:n,d=t.compile,null===o?i&&delete l[i]:(o=d?d.call(l,i,o,s,0):o,i&&(l[i]=o)),d&&o&&(o._is=e),o&&(a=ae.onStore[e])&&a(i,o,d),o}var r=e+"s";Y[r]=n}function M(e){le[e]=function(t){return arguments.length?(de[e]=t,le):de[e]}}function $(e){function t(t,n){this.tgt=e.getTgt(t,n)}return te(e)&&(e={getTgt:e}),e.baseMap&&(e=u(u({},e.baseMap),e)),e.map=function(e,n){return new t(e,n)},e}function N(e,t,n,r,i,o){var s,a,d,u,l,p,c,f,g=r,v="";if(t===!0?(n=t,t=void 0):typeof t!==Fe&&(t=void 0),(d=this.tag)?(l=this,g=g||l.view,u=g.getTmpl(d.template||l.tmpl),arguments.length||(e=g)):u=this,u){if(!g&&e&&"view"===e._is&&(g=e),g&&e===g&&(e=g.data),p=!g,me=me||p,g||((t=t||{}).root=e),!me||ue.useViews||u.useViews||g&&g!==X)v=E(u,e,t,n,g,i,o,d);else{if(g?(c=g.data,f=g.index,g.index=Ue):(g=X,g.data=e,g.ctx=t),ne(e)&&!n)for(s=0,a=e.length;a>s;s++)g.index=s,g.data=e[s],v+=u.fn(e[s],g,ae);else g.data=e,v+=u.fn(e,g,ae);g.data=c,g.index=f}p&&(me=void 0)}return v}function E(e,t,n,r,i,o,s,a){function d(e){_=u({},n),_[x]=e}var l,p,c,f,g,v,h,m,w,x,_,y,k="";if(a&&(w=a.tagName,y=a.tagCtx,n=n?L(n,a.ctx):a.ctx,e===i.content?h=e!==i.ctx._wrp?i.ctx._wrp:void 0:e!==y.content?e===a.template?(h=y.tmpl,n._wrp=y.content):h=y.content||i.content:h=i.content,y.props.link===!1&&(n=n||{},n.link=!1),(x=y.props.itemVar)&&("~"!==x.charAt(0)&&I("Use itemVar='~myItem'"),x=x.slice(1))),i&&(s=s||i._.onRender,n=L(n,i.ctx)),o===!0&&(v=!0,o=0),s&&(n&&n.link===!1||a&&a._.noVws)&&(s=void 0),m=s,s===!0&&(m=void 0,s=i._.onRender),n=e.helpers?L(e.helpers,n):n,_=n,ne(t)&&!r)for(c=v?i:void 0!==o&&i||new b(n,"array",i,t,e,o,s),i&&i._.useKey&&(c._.bnd=!a||a._.bnd&&a),x&&(c.it=x),x=c.it,l=0,p=t.length;p>l;l++)x&&d(t[l]),f=new b(_,"item",c,t[l],e,(o||0)+l,s,h),g=e.fn(t[l],f,ae),k+=c._.onRender?c._.onRender(g,f):g;else x&&d(t),c=v?i:new b(_,w||"data",i,t,e,o,s,h),a&&!a.flow&&(c.tag=a),k+=e.fn(t,c,ae);return m?m(k,c):k}function F(e,t,n){var r=void 0!==n?te(n)?n.call(t.data,e,t):n||"":"{Error: "+e.message+"}";return de.onError&&void 0!==(n=de.onError.call(t.data,e,n&&r,t))&&(r=n),t&&!t.linkCtx?ie.html(r):r}function S(e){throw new ae.Err(e)}function I(e){S("Syntax error\n"+e)}function U(e,t,n,r,i){function o(t){t-=v,t&&m.push(e.substr(v,t).replace(_e,"\\n"))}function s(t,n){t&&(t+="}}",I((n?"{{"+n+"}} block has {{/"+t+" without {{"+t:"Unmatched or missing {{/"+t)+", in template:\n"+e))}function a(a,d,u,c,g,x,b,_,y,k,j,C){(b&&d||y&&!u||_&&":"===_.slice(-1)||k)&&I(a),x&&(g=":",c=Ee),y=y||n&&!i;var A=(d||n)&&[[]],T="",V="",R="",M="",$="",N="",E="",F="",S=!y&&!g;u=u||(_=_||"#data",g),o(C),v=C+a.length,b?f&&m.push(["*","\n"+_.replace(/^:/,"ret+= ").replace(ye,"$1")+";\n"]):u?("else"===u&&(Ce.test(_)&&I('for "{{else if expr}}" use "{{else expr}}"'),A=w[7]&&[[]],w[8]=e.substring(w[8],C),w=h.pop(),m=w[2],S=!0),_&&O(_.replace(_e," "),A,t).replace(je,function(e,t,n,r,i,o,s,a){return r="'"+i+"':",s?(V+=o+",",M+="'"+a+"',"):n?(R+=r+o+",",N+=r+"'"+a+"',"):t?E+=o:("trigger"===i&&(F+=o),T+=r+o+",",$+=r+"'"+a+"',",p=p||Re.test(i)),""}).slice(0,-1),A&&A[0]&&A.pop(),l=[u,c||!!r||p||"",S&&[],J(M||(":"===u?"'#data',":""),$,N),J(V||(":"===u?"data,":""),T,R),E,F,A||0],m.push(l),S&&(h.push(w),w=l,w[8]=v)):j&&(s(j!==w[0]&&"else"!==w[0]&&j,w[0]),w[8]=e.substring(w[8],C),w=h.pop()),s(!w&&j),m=w[2]}var d,u,l,p,c,f=de.allowCode||t&&t.allowCode||le.allowCode===!0,g=[],v=0,h=[],m=g,w=[,,g];if(f&&(t.allowCode=f),n&&(void 0!==r&&(e=e.slice(0,-r.length-2)+ge),e=pe+e+ge),s(h[0]&&h[0][2].pop()[0]),e.replace(G,a),o(e.length),(v=g[g.length-1])&&s(""+v!==v&&+v[8]===v[8]&&v[0]),n){for(u=B(g,e,n),c=[],d=g.length;d--;)c.unshift(g[d][7]);q(u,c)}else u=B(g,t);return u}function q(e,t){var n,r,i=0,o=t.length;for(e.deps=[];o>i;i++){r=t[i];for(n in r)"_jsvto"!==n&&r[n].length&&(e.deps=e.deps.concat(r[n]))}e.paths=r}function J(e,t,n){return[e.slice(0,-1),t.slice(0,-1),n.slice(0,-1)]}function K(e,t){return"\n	"+(t?t+":{":"")+"args:["+e[0]+"]"+(e[1]||!t?",\n	props:{"+e[1]+"}":"")+(e[2]?",\n	ctx:{"+e[2]+"}":"")}function O(e,t,n){function r(r,m,w,x,b,_,y,k,j,C,A,T,V,R,M,$,N,E,F,S){function q(e,n,r,s,a,d,p,c){var f="."===r;if(r&&(b=b.slice(n.length),/^\.?constructor$/.test(c||b)&&I(e),f||(e=(s?'view.hlp("'+s+'")':a?"view":"data")+(c?(d?"."+d:s?"":a?"":"."+r)+(p||""):(c=s?"":a?d||"":r,"")),e+=c?"."+c:"",e=n+("view.data"===e.slice(0,9)?e.slice(5):e)),u)){if(O="linkTo"===i?o=t._jsvto=t._jsvto||[]:l.bd,B=f&&O[O.length-1]){if(B._jsv){for(;B.sb;)B=B.sb;B.bnd&&(b="^"+b.slice(1)),B.sb=b,B.bnd=B.bnd||"^"===b.charAt(0)}}else O.push(b);h[g]=F+(f?1:0)}return e}x=u&&x,x&&!k&&(b=x+b),_=_||"",w=w||m||T,b=b||j,C=C||N||"";var J,K,O,B,L,Q=")";if("["===C&&(C="[j._sq(",Q=")]"),!y||d||a){if(u&&$&&!d&&!a&&(!i||s||o)&&(J=h[g-1],S.length-1>F-(J||0))){if(J=S.slice(J,F+r.length),K!==!0)if(O=o||p[g-1].bd,B=O[O.length-1],B&&B.prm){for(;B.sb&&B.sb.prm;)B=B.sb;L=B.sb={path:B.sb,bnd:B.bnd}}else O.push(L={path:O.pop()});$=ce+":"+J+" onerror=''"+fe,K=f[$],K||(f[$]=!0,f[$]=K=U($,n,!0)),K!==!0&&L&&(L._jsv=K,L.prm=l.bd,L.bnd=L.bnd||L.path&&L.path.indexOf("^")>=0)}return d?(d=!V,d?r:T+'"'):a?(a=!R,a?r:T+'"'):(w?(h[g]=F++,l=p[++g]={bd:[]},w):"")+(E?g?"":(c=S.slice(c,F),(i?(i=s=o=!1,"\b"):"\b,")+c+(c=F+r.length,u&&t.push(l.bd=[]),"\b")):k?(g&&I(e),u&&t.pop(),i=b,s=x,c=F+r.length,x&&(u=l.bd=t[i]=[]),b+":"):b?b.split("^").join(".").replace(xe,q)+(C?(l=p[++g]={bd:[]},v[g]=Q,C):_):_?_:M?(M=v[g]||M,v[g]=!1,l=p[--g],M+(C?(l=p[++g],v[g]=Q,C):"")):A?(v[g]||I(e),","):m?"":(d=V,a=R,'"'))}I(e)}var i,o,s,a,d,u=t&&t[0],l={bd:u},p={0:l},c=0,f=n?n.links:u&&(u.links=u.links||{}),g=0,v={},h={},m=(e+(n?" ":"")).replace(be,r);return!g&&m||I(e)}function B(e,t,n){var r,i,o,s,a,d,u,l,p,c,f,g,v,h,m,w,x,b,_,y,k,j,C,A,T,R,M,$,N,E,F=0,S=ue.useViews||t.useViews||t.tags||t.templates||t.helpers||t.converters,U="",J={},O=e.length;for(""+t===t?(b=n?'data-link="'+t.replace(_e," ").slice(1,-1)+'"':t,t=0):(b=t.tmplName||"unnamed",t.allowCode&&(J.allowCode=!0),t.debug&&(J.debug=!0),f=t.bnds,x=t.tmpls),r=0;O>r;r++)if(i=e[r],""+i===i)U+='\n+"'+i+'"';else if(o=i[0],"*"===o)U+=";\n"+i[1]+"\nret=ret";else{if(s=i[1],k=!n&&i[2],a=K(i[3],"params")+"},"+K(v=i[4]),$=i[5],E=i[6],j=i[8]&&i[8].replace(ye,"$1"),(T="else"===o)?g&&g.push(i[7]):(F=0,f&&(g=i[7])&&(g=[g],F=f.push(1))),S=S||v[1]||v[2]||g||/view.(?!index)/.test(v[0]),(R=":"===o)?s&&(o=s===Ee?">":s+o):(k&&(_=V(j,J),_.tmplName=b+"/"+o,_.useViews=_.useViews||S,B(k,_),S=_.useViews,x.push(_)),T||(y=o,S=S||o&&(!se[o]||!se[o].flow),A=U,U=""),C=e[r+1],C=C&&"else"===C[0]),N=$?";\ntry{\nret+=":"\n+",h="",m="",R&&(g||E||s&&s!==Ee)){if(M=new Function("data,view,j,u"," // "+b+" "+F+" "+o+"\nreturn {"+a+"};"),M._er=$,M._tag=o,n)return M;q(M,g),w='c("'+s+'",view,',c=!0,h=w+F+",",m=")"}if(U+=R?(n?($?"try{\n":"")+"return ":N)+(c?(c=void 0,S=p=!0,w+(g?(f[F-1]=M,F):"{"+a+"}")+")"):">"===o?(u=!0,"h("+v[0]+")"):(l=!0,"((v="+v[0]+")!=null?v:"+(n?"null)":'"")'))):(d=!0,"\n{view:view,tmpl:"+(k?x.length:"0")+","+a+"},"),y&&!C){if(U="["+U.slice(0,-1)+"]",w='t("'+y+'",view,this,',n||g){if(U=new Function("data,view,j,u"," // "+b+" "+F+" "+y+"\nreturn "+U+";"),U._er=$,U._tag=y,g&&q(f[F-1]=U,g),n)return U;h=w+F+",undefined,",m=")"}U=A+N+w+(F||U)+")",g=0,y=0}$&&(S=!0,U+=";\n}catch(e){ret"+(n?"urn ":"+=")+h+"j._err(e,view,"+$+")"+m+";}"+(n?"":"ret=ret"))}U="// "+b+"\nvar v"+(d?",t=j._tag":"")+(p?",c=j._cnvt":"")+(u?",h=j._html":"")+(n?";\n":',ret=""\n')+(J.debug?"debugger;":"")+U+(n?"\n":";\nreturn ret;"),de.debugMode!==!1&&(U="try {\n"+U+"\n}catch(e){\nreturn j._err(e, view);\n}");try{U=new Function("data,view,j,u",U)}catch(L){I("Compiled template code:\n\n"+U+'\n: "'+L.message+'"')}return t&&(t.fn=U,t.useViews=!!S),U}function L(e,t){return e&&e!==t?t?u(u({},t),e):e:t&&u({},t)}function Q(e){return Ne[e]||(Ne[e]="&#"+e.charCodeAt(0)+";")}function H(e){var t,n,r=[];if(typeof e===Fe)for(t in e)n=e[t],t===ee||te(n)||r.push({key:t,prop:n});return r}function P(e,n,r){var i=this.jquery&&(this[0]||S('Unknown template: "'+this.selector+'"')),o=i.getAttribute(Se);return N.call(o?t.data(i)[Ie]:re(i),e,n,r)}function D(e){return void 0!=e?Ve.test(e)&&(""+e).replace(Me,Q)||e:""}var Z=t===!1;t=t&&t.fn?t:e.jQuery;var z,G,W,X,Y,ee,te,ne,re,ie,oe,se,ae,de,ue,le,pe,ce,fe,ge,ve,he,me,we="v0.9.78",xe=/^(!*?)(?:null|true|false|\d[\d.]*|([\w$]+|\.|~([\w$]+)|#(view|([\w$]+))?)([\w$.^]*?)(?:[.[^]([\w$]+)\]?)?)$/g,be=/(\()(?=\s*\()|(?:([([])\s*)?(?:(\^?)(!*?[#~]?[\w$.^]+)?\s*((\+\+|--)|\+|-|&&|\|\||===|!==|==|!=|<=|>=|[<>%*:?\/]|(=))\s*|(!*?[#~]?[\w$.^]+)([([])?)|(,\s*)|(\(?)\\?(?:(')|("))|(?:\s*(([)\]])(?=\s*[.^]|\s*$|[^([])|[)\]])([([]?))|(\s+)/g,_e=/[ \t]*(\r\n|\n|\r)/g,ye=/\\(['"])/g,ke=/['"\\]/g,je=/(?:\x08|^)(onerror:)?(?:(~?)(([\w$_\.]+):)?([^\x08]+))\x08(,)?([^\x08]+)/gi,Ce=/^if\s/,Ae=/<(\w+)[>\s]/,Te=/[\x00`><"'&=]/g,Ve=/[\x00`><\"'&=]/,Re=/^on[A-Z]|^convert(Back)?$/,Me=Te,$e=0,Ne={"&":"&amp;","<":"&lt;",">":"&gt;","\x00":"&#0;","'":"&#39;",'"':"&#34;","`":"&#96;","=":"&#61;"},Ee="html",Fe="object",Se="data-jsv-tmpl",Ie="jsvTmpl",Ue="For #index in nested block use #getIndex().",qe={},Je=e.jsrender,Ke=Je&&t&&!t.render,Oe={template:{compile:j},tag:{compile:y},viewModel:{compile:T},helper:{},converter:{}};if(Y={jsviews:we,sub:{View:b,Err:d,tmplFn:U,parse:O,extend:u,extendCtx:L,syntaxErr:I,onStore:{},addSetting:M,settings:{allowCode:!1},advSet:s,_ths:i,_tg:function(){},_cnvt:h,_tag:x,_er:S,_err:F,_html:D,_sq:function(e){return"constructor"===e&&I(""),e}},settings:{delimiters:l,advanced:function(e){return e?(u(ue,e),ae.advSet(),le):ue}},map:$},(d.prototype=new Error).constructor=d,c.depends=function(){return[this.get("item"),"index"]},f.depends="index",b.prototype={get:p,getIndex:f,getRsc:w,getTmpl:v,hlp:g,_is:"view"},ae=Y.sub,le=Y.settings,!(Je||t&&t.render)){for(z in Oe)R(z,Oe[z]);ie=Y.converters,oe=Y.helpers,se=Y.tags,ae._tg.prototype={baseApply:k,cvtArgs:m},X=ae.topView=new b,t?(t.fn.render=P,ee=t.expando,t.observable&&(u(ae,t.views.sub),Y.map=t.views.map)):(t={},Z&&(e.jsrender=t),t.renderFile=t.__express=t.compile=function(){throw"Node.js: use npm jsrender, or jsrender-node.js"},t.isFunction=function(e){return"function"==typeof e},t.isArray=Array.isArray||function(e){return"[object Array]"==={}.toString.call(e)},ae._jq=function(e){e!==t&&(u(e,t),t=e,t.fn.render=P,delete t.jsrender,ee=t.expando)},t.jsrender=we),de=ae.settings,de.allowCode=!1,te=t.isFunction,ne=t.isArray,t.render=qe,t.views=Y,t.templates=re=Y.templates;for(he in de)M(he);(le.debugMode=function(e){return void 0===e?de.debugMode:(de.debugMode=e,de.onError=e+""===e?new Function("","return '"+e+"';"):te(e)?e:void 0,le)})(!1),ue=de.advanced={useViews:!1,_jsv:!1},se({"if":{render:function(e){var t=this,n=t.tagCtx,r=t.rendering.done||!e&&(arguments.length||!n.index)?"":(t.rendering.done=!0,t.selected=n.index,n.render(n.view,!0));return r},flow:!0},"for":{render:function(e){var t,n=!arguments.length,r=this,i=r.tagCtx,o="",s=0;return r.rendering.done||(t=n?i.view.data:e,void 0!==t&&(o+=i.render(t,n),s+=ne(t)?t.length:1),(r.rendering.done=s)&&(r.selected=i.index)),o},flow:!0},props:{baseTag:"for",dataMap:$(H),flow:!0},include:{flow:!0},"*":{render:o,flow:!0},":*":{render:o,flow:!0},dbg:oe.dbg=ie.dbg=a}),ie({html:D,attr:D,url:function(e){return void 0!=e?encodeURI(""+e):null===e?e:""}})}return de=ae.settings,le.delimiters("{{","}}","^"),Ke&&Je.views.sub._jq(t),t||Je},window);

/**
 * JS utilities for Common Utilities
 * @author Eduardo Cortés <i.bitcero@gmail.com>
 * @link   https://eduardocortes.mx
 * @link   https://rmcommon.bitcero.dev
 */


var cuHandler = {

    /**
     * Propiedades
     */
    ismobile: /android|webos|iphone|ipad|ipod|blackberry|iemobile|opera mini/i.test(navigator.userAgent.toLowerCase()),

    currentResponse: false,

    checkEmail: function (email) {
        var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(email);
    },

    /**
     * Send a request to a remote URL and present the response in a window
     * Launcher can have several useful data attributes:
     * <strong>data-url</strong>: URL where the request will be send
     * @param launcher Element that will function as launcher
     * @returns {boolean}
     */
    loadRemoteDialog: function (launcher) {

        var url = $(launcher).attr("href") != undefined && $(launcher).attr("href") != '#' ? $(launcher).attr("href") : $(launcher).data('url');
        var handler = $(launcher).data("handler");
        var window_id = $(launcher).data("window-id");

        var params = {CUTOKEN_REQUEST: $("#cu-token").val()};

        if (params == false)
            return false;

        cuHandler.showLoader();

        $.get(url, params, function (response) {

            if (!cuHandler.retrieveAjax(response))
                return false;

            if (handler == undefined && handler != '') {

                cuHandler.closeLoader();

                cuHandler.modal.show({
                    content: response.content,
                    title: response.message,
                    icon: response.icon != undefined ? response.icon : '',
                    width: response.width != undefined ? response.width : '',
                    id: response.id != undefined ? response.id : window_id,
                    animate: false,
                    color: response.color != undefined ? response.color : '',
                    closeButton: response.closeButton != undefined ? response.closeButton : true,
                    helpButton: response.helpButton != undefined ? response.helpButton : false,
                    helpUrl: response.helpUrl != undefined ? response.helpUrl : '#',
                    solid: response.solid != undefined ? true : false
                });

            } else {

                cuHandler.closeLoader();
                eval(handler + "(response, launcher);");

            }

            $(".cu-data-table").each(function () {
                cuHandler.createDataTable($(this));
            });

            cuHandler.checkAjaxAction(response);

            return false;

        }, 'json');

        return false;

    },

    submitAjaxForm: function (form) {

        if (!$(form).valid())
            return false;

        cuHandler.showLoader();

        var params = form.serialize();
        params += "&CUTOKEN_REQUEST=" + $("#cu-token").val();

        var action = form.attr("action");
        var method = form.attr("method");

        if (method == 'post')
            $.post(action, params, cuHandler.retrieveAjax, 'json');
        else
            $.get(action, params, cuHandler.retrieveAjax, 'json');

        return false;

    },

    /**
     * Send an AJAX request
     * @param e DOM element
     * @param data
     */
    requestAjax: function (e, data) {

        $(e).cuSpinner({icon: 'svg-rmcommon-spinner-14'});

        if (data == undefined) {
            return false;
        }

        if (data.url == undefined) {
            return false;
        }

        if (data.parameters == undefined) {
            data.parameters = {};
        }

        data.parameters.CUTOKEN_REQUEST = $("#cu-token").val();

        if (data.method == undefined || data.method == 'post') {
            $.post(data.url, data.parameters, cuHandler.retrieveAjax, 'json');
        } else {
            $.get(data.url, data.parameters, cuHandler.retrieveAjax, 'json');
        }

    },

    isDisabled: function (e) {
        if ($(e).hasClass('disabled') || $(e).attr('disabled') != undefined) {
            return true;
        }
        return false;
    },

    // Retrieve information for AJAX-FORMS
    retrieveAjax: function (response, showAlert) {

        showAlert = showAlert == undefined ? true : showAlert;

        this.currentResponse = response;

        if (response.type == 'error') {

            if (response.modal_message != undefined) {
                bootbox.alert({
                    message: response.message
                });
            } else if (response.notify != undefined && response.message != undefined) {
                cuHandler.notify({
                    title: response.notify.title == undefined ? '' : response.notify.title,
                    type: response.notify.type == undefined ? 'alert-info' : response.notify.type,
                    icon: response.notify.icon == undefined ? 'svg-rmcommon-info-solid' : response.notify.icon,
                    text: undefined == response.notify.text ? response.message : response.notify.text
                });
                response.notify = undefined;
            } else if (showAlert) {
                alert(response.message);
            }

        }

        if (response.token != undefined && response.token != '')
            $("#cu-token").val(response.token);

        cuHandler.closeLoader();

        cuHandler.checkAjaxAction(response);

        if (response.type == 'error')
            return false;

        return true;

    },

    /**
     * Check AJAX action reponsed.
     * @param data
     */
    checkAjaxAction: function (data) {

        /**
         * Ejecución de otras acciones
         */
        if (data.showMessage != undefined)
            alert(data.message);

        if (data.notify != undefined && data.message != undefined) {
            this.notify({
                title: data.notify.title == undefined ? null : data.notify.title,
                type: data.notify.type == undefined ? 'alert-info' : data.notify.type,
                icon: data.notify.icon == undefined ? 'svg-rmcommon-info-solid' : data.notify.icon,
                text: undefined == data.notify.text ? data.message : data.notify.text
            })
        }

        // closeWindow: "#window-id"
        if (data.closeWindow != undefined)
            $(data.closeWindow).modal('hide');

        if (data.runHandler != undefined)
            eval(data.runHandler + "(data)");

        if (data.goto != undefined)
            window.location.href = data.goto;

        if (data.function != undefined)
            eval(data.function);

        if (data.openDialog != undefined) {

            cuHandler.modal.dialog({
                message: data.content,
                title: data.message,
                icon: data.icon != undefined ? data.icon : '',
                width: data.width != undefined ? data.width : '',
                owner: data.owner != undefined ? data.owner : '',
                id: data.windowId != undefined ? data.windowId : '',
                animate: false,
                closeButton: data.closeButton != undefined ? data.closeButton : true,
                helpButton: data.helpButton != undefined ? data.helpButton : false,
                helpUrl: data.helpUrl != undefined ? data.helpUrl : '#',
                color: data.color != undefined ? data.color : '',
                solid: data.solid != undefined ? true : false,
            });

        }

        if (data.dynamicTable != undefined) {
            $(data.dynamicTable.table).dynamicTable(data.dynamicTable.action, {});
        }

        // Reload
        if (data.reload != undefined) {
            window.location.reload(true);
            return;
        }

    },

    showLoader: function () {

        $(".cu-window-loader").remove();
        $(".cu-window-blocker").remove();

        var html = '<div class="cu-window-blocker"></div>';
        html += '<div class="cu-window-loader">' +
            '<div class="loader-container text-center">' +
            '<button class="close" type="button">&times;</button>' +
            '<span>' +
            '<span class="cu-icon cu-spinner" style="animation: cu-spin 1s infinite steps(10)">' +
            '<svg version="1.1" id="L9" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"\n' +
            '  viewBox="0 0 100 100" enable-background="new 0 0 0 0" xml:space="preserve">\n' +
            '    <path d="M73,50c0-12.7-10.3-23-23-23S27,37.3,27,50 M30.9,50c0-10.5,8.5-19.1,19.1-19.1S69.1,39.5,69.1,50">\n' +
            '      <animateTransform \n' +
            '         attributeName="transform" \n' +
            '         attributeType="XML" \n' +
            '         type="rotate"\n' +
            '         dur="1s" \n' +
            '         from="0 50 50"\n' +
            '         to="360 50 50" \n' +
            '         repeatCount="indefinite" />\n' +
            '  </path>\n' +
            '</svg>' +
            '</span>' + ' ' + cuLanguage.inProgress + '</span>' +
            '</div></div>';

        $('body').append(html);
        $("div.cu-window-blocker").show();
        $("div.cu-window-loader").addClass('active');

    },

    closeLoader: function (handler) {
        $(".cu-window-loader").fadeOut(1, function () {
            $(".cu-window-blocker").fadeOut(0, function () {
                $(".cu-window-loader").remove();
                $(".cu-window-blocker").remove();
                if (handler != 'undefined')
                    handler;
            });
        });
    },

    getURI: function (module, controller, action, zone, params) {
        return this.getControllerURI(module, controller, action, zone, params);
    },

    getControllerURI: function (module, controller, action, zone, params) {

        var url = xoUrl;

        if (cu_modules[module] != undefined && controller != undefined && controller != '') {

            url += zone == 'backend' ? '/admin' : '';
            url += cu_modules[module] + '/' + controller + '/' + action + '/';

        } else {

            url += '/modules/' + module;

            url += zone == 'backend' ? '/admin' : '';
            if (controller == '' || controller == undefined)
                return url;

            url += '/index.php/' + controller + '/' + action + '/';

        }

        if (params == undefined)
            return url;

        var query = '';
        for (key in params) {
            query += (query == '' ? '?' : '&') + key + '=' + eval('params.' + key);
        }

        return url + query;

    },

    /**
     * Get the absolute or relative URL according to a given path
     * @param url
     * @param relative
     * @returns {string}
     */
    url: function (url, relative) {

        // Get the hostname
        var host = window.location.protocol + '//' + window.location.host;

        if (window.location.port != '') {
            host += ':' + window.location.port;
        }

        var baseUrl = xoUrl.replace(host, '');

        if (undefined == url) {
            return xoUrl;
        }

        if (arguments.length == 1 || true != relative) {
            return xoUrl + url;
        }

        return baseUrl + url;
    },

    /**
     * Crea una tabla de datos
     * @param ele
     */
    createDataTable: function (ele) {

        if (ele.hasClass("dataTable"))
            return;

        var exclude = $(ele).data("exclude");
        var cols = exclude != undefined ? exclude.toString().split(",") : '';

        $(ele).dataTable({
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": $(ele).data('source'),
            "bPaginate": true,
            //"aoColumnDefs": [exclude != undefined ? {"bSortable": false, "aTargets": cols} : '']
        });

    },

    /**
     * Ejecuta una acción asociada a un elemento específico
     */
    runAction: function (e) {

        var action = $(e).data("action");

        switch (action) {
            case 'load-remote-dialog':
            case 'load-module-dialog':
                cuHandler.loadRemoteDialog(e);
                break;
            case 'goto':
                var url = $(e).data("url");
                if (url == undefined)
                    url = $(e).attr("href");
                var retriever = $(e).data("retriever");

                if (retriever != undefined)
                    url = url + eval(retriever + '(e)');

                if (url != undefined && url != '')
                    window.location.href = url;
                break;
            case 'ajax':
                var url = $(e).attr('href') != '' ? $(e).attr('href') : $(e).data('url');
                cuHandler.requestAjax(e, {url: url});
            case 'register':
                this.registerForm({
                    item: $(e).data('item'),
                    type: $(e).data('type')
                });
                break;
            default:

                if ('' == action) {
                    return;
                }

                var parts = action.split('.');

                if (parts.length < 1) {
                    return;
                }

                if (parts.length == 1) {
                    if (undefined == window[parts[0]]) {
                        return;
                    }

                    window[parts[0]]();
                    return;
                }

                if (parts.length == 2) {
                    if (undefined == window[parts[0]][parts[1]]) {
                        return;
                    }

                    window[parts[0]][parts[1]]();
                }

                if (parts.length == 3) {
                    if (undefined == window[parts[0]][parts[1]][parts[2]]) {
                        return;
                    }

                    window[parts[0]][parts[1]][parts[2]]();
                }
                break;
        }

    },

    enableCommands: function (id_activator, type) {

        var commands = $("*[data-activator='" + id_activator + "']");

        var total = $("#" + id_activator + " :" + type + ":checked").length;

        $(commands).each(function (index) {

            var required = $(this).data("oncount") != undefined ? $(this).data("oncount") : ' >= 1';

            if (eval('total ' + required))
                $(this).enable();
            else
                $(this).disable();


        });

    },

    registerForm: function (params) {

        if (undefined == params) {
            this.notify({
                type: 'alert-danger',
                icon: 'svg-rmcommon-error',
                text: cuLanguage.dataInvalid
            });
            return false;
        }

        if (undefined == params.item) {
            this.notify({
                type: 'alert-danger',
                icon: 'svg-rmcommon-error',
                text: cuLanguage.noItemRegister
            });
            return false;
        }

        if (undefined == params.type) {
            this.notify({
                type: 'alert-danger',
                icon: 'svg-rmcommon-error',
                text: cuLanguage.noTypeRegister
            });
            return false;
        }

        var params = {
            CUTOKEN_REQUEST: $("#cu-token").val(),
            action: 'form',
            dir: params.item,
            type: params.type
        };

        $.post(xoUrl + '/modules/rmcommon/ajax/register.php', params, function (response) {

            if (false == cuHandler.retrieveAjax(response)) {
                return false;
            }

            if (undefined == response.form) {
                return false;
            }

            $("body")
                .addClass('registering')
                .append(response.form);

            $("#rmc-register-overlay").fadeIn(300, function () {
                $("#rmc-register-form").fadeIn(250);
            });

        }, 'json');

    },

    sendRegistration: function () {
        var $form = $("#rmc-register-form");
        var email = $form.find("#register-email").val();
        var api = $form.find("#register-api").val();
        var key = $form.find("#register-key").val();
        var type = $form.find("#register-type").val();
        var item = $form.find("#register-item").val();
        var error = false;

        if (undefined == email || '' == email || false == this.checkEmail(email)) {
            $form.find("#register-email").parent().addClass('error');
            error = true;
        }
        if (undefined == api || '' == api) {
            $form.find("#register-api").parent().addClass('error');
            error = true;
        }
        if (undefined == key || '' == key) {
            $form.find("#register-key").parent().addClass('error');
            error = true;
        }

        if (error) {
            cuHandler.notify({
                text: cuLanguage.thereAreErrors,
                icon: 'svg-rmcommon-error',
                type: 'alert-danger'
            });
            return;
        }

        var params = {
            CUTOKEN_REQUEST: $("#cu-token").val(),
            action: 'register',
            api: api,
            email: email,
            key: key,
            dir: item,
            type: type
        };

        $.post(xoUrl + '/modules/rmcommon/ajax/register.php', params, function (response) {

            if (false == cuHandler.retrieveAjax(response)) {
                if (undefined != response.code && response.code == 'reactivate') {
                    cuHandler.showReactivation(response);
                }
                return false;
            }

            $("#rmc-register-form .form-group").fadeOut(250, function () {
                $(this).remove();
            });

            setTimeout(function () {
                $("#rmc-register-form").append(response.activation);
            }, 300);

        }, 'json');

    },

    showReactivation: function (response) {

        $("#rmc-register-form").fadeOut(250, function () {
            $(this).remove();
        });

        if(undefined == response.form || '' == response.form){
            cuHandler.notify({
                'text': cuLanguage.invalidResponse,
                'type': 'alert-warning',
                'icon': 'svg-rmcommon-alert'
            });
            return false;
        }

        $("body").append(response.form);
        $("#rmc-reactivate-form").fadeIn(300);

    },

    sendReactivation: function(){
        var $form = $("#rmc-reactivate-form");
        var email = $form.find("#reactivate-email").val();
        var api = $form.find("#reactivate-api").val();
        var key = $form.find("#reactivate-key").val();
        var type = $form.find("#reactivate-type").val();
        var item = $form.find("#reactivate-item").val();
        var license = $form.find("#reactivate-license").val();
        var error = false;

        if (undefined == key || '' == key) {
            $form.find("#reactivate-key").parent().addClass('error');
            error = true;
        }

        if (error) {
            cuHandler.notify({
                text: cuLanguage.thereAreErrors,
                icon: 'svg-rmcommon-error',
                type: 'alert-danger'
            });
            return;
        }

        var params = {
            CUTOKEN_REQUEST: $("#cu-token").val(),
            action: 'reactivate',
            api: api,
            email: email,
            key: key,
            license: license,
            dir: item,
            type: type
        };

        $.post(xoUrl + '/modules/rmcommon/ajax/register.php', params, function (response) {

            if (false == cuHandler.retrieveAjax(response)) {
                return false;
            }

            $("#rmc-reactivate-form .form-group, #rmc-reactivate-form .help-block").fadeOut(250, function () {
                $(this).remove();
            });

            setTimeout(function () {
                $("#rmc-reactivate-form").append(response.activation);
            }, 300);

        }, 'json');
    },

    /*------------------------------------------------
     1.7 GET SVG ICON
     ------------------------------------------------*/
    getIcon: function (icon) {

        // Get an SVG icon from providers

        var isSVG = 'svg-' == icon.slice(0, 4);
        var isAbsolute = false;
        var isUrl = false;
        if (!isSVG) {
            isSVG = '.svg' == icon.slice(-4);

            if (isSVG) {
                isUrl = true;
            }
        }

        isAbsolute = null != icon.match(/^(http:\/\/|ftp:\/\/|https:\/\/\|\/\/)/i);

        if (isSVG && !isUrl) {
            var parts = icon.split("-");

            if (parts.length < 3) {
                return this.url("modules/rmcommon/icons/noicon.svg");
            }

            var fileName = icon.slice(5 + parts[1].length);

            var defaultFile = this.url("modules/rmcommon/icons/" + fileName + '.svg');

            if (undefined == iconsProviders) {
                return defaultFile;
            }

            if (iconsProviders.hasOwnProperty(parts[1])) {
                var file = iconsProviders[parts[1]] + '/' + fileName + '.svg';
                return file;
            }

            return defaultFile;
        }

        // SVG from URL
        if (isUrl && !isAbsolute) {
            return this.url(icon);
        }

        // Get an image
        var images = ['.jpg', '.gif', '.png', 'jpeg'];
        var ext = icon.slice(-4);

        if (images.indexOf(ext) || isAbsolute) {
            return icon;
        }

        // Get a icon from font
        return '<span class="' + icon + '"></span>';

    },

    /*------------------------------------------------
     1.8 LOAD ICON INSIDE CONTAINER
     ------------------------------------------------*/
    /**
     * Load an icon inside a container.
     * @param icon Icon path or name to use
     * @param container Container DOM element
     * @param replace Indicate if replace current xo-icon-svg or use existent
     * @returns {boolean}
     */
    loadIcon: function (icon, container, replace) {

        // We need two arguments
        if (arguments.length < 1) {
            return false;
        }

        replace = arguments.length == 2 ? true : (arguments.length == 1 ? false : replace);

        var file = this.getIcon(icon);

        var is_svg = file.slice(-4) == '.svg';
        var is_font = file.slice(0, 5) == '<span';

        if (replace) {
            var iconLoaded = container.find('.cu-icon');
        } else {
            var iconLoaded = $("<span />", {"class": 'cu-icon'});
        }

        if (undefined == iconLoaded || iconLoaded.length <= 0) {
            replace = false;
            iconLoaded = $("<span />", {"class": 'cu-icon'});
        }

        // Load a SVG icon
        if (is_svg) {
            iconLoaded.html('').load(file);
        } else if (is_font) {
            iconLoaded.html('').append(file);
        } else {
            /* If it is not a SVG icon then it's an image (?) */
            var img = $("<img>").attr("src", file);
            iconLoaded.html('').append(img);
        }

        if (!replace && arguments.length > 1) {
            $(container).append(iconLoaded);
        }

        return iconLoaded;

    },

    modal: modals,

    /**
     * This is a wrapper for PNotify plugin
     * See http://sciactive.com/pnotify/ for docs
     * @param options To be passed to PNotify plugin
     */
    notify: function (options) {
        return new PNotify(options);
    },

    /**
     * Images manager launcher
     * <pre>
     * cuHandler.imagesManager({
     *     type: 'tiny|html|markdown|simple|external',
     *     target: 'container|function name',
     *     idContainer: 'container id',
     *     multiple: 'yes|no',
     *     title: 'title for modal'
     * }
     * </pre>
     */
    imagesManager: function (options) {

        var html = '<div id="blocker-' + options.idContainer + '" class="mgr_blocker"></div><div id="window-' + options.idContainer + '" class="imgmgr_container">';

        html += '<div class="window-title cu-titlebar"><button type="button" class="close">&times;</button>' + options.title + '</div>';
        html += '<iframe src="' + xoUrl + '/modules/rmcommon/include/tiny-images.php?type=' + options.type + '&amp;idcontainer=' + options.idContainer + '&amp;editor=' + options.idContainer + '&amp;target=' + options.target + '&amp;&amp;multi=' + options.multiple + '" name="image"></iframe>'
        html += '</div>';

        $("body").append(html);

        // window height


        $("#blocker-" + options.idContainer).fadeIn('fast', function () {
            $("body").css('overflow', 'hidden');
            $("#window-" + options.idContainer).fadeIn('fast', function () {

            });

        });

        $("#blocker-" + options.idContainer + ", #window-" + options.idContainer + " .window-title .close").click(function () {

            $("#window-" + options.idContainer).fadeOut('fast', function () {

                $("#blocker-" + options.idContainer).fadeOut('fast', function () {
                    $("body").css('overflow', 'auto');
                    $("#window-" + options.idContainer).remove();
                    $("#blocker-" + options.idContainer).remove();

                });

            })

        });

    },

    template: function (template, data) {

        var tpl = $.templates(template);
        return tpl.render(data);

    },

};

/**
 * Currenty format
 */
Number.prototype.formatMoney = function (c, d, t) {
    var n = this,
        c = isNaN(c = Math.abs(c)) ? 2 : c,
        d = d == undefined ? "." : d,
        t = t == undefined ? "," : t,
        s = n < 0 ? "-" : "",
        i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "",
        j = (j = i.length) > 3 ? j % 3 : 0;
    return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
};

/**
 * jQuery plugin to enable or disable an element
 */
jQuery.fn.enable = function () {
    this.each(function () {
        jQuery(this).removeAttr("disabled");
        jQuery(this).removeClass("disabled");
    });
}

jQuery.fn.disable = function () {
    this.each(function () {
        jQuery(this).prop("disabled", true);
        jQuery(this).addClass("disabled");
    });
}

$(document).ready(function () {

    var textarea_style = '';

    $("body").on('click', "#rmc-register-form > .close, #rmc-reactivate-form > .close", function () {
        $("#rmc-register-form, #rmc-reactivate-form").fadeOut(250, function () {
            $("#rmc-register-overlay").fadeOut(300, function () {
                $("#rmc-register-form, #rmc-reactivate-form").remove();
                $("#rmc-register-overlay").remove();
            });
        });
    });

    $("body").on('click', "#rmc-register-form button.btn", function () {
        cuHandler.sendRegistration();
    });

    $("body").on('click', "#rmc-reactivate-form button.btn", function () {
        cuHandler.sendReactivation();
    });

    $("body").on('keyup', "#rmc-register-form .form-group.error .form-control", function (e) {
        if (e.which != 13) {
            if ($(this).attr('name') == 'email') {
                if (cuHandler.checkEmail($(this).val())) {
                    $(this).parent().removeClass('error');
                }
                return;
            }
            if ('' != $(this).val()) {
                $(this).parent().removeClass('error');
                return;
            }
        }
    });

    /**
     * Cargar diálogos de otros módulos
     */
    $('body').on('click', '[data-action]', function (e) {
        if ($(this).is(":disabled") || $(this).attr("disabled") || $(this).hasClass('disabled')) {
            $(e).stopPropagation();
            return false;
        }
        cuHandler.runAction($(this));
        return false;

    });

    $("body").on('submit', 'form[data-type="ajax"]', function () {
        cuHandler.submitAjaxForm($(this));
        return false;
    });

    // Prevent submission of forms no-submit
    $("body").on('submit', 'form[data-type="no-submit"]', function () {
        return false;
    });

    $('body').on("click", ".cu-window-loader .close", function () {
        cuHandler.closeLoader();
    });

    $(".cu-data-table").each(function () {
        cuHandler.createDataTable($(this));
    });

    /**
     * Activar comandos
     */
    $("body").on('change', '.activator-container :checkbox[data-switch], .activator-container :radio[data-switch]', function () {

        event.stopPropagation();

        var id_container = $(this).parents(".activator-container").attr("id");

        cuHandler.enableCommands(id_container, $(this).attr("type"));

        if ($(this).attr("type") == 'radio') {
            $(this).parents(".activator-container").find(".tr-checked").removeClass('tr-checked');
        }

        if ($(this).is(":checked"))
            $(this).parents("tr").addClass('tr-checked');
        else
            $(this).parents("tr").removeClass('tr-checked');

    });

    /**
     * Select all checkbox
     */
    $("body").on("change", ":checkbox[data-checkbox]", function () {

        var checkbox_class = $(this).data("checkbox");

        if (checkbox_class == undefined)
            return;

        $(":checkbox[data-oncheck='" + checkbox_class + "']").prop('checked', $(this).prop('checked')).change();

        var $activator = $(this).parents('.activator-container');
        if ($activator.length > 0) {
            cuHandler.enableCommands($activator.attr("id"), 'checkbox');
        }

    });

    $("body").on("change", ':checkbox[data-oncheck]', function () {

        /*if (this.hasAttribute('data-checkbox'))
            return;*/

        /*if (!this.hasAttribute('data-oncheck'))
            return;*/

        var total = $(":checkbox[data-oncheck='" + $(this).data('oncheck') + "']").length;

        //var existing = $(":checkbox[data-oncheck='" + $(this).data('oncheck') + "']");
        var checked = $(":checkbox[data-oncheck='" + $(this).data('oncheck') + "']:checked");

        var activator = $(":checkbox[data-checkbox='" + $(this).data('oncheck') + "']");

        //if ( checked.length < existing.length )
        if (checked.length < total && undefined == $(activator).attr('data-oncheck')) {
            $(activator).prop('checked', false);
        }  else if (checked.length >= total) {
            $(activator).prop('checked', true);
        }

    });


    /**
     * Check if there are a "News" box in current page
     * and then, load the news for this module
     */
    if ($("*[data-news='load']").length == 1 || $("*[data-boxes='load']").length > 0) {

        var container = $("*[data-news='load']");
        if (container.length <= 0)
            container = $("*[data-boxes='load']");

        if (container.length <= 0)
            return false;

        var module = container.data('module');
        var target = $(container.data('target'));

        if (target != undefined)
            target.html('<div class="text-success"><span class="fa fa-spinner fa-pulse"></span> ' + cuLanguage.downloadNews + '</div>');

        var bcontainer = $("*[data-boxes='load']");
        var lang = $("html").attr('lang');
        lang = undefined == lang ? 'en' : lang;

        var params = {
            module: module,
            CU_TOKEN: $("#cu-token").val(),
            lang: lang
        };

        $.get(xoUrl + '/modules/rmcommon/ajax/module-info.php', params, function (response) {

            if (response.type == 'error') {
                target.html('<div class="text-danger"><span class="fa fa-exclamation-triangle"></span> ' + cuLanguage.downloadNewsError + '</div>')
                return;
            }

            /**
             * Get News
             */
            if (response.news != undefined && target != undefined) {

                news = $("<ul>").addClass("cu-ajax-news list-unstyled");
                for (i = 0; i < response.news.length; i++) {

                    var html = '<li>' +
                        '<small>' + response.news[i].date + '</small>' +
                        '<h5><a href="' + response.news[i].link + '" target="_blank">' + response.news[i].title + '</a></h5>';
                    /*if ( response.news[i].image )
                     html += '<img src="'+response.news[i].image+'" class="img-responsive">';*/

                    html += '<p class="help-block">' + response.news[i].content + '</p>' +
                        '</li>';
                    news.append(html);

                }
                target.html('').append(news);
                news.fadeIn('fast', function () {
                    $('html.dashboard [data-container="dashboard"]').trigger('containerUpdated');
                });

            }

            /**
             * Get boxes
             */
            if (response.boxes != undefined && bcontainer != undefined) {

                for (i = 0; i < response.boxes.length; i++) {

                    if (response.boxes[i].size == undefined || response.boxes[i].size <= 0) {
                        var size = 1;
                    } else {
                        size = response.boxes[i].size;
                    }

                    var box = $("<div data-dashboard=\"item\">").addClass('size-' + size).css("display", 'none');
                    box.append('<div class="cu-box ' + response.boxes[i].class + '"><div class="box-header">' +
                        '<span class="fa fa-caret-up box-handler"></span>' +
                        '<h3 class="box-title">' + response.boxes[i].title + '</h3></div>' +
                        '<div class="box-content">' + response.boxes[i].content + '</div></div>');
                    // Get the box position
                    if (response.boxes[i].container != undefined) {
                        var box_container = $(response.boxes[i].container);
                        if (box_container.length > 0) {
                            $(box_container).each(function () {
                                var newbox = box.clone();
                                if (response.boxes[i].position == 'top')
                                    $(this).prepend(newbox);
                                else
                                    $(this).append(newbox);
                                newbox.fadeIn('fast', function () {
                                    $('html.dashboard [data-container="dashboard"]').trigger('containerUpdated');
                                });

                            });
                        }
                    }

                }

            }


        }, 'json');

    }

    /**
     * Editor full screen
     */
    $("body").on('click', ".ed-container .full-screen", function () {
        $(this).parents(".ed-container").addClass('full-screen-edit');
        $(this).removeClass('full-screen').addClass('normal-screen');
        textarea_style = $(this).parents(".ed-container").find('.txtarea-container').attr("style");

        $(this).parents(".ed-container").find('.txtarea-container').attr("style", '');
        $("body").css("overflow", 'hidden');

    });
    $("body").on('click', ".ed-container .normal-screen", function () {
        $(this).parents(".ed-container").removeClass('full-screen-edit');
        $(this).addClass('full-screen').removeClass('normal-screen');
        $(this).parents(".ed-container").find('.txtarea-container').attr("style", textarea_style);
        textarea_style = '';
        $("body").css("overflow", 'visible');
    });

    /**
     * Set user notifications
     */
    $(".cu-notifications form .notification-item").change(function () {

        var event = $(this).data('event');
        var status = $(this).is(":checked") ? 1 : 0;
        var item = $(this);

        var params = {
            event: event,
            status: status,
            page: 'cu-notification-subscribe'
        };

        $.post(xoUrl + '/notifications.php', params, function (response) {

            if ('error' == response.type) {
                alert(response.message);
                return;
            }

            $(item).parent().parent()
                .animate({
                    backgroundColor: '#ffff99'
                }, 500, 'linear', function () {
                    $(this).animate({
                        backgroundColor: 'transparent'
                    }, 500, 'linear');
                });

        }, 'json');

    });

    $(" #cu-notifications .cancel-subscription").click(function () {

        var event = $(this).data('info');
        var status = 0;
        var item = $(this);

        var params = {
            event: event,
            status: status,
            page: 'cu-notification-subscribe'
        };

        $.post(xoUrl + '/notifications.php', params, function (response) {

            if ('error' == response.type) {
                alert(response.message);
                return;
            }

            $(item).parent().parent()
                .slideUp(250, function () {
                    $(this).remove();
                });

        }, 'json');

    });

});

