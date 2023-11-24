<div class="show">
	<?php $note = $params['note'] ?? null; ?>
	<?php if($note) : ?>
		<ul>
			<li>Id: <?php echo htmlentities ((int) $note['id'])?></li>
			<li>Tytuł: <?php echo htmlentities ($note['title'])?></li>
			<li>Opis: <?php echo htmlentities ($note['description'])?></li>
			<li>Zapisano: <?php echo htmlentities ($note['created'])?></li>
		</ul>
	<?php else: ?>
		<div>Brak notatki do wyświetlenia</div>
	<?php endif; ?>
		<a href="/">
			<button>Powrot do listy notatek</button>
		</a>
</div>