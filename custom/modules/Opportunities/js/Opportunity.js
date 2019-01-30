$(document).ready(function(){
    $('#convert_opp_button').click(function(){
        var id = $('input[name="record"').val(),
            url = 'index.php?module=Opportunities&action=convert_opp&record=' + id + '&to_pdf=1';
        $.ajax(url).done(function() {
            window.location = "index.php?module=Accounts&action=EditView&return_module=Opportunities&return_action=DetailView&return_id=" + id + "&opp_id="+ id;
        }).fail(function() {
            console.log("Not able to convert opportunity")
        }).always(function() {});
    });
});
