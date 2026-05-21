<?php

class HomeController extends Controller
{
    public function index()
    {
        $this->view('home/index', [
            'title' => 'Travely - Đặt tour du lịch cinematic',
            'featuredTours' => Tour::featured(8),
            'domesticTours' => Tour::all(['type' => 'domestic', 'limit' => 4]),
            'foreignTours' => Tour::all(['type' => 'foreign', 'limit' => 5]),
            'dealTours' => Tour::deals(4),
        ]);
    }
}

