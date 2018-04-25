/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */
function getBaseURL () {
   return location.protocol + "//" + location.hostname + 
      (location.port && ":" + location.port) + "/";
}

CKEDITOR.editorConfig = function(config) {
	base_path = 'http://192.168.100.6/development/onlineshop/public';
	config.filebrowserBrowseUrl = base_path + '/js/ckeditor/kcfinder/browse.php?type=files';
	config.filebrowserImageBrowseUrl = base_path + '/js/ckeditor/kcfinder/browse.php?type=images';
	config.filebrowserFlashBrowseUrl = base_path + '/js/ckeditor/kcfinder/browse.php?type=flash';
	config.filebrowserUploadUrl = base_path + '/js/ckeditor/kcfinder/upload.php?type=files';
	config.filebrowserImageUploadUrl = base_path + '/js/ckeditor/kcfinder/upload.php?type=images';
	config.filebrowserFlashUploadUrl = base_path + '/js/ckeditor/kcfinder/upload.php?type=flash';
	config.extraPlugins = 'video';
	config.extraPlugins = 'imagepaste';
};