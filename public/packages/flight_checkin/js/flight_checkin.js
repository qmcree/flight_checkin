jQuery(document).ready(function() {
    jQuery.backstretch('/packages/flight_checkin/images/bg-airplane.jpg');

    jQuery('input.datetime').datetimepicker({
        format: 'yyyy-mm-dd hh:ii:ss',
        autoclose: true,
        minuteStep: 15
    });

    jQuery('a.delete').click(function(e) {
        e.preventDefault();

        var link = jQuery(this);

        if (window.confirm('Are you sure you want to delete this ' + link.data('type') + '? This is irreversible.')) {
            window.location = link.prop('href');
        }
    });
});