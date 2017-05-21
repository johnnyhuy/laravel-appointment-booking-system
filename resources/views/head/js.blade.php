<script type="text/javascript" src="{{ asset('js/app.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/improved-links.js') }}"></script>
<script type="text/javascript">
    function showRedirect($selector, $url) {
        $($selector).show();

        location = $url;
    }
</script>