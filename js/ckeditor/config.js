/**
 * @license Copyright (c) 2003-2012, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	config.uiColor = '#444444';
	config.language = 'ru';
	// config.toolbarLocation = 'bottom';
 	/*config.toolbar = [
                ['Undo', 'Redo'],
                [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ],
                [ 'FontSize', 'TextColor', 'BGColor' ],
                [ 'UIColor' ],
                ['NumberedList', 'BulletedList'],
                ['Sourcedialog']
            ]; 	*/
			config.toolbar = [
        { name: 'basicstyles', items : ['Sourcedialog', '-', 'Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat' ] },
				{ name: 'paragraph', items : [ 'NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote','CreateDiv',
				'-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','BidiLtr','BidiRtl' ] },
				{ name: 'links', items : [ 'Link','Unlink','Anchor' ] },
				{ name: 'insert', items : [ 'Image','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak','Iframe' ] },
				'/',
				{ name: 'styles', items : [ 'Styles','Format','Font','FontSize' ] },
				{ name: 'colors', items : [ 'TextColor','BGColor' ] },
				{ name: 'tools', items : [ 'Maximize', 'ShowBlocks','-','About' ] },

				{ name: 'document', items : [ 'Source','-','Save','NewPage','DocProps','Preview','Print','-','Templates' ] },
				{ name: 'clipboard', items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo' ] },
				{ name: 'editing', items : [ 'Find','Replace','-','SelectAll','-','SpellChecker', 'Scayt' ] },
            ];
  config.extraPlugins = 'sourcedialog';
  config.removePlugins = 'sourcearea';
};
