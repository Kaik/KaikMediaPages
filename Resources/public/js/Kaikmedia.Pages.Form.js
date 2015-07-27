/**
 *Pages form 
 */

var KaikMedia = KaikMedia || {};
KaikMedia.Pages = KaikMedia.Pages || {};
KaikMedia.Pages.Form = {};

( function($) {
    // content
      
    KaikMedia.Pages.Form.init = function ()
    {
    	$form = $('form[name=pageform]');
    	if ( $form.length ) {    	
    	
    		KaikMedia.Pages.Form.textarea();
    	}
    };
       
    KaikMedia.Pages.Form.textarea = function ()
    {   
	    tinymce.init({
	        selector: "textarea#pageform_content",
	        theme: "modern",
	        height: 800,
	        plugins: [
	             "advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
	             "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
	             "save table contextmenu directionality emoticons template paste textcolor"
	       ],
	       menubar: false,
	       content_css: "/web/modules/kaikmediapages/css/content.css",
	       toolbar1: "gallery | code print preview",      	       
	       toolbar2: "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link charmap table image ",      
	       style_formats: [
	       	    {title: 'Headers'},           
   	            {title: 'H1', block: 'h1'},
	            {title: 'H2', block: 'h2'},
	    	    {title: 'H3', block: 'h3'},
	    	    {title: 'H4', block: 'h4'},	  
	    	    {title: 'H5', block: 'h5'},	  
	    	    {title: 'H6', block: 'h6'}
	        ]
	     });     
    };   
                
    $(document).ready(function() {
        KaikMedia.Pages.Form.init();
    });
})(jQuery);