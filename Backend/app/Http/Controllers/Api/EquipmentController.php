<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\User;
use App\Models\Division;
use App\Models\Equipment;
use App\Http\Controllers\Controller;
use App\Http\Requests\EquipmentPost;
use App\Http\Resources\EquipmentResource;

class EquipmentController extends Controller
{
    public function getUserEquipments(User $user)
    {
        EquipmentResource::$detail = false;
        return EquipmentResource::collection($user->equipments);
    }

    public function getUserEquipment(User $user, Equipment $equipment)
    {
        EquipmentResource::$detail = true;
        return new EquipmentResource($equipment);
    }

    public function postUserEquipment(EquipmentPost $request, User $user)
    {
        if ($request->division_id != null) {
            $division = Division::find($request->division_id);
            if ($division == null) {
                return response(['error' => 'Division ' . $request->division_id . ' does not exist'], 404);
            }
            if ($division->user_id != $user->id) {
                return response(['error' => 'Division ' . $request->division_id . ' does not belongs to you'], 400);
            }
        }

        $equipment = new Equipment();
        $equipment->fill($request->validated());
        $equipment->user_id = $user->id;

        try {
            $equipment->save();
        } catch (Exception $e) {
            return response(['error' => 'Something went wrong when creating the equipment'], 500);
        }
        EquipmentResource::$detail = true;
        return new EquipmentResource($equipment);
    }

    public function putUserEquipment(EquipmentPost $request, User $user, Equipment $equipment)
    {
        $division = Division::find($request->division_id);
        if ($division == null) {
            return response(['error' => 'Division ' . $request->division_id . ' does not exist'], 404);
        }

        $equipment->fill($request->validated());
        try {
            $equipment->save();
        } catch (Exception $e) {
            return response(['error' => 'Something went wrong when changing the equipment'], 500);
        }
        EquipmentResource::$detail = true;
        return new EquipmentResource($equipment);
    }

    public function deleteUserEquipment(User $user, Equipment $equipment)
    {
        try {
            $equipment->delete();
        } catch (Exception $e) {
            return response(['error' => 'Something went wrong when deleting the equipment'], 500);
        }
        EquipmentResource::$detail = true;
        return new EquipmentResource($equipment);
    }
}
