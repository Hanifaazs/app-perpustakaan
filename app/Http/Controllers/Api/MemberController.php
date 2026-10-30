<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MemberResource;
use App\Models\Member;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function index()
    {
        $members = Member::paginate(10);

        return MemberResource::collection($members);
    }

    public function show(string $id)
    {
        $member = Member::findOrFail($id);

        return new MemberResource($member);
    }
}
