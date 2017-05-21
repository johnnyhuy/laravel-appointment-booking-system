
       /**
         * Handles the action triggered by user.
         * This method recognizes the `data-method` attribute of the element. If the attribute exists,
         * the method will submit the form containing this element. If there is no containing form, a form
         * will be created and submitted using the method given by this attribute value (e.g. "post", "put").
         * For hyperlinks, the form action will take the value of the "href" attribute of the link.
         * For other elements, either the containing form action or the current page URL will be used
         * as the form action URL.
         *
         * ```php
         * {{ Html::methodLink("PUT", 'site/foobar', 'submit', ['data' => ['name1' => 'value1, 'name2' => 'value2']]) }}
         * ```
         *
         */
jQuery.fn.methodLink = function(){
      var $link = this,
          csrf = $link.data('csrf'),
          method = $link.data('method'),
          action = $link.attr('href'),
          attributes = $link.data('attributes');

      $link.on('click', function(){

            var $zelda = $('<form/>', {method: method, action: action});
                var target = $link.attr('target');
                if (target) {
                    $zelda.attr('target', target);
                }
                if (!/(get|post)/i.test(method)) {
                    $zelda.append($('<input/>', {name: '_method', value: method, type: 'hidden'}));
                    method = 'post';
                    $zelda.attr('method', method);
                }
                if (/post/i.test(method)) {
                    if (csrf) {
                        $zelda.append($('<input/>', {name: '_token', value: csrf, type: 'hidden'}));
                    }
                }
                for (var key in attributes) {
                    $zelda.append($('<input/>', {name: key, value: attributes[key], type: 'hidden'}));
                }
                $zelda.hide().appendTo('body');

                $zelda.trigger('submit');
            return false;
      });

};

$(document).ready(function() {
    $('a.method-link').methodLink();
});
