/**
 * Handle: wpQCPAdmin
 * Version: 0.0.1
 * Deps: jquery
 * Enqueue: true
 */

var wpQCPAdmin = function () {}

wpQCPAdmin.prototype = {
    options           : {},
    generateShortCode : function() {
        var content = this['options']['content'];
        delete this['options']['content'];

        var attrs = '';
		var content = document.getElementById('wpQCP_url').value;
		var returnnum = document.getElementById('wpQCP_return').value;
			if (returnnum != '') {
                attrs += ' return="' + returnnum + '"';
            }
		var previewnum = document.getElementById('wpQCP_preview').value;
			if (returnnum != '') {
                attrs += ' preview="' + previewnum + '"';
            }
		return '[QuickCafe' + attrs + ']' + content + '[/QuickCafe]'
    },
    sendToEditor      : function(f) {
        var collection = jQuery(f).find("input[id^=wpQCPName]:not(input:checkbox),input[id^=wpQCPName]:checkbox:checked");
        var $this = this;
        collection.each(function () {
            var name = this.name.substring(13, this.name.length-1);
            $this['options'][name] = this.value;
        });
        send_to_editor(this.generateShortCode());
        return false;
    }
}

var this_wpQCPAdmin = new wpQCPAdmin();