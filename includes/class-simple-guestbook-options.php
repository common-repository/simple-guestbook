<?php
class Simple_Guestbook_Options {
	/**
	 * Handle plugin options.
	 *
	 * @since    1.0.0
	**/

	public static function get_plugin_option($key) {
		$plugin_options = self::get_plugin_options();
        return false === $plugin_options ? false : esc_attr($plugin_options[$key]);
	}

    public static function get_plugin_options() {
		return get_option(SIMPLE_GUESTBOOK_OPTION_NAME);
	}

    public static function set_plugin_option($key, $value) {
		if (false === self::get_plugin_option($key)) {
            add_option(SIMPLE_GUESTBOOK_OPTION_NAME, array($key => $value));
        } else {
            update_option(SIMPLE_GUESTBOOK_OPTION_NAME, array($key => $value));
        }
	}

    public static function set_plugin_options($options) {
		if (false === self::get_plugin_options()) {
            add_option(SIMPLE_GUESTBOOK_OPTION_NAME, $options);
        }
        else {
            update_option(SIMPLE_GUESTBOOK_OPTION_NAME, $options);
        }
	}

    public static function delete_plugin_options() {
		delete_option(SIMPLE_GUESTBOOK_OPTION_NAME);
	}

    public static function get_default_options() {
        return array(
            'sortorder' => 'DESC',
			'perpage' => '10',
			'avatarsize' => '40',
			'avataroption' => '0',
			'avatarurl' => '',
			'validation' => 'guestbook',
            'replyoption' => '0'
        );
    }

    public static function set_default_options() {
        $default_options = self::get_default_options();
        // Check if the options don't exist before adding them
        if (false === self::get_plugin_options()) {
            add_option(SIMPLE_GUESTBOOK_OPTION_NAME, $default_options);
        }
    }
}