<?php

namespace App\Http\Controllers;

use App\Interfaces\BoardingHouseRepositoryInterface;
use App\Interfaces\CategoryRepositoryInterface;
use App\Interfaces\CityRepositoryInterface;
use Illuminate\Http\Request;

class BoardingHouseController extends Controller
{
      private CityRepositoryInterface $cityRepository;
    private CategoryRepositoryInterface $categoryRepository;
    private BoardingHouseRepositoryInterface $boardingHouseRepository;

    public function __construct(
        CityRepositoryInterface $cityRepository,
        CategoryRepositoryInterface $categoryRepository,
        BoardingHouseRepositoryInterface $boardingHouseRepository
    )
    {
       $this->cityRepository = $cityRepository;
       $this->categoryRepository = $categoryRepository;
       $this->boardingHouseRepository = $boardingHouseRepository;
    }


    public function show($slug)
    {
        $boardingHouse = $this->boardingHouseRepository->getBoardingHouseBySlug($slug);
        return view('pages.boarding-house.show',compact('boardingHouse'));
    }

    public function rooms($slug)
    {
        $boardingHouse = $this->boardingHouseRepository->getBoardingHouseBySlug($slug);
        return view('pages.boarding-house.rooms',compact('boardingHouse'));
    }
    public function find()
    {
        $cities = $this->cityRepository->getAllCities();
        $categories = $this->categoryRepository->getAllCategories();
        return view('pages.boarding-house.find',compact('cities','categories'));
    }

    public function findResult (Request $request){

        $name = $request->input('name');
        $city = $request->input('city');
        $category = $request->input('category');
        $keywords = [];

        if($name)
        {
            $keywords[] = "Name: $name";
        }
        if($city)
        {
            $keywords[] = "City: $city";
        }
        if($category)
        {
            $keywords[] = "Category: $category";
        }


        $keyword = implode(', ', $keywords);
        $results = $this->boardingHouseRepository->getAllBoardingHouses($search = $request->name, $city = $request->city, $category = $request->category);
        return view('pages.boarding-house.search-result',compact('results','keyword'));

    }
}
