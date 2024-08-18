<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stichoza\GoogleTranslate\GoogleTranslate;

class GoogleTranslateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /** Translate */
    public function translate(Request $request)
    {
        if (isset(request()->title)) {
            $title = request()->title;
        }
         if (isset(request()->description)) {
            $description = request()->description;
        }
        if (isset(request()->content)) {
            $content = request()->content;
        }
        $data_language_from = request()->data_language_from;
        $data_language_to = request()->data_language_to;

        $translation = new GoogleTranslate($data_language_from);
        $translation->setTarget($data_language_to);

        $array_data = [];
        if (isset(request()->description)) {
            array_push($array_data, array('description' => $translation->translate($description)));
        } else {
            array_push($array_data, array('description' => ''));
        }
        if (isset(request()->content)) {
            array_push($array_data, array('content' => $translation->translate($content)));
        } else {
            array_push($array_data, array('content' => ''));
        }
        if (isset(request()->title)) {
            array_push($array_data, array('title' => $translation->translate($title)));
        } else {
            array_push($array_data, array('title' => ''));
        }
        return json_encode($array_data);
    }

}
