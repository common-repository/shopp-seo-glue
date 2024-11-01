<?php
/*
Plugin Name: Shopp SEO Glue
Plugin URI: http://freshlybakedwebsites.net/wordpress/shopp/wordpress-seo-compatibility/
Description: Aims to reconcile the popular Shopp and WordPress SEO plugins so that Shopp products can benefit from WordPress SEO's feature set. Currently experimental, this plugin was built against Shopp 1.2.3 and WordPress SEO 1.2.5: it may not work with earlier or later versions of either plugin.
Version: 1.1
Author: Barry Hughes
Author URI: http://freshlybakedwebsites.net/
License: GPL version 3.0 @see http://www.gnu.org/licenses/gpl.html

	Shopp SEO Glue - compatibility layer between Shopp and WordPress SEO
	Copyright (C) 2012 Barry Hughes

	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/


class ShoppSEOGlueLayer {
	public $wpseoMetabox;


	public function __construct() {

		if ($this->onProductEditorPage()) {
			add_action('shopp_warehouse_init',
				array($this, 'pullInWPSEOMetabox'));
			$this->repairURLPreview();
		}
	}


	protected function onProductEditorPage() {
		if ($GLOBALS['pagenow'] !== 'admin.php') return false;
		if (array_key_exists('page', $_GET) === false) return false;
		if ($_GET['page'] !== 'shopp-products') return false;

		return true;
	}


	public function pullInWPSEOMetabox() {
		$this->loadSEOMetaboxClass();
		if ($this->wpseoMetabox !== false)
			$this->reattachWPSEOBehaviours();
	}

	public function loadSEOMetaboxClass() {
		if (defined('WPSEO_PATH') and class_exists('WPSEO_Metabox') === false) {
			require WPSEO_PATH.'admin/class-metabox.php';
			$this->wpseoMetabox = (isset($wpseo_metabox))
				? $wpseo_metabox : false;
		}
	}


	public function reattachWPSEOBehaviours() {
		$this->adjustWPGlobals();

		add_action('admin_print_styles',
			array($this->wpseoMetabox, 'enqueue'));

		add_action('shopp_product_saved',
			array($this, 'wpseoSavePostData'));
	}


	protected function adjustWPGlobals() {
		global $pagenow, $post;

		if (array_key_exists('id', $_GET) and is_numeric($_GET['id']))
			$post = (int) $_GET['id'];

		$pagenow = 'post.php';
	}


	public function wpseoSavePostData() {
		global $post_id;

		if ($post_id === null and array_key_exists('id', $_GET))
			$post_id = (int) $_GET['id'];

		$this->wpseoMetabox->save_postdata($post_id);
	}


	protected function repairURLPreview() {
		add_action('admin_print_footer_scripts',
			array($this, 'insertURLPreviewScript'));

	}


	public function insertURLPreviewScript() {
		echo '<script type="text/javascript">';
		include dirname(__FILE__).'/assets/wpseourlpreview.js';
		echo '</script>';
	}
}


function shoppSEOGlueInit() {
	if (defined('SHOPP_VERSION') and defined('WPSEO_VERSION'))
		new ShoppSEOGlueLayer;
}


add_action('plugins_loaded', 'shoppSEOGlueInit', 80);