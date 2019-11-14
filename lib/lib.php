<?php

function l($text)
{
	printf('[VSM][%s] %s'.PHP_EOL, date('m-d-Y h:i a'), $text);
}

function siteExists($site)
{
	if (!file_exists(VSITES.'/'.$site.'.conf')) {
		return false;
	}

	if (!file_exists(WEBROOTS.'/'.$site)) {
		return false;
	}

	//mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
	$mysqli = new mysqli(getVar('HOST'), getVar('USER'), getVar('PASSWORD'), '', getVar('PORT'));

	if ($mysqli->select_db(explode('.', $site)[0]) === false) {
		return false;
	}

	return true;
}

function createDb($db)
{
	//mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
	$mysqli = new mysqli(getVar('HOST'), getVar('USER'), getVar('PASSWORD'), '', getVar('PORT'));
	
	if ($mysqli->query('CREATE DATABASE '.$db) === false) {
		l('Could not create database: '.$db);
	}
}

function deleteDb($db)
{
	//mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
	$mysqli = new mysqli(getVar('HOST'), getVar('USER'), getVar('PASSWORD'), '', getVar('PORT'));
	
	if ($mysqli->query('DROP DATABASE '.$db) === false) {
		l('Could not delete database: '.$db);
	}
}

function createVsite($sites)
{
	$sample = file_get_contents(SAMPLES.'/vsite_sample');

	mkdir(WEBROOTS.'/'.$sites[0]);

	passthru('chown vsm:vsm-web '.WEBROOTS.'/'.$sites[0]);
	passthru('chmod 2775 '.WEBROOTS.'/'.$sites[0].' -R');

	$sample = str_replace('${ROOT_DIRECTORY}', WEBROOTS.'/'.$sites[0], $sample);
	$sample = str_replace('${SERVER_NAME}', implode(' ', $sites), $sample);

	if (!file_put_contents(VSITES.'/'.$sites[0].'.conf', $sample)) {
		throw new Exception('Cannot write to file '.$file);
	}

	return VSITES.'/'.$sites[0].'.conf';
}

function opt($argv, $opt)
{
	foreach ($argv as $a) {
		if (trim($a) === $opt) {
			return true;
		}
	}

	return false;
}

function validateSite($site)
{
	if (!siteExists($site)) {
		throw new Exception('Setup not completed (DB, VSITE, WEBROOT): '.$site);
	}
}

function findMatch($name)
{
    $config = file_get_contents(__DIR__.'/../config');
    preg_match_all('/'.$name.'=(.*)/m', $config, $matches, PREG_SET_ORDER, 0);

    if (isset($matches[0])) {
        if (isset($matches[0][0])) {
		    return $matches;
		} else {
		    l("No such variable ".$name);
		    exit;
		}
    } else {
		l("No such variable ".$name);
		exit;
    }
}

function changeVar($name, $value)
{
	$config = str_replace(findMatch($name)[0][0], $name.'='.$value, file_get_contents(__DIR__.'/../config'));

	if (!file_put_contents(__DIR__.'/../config', $config)) {
		throw new Exception('Cannot write to file '.$file);
	}
}

function getVar($name)
{
	return findMatch($name)[0][1];
}

function findDirective($file, $directive)
{
	$return = false;

	$buffer = file($file);

	foreach ($buffer as $k => $line) {
		if (trim($line) === $directive) {
			$return = [
				'line' => ($k + 1),
				'value' => trim($line)
			];
		}
	}

	if (!$return) {
		throw new Exception('No such directive '.$directive);
	}

	return $return;
}

function updateDirective($fileSrc, $directive, $value)
{
	$file = file($fileSrc);
	$line = findDirective($fileSrc, $directive);

	$file[$line['line']] = $value.PHP_EOL;

	if (!file_put_contents($fileSrc, implode("", $file))) {
		throw new Exception('Cannot write to file '.$file);
	}
}

function addHostEntry($domain)
{
	if (file_exists('/etc/hosts')) {
		$hostsFile = file('/etc/hosts');
		$vsmHostsKey = null;

		foreach ($hostsFile as $k => $line) {
			if (strpos($line, '# Managed by VSM') !== false) {
				$vsmHostsKey = $k+1;
				break;
			}
		}

		if (!is_null($vsmHostsKey)) {
			$hostsFile[$vsmHostsKey] = trim($hostsFile[$vsmHostsKey]);

			$hostsFile[$vsmHostsKey] .= ' '.$domain.PHP_EOL;
		} else {
			$hostsFile[] = PHP_EOL.'# Managed by VSM'.PHP_EOL;
			$hostsFile[] = '127.0.0.1 '.$domain.PHP_EOL;
		}

		file_put_contents('/etc/hosts', implode(null, $hostsFile));
	}
}

function removeHostsEntry($domain)
{
	if (file_exists('/etc/hosts')) {
		$hostsFile = file('/etc/hosts');
		$vsmHostsKey = null;

		foreach ($hostsFile as $k => $line) {
			if (strpos($line, '# Managed by VSM') !== false) {
				$vsmHostsKey = $k+1;
				break;
			}
		}

		if (!is_null($vsmHostsKey)) {
			$hostsFile[$vsmHostsKey] = str_replace(' '.$domain, '', $hostsFile[$vsmHostsKey]);
		}

		file_put_contents('/etc/hosts', implode(null, $hostsFile));
	}
}

function updateEnv($envFile, $data = array())
{
    if (!count($data)) {
        return;
    }

    $pattern = '/([^\=]*)\=[^\n]*/';

    $lines = file($envFile);
    $newLines = [];
    foreach ($lines as $line) {
        preg_match($pattern, $line, $matches);

        if (!count($matches)) {
            $newLines[] = $line;
            continue;
        }

        if (!key_exists(trim($matches[1]), $data)) {
            $newLines[] = $line;
            continue;
        }

        $line = trim($matches[1]) . "={$data[trim($matches[1])]}\n";
        $newLines[] = $line;
    }

    $newContent = implode('', $newLines);
    file_put_contents($envFile, $newContent);
}
