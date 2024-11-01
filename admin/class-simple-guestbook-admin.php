<?php

class Simple_Guestbook_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version, $option_name ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->option_name = $option_name;
	}

	/**
	 * Make sure that the Media Uploader API is there
	 *
	 * @since  1.0.0
	 */
	public function enqueue_media_scripts() {
		wp_enqueue_media();
	}

	/**
	 * Add link to plugin setting page on plugins page.
	 *
	 * @since  1.0.0
	 */
	public function add_plugin_settings_link( $links ): array {
		$settings_link = '<a href="options-general.php?page=simple-guestbook">' . esc_html__('Settings') . '</a>';
        array_unshift($links, $settings_link);
        return $links;
	}

	/**
	 * Add an options page under the Settings submenu
	 *
	 * @since  1.0.0
	 */
	public function add_options_page() {
	
		$this->plugin_screen_hook_suffix = add_options_page(
			esc_html__( 'Simple Guestbook', 'simple-guestbook' ),
			esc_html__( 'Simple Guestbook', 'simple-guestbook' ),
			'manage_options',
			$this->plugin_name,
			array( $this, 'display_options_page' )
		);
	
	}

	/**
	 * Render the options page for plugin
	 *
	 * @since  1.0.0
	 */
	public function display_options_page() {
		include_once 'partials/simple-guestbook-admin-display.php';
	}

	/**
	 * Register all related settings of this plugin
	 *
	 * @since  1.0.0
	 */
	public function register_setting() {

		add_settings_section(
			$this->option_name . '_general',
			'',
			array( $this, $this->option_name . '_general_cb' ),
			$this->plugin_name
		);

		add_settings_field(
			$this->option_name . '_sortoder',
			esc_html__( 'Sort order', 'simple-guestbook' ),
			array( $this, $this->option_name . '_sortorder_cb' ),
			$this->plugin_name,
			$this->option_name . '_general',
			array( 'label_for' => $this->option_name . '_sortorder' )
		);

		add_settings_field(
			$this->option_name . '_perpage',
			esc_html__( 'Entries per page (1-50)', 'simple-guestbook' ),
			array( $this, $this->option_name . '_perpage_cb' ),
			$this->plugin_name,
			$this->option_name . '_general',
			array( 'label_for' => $this->option_name . '_perpage' )
		);

		add_settings_field(
			$this->option_name . '_avatarsize',
			esc_html__( 'Avatar size (0-96)', 'simple-guestbook' ),
			array( $this, $this->option_name . '_avatarsize_cb' ),
			$this->plugin_name,
			$this->option_name . '_general',
			array( 'label_for' => $this->option_name . '_avatarsize' )
		);

		add_settings_field(
			$this->option_name . '_avataroption',
			esc_html__( 'Use custom avatar?', 'simple-guestbook' ),
			array( $this, $this->option_name . '_avataroption_cb' ),
			$this->plugin_name,
			$this->option_name . '_general',
			array( 'label_for' => $this->option_name . '_avataroption' )
		);

		add_settings_field(
			$this->option_name . '_avatarurl',
			esc_html__( 'Custom avatar URL', 'simple-guestbook' ),
			array( $this, $this->option_name . '_avatarurl_cb' ),
			$this->plugin_name,
			$this->option_name . '_general',
			array( 'label_for' => $this->option_name . '_avatarurl' )
		);

		add_settings_field(
			$this->option_name . '_validation',
			esc_html__( 'JS comment form validation', 'simple-guestbook' ),
			array( $this, $this->option_name . '_validation_cb' ),
			$this->plugin_name,
			$this->option_name . '_general',
			array( 'label_for' => $this->option_name . '_validation' )
		);

		add_settings_field(
			$this->option_name . '_replyoption',
			esc_html__( 'Allow reply for editors?', 'simple-guestbook' ),
			array( $this, $this->option_name . '_replyoption_cb' ),
			$this->plugin_name,
			$this->option_name . '_general',
			array( 'label_for' => $this->option_name . '_replyoption' )
		);

		register_setting( $this->plugin_name, $this->option_name, array( $this, 'sanitize_options' ) );
	}

	/**
	 * Render the text for the general section
	 *
	 * @since  1.0.0
	 */
	public function simple_guestbook_options_general_cb() {
		echo '<p><b>' . esc_html__( 'To create a guestbook page just use the shortcode', 'simple-guestbook' ) . ' <code>[simple-guestbook]</code>.</b></p>';
	}

	/**
	 * Render the combobox for the sort order for this plugin
	 *
	 * @since  1.0.0
	 */
	public function simple_guestbook_options_sortorder_cb() {
		$key = $this->option_name . '[sortorder]';
		$value = Simple_Guestbook_Options::get_plugin_option('sortorder');

		// Define the possible options for the combobox
		$possible_options = array(
			'DESC' => esc_html__('descending', 'simple-guestbook'),
			'ASC' => esc_html__('ascending', 'simple-guestbook'),
		);
	
		// Get the currently selected option (or use a default if not set)
		$selected_option = isset($value) ? $value : 'DESC';
	
		// Output the HTML for the combobox
		?>
		<select name="<?php echo esc_attr($key) ?>">
			<?php
			foreach ($possible_options as $value => $label) {
				$selected = selected($value, $selected_option, false);
				echo '<option value="' . esc_attr($value) . '" '. esc_attr($selected) . '>' . esc_html($label) . '</option>';
			}
			?>
		</select>
		<?php
	}

	/**
	 * Render the textbox for entries per page
	 *
	 * @since  1.0.0
	 */
	public function simple_guestbook_options_perpage_cb() {
		$key = $this->option_name . '[perpage]';
		$value = Simple_Guestbook_Options::get_plugin_option('perpage');
		echo '<input type="text" name="'. esc_attr($key) . '" id="'. esc_attr($key) . '" value="' . esc_attr($value) . '" size="1"/>';
	}

	/**
	 * Render the textbox for avatar size
	 *
	 * @since  1.0.0
	 */
	public function simple_guestbook_options_avatarsize_cb() {
		$key = $this->option_name . '[avatarsize]';
		$value = Simple_Guestbook_Options::get_plugin_option('avatarsize');
		echo '<input type="text" name="'. esc_attr($key) . '" id="'. esc_attr($key) . '" value="' . esc_attr($value) . '" size="1"/>';
	}

	/**
	 * Render the checkbox for avatar option 
	 *
	 * @since  1.0.0
	 */
	public function simple_guestbook_options_avataroption_cb() {
		$key = $this->option_name . '[avataroption]';
		$value = Simple_Guestbook_Options::get_plugin_option('avataroption');
		$avatarsize = Simple_Guestbook_Options::get_plugin_option('avatarsize');
		$avatarurl = Simple_Guestbook_Options::get_plugin_option('avatarurl');
		// Define the checkbox option value
		$checkbox_option = isset($value) ? $value : '0';
		
		// Output the HTML for the checkbox
		// https://stackoverflow.com/questions/1809494/post-unchecked-html-checkboxes/1992745#1992745
		?>
		<div style="display: flex; align-items: center;">
			<input type="hidden" name="<?php echo esc_attr($key) ?>" value="0" />
			<input type="checkbox" id="<?php echo esc_attr($key) ?>" name="<?php echo esc_attr($key) ?>" value="1" <?php checked($checkbox_option, '1'); ?> />
			<?php
			if ($value && $avatarurl && $avatarsize) {
				echo '<img src="' . esc_attr($avatarurl) . '" width="' . esc_attr($avatarsize) . '" height="' . esc_attr($avatarsize) . '" style="border-radius: ' . esc_attr($avatarsize) . 'px;" />';
			}
			?>
		</div>
		<?php
	}

	/**
	 * Render controls for custom avatar
	 *
	 * @since  1.0.0
	 */
	public function simple_guestbook_options_avatarurl_cb() {
		$key = $this->option_name . '[avatarurl]';
		$value = Simple_Guestbook_Options::get_plugin_option('avatarurl');
		// Output the HTML for the image picker
		?>
		<div class="simple-guestbook-avatar-container">
			<input type="text" id="simple-guestbook-avatar-image-url" name="<?php echo esc_attr($key); ?>" value="<?php echo esc_attr($value); ?>" size="45" placeholder="<?php esc_html_e('Image from your media library', 'simple-guestbook') ?>" readonly/>
			<button class="button" id="simple-guestbook-select-image-button">Select Image</button>
		</div>

		<script>
			var el = document.getElementById('simple-guestbook-avatar-image-url');
			el.scrollLeft = el.scrollWidth;

			document.addEventListener('DOMContentLoaded', function () {
				var uploadButton = document.getElementById('simple-guestbook-select-image-button');
				var imagePathInput = document.getElementById('simple-guestbook-avatar-image-url');

				uploadButton.addEventListener('click', function (event) {
					event.preventDefault();

					// Create a media frame
					var mediaFrame = wp.media({
						title: 'Choose custom avatar',
						button: {
							text: 'Choose Image'
						},
						library: {
							type: 'image'
						},
						multiple: false
					});

					// When an image is selected, run a callback
					mediaFrame.on('select', function () {
						var attachment = mediaFrame.state().get('selection').first().toJSON();
						imagePathInput.value = attachment.url;
						imagePathInput.scrollLeft = imagePathInput.scrollWidth;
					});

					// Open the media uploader
					mediaFrame.open();
				});
			});
		</script>

		<?php
	}

	/**
	 * Render the radio input field for validation option
	 *
	 * @since  1.0.0
	 */
	public function simple_guestbook_options_validation_cb() {
		$key = $this->option_name . '[validation]';
		$value = Simple_Guestbook_Options::get_plugin_option('validation');
		?>
			<fieldset>
				<label>
					<input type="radio" name="<?php echo esc_attr($key) ?>" id="<?php echo esc_attr($key) ?>" value="disabled" <?php checked( $value, 'disabled' ); ?>>
					<?php esc_html_e( 'Disabled', 'simple-guestbook' ); ?>
				</label>
				<br />
				<label>
					<input type="radio" name="<?php echo esc_attr($key) ?>" value="guestbook" <?php checked( $value, 'guestbook' ); ?>>
					<?php esc_html_e( 'Enabled for guestbooks', 'simple-guestbook' ); ?>
				</label>
				<br />
				<label>
					<input type="radio" name="<?php echo esc_attr($key) ?>" value="blog" <?php checked( $value, 'blog' ); ?>>
					<?php esc_html_e( 'Enabled for the entire site', 'simple-guestbook' ); ?>
				</label>
			</fieldset>
		<?php
	}

	/**
	 * Render the checkbox for avatar option 
	 *
	 * @since  1.0.0
	 */
	public function simple_guestbook_options_replyoption_cb() {
		$key = $this->option_name . '[replyoption]';
		$value = Simple_Guestbook_Options::get_plugin_option('replyoption');
		// Define the checkbox option value
		$checkbox_option = isset($value) ? $value : '0';
		
		// Output the HTML for the checkbox
		// https://stackoverflow.com/questions/1809494/post-unchecked-html-checkboxes/1992745#1992745
		?>
		<input type="hidden" name="<?php echo esc_attr($key) ?>" value="0" />
		<input type="checkbox" id="<?php echo esc_attr($key) ?>" name="<?php echo esc_attr($key) ?>" value="1" <?php checked($checkbox_option, '1'); ?> />
		<?php
	}

	/**
	 * Sanitize and validate options
	 *
	 * @param  string $input $_POST value
	 * @since  1.0.0
	 * @return string           Sanitized array
	 */
	public function sanitize_options($input) {
		// Get current options
		$current_options = Simple_Guestbook_Options::get_plugin_options();

		// Sanitize the input
		$sanitized_input = array();
        foreach ($input as $key => $value) {
            $sanitized_input[$key] = sanitize_text_field($value);
        }

		// Check if 'sortorder' has a specific value
        if (!in_array($sanitized_input['sortorder'], array( 'DESC', 'ASC' ))) {
            // Add an error message
            add_settings_error(
                $this->option_name,
                'invalid_sortorder',
                esc_html__('Sort order property out of range.', 'simple-guestbook'),
                'error'
            );
			$sanitized_input['sortorder'] = $current_options['sortorder'];
        }

		// Check if 'perpage' is between 1 and 50
		if ((!is_numeric($sanitized_input['perpage'])) || ($sanitized_input['perpage'] < 1) || ($sanitized_input['perpage'] > 50)) {
			// Add an error message
            add_settings_error(
                $this->option_name,
                'invalid_perpage',
                esc_html__('Per page value should be between 1 and 50.', 'simple-guestbook') . ' ' . esc_html__('You entered:', 'simple-guestbook'). ' ' . $sanitized_input['perpage'],
                'error'
            );
            $sanitized_input['perpage'] = $current_options['perpage'];
		}

		// Check if 'avatarsize' is between 0 and 96
		if ((!is_numeric($sanitized_input['avatarsize'])) || ($sanitized_input['avatarsize'] < 0) || ($sanitized_input['avatarsize'] > 96)) {
			// Add an error message
            add_settings_error(
                $this->option_name,
                'invalid_perpage',
                esc_html__('Avatar size value should be between 0 and 96.', 'simple-guestbook') . ' ' . esc_html__('You entered:', 'simple-guestbook'). ' ' . $sanitized_input['avatarsize'],
                'error'
            );
            $sanitized_input['avatarsize'] = $current_options['avatarsize'];
		}

		// Check if 'avatarurl' is set
		if ($sanitized_input['avataroption'] == '1' && $sanitized_input['avatarurl'] == '') {
			// Add an error message
            add_settings_error(
                $this->option_name,
                'invalid_perpage',
                esc_html__('Please choose a custom avatar from your media library as well.', 'simple-guestbook'),
                'error'
            );
            $sanitized_input['avataroption'] = $current_options['avataroption'];
		}

        // Check if 'validation' has a specific value
        if (!in_array($sanitized_input['validation'], array( 'disabled', 'guestbook', 'blog' ))) {
            // Add an error message
            add_settings_error(
                $this->option_name,
                'invalid_validation',
                esc_html__('Validation property out of range.', 'simple-guestbook') . ' ' . esc_html__('You entered:', 'simple-guestbook'). ' ' . $sanitized_input['validation'],
                'error'
            );
            $sanitized_input['validation'] = $current_options['validation'];
        }

        // Return the sanitized input for saving to the database
        return $sanitized_input;
    }
}
