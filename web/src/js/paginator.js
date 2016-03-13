(function ( $ ) {

    $.templates('pager', '#tmpl-pager');

    $.fn.paginate = function(action) {

        return this.each(function() {
            var $this = $(this);

            if (typeof action === 'object') {
                var settings = $.extend({
                    page: 1,
                    limit: 50
                }, action );

                $this.data('uri', 'http://' + api.host + '/app_dev.php' + settings.uri);
                $this.data('page', settings.page);
                $this.data('limit', settings.limit);
                $this.data('tmpl', $.templates('#' + $this.find('script').attr('id')));

                $this.paginate('update');
            }

            if (action === 'update') {
                $.ajax({
                    method: 'GET',
                    url: $this.data('uri'),
                    data: {
                        p: $this.data('page'),
                        l: $this.data('limit')
                    },
                    headers: {
                        'Authorization' : 'Bearer ' + api.token
                    },
                    success: function(data) {
                        $this.html($this.data('tmpl').render(data));
                        $this.append($.templates.pager(data));

                        $this.find('[data-goto-page]').click(function() {
                            var $btn = $(this);
                            $this.data('page', $btn.data('goto-page'));
                            $this.paginate('update');
                        });
                    },
                    error: function(a) {
                        var data = JSON.parse(a.responseText);
                        console.log(data);
                    }
                });
            }
        });
    };

}( jQuery ));

$(function() {
    $('[data-paginate]').each(function() {
        var $this = $(this);
        $this.paginate({
            uri: $this.data('paginate'),
            page: $this.data('page') || 1,
            limit: $this.data('limit') || 50
        });
    });
});