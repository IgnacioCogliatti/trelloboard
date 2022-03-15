<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Http;

class ApiController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    public function getInfoOfFile(){
        $albuns = array();
        $decades = array();
        if ($file = fopen('C:\laragon\www\trelloboard\public\discography.txt','r')) {
            while(!feof($file)) {
                $line = fgets($file);
                if($line != false){
                    if(!in_array($line[2],$decades)){
                        array_push($decades,$line[2]);
                    }
                }                array_push($albuns,$line);
            }
            fclose($file);
        }
        sort($albuns);
        sort($decades);
        array_push($decades,'0');
        array_push($decades,'1');
        array_shift($decades);
        array_shift($decades);
        array_shift($albuns);
        $this->createTrealloBoard();
        $board_id = $this->getBoardId();
        $info_of_columns = $this->createTrealloColumns($decades,$board_id);
        $this->createCardsOnEachList($decades,$albuns,$info_of_columns);
        dd('llego al fin');
    }


    private function createTrealloBoard(): bool
    {
        $client = new \GuzzleHttp\Client();
        //CREATE BOARD
        Http::post('https://api.trello.com/1/boards/?name=BOB+DYLAN+ALBUNES&defaultLists=false&desc=Board+created+with+trello+api+to+show+Bob+Dylan+albunes&prefs_background=sky&key=c2397582f4831bd1d35d12efccfe413c&token=5bd47655f02ac0176df5f6457925c3363007b70499a4103f82c524e2a5f75da4')->body();
        return true;
    }

    private function createTrealloColumns(array $decades, $board_id)
    {
        $info_of_list = array();
        foreach ($decades as $decade){
            $client = new \GuzzleHttp\Client();
            //CREATE BOARD
            $info = Http::post('https://api.trello.com/1/lists?name=' . $decade . '0s&idBoard=' . $board_id . '&key=c2397582f4831bd1d35d12efccfe413c&token=5bd47655f02ac0176df5f6457925c3363007b70499a4103f82c524e2a5f75da4')->body();
            $info = json_decode($info)->id;
            $info_of_list[$decade] = $info;
        }
        return $info_of_list;
    }

    private function getBoardId()
    {
        $client = new \GuzzleHttp\Client();
        //CREATE BOARD
        $boards = Http::get('https://api.trello.com/1/members/me/boards?key=c2397582f4831bd1d35d12efccfe413c&token=5bd47655f02ac0176df5f6457925c3363007b70499a4103f82c524e2a5f75da4')->body();
        $board_id = json_decode($boards)[0]->id;
        return $board_id;
    }

    private function createCardsOnEachList(array $decades, array $albuns, $info_of_columns)
    {
        foreach ($albuns as $albun){
            $client = new \GuzzleHttp\Client();
            //CREATE BOARD
            Http::post('https://api.trello.com/1/cards?idList=' . $info_of_columns[$albun[2]] . '&name=' . $albun . '&key=c2397582f4831bd1d35d12efccfe413c&token=5bd47655f02ac0176df5f6457925c3363007b70499a4103f82c524e2a5f75da4')->body();
          }
    }

}
