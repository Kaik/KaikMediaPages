/**
 * Item form 
 */

var KaikMedia = KaikMedia || {};
KaikMedia.Pages = KaikMedia.Pages || {};
KaikMedia.Pages.model = KaikMedia.Pages.model || {};
/*
 * News item
 *
 */
(function ($) {
    // constructor
    KaikMedia.Pages.model.pageItem = function () {

        var item = {
            id: false,
            title: '',
            urltitle: '',

            description: '',
            content: '',

            author: null,

            views: 0,

            parent: null,
            categoryAssignments: null, // list as id's

            online: 1,
            depot: 0,
            inmenu: 1,
            inlist: 1,
            language: '',
            layout: '',

            publishedAt: null,
            expiredAt: null,

            status: 'A',

            createdAt: false,
            createdBy: false,
            updatedAt: false,
            updatedBy: false,
            deletedAt: false,
            deletedBy: false
        };

        var itemView = function () {
            var $item;

            function setView($itemRaw) {
                //as jqueryObj
                $item = $($itemRaw);
            }
            
            function bindActions() {
                
                $item.on('click', '.action', function (e) {
//                    e.preventDefault();
//                    handleAction($(this).data());
                });
                
                $item.on('click', '.action-media', function (e) {
                    e.preventDefault();
                    handleMedia();
                });
                
                $item.on('click', '.action-status', function (e) {
                    e.preventDefault();
                    handleStatus($(this).data());
                });
                
                $item.on('click', '.action-property', function (e) {
                    e.preventDefault();
                    handleProperty($(this).data());
                });
                
                $item.on('click', '.action-userselect', function (e) {
                    e.preventDefault();
                    handleUserSelect($(this).data());
                });
            }
            
            function handleAction(data) {
                //edit, remove/ preview etc..
                performAction(data);
                // done etc for now it is not needed
            }
            
            function handleMedia() {
                console.log('load gallery');
            }
            
            function handleStatus(data) {
                toggleStatus(data)
                .done(newStatus => {
                    updateStatusField(data.id, data.name, newStatus.value, data.texton, data.textoff, data.labelon, data.labeloff );
                })
                .fail()
                //well we will fail quietly 
                ;
            }

            function updateStatusField(id, name, value, textOn, textOff, labelOn, labelOff ) {
                var $switch = $item.find('.' + name + '-onoff-' + id);
                // we will use item data it should
                if (value) {
                    $switch
                        .removeClass(labelOff)
                        .addClass(labelOn)
                        .text(textOn)
                        .attr('data-value', value)
                        .attr('title', textOn);
                } else {
                    $switch
                        .removeClass(labelOn)
                        .addClass(labelOff)
                        .text(textOn)
                        .attr('data-value', value)
                        .attr('title', textOff);
                }
            }
            
            function handleProperty(data) {
                console.log(data);
                updateProperty(data)
                    .done(newData => {
                        updateDropdownField(data.id, data.name, newData.value);
                    })
                    .fail()
                    ;
            }
            
            function updateDropdownField(id, name, value) {
                var $menuitem = $item.find('.' + name + '-menuitem-' + id + "[data-value='" + value +"']");
                
                $menuitem.parents('.dropdown-menu') //up 
                        .find('li').removeClass('disabled') //down
                        .find('.fa-check-circle') //down
                            .removeClass('fa-check-circle text-primary')
                            .addClass('fa-circle')
                    ;
                
                // internal active
                $menuitem.parent().addClass('disabled');
                $menuitem.find('.fa-circle')
                            .removeClass('fa-circle')
                            .addClass('fa-check-circle text-primary')
                ;
                // button text and colour
                $menuitem.parents('.dropdown')
                            .find('.dropdown-toggle span')
                                .removeClass('label-default')
                                .addClass('label-info')
                                .text($menuitem.text())
                ;
            }
            
            function handleUserSelect(data) {
                console.log(data);
//                updateProperty(data)
//                    .done(newData => {
//                        updateDropdownField(data.id, data.name, newData.value);
//                    })
//                    .fail()
//                    ;
            }            
            
            
            return {
//                render: render,
                setView: setView,
                bindActions: bindActions,
            };
        };   
            
        var view = new itemView();

        this.setItemFromView = function ($itemRaw) {
            var data_item = $($itemRaw).find('.itemData').data();
            $.extend(item, data_item);
            view.setView($itemRaw);
            view.bindActions();
        };
        
        updateItemProperty = function (name, value) {
            // id name value
            if (item.hasOwnProperty(name)) { 
                item[name] = value;
            }

            return this;
        }        
        
        function performAction(data) {
            // id name value
            return data;
        }
        
        function toggleStatus(data) {
            var deferred = $.Deferred();
            // check data
//            if (data.id !== 'undefined')
            $.ajax({
                type: 'GET',
                url: Routing.generate('kaikmediapagesmodule_manager_toggle', {'id':  data.id, 'property': data.name, 'format': 'json'}),
            }).done( statusJson => {
                var status = JSON.parse(statusJson);
                updateItemProperty(status.name, status.value);
                deferred.resolve(status);
            }).fail( error => {
                console.log(JSON.parse(error));
            });
            
            return deferred.promise();
        }
        
        function updateProperty(data) {
            var deferred = $.Deferred();
            // check data
            if (data.id == 'undefined') {
                
            }
            $.ajax({
                type: 'POST',
                url: Routing.generate('kaikmediapagesmodule_manager_update', {'id':  data.id, 'format': 'json'}),
                data: JSON.stringify(data),
            }).done( statusJson => {
                var status = JSON.parse(statusJson);
                updateItemProperty(status.name, status.value);
                console.log(status);
                deferred.resolve(status);
            }).fail( error => {
                console.log(data);
            });
            
            return deferred.promise();
        }
        

    };
    //news model ends here
}(jQuery));
