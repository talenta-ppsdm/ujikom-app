<?php

namespace App\Http\Controllers;

use App\Repositories\TimekeeperRepository;
use Illuminate\Http\Request;

class TimekeeperController extends Controller
{
    protected TimekeeperRepository $timekeeperRepository;

    public function __construct(TimekeeperRepository $timekeeperRepository)
    {
        $this->timekeeperRepository = $timekeeperRepository;
    }

    public function index()
    {
        $timekeeper = $this->timekeeperRepository->all();

        return view('/admin/timekeeper', compact('timekeeper'));
    }
}
