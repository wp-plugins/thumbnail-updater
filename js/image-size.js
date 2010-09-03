var imageSizes;

(function($) {
    imageSizes = {
	iasapi : {},
	hold : {},
	postid : '',

	intval : function(f) {
            return f | 0;
	},

        open : function(postid, nonce) {
            var data, elem = $('#image-editor-' + postid), head = $('#media-head-' + postid),
                    btn = $('#thumbnail-updater-' + postid), spin = btn.siblings('img');

            btn.attr('disabled', 'disabled');
            spin.css('visibility', 'visible');
            
            data = {
                    'action': 'display-thumbnails',
                    '_ajax_nonce': nonce,
                    'postid': postid,
                    'do': 'open'
            };

            elem.load(ajaxurl, data, function() {
                    elem.fadeIn('fast');
                    head.fadeOut('fast', function(){
                            btn.removeAttr('disabled');
                            spin.css('visibility', 'hidden');
                    });
            });
        },

        close: function () {
            $('.image-editor').fadeOut('fast', function() {
                    $('.media-item-info').fadeIn('fast');
                    $(this).empty();
            });

        },

        update : function (postid, nonce) {
            var data, elem = $('#thumb-update-' + postid),
                    btn = $('#update-thumbnail-' + postid), spin = btn.siblings('img');

            btn.attr('disabled', 'disabled');
            spin.css('visibility', 'visible');
            
            
            data = {
                    'action'      : 'update-thumbnail',
                    '_ajax_nonce' : nonce,
                    'postid'      : postid,
                    'do'          : 'update'
            };

            $.post(ajaxurl, data, function(r){

                btn.removeAttr('disabled');
                spin.css('visibility', 'hidden');

                if (r.thumbnail_updated) {
                    elem.addClass('updated')
                        .fadeIn('slow')
                        .delay(5000)
                        .fadeOut('slow', function(){
                            var container = $('#image-editor-' + data.postid);

                             refreshData = {
                                    'action': 'display-thumbnails',
                                    '_ajax_nonce': data._ajax_nonce,
                                    'postid': data.postid,
                                    'do': 'open'
                            };
                            
                            container.load(ajaxurl, refreshData);
                        });

                    elem.html('Updated!')                                        
                } else {
                    elem.addClass('updated')
                        .fadeIn('slow')
                        .delay(5000)
                        .fadeOut('slow');
                    elem.html('Error!');
                }                
            });
        }

    }
 
})(jQuery);

