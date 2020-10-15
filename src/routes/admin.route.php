<?php 
include("../src/connect.php");
include("../src/is_admin.php");

/*SELECT SUM(op.quantity*op.price) as sum FROM delivery_orders o, delivery_order_product op WHERE DATE(created_at) = DATE(NOW()) AND o.id = op.order_id GROUP BY o.id*/

include ("../templates/admin/index.phtml");