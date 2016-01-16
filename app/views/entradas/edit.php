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
	    <input type="text" class="form-control" name='name' value='<?php echo $data['row'][0]->name;?>'>
	</div>
	<div class="form-group">
	    <label for="name">Descripción (separados solo por espacios)</label>
	    <input type="text" class="form-control" name='description' value='<?php echo $data['row'][0]->description;?>'>
	</div>
	<button type="submit" name='submit' class="btn btn-primary" value='Update'>Editar</button>
</form>

</br>
<label for="name">Tags</label>
<div class="panel panel-default">
	<div class="panel-body">
		<?php 
			foreach ($data['tags'] as $tag) {
				echo $tag['0']->tag . " ";
			}
		?>
	</div>
</div>

