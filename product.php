
<?php
  include_once('lib/db.inc.php');
  $db = ierg4210_DB();
  $pid = (int)$_REQUEST['pid'];
  $q = $db->prepare("SELECT * FROM products WHERE pid = $pid;");
  if ($q->execute())
    $prod = $q->fetchAll();
?>

<div class = "contentright">

  <h2 class="media-heading"><?php echo $prod[0]['name'];?></h2>

  <div class="media">
      <a class="media-top" href="#">
          <img class="media-object" src="incl/img/<?php echo $prod[0]['pid']?>.jpg" width="200" height="200"
               alt="productcell">
      </a>
      <div class="media-body">
          <p></p>
          <h4 class="media-heading">$<?php echo $prod[0]['price'];?></h4>

          <button type="button" class="btn .btn-primary">Add</button>
      </div>
  </div>

  <h5>Description:</h5>
  <P><?php echo $prod[0]['description'];?><P>

</div>
