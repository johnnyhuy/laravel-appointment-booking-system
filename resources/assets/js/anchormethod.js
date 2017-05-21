// Source: https://www.snip2code.com/Snippet/534282/Laravel-5---Use-anchor-links-for-DELETE-
// Modified to use data-method attribute
anchorMethod = {
    init: function () {
        $('a[data-method]').on('click', function (e) {
            e.preventDefault();
            var link = $(this);
            var httpMethod = link.data('method').toUpperCase();
            var form;

            if ($.inArray(httpMethod, ['PUT', 'DELETE']) === -1) {
                return;
            }

            anchorMethod.submit(link);
        });
    },

    submit: function (link) {
        var form =
            $('<form>', {
                'method': 'POST',
                'action': link.attr('href')
            });

        var token =
            $('<input>', {
                'type':  'hidden',
                'name':  '_token',
                'value': $('meta[name="csrf-token"]').attr('content')
            });

        var hiddenInput =
            $('<input>', {
                'name':  '_method',
                'type':  'hidden',
                'value': link.data('method')
            });

        form.append(token, hiddenInput)
            .appendTo('body').submit();
    }
};