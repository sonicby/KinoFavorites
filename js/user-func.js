function overlooked(id)
{
	$.post("action.php",{action: 'overlooked', id: id},
		function(data) {
			if (data.error)
			{
				$('#notice').empty().attr('background', '#FF6633').append('Ошибка передачи данных<br />Попробуйте ещё раз.').delay(3000).fadeOut(400);
			}
		}, "json"
	);
	return false;
}

function hide(type, id, div_id)
{
	$.post("action.php",{action: 'hide', id: id},
		function(data) {
			if (data.error)
			{
				$('#notice').empty().attr('background', '#FF6633').append('Ошибка передачи данных<br />Попробуйте ещё раз.').delay(3000).fadeOut(400);
			}
			else
			{
        		$.post("content.php",{type: type, id: div_id},
        			function(data) {
        				if (data.error)
        				{
        					$('#notice').empty().attr('background', '#FF6633').append('Ошибка передачи данных<br />Попробуйте ещё раз.').delay(3000).fadeOut(400);
        				}
        				else
        				{
        					$('#'+div_id).empty().append(data);
        				}
        			}
        		);
        		return false;			
			}
		}, "json"
	);
	return false;
}

function show(type, id)
{
	if (document.getElementById(id).style.display != "block")
	{
		document.getElementById(id).style.display = "block"
		$.post("content.php",{type: type, id: id},
			function(data) {
				if (data.error)
				{
					$('#notice').empty().attr('background', '#FF6633').append('Ошибка передачи данных<br />Попробуйте ещё раз.').delay(3000).fadeOut(400);
				}
				else
				{
					$('#'+id).empty().append(data);
				}
			}
		);
		return false;
	}
	else
	{
		document.getElementById(id).style.display = "none"
		$('#'+id).empty();
	}
}

function del(id)
{
	$.post("action.php",{action: 'del', id: id},
		function(data) {
			if (data.error)
			{
				$('#notice').empty().attr('background', '#FF6633').append('Ошибка передачи данных<br />Попробуйте ещё раз.').delay(3000).fadeOut(400);
			}
			else
			{
    			location.reload();
			}
		}
	);
	return false;    
}

//Передаём имя артиста
$("#artist").submit(function()
{
	var $form = $(this),
		b = $form.find('input[type=submit]'),
		name = $form.find('input[name="artist-name"]').val()
	
	$(b).attr('disabled', 'disabled');
								
	if (name != '')
	{
		$('#notice').empty().append('Произвожу поиск...');
		$.post("get.php",{action: 'get_artist', artist: name},
			function(data) {
				$('#notice').empty().append(data);
				$(name).empty();
				$(b).removeAttr('disabled');
			}
		);
	}
	else 
		alert("Вы не указали имя!");
	return false;
});

//Добавляем артиста
function add_artist(id, type, name)
{
	$('#notice').empty().append('Добавляю в базу...');
	$.post("action.php",{action: 'add_artist', id: id, type: type, name: name},
		function(data) {
			$('#notice').empty().append(data);
			location.reload();
		}
	);
	return false;
}