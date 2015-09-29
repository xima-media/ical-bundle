jQuery(document).ready(function() {

    //if 'isAllDayEvent' is checked, hide datetime picker and show date picker, otherwise hide the datepicker
    jQuery('[id*="_noTime"]').each(function(event){
        var row = jQuery(this).attr('id').split('_')[2];
        if( jQuery(this).prop('checked')) {
            selectAllDayOption(row);
        }
    });
    addAllDayBehaviour();
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
    jQuery('[id*="_noTime"]').on('ifChecked', function(event){
        var row = jQuery(this).attr('id').split('_')[2];
        selectAllDayOption(row);
    });
    jQuery('[id*="_noTime"]').on('ifUnchecked', function(e, aux){
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
    var $time = '00:00';

    var $inputStart = jQuery("input[id*='_events_" + row + "_timeFrom']");
    var $inputEnd = jQuery("input[id*='_events_" + row + "_timeTo']");

    jQuery.each([$inputStart, $inputEnd], function(index, $input) {
        var $dp = $input.parent().data("DateTimePicker");

        $input.val($time);
        $dp.disable();
    });
}

/*
 * Change date format to date and time.
 *
 * @param row The event's table row.
 */
function deselectAllDayOption(row) {
    var $inputStart = jQuery("input[id*='_events_" + row + "_timeFrom']");
    var $inputEnd = jQuery("input[id*='_events_" + row + "_timeTo']");

    jQuery.each([$inputStart, $inputEnd], function(index, $input) {
        var $dp = $input.parent().data("DateTimePicker");

        $dp.enable();
    });
}
