<?php
if(isset($error)){
	foreach($error as $error){
		echo "<p>$error</p>";
	}
}
?>

<div class="page-header">
	<h1><?php echo $data['title'] ?></h1>
</div>

<form action='' method='post'>
	<div class="form-group">
	    <label for="name">Nombre</label>
	    <input type="text" class="form-control" name='name'>
	</div>
	<div class="form-group">
	    <label for="name">Descripción (separados solo por espacios)</label>
	    <input type="text" class="form-control" name='description'>
	</div>
	<button type="submit" name='submit' class="btn btn-primary" value='add'>Añadir</button>
</form>

