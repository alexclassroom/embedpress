<?php
/*
 * It will be customzed for OpenSea
 *  All undefined vars comes from 'render_settings_page' method
 *  */

$personal_token_url = 'https://www.instagram.com/oauth/authorize?app_id=1021573018834002&redirect_uri=https://api.embedpress.com/instagram.php&response_type=code&scope=user_profile,user_media&state=' . admin_url('admin.php');

$business_token_url = 'https://www.facebook.com/dialog/oauth?client_id=928673724899905&redirect_uri=https://api.embedpress.com/facebook.php&response_type=code&scope=pages_show_list,instagram_basic,instagram_manage_comments,instagram_manage_insights,pages_read_engagement&state=' . admin_url('admin.php') . '&user_id=5511';

$personal_data = !empty(get_option('instagram_account_data')) ? get_option('instagram_account_data') : [];

$business_data = !empty(get_option('instagram_account_data')) ? get_option('instagram_account_data') : [];;

$get_data = $personal_data;

$is_connected = false;


?>

<div class="embedpress__settings background__white radius-25 p40">
    <h3><?php esc_html_e("Instagram Settings", "embedpress"); ?></h3>
    <div class="embedpress__settings__form">
        <div class="account-section">
            <div class="account-wrap full-width-layout">
                <p>
                    <a href="<?php echo esc_url($personal_token_url); ?>" class="account-button personal-account" target="_self" title="Add Personal Account">Connect with Instagram</a>


                    <span class="premium-field">
                        <span class="description hidden">
                            <small>Multiple user accounts and business accounts are premium features.</small>
                        </span>
                    </span>
                </p>
                <table class="emebedpress">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Username</th>
                            <th>Account</th>
                            <th>Access Token</th>
                            <th>Type</th>
                            <th>Connect</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php if (is_array($get_data) && count($get_data) > 0) : ?>
                            <?php
                                $avater_url = 'http://2.gravatar.com/avatar/b642b4217b34b1e8d3bd915fc65c4452?s=150&d=mm&r=g';
                                if (!empty($get_data['account_type']) && (strtolower($get_data['account_type'])  === 'business')) {
                                    $avater_url = '';
                                }
                                ?>
                            <?php foreach ($get_data as $data) : ?>
                                <tr data-userid="<?php echo esc_attr($data['user_id']); ?>" data-accounttype="<?php echo esc_attr($data['account_type']); ?>">
                                    <td>
                                        <div class="user-image"><img class="user-avatar" src="<?php echo esc_url($avater_url); ?>"></div>
                                    </td>
                                    <td><?php echo esc_html($data['username']) ?></td>
                                    <td><?php echo esc_html($data['user_id']) ?></td>
                                    <?php
                                            if (!empty($data['user_id'])) {
                                                $is_connected = true;
                                            }
                                            ?>
                                    <td style="width: 300px;">
                                        <input type="text" readonly="" value="<?php echo esc_attr($data['access_token']); ?>" maxlength="20" pattern="">
                                        <span>...</span>
                                        <!-- <button class="button button-primary"><i class="dashicons dashicons-admin-page"></i></button> -->
                                    </td>
                                    <td style="text-transform: uppercase;"><?php echo esc_attr($data['account_type']); ?></td>

                                    <td>
                                        <div class="user-image">
                                            <?php
                                                    if (strtolower($data['account_type']) !== 'business') {
                                                        if (!empty($is_connected)) {
                                                            echo '<a href="' . esc_url('https://www.facebook.com/dialog/oauth?client_id=1280977446114033&redirect_uri=https://api.embedpress.com/facebook.php&response_type=code&scope=pages_show_list,instagram_basic,instagram_manage_comments,instagram_manage_insights,pages_read_engagement&state=' . admin_url('admin.php') . '?user_id=' . $data['user_id'] . '') . ',username=' . $data['username'] . '" target="_self" title="Add Business Account"><i class="dashicons dashicons-plus"></i></a>';
                                                        }
                                                    } else {
                                                        echo '<i class="dashicons dashicons-saved"></i>';
                                                    }

                                                    ?>
                                        </div>
                                    </td>

                                    <td>
                                        <button class="button button-secondary account-delete-button"><i class="dashicons dashicons-trash"></i></button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>

                        <?php endif; ?>


                    </tbody>

                </table>
            </div>
        </div>

    </div>
</div>