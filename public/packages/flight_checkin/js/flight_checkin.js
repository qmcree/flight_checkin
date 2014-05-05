jQuery(document).ready(function() {
    jQuery('input.datetime').datetimepicker({
        format: 'yyyy-mm-dd hh:ii:ss',
        autoclose: true,
        minuteStep: 15
    });
});