<?php

namespace App\Project\Modules\Core\Users\Services;

use App\Project\Modules\Core\Users\User;
use App\Project\Modules\Core\Users\SystemUser;
use App\Models\Web\System\Property\Property;
use App\Models\Web\System\Property\PropertyUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ChangeCurrentPropertyFormAction
{
    public function handle(Request $request, $id)
    {
        DB::beginTransaction();

        $property = Property::find($request->property);

        //set propert to session
        Session::put('CURRENT_PROPERTY_ID',$property->id);
        Session::put('CURRENT_PROPERTY',$property->name);

        DB::commit();
        return ['status' => true, 'message' => 'Current property changed successfully'];
    }
}
