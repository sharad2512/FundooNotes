<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Label;

class LabelController extends Controller
{
    public function createLabel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:250',
        ]);

        if ($validator->fails()) {
            return response()->json([$validator->errors()], 400);
        }
        $user = auth()->user();

        $label = new Label();
        $label->name = $request->input('name');
        $label->user_id = $user->id;
        $label->save();
        return response()->json([
            'message' => 'Lable created successfully',
            'label'=> $label,
        ],201);
    }
}
