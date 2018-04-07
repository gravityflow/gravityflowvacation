(function (GravityFlowVacation, $) {
    var strings = gravityflowvacation_vacation_strings;

    GravityFlowVacation.ConfirmDeleteVacationForm = function (form_id) {
        if (confirm(strings.message)) {
            DeleteForm(form_id);
        }
    };

}(window.GravityFlowVacation = window.GravityFlowVacation || {}, jQuery));
