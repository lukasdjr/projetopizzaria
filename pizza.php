<?php
  include_once("conn.php");
  $method = $_SERVER["REQUEST_METHOD"];
  
  if($method === "GET") {
   $bordasQuery = $conn->query("SELECT * FROM bordas;");

   $bordas = $bordasQuery->fetchAll();

   $massasQuery = $conn->query("SELECT * FROM massas;");

   $massas = $massasQuery->fetchAll();

   $saboresQuery = $conn->query("SELECT * FROM sabores;");

   $sabores = $saboresQuery->fetchAll();


  } else if($method === "POST") {
    $data = $_POST;

    $borda = $data["borda"];
    $massa = $data["massa"];
    $sabores = $data["sabores"];

    /* validação de sabores max */
    if(count($sabores) > 3) {
       
      $_SESSION["msg"] = "Selecione no máximo 3 sabores!";
      $_SESSION["status"] = "warning";
     // salvando borda e massa na pizza  
    } else {
      $stmt = $conn->prepare("INSERT INTO pizzas(borda_id, massas_id)VALUEs (:borda, :massa)");

    //filtrando inputs
      $stmt->bindParam(":borda", $borda, PDO::PARAM_INT);
      $stmt->bindParam(":massa", $massa, PDO::PARAM_INT);
      
      $stmt-> execute();

      //resgatando utimo id da pizza
      $pizzaId = $conn->lastInsertId();

      $stmt = $conn->prepare("INSERT INTO pizza_sabor (pizza_id, sabor_id) VALUES (:pizza, :sabor)");

     //repetição ate terminar de salvar sabores
      foreach($sabores as $sabor) {

         //filtrando imput
        $stmt->bindParam(":pizza", $pizzaId, PDO::PARAM_INT);
        $stmt->bindParam(":sabor", $sabor, PDO ::PARAM_INT);

        $stmt->execute();
      }
      //criar pizza
      $stmt = $conn->prepare("INSERT INTO pedidos (pizza_id, status_id) VALUES (:pizza, :status)");
    
      //status
      $statusId = 1;

      //filtrando imputs
      $stmt->bindParam(":pizza", $pizzaId);
      $stmt->bindParam(":status", $statusId);

      $stmt-> execute();

      $_SESSION["msg"] = "Pedido realizado com sucesso";
      $_SESSION["status"] = "sucess";
    

    }
    header("Location: ..");  
  }
?>