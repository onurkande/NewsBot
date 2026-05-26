<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Feature;
use App\Models\FeatureSetting;
use App\Models\About;
use App\Models\AboutImage;
use App\Models\Video;
use App\Models\Blog;
use App\Models\Contact;
use App\Models\SliderImage;
use App\Models\SliderSetting;
class IndexController extends Controller
{
    public function index()
    {
        return view('site.index');
    }
}
