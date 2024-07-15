https://www.php.net/manual/en/pdostatement.bindvalue.php
https://book.cakephp.org/4/en/orm/query-builder.html#binding-values
<?php

$studentId = '';
$date_of_birth = '';
$uid = '';

$this->Students = $this->getDbTable('Student.Students');
        $student = $this->Students->find()->where([
          'BINARY student_id = :studentId',
          'dob = :dob',
          'university_id = :university_id',
        ])
        ->bind(':studentId', $studentId, 'string')
        ->bind(':dob', $date_of_birth, 'string')
        ->bind(':university_id', $uid, 'integer')
        ->first();

public function getProductInfoBySlug($slug)
{
	$productInfo = $this->ProductModule->find()->contain(['Categories'])->where(array('Products.slug = :productSlug'))->bind(":productSlug", $slug, 'string')->first();
	return $productInfo ? $productInfo->toArray() : '';
}



public function getProductOptionByAttributeFrame($categoryid, $productId=[], $mould=[], $colour=[], $option=[], $order, $offset, $hasGitemIncart)
    {
        $university_prefix = $this->getComponent('CommonFunction')->getDatabaseTablePrefix('categories');
        $prefix = $this->getComponent('CommonFunction')->getDatabaseTablePrefix('options');
        $conn = ConnectionManager::get('organizations');

        $parameters = [$categoryid];
//        $sql = "SELECT a.option_name,a.mould,a.colour,po.pre_price,po.pos_price, po.id, po.product_id, po.attribute_id, c.id, c.title, p.id, p.title, p.slug, a.weight,  ROUND(((po.pos_price - po.pre_price) * 100) / po.pos_price) as discount,  po.pre_price as price from " . $university_prefix . "categories AS c, " . $university_prefix . "products AS p, " . $university_prefix . "product_options AS po," . $prefix . "options AS a where c.id = ? ";

        if($hasGitemIncart == true) {
//            $sql = "SELECT a.option_name,a.mould,a.colour,po.pre_price,po.pos_price, po.id, po.product_id, po.attribute_id, c.id, c.title, p.id, p.title, p.slug, a.weight,  ROUND(((po.pos_price - po.pre_price) * 100) / po.pos_price) as discount, (case when (po.pos_price > po.pre_price ) then po.pre_price when (po.pos_price < po.pre_price ) then po.pos_price when (po.pos_price = po.pre_price ) then po.pos_price end) as price from " . $university_prefix . "categories AS c, " . $university_prefix . "products AS p, " . $university_prefix . "product_options AS po," . $prefix . "options AS a where c.id = ? ";
            $sql = "SELECT a.id  as optionid, a.option_name,a.mould,a.colour,po.pre_price,po.pos_price, po.id as po_option_id, po.product_id, po.attribute_id, c.id as category_id, c.title, p.id, p.title, p.slug, a.weight,  ROUND(((po.pos_price - po.pre_price) * 100) / po.pos_price) as discount,  po.pre_price as price from " . $university_prefix . "categories AS c, " . $university_prefix . "products AS p, " . $university_prefix . "product_options AS po," . $prefix . "options AS a where c.id = :cid ";
        } else {
            $sql = "SELECT a.id as optionid, a.option_name,a.mould,a.colour,po.pre_price,po.pos_price, po.id as po_option_id, po.product_id, po.attribute_id, c.id as category_id, c.title, p.id, p.title, p.slug, a.weight,  0 as discount,  po.pos_price as price from " . $university_prefix . "categories AS c, " . $university_prefix . "products AS p, " . $university_prefix . "product_options AS po," . $prefix . "options AS a where c.id = :cid ";
        }
        $sql .= " AND p.status = 1";
        $sql .= " AND p.category_id = c.id";
        $sql .= " AND po.product_id = p.id";
        $sql .= " AND a.id = po.option_id";

        if ($this->getComponent('CommonFunction')->getConfiguration("Feature.frame_option_status_check")) {
            $sql .= " AND po.status = 1";
        }

        if(!empty($productId)) {
            //$productIdData = "";
            $productId_bind_place_holder = '';
            foreach($productId as $key => $eachProduct) {
                //$productIdData .=  "'" . $eachProduct . "', ";
                $productId_bind_place_holder .= ':productId'.$key.', ';
            }
//            $productIdData = trim($productIdData, ', ');
//            $sql .= " AND p.id IN ( " . $productIdData . " )";

            $productId_bind_place_holder = trim($productId_bind_place_holder, ', ');
            $sql .= " AND p.id IN ( $productId_bind_place_holder )";
        }
        if(!empty($mould)) {
            //$mouldData = "";
            $mould_bind_place_holder = '';
            foreach($mould as $key =>$eachMould) {
                //$mouldData .=  "'" . $eachMould . "', ";
                $mould_bind_place_holder .= ':mould'.$key.', ';
            }
            //$mouldData = trim($mouldData, ', ');
            //$sql .= " AND a.mould IN ( " . $mouldData . " )";
            $mould_bind_place_holder = trim($mould_bind_place_holder, ', ');
            $sql .= " AND a.mould IN ( $mould_bind_place_holder )";
        }

        if(!empty($colour)) {
            //$colourData = "";
            $colour_bind_place_holder = '';
            foreach($colour as $key => $eachColour) {
                //$colourData .=  "'" . $eachColour . "', ";
                $colour_bind_place_holder .= ':colour'.$key.', ';
            }
            //$colourData = trim($colourData, ', ');
            //$sql .= " AND a.colour IN ( " . $colourData . " )";
            $colour_bind_place_holder = trim($colour_bind_place_holder, ', ');
            $sql .= " AND a.colour IN ( $colour_bind_place_holder )";
        }

        if(!empty($option)) {
            //$optionData = "";
            $option_bind_place_holder = '';
            foreach($option as $key => $eachOption) {
                //$optionData .=  "'" . $eachOption . "', ";
                $option_bind_place_holder .= ':option'.$key.', ';
            }
            //$optionData = trim($optionData, ', ');
            //$sql .= " AND a.option_name IN ( " . $optionData . " )";

            $option_bind_place_holder = trim($option_bind_place_holder, ', ');
            $sql .= " AND a.option_name IN ( $option_bind_place_holder )";
        }
        if(!empty($order))
        {
            if($order == 2) {
                $sql .= " ORDER BY price ASC";
            }
            elseif($order == 3){

                $sql .= " ORDER BY price DESC";
            }
            else {
                $sql.=" ORDER BY discount DESC, a.weight ASC";
            }
        }
        else
        {
            $sql.=" ORDER BY discount DESC, a.weight ASC";
        }
        if(!empty($offset))
        {
            $sql .= " LIMIT  12 OFFSET :offset";
        }
        else
        {
            $sql.=" LIMIT 12 OFFSET 0";
        }

        $this->log('getProductOptionByAttributeFrame->$sql');
        $this->log($sql);
        $statement = $conn->prepare($sql);
        $statement->bindValue('cid', $categoryid, 'integer');
        if(!empty($offset)) {
            $statement->bindValue('offset', $offset, 'integer');
        }
        if(!empty($productId)) {
            foreach($productId as $key => $eachProduct) {
                $statement->bindValue('productId'.$key, $eachProduct, 'integer');
            }
        }
        if(!empty($mould)) {
            foreach($mould as $key => $eachMould) {
                $statement->bindValue('mould'.$key, $eachMould, 'string');
            }
        }
        if(!empty($colour)) {
            foreach($colour as $key => $eachColour) {
                $statement->bindValue('colour'.$key, $eachColour, 'string');
            }
        }
        if(!empty($option)) {
            foreach($option as $key => $eachOption) {
                $statement->bindValue('option'.$key, $eachOption, 'string');
            }
        }

        $statement->execute();
        $options = $statement->fetchAll('assoc');

        //$options = $conn->execute($sql, $parameters)->fetchAll('assoc');
        return $options;

    }
	
	
	
	
$conn = ConnectionManager::get('organizations');
$category_id = 4;
$sql = "SELECT id, title from latrobe_d8765_products Product where category_id = ?";
$products = $conn->execute($sql, [$category_id], ['integer'])->fetchAll('assoc');