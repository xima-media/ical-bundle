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
    addEventDeleteListener();

    jQuery( document ).ajaxComplete(function() {
        addEventDeleteListener();
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
            jQuery("div[id*='" + key + "_recurrenceRule_byDay']").appendTo("#collapseRecurrenceRule_" + key).children().andSelf().not('.select2-display-none, script').css('display', 'block');
        }
    });
    advancedOptionsHint();
}

/*
 * Add hint for hidden advanced options
 */
function advancedOptionsHint() {
    jQuery("div[id*='events'] tbody.sonata-ba-tbody tr").each(function (row) {
        var interval = jQuery("input[id*='" + row + "_recurrenceRule_interval']").val();
        var byMonth = jQuery("div.select2-container[id*='" + row + "_recurrenceRule_byMonth'] .select2-search-choice").length;
        var byWeekNo = jQuery("div.select2-container[id*='" + row + "_recurrenceRule_byWeekNo'] .select2-search-choice").length;
        var byYearDay = jQuery("div.select2-container[id*='" + row + "_recurrenceRule_byYearDay'] .select2-search-choice").length;
        var byMonthDay = jQuery("div.select2-container[id*='" + row + "_recurrenceRule_byMonthDay'] .select2-search-choice").length;
        var byDays = jQuery("div[id*='" + row + "_recurrenceRule_'][id*=_delete]").length;
        var count = 0;
        count += interval ? 1 : 0;
        count += byMonth ? byMonth : 0;
        count += byWeekNo ? byWeekNo : 0;
        count += byYearDay ? byYearDay : 0;
        count += byMonthDay ? byMonthDay : 0;
        count += byDays ? byDays : 0;

        var heading = jQuery("a[href='#collapseRecurrenceRule_" + row + "']");
        if (count) {
            heading.html(heading.html() + " (" + count + ")");
        }
    });
}

/*
 * Add event listener for deletion of events and nthOccurrence
 */
function addEventDeleteListener() {
    jQuery('[id*="_delete"][id*="_events"]:not([id*="recurrenceRule"])').on('ifChecked', function (event) {
        if (jQuery('[id*="_events"][id*="sonata-ba-field-container"] tbody tr').length > 1) {
            jQuery(this).parent().parent().parent().parent().parent().remove();
        }
    });
    jQuery('[id*="_delete"][id*="_events"][id*="recurrenceRule"]').on('ifChecked', function (event) {
        jQuery(this).parent().parent().parent().parent().parent().next().remove();
        jQuery(this).parent().parent().parent().parent().parent().next().remove();
        jQuery(this).parent().parent().parent().parent().parent().next().remove();
        jQuery(this).parent().parent().parent().parent().parent().next().remove();
        jQuery(this).parent().parent().parent().parent().parent().remove();
    });
}