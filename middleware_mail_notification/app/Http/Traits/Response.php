<?php

    namespace App\Http\Traits;

    Trait Response{
        public function sendErrorMessage($validation)
        {
            return response()->json(['status'=>false,'message'=>'validation error(s)','Error'=>$validation->errors()],422);
        }

        public function sendSuccessResponse($message,$data='')
        {
            return response()->json(['status'=>true,'message'=>$message,'data'=>$data]);
        }

        public function sendFailerResponse($message)
        {
            return response()->json(['status'=>false,'message'=>$message]);
        }
    }
?>