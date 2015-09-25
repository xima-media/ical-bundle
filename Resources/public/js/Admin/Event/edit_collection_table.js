jQuery(document).ready(function() {

    //if 'isAllDayEvent' is checked, hide datetime picker and show date picker, otherwise hide the datepicker
    jQuery('[id*="_isAllDayEvent"]').each(function(event){
        var row = jQuery(this).attr('id').split('_')[2];
        if( jQuery(this).prop('checked')){
            selectAllDayOption(row);
        }else{
            deselectAllDayOption(row);
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
 * Hide datetime pickers, show date pickers.
 *
 * @param row The event's table row.
 */
function selectAllDayOption(row) {
    hideColumnOfObject(jQuery("input[id*='_events_" + row + "_dtStart']"));
    hideColumnOfObject(jQuery("input[id*='_events_" + row + "_dtEnd']"));
    showColumnOfObject(jQuery("input[id*='_events_" + row + "_allDayStart']"));
    showColumnOfObject(jQuery("input[id*='_events_" + row + "_allDayEnd']"));
}

/*
 * Show datetime pickers, hide date pickers.
 *
 * @param row The event's table row.
 */
function deselectAllDayOption(row) {
    showColumnOfObject(jQuery("input[id*='_events_" + row + "_dtStart']"));
    showColumnOfObject(jQuery("input[id*='_events_" + row + "_dtEnd']"));
    hideColumnOfObject(jQuery("input[id*='_events_" + row + "_allDayStart']"));
    hideColumnOfObject(jQuery("input[id*='_events_" + row + "_allDayEnd']"));
}

/**
 * Show the column of the table that includes the given object.
 *
 * @param object
 */
function showColumnOfObject(object) {
    var index = object.closest('td').index();
    var table = object.closest('table');
    table.filter('th:nth-child('+index+')').show();
    table.filter('td:nth-child('+index+')').show();
}

/**
 * Hide the column of the table that includes the given object.
 *
 * @param object
 */
function hideColumnOfObject(object) {
    var index = object.closest('td').index();
    var table = object.closest('table');
    table.filter('th:nth-child('+index+')').hide();
    table.filter('td:nth-child('+index+')').hide();
}