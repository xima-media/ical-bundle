jQuery(document).ready(function() {

    //if 'isAllDayEvent' is checked, hide datetime picker and show date picker, otherwise hide the datepicker
    jQuery('[id*="_isAllDayEvent"]').each(function(event){
        var row = jQuery(this).attr('id').split('_')[2];
        if( jQuery(this).prop('checked')){
            selectAllDayOption(row);
        }
    });
});

/**
 * Apply recurrence fields behaviour to all event rows.
 */
function addRecurrenceRuleBehaviour() {
    // toggle visibility of recurrence fields
    var recurrenceRuleSelectElements = jQuery("select[id*='recurrenceRule_freq']");

    recurrenceRuleSelectElements.change(function() {
        toggleRecurrenceRuleFields(jQuery(this));
    });
    recurrenceRuleSelectElements.each(function() {
        toggleRecurrenceRuleFields(jQuery(this));
    });

}

/**
 * Show recurrence rule fields if a frequency is selected.
 *
 * @param selectElement
 */
function toggleRecurrenceRuleFields(selectElement) {

    var followingSiblings = selectElement.parent().parent().nextAll();

    if (selectElement.val()) {
        followingSiblings.show();
    } else {
        followingSiblings.hide();
    }
}

/**
 * Apply all day behaviour to all event rows.
 */
function addAllDayBehaviour() {
    jQuery('[id*="_isAllDayEvent"]').on('ifChecked', function(event){
        var row = jQuery(this).attr('id').split('_')[2];
        selectAllDayOption(row);
    });
    jQuery('[id*="_isAllDayEvent"]').on('ifUnchecked', function(e, aux){
        var row = jQuery(this).attr('id').split('_')[2];
        deselectAllDayOption(row);
    });
}

/*
 * Change date format to date only.
 *
 * @param row The event's table row.
 */
function selectAllDayOption(row) {
    var $format = 'DD.MM.YYYY';

    var $inputStart = jQuery("input[id*='_events_" + row + "_dtStart']");
    var $inputEnd = jQuery("input[id*='_events_" + row + "_dtEnd']");

    jQuery.each([$inputStart, $inputEnd], function(index, $input) {
        var $dp = $input.parent().data("DateTimePicker");
        var $date = $dp.getDate();

        $dp.destroy();
        $input.data('dateFormat', $format);
        $input.attr('data-date-format', $format);

        $dp = $input.parent().datetimepicker({
            format: $format,
            pickTime: false
        });
        $dp = $input.parent().data("DateTimePicker");
        $dp.setDate($date);
    });
}

/*
 * Change date format to date and time.
 *
 * @param row The event's table row.
 */
function deselectAllDayOption(row) {
    var $format = 'DD.MM.YYYY, HH:mm';

    var $inputStart = jQuery("input[id*='_events_" + row + "_dtStart']");
    var $inputEnd = jQuery("input[id*='_events_" + row + "_dtEnd']");

    jQuery.each([$inputStart, $inputEnd], function(index, $input) {
        var $dp = $input.parent().data("DateTimePicker");
        var $date = $dp.getDate();

        $dp.destroy();
        $input.data('dateFormat', $format);
        $input.attr('data-date-format', $format);

        $dp = $input.parent().datetimepicker({
            format: $format,
            pickTime: false
        });
        $dp = $input.parent().data("DateTimePicker");
        $dp.setDate($date);
    });
}
