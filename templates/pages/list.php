<div>
	<div class="message">
		<?php 
		if(!empty($params['before'])){
			switch($params['before']){
				case('created'):
					echo 'Notatka zostala utworzona';
					break;
			}
		}
		?>
	</div>

	<h4>Lisa notatek </h4>
	<b><?php echo $params['resultList'] ?? "" ?></b>
</div>