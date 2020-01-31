/**
 *News 
 */
var KaikMedia = KaikMedia || {};
KaikMedia.Pages = KaikMedia.Pages || {};
KaikMedia.Pages.Manager = KaikMedia.Pages.Manager || {};

//single page list management
(function ($) {
    KaikMedia.Pages.Manager.preview = (function () {
        
        var $list;
        
        // Init
        var data = {
        };
        var settings = {
            limit: 100,
            ajax_timeout: 10000,
	    accept: 'image/*'
        }
        
	 // Init
        function init()
        {
            console.log('KaikMedia.Pages.Manager.preview 1.0');
        }
        ;

        // Read item list from html
        function manageItem(index, $item)
        {
            createNewsItem($item)
            .done( managedItem => {
                selected.push(managedItem);
            });
        }
        ;
        // Read item list from html
        function createNewsItem($item)
        {
            var deferred = $.Deferred();
            var item = new KaikMedia.Pages.model.newsItem();
            item.setItemFromView($item);
            deferred.resolve(item);
            return deferred.promise();
        }
        ;





        //return this and init when ready
        return {
            init: init
        };  
    })();
    $(document).ready(function() {
        KaikMedia.Pages.Manager.preview.init();
    });
})(jQuery);