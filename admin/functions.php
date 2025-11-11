<?php
function get_product_count(){
		$connection = mysqli_connect("localhost","root","");
		$db = mysqli_select_db($connection,"shoppulse");
		$product_count = 0;
		$query = "select count(*) as product_count from items";
		$query_run = mysqli_query($connection,$query);
		while ($row = mysqli_fetch_assoc($query_run)){
			$product_count = $row['product_count'];
		}
		return($product_count);
	}

    // 
    function get_customer_count(){
		$connection = mysqli_connect("localhost","root","");
		$db = mysqli_select_db($connection,"shoppulse");
		$user_count = 0;
		$query = "select count(*) as user_count from customer";
		$query_run = mysqli_query($connection,$query);
		while ($row = mysqli_fetch_assoc($query_run)){
			$user_count = $row['user_count'];
		}
		return($user_count);
	}
?>