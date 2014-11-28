require(['jquery', 'system!locale', 'uikit!form-select,datepicker,autocomplete,timepicker', 'domReady!'], function($, system, uikit) {

    var form = $('#js-term'), id = $('input[name="id"]', form), cancel = $('.js-cancel', form), spinner = $('.js-spinner', form);

    // form ajax saving
    form.on('submit', function(e) {

        e.preventDefault();
        e.stopImmediatePropagation();

        spinner.removeClass('uk-hidden');

        $.post(form.attr('action'), form.serialize(), function(response) {

            uikit.notify(response.message, response.error ? 'danger' : 'success');

            if (response.id) {
                id.val(response.id);
                cancel.text(cancel.data('label'));
            }

            spinner.addClass('uk-hidden');
        });
    });

});