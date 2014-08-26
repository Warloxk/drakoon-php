<?
function snippet_info_messages(  )
{
	$r = '';

	if ( isset($_SESSION['info_message'] ) && !empty( $_SESSION['info_message'] ) )
	{
		$r .= '<div class="info_message">
			<button type="button" class="close" onclick="javascript:MessageClose(this)">×</button>
			<p>' . urldecode( $_SESSION['info_message'] ) . '</p>
		</div>';
	}

	if ( isset($_SESSION['error_message'] ) && !empty( $_SESSION['error_message'] ) )
	{
		$r .= '<div class="error_message">
			<button type="button" class="close" onclick="javascript:MessageClose(this)">×</button>
			<p><b>ERROR:</b> ' . urldecode( $_SESSION['error_message'] ) . '</p>
		</div>';
	}

	return $r;
}