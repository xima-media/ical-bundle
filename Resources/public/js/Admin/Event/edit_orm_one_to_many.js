jQuery(document).ready(function() {

    //if 'isAllDayEvent' is checked, hide datetime picker and show date picker, otherwise hide the datepicker
    jQuery('[id*="_noTime"]').each(function(event){
        var row = jQuery(this).attr('id').split('_')[2];
        if( jQuery(this).prop('checked')) {
            selectAllDayOption(row);
        }
    });
    addAllDayBehaviour();

    // mark changes as not coming from user, so confirm message on reload is not shown
    jQuery('.sonata-ba-form form').each(function () { jQuery(this).confirmExit(); });

    jQuery( document ).ajaxComplete(function() {
        jQuery('[id*="_delete"]').on('ifChecked', function(event){
            if ($(".sonata-ba-tbody tr").length > 1) {
                jQuery(this).parent().parent().parent().remove();
            }
        });

        hideAdvancedRecurrenceRuleSettings();
    });

    hideAdvancedRecurrenceRuleSettings();
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

    var followingSiblings = selectElement.parent().parent().nextAll().children().andSelf();

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
    var $time = ['12:00 am','11:59 pm'];

    var $inputStart = jQuery("input[id*='_events_" + row + "_timeFrom']");
    var $inputEnd = jQuery("input[id*='_events_" + row + "_timeTo']");

    jQuery.each([$inputStart, $inputEnd], function(index, $input) {
        var $dp = $input.parent().data("DateTimePicker");
        $input.val($time[index]);
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

function hideAdvancedRecurrenceRuleSettings() {
    jQuery("div[id*='_events'] .sonata-ba-tbody tr").each(function(key, value) {
        if (!jQuery(this).find("#collapseRecurrenceRule_" + key).length > 0) {
            if (jQuery("div[id*='" + key + "_recurrenceRule_interval']").css('display') == 'none') {
                jQuery("div[id*='" + key + "_recurrenceRule_interval']").before('<div class="panel-group" style="display: none"> <div class="panel panel-default"> <div class="panel-heading"> <a data-toggle="collapse" href="#collapseRecurrenceRule_' + key + '">Advanced options</a> </div> <div id="collapseRecurrenceRule_' + key + '" class="panel-collapse collapse"></div> </div> </div>');
            } else {
                jQuery("div[id*='" + key + "_recurrenceRule_interval']").before('<div class="panel-group"> <div class="panel panel-default"> <div class="panel-heading"> <a data-toggle="collapse" href="#collapseRecurrenceRule_' + key + '">Advanced options</a> </div> <div id="collapseRecurrenceRule_' + key + '" class="panel-collapse collapse"></div> </div> </div>');
            }
            jQuery("div[id*='" + key + "_recurrenceRule_interval']").appendTo("#collapseRecurrenceRule_" + key).children().andSelf().not('.select2-display-none').css('display', 'block');
            jQuery("div[id*='" + key + "_recurrenceRule_byMonth']").appendTo("#collapseRecurrenceRule_" + key).children().andSelf().not('.select2-display-none').css('display', 'block');
            jQuery("div[id*='" + key + "_recurrenceRule_byWeekNo']").appendTo("#collapseRecurrenceRule_" + key).children().andSelf().not('.select2-display-none').css('display', 'block');
            jQuery("div[id*='" + key + "_recurrenceRule_byYearDay']").appendTo("#collapseRecurrenceRule_" + key).children().andSelf().not('.select2-display-none').css('display', 'block');
            jQuery("div[id*='" + key + "_recurrenceRule_byMonthDay']").appendTo("#collapseRecurrenceRule_" + key).children().andSelf().not('.select2-display-none').css('display', 'block');
            jQuery("div[id*='" + key + "_recurrenceRule_byDay']").appendTo("#collapseRecurrenceRule_" + key).children().andSelf().not('.select2-display-none').css('display', 'block');
        }
    });
}