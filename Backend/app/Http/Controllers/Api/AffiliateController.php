<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\AffiliatePost;
use App\Http\Resources\UserResource;

class AffiliateController extends Controller
{
    public function getUserAffiliates(User $user)
    {
        return User::collection($user->affiliates);
    }

    public function postUserAffiliate(AffiliatePost $request, User $user)
    {
        if ($request->email == $user->email) {
            return response(['error' => 'Affiliate e-mail is equal to yours'], 400);
        }

        $affiliate = User::where('email', $request->email)->first();
        if ($affiliate == null) {
            return response(['error' => 'Affiliate e-mail does not exist'], 404);
        }

        $alreadyAffiliated = false;
        foreach ($user->affiliates as $affiliated) {
            if ($affiliated->id == $affiliate->id) {
                $alreadyAffiliated = true;
                break;
            }
        }

        if ($alreadyAffiliated) {
            return response(['error' => 'Affiliate e-mail is already affiliated'], 400);
        }
        $user->affiliates()->attach($affiliate->id);

        return new UserResource($affiliate);
    }
}
