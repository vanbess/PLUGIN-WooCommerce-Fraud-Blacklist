<?php

/***********************************************************************/
/**ADMIN PAGE FOR ENTERING BLACKLISTED ADDRESSES AND PAYMENT GATEWAYS**/
/**********************************************************************/
function sbwcfb_admin_page()
{
    add_menu_page(
        __('Fraud Blacklist Settings', 'textdomain'),
        'Fraud Blacklist',
        'manage_options',
        'sbwcfb-settings',
        'sbwcfb_settings_render',
        'dashicons-shield',
        25
    );
}
add_action('admin_menu', 'sbwcfb_admin_page');

// callback/display function
function sbwcfb_settings_render()
{ ?>
    <div id="sbwcfb_settings_render">
        <h3>SBWC Fraud Blacklist Settings</h3>

        <div id="sbwcfb_settings_inner">

            <span class="sbwcfb_help"><?php _e('Use the inputs below to update your blacklist settings as and when needed.'); ?></span>

            <!-- blacklisted addresses -->
            <label for="bl-addys"><?php _e('Add blacklisted addresses here, one per line*'); ?></label>
            <textarea name="bl-addys" id="bl-addys" cols="30" rows="10" data-current="<?php echo implode(',', maybe_unserialize(get_option('sbwcfb_addresses'))); ?>">
            </textarea>

            <!-- blacklisted gateways -->
            <label for="bl-gways"><?php _e('Add IDs for gateways which should be disabled when these addresses are found below, one per line*'); ?></label>
            <textarea name="bl-gways" id="bl-gways" cols="30" rows="10" data-current="<?php echo implode(',', maybe_unserialize(get_option('sbwcfb_gids'))); ?>">
            </textarea>

            <!-- show currently available gateways -->
            <span class="sbwcfb_help">
                <a id="sbwcfb_show_gateways" href="javascript:void(0);"><?php _e('View list of IDs for currently installed gateways'); ?></a>
            </span>

            <div id="sbwcfb_slug_list" style="display: none;">
                <div id="sbwcfb_slug_list_table_cont" style="width: 100%;">
                    <table id="sbwcfb_slug_list_table">
                        <thead>
                            <tr>
                                <th>Gateway Name</th>
                                <th>ID</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php
                            // get and display current WC payment gateway name:slug list
                            $gateways = new WC_Payment_Gateways;
                            $available = $gateways->get_available_payment_gateways();

                            foreach ($available as $data) :
                            ?>
                                <tr>
                                    <td><?php echo $data->title; ?></td>
                                    <td><?php echo $data->id; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- save -->
            <button id="sbwcfb_save_settings" data-aju="<?php echo admin_url('admin-ajax.php'); ?>" class="button button-primary"><?php _e('Save Settings'); ?></button>

        </div>

    </div>
<?php }

/**************************************/
/**AJAX TO PROCESS SAVING OF SETTINGS**/
/**************************************/
add_action('wp_ajax_sbwcfb_save_settings', 'sbwcfb_save_settings');
add_action('wp_ajax_nopriv_sbwcfb_save_settings', 'sbwcfb_save_settings');
function sbwcfb_save_settings()
{
    if ($_POST['action'] == 'sbwcfb_save_settings') :

        // get submitted data and parse
        $addresses = preg_split("/\r\n|\n|\r/", $_POST['addresses']);
        $gids = preg_split("/\r\n|\n|\r/", $_POST['gids']);

        // save data
        if (!empty($addresses)) :
            $addresses_saved = update_option('sbwcfb_addresses', maybe_serialize($addresses));
        endif;

        if (!empty($gids)) :
            $gids_saved = update_option('sbwcfb_gids', maybe_serialize($gids));
        endif;

        if ($addresses_saved || $gids_saved) :
            _e('All settings were saved successfully.');
        else :
            _e('Settings were not saved. If you think this is an error please reload the page and try again.');
        endif;

    endif;
    wp_die();
}

/*************************************************/
/**FILTER AVAILABLE PAYMENT GATEWAYS AS REQUIRED**/
/*************************************************/
add_filter('woocommerce_available_payment_gateways', 'sbwcfb_filter_gateways');

function sbwcfb_filter_gateways($available_gateways)
{
    // return if is admin or is not checkout
    if (is_admin() || !is_checkout()) :
        return $available_gateways;
    endif;

    // get current user address
    $curr_user_address = wc()->customer->get_billing_address_1() . ' ' . wc()->customer->get_billing_address_2();

    // get blacklisted addresses
    $bl_addies = maybe_unserialize(get_option('sbwcfb_addresses'));

    // get payment gateways which are to be disabled
    $bl_gateways = maybe_unserialize(get_option('sbwcfb_gids'));

    // unset payment gateways if banned addresses are present and gateways ids are present and current user address if found in banned address list
    if (!empty($bl_addies) && !empty($bl_gateways) && in_array($curr_user_address, $bl_addies)) :
        foreach ($bl_gateways as $gateway) :
            unset($available_gateways[$gateway]);
        endforeach;
    endif;

    // return revised gateways
    return $available_gateways;
}

?>