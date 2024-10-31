<?php
/**
 * Plugin Name: ngDesk
 * Plugin URI: https://ngdesk.com/wordpress-plugin
 * Description: Adds ngDesk to your website 
 * Version: 1.0.0
 * Author: ngDesk
 * Author URI: https://ngdesk.com
 * License: GPLv2 or later
 * Text Domain: ngdesk
 */

/*
	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
	Copyright 2005-2015 Automattic, Inc.
*/

defined('ABSPATH') or die('You can\t access this file');

define('NGDESK_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('NGDESK_PLUGIN_URL', plugin_dir_url(__FILE__));
define('NGDESK_PLUGIN', plugin_basename(__FILE__));

if(!class_exists('NgdeskPlugin')) {
	class NgdeskPlugin {
	
	public $ngdeskplugin;
	public $ngdesksettings = array();
	public $ngdesksections = array();
	public $ngdeskfields = array();
	public $ngdesksubdomainValue;
	public $ngdeskchatWidgetIdValue;
	public $ngdesksrc;
	
	function __construct() {
		$this->ngdeskplugin = NGDESK_PLUGIN;
	}
	
	function ngdeskRegister() {
		
		add_action('admin_menu', array($this, 'ngdesk_settings_page'));
		add_action('admin_init', array($this, 'registerNgdeskCustomFields'));
		add_action('template_redirect', array($this,'load_ngdesk_widget'));
		add_filter("plugin_action_links_$this->ngdeskplugin", array($this, 'ngdesk_plugin_settings_link'));
		$this->setNgdeskSettingsData();
		$this->setNgdeskSectionsData();
		$this->setNgdeskFieldsData();
	}
	
	public function ngdesk_settings_page() {
		add_options_page('ngDesk Settings', 'ngDesk', 'manage_options', 'ngdesk_plugin', array($this,'load_ngdesk_settings_page'),'',110);
	}
	
	public function load_ngdesk_settings_page() {
		require_once NGDESK_PLUGIN_PATH . 'templates/ngdesk_settings_page.php';
	}
	
	public function ngdesk_plugin_settings_link($links) {
		$ngdesk_settings_link = '<a href="options-general.php?page=ngdesk_plugin">Settings</a>';
		array_push($links, $ngdesk_settings_link);
		return $links;
	}
	
	public function setNgdeskSettings(array $ngdesksettings) {
		$this -> ngdesksettings = $ngdesksettings;
		return $this;
	}
	public function setNgdeskSections(array $ngdesksections) {
		$this -> ngdesksections = $ngdesksections;
		return $this;
	}
	public function setNgdeskFields(array $ngdeskfields) {
		$this -> ngdeskfields = $ngdeskfields;
		return $this;
	}
	public function registerNgdeskCustomFields() {
		//register setting
		foreach($this->ngdesksettings as $ngdesksetting) {
			register_setting( $ngdesksetting["option_group"], $ngdesksetting["option_name"], (isset($ngdesksetting["callback"] )? $ngdesksetting["callback"] : ''));
		}
		//add settings section
		foreach($this->ngdesksections as $ngdesksection) {
			add_settings_section( $ngdesksection["id"], $ngdesksection["title"], (isset($ngdesksection["callback"] )? $ngdesksection["callback"] : ''), $ngdesksection["page"] );
		}
		
		//add settings field
		foreach($this->ngdeskfields as $ngdeskfield) {
			add_settings_field( $ngdeskfield["id"], $ngdeskfield["title"], (isset($ngdeskfield["callback"] )? $ngdeskfield["callback"] : ''), $ngdeskfield["page"], $ngdeskfield["section"], (isset($ngdeskfield["args"] )? $ngdeskfield["args"] : ''));
		}
		
		
	}
	public function ngdeskOptionsGroup($ngdeskinput) {
		return $ngdeskinput;
	}
	public function ngdeskAdminSection() {
		
	}
	public function ngdeskSubdomain()
	{	
		$ngdesksubdomainValue = esc_attr( get_option( 'subdomain' ) );
		echo '<input type="text" class="regular-text" name="subdomain" value="' . $ngdesksubdomainValue . '" placeholder="Enter subdomain">';
	}
	public function ngdeskWidgetId()
	{	
		$ngdeskchatWidgetIdValue = esc_attr( get_option( 'widgetid' ) );
		echo '<input type="text" class="regular-text" name="widgetid" value="' . $ngdeskchatWidgetIdValue . '" placeholder="Enter widget id">';
	}
	
	public function setNgdeskSettingsData()
	{
		$args = array(
			array(
				'option_group' => 'ngdesk_plugin_settings',
				'option_name' => 'subdomain',
				'callback' => array( $this, 'ngdeskOptionsGroup' )
			),
			array(
				'option_group' => 'ngdesk_plugin_settings',
				'option_name' => 'widgetid',
				'callback' => array( $this, 'ngdeskOptionsGroup' )
			)
		);
		$this->setNgdeskSettings( $args );
	}
	public function setNgdeskSectionsData()
	{
		$args = array(
			array(
				'id' => 'ngdesk_admin_index',
				'title' => 'ngDesk Settings',
				'callback' => array( $this, 'ngdeskAdminSection' ),
				'page' => 'ngdesk_plugin'
			)
		);
		$this->setNgdeskSections( $args );
	}
	public function setNgdeskFieldsData()
	{
		$args = array(
			array(
				'id' => 'subdomain',
				'title' => 'Subdomain:',
				'callback' => array( $this, 'ngdeskSubdomain' ),
				'page' => 'ngdesk_plugin',
				'section' => 'ngdesk_admin_index',
				'args' => array(
					'label_for' => 'Subdomain'
				)
			),
			array(
				'id' => 'widgetid',
				'title' => 'Chat Widget Id:',
				'callback' => array( $this, 'ngdeskWidgetId' ),
				'page' => 'ngdesk_plugin',
				'section' => 'ngdesk_admin_index',
				'args' => array(
					'label_for' => 'Chat Widget Id'
				)
			)
		);
		$this->setNgdeskFields( $args );
	}
	
	function load_ngdesk_widget() {
		require_once NGDESK_PLUGIN_PATH . 'widget.php';

	 }	

}

	$ngdeskPlugin = new NgdeskPlugin();
	$ngdeskPlugin->ngdeskRegister();
}
	

 
