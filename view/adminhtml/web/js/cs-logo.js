require([
    "jquery"
], function($){
    'use strict';

    var csLogo = function() {
        this.csLogoClass = 'cs-csfeature__logo';
        this.inputCsLogo = $('input.' + this.csLogoClass);
        this.init();
    };

    csLogo.prototype = {
        init: function() {
            var that = this;

            that.inputCsLogo.each(function() {
                $(this).parent().siblings('label').addClass(that.csLogoClass);
            });
        }
    };

    new csLogo();
});