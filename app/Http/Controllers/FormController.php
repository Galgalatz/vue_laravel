<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class FormController extends Controller
{
    public function index()
    {
        self::$data['title'] = 'להצטרפות';            
        return view('form', self::$data);
    }

    public function submit(Request $request) {
        $valid = $this->validate($request, [
            'name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|digits:10',
            'user_id' => 'required|numeric',
        ]);


        if($valid){
            $data = $request->input();
            $url = "https://ibell.frb.io/api/test/getJson";
            $response = Http::post($url, [$data]);

            // if($response->json()){
            //     self::process($response->json());            
            // }else{
            //     return false;
            // }

            if($response->successful()){
                return $response;
            }
        }
        
        return response()->json(null, 200);
}


         public function process(Request $request)
        {

            $data = $request->input();
            $bigArray = array_merge($data['חיים ואובדן כושר עבודה'], $data['בריאות ותאונות אישיות']);
            $key = 'premia';
            $premia = array_sum(array_column($bigArray,$key));            
            $url = $premia <= 50 ? 'https://ibell.frb.io/zapier/add/lead/38754' : 'https://ibell.frb.io/zapier/add/lead/38754'; 
           
            return $url;

         }
}