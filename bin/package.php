<?php
/**
 * DbPatch
 *
 * Copyright (c) 2011, Sandy Pleyte.
 * Copyright (c) 2010-2011, Martijn De Letter.
 *
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *  * Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 *
 *  * Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in
 *    the documentation and/or other materials provided with the
 *    distribution.
 *
 *  * Neither the name of the authors nor the names of his
 *    contributors may be used to endorse or promote products derived
 *    from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 */

error_reporting(E_ALL & ~E_STRICT & ~E_DEPRECATED);
require_once('PEAR/PackageFileManager2.php');
PEAR::setErrorHandling(PEAR_ERROR_DIE);

function createPackager($original_file, $options = array())
{
    // merge the options with these defaults.
    $options = array_merge(array(
        'packagefile' => 'package.xml',
        'filelistgenerator' => 'file',
        'simpleoutput' => true,
        'baseinstalldir' => '/DbPatch',
        'packagedirectory' => dirname(__FILE__) . '/../',
        'clearcontents' => true,
        'ignore' => array(
            'deploy.properties',
            'deploy.xml',
            'build/*',
            'bin/package.php',
        ),
        'exceptions' => array(
            'bin/dbpatch.php' => 'script',
            'bin/dbpatch.bat' => 'script',
            'LICENSE' => 'php',
            'phpunit.xml.dist' => 'php',
            'README.md' => 'php',
            'TODOS.md' => 'php',
            'CHANGES.md' => 'php',
        ),
        'installexceptions' => array(
            'bin/dbpatch.php' => '/',
            'bin/dbpatch.bat' => '/'
        ),
        'dir_roles' => array(
            'bin' => 'php',
            'docs' => 'php',
            'tests' => 'php',
            'src' => 'php',
        ),
    ), $options);

    $packagexml = PEAR_PackageFileManager2::importOptions($original_file, $options);
    $packagexml->setPackageType('php');

    $packagexml->setPackage('DbPatch');
    $packagexml->setSummary('DbPatch is an open-source PHP command-line utility to patch your MySQL database.');
    $packagexml->setDescription(<<<DESC
DbPatch is an open-source PHP command-line utility to patch your MySQL database.

Add a patch file to your codebase and use a single command to easily update your database.
DESC
    );
    $packagexml->setChannel('pear.dbpatch-project.com');
    $packagexml->setNotes('Please see the README in the root of the application for the latest changes');

    $packagexml->setPhpDep('5.1.6');
    $packagexml->setPearinstallerDep('1.4.0');
    $packagexml->addPackageDepWithChannel('required', 'PEAR', 'pear.php.net', '1.4.0');

    $packagexml->addReplacement('bin/dbpatch.php', 'pear-config', '/usr/bin/env php', 'php_bin');
    $packagexml->addReplacement('bin/dbpatch.php', 'pear-config', '@php_bin@', 'php_bin');
    $packagexml->addReplacement('bin/dbpatch.php', 'pear-config', '@php_dir@', 'php_dir');

    $packagexml->addMaintainer('lead', 'sndpl', 'Sandy Pleyte', 'sandy@ibuildings.nl');
    $packagexml->addMaintainer('lead', 'mdletter', 'Martijn De Letter', 'martijn@ibuildings.nl');
    $packagexml->setLicense('MIT', 'http://www.opensource.org/licenses/mit-license.html');

    // Add this as a release, and generate XML content
    $packagexml->addRelease();
    $packagexml->setOSInstallCondition('windows');
    $packagexml->addInstallAs('bin/dbpatch.bat', 'dbpatch.bat');
    $packagexml->addInstallAs('bin/dbpatch.php', 'dbpatch.php');

    $packagexml->addRelease();
    $packagexml->addInstallAs('bin/dbpatch.php', 'dbpatch');
    $packagexml->addIgnoreToRelease('bin/dbpatch.bat');

    return $packagexml;
}


echo 'DbPatch PEAR Packager v1.0' . PHP_EOL;

if ($argc < 3) {
    echo <<<HELP

Usage:
  php package.php [version] [stability] [make|nothing]

Description:
  The DbPatch packager generates a package.xml file and accompanying package.
  By specifying the version and stability you can tell the packager which version to package.

  A file will only be generated if the third parameter is the word 'make'; otherwise the output will be send to
  the command line.

HELP;
    exit(0);
}

$packager = createPackager('../package.xml');

$packager->setAPIVersion($argv[1]);
$packager->setReleaseVersion($argv[1]);
$packager->setReleaseStability($argv[2]);
$packager->setAPIStability($argv[2]);

$packager->generateContents();
if (isset($argv[3]) && ($argv[3] == 'make')) {
    $packager->writePackageFile();
} else {
    $packager->debugPackageFile();
}
