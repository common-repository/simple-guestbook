<?php
if ( ! defined( 'WPINC' ) ) die;

/**
 * Provide an admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @since      1.0.0
 *
 * @package    Simple_Guestbook
 * @subpackage Simple_Guestbook/admin/partials
 */
?>

<div class="wrap">
    <div style="float:right;">
        <a href="https://ko-fi.com/dichternebel" target="_blank">
            <img src="<?php echo esc_url( plugins_url( 'assets/kofi_button_stroke.png', __DIR__ ) ) ?>" alt="Support me on Ko-fi" width="200" />
        </a>
    </div>
    <h1><?php esc_html_e( 'Settings' ); ?> > <?php echo esc_html( get_admin_page_title() ); ?></h1>
    <form action="options.php" method="post">
        <?php
            settings_fields( $this->plugin_name );
            do_settings_sections( $this->plugin_name );
            submit_button();
        ?>
    </form>
</div>