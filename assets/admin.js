jQuery(document).ready(function ($) {

    // show gateway list
    $('#sbwcfb_show_gateways').click(function (e) {
        e.preventDefault();
        $('div#sbwcfb_slug_list').slideToggle();
    });

    // save settings
    $('button#sbwcfb_save_settings').click(function (e) {
        e.preventDefault();

        var addresses, gids, ajax_url;
        addresses = $('textarea#bl-addys').val();
        gids = $('textarea#bl-gways').val();
        ajax_url = $(this).data('aju');

        if (!addresses || !gids) {
            alert('Please provide all relevant info.');
        } else {
            var data = {
                'action': 'sbwcfb_save_settings',
                'addresses': addresses,
                'gids': gids
            };
            $.post(ajax_url, data, function (response) {
                alert(response);
                location.reload();
            });
        }

    });

    // display current settings
    var curr_addies = $('#bl-addys').data('current');
    curr_addies = curr_addies.replace(/,/g, '\n');
    $('#bl-addys').val(curr_addies);

    var curr_gids = $('#bl-gways').data('current');
    curr_gids = curr_gids.replace(/,/g, '\n');
    $('#bl-gways').val(curr_gids);


});