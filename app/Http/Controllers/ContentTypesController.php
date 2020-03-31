<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Entities\ContentType;


class ContentTypesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $contentTypes = ContentType::all();
        return view('content_types.index',compact('contentTypes'))
        ->withcontentTypes($contentTypes);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
         return view('content_types.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->saveContentType($request);

        return redirect()->route('content_type.index')->with([
            'message' => __('order_packages.message.content_store'),
            'alert-type' => 'success'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $contentType = ContentType::find($id);

        return view('content_types.create', compact('contentType'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */  
    public function update(Request $request, $id)
    {
        $this->saveContentType($request, $id);

        return redirect()->route('content_type.index')->with([
            'message' => __('order_packages.message.content_update'),
            'alert-type' => 'success'
        ]);
    }
    
     private function saveContentType(Request $request, $id = null) {
       
        if (is_null($id)) {
        $contentType = new ContentType;
        } else {
            $contentType= ContentType::find($id);
        }
        $contentType->name = $request->name;
        $contentType->symbol = $request->symbol;
        $contentType->save();
 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $contentType = ContentType::find($id); 
        $contentType->delete();

        return redirect()->route('content_type.index');
    }
}
