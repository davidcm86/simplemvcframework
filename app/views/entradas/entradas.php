<div class="page-header">
	<h1><?php echo $data['title'] ?></h1>
</div>

<div class="row">
	<div class="col-md-1"><?php echo "<a class='btn btn-default' style='margin:10px;' href='".DIR."entradas/add'>Añadir entrada</a>"; ?></div>
</div>

<div class='table-responsive'>
<table class='table table-hover'>
<tr>
	<th>Nombre</th>
	<th>Descripción</th>
	<th>Acciones</th>
</tr>
<?php
	if($data['entradas']){
		foreach($data['entradas'] as $row){
			echo "<tr>";
			echo "<td>$row->name</td>";
			echo "<td>$row->description</td>";
			echo "<td>
			<a href='".DIR."entradas/edit/$row->id'>Editar</a>
			<a href='".DIR."entradas/delete/$row->id'>Borrar</a>
			</td>";
			echo "</tr>";
		}
	}
?>
</table>
</div>

