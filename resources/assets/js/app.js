
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

window.Laravel = { csrfToken: $('meta[name=csrf-token]').attr("content") };

require('./bootstrap');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

// Vue.component('example', require('./components/Example.vue'));

// const app = new Vue({
//     el: '#app'
// });

// Source: https://www.snip2code.com/Snippet/534282/Laravel-5---Use-anchor-links-for-DELETE-
// Modified to use data-method attribute
var anchorMethod = {
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

$(document).ready(function() {
    $('[data-toggle="tooltip"]').tooltip();

    // Enable anchor method to create PUT DELETE anchor tags
    anchorMethod.init();
});