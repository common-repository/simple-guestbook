<?php

class Simple_Guestbook_Activator {

	public static function activate() {
		Simple_Guestbook_Options::set_default_options();
	}
}
