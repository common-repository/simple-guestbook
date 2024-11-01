<?php
class Simple_Guestbook_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		require_once('class-simple-guestbook-walker-comment.php');
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->simple_guestbook_post_id = -1;  
	}

	public function register_shortcode() {
		add_shortcode( 'simple-guestbook', array( $this, 'do_nothing') );
	}

	public function do_nothing() {
	}

	public function disable_comments($template) {
		if (is_page($this->simple_guestbook_post_id)) {
			return plugin_dir_path( dirname( __FILE__ ) ) . 'index.php';
		}
		return $template;
	}

	public function disable_block_comments($pre_render, $parsed_block) {
		if (is_page($this->simple_guestbook_post_id) && $parsed_block['blockName'] == "core/comments") return "";
	}

	// Should normally be done by the theme dev but who knows?
	public function html5_theme_setup() {
		if (!is_page($this->simple_guestbook_post_id)) return;
		add_theme_support( 'html5', array( 'comment-list' ) );
	}

	public function custom_comment_validation_js() {
		// Get option
		$validation_option = Simple_Guestbook_Options::get_plugin_option('validation');

		// Guards
		if ('disabled' === $validation_option) return;
		if (!comments_open()) return;
		if (!is_page($this->simple_guestbook_post_id) && 'guestbook' === $validation_option) return;

		?>
		<script>
			if (document.getElementById('commentform')) {
				document.addEventListener('DOMContentLoaded', function() {
					document.getElementById('commentform').addEventListener('submit', function(event) {

						var isUserLoggedIn = <?php echo is_user_logged_in() ? 'true' : 'false'; ?>;
						if (!isUserLoggedIn) {
							var name = document.getElementById('author').value;
							var email = document.getElementById('email').value;
							var url = document.getElementById('url').value;
						}
						else {
							<?php $current_user = wp_get_current_user(); ?>
							var name = "<?php echo esc_html($current_user->display_name); ?>";
							var email = "<?php echo esc_html($current_user->user_email); ?>";
							var url = "<?php echo esc_url($current_user->user_url); ?>";
						}
						var comment = document.getElementById('comment').value;
						var submitBtn = document.getElementById('submit');

						if (!document.getElementById('commentform-errors'))
						{
							var submitParagraph = submitBtn.parentNode;
							submitParagraph.innerHTML += '<div id="commentform-errors"></div>';
						}
		
						let errors = 0;

						if (comment === '') {
							errors++;
							document.getElementById('comment').classList.add('error-border');
						} else {
							document.getElementById('comment').classList.remove('error-border');
						}
						if (name === '') {
							errors++;
							document.getElementById('author').classList.add('error-border');
						} else if (!isUserLoggedIn)  {
							document.getElementById('author').classList.remove('error-border');
						}
						if (email === '' || !validateEmail(email)) {
							errors++;
							document.getElementById('email').classList.add('error-border');
						} else if (!isUserLoggedIn) {
							document.getElementById('email').classList.remove('error-border');
						}
						if (url !== '' && !validateURL(url)) {
							errors++;
							document.getElementById('url').classList.add('error-border');
						} else if (!isUserLoggedIn) {
							document.getElementById('url').classList.remove('error-border');
						}
						// Check for hCaptcha response
						if (typeof hcaptcha !== 'undefined') {
							var hcaptchaResponse = hcaptcha.getResponse();
							if (hcaptchaResponse === '') {
								errors++;
								if (document.getElementsByClassName('h-captcha').length > 0) {
									document.getElementsByClassName('h-captcha')[0].classList.add('error-border');
								}
							}
						} else if (!isUserLoggedIn && document.getElementsByClassName('h-captcha').length > 0) {
							document.getElementsByClassName('h-captcha')[0].classList.remove('error-border');
						}
						// Check for reCAPTCHA response
						if (typeof grecaptcha !== 'undefined') {
							var recaptchaResponse = grecaptcha.getResponse();
							if (recaptchaResponse === '') {
								errors++;
								if (document.getElementsByClassName('g-recaptcha').length > 0) {
									document.getElementsByClassName('g-recaptcha')[0].classList.add('error-border');
								}
							}
						} else if (!isUserLoggedIn && document.getElementsByClassName('g-recaptcha').length > 0) {
							document.getElementsByClassName('g-recaptcha')[0].classList.remove('error-border');
						}

						var errorMessage = document.getElementById('commentform-errors');
						if (errors > 0) {
							event.preventDefault(); // Prevent default form submission
							errorMessage.innerHTML = '<p><?php echo wp_kses_post(__('<strong>Error:</strong> Please fill the required fields.')) ?></p>';
						}
					});
				});
			}

			function validateEmail(email) {
				var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
				return emailPattern.test(email);
			}

			function validateURL(url) {
				var urlPattern = /^(http|https):\/\/[^ "]+$/;
				return urlPattern.test(url);
			}
		</script>
		<style>
			#commentform-errors {
				color: red; /* Style for the error texts */
			}
			.error-border {
				border: 1px solid red !important; /* Style for the error input fields */
			}
		</style>
		<?php
	}

	public function simple_guestbook_comment_styles() {
		if (!is_page($this->simple_guestbook_post_id)) return;

		$guestbook_comment_style =
		'
		<style>
			article a {
				text-decoration: none;
			}
			.navigation {
				text-align: center;
			}
			input:not([type=submit]):not([type=checkbox]):not([type=hidden]), .comment-form textarea {
				box-sizing: border-box;
				display: block;
				width: 100%;
				font-familiy: inherit;
				font-size: 1em;
			}
			input:not([type=submit]):not([type=checkbox]), .comment-form textarea {
				padding: calc(0.667em + 2px);
			}
			.simple-guestbook-body {
				margin-bottom: 40px;
			}
			.simple-guestbook-sign,
			.simple-guestbook-show,
			.simple-guestbook-author {
				font-weight: bold;
			}
			.simple-guestbook-entries {
				float: right;
			}
			.simple-guestbook-meta {
				display:flex;
				align-items:center;
				gap: 0.5em;
			}
			.simple-guestbook-meta div:last-child {
				margin-left: auto;
			}
			.simple-guestbook-metadata time {
				font-size: small;
				opacity: 0.3;
			}
			.simple-guestbook-content p {
				margin: 0.5em;
			}
			.simple-guestbook-reply {
				margin-top: -20px;
				margin-left: 40px;
			}
			.simple-guestbook-edit-link {
				margin-right: 0.5em;
			}
		</style>
		';

		echo wp_kses(
			$guestbook_comment_style,
			array(
				'style' => array()
			)
		);
	}

	/**
	 * This will overwrite the custom content generation and outout comments.
	 *
	 * @since    1.0.0
	 */
	public function simple_guestbook_content($content) {
		// Guard
		if (!is_singular() || !in_the_loop() || !is_main_query() || !is_page() || !has_shortcode( $content, 'simple-guestbook' )) {
			return $content;
		}

	 	// Prepare vars
		$item_id = get_queried_object_id();
		$this->simple_guestbook_post_id = $item_id;
		$current_page = get_query_var('cpage', 1);
		$current_url = get_permalink( $item_id );

		// Get options
		$sort_order = Simple_Guestbook_Options::get_plugin_option('sortorder');
		$comments_per_page = Simple_Guestbook_Options::get_plugin_option('perpage');
		$avatar_size = Simple_Guestbook_Options::get_plugin_option('avatarsize');
		$is_reply_enabled = '1' === Simple_Guestbook_Options::get_plugin_option('replyoption') && current_user_can('edit_posts');

		// Show comment form
		if ($current_page == '0') {
			ob_start();
			echo '<div class="simple-guestbook-header" id="simple-guestbook-header">';
			echo '<a class="simple-guestbook-show" id="simple-guestbook-show" href="' . esc_url($current_url) . '">' . esc_html__( 'Show guestbook', 'simple-guestbook' ) . '</a>';
			echo '</div>';

			if (comments_open()) {
				$comment_form_args = array(
					// Change the title of the comment form
					'title_reply' => esc_html__( 'Sign guestbook', 'simple-guestbook' ),
					// Remove "Text or HTML to be displayed after the set of comment fields".
					'comment_notes_after' => '',
				);
				wp_kses_post(comment_form( $comment_form_args ));
			} else {
				echo '<div><i>' . esc_html__('Sorry, comments are closed for this item.') . '</i></div>';
			}
			$content = ob_get_clean();
			return $content;
		}

		// Prepare for showing comments
		$count_comments = wp_count_comments($item_id);
		$total_pages = ceil( $count_comments->approved / $comments_per_page);
		$comments = get_comments(
			array(
				'post_id' => $item_id,
				'order' => $sort_order,
			)
		);

		// Output customization is done using "the walker"
		// https://pressidium.com/blog/understanding-the-walker-class-in-wordpress/
		$comment_args = array(
			'walker'        	=> new Simple_Guestbook_Walker_Comment(),
			'max_depth'         => '1',
			'style'             => 'div',
			'type'              => 'comment',
			'page'              => $current_page,
			'per_page'          => $comments_per_page,
			'avatar_size'       => $avatar_size,
			'reverse_children'  => true,
			'short_ping'        => false,   // @since 3.6
			'echo'              => true
		);

		// allow editors to post replies to guestbook entries if enabled
		if ($is_reply_enabled) {
			$comment_args["max_depth"] = '2';
			$comment_args["short_ping"] = true;
		}
		
		$pagination_args = array(
			'format'       => '?cpage=%#%',
			'total'        => $total_pages,
			'current'      => $current_page,
			'echo'         => false,
			'show_all'     => false,
			'prev_next'    => true,
		);

		// I was trying to collect common navigation related classes
		// and ended up using the following classes together with an aria-label and a role:
		$pagination = '<nav class="navigation paging-navigation pagination loop-pagination" aria-label="page navigation" role="navigation">';
		$pagination .= paginate_links($pagination_args);
		$pagination .= '</nav>';
		
		ob_start();

		echo '<div class="simple-guestbook-header" id="simple-guestbook-header">';
		echo '<a class="simple-guestbook-sign" id="simple-guestbook-sign" href="' . esc_url(add_query_arg('cpage', '0', $current_url)) . '">';
		echo esc_html__( 'Sign guestbook', 'simple-guestbook' );
		echo '</a>';
		echo '<span class="simple-guestbook-entries" id="simple-guestbook-entries">'. esc_html($count_comments->approved) . ' ' . esc_html__( 'entries', 'simple-guestbook' ) . '</span>';
		echo '</div>';
		echo wp_kses_post($pagination);
		// Display comments
		echo '<div class="simple-guestbook-list" id="simple-guestbook-list">';
		wp_list_comments($comment_args, $comments);
		echo '</div>';
		echo wp_kses_post($pagination);
		if ($is_reply_enabled) {
			echo wp_kses_post(comment_form());
		}

		$content .= ob_get_clean();
		return $content;
	}
}
