/**
 * Zikula.Intercom.import.js
 */

// in case not exists
var Kaikmedia = Kaikmedia || {};
Kaikmedia.Pages = Kaikmedia.Pages || {};
(function ($) {
    Kaikmedia.Pages.import = (function () {
        // Init
        var data = {
            imported: 0,
            rejected: 0
        };
        var settings = {
            limit: 100,
            ajax_timeout: 10000
        }
        ;
        function init()
        {
            $("#table-selector").change(function (e) {
                e.preventDefault();
                data.table = $(this).val();
                $("#table-selector-check").prop("disabled", false).removeClass('btn-default').addClass('btn-primary');
                log(Translator.__('Table ' + data.table + ' selected.'));
                log(Translator.__('You need to check table. Please click check button'));
            });

            $("#table-selector-check").click(function (e) {
                e.preventDefault();
                tableCheck();
            });

            log(Translator.__('Import init done.'));
            log(Translator.__('Please select table to import.'));

            $("#start_import_yes").click(function (e) {
                e.preventDefault();
                readSettings();
                calcPages();
                startImport(data);
            });
        }
        ;
        function readSettings()
        {
            settings.limit = parseInt($("#import_limit").val());
            settings.ajax_timeout = parseInt($("#import_ajax_timeout").val());
        }
        ;
        function log(log)
        {
            var log_string = '';
            if (log === '') {
            } else if (log === null) {
                log_string = Translator.__('Unknown log request!.');
            } else if (log.constructor === Array) {
                log_string = log.join('&#xA;') + '&#xA;';
            } else {
                log_string = log + '&#xA;';
            }
            $('<p class="text-muted"></p>').append('<i class="fa fa-terminal" aria-hidden="true"></i> ' + log_string).appendTo($('#import_logs'));
        }
        function tableCheck()
        {
            importAjax(Routing.generate('kaikmediapagesmodule_import_status'), data).done(function (result) {
                data = result;
                log(Translator.__('Table ' + data.table + ' contains ' + data.total + ' items to import.'));
                $("#table-selector-check").prop("disabled", true).removeClass('btn-primary').addClass('btn-default');
                $("#start_import_yes").prop("disabled", false).removeClass('btn-default').addClass('btn-primary');
                log(Translator.__('Start import?'));
            });
        }
        ;
        function startImport(data) {
            console.log(data);
            // disable table select
            $("#table-selector").prop("disabled", true);
            // disable table check button
            $("#table-selector-check").prop("disabled", true).removeClass('btn-primary').addClass('btn-default');
            // disable items per page
            $("#import_limit").prop("disabled", true);
            // disable ajax timeout
            $("#import_ajax_timeout").prop("disabled", true);
            // disable start importing
            $("#start_import_yes").prop("disabled", true).removeClass('btn-primary').addClass('btn-default');

            $('<p class="text-info"></p>')
                    .append('<i id="status_importing_icon" class="fa fa-refresh fa-spin fa-fw" aria-hidden="true"></i> ')
                    .append('<span id="status_importing">' + Translator.__('Importing... ' + '</span>'))
                    .append('<span class="text-success">' + Translator.__('Imported items: ') + ' <span id="total_imported"> ' + data.imported + ' </span></span> ')
                    .append('<span class="text-warning">' + Translator.__(' Rejected items: ') + '<span id="total_rejected">' + data.rejected + ' </span></span>')
                    .appendTo($('#import_logs'));
            $('#import_progress').removeClass('hide');
            $('#import_rejected').removeClass('hide');


//            // call import
            itemsImport(data).done(function () {
                $("#status_importing_icon").removeClass('fa-refresh fa-spin fa-fw')
                        .addClass('fa-check-circle');
                $("#import_progress").find('.progress-bar').removeClass('progress-bar-info').addClass('progress-bar-success');
                $('#import_clear_data').removeClass('hide');
            });
        }

        function calcPages() {
            if (data.total > 0) {
                data.pageSize = settings.limit;
                data.pages = Math.ceil(data.total / data.pageSize);

                return;
            }
            data.pages = 0;

            return;
        }

        function itemsImport(data) {
            //console.log(data);
            var def = $.Deferred();
            def.progress(function (data) {
                $("#total_imported").html(data.imported);
                $("#total_rejected").html(data.rejected);
                $.each(data.rejected_items, function (index, item) {
                    var reason = item.reason === 0 ? Translator.__('Empty text') : Translator.__('Already exists.');
                    $(' <span class="text-muted small"></span> ')
                            .append('<i class="fa fa-hashtag text-danger" title="' + reason + '" aria-hidden="true"></i>')
                            .append('<span class="rejected_id">' + item.id + ' </span>')
                            .appendTo($('#import_rejected'));
                });

                var percent = 100 * data.page / data.pages;
                $("#import_progress").find('.progress-bar').css('width', percent + '%').attr('aria-valuenow', percent);
                $("#import_progress").find('.info').text('Importing ' + data.page + ' page from ' + data.pages).css('color', '#000');
            });
            data.page = 0; // first page 0-49
            calcPages(); // once again
            (function loop(data, def) {
                if (data.page < data.pages || data.pages === 0) {
                    importAjax(Routing.generate('kaikmediapagesmodule_import_import'), data).done(function (data) {
                        data.page++;
                        def.notify(data);
                        loop(data, def);
                    });
                } else {
                    def.resolve(data);
                }
            })(data, def);
            return def.promise();
        }

        function clearData() {
            alert(Translator.__('Only manual data removal available at the moment.'));
        }

        //ajax util
        function importAjax(url, data) {
            console.log(data);
            return $.ajax({
                type: 'POST',
                url: url,
                data: JSON.stringify(data),
                timeout: settings.ajax_timeout,
                contentType: "application/json",
                dataType: 'json'
            });
        }

        //return this and init when ready
        return {
            init: init
        };
    })();
    $(function () {
        Kaikmedia.Pages.import.init();
    });
}
)(jQuery);
