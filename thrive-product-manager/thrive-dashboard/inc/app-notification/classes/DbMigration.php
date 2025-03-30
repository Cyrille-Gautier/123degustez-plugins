<?php
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
class DbMigration {
	static $tableName = 'td_app_notifications';

	public static function migrate() {
		global $wpdb;

		$charsetCollate = $wpdb->get_charset_collate();
		$table = $wpdb->prefix . static::$tableName;
		if ( $wpdb->get_var("SHOW TABLES LIKE '$table'") != $table ) {
			$sql = "CREATE TABLE $table (
				`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				`slug` varchar(255) NOT NULL,
				`title` text NOT NULL,
				`content` longtext NOT NULL,
				`type` varchar(64) NOT NULL,
				`level` text NOT NULL,
				`notification_id` bigint(20) unsigned DEFAULT NULL,
				`notification_name` varchar(255) DEFAULT NULL,
				`start` datetime DEFAULT NULL,
				`end` datetime DEFAULT NULL,
				`button1_label` varchar(255) DEFAULT NULL,
				`button1_action` varchar(255) DEFAULT NULL,
				`button2_label` varchar(255) DEFAULT NULL,
				`button2_action` varchar(255) DEFAULT NULL,
				`dismissed` tinyint(1) NOT NULL DEFAULT 0,
				`created` datetime NOT NULL,
				`updated` datetime NOT NULL,
				PRIMARY KEY (id),
				UNIQUE KEY ian_slug (slug),
				KEY ian_dates (start, end),
				KEY ian_type (type),
				KEY ian_dismissed (dismissed)
			) $charsetCollate;";
			dbDelta($sql);
		}
	}
}