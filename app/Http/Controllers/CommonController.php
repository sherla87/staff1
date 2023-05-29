<?php



namespace App\Http\Controllers;



use Illuminate\Http\Request;

use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\DB;

use Illuminate\Database\Schema\Blueprint;

use Illuminate\Support\Facades\Schema;



class CommonController extends Controller

{

    public function get_table_record($table)

    {

        $result = DB::select("select * from " . $table);

        return response()->json($result, 200);

    }

    public function get_table_record_where($table,$recordw,$field,$value)

    {

        $result = DB::select('select * from ' . $table . ' where ' .

        $field . ' = ' . $value);

        return response()->json($result, 200);

    }    





 



    public function exec_insert_record($objt_param)

    {

        $response = (object) ['result' => (object)[],'error' => (object)[]];

        

        /*entity(e) prop exist in input parameter object*/

        if (isset($objt_param->e)) {

            

            $entity=$objt_param->e;

            /*entity table exist in schema*/

            if (Schema::hasTable($entity)) {

                try { 

                /*get field names */

                $arry_field= Schema::getColumnListing($entity);

                /*remove id field*/

                array_shift($arry_field);

                /*prep part1.1: insert into field*/

                $strg_sql='insert into '.$entity.'('.implode(",", 

                    $arry_field).')';

                /*prep part1.2: field format*/

                $strg_sql.=' values ('.implode(",",

                    array_map(function($v){return '?';},$arry_field)).')';

                /*prep part2: field values*/

                $objt_reco=$objt_param->reco;

                $arry_value=array();

                foreach ($arry_field as $field) {

                    if(isset($objt_reco[$field])){

                        array_push($arry_value,$objt_reco[$field]);

                    }else{

                        array_push($arry_value,null);

                    }

                }

                $response->result=DB::insert($strg_sql,$arry_value); 

                }

    

                catch(\Illuminate\Database\QueryException $ex){

                    $response->error=$ex->getMessage();

                } 

                catch (ModelNotFoundException $ex) {

                    $response->error=$ex->getMessage();

                }  



            }/* if table exist */

        }/* if prop exist */

        return response()->json($response, 200);

    }

    

    public function get_insert_record($json=null)

    {

        $objt_param = (object) json_decode($json, true);

        return $this->exec_insert_record($objt_param);

    }   

    public function post_insert_record(Request $request)

    {

        return $this->exec_insert_record($request);

    }   





    public function exec_update_record($objt_param)

    {

        $response = (object) ['result' => (object)[],'error' => (object)[]];

        

        /*entity(e) prop exist in input parameter object*/

        if (isset($objt_param->e)) {

            

            $entity=$objt_param->e;

            /*entity table exist in schema*/

            if (Schema::hasTable($entity)) {

                try { 

                /*get field names */

                $arry_field= Schema::getColumnListing($entity);

                /*remove id field*/

                array_shift($arry_field);

                /*prep part1: update ... set*/

                $strg_sql='update '.$entity.' set ';

                /*prep part2: field mask, field value*/

                $objt_reco=$objt_param->reco;

                $arry_mask=array();

                $arry_value=array();

                foreach ($arry_field as $field) {

                    array_push($arry_mask,$field.'=?');

                    array_push($arry_value,$objt_reco[$field]);

                }

                $strg_sql.= implode(",",$arry_mask);

                /*prep part3: where*/

                $strg_sql.=' where id = ?';

                array_push($arry_value,$objt_reco['id']);

                $response->result=DB::insert($strg_sql,$arry_value); 

                }

    

                catch(\Illuminate\Database\QueryException $ex){

                    $response->error=$ex->getMessage();

                } 

                catch (ModelNotFoundException $ex) {

                    $response->error=$ex->getMessage();

                }  



            }/* if table exist */

        }/* if prop exist */

        return response()->json($response, 200);

    }



    public function get_update_record($json=null)

    {

        $objt_param = (object) json_decode($json, true);

        return $this->exec_update_record($objt_param);

    }   

    public function post_update_record(Request $request)

    {

        return $this->exec_post_record($request);

    } 





    public function exec_delete_record($objt_param)

    {

        $response = (object) ['result' => (object)[],'error' => (object)[]];

        

        /*entity(e) prop exist in input parameter object*/

        if (isset($objt_param->e)) {

            

            $entity=$objt_param->e;

            /*entity table exist in schema*/

            if (Schema::hasTable($entity)) {

                try { 

                /*prep part1: delete from*/

                $strg_sql='delete from '.$entity;

                /*prep part2: where*/

                $strg_sql.=' where id = '.$objt_param->id;

                $response->result=DB::delete($strg_sql); 

                }

    

                catch(\Illuminate\Database\QueryException $ex){

                    $response->error=$ex->getMessage();

                } 

                catch (ModelNotFoundException $ex) {

                    $response->error=$ex->getMessage();

                }  



            }/* if table exist */

        }/* if prop exist */

        return response()->json($response, 200);

    }



    public function get_delete_record($json=null)

    {

        $objt_param = (object) json_decode($json, true);

        return $this->exec_delete_record($objt_param);

    }   

    public function post_delete_record(Request $request)

    {

        return $this->exec_delete_record($request);

    } 

    

}