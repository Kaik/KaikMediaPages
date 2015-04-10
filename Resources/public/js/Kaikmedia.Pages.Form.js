/**
 *Pages form 
 */

var KaikMedia = KaikMedia || {};
KaikMedia.Pages = KaikMedia.Pages || {};
KaikMedia.Pages.Form = {};

( function($) {
    // content
    var $collectionHolder;   
    
    KaikMedia.Pages.Form.init = function ()
    {
        // Get the ul that holds the collection of tags
        $collectionHolder = $('ul.images');    

        // add a delete link to all of the existing tag form li elements
        $collectionHolder.find('li').each(function() {
            KaikMedia.Pages.Form.addFormDeleteLink($(this));           
        });             
        KaikMedia.Pages.Form.detailsAdd();
    };
    
    KaikMedia.Pages.Form.addFormDeleteLink = function ($FormLi)
    {
        var $removeFormA = $('<a href="#"><i class="fa fa-trash"> </i></a>');
        $FormLi.append($removeFormA);

        $removeFormA.on('click', function(e) {
            // prevent the link from creating a "#" on the URL
            e.preventDefault();
            // remove the li for the tag form
            $FormLi.remove();
        });
    };     

    KaikMedia.Pages.Form.detailsAdd = function ()
    {
        // setup an "add a tag" link
        //var $addLink = $('<a href="#"><i class="fa fa-plus"> </i></a>');
        var $newLi = $('<li class="col-xs-12 col-md-3 form-group-sm"></li>');   
        // add the "add a tag" anchor and li to the tags ul
        $collectionHolder.append($newLi);

        // count the current form inputs we have (e.g. 2), use that as the new
        // index when inserting a new item (e.g. 2)
        $collectionHolder.data('index', $collectionHolder.find(':input').length);

        $('#addnewdetail').on('click', function(e) {
            // prevent the link from creating a "#" on the URL
            e.preventDefault();
            // add a new tag form (see next code block)
            KaikMedia.Pages.Form.addForm($newLi);
        });   
    };

    
    KaikMedia.Pages.Form.addForm = function ($newLi)
    {
        // Get the data-prototype explained earlier
        var prototype = $collectionHolder.data('prototype');

        // get the new index
        var index = $collectionHolder.data('index');

        // Replace '__name__' in the prototype's HTML to
        // instead be a number based on how many items we have
        var newForm = prototype.replace(/__name__/g, index);

        // increase the index with one for the next item
        $collectionHolder.data('index', index + 1);

        // Display the form in the page in an li, before the "Add a tag" link li
        var $newFormLi = $('<li class="col-xs-12 col-md-3 form-group-sm"></li>').append(newForm);
        $newLi.before($newFormLi);
        KaikMedia.Pages.Form.addFormDeleteLink($newFormLi);
    }    

    $(document).ready(function() {
        KaikMedia.Pages.Form.init();
    });
})(jQuery);