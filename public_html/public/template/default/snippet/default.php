<?php
/*
function snippet_comment( $value, $link = '/', $hid = 0, $small = false )
{
	if ( $hid > 0 )
	{
		$link .= '/hid-' . $hid;
	}

	$user = UserHirdeto( $value['user_id'], $value['hirdeto_id'] );

	$r = '<div class="comment' . ( $small ? ' small' : '' ) . '">
			<div class="image">';
					if ( $user['hirdeto_id'] > 0 )
					{
						$r .= '<a href="/szexpartner/hid-' . $user['hirdeto_id'] . '" target="_blank" class="tooltip" title="hirdető adatlapjára">';
					}
					else
					{
						$r .= '<a href="/uzenet_kuldes/uid-' . $user['id'] . '" target="_blank" class="tooltip" title="Privát üzenet küldése: ' . $user['nick'] . '">';
					}

					$r .= '<img src="' . $user['avatar'] . '" alt="' . $user['nick'] . '" class="user_pic" /></a>
					<p class="rank">' . $user['rank_name'] . '</p>
				</div>

				<p class="meta">';
					if ( $user['hirdeto_id'] > 0 )
					{
						$r .= '<a href="/szexpartner/hid-' . $user['hirdeto_id'] . '" target="_blank" class="tooltip" title="hirdető adatlapjára">' . $user['nick'] . '</a>';
					}
					else
					{
						$r .= '<a href="/uzenet_kuldes/uid-' . $user['id'] . '" target="_blank" class="tooltip" title="Privát üzenet küldése: ' . $user['nick'] . '">' . $user['nick'] . '</a>';
					}
					$r .= ' | <span class="color_gray italic">' . $value['date'] . '</span>';
			//$r .= ' | <a href="/' . $link . '/response-' . $value['id'] . '">válasz</a>';
			$r .= ( User::$rank >= 240 ? ' | <a href="/' . $link . '/delete-' . $value['id'] . '/post-1">törlés</a>' : '' ) . '</p>
			<p class="text">' . $value['comment_html'] . '</p>
		</div>';

	return $r;
}*/


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