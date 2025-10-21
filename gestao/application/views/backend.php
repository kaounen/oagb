<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php 
foreach($css_files as $file): ?>
	<link type="text/css" rel="stylesheet" href="<?php echo $file; ?>" />
<?php endforeach; ?>
</head>
<body>
	<div>
		<!-- <a href='<?php echo site_url('admin/agenda_')?>'>Agenda</a> |
		<a href='<?php echo site_url('admin/apresentacaoalfalit_')?>'>Apresentacao Alfalit</a> |
		<a href='<?php echo site_url('admin/calendario_')?>'>Calendario</a> |
		<a href='<?php echo site_url('admin/equipa_')?>'>Equipa</a> |
		<a href='<?php echo site_url('admin/marcos_')?>'>Marcos Historicos</a> | -->
		<a href='<?php echo site_url('admin/noticias_')?>'>Noticias</a> |

		
		
	</div>
	<div style='height:20px;'></div>  
    <div style="padding: 10px">
		<?php echo $output; ?>
    </div>
    <?php foreach($js_files as $file): ?>
        <script src="<?php echo $file; ?>"></script>
    <?php endforeach; ?>
</body>
</html>
