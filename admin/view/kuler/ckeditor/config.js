/**
 * @license Copyright (c) 2003-2014, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

var allPlugins = ['a11yhelp','about','clipboard','codemirror','colordialog','dialog','div','fakeobjects','find','flash','forms','iframe','image','link','liststyle','magicline','pagebreak','pastefromword','preview','scayt','showblocks','smiley','sourcedialog','specialchar','syntaxhighlight','table','tabletools','templates','wsc'];
var plugins = ['a11yhelp','about','div','find','flash','forms','language','pagebreak','preview','print','newpage','save','smiley','sourcearea','templates'];

CKEDITOR.editorConfig = function( config ) {

    config.extraPlugins  = 'sourcedialog';

    config.removePlugins = eval('plugins.join(",")');

    config.allowedContent = true;
};

