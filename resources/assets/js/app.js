
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

window.Laravel = { csrfToken: $('meta[name=csrf-token]').attr("content") };

require('./bootstrap');
require('./maskedinput')
require('./anchormethod')


/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

// Vue.component('example', require('./components/Example.vue'));

// const app = new Vue({
//     el: '#app'
// });

$(document).ready(function() {
    $('[data-toggle="tooltip"]').tooltip();

    // Enable anchor method to create PUT DELETE anchor tags
    anchorMethod.init();

    $('input[masked-time]').mask('99:99', {placeholder: 'hh:mm'})
    $('input[masked-date]').mask('99/99/9999', {placeholder: 'dd/mm/yyyy'})
});