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
            KaikMedia.Pages.Form.addDeleteLink($(this));
            KaikMedia.Pages.Form.addEditLink($(this)); 
        });             
        KaikMedia.Pages.Form.AddNew();
    };
    
    KaikMedia.Pages.Form.addDeleteLink = function ($FormLi)
    {
        var $removeFormA = $('<a href="#" class="btn btn-default btn-xs"> <i class="fa fa-trash"> </i></a>');
        $FormLi.find('.menu').append($removeFormA);

        $removeFormA.on('click', function(e) {
            // prevent the link from creating a "#" on the URL
            e.preventDefault();
            // remove the li for the tag form
            $FormLi.remove();
        });
    };
    
    KaikMedia.Pages.Form.addEditLink = function ($FormLi)
    {
        var $editFormA = $('<a href="#" class="btn btn-default btn-xs"> <i class="fa fa-pencil"> </i></a>');
        $FormLi.find('.menu').append($editFormA);

        $editFormA.on('click', function(e) {
            // prevent the link from creating a "#" on the URL
            e.preventDefault();
            // display editable fields
            $FormLi.find('.name input').toggleClass( "simplebox");
            $FormLi.find('.description textarea').toggleClass( "simplebox");
        });
    };       

    KaikMedia.Pages.Form.AddNew = function ()
    {
        $('#addnew').on('click', function(e) {
            // prevent the link from creating a "#" on the URL
            e.preventDefault();
            // add a new tag form (see next code block)
            KaikMedia.Pages.Form.addForm();
        });   
    };

    
    KaikMedia.Pages.Form.addForm = function ()
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
        var $newFormLi = $('<li class="col-xs-12 col-md-2 form-group-sm"></li>').append(newForm);
        $collectionHolder.find('.new').before($newFormLi);
    }    

    $(document).ready(function() {
        KaikMedia.Pages.Form.init();
    });
})(jQuery);