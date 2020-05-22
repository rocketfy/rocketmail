<?php

Route::get('/', 'MailablesController@toMailablesList')->name('backetfy.mails.index');

Route::group(['prefix' => 'plantillas'], function () {
    Route::get('/', 'TemplatesController@index')->name('backetfy.mails.templateList');
    Route::get('new', 'TemplatesController@select')->name('backetfy.mails.selectNewTemplate');
    Route::get('new/{type}/{name}/{skeleton}', 'TemplatesController@new')->name('backetfy.mails.newTemplate');
    Route::get('edit/{templatename}', 'TemplatesController@view')->name('backetfy.mails.viewTemplate');
    Route::post('new', 'TemplatesController@create')->name('backetfy.mails.createNewTemplate');
    Route::post('delete', 'TemplatesController@delete')->name('backetfy.mails.deleteTemplate');
    Route::post('update', 'TemplatesController@update')->name('backetfy.mails.updateTemplate');
    Route::post('preview', 'TemplatesController@previewTemplateMarkdownView')->name('backetfy.mails.previewTemplateMarkdownView');
});

Route::group(['prefix' => 'acciones'], function () {
    Route::get('/', 'MailablesController@index')->name('backetfy.mails.mailableList');
    Route::get('view/{name}', 'MailablesController@viewMailable')->name('backetfy.mails.viewMailable');
    Route::get('edit/template/{name}', 'MailablesController@editMailable')->name('backetfy.mails.editMailable');
    Route::post('parse/template', 'MailablesController@parseTemplate')->name('backetfy.mails.parseTemplate');
    Route::post('preview/template', 'MailablesController@previewMarkdownView')->name('backetfy.mails.previewMarkdownView');
    Route::get('preview/template/previewerror', 'MailablesController@templatePreviewError')->name('backetfy.mails.templatePreviewError');
    Route::get('preview/{name}', 'MailablesController@previewMailable')->name('backetfy.mails.previewMailable');
    Route::get('new', 'MailablesController@createMailable')->name('backetfy.mails.createMailable');
    Route::post('new', 'MailablesController@generateMailable')->name('backetfy.mails.generateMailable');
    Route::post('delete', 'MailablesController@delete')->name('backetfy.mails.deleteMailable');
});
