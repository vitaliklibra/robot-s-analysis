<?php

	// Метод для получение кода ответа HTTP при запросе URL.
	function get_http_response_code($theURL)
	{
    $headers = get_headers($theURL);
    return substr($headers[0], 9, 3);
	}

	// Метод для создания URL, иключаещего имя искомого файла.
	function get_full_url($fileName)
	{
		if (isset($_POST['SubmitBtn']))
		{
			if (!empty($_POST['InputUrl']))
			{
				$inputUrl = trim($_POST['InputUrl']);

				// Если url заканчивается на "/"
				if (substr($inputUrl, strlen($inputUrl)-1, 1) == "/")
				{
					return ($inputUrl . $fileName);
				}
				// Если url не заканчивается на "/"
				else
				{
					return ($inputUrl . "/" . $fileName);
				}
			}
		}
	}

	// Метод для копирования файла из сайта во временную папку
	// с временным именем файла и получения его размера.
	function get_file_size($fullUrl)
	{
		$tempFileName = tempnam(sys_get_temp_dir(), 'rob');
		copy ($fullUrl, $tempFileName);
		$fileSize = round(filesize($tempFileName) / 1024, 3); // КБ
		unlink($tempFileName);

		return $fileSize ? $fileSize : 0;
	}

	// Метод для получения количества искомых директив
	// в открытом файле.
	function get_count_directive($fo)
	{
		$countHost = 0;
		$countSitemap = 0;

		do {
			$line = fgets($fo);
			$lineHost = substr($line, 0, 4);
			$lineSitemap = substr($line, 0, 7);

			if ($lineHost == "Host")
			{
				$countHost++;
			}

			if ($lineSitemap == "Sitemap")
			{
				$countSitemap++;
			}
		} while (!feof($fo));

		$arr[0] = $countHost;
		$arr[1] = $countSitemap;

		return $arr;
	}

?>