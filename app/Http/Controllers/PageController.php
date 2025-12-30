<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SiteSetting;

class PageController extends Controller
{
    public function about()
    {
        $content = SiteSetting::where('setting_key', 'page_about_content')->value('setting_value');
        return view('pages.generic', [
            'title' => 'Tentang Kami',
            'content' => $content
        ]);
    }

    public function contact()
    {
        $content = SiteSetting::where('setting_key', 'page_contact_content')->value('setting_value');
        return view('pages.generic', [
            'title' => 'Hubungi Kami',
            'content' => $content
        ]);
    }

    public function privacy()
    {
        $content = SiteSetting::where('setting_key', 'page_privacy_content')->value('setting_value');
        return view('pages.generic', [
            'title' => 'Kebijakan Privasi',
            'content' => $content
        ]);
    }

    public function terms()
    {
        $content = SiteSetting::where('setting_key', 'page_terms_content')->value('setting_value');
        return view('pages.generic', [
            'title' => 'Ketentuan Layanan',
            'content' => $content
        ]);
    }
}
