<?php

namespace rocketfy\rocketMail\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\App;
use rocketfy\rocketMail\rocketMail;

class TemplatesController extends Controller
{
    public function __construct()
    {
        abort_unless(
            App::environment(config('rocketmail.allowed_environments', ['local'])),
            403
        );
    }

    public function index()
    {
        $active_item = 'mails';
        $nocard = 1;

        $skeletons = rocketMail::getTemplateSkeletons();
        $templates = rocketMail::getTemplates();

        return View(rocketMail::$view_namespace.'::sections.templates', compact('skeletons', 'templates', 'active_item', 'nocard'));
    }

    public function new($type, $name, $skeleton)
    {
        $active_item = 'mails';
        $nocard = 1;
        $type = $type === 'html' ? $type : 'markdown';

        $skeleton = rocketMail::getTemplateSkeleton($type, $name, $skeleton);

        return View(rocketMail::$view_namespace.'::sections.create-template', compact('skeleton', 'active_item', 'nocard'));
    }

    public function view($templateslug = null)
    {
        $active_item = 'mails';
        $nocard = 1;
        $template = rocketMail::getTemplate($templateslug);

        if (is_null($template)) {
            return redirect()->route('backetfy.mails.templateList');
        }

        return View(rocketMail::$view_namespace.'::sections.edit-template', compact('template', 'active_item', 'nocard'));
    }

    public function create(Request $request)
    {
        return rocketMail::createTemplate($request);
    }

    public function select(Request $request)
    {
        $active_item = 'mails';
        $nocard = 1;
        $skeletons = rocketMail::getTemplateSkeletons();

        return View(rocketMail::$view_namespace.'::sections.new-template', compact('skeletons', 'active_item', 'nocard'));
    }

    public function previewTemplateMarkdownView(Request $request)
    {
        return rocketMail::previewMarkdownViewContent(false, $request->markdown, $request->name, true);
    }

    public function delete(Request $request)
    {
        $usingTemplate = rocketMail::getMailables()->filter(function ($mail) use ($request) {
            return false !== stristr($mail['view_path'], $request->templateslug);
        });
        if (rocketMail::deleteTemplate($request->templateslug)) {
            foreach ($usingTemplate as $key => $mail) {
                $mail_file = $mail['path_name'];
                $view_path = $mail['view_path'];
                $currentTemplate = 'rocketmail::templates.'.$request->templateslug;
                
                $selectedTemplate = 'view.name';
                
                $mail_file_content = file_get_contents($mail_file);
                $newMail = str_replace($currentTemplate, $selectedTemplate, $mail_file_content);
                file_put_contents($mail_file, $newMail);
                $mail_file_content = file_get_contents($mail_file);
            }

            return response()->json([
                'status' => 'ok',
            ]);
        } else {
            return response()->json([
                'status' => 'error',
            ]);
        }
    }

    public function update(Request $request)
    {
        return rocketMail::updateTemplate($request);
    }
}
