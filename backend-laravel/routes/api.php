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
    Route::get("{id}", "ArrivalController@getbyId");
    Route::get("{id}/tier","ArrivalController@getTierbyArrivalId");
    Route::post("/","ArrivalController@create");
    Route::patch("{id}","ArrivalController@update");
    Route::delete("{id}","ArrivalController@logicalDelete") ;
});

//Tier
Route::prefix('tiers')->group(function () {

    Route::get("/", "TierController@getAll");
});


//Department
Route::prefix('departments')->group(function () {

    Route::get("/", function () {
        return "department test";
    });

});


//Material
Route::prefix('materials')->group(function () {

    Route::get("/", function () {
        return "Material test";
    });

});


//MaterialStatus
Route::prefix('materialstatus')->group(function () {

    Route::get("/", function () {
        return "MaterialStatus test";
    });

});


//MaterialType
Route::prefix('materialtype')->group(function () {

    Route::get("/", function () {
        return "MaterialType test";
    });

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
Route::prefix('stocks')->group(function () {

    Route::get("/", function () {
        return "Stock test";
    });

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