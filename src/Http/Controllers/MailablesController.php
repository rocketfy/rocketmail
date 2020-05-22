<?php

namespace rocketfy\rocketMail\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\App;
use rocketfy\rocketMail\rocketMail;

class MailablesController extends Controller
{
    public function __construct()
    {
        abort_unless(
            App::environment(config('rocketmail.allowed_environments', ['local'])),
            403
      );
    }

    public function toMailablesList()
    {
        return redirect()->route('backetfy.mails.mailableList');
    }

    public function index()
    {
        $active_item = 'mails';
        $nocard = 1;

        $mailables = rocketMail::getMailables();
        $mailables = (null !== $mailables) ? $mailables->sortBy('name') : collect([]);

        return view(rocketMail::$view_namespace.'::sections.mailables', compact('mailables', 'active_item', 'nocard'));
    }

    public function createMailable(Request $request)
    {
        $active_item = 'mails';
        $nocard = 1;
        return view(rocketMail::$view_namespace.'::createmailable', compact(['active_item', 'nocard']));
    }

    public function generateMailable(Request $request)
    {
        return rocketMail::generateMailable($request);
    }

    public function viewMailable($name)
    {
        $active_item = 'mails';
        $nocard = 1;
        $mailable = rocketMail::getMailable('name', $name);

        if ($mailable->isEmpty()) {
            return redirect()->route('backetfy.mails.mailableList');
        }

        $mailable = $mailable->first();


        $templates = rocketMail::getTemplates();
        $namespace = $mailable['namespace'];
        $view_path = ($mailable['view_path']) ? $mailable['view_path'] : 'templates/view.name.blade.php';
        $currentTemplate = substr(explode('/',$view_path)[count(explode('/',$view_path)) - 1], 0, -10);
        $currentMail = $mailable['path_name'];

        return view(rocketMail::$view_namespace.'::sections.view-mailable')->with(compact('mailable', 'templates', 'currentTemplate', 'currentMail', 'active_item', 'nocard'));
    }

    public function editMailable($name)
    { 
        $active_item = 'mails';
        $nocard = 1;
        $templateData = rocketMail::getMailableTemplateData($name);

        if (! $templateData) {
            return redirect()->route('backetfy.mails.viewMailable', ['name' => $name]);
        }

        return view(rocketMail::$view_namespace.'::sections.edit-mailable-template', compact('templateData', 'name', 'active_item', 'nocard'));
    }

    public function templatePreviewError()
    {
        $active_item = 'mails';
        $nocard = 1;
        return view(rocketMail::$view_namespace.'::previewerror', compact(['active_item', 'nocard']));
    }

    public function parseTemplate(Request $request)
    {
        if ($request->onlyChangeView) {
            $mail_file = $request->currentMailable;
            $currentTemplate = $request->currentTemplate;
            $selectedTemplate = $request->selectedTemplate;
            if ($currentTemplate == 'view.name') {
                $selectedTemplate = 'rocketmail::templates.'.$selectedTemplate;
            }
            
            $mail_file_content = file_get_contents($mail_file);
            $newMail = str_replace($currentTemplate, $selectedTemplate, $mail_file_content);
            file_put_contents($mail_file, $newMail);
            $mail_file_content = file_get_contents($mail_file);

            return response()->json([
                'status' => 'ok',
            ]);
        }
        
        $template = $request->has('template') ? $request->template : false;

        $viewPath = $request->has('template') ? $request->viewpath : base64_decode($request->viewpath);

        // ref https://regexr.com/4dflu
        $bladeRenderable = preg_replace('/((?!{{.*?-)(&gt;)(?=.*?}}))/', '>', $request->markdown);

        if (rocketMail::markdownedTemplateToView(true, $bladeRenderable, $viewPath, $template)) {
            return response()->json([
                'status' => 'ok',
            ]);
        }

        return response()->json([
            'status' => 'error',
        ]);
    }

    public function previewMarkdownView(Request $request)
    {
        return rocketMail::previewMarkdownViewContent(false, $request->markdown, $request->name, false, $request->namespace);
    }

    public function previewMailable($name)
    {
        $mailable = rocketMail::getMailable('name', $name);

        if ($mailable->isEmpty()) {
            return redirect()->route('backetfy.mails.mailableList');
        }

        $resource = $mailable->first();

        if (! is_null(rocketMail::handleMailableViewDataArgs($resource['namespace']))) {
            // $instance = new $resource['namespace'];
            //
            $instance = rocketMail::handleMailableViewDataArgs($resource['namespace']);
        } else {
            $instance = new $resource['namespace'];
        }

        if (collect($resource['data'])->isEmpty()) {
            return 'Vista no encontrada';
        }

        $view = ! is_null($resource['markdown']) ? $resource['markdown'] : $resource['data']->view;

        if (view()->exists($view)) {
            try {
                $html = $instance;

                return $html->render();
            } catch (\ErrorException $e) {
                return view(rocketMail::$view_namespace.'::previewerror', ['errorMessage' => $e->getMessage()]);
            }
        }

        return view(rocketMail::$view_namespace.'::previewerror', ['errorMessage' => 'La acción no tiene plantilla asociada.']);
    }

    public function delete(Request $request)
    {
        $name = $request->mailablename;
        $jobName = 'Send'.$name.'Mail';
        $notifyName = $name.'Notify';

        $mailableFile = config('rocketmail.mailables_dir').$name.'.php';
        $jobFile = app_path('Jobs/'.$jobName.'.php');
        $notifyFile = app_path('Notifications/'.$notifyName.'.php');

        if (file_exists($jobFile)) {
            unlink($jobFile);
        }
        if (file_exists($notifyFile)) {
            unlink($notifyFile);
        }

        if (file_exists($mailableFile)) {
            unlink($mailableFile);

            return response()->json([
                'status' => 'ok',
            ]);
        }

        return response()->json([
                'status' => 'error',
            ]);
    }
}
