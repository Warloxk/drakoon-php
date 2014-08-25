<div class="grid">
	<div class="col-1-1">
		<h2>Sites</h2>
	</div>

	<div class="col-1-1">
		<select class="dynamic_select">
			<option value="">Filter by Category</option>
			<option value="/admin">All</option>
			<? foreach ( $m->view->categories as $category ) : ?>
				<option value="/admin/cat-<?=$category['id']?>"><?=$category['name']?></option>
			<? endforeach ?>
		</select>
	</div>

	<div class="col-1-1">
		<table class="drakoonTable">
			<thead>
				<tr>
					<th>#</th>
					<th>Name</th>
					<th>Link</th>
					<th>Category</th>
					<th style="width:220px">Actions</th>
				</tr>
			</thead>

			<tbody>
				<? foreach ( $m->view->sites as $site ) : ?>
					<tr>
						<td><?=$site['id']?></td>
						<td><?=$site['name']?></td>
						<td><?=$site['link']?></td>
						<td><?=$site['category']?></td>
						<td>
							<? if ( $site['status'] == 1 ) : ?>
								<a href="/admin/act-2/id-<?=$site['id']?>/post-1">Deactivate</a> |
							<? else : ?>
								<a href="/admin/act-1/id-<?=$site['id']?>/post-1">Activate</a> |
							<? endif ?>
							<a href="/admin_edit_site/id-<?=$site['id']?>" target="_blank">Edit</a> |
							<a href="/admin/del-1/id-<?=$site['id']?>/post-1" onclick="return confirm('Are you sure you want to delete this site?')" target="_blank">Delete</a>
						</td>
					</tr>
				<? endforeach ?>
			</tbody>
		</table>
	</div>
</div>