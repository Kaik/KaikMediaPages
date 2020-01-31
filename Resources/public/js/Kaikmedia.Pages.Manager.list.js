/**
 *News 
 */
var KaikMedia = KaikMedia || {};
KaikMedia.Pages = KaikMedia.Pages || {};
KaikMedia.Pages.Manager = KaikMedia.Pages.Manager || {};

//single page list management
(function ($) {
    KaikMedia.Pages.Manager.list = (function () {
        
        var $list;
        
        // Init
        var data = {
        };
        var settings = {
            limit: 100,
            ajax_timeout: 10000,
	    accept: 'image/*'
        }
        
        var selected = [];
        
        var actions = [];
        
	 // Init
        function init()
        {
            console.log('KaikMedia.Pages.Manager.list 1.0');
            $list = $('#itemsList');

            getListFromHtml();
//            console.log(this);
        }
        ;

        // Read item list from html
        function getListFromHtml()
        {
            $list.children().each(manageItem);
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
            var item = new KaikMedia.Pages.model.pageItem();
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
        KaikMedia.Pages.Manager.list.init();
    });
})(jQuery);