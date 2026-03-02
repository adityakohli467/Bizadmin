<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Internalorder_model extends CI_Model{
	
	function __construct() {
		parent::__construct();
		$this->location_id = $this->session->userdata('location_id');
	}
	// in case of internal order locations means sublocations
	
	public function fetchLocations($branch_id,$columnsToRetreive='',$notIsKitchen = '',$lastCountedDate='',$where=''){
        if($columnsToRetreive==''){
          $cols = '*';  
        }else{
            $cols = $columnsToRetreive;  
        }
        
        if($where !=''){
          $extraWhere = ' AND '.$where;
        }else{
           $extraWhere = '' ;
        }
	   $query = "SELECT ".$cols." FROM `SUPPLIERS_internalOrderLocations` WHERE is_deleted = 0".$extraWhere;
  
	  if($notIsKitchen !=''){
	    $query .= " and is_kitchen = 0";
	  }
	   $query .= " order by is_kitchen ASC";
	  
// 	  if($lastCountedDate !=''){
// 	    $query .= " and last_countedAt = '".date('Y-m-d')."'";
// 	  }
	  
	  $query=$this->tenantDb->query($query);
              
        $res = $query->result_array();
        // echo $lastQuery = $this->tenantDb->last_query(); exit;
        if(!empty($res)){
            return $res;
        }else{
            return false;
        }
	  
      }
      
       public function addLocation($data) {
       $insertData = array(
        'name' => $data['name'],
        'email' => $data['email'],
        'ccemail' => $data['ccemail'],
        'requireDD' => $data['requireDD'],
        'is_kitchen' => $data['is_kitchen'],
        'status' => '1',
        'location_id' => $data['location_id'] !='' ? $data['location_id'] :  $this->location_id,
        'created_at' => date('Y-m-d')
    );
    $this->tenantDb->insert('SUPPLIERS_internalOrderLocations', $insertData);
    return true;

}

   public function updateLocation($id, $data) {
      $this->tenantDb->where('id', $id);
      $this->tenantDb->update('SUPPLIERS_internalOrderLocations',$data);
      return true;
    }
    

	   public function locationStatus($id, $data) {
        if (isset($data['is_deleted']) && $data['is_deleted'] == 1) {
        $this->tenantDb->set('is_deleted', '1');
        } else {
        $this->tenantDb->set('status', $data['status']);
        }
        $this->tenantDb->where('id', $id);
        $this->tenantDb->update('SUPPLIERS_internalOrderLocations');
      }
   
   
//   Product reletd queries
   function mergeArrayById($array) {
    $result = array();
    
     foreach ($array as $item) {
        $id = $item['id'];
       
    if (!isset($result[$id])) {
    $result[$id] = $item;
    $result[$id]['par_level'] = array();
    $result[$id]['sublocation_id'] = array();
    $subLocArray = array();
    $sameProducts = array_filter($array, function($element) use ($id) {
        return $element['id'] === $id;
    });
    if(isset($sameProducts) && !empty($sameProducts)){  
    foreach($sameProducts as $sameProduct){
    $subLocId = isset($sameProduct['subLoc_id']) ? $sameProduct['subLoc_id'] : null;
    if(!empty($subLocId)){
    array_push($subLocArray, $subLocId);      
    $result[$id]['par_level'][$subLocId] = isset($sameProduct['par_level']) ? $sameProduct['par_level'] : '';
    }
    $result[$id]['subLoc_id'] = $subLocArray;
    }    
    }
    // Store sublocation_id as JSON-encoded associative array for view compatibility
    $result[$id]['sublocation_id'] = !empty($result[$id]['par_level']) ? json_encode($result[$id]['par_level']) : null;
    }
   
    }
    $result = array_values($result);
    return $result;
}
	public function fetchProducts($id=''){
        $this->tenantDb->select('id');
        $this->tenantDb->from('SUPPLIERS_internalOrderLocations');
        $this->tenantDb->where('location_id', $this->location_id);
        $querySub = $this->tenantDb->get();
        $result = $querySub->result_array();
        if(isset($result) && !empty($result)){
          $subLocationId =  $result[0]['id']; 
        }else{
         $subLocationId = 0;
        }
       
	   $query = "SELECT sip.*,spl.par_level,spl.sublocation_id as subLoc_id,sic.category_name FROM `SUPPLIERS_internalOrderProducts` sip  left join `SUPPLIERS_internalOrderCategory` sic on sip.category_id = sic.id left join `SUPPLIERS_internalOrderProductsToSubLocation` spl on sip.id = spl.product_id WHERE sip.is_deleted = 0 AND (spl.sublocation_id =".$subLocationId." OR sip.location_id=".$this->location_id.")";
	   if($id !=''){
	    $query .= " AND sip.id = ".$id;
	   }
	   $query .=" order by sip.sort_order ASC";
	   
	   //echo $query; exit;
	   $query=$this->tenantDb->query($query);
        $res = $query->result_array();
        if(!empty($res)){
            $disticntProducts = $this->mergeArrayById($res);
            return $disticntProducts;
        }else{
            return false;
        }
	  
      }
      
     public function fetchProductsSubLocationWise(){
      
     $this->tenantDb->distinct(); 
     $this->tenantDb->select('id, name');
     $this->tenantDb->from('SUPPLIERS_internalOrderProducts');
     $this->tenantDb->where('location_id', $this->location_id);
     $this->tenantDb->order_by('sort_order', 'ASC'); 
     $query = $this->tenantDb->get();

     $res = $query->result_array(); // Fetch the results

     if (!empty($res)) {
       return $res;
      } else {
      return false;
     }

	  
      } 
     public function addProduct($data) {
       
         // arrange array for par level for each sublocation will be different
         
         $subParLevel = [];
         foreach($data['subLocId'] as $index => $subLocId){
           $subParLevel[$subLocId] =  $data['par_level'][$index];
         }
      $insertData = array(
        'name' => $data['productName'],
        'uom' => $data['uom'],
        'price' => $data['price'],
        'category_id' => $data['category_id'],
        'requireAttach' => isset($data['requireAttach']) && $data['requireAttach'] == 'on' ? 1 : 0,
        'requireTemp' => isset($data['requireTemp']) && $data['requireTemp'] == 'on' ? 1 : 0,
        'sublocation_id' => json_encode($subParLevel),
        'location_id' => $this->location_id,
        'status' => '1',
        'created_at' => date('Y-m-d')
    );
     
    $this->tenantDb->insert('SUPPLIERS_internalOrderProducts', $insertData);
    $productId = $this->tenantDb->insert_id();
     $subData = [];
    foreach($data['subLocId'] as $indexS => $subLocId){
     $subData[$indexS]['product_id'] =  $productId;
     $subData[$indexS]['sublocation_id'] =  $subLocId;
     $subData[$indexS]['par_level'] =  $data['par_level'][$indexS];
     }
    $this->tenantDb->insert_batch('SUPPLIERS_internalOrderProductsToSubLocation', $subData);
    return true;

  }
    public function updateProduct($id, $data) {
    
    
    foreach($data['subLocId'] as $index => $subLocId){
           $subParLevel[$subLocId] =  $data['par_level'][$index];
         }
     $updateData = array(
        'name' => $data['productName'],
        'uom' => $data['uom'],
        'price' => $data['price'],
        'category_id' => $data['category_id'],
        'requireAttach' => isset($data['requireAttach']) && $data['requireAttach'] == 'on' ? 1 : 0,
        'requireTemp' => isset($data['requireTemp']) && $data['requireTemp'] == 'on' ? 1 : 0,
        'sublocation_id' => json_encode($subParLevel),
        'status' => '1',
        'updated_at' => date('Y-m-d')
    );    
         
    $subData = [];
    foreach($data['subLocId'] as $indexS => $subLocId){
     $subData[$indexS]['product_id'] =  $id;
     $subData[$indexS]['sublocation_id'] =  $subLocId;
     $subData[$indexS]['par_level'] =  $data['par_level'][$indexS];
     $this->tenantDb->where('product_id', $id);
     $this->tenantDb->delete('SUPPLIERS_internalOrderProductsToSubLocation');
     }
    
    $this->tenantDb->insert_batch('SUPPLIERS_internalOrderProductsToSubLocation', $subData);
    $this->tenantDb->where('id', $id);
    $this->tenantDb->update('SUPPLIERS_internalOrderProducts',$updateData);
   
    return true;

}

public function productStatus($id, $data ,$table) {
    if (isset($data['is_deleted']) && $data['is_deleted'] == 1) {
        $this->tenantDb->set('is_deleted', '1');
    } else {
        $this->tenantDb->set('status', $data['status']);
    }
 
    $this->tenantDb->where('id', $id);
    $this->tenantDb->update($table);
    return true;
   }
   
   
   function insertProductCountBatch($insertData){
      $this->tenantDb->insert_batch('SUPPLIERS_internalOrderProductCount', $insertData);
   }
   
    function updateProductCountBatch($updateData){
        // echo "<pre>"; print_r($updateData); exit;
      $this->tenantDb->update_batch('SUPPLIERS_internalOrderProductCount', $updateData, 'id'); 
       
   }
   
   function fetchProductCountData($productId ='',$sublocationId =''){
     $this->tenantDb->select('SUPPLIERS_internalOrderProductCount.id,SUPPLIERS_internalOrderProductCount.dailtQtyNeed,SUPPLIERS_internalOrderProductCount.qtyToMake,SUPPLIERS_internalOrderProductCount.product_id,SUPPLIERS_internalOrderProductCount.sublocation_id');
$this->tenantDb->from('SUPPLIERS_internalOrderProductCount');
$this->tenantDb->where('SUPPLIERS_internalOrderProductCount.location_id', $this->location_id);
$this->tenantDb->where('SUPPLIERS_internalOrderProductCount.date_completed', date('Y-m-d'));
if($productId !='' && $sublocationId !=''){
   
   $this->tenantDb->where('SUPPLIERS_internalOrderProductCount.product_id', $productId);
   $this->tenantDb->where('SUPPLIERS_internalOrderProductCount.sublocation_id', $sublocationId);
}


      $query = $this->tenantDb->get();

      $res = $query->result_array();
  
	
        if(!empty($res)){
            return $res;
        }else{
            return false;
        }
	  
   }
   
   function placeOrder($insertData){
        $this->tenantDb->insert('SUPPLIERS_internalOrderPlacedOrders', $insertData);
        return $this->tenantDb->insert_id();
   }
   function placeOrderInsertproducts($insertData){
       $this->tenantDb->insert_batch('SUPPLIERS_internalOrderPlacedOrdersProducts', $insertData); 
   }
   function fetchInternalOrderSubLocations($colsToFetch=''){
    $query = "SELECT ".$colsToFetch." FROM `SUPPLIERS_internalOrderPlacedOrders` sio LEFT JOIN `SUPPLIERS_internalOrderLocations` sil ON sil.id = sio.sublocation_id where sio.delivery_date ='".date('Y-m-d')."' AND sio.location_id=".$this->location_id;
//   echo $query; exit;
    $result = $this->tenantDb->query($query)->result_array();
    return $result;
    
   }
   
   
    
   function fetchInternalOrder($colsName='',$whereCondition=''){
       if($colsName ==''){
        $colsName = 'sio.*';
       }
       
    $query = "
SELECT ".$colsName."
FROM 
    `SUPPLIERS_internalOrderPlacedOrders` sio
LEFT JOIN 
    `SUPPLIERS_internalOrderPlacedOrdersProducts` siop ON sio.id = siop.order_id
LEFT JOIN 
    `SUPPLIERS_internalOrderProducts` sip ON sip.id = siop.product_id 
LEFT JOIN 
    `SUPPLIERS_internalOrderLocations` sil ON sil.id = sio.sublocation_id      
WHERE  ".$whereCondition."
";

$result = $this->tenantDb->query($query)->result_array();
return $result;
  
   }
   
   function updateOrderProduct($orderId,$productId,$data){
    
    $this->tenantDb->set($data);
    $this->tenantDb->where('order_id', $orderId);
    $this->tenantDb->where('product_id', $productId);
    $this->tenantDb->update('SUPPLIERS_internalOrderPlacedOrdersProducts');   
   }
   
   function fetchOrderProducts($orderId){
       $query = "
SELECT siop.*,sip.name,sip.uom,sic.category_name FROM `SUPPLIERS_internalOrderPlacedOrdersProducts` siop
 
LEFT JOIN  `SUPPLIERS_internalOrderProducts` sip ON sip.id = siop.product_id
     
LEFT JOIN  `SUPPLIERS_internalOrderCategory` sic ON sic.id = sip.category_id     
   
WHERE  
    siop.order_id = ".$orderId;

return $this->tenantDb->query($query)->result_array(); 
   }
   
   function filterOrder($deliveryDateFrom,$deliveryDateTo,$subLocId){
       $this->tenantDb->select('*');
$this->tenantDb->from('SUPPLIERS_internalOrderPlacedOrders');
$this->tenantDb->where('delivery_date >=', date('Y-m-d',strtotime($deliveryDateFrom)));
$this->tenantDb->where('delivery_date <=', date('Y-m-d',strtotime($deliveryDateTo)));
$this->tenantDb->where('sublocation_id =', $subLocId);
$query = $this->tenantDb->get();

// Assuming you want associative results
$result = $query->result_array();

return $result;
   }
   
     public function getFilteredOrders($from_date, $to_date, $product_ids, $location_ids) {
       $this->tenantDb->select('sip.name as product_name, sic.category_name, sipp.delivery_date, sil.name as location_name, sipop.product_id, sip.price as price, SUM(sipop.orderQty) as orderQty')
         ->from('SUPPLIERS_internalOrderPlacedOrders sipp')
        ->join('SUPPLIERS_internalOrderPlacedOrdersProducts sipop', 'sipp.id = sipop.order_id', 'left')
        ->join('SUPPLIERS_internalOrderProducts sip', 'sipop.product_id = sip.id', 'left')
        ->join('SUPPLIERS_internalOrderCategory sic', 'sic.id = sip.category_id', 'left')
        ->join('SUPPLIERS_internalOrderLocations sil', 'sipp.sublocation_id = sil.id', 'left')
        ->where('sipp.delivery_date >=', date('Y-m-d', strtotime($from_date)))
        ->where('sipp.delivery_date <=', date('Y-m-d', strtotime($to_date)));

       if (!empty($location_ids)) {
        $this->tenantDb->where_in('sipp.location_id', $location_ids);
       }

      if (!empty($product_ids)) {
       $this->tenantDb->where_in('sipop.product_id', $product_ids);
      }

      $this->tenantDb->group_by('sipop.product_id');

      $res = $this->tenantDb->get()->result_array();
      
      $total_price = 0;
     $total_orderQty = 0;

foreach ($res as $row) {
    
    $currentPrice = $row['price'] * $row['orderQty'];
    $total_price += $currentPrice;
    $total_orderQty += $row['orderQty'];
}

    // You can return $res for individual product data and $total_price, $total_orderQty for the totals
    return [
    'filteredData' => $res,
    'totalPrice' => $total_price,
    'totalOrderQty' => $total_orderQty,
     ];

      // echo $this->tenantDb->last_query(); exit;
      

            }
         }
	
	?>