<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



//Arrival
Route::prefix('arrivals')->group(function () {
    Route::get("/", "ArrivalController@getAll");
    Route::get("/inTransitorpartial","ArrivalController@getInTransitorPartiallyReceivedArrivals");
    Route::get("{id}", "ArrivalController@getbyId");
    Route::get("{id}/tier","ArrivalController@getTierbyArrivalId");
    Route::post("/","ArrivalController@create");
    Route::patch("{id}","ArrivalController@update");
    Route::delete("{id}","ArrivalController@logicalDelete") ;
});

//Tier
Route::prefix('tiers')->group(function () {

    Route::get("/", "TierController@getAll");
    Route::get("/suppliers","TierController@getAllSuppliers");
});


//Department
Route::prefix('departments')->group(function () {

    Route::get("/", function () {
        return "department test";
    });

});


//Material
Route::prefix('materials')->group(function () {

    Route::get("/", "MaterialController@getAll");
    Route::get("{id}", "MaterialController@getbyId");
    Route::get("{id}/tier","MaterialController@getTierbyArrivalId");
    Route::post("/","MaterialController@create");
    Route::patch("{id}","MaterialController@update");
    Route::delete("{id}","MaterialController@logicalDelete") ;

});

Route::prefix('batches')->group(function () {

    Route::get("/", "MaterialBatchController@getAll");
    Route::post("/","MaterialBatchController@create");

});


//MaterialStatus
Route::prefix('materialstatus')->group(function () {

    Route::get("/", function () {
        return "MaterialStatus test";
    });

});


//MaterialType
Route::prefix('materialtypes')->group(function () {

    Route::get("/", "MaterialTypeController@getAll");
    Route::post("/", "MaterialTypeController@create");

});



//Operation
Route::prefix('operations')->group(function () {

    Route::get("/", function () {
        return "Operation test";
    });

});


//OperationDefinitions
Route::prefix('operationdefinitions')->group(function () {

    Route::get("/", function () {
        return "OperationDefinitions test";
    });

});


//Product
Route::prefix('products')->group(function () {

    Route::get("/", function () {
        return "Product test";
    });

});


//ProductMaterial
Route::prefix('productmaterial')->group(function () {

    Route::get("/", function () {
        return "ProductMaterial test";
    });

});


//ProductOperations
Route::prefix('productoperations')->group(function () {

    Route::get("/", function () {
        return "ProductOperations test";
    });

});


//ProductSeries
Route::prefix('productseries')->group(function () {

    Route::get("/", function () {
        return "ProductSeries test";
    });

});


//ProductStatus
Route::prefix('productstatus')->group(function () {

    Route::get("/", function () {
        return "ProductStatus test";
    });

});


//Shipping
Route::prefix('shipping')->group(function () {

    Route::get("/", function () {
        return "Shipping test";
    });

});


//Stock
Route::prefix('locations')->group(function () {

    Route::get("/", "LocationController@getAll");

});



//Unit
Route::prefix('units')->group(function () {

    Route::get("/", function () {
        return "Unit test";
    });

});


//User
Route::prefix('users')->group(function () {

    Route::get("/", function () {
        return "User test";
    });

});