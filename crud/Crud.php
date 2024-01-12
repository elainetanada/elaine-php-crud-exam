<?php

require_once("../services/DatabaseService.php");

class Crud {

    const ASSET_IMAGE_DIR = 'assets/images/';

    /** @var DatabaseService $instance */
    private $db;
    
    /**
	* ProductRepository Constructor
	*/
    public function __construct() {
        $this->db = DatabaseService::getInstance();
    }

    /**
	* Get Product
    * 
    * @return array
	*/
	public function getAll()
	{
		$allProducts = $this->db->fetchAllQuery('SELECT * FROM `product`');

        $productsArray = [];

		foreach($allProducts as $product) {
            $productsArray[] = [
                'id' => $product['id'],
                'name' => $product['name'],
                'unit' => $product['unit'],
                'price' => $product['price'],
                'expiry_date' => $product['expiry_date'],
                'available_inventory' => $product['available_inventory'],
                'available_inventory_price' => number_format($product['available_inventory'] * $product['price'], 2),
                'image' => self::ASSET_IMAGE_DIR . $product['id'] . '/' . $product['image'],
                'created_at' => $product['created_at'],
                'updated_at' => $product['updated_at'],
            ];
		}

        return $productsArray;
	}

    /**
	* Get by id
    *
    * @param integer id
    * @return array
	*/
	public function get($id)
	{
		$product = $this->db->fetchQuery('SELECT * FROM `product` WHERE id = ' . $id);
        
        $product['expiry_date'] = date("Y-m-d", strtotime($product['expiry_date']));
        $product['image'] = '/'.self::ASSET_IMAGE_DIR . $product['id'] . '/' . $product['image'];

        return $product;
	}

    /**
	* Store Data
    *
    * @param array $payload
    * @return array
	*/
	public function store(array $payload)
	{
		try{
			$sql = "INSERT INTO `product` (`name`, `unit`, `price`, `expiry_date`, `available_inventory`, `image`) VALUES('". $payload['name'] . "','" . $payload['unit'] . "'," . $payload['price'] . ",'" . $payload['expiry_date'] . "'," . $payload['available_inventory'] . ",'" . $payload['image'] . "')";
			$this->db->executeQuery($sql);
			
			return $this->db->getLastInsertId($sql);
		} catch(Exception $e) {
			return $e->getMessage();
		}
        
	}

	/**
	* Update Data
    *
	* @param string $id
    * @param array $payload
    * @return array
	*/
	public function update($id, array $payload)
	{
		try{
			
			$sql = "UPDATE product SET
				 	`name` = '". $payload['name'] . "',
					`unit` = '". $payload['unit'] . "',
					`price` = " . $payload['price'] . ",
					`expiry_date` = '". $payload['expiry_date'] . "',
					`available_inventory` = " . $payload['available_inventory'] . "";
					
				
			if(isset($payload['image']) && $payload['image']) {
				$sql .= ", `image` = '". $payload['image'] . "'";
			}
			
			$sql .= " WHERE `id` = " . $id;
		    $this->db->executeQuery($sql); 
			return true;
		} catch(Exception $e) {
			return $e->getMessage();
		}
        
	}

    /**
	* Delete Data
    *
    * @param integer $id
    * @return array
	*/
	public function delete($id)
	{
        $this->db->executeQuery("DELETE FROM `product` WHERE `id`=" . $id);
	}
}
$crudInstance = new Crud();

if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["action"])) {
    // Handle GET requests (Read)
    if ($_GET["action"] === "read") {
       $products = $crudInstance->getAll();
       echo json_encode($products);
    }

    if ($_GET["action"] === "readOne" && $_GET['id']) {
        $product = $crudInstance->get($_GET['id']);
        echo json_encode($product);
    }
} elseif ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["action"])) {
    $request = array_filter($_POST); // remove fields with empty values
    $file = $_FILES;
    // Handle POST requests (Create)
    if ($_POST["action"] === "create") { // create a product
        $uploadFile =  basename($_FILES['image']['name']);
        $request['image'] = $uploadFile;
        $result = ['success' => false, 'message' => 'Failed to save product'];
        $statusCode = 400;
        $newProduct = $crudInstance->store($request);

        if($newProduct) {
            if(!file_exists('../assets/images/'. $newProduct)) {
                mkdir('../assets/images/'. $newProduct, 0775);
            }

            $result = ['success' => true, 'message' => 'Successfully saved but there was a problem with uploading the file.'];

            if (move_uploaded_file($_FILES['image']['tmp_name'], '../assets/images/'. $newProduct . '/'. $uploadFile)) {
                $request['image'] = $uploadFile;

                $result = ['success' => true, 'message' => 'Successfully saved!'];
            } 
            
            echo json_encode($result);
        }
    }

    if ($_POST["action"] === "update" && $_GET['id']) {// Update a product
        $uploadFile =  ($file) ? basename($_FILES['image']['name']) : '';
        $request['image'] = $uploadFile;
        $result = ['success' => false, 'message' => 'Failed to update product'];
        $statusCode = 400;
        
        $updateProduct = $crudInstance->update($_GET['id'], $request);

        if($updateProduct) {
            if(!file_exists('../assets/images/'. $updateProduct)) {
                mkdir('../assets/images/'. $_GET['id'], 0775);
            }

            if ($uploadFile && move_uploaded_file($_FILES['image']['tmp_name'], '../assets/images/'. $_GET['id'] . '/'. $uploadFile)) {
                $request['image'] = $uploadFile;

               
            } 
            $result = ['success' => true, 'message' => 'Successfully updated!'];
            
            echo json_encode($result);
        }
    }
} elseif ($_SERVER["REQUEST_METHOD"] === "DELETE") { // Delete a Product
    if ($_GET['id']) {
        $crudInstance->delete($_GET['id'], $request);
        echo json_encode(['success' => true, 'message' => 'Successfully deleted!']);
    }
    
} else {
    echo "Method not allowed";
}
