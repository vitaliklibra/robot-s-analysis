<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
	<link rel="stylesheet" type="text/css" href="main.css">
	<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
	<script src="main.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
	<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
</head>
<body>
	
	<form class="form" method="POST" onsubmit="return validateForm()">
		<div class="col-md-4">	
			<input class="form-control" type="text" name="InputUrl" placeholder="Введите URL в формате http://www... или https://www... ">
		</div>
		<div>
			<input class="btn btn-primary" type="submit" name="SubmitBtn" value="Выполнить проверку" id="SubmitBtn">
			<input class="btn btn-default" type="button" name="SubmitBtn" value="Сброс" onclick="document.location.href='<?php $_SERVER['HTTP_REFERER']?>'">
		</div>
	</form>

	<?php

		require_once "functions.php";

		$fileName = "robots.txt";
		$fullUrl = "";
		$wasread = false;

		$expectFileExist = "";
		$expectFileSize = "";
		$expectHTTPStatusCode = "";
		$expectHost = "";
		$expectCountHost = "";
		$expectSitemap = "";

		$stateFile = "";
		$stateFileSize = "";
		$stateHTTPStatusCode = "";
		$stateHost = "";
		$stateCountHost = "";
		$stateSitemap = "";

		$fullUrl = get_full_url($fileName);

		if ($fullUrl)
		{
			$wasread = true;
			$fo = @fopen($fullUrl, "r");
			
			if ($fo)
			{
				// Проверка наличия файла
				$expectFileExist = "OK";
				$stateFile = "Файл " . $fileName . " присутствует";

				// Проверка указания директивы Host и Sitemap
				list($countHost, $countSitemap) = get_count_directive($fo);

				// Проверка указания директивы Host
				if ($countHost > 0)
				{
					$expectHost = "OK";
					$stateHost = "Директива Host указана";

					// Проверка количества директив Host, прописанных в файле
					if ($countHost == 1)
					{
						$expectCountHost = "ОК";
						$stateCountHost = "В файле " . $fileName . " прописана 1 директива Host";
					}
					else
					{
						$expectCountHost = "ОШИБКА";
						$stateCountHost = "В файле " . $fileName . " прописано несколько директив Host";
					}
				}
				else
				{
					$expectHost = "ОШИБКА";
					$stateHost = "В файле " . $fileName . " не указана директива Host";
					$expectCountHost = "ОШИБКА";
					$stateCountHost = "(См. предыдущий пункт проверки)";
				}
				
				// Проверка указания директивы Sitemap
				if ($countSitemap > 0)
				{
					$expectSitemap = "OK";
					$stateSitemap = "Директива Sitemap указана";
				}
				else
				{
					$expectSitemap = "ОШИБКА";
					$stateSitemap = "В файле " . $fileName . " не указана директива Sitemap";
				}

				// Проверка размера файла
				$fileSize = get_file_size($fullUrl);
				if ($fileSize > 0)
				{
					if ($fileSize <= 32)
					{
						$expectFileSize = "OK";
						$stateFileSize = "Размер файла " . $fileName . " составляет " . $fileSize . "КБ, что находится в пределах допустимой нормы";
					}
					else
					{
						$expectFileSize = "ОШИБКА";
						$stateFileSize = "Размер файла " . $fileName . " составляет " . $fileSize . "УБ, что превышает допустимую норму";
					}
				}

				// Проверка кода ответа сервера для файла
				$HTTPStatusCode = get_http_response_code($fullUrl);
				if ($HTTPStatusCode == 200)
				{
					$expectHTTPStatusCode = "OK";
					$stateHTTPStatusCode = "При обращении к файлу " . $fileName . " сервер возвращает код ответа " . $HTTPStatusCode;
				}
				else
				{
					$expectHTTPStatusCode = "ОШИБКА";
					$stateHTTPStatusCode = "При обращении к файлу " . $fileName . " сервер возвращает код ответа " . $HTTPStatusCode;
				}

				fclose($fo);
			}
			else
			{
				$expectFileExist = "ОШИБКА";
				$expectFileSize = "ОШИБКА";
				$expectHTTPStatusCode = "ОШИБКА";
				$expectHost = "ОШИБКА";
				$expectCountHost = "ОШИБКА";
				$expectSitemap = "ОШИБКА";

				$stateFile = "Файл " . $fileName . " отсутствует";
				$stateFileSize = "";
				$stateHTTPStatusCode = "";
				$stateHost = "";
				$stateCountHost = "";
				$stateSitemap = "";
			}
		}

	 	if ($wasread)
	 	{
			echo "<div id='result'><h2>Результаты</h2></div>";

			echo <<<HERE
<div class="bodytable">		
	<table class="table table-bordered">
		<tr>
			<th style="width:3%"></th>
			<th style="width:32%"><center>Название проверки</center></th>
			<th style="width:10%"><center>Статус</center></th>
			<th><center>Текущее состояние</center></th>
		</tr>
		<tr>
			<td><center>1</center></td>
			<td>Проверка кода ответа сервера для файла robots.txt</td>
			<td>$expectHTTPStatusCode</td>
			<td>$stateHTTPStatusCode</td>
		</tr>
		<tr>
			<td><center>2</center></td>
			<td>Проверка наличия файла robots.txt</td>
			<td>$expectFileExist</td>
			<td>$stateFile</td>
		</tr>
		<tr>
			<td><center>3</center></td>
			<td>Проверка размера файла robots.txt</td>
			<td>$expectFileSize</td>
			<td>$stateFileSize</td>
		</tr>
		<tr>
			<td><center>4</center></td>
			<td>Проверка указания директивы Host</td>
			<td>$expectHost</td>
			<td>$stateHost</td>
		</tr>
		<tr>
			<td><center>5</center></td>
			<td>Проверка количества директив Host, прописанных в файле</td>
			<td>$expectCountHost</td>
			<td>$stateCountHost</td>
		</tr>
		
		<tr>
			<td><center>6</center></td>
			<td>Проверка указания директивы Sitemap</td>
			<td>$expectSitemap</td>
			<td>$stateSitemap</td>
		</tr>
		
	</table>
</div>
HERE;
	}

	?>

</body>
</html>