<?php

namespace Modules\Page\Http\Controllers;

use Illuminate\Http\Response;
use Modules\Storefront\Http\ViewComposers\HomePageComposer;

class HomeController
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $app_module = config('app_switch.app_module');
        if (isStoreFront($app_module)) {
            return view('storefront::public.home.index');
        } elseif (isFloraMom($app_module)) {
            return view('floramom::layouts.main');
        }
    }
    public function getSections()
    {
        $home_page_composer = new HomePageComposer();
        return $home_page_composer->sections();
    }

	 public function getMobileSections()
    {
        $home_page_composer = new HomePageComposer();
        return $home_page_composer->mobileSections();
    }
}
